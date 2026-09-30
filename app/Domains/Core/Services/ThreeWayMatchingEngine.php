<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\DTOs\MatchResult;
use App\Domains\Core\Enums\SupplierInvoiceMatchStatus;
use App\Domains\Core\Enums\SupplierInvoiceStatus;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Exceptions\InvalidStateTransitionException;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\PurchaseOrderLine;
use App\Domains\Core\Models\PurchaseReceipt;
use App\Domains\Core\Models\PurchaseReceiptLine;
use App\Domains\Core\Models\SupplierInvoice;
use App\Domains\Core\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ThreeWayMatchingEngine
{
    /**
     * Perform a 3-way match evaluation between Purchase Order, Purchase Receipts, and Supplier Invoice.
     *
     * @throws CrossOrganizationException
     * @throws InvalidStateTransitionException
     */
    public function evaluateMatch(SupplierInvoice $supplierInvoice): MatchResult
    {
        return DB::transaction(function () use ($supplierInvoice) {
            /** @var SupplierInvoice $lockedInvoice */
            $lockedInvoice = SupplierInvoice::withoutGlobalScopes()
                ->with(['lines'])
                ->lockForUpdate()
                ->findOrFail($supplierInvoice->id);

            if ($lockedInvoice->status === SupplierInvoiceStatus::VOIDED) {
                throw new InvalidStateTransitionException("Cannot evaluate 3-way match for voided Supplier Invoice {$lockedInvoice->id}.");
            }

            $discrepancies = [];
            $lineDetails = [];

            if (! $lockedInvoice->purchase_order_id) {
                $discrepancies[] = "Supplier Invoice {$lockedInvoice->id} has no Purchase Order reference attached for 3-way matching.";

                $lockedInvoice->match_status = SupplierInvoiceMatchStatus::DISCREPANCY;
                $lockedInvoice->discrepancy_reason = implode("\n", $discrepancies);
                $lockedInvoice->match_details = [
                    'line_details' => [],
                    'discrepancies' => $discrepancies,
                ];
                $lockedInvoice->save();

                return new MatchResult(
                    SupplierInvoiceMatchStatus::DISCREPANCY,
                    false,
                    $discrepancies,
                    [],
                    '3-Way Match Failed: No Purchase Order attached.'
                );
            }

            /** @var PurchaseOrder|null $po */
            $po = PurchaseOrder::withoutGlobalScopes()
                ->lockForUpdate()
                ->find($lockedInvoice->purchase_order_id);

            if (! $po) {
                $discrepancies[] = "Associated Purchase Order #{$lockedInvoice->purchase_order_id} not found.";

                $lockedInvoice->match_status = SupplierInvoiceMatchStatus::DISCREPANCY;
                $lockedInvoice->discrepancy_reason = implode("\n", $discrepancies);
                $lockedInvoice->match_details = [
                    'line_details' => [],
                    'discrepancies' => $discrepancies,
                ];
                $lockedInvoice->save();

                return new MatchResult(
                    SupplierInvoiceMatchStatus::DISCREPANCY,
                    false,
                    $discrepancies,
                    [],
                    '3-Way Match Failed: Purchase Order record not found.'
                );
            }

            // Tenant isolation verification
            $poBu = BusinessUnit::withoutGlobalScopes()->find($po->business_unit_id);
            if ($poBu && (int) $poBu->organization_id !== (int) $lockedInvoice->organization_id) {
                throw new CrossOrganizationException("Purchase Order {$po->id} (Organization {$poBu->organization_id}) does not belong to Supplier Invoice Organization {$lockedInvoice->organization_id}.");
            }
            if ((int) $po->business_unit_id !== (int) $lockedInvoice->business_unit_id) {
                throw new CrossOrganizationException("Purchase Order {$po->id} (Business Unit {$po->business_unit_id}) does not belong to Supplier Invoice Business Unit {$lockedInvoice->business_unit_id}.");
            }

            $poLines = PurchaseOrderLine::where('purchase_order_id', $po->id)->get()->keyBy('id');
            $receipts = PurchaseReceipt::where('purchase_order_id', $po->id)->get();
            $receiptLines = PurchaseReceiptLine::whereIn('purchase_receipt_id', $receipts->pluck('id'))->get();

            foreach ($lockedInvoice->lines as $invoiceLine) {
                $poLine = null;

                if ($invoiceLine->purchase_receipt_line_id) {
                    $rcptLine = $receiptLines->firstWhere('id', $invoiceLine->purchase_receipt_line_id);
                    if ($rcptLine) {
                        $poLine = $poLines->get($rcptLine->purchase_order_line_id);
                    }
                }

                if (! $poLine && $invoiceLine->item_id) {
                    $poLine = $poLines->firstWhere('item_id', $invoiceLine->item_id);
                }

                if (! $poLine) {
                    $msg = "Invoice line [Item ID: {$invoiceLine->item_id}, '{$invoiceLine->description}'] does not exist on Purchase Order #{$po->id}.";
                    $discrepancies[] = $msg;
                    $lineDetails[] = [
                        'invoice_line_id' => $invoiceLine->id,
                        'item_id' => $invoiceLine->item_id,
                        'status' => 'UNMATCHED_LINE',
                        'reason' => $msg,
                    ];

                    continue;
                }

                $lineDiscrepancies = [];

                // Unit Price check
                $expectedPrice = (string) $poLine->unit_price;
                $invoicedPrice = (string) $invoiceLine->unit_price;
                $priceComparison = bccomp($invoicedPrice, $expectedPrice, 4);
                $priceVariance = bcsub($invoicedPrice, $expectedPrice, 4);

                if ($priceComparison !== 0) {
                    $msg = "Invoice line [{$invoiceLine->description}] unit price ({$invoicedPrice}) differs from PO unit price ({$expectedPrice}). Variance: {$priceVariance}.";
                    $discrepancies[] = $msg;
                    $lineDiscrepancies[] = $msg;
                }

                // Received Quantity calculation
                $receivedQty = '0.0000';
                $matchingReceiptLines = $receiptLines->where('purchase_order_line_id', $poLine->id);
                foreach ($matchingReceiptLines as $rLine) {
                    $receivedQty = bcadd($receivedQty, (string) $rLine->quantity, 4);
                }

                $invoicedQty = (string) $invoiceLine->quantity;
                $orderedQty = (string) $poLine->quantity;

                if ($receipts->isEmpty()) {
                    $msg = "No goods receipts found for Purchase Order #{$po->id} line [Item ID: {$poLine->item_id}].";
                    $discrepancies[] = $msg;
                    $lineDiscrepancies[] = $msg;
                } elseif (bccomp($invoicedQty, $receivedQty, 4) > 0) {
                    $msg = "Invoice line [{$invoiceLine->description}] invoiced quantity ({$invoicedQty}) exceeds received quantity ({$receivedQty}).";
                    $discrepancies[] = $msg;
                    $lineDiscrepancies[] = $msg;
                } elseif (bccomp($invoicedQty, $receivedQty, 4) < 0) {
                    $msg = "Invoice line [{$invoiceLine->description}] invoiced quantity ({$invoicedQty}) is less than received quantity ({$receivedQty}).";
                    $discrepancies[] = $msg;
                    $lineDiscrepancies[] = $msg;
                }

                // Store reference snapshot on invoice line
                $invoiceLine->received_quantity_reference = $receivedQty;
                $invoiceLine->save();

                $lineDetails[] = [
                    'invoice_line_id' => $invoiceLine->id,
                    'item_id' => $invoiceLine->item_id,
                    'po_line_id' => $poLine->id,
                    'expected_price' => $expectedPrice,
                    'invoiced_price' => $invoicedPrice,
                    'price_variance' => $priceVariance,
                    'ordered_quantity' => $orderedQty,
                    'received_quantity' => $receivedQty,
                    'invoiced_quantity' => $invoicedQty,
                    'line_discrepancies' => $lineDiscrepancies,
                    'matched' => empty($lineDiscrepancies),
                ];
            }

            $isMatched = empty($discrepancies);
            $matchStatus = $isMatched ? SupplierInvoiceMatchStatus::MATCHED : SupplierInvoiceMatchStatus::DISCREPANCY;

            $lockedInvoice->match_status = $matchStatus;
            $lockedInvoice->discrepancy_reason = $isMatched ? null : implode("\n", $discrepancies);
            $lockedInvoice->match_details = [
                'line_details' => $lineDetails,
                'discrepancies' => $discrepancies,
                'summary' => $isMatched ? '3-Way Match Verified.' : '3-Way Match Failed with Discrepancies.',
            ];
            $lockedInvoice->save();

            return new MatchResult(
                $matchStatus,
                $isMatched,
                $discrepancies,
                $lineDetails,
                $isMatched ? '3-Way Match Verified.' : '3-Way Match Failed with Discrepancies.'
            );
        });
    }

    /**
     * Managerial override for a Supplier Invoice discrepancy.
     *
     * @throws CrossOrganizationException
     * @throws InvalidStateTransitionException
     * @throws InvalidArgumentException
     */
    public function overrideDiscrepancy(SupplierInvoice $supplierInvoice, User $manager, string $reason): SupplierInvoice
    {
        return DB::transaction(function () use ($supplierInvoice, $manager, $reason) {
            $reason = trim($reason);
            if ($reason === '') {
                throw new InvalidArgumentException('An explicit override reason must be provided to override a discrepancy.');
            }

            /** @var SupplierInvoice $lockedInvoice */
            $lockedInvoice = SupplierInvoice::withoutGlobalScopes()
                ->lockForUpdate()
                ->findOrFail($supplierInvoice->id);

            if ($lockedInvoice->status === SupplierInvoiceStatus::VOIDED) {
                throw new InvalidStateTransitionException("Cannot override discrepancy on a voided Supplier Invoice {$lockedInvoice->id}.");
            }

            // Manager tenant validation via ContextManager
            $contextManager = app(ContextManager::class);
            $activeOrgId = $contextManager->getActiveOrganizationId();
            if ($activeOrgId !== null && (int) $activeOrgId !== (int) $lockedInvoice->organization_id) {
                throw new CrossOrganizationException("Active Context Organization {$activeOrgId} does not match Supplier Invoice Organization {$lockedInvoice->organization_id}.");
            }

            $activeBuId = $contextManager->getActiveBusinessUnitId();
            if ($activeBuId !== null && (int) $activeBuId !== (int) $lockedInvoice->business_unit_id) {
                throw new CrossOrganizationException("Active Context Business Unit {$activeBuId} does not match Supplier Invoice Business Unit {$lockedInvoice->business_unit_id}.");
            }

            // Apply override while preserving original discrepancy records
            $lockedInvoice->match_status = SupplierInvoiceMatchStatus::MATCHED;
            $lockedInvoice->overridden_by = $manager->id;
            $lockedInvoice->overridden_at = now();
            $lockedInvoice->override_reason = $reason;
            $lockedInvoice->save();

            return $lockedInvoice;
        });
    }
}

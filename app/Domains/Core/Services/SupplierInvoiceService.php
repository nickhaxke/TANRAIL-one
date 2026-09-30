<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Enums\SupplierInvoiceMatchStatus;
use App\Domains\Core\Enums\SupplierInvoiceStatus;
use App\Domains\Core\Events\SupplierInvoiceApproved;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Exceptions\InvalidStateTransitionException;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\PurchaseReceiptLine;
use App\Domains\Core\Models\Supplier;
use App\Domains\Core\Models\SupplierInvoice;
use App\Domains\Core\Models\SupplierInvoiceLine;
use App\Domains\Core\Models\TaxCategory;
use App\Domains\Core\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupplierInvoiceService
{
    public function createDraft(
        Branch $branch,
        Supplier $supplier,
        string $supplierBillNumber,
        string $internalReference,
        ?PurchaseOrder $po = null,
        ?int $userId = null
    ): SupplierInvoice {
        $bu = $branch->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($branch->business_unit_id);
        if ($supplier->organization_id !== $bu->organization_id) {
            throw new CrossOrganizationException("Supplier {$supplier->id} does not belong to Organization {$bu->organization_id}.");
        }

        if ($po && $po->business_unit_id !== $branch->business_unit_id) {
            throw new CrossOrganizationException("PurchaseOrder {$po->id} does not belong to Business Unit {$branch->business_unit_id}.");
        }

        $bill = new SupplierInvoice;
        $bill->forceFill([
            'organization_id' => $bu->organization_id,
            'business_unit_id' => $branch->business_unit_id,
            'branch_id' => $branch->id,
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $po?->id,
            'supplier_bill_number' => $supplierBillNumber,
            'internal_reference' => $internalReference,
            'bill_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => '0.0000',
            'tax_amount' => '0.0000',
            'total' => '0.0000',
            'amount_paid' => '0.0000',
            'balance_due' => '0.0000',
            'status' => SupplierInvoiceStatus::DRAFT,
            'match_status' => SupplierInvoiceMatchStatus::UNMATCHED,
            'created_by' => $userId,
        ]);
        $bill->save();

        return $bill;
    }

    public function addLine(
        SupplierInvoice $bill,
        Item $item,
        float|string $quantity,
        float|string $unitPrice,
        ?TaxCategory $taxCategory = null,
        ?string $description = null,
        ?PurchaseReceiptLine $receiptLine = null
    ): SupplierInvoiceLine {
        if ($bill->status !== SupplierInvoiceStatus::DRAFT) {
            throw new InvalidStateTransitionException('Cannot add lines. Supplier invoice is no longer in DRAFT status.');
        }

        $qtyStr = bcadd((string) $quantity, '0', 4);
        if (bccomp($qtyStr, '0', 4) <= 0) {
            throw new RuntimeException('Quantity must be greater than zero.');
        }

        $itemBu = $item->businessUnit;
        if ($itemBu && (int) $itemBu->organization_id !== (int) $bill->organization_id) {
            throw new CrossOrganizationException("Item {$item->id} does not belong to Organization {$bill->organization_id}.");
        }

        $priceStr = bcadd((string) $unitPrice, '0', 4);
        $taxRateStr = '0.00';
        if ($taxCategory) {
            if ((int) $taxCategory->organization_id !== (int) $bill->organization_id) {
                throw new CrossOrganizationException("TaxCategory {$taxCategory->id} does not belong to Organization {$bill->organization_id}.");
            }
            $taxRateStr = bcadd((string) ($taxCategory->rate ?? '0'), '0', 2);
        }

        $subtotal = bcmul($priceStr, $qtyStr, 4);
        $taxAmount = bcmul($subtotal, bcdiv($taxRateStr, '100', 6), 4);
        $total = bcadd($subtotal, $taxAmount, 4);

        return DB::transaction(function () use ($bill, $item, $taxCategory, $receiptLine, $qtyStr, $priceStr, $taxRateStr, $taxAmount, $subtotal, $total, $description) {
            $line = $bill->lines()->create([
                'item_id' => $item->id,
                'unit_id' => $item->unit_id,
                'tax_category_id' => $taxCategory?->id,
                'purchase_receipt_line_id' => $receiptLine?->id,
                'description' => $description ?? $item->name,
                'quantity' => $qtyStr,
                'unit_price' => $priceStr,
                'tax_rate_snapshot' => $taxRateStr,
                'tax_amount' => $taxAmount,
                'subtotal' => $subtotal,
                'total' => $total,
                'received_quantity_reference' => $receiptLine ? (string) $receiptLine->quantity : null,
            ]);

            $this->recalculateBillTotals($bill);

            return $line;
        });
    }

    public function removeLine(SupplierInvoice $bill, SupplierInvoiceLine $line): void
    {
        if ($bill->status !== SupplierInvoiceStatus::DRAFT) {
            throw new InvalidStateTransitionException('Cannot remove lines. Supplier invoice is no longer in DRAFT status.');
        }

        if ((int) $line->supplier_invoice_id !== (int) $bill->id) {
            throw new RuntimeException('SupplierInvoiceLine does not belong to this SupplierInvoice.');
        }

        DB::transaction(function () use ($bill, $line) {
            $line->delete();
            $this->recalculateBillTotals($bill);
        });
    }

    public function receiveBill(SupplierInvoice $bill): SupplierInvoice
    {
        return DB::transaction(function () use ($bill) {
            $lockedBill = SupplierInvoice::withoutGlobalScopes()->where('id', $bill->id)->lockForUpdate()->first();

            if ($lockedBill->status === SupplierInvoiceStatus::RECEIVED) {
                return $lockedBill;
            }

            if ($lockedBill->status !== SupplierInvoiceStatus::DRAFT) {
                throw new InvalidStateTransitionException("Cannot transition bill to RECEIVED from status {$lockedBill->status->value}.");
            }

            if ($lockedBill->lines()->count() === 0) {
                throw new RuntimeException('Cannot receive an empty supplier invoice.');
            }

            $lockedBill->status = SupplierInvoiceStatus::RECEIVED;
            $lockedBill->save();

            return $lockedBill;
        });
    }

    public function approveBill(SupplierInvoice $bill, User $approvingManager): SupplierInvoice
    {
        return DB::transaction(function () use ($bill, $approvingManager) {
            $lockedBill = SupplierInvoice::withoutGlobalScopes()->where('id', $bill->id)->lockForUpdate()->first();

            if ($lockedBill->status === SupplierInvoiceStatus::APPROVED) {
                return $lockedBill;
            }

            if (! in_array($lockedBill->status, [SupplierInvoiceStatus::DRAFT, SupplierInvoiceStatus::RECEIVED], true)) {
                throw new InvalidStateTransitionException("Cannot approve bill in status {$lockedBill->status->value}.");
            }

            if ($lockedBill->lines()->count() === 0) {
                throw new RuntimeException('Cannot approve an empty supplier invoice.');
            }

            $lockedBill->status = SupplierInvoiceStatus::APPROVED;
            $lockedBill->approved_by = $approvingManager->id;
            $lockedBill->approved_at = now();
            $lockedBill->balance_due = bcsub((string) $lockedBill->total, (string) $lockedBill->amount_paid, 4);
            $lockedBill->save();

            event(new SupplierInvoiceApproved($lockedBill));

            return $lockedBill;
        });
    }

    public function voidBill(SupplierInvoice $bill, string $reason): SupplierInvoice
    {
        return DB::transaction(function () use ($bill) {
            $lockedBill = SupplierInvoice::withoutGlobalScopes()->where('id', $bill->id)->lockForUpdate()->first();

            if ($lockedBill->status === SupplierInvoiceStatus::VOIDED) {
                return $lockedBill;
            }

            if (in_array($lockedBill->status, [SupplierInvoiceStatus::PAID], true)) {
                throw new InvalidStateTransitionException('Cannot void a PAID supplier invoice.');
            }

            if (bccomp((string) $lockedBill->amount_paid, '0', 4) > 0) {
                throw new InvalidStateTransitionException('Cannot void supplier invoice with existing payment allocations.');
            }

            $lockedBill->status = SupplierInvoiceStatus::VOIDED;
            $lockedBill->save();

            return $lockedBill;
        });
    }

    public function recalculateBillTotals(SupplierInvoice $bill): void
    {
        $subtotal = '0.0000';
        $taxTotal = '0.0000';
        $total = '0.0000';

        foreach ($bill->lines as $line) {
            $subtotal = bcadd($subtotal, (string) $line->subtotal, 4);
            $taxTotal = bcadd($taxTotal, (string) $line->tax_amount, 4);
            $total = bcadd($total, (string) $line->total, 4);
        }

        $bill->subtotal = $subtotal;
        $bill->tax_amount = $taxTotal;
        $bill->total = $total;
        $bill->balance_due = bcsub($total, (string) $bill->amount_paid, 4);
        $bill->save();
    }
}

<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Enums\PaymentMethod;
use App\Domains\Core\Enums\PurchaseOrderStatus;
use App\Domains\Core\Events\PurchaseReceiptConfirmed;
use App\Domains\Core\Events\SupplierPaymentRecorded;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\PurchaseOrderLine;
use App\Domains\Core\Models\PurchaseReceipt;
use App\Domains\Core\Models\Supplier;
use App\Domains\Core\Models\SupplierPayment;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function createDraft(Branch $branch, Supplier $supplier, ?int $userId = null): PurchaseOrder
    {
        if ($supplier->organization_id !== $branch->businessUnit->organization_id) {
            throw new Exception('Cross-Organization procurement is strictly forbidden.');
        }

        $order = new PurchaseOrder;
        $order->forceFill([
            'branch_id' => $branch->id,
            'business_unit_id' => $branch->business_unit_id,
            'supplier_id' => $supplier->id,
            'status' => PurchaseOrderStatus::DRAFT,
            'subtotal' => 0,
            'tax_total' => 0,
            'total' => 0,
            'created_by' => $userId,
        ]);
        $order->save();

        return $order;
    }

    public function addLine(PurchaseOrder $order, Item $item, float $quantity, float $unitPrice, float $taxAmount = 0): PurchaseOrderLine
    {
        if (! in_array($order->status, [PurchaseOrderStatus::DRAFT, PurchaseOrderStatus::SUBMITTED])) {
            throw new Exception('Cannot add items. Order is no longer in DRAFT or SUBMITTED state.');
        }

        if ($quantity <= 0 || $unitPrice < 0 || $taxAmount < 0) {
            throw new Exception('Invalid quantity, price, or tax amount.');
        }

        if ($item->business_unit_id !== $order->business_unit_id) {
            throw new Exception("Cross-BU isolation violation: Item belongs to BU {$item->business_unit_id}, PO belongs to BU {$order->business_unit_id}.");
        }

        return DB::transaction(function () use ($order, $item, $quantity, $unitPrice, $taxAmount) {
            $subtotal = $unitPrice * $quantity;
            $total = $subtotal + $taxAmount;

            $line = $order->lines()->create([
                'item_id' => $item->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'tax_amount' => $taxAmount,
                'subtotal' => $subtotal,
                'total' => $total,
                'received_quantity' => 0,
            ]);

            $this->recalculateTotals($order);

            return $line;
        });
    }

    public function removeLine(PurchaseOrder $order, PurchaseOrderLine $line): void
    {
        if (! in_array($order->status, [PurchaseOrderStatus::DRAFT, PurchaseOrderStatus::SUBMITTED])) {
            throw new Exception('Cannot remove items. Order is no longer in DRAFT or SUBMITTED state.');
        }

        if ($line->purchase_order_id !== $order->id) {
            throw new Exception('Line does not belong to this order.');
        }

        DB::transaction(function () use ($order, $line) {
            $line->delete();
            $this->recalculateTotals($order);
        });
    }

    public function submitOrder(PurchaseOrder $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = PurchaseOrder::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->status !== PurchaseOrderStatus::DRAFT) {
                return; // Idempotent
            }

            if ($lockedOrder->lines()->count() === 0) {
                throw new Exception('Cannot submit an empty order.');
            }

            $lockedOrder->forceFill(['status' => PurchaseOrderStatus::SUBMITTED])->save();
            $order->forceFill(['status' => PurchaseOrderStatus::SUBMITTED]); // Sync memory
        });
    }

    public function approveOrder(PurchaseOrder $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = PurchaseOrder::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->status !== PurchaseOrderStatus::SUBMITTED) {
                throw new Exception('Order must be SUBMITTED before it can be APPROVED.');
            }

            $lockedOrder->forceFill(['status' => PurchaseOrderStatus::APPROVED])->save();
            $order->forceFill(['status' => PurchaseOrderStatus::APPROVED]);
        });
    }

    /**
     * @param  array  $receiptLines  Array of associative arrays: [['line_id' => 1, 'quantity' => 10], ...]
     */
    public function receiveGoods(PurchaseOrder $order, InventoryLocation $location, array $receiptLines, ?string $reference = null, ?int $userId = null): PurchaseReceipt
    {
        if (! in_array($order->status, [PurchaseOrderStatus::APPROVED, PurchaseOrderStatus::PARTIAL_RECEIVED])) {
            throw new Exception('Order must be APPROVED or PARTIAL_RECEIVED to receive goods.');
        }

        if ($location->branch->business_unit_id !== $order->business_unit_id) {
            throw new Exception("Cross-BU isolation violation: Location belongs to BU {$location->branch->business_unit_id}.");
        }

        return DB::transaction(function () use ($order, $location, $receiptLines, $reference, $userId) {
            $lockedOrder = PurchaseOrder::where('id', $order->id)->lockForUpdate()->first();

            if (! in_array($lockedOrder->status, [PurchaseOrderStatus::APPROVED, PurchaseOrderStatus::PARTIAL_RECEIVED])) {
                throw new Exception('Order status changed concurrently.');
            }

            $receipt = $lockedOrder->receipts()->create([
                'inventory_location_id' => $location->id,
                'reference_number' => $reference,
                'received_at' => now(),
                'created_by' => $userId,
            ]);

            foreach ($receiptLines as $receiptData) {
                $lineId = $receiptData['line_id'];
                $quantityToReceive = (float) $receiptData['quantity'];

                if ($quantityToReceive <= 0) {
                    throw new Exception('Cannot receive zero or negative quantity.');
                }

                $poLine = PurchaseOrderLine::where('id', $lineId)->where('purchase_order_id', $lockedOrder->id)->first();
                if (! $poLine) {
                    throw new Exception('Purchase Order Line not found.');
                }

                if (($poLine->received_quantity + $quantityToReceive) > $poLine->quantity) {
                    throw new Exception("Cannot over-receive line item (Ordered: {$poLine->quantity}, Already Received: {$poLine->received_quantity}, Attempting: {$quantityToReceive}).");
                }

                // Update line received quantity
                $poLine->received_quantity += $quantityToReceive;
                $poLine->save();

                // Create receipt line
                $receipt->lines()->create([
                    'purchase_order_line_id' => $poLine->id,
                    'item_id' => $poLine->item_id,
                    'quantity' => $quantityToReceive,
                ]);
            }

            // Update PO Status based on line fulfillment
            $allFullyReceived = true;
            foreach ($lockedOrder->lines as $line) {
                if ($line->received_quantity < $line->quantity) {
                    $allFullyReceived = false;
                    break;
                }
            }

            $newStatus = $allFullyReceived ? PurchaseOrderStatus::RECEIVED : PurchaseOrderStatus::PARTIAL_RECEIVED;
            $lockedOrder->forceFill(['status' => $newStatus])->save();
            $order->forceFill(['status' => $newStatus]); // Sync memory

            // Dispatch event for Inventory Integration
            PurchaseReceiptConfirmed::dispatch($receipt);

            return $receipt;
        });
    }

    public function recordPayment(PurchaseOrder $order, float $amount, PaymentMethod $method, ?string $reference = null, ?int $userId = null): SupplierPayment
    {
        if ($order->status === PurchaseOrderStatus::CANCELLED) {
            throw new Exception('Cannot apply payments to a CANCELLED order.');
        }

        if ($amount <= 0) {
            throw new Exception('Payment amount must be greater than zero.');
        }

        return DB::transaction(function () use ($order, $amount, $method, $reference, $userId) {
            $lockedOrder = PurchaseOrder::where('id', $order->id)->lockForUpdate()->first();

            $payment = $lockedOrder->payments()->create([
                'amount' => $amount,
                'method' => $method,
                'reference' => $reference,
                'created_by' => $userId,
            ]);

            SupplierPaymentRecorded::dispatch($payment);

            return $payment;
        });
    }

    public function cancelOrder(PurchaseOrder $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = PurchaseOrder::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->status === PurchaseOrderStatus::CANCELLED) {
                return; // Idempotent
            }

            if (in_array($lockedOrder->status, [PurchaseOrderStatus::PARTIAL_RECEIVED, PurchaseOrderStatus::RECEIVED])) {
                throw new Exception('Cannot cancel a PO that has already been partially or fully received. Use a Return-to-Vendor workflow instead.');
            }

            $lockedOrder->forceFill(['status' => PurchaseOrderStatus::CANCELLED])->save();
            $order->forceFill(['status' => PurchaseOrderStatus::CANCELLED]);
        });
    }

    private function recalculateTotals(PurchaseOrder $order): void
    {
        $order->forceFill([
            'subtotal' => $order->lines()->sum('subtotal'),
            'tax_total' => $order->lines()->sum('tax_amount'),
            'total' => $order->lines()->sum('total'),
        ])->save();
    }
}

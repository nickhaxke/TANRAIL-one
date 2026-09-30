<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Enums\OrderStatus;
use App\Domains\Core\Enums\PaymentMethod;
use App\Domains\Core\Events\OrderCancelled;
use App\Domains\Core\Events\OrderConfirmed;
use App\Domains\Core\Events\SalesPaymentReceived;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\Customer;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\Order;
use App\Domains\Core\Models\OrderLine;
use App\Domains\Core\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createDraft(Branch $branch, ?Customer $customer = null, ?int $userId = null): Order
    {
        // If customer is provided, they must belong to the same Organization as the Branch's BU
        if ($customer && $customer->organization_id !== $branch->businessUnit->organization_id) {
            throw new Exception('Customer does not belong to the correct Organization.');
        }

        $order = new Order;
        $order->forceFill([
            'branch_id' => $branch->id,
            'business_unit_id' => $branch->business_unit_id,
            'customer_id' => $customer?->id,
            'status' => OrderStatus::DRAFT,
            'subtotal' => 0,
            'tax_total' => 0,
            'total' => 0,
            'created_by' => $userId,
        ]);
        $order->save();

        return $order;
    }

    public function addItem(Order $order, Item $item, float $quantity): OrderLine
    {
        if ($order->status !== OrderStatus::DRAFT) {
            throw new Exception('Cannot add items. Order is no longer in DRAFT state.');
        }

        if ($quantity <= 0) {
            throw new Exception('Quantity must be greater than zero.');
        }

        if ($item->business_unit_id !== $order->branch->business_unit_id) {
            throw new Exception("Cross-BU isolation violation: Item belongs to BU {$item->business_unit_id}, Order belongs to BU {$order->branch->business_unit_id}.");
        }

        return DB::transaction(function () use ($order, $item, $quantity) {
            // Recalculate totals
            $unitPrice = $item->base_price;
            $subtotal = $unitPrice * $quantity;
            $taxAmount = 0; // Simplified for this phase. Future: calculate from TaxCategory.
            $total = $subtotal + $taxAmount;

            $line = $order->lines()->create([
                'item_id' => $item->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'tax_amount' => $taxAmount,
                'subtotal' => $subtotal,
                'total' => $total,
            ]);

            $this->recalculateOrderTotals($order);

            return $line;
        });
    }

    public function removeItem(Order $order, OrderLine $orderLine): void
    {
        if ($order->status !== OrderStatus::DRAFT) {
            throw new Exception('Cannot remove items. Order is no longer in DRAFT state.');
        }

        if ($orderLine->order_id !== $order->id) {
            throw new Exception('OrderLine does not belong to this order.');
        }

        DB::transaction(function () use ($order, $orderLine) {
            $orderLine->delete();
            $this->recalculateOrderTotals($order);
        });
    }

    public function confirmOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Pessimistic lock to prevent race conditions during confirmation
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->status !== OrderStatus::DRAFT) {
                return; // Idempotent: silently return if already out of draft state
            }

            if ($lockedOrder->lines()->count() === 0) {
                throw new Exception('Cannot confirm an empty order.');
            }

            $lockedOrder->status = OrderStatus::CONFIRMED;
            $lockedOrder->save();

            // Fire event. This runs synchronously in the current transaction.
            // DeductInventoryListener will issue stock. If it fails, it throws Exception rolling back.
            OrderConfirmed::dispatch($lockedOrder);
        });
    }

    public function fulfillOrder(Order $order): void
    {
        if ($order->status !== OrderStatus::CONFIRMED) {
            throw new Exception('Order must be CONFIRMED before it can be FULFILLED.');
        }

        $order->status = OrderStatus::FULFILLED;
        $order->save();

        $this->checkCompletionStatus($order);
    }

    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->status === OrderStatus::CANCELLED) {
                return; // Idempotent
            }

            if ($lockedOrder->status === OrderStatus::COMPLETED) {
                throw new Exception('Cannot cancel a COMPLETED order.');
            }

            $previousStatus = $lockedOrder->status;
            $lockedOrder->status = OrderStatus::CANCELLED;
            $lockedOrder->save();

            if (in_array($previousStatus, [OrderStatus::CONFIRMED, OrderStatus::FULFILLED])) {
                // Return inventory
                OrderCancelled::dispatch($lockedOrder);
            }
        });
    }

    public function recordPayment(Order $order, float $amount, PaymentMethod $method, ?string $reference = null, ?int $userId = null): Payment
    {
        if ($order->status === OrderStatus::CANCELLED) {
            throw new Exception('Cannot apply payments to a CANCELLED order.');
        }

        if ($amount <= 0) {
            throw new Exception('Payment amount must be greater than zero.');
        }

        return DB::transaction(function () use ($order, $amount, $method, $reference, $userId) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            $payment = $lockedOrder->payments()->create([
                'amount' => $amount,
                'method' => $method,
                'reference' => $reference,
                'created_by' => $userId,
            ]);

            $this->checkCompletionStatus($lockedOrder);

            SalesPaymentReceived::dispatch($payment);

            return $payment;
        });
    }

    private function recalculateOrderTotals(Order $order): void
    {
        $order->subtotal = $order->lines()->sum('subtotal');
        $order->tax_total = $order->lines()->sum('tax_amount');
        $order->total = $order->lines()->sum('total');
        $order->save();
    }

    private function checkCompletionStatus(Order $order): void
    {
        if ($order->status === OrderStatus::FULFILLED) {
            $totalPayments = $order->payments()->sum('amount');
            if ($totalPayments >= $order->total) {
                $order->status = OrderStatus::COMPLETED;
                $order->save();
            }
        }
    }
}

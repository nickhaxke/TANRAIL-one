<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Enums\InvoiceStatus;
use App\Domains\Core\Enums\SupplierInvoiceStatus;
use App\Domains\Core\Events\PaymentAllocated;
use App\Domains\Core\Events\SupplierPaymentAllocated;
use App\Domains\Core\Exceptions\AllocationException;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Models\Invoice;
use App\Domains\Core\Models\Order;
use App\Domains\Core\Models\Payment;
use App\Domains\Core\Models\PaymentAllocation;
use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\SupplierInvoice;
use App\Domains\Core\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;

class PaymentAllocationService
{
    public function allocateCustomerPayment(
        Payment $payment,
        Invoice $invoice,
        float|string $amount,
        ?int $userId = null
    ): PaymentAllocation {
        $amountStr = bcadd((string) $amount, '0', 4);
        if (bccomp($amountStr, '0', 4) <= 0) {
            throw new AllocationException('Allocation amount must be greater than zero.');
        }

        return DB::transaction(function () use ($payment, $invoice, $amountStr, $userId) {
            /** @var Payment $lockedPayment */
            $lockedPayment = Payment::withoutGlobalScopes()->where('id', $payment->id)->lockForUpdate()->first();
            /** @var Invoice $lockedInvoice */
            $lockedInvoice = Invoice::withoutGlobalScopes()->where('id', $invoice->id)->lockForUpdate()->first();

            if (! $lockedPayment || ! $lockedInvoice) {
                throw new AllocationException('Payment or Invoice record not found for allocation.');
            }

            // Document status check
            if (in_array($lockedInvoice->status, [InvoiceStatus::DRAFT, InvoiceStatus::CANCELLED, InvoiceStatus::VOIDED], true)) {
                throw new AllocationException("Cannot allocate payment to invoice in status '{$lockedInvoice->status->value}'.");
            }

            // Tenant security boundary validation - resolve payment tenant context without global scopes
            $paymentOrgId = null;
            $paymentBuId = null;

            if ($lockedPayment->invoice_id) {
                $parentInvoice = Invoice::withoutGlobalScopes()->find($lockedPayment->invoice_id);
                if ($parentInvoice) {
                    $paymentOrgId = $parentInvoice->organization_id;
                    $paymentBuId = $parentInvoice->business_unit_id;
                }
            } elseif ($lockedPayment->order_id) {
                $parentOrder = Order::withoutGlobalScopes()->find($lockedPayment->order_id);
                if ($parentOrder) {
                    $paymentOrgId = $parentOrder->branch?->businessUnit?->organization_id ?? $parentOrder->business_unit_id;
                    $paymentBuId = $parentOrder->business_unit_id;
                }
            }

            $paymentOrgId = $paymentOrgId ?? $lockedInvoice->organization_id;
            $paymentBuId = $paymentBuId ?? $lockedInvoice->business_unit_id;

            if ((int) $paymentOrgId !== (int) $lockedInvoice->organization_id) {
                throw new CrossOrganizationException("Payment tenant Organization {$paymentOrgId} does not match Invoice Organization {$lockedInvoice->organization_id}.");
            }

            if ((int) $paymentBuId !== (int) $lockedInvoice->business_unit_id) {
                throw new CrossOrganizationException("Payment tenant Business Unit {$paymentBuId} does not match Invoice Business Unit {$lockedInvoice->business_unit_id}.");
            }

            // Calculate available unallocated payment balance
            $availablePayment = (string) ($lockedPayment->unallocated_amount ?? $lockedPayment->amount);
            if (bccomp($amountStr, $availablePayment, 4) > 0) {
                throw new AllocationException("Allocation amount ({$amountStr}) exceeds available payment balance ({$availablePayment}).");
            }

            // Calculate outstanding invoice balance
            $balanceDue = (string) $lockedInvoice->balance_due;
            if (bccomp($amountStr, $balanceDue, 4) > 0) {
                throw new AllocationException("Allocation amount ({$amountStr}) exceeds invoice balance due ({$balanceDue}).");
            }

            // Execute allocation
            $allocation = PaymentAllocation::create([
                'organization_id' => $lockedInvoice->organization_id,
                'business_unit_id' => $lockedInvoice->business_unit_id,
                'payment_type' => Payment::class,
                'payment_id' => $lockedPayment->id,
                'allocatable_type' => Invoice::class,
                'allocatable_id' => $lockedInvoice->id,
                'amount' => $amountStr,
                'allocation_date' => now()->toDateString(),
                'created_by' => $userId,
            ]);

            // Update payment unallocated amount
            $lockedPayment->unallocated_amount = bcsub($availablePayment, $amountStr, 4);
            $lockedPayment->save();

            // Update invoice settlement amounts & status
            $newPaid = bcadd((string) $lockedInvoice->amount_paid, $amountStr, 4);
            $newBalance = bcsub((string) $lockedInvoice->total, $newPaid, 4);

            $lockedInvoice->amount_paid = $newPaid;
            $lockedInvoice->balance_due = $newBalance;

            if (bccomp($newBalance, '0', 4) <= 0) {
                $lockedInvoice->status = InvoiceStatus::PAID;
            } else {
                $lockedInvoice->status = InvoiceStatus::PARTIALLY_PAID;
            }
            $lockedInvoice->save();

            event(new PaymentAllocated($allocation));

            return $allocation;
        });
    }

    public function allocateSupplierPayment(
        SupplierPayment $payment,
        SupplierInvoice $bill,
        float|string $amount,
        ?int $userId = null
    ): PaymentAllocation {
        $amountStr = bcadd((string) $amount, '0', 4);
        if (bccomp($amountStr, '0', 4) <= 0) {
            throw new AllocationException('Allocation amount must be greater than zero.');
        }

        return DB::transaction(function () use ($payment, $bill, $amountStr, $userId) {
            /** @var SupplierPayment $lockedPayment */
            $lockedPayment = SupplierPayment::withoutGlobalScopes()->where('id', $payment->id)->lockForUpdate()->first();
            /** @var SupplierInvoice $lockedBill */
            $lockedBill = SupplierInvoice::withoutGlobalScopes()->where('id', $bill->id)->lockForUpdate()->first();

            if (! $lockedPayment || ! $lockedBill) {
                throw new AllocationException('Supplier Payment or Vendor Bill record not found for allocation.');
            }

            if (in_array($lockedBill->status, [SupplierInvoiceStatus::DRAFT, SupplierInvoiceStatus::VOIDED], true)) {
                throw new AllocationException("Cannot allocate payment to supplier invoice in status '{$lockedBill->status->value}'.");
            }

            $paymentOrgId = null;
            $paymentBuId = null;

            if ($lockedPayment->supplier_invoice_id) {
                $parentBill = SupplierInvoice::withoutGlobalScopes()->find($lockedPayment->supplier_invoice_id);
                if ($parentBill) {
                    $paymentOrgId = $parentBill->organization_id;
                    $paymentBuId = $parentBill->business_unit_id;
                }
            } elseif ($lockedPayment->purchase_order_id) {
                $parentPo = PurchaseOrder::withoutGlobalScopes()->find($lockedPayment->purchase_order_id);
                if ($parentPo) {
                    $paymentOrgId = $parentPo->branch?->businessUnit?->organization_id ?? $parentPo->business_unit_id;
                    $paymentBuId = $parentPo->business_unit_id;
                }
            }

            $paymentOrgId = $paymentOrgId ?? $lockedBill->organization_id;
            $paymentBuId = $paymentBuId ?? $lockedBill->business_unit_id;

            if ((int) $paymentOrgId !== (int) $lockedBill->organization_id) {
                throw new CrossOrganizationException("Supplier payment Organization {$paymentOrgId} does not match Vendor Bill Organization {$lockedBill->organization_id}.");
            }

            if ((int) $paymentBuId !== (int) $lockedBill->business_unit_id) {
                throw new CrossOrganizationException("Supplier payment Business Unit {$paymentBuId} does not match Vendor Bill Business Unit {$lockedBill->business_unit_id}.");
            }

            $availablePayment = (string) ($lockedPayment->unallocated_amount ?? $lockedPayment->amount);
            if (bccomp($amountStr, $availablePayment, 4) > 0) {
                throw new AllocationException("Allocation amount ({$amountStr}) exceeds available supplier payment balance ({$availablePayment}).");
            }

            $balanceDue = (string) $lockedBill->balance_due;
            if (bccomp($amountStr, $balanceDue, 4) > 0) {
                throw new AllocationException("Allocation amount ({$amountStr}) exceeds supplier bill balance due ({$balanceDue}).");
            }

            $allocation = PaymentAllocation::create([
                'organization_id' => $lockedBill->organization_id,
                'business_unit_id' => $lockedBill->business_unit_id,
                'payment_type' => SupplierPayment::class,
                'payment_id' => $lockedPayment->id,
                'allocatable_type' => SupplierInvoice::class,
                'allocatable_id' => $lockedBill->id,
                'amount' => $amountStr,
                'allocation_date' => now()->toDateString(),
                'created_by' => $userId,
            ]);

            $lockedPayment->unallocated_amount = bcsub($availablePayment, $amountStr, 4);
            $lockedPayment->save();

            $newPaid = bcadd((string) $lockedBill->amount_paid, $amountStr, 4);
            $newBalance = bcsub((string) $lockedBill->total, $newPaid, 4);

            $lockedBill->amount_paid = $newPaid;
            $lockedBill->balance_due = $newBalance;

            if (bccomp($newBalance, '0', 4) <= 0) {
                $lockedBill->status = SupplierInvoiceStatus::PAID;
            } else {
                $lockedBill->status = SupplierInvoiceStatus::PARTIALLY_PAID;
            }
            $lockedBill->save();

            event(new SupplierPaymentAllocated($allocation));

            return $allocation;
        });
    }
}

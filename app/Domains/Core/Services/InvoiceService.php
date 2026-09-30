<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Enums\InvoiceStatus;
use App\Domains\Core\Events\InvoiceIssued;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Exceptions\InvalidStateTransitionException;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\Customer;
use App\Domains\Core\Models\Invoice;
use App\Domains\Core\Models\InvoiceLine;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\Order;
use App\Domains\Core\Models\TaxCategory;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InvoiceService
{
    public function createDraft(
        Branch $branch,
        Customer $customer,
        ?string $invoiceNumber = null,
        ?Order $order = null,
        string $currency = 'TZS',
        ?int $userId = null
    ): Invoice {
        $bu = $branch->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($branch->business_unit_id);
        if ($customer->organization_id !== $bu->organization_id) {
            throw new CrossOrganizationException("Customer {$customer->id} does not belong to Organization {$bu->organization_id}.");
        }

        if ($order && $order->business_unit_id !== $branch->business_unit_id) {
            throw new CrossOrganizationException("Order {$order->id} does not belong to Business Unit {$branch->business_unit_id}.");
        }

        $number = $invoiceNumber ?? ($branch->code.'-INV-'.str_pad((string) (Invoice::withoutGlobalScopes()->where('business_unit_id', $branch->business_unit_id)->count() + 1), 5, '0', STR_PAD_LEFT));

        $invoice = new Invoice;
        $invoice->forceFill([
            'organization_id' => $bu->organization_id,
            'business_unit_id' => $branch->business_unit_id,
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'order_id' => $order?->id,
            'invoice_number' => $number,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'currency' => $currency,
            'subtotal' => '0.0000',
            'discount_amount' => '0.0000',
            'tax_amount' => '0.0000',
            'total' => '0.0000',
            'amount_paid' => '0.0000',
            'balance_due' => '0.0000',
            'status' => InvoiceStatus::DRAFT,
            'created_by' => $userId,
        ]);
        $invoice->save();

        return $invoice;
    }

    public function addLine(
        Invoice $invoice,
        Item $item,
        float|string $quantity,
        float|string|null $unitPrice = null,
        ?TaxCategory $taxCategory = null,
        ?string $description = null
    ): InvoiceLine {
        if ($invoice->status !== InvoiceStatus::DRAFT) {
            throw new InvalidStateTransitionException('Cannot add lines. Invoice is no longer in DRAFT status.');
        }

        $qtyStr = bcadd((string) $quantity, '0', 4);
        if (bccomp($qtyStr, '0', 4) <= 0) {
            throw new RuntimeException('Quantity must be greater than zero.');
        }

        $itemBu = $item->businessUnit;
        if ($itemBu && (int) $itemBu->organization_id !== (int) $invoice->organization_id) {
            throw new CrossOrganizationException("Item {$item->id} does not belong to Organization {$invoice->organization_id}.");
        }

        $priceStr = bcadd((string) ($unitPrice ?? $item->base_price ?? '0'), '0', 4);
        $taxRateStr = '0.00';
        if ($taxCategory) {
            if ((int) $taxCategory->organization_id !== (int) $invoice->organization_id) {
                throw new CrossOrganizationException("TaxCategory {$taxCategory->id} does not belong to Organization {$invoice->organization_id}.");
            }
            $taxRateStr = bcadd((string) ($taxCategory->rate ?? '0'), '0', 2);
        }

        $subtotal = bcmul($priceStr, $qtyStr, 4);
        $taxAmount = bcmul($subtotal, bcdiv($taxRateStr, '100', 6), 4);
        $total = bcadd($subtotal, $taxAmount, 4);

        return DB::transaction(function () use ($invoice, $item, $taxCategory, $qtyStr, $priceStr, $taxRateStr, $taxAmount, $subtotal, $total, $description) {
            $line = $invoice->lines()->create([
                'item_id' => $item->id,
                'unit_id' => $item->unit_id,
                'tax_category_id' => $taxCategory?->id,
                'item_name_snapshot' => $item->name,
                'description' => $description ?? $item->name,
                'quantity' => $qtyStr,
                'unit_price' => $priceStr,
                'discount_amount' => '0.0000',
                'tax_rate_snapshot' => $taxRateStr,
                'tax_amount' => $taxAmount,
                'subtotal' => $subtotal,
                'total' => $total,
            ]);

            $this->recalculateInvoiceTotals($invoice);

            return $line;
        });
    }

    public function removeLine(Invoice $invoice, InvoiceLine $line): void
    {
        if ($invoice->status !== InvoiceStatus::DRAFT) {
            throw new InvalidStateTransitionException('Cannot remove lines. Invoice is no longer in DRAFT status.');
        }

        if ((int) $line->invoice_id !== (int) $invoice->id) {
            throw new RuntimeException('InvoiceLine does not belong to this Invoice.');
        }

        DB::transaction(function () use ($invoice, $line) {
            $line->delete();
            $this->recalculateInvoiceTotals($invoice);
        });
    }

    public function issueInvoice(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $lockedInvoice = Invoice::withoutGlobalScopes()->where('id', $invoice->id)->lockForUpdate()->first();

            if ($lockedInvoice->status === InvoiceStatus::ISSUED) {
                return $lockedInvoice;
            }

            if ($lockedInvoice->status !== InvoiceStatus::DRAFT) {
                throw new InvalidStateTransitionException("Cannot issue invoice in status {$lockedInvoice->status->value}.");
            }

            if ($lockedInvoice->lines()->count() === 0) {
                throw new RuntimeException('Cannot issue an empty invoice.');
            }

            $lockedInvoice->status = InvoiceStatus::ISSUED;
            $lockedInvoice->issue_date = now()->toDateString();
            $lockedInvoice->balance_due = bcsub((string) $lockedInvoice->total, (string) $lockedInvoice->amount_paid, 4);
            $lockedInvoice->save();

            event(new InvoiceIssued($lockedInvoice));

            return $lockedInvoice;
        });
    }

    public function cancelInvoice(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $lockedInvoice = Invoice::withoutGlobalScopes()->where('id', $invoice->id)->lockForUpdate()->first();

            if ($lockedInvoice->status === InvoiceStatus::CANCELLED) {
                return $lockedInvoice;
            }

            if (in_array($lockedInvoice->status, [InvoiceStatus::PAID, InvoiceStatus::VOIDED], true)) {
                throw new InvalidStateTransitionException("Cannot cancel invoice in status {$lockedInvoice->status->value}.");
            }

            if (bccomp((string) $lockedInvoice->amount_paid, '0', 4) > 0) {
                throw new InvalidStateTransitionException('Cannot cancel invoice with existing payment allocations. Void or reverse allocations first.');
            }

            $lockedInvoice->status = InvoiceStatus::CANCELLED;
            $lockedInvoice->save();

            return $lockedInvoice;
        });
    }

    public function voidInvoice(Invoice $invoice, string $reason): Invoice
    {
        return DB::transaction(function () use ($invoice, $reason) {
            $lockedInvoice = Invoice::withoutGlobalScopes()->where('id', $invoice->id)->lockForUpdate()->first();

            if ($lockedInvoice->status === InvoiceStatus::VOIDED) {
                return $lockedInvoice;
            }

            if (in_array($lockedInvoice->status, [InvoiceStatus::DRAFT, InvoiceStatus::CANCELLED], true)) {
                throw new InvalidStateTransitionException("Cannot void invoice in status {$lockedInvoice->status->value}. Use CANCEL for drafts.");
            }

            $lockedInvoice->status = InvoiceStatus::VOIDED;
            $lockedInvoice->notes = ($lockedInvoice->notes ? $lockedInvoice->notes."\n" : '')."VOID REASON: {$reason}";
            $lockedInvoice->save();

            return $lockedInvoice;
        });
    }

    public function recalculateInvoiceTotals(Invoice $invoice): void
    {
        $invoice->load('lines');
        $subtotal = '0.0000';
        $taxTotal = '0.0000';
        $total = '0.0000';

        foreach ($invoice->lines as $line) {
            $subtotal = bcadd($subtotal, (string) $line->subtotal, 4);
            $taxTotal = bcadd($taxTotal, (string) $line->tax_amount, 4);
            $total = bcadd($total, (string) $line->total, 4);
        }

        $invoice->subtotal = $subtotal;
        $invoice->tax_amount = $taxTotal;
        $invoice->total = $total;
        $invoice->balance_due = bcsub($total, (string) $invoice->amount_paid, 4);
        $invoice->save();
    }
}

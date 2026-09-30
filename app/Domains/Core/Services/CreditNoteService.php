<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Enums\CreditNoteStatus;
use App\Domains\Core\Enums\InvoiceStatus;
use App\Domains\Core\Events\CreditNoteIssued;
use App\Domains\Core\Exceptions\AllocationException;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Exceptions\InvalidStateTransitionException;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\CreditNote;
use App\Domains\Core\Models\CreditNoteLine;
use App\Domains\Core\Models\Customer;
use App\Domains\Core\Models\Invoice;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\TaxCategory;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreditNoteService
{
    public function createDraft(
        Branch $branch,
        Customer $customer,
        string $reason,
        ?string $creditNoteNumber = null,
        ?Invoice $invoice = null,
        ?int $userId = null
    ): CreditNote {
        $bu = $branch->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($branch->business_unit_id);
        if ($customer->organization_id !== $bu->organization_id) {
            throw new CrossOrganizationException("Customer {$customer->id} does not belong to Organization {$bu->organization_id}.");
        }

        if ($invoice && $invoice->business_unit_id !== $branch->business_unit_id) {
            throw new CrossOrganizationException("Invoice {$invoice->id} does not belong to Business Unit {$branch->business_unit_id}.");
        }

        $number = $creditNoteNumber ?? ($branch->code.'-CN-'.str_pad((string) (CreditNote::withoutGlobalScopes()->where('business_unit_id', $branch->business_unit_id)->count() + 1), 5, '0', STR_PAD_LEFT));

        $note = new CreditNote;
        $note->forceFill([
            'organization_id' => $bu->organization_id,
            'business_unit_id' => $branch->business_unit_id,
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'invoice_id' => $invoice?->id,
            'credit_note_number' => $number,
            'issue_date' => now()->toDateString(),
            'subtotal' => '0.0000',
            'tax_amount' => '0.0000',
            'total' => '0.0000',
            'amount_allocated' => '0.0000',
            'remaining_credit' => '0.0000',
            'status' => CreditNoteStatus::DRAFT,
            'reason' => $reason,
            'created_by' => $userId,
        ]);
        $note->save();

        return $note;
    }

    public function addLine(
        CreditNote $note,
        Item $item,
        float|string $quantity,
        float|string $unitPrice,
        ?TaxCategory $taxCategory = null,
        ?string $description = null
    ): CreditNoteLine {
        if ($note->status !== CreditNoteStatus::DRAFT) {
            throw new InvalidStateTransitionException('Cannot add lines. Credit note is no longer in DRAFT status.');
        }

        $qtyStr = bcadd((string) $quantity, '0', 4);
        if (bccomp($qtyStr, '0', 4) <= 0) {
            throw new RuntimeException('Quantity must be greater than zero.');
        }

        $itemBu = $item->businessUnit;
        if ($itemBu && (int) $itemBu->organization_id !== (int) $note->organization_id) {
            throw new CrossOrganizationException("Item {$item->id} does not belong to Organization {$note->organization_id}.");
        }

        $priceStr = bcadd((string) $unitPrice, '0', 4);
        $taxRateStr = '0.00';
        if ($taxCategory) {
            if ((int) $taxCategory->organization_id !== (int) $note->organization_id) {
                throw new CrossOrganizationException("TaxCategory {$taxCategory->id} does not belong to Organization {$note->organization_id}.");
            }
            $taxRateStr = bcadd((string) ($taxCategory->rate ?? '0'), '0', 2);
        }

        $subtotal = bcmul($priceStr, $qtyStr, 4);
        $taxAmount = bcmul($subtotal, bcdiv($taxRateStr, '100', 6), 4);
        $total = bcadd($subtotal, $taxAmount, 4);

        return DB::transaction(function () use ($note, $item, $qtyStr, $priceStr, $taxAmount, $total, $description) {
            $line = $note->lines()->create([
                'item_id' => $item->id,
                'description' => $description ?? $item->name,
                'quantity' => $qtyStr,
                'unit_price' => $priceStr,
                'tax_amount' => $taxAmount,
                'total' => $total,
            ]);

            $this->recalculateCreditNoteTotals($note);

            return $line;
        });
    }

    public function removeLine(CreditNote $note, CreditNoteLine $line): void
    {
        if ($note->status !== CreditNoteStatus::DRAFT) {
            throw new InvalidStateTransitionException('Cannot remove lines. Credit note is no longer in DRAFT status.');
        }

        if ((int) $line->credit_note_id !== (int) $note->id) {
            throw new RuntimeException('CreditNoteLine does not belong to this CreditNote.');
        }

        DB::transaction(function () use ($note, $line) {
            $line->delete();
            $this->recalculateCreditNoteTotals($note);
        });
    }

    public function issueCreditNote(CreditNote $note): CreditNote
    {
        return DB::transaction(function () use ($note) {
            $lockedNote = CreditNote::withoutGlobalScopes()->where('id', $note->id)->lockForUpdate()->first();

            if ($lockedNote->status === CreditNoteStatus::ISSUED) {
                return $lockedNote;
            }

            if ($lockedNote->status !== CreditNoteStatus::DRAFT) {
                throw new InvalidStateTransitionException("Cannot issue credit note in status {$lockedNote->status->value}.");
            }

            if ($lockedNote->lines()->count() === 0) {
                throw new RuntimeException('Cannot issue an empty credit note.');
            }

            $lockedNote->status = CreditNoteStatus::ISSUED;
            $lockedNote->issue_date = now()->toDateString();
            $lockedNote->remaining_credit = bcsub((string) $lockedNote->total, (string) $lockedNote->amount_allocated, 4);
            $lockedNote->save();

            event(new CreditNoteIssued($lockedNote));

            return $lockedNote;
        });
    }

    public function voidCreditNote(CreditNote $note, string $reason): CreditNote
    {
        return DB::transaction(function () use ($note, $reason) {
            $lockedNote = CreditNote::withoutGlobalScopes()->where('id', $note->id)->lockForUpdate()->first();

            if ($lockedNote->status === CreditNoteStatus::VOIDED) {
                return $lockedNote;
            }

            if ($lockedNote->status === CreditNoteStatus::APPLIED) {
                throw new InvalidStateTransitionException('Cannot void an APPLIED credit note.');
            }

            if (bccomp((string) $lockedNote->amount_allocated, '0', 4) > 0) {
                throw new InvalidStateTransitionException('Cannot void a credit note with existing allocations.');
            }

            $lockedNote->status = CreditNoteStatus::VOIDED;
            $lockedNote->reason = $lockedNote->reason." | VOID: {$reason}";
            $lockedNote->save();

            return $lockedNote;
        });
    }

    public function recalculateCreditNoteTotals(CreditNote $note): void
    {
        $subtotal = '0.0000';
        $taxTotal = '0.0000';
        $total = '0.0000';

        $lines = $note->lines()->get();
        foreach ($lines as $line) {
            $lineSub = bcmul((string) $line->quantity, (string) $line->unit_price, 4);
            $subtotal = bcadd($subtotal, $lineSub, 4);
            $taxTotal = bcadd($taxTotal, (string) $line->tax_amount, 4);
            $total = bcadd($total, (string) $line->total, 4);
        }

        $note->subtotal = $subtotal;
        $note->tax_amount = $taxTotal;
        $note->total = $total;
        $note->remaining_credit = bcsub($total, (string) $note->amount_allocated, 4);
        $note->save();
    }

    /**
     * Apply an ISSUED CreditNote against a Customer Invoice.
     *
     * @throws AllocationException
     * @throws CrossOrganizationException
     * @throws InvalidStateTransitionException
     */
    public function applyCreditNoteToInvoice(
        CreditNote $note,
        Invoice $invoice,
        float|string $amount,
        ?int $userId = null
    ): CreditNote {
        $amountStr = bcadd((string) $amount, '0', 4);
        if (bccomp($amountStr, '0', 4) <= 0) {
            throw new AllocationException('Credit note application amount must be greater than zero.');
        }

        return DB::transaction(function () use ($note, $invoice, $amountStr) {
            /** @var CreditNote|null $lockedNote */
            $lockedNote = CreditNote::withoutGlobalScopes()->where('id', $note->id)->lockForUpdate()->first();
            /** @var Invoice|null $lockedInvoice */
            $lockedInvoice = Invoice::withoutGlobalScopes()->where('id', $invoice->id)->lockForUpdate()->first();

            if (! $lockedNote || ! $lockedInvoice) {
                throw new AllocationException('Credit Note or Invoice record not found for application.');
            }

            if ($lockedNote->status !== CreditNoteStatus::ISSUED) {
                throw new InvalidStateTransitionException("Cannot apply Credit Note in status '{$lockedNote->status->value}'. Must be ISSUED.");
            }

            if (in_array($lockedInvoice->status, [InvoiceStatus::DRAFT, InvoiceStatus::CANCELLED, InvoiceStatus::VOIDED], true)) {
                throw new InvalidStateTransitionException("Cannot apply Credit Note to Invoice in status '{$lockedInvoice->status->value}'.");
            }

            // Tenant security checks
            if ((int) $lockedNote->organization_id !== (int) $lockedInvoice->organization_id) {
                throw new CrossOrganizationException("Credit Note Organization {$lockedNote->organization_id} does not match Invoice Organization {$lockedInvoice->organization_id}.");
            }

            if ((int) $lockedNote->business_unit_id !== (int) $lockedInvoice->business_unit_id) {
                throw new CrossOrganizationException("Credit Note Business Unit {$lockedNote->business_unit_id} does not match Invoice Business Unit {$lockedInvoice->business_unit_id}.");
            }

            // Customer alignment check
            if ((int) $lockedNote->customer_id !== (int) $lockedInvoice->customer_id) {
                throw new AllocationException("Credit Note Customer {$lockedNote->customer_id} does not match Invoice Customer {$lockedInvoice->customer_id}.");
            }

            // Balance & Credit availability checks
            $remainingCredit = (string) $lockedNote->remaining_credit;
            if (bccomp($amountStr, $remainingCredit, 4) > 0) {
                throw new AllocationException("Application amount ({$amountStr}) exceeds Credit Note remaining credit ({$remainingCredit}).");
            }

            $balanceDue = (string) $lockedInvoice->balance_due;
            if (bccomp($amountStr, $balanceDue, 4) > 0) {
                throw new AllocationException("Application amount ({$amountStr}) exceeds Invoice balance due ({$balanceDue}).");
            }

            // Execute Credit Note Allocation
            $newAllocated = bcadd((string) $lockedNote->amount_allocated, $amountStr, 4);
            $newRemaining = bcsub((string) $lockedNote->total, $newAllocated, 4);

            $lockedNote->amount_allocated = $newAllocated;
            $lockedNote->remaining_credit = $newRemaining;

            if (bccomp($newRemaining, '0', 4) <= 0) {
                $lockedNote->status = CreditNoteStatus::APPLIED;
            }
            $lockedNote->save();

            // Update Invoice balance and status
            $newBalance = bcsub((string) $lockedInvoice->balance_due, $amountStr, 4);
            $lockedInvoice->balance_due = $newBalance;

            if (bccomp($newBalance, '0', 4) <= 0) {
                if (bccomp((string) $lockedInvoice->amount_paid, '0', 4) > 0) {
                    $lockedInvoice->status = InvoiceStatus::PAID;
                } else {
                    $lockedInvoice->status = InvoiceStatus::CANCELLED;
                }
            }
            $lockedInvoice->save();

            return $lockedNote;
        });
    }
}

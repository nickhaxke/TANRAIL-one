<?php

namespace App\Domains\Core\Listeners;

use App\Domains\Core\Enums\JournalType;
use App\Domains\Core\Events\CreditNoteIssued;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\FinancialSettings;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Services\AccountResolver;
use App\Domains\Core\Services\FinancialService;

class PostCreditNoteToGlListener
{
    public function __construct(
        private FinancialService $financialService,
        private AccountResolver $accountResolver
    ) {}

    public function handle(CreditNoteIssued $event): void
    {
        $creditNote = $event->creditNote;

        ProcessedEvent::process(get_class($event), $event->eventId, function () use ($creditNote) {
            $businessUnit = $creditNote->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($creditNote->business_unit_id);
            if (! $businessUnit) {
                return;
            }

            $hasSettings = FinancialSettings::withoutGlobalScopes()
                ->where('organization_id', $businessUnit->organization_id)
                ->exists();

            if (! $hasSettings) {
                return;
            }

            $salesReturnsAccount = $this->accountResolver->resolveSalesReturnsAccount($businessUnit);
            $arAccount = $this->accountResolver->resolve($businessUnit, 'default_accounts_receivable_account_id');

            $lines = [
                [
                    'account_id' => $salesReturnsAccount->id,
                    'debit' => $creditNote->subtotal,
                    'credit' => 0,
                    'description' => "Sales Returns & Allowances debited for Credit Note #{$creditNote->credit_note_number}",
                ],
            ];

            if (bccomp((string) $creditNote->tax_amount, '0', 4) > 0) {
                $taxAccount = $this->accountResolver->resolve($businessUnit, 'default_tax_liability_account_id');
                $lines[] = [
                    'account_id' => $taxAccount->id,
                    'debit' => $creditNote->tax_amount,
                    'credit' => 0,
                    'description' => "Output Tax Adjustment debited for Credit Note #{$creditNote->credit_note_number}",
                ];
            }

            $lines[] = [
                'account_id' => $arAccount->id,
                'debit' => 0,
                'credit' => $creditNote->total,
                'description' => "Accounts Receivable credited for Credit Note #{$creditNote->credit_note_number}",
            ];

            $this->financialService->postJournal(
                businessUnit: $businessUnit,
                description: "Customer Credit Note Issued #{$creditNote->credit_note_number}",
                postingDate: $creditNote->issue_date ?? now(),
                lines: $lines,
                reference: $creditNote,
                userId: $creditNote->created_by,
                type: JournalType::OPERATIONAL
            );
        });
    }
}

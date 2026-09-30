<?php

namespace App\Domains\Core\Listeners;

use App\Domains\Core\Enums\JournalType;
use App\Domains\Core\Events\InvoiceIssued;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\FinancialSettings;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Services\AccountResolver;
use App\Domains\Core\Services\FinancialService;

class PostInvoiceToGlListener
{
    public function __construct(
        private FinancialService $financialService,
        private AccountResolver $accountResolver
    ) {}

    public function handle(InvoiceIssued $event): void
    {
        $invoice = $event->invoice;

        ProcessedEvent::process(get_class($event), $event->eventId, function () use ($invoice) {
            $businessUnit = $invoice->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($invoice->business_unit_id);
            if (! $businessUnit) {
                return;
            }

            $hasSettings = FinancialSettings::withoutGlobalScopes()
                ->where('organization_id', $businessUnit->organization_id)
                ->exists();

            if (! $hasSettings) {
                return;
            }

            $arAccount = $this->accountResolver->resolve($businessUnit, 'default_accounts_receivable_account_id');
            $revenueAccount = $this->accountResolver->resolve($businessUnit, 'default_sales_revenue_account_id');

            $lines = [
                [
                    'account_id' => $arAccount->id,
                    'debit' => $invoice->total,
                    'credit' => 0,
                    'description' => "Accounts Receivable debited for Invoice #{$invoice->invoice_number}",
                ],
                [
                    'account_id' => $revenueAccount->id,
                    'debit' => 0,
                    'credit' => $invoice->subtotal,
                    'description' => "Sales Revenue credited for Invoice #{$invoice->invoice_number}",
                ],
            ];

            if (bccomp((string) $invoice->tax_amount, '0', 4) > 0) {
                $taxAccount = $this->accountResolver->resolve($businessUnit, 'default_tax_liability_account_id');
                $lines[] = [
                    'account_id' => $taxAccount->id,
                    'debit' => 0,
                    'credit' => $invoice->tax_amount,
                    'description' => "Output Tax Liability credited for Invoice #{$invoice->invoice_number}",
                ];
            }

            $this->financialService->postJournal(
                businessUnit: $businessUnit,
                description: "Customer Invoice Issued #{$invoice->invoice_number}",
                postingDate: $invoice->issue_date ?? now(),
                lines: $lines,
                reference: $invoice,
                userId: $invoice->created_by,
                type: JournalType::OPERATIONAL
            );
        });
    }
}

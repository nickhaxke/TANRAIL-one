<?php

namespace App\Domains\Core\Listeners;

use App\Domains\Core\Enums\JournalType;
use App\Domains\Core\Events\SupplierInvoiceApproved;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\FinancialSettings;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Services\AccountResolver;
use App\Domains\Core\Services\FinancialService;

class PostSupplierInvoiceToGlListener
{
    public function __construct(
        private FinancialService $financialService,
        private AccountResolver $accountResolver
    ) {}

    public function handle(SupplierInvoiceApproved $event): void
    {
        $supplierInvoice = $event->supplierInvoice;

        ProcessedEvent::process(get_class($event), $event->eventId, function () use ($supplierInvoice) {
            $businessUnit = $supplierInvoice->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($supplierInvoice->business_unit_id);
            if (! $businessUnit) {
                return;
            }

            $hasSettings = FinancialSettings::withoutGlobalScopes()
                ->where('organization_id', $businessUnit->organization_id)
                ->exists();

            if (! $hasSettings) {
                return;
            }

            $expenseAccount = $this->accountResolver->resolveExpenseAccount($businessUnit);
            $apAccount = $this->accountResolver->resolve($businessUnit, 'default_accounts_payable_account_id');

            $lines = [
                [
                    'account_id' => $expenseAccount->id,
                    'debit' => $supplierInvoice->subtotal,
                    'credit' => 0,
                    'description' => "Expense / Inventory debited for Vendor Bill #{$supplierInvoice->supplier_bill_number}",
                ],
            ];

            if (bccomp((string) $supplierInvoice->tax_amount, '0', 4) > 0) {
                $inputTaxAccount = $this->accountResolver->resolveInputTaxRecoverableAccount($businessUnit);
                $lines[] = [
                    'account_id' => $inputTaxAccount->id,
                    'debit' => $supplierInvoice->tax_amount,
                    'credit' => 0,
                    'description' => "Input Recoverable Tax debited for Vendor Bill #{$supplierInvoice->supplier_bill_number}",
                ];
            }

            $lines[] = [
                'account_id' => $apAccount->id,
                'debit' => 0,
                'credit' => $supplierInvoice->total,
                'description' => "Accounts Payable credited for Vendor Bill #{$supplierInvoice->supplier_bill_number}",
            ];

            $this->financialService->postJournal(
                businessUnit: $businessUnit,
                description: "Supplier Invoice Approved #{$supplierInvoice->supplier_bill_number}",
                postingDate: $supplierInvoice->bill_date ?? now(),
                lines: $lines,
                reference: $supplierInvoice,
                userId: $supplierInvoice->approved_by ?? $supplierInvoice->created_by,
                type: JournalType::OPERATIONAL
            );
        });
    }
}

<?php

namespace App\Domains\Core\Listeners;

use App\Domains\Core\Enums\JournalType;
use App\Domains\Core\Events\SupplierPaymentAllocated;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\FinancialSettings;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Models\SupplierInvoice;
use App\Domains\Core\Models\SupplierPayment;
use App\Domains\Core\Services\AccountResolver;
use App\Domains\Core\Services\FinancialService;

class PostSupplierPaymentToGlListener
{
    public function __construct(
        private FinancialService $financialService,
        private AccountResolver $accountResolver
    ) {}

    public function handle(SupplierPaymentAllocated $event): void
    {
        $allocation = $event->allocation;

        if ($allocation->allocatable_type !== SupplierInvoice::class && ! ($allocation->allocatable instanceof SupplierInvoice)) {
            return;
        }

        ProcessedEvent::process(get_class($event), $event->eventId, function () use ($allocation) {
            $businessUnit = $allocation->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($allocation->business_unit_id);
            if (! $businessUnit) {
                return;
            }

            $hasSettings = FinancialSettings::withoutGlobalScopes()
                ->where('organization_id', $businessUnit->organization_id)
                ->exists();

            if (! $hasSettings) {
                return;
            }

            $payment = $allocation->payment;
            if (! $payment instanceof SupplierPayment && $allocation->payment_type !== SupplierPayment::class) {
                return;
            }

            /** @var SupplierPayment $supplierPaymentModel */
            $supplierPaymentModel = $payment instanceof SupplierPayment ? $payment : SupplierPayment::withoutGlobalScopes()->find($allocation->payment_id);
            if (! $supplierPaymentModel) {
                return;
            }

            $cashAccount = $this->accountResolver->resolvePaymentAccount($businessUnit, $supplierPaymentModel->method);
            $apAccount = $this->accountResolver->resolve($businessUnit, 'default_accounts_payable_account_id');

            $methodName = $supplierPaymentModel->method instanceof \BackedEnum ? $supplierPaymentModel->method->value : (string) $supplierPaymentModel->method;

            $this->financialService->postJournal(
                businessUnit: $businessUnit,
                description: "Supplier Payment Allocated for Vendor Bill Allocation #{$allocation->id}",
                postingDate: $allocation->allocation_date ?? now(),
                lines: [
                    [
                        'account_id' => $apAccount->id,
                        'debit' => $allocation->amount,
                        'credit' => 0,
                        'description' => 'Accounts Payable debited via supplier payment allocation',
                    ],
                    [
                        'account_id' => $cashAccount->id,
                        'debit' => 0,
                        'credit' => $allocation->amount,
                        'description' => "Disbursed via {$methodName}",
                    ],
                ],
                reference: $allocation,
                userId: $allocation->created_by,
                type: JournalType::OPERATIONAL
            );
        });
    }
}

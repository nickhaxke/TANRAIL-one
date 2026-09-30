<?php

namespace App\Domains\Core\Listeners;

use App\Domains\Core\Enums\JournalType;
use App\Domains\Core\Events\PaymentAllocated;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\FinancialSettings;
use App\Domains\Core\Models\Invoice;
use App\Domains\Core\Models\Payment;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Services\AccountResolver;
use App\Domains\Core\Services\FinancialService;

class PostCustomerPaymentToGlListener
{
    public function __construct(
        private FinancialService $financialService,
        private AccountResolver $accountResolver
    ) {}

    public function handle(PaymentAllocated $event): void
    {
        $allocation = $event->allocation;

        if ($allocation->allocatable_type !== Invoice::class && ! ($allocation->allocatable instanceof Invoice)) {
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
            if (! $payment instanceof Payment && $allocation->payment_type !== Payment::class) {
                return;
            }

            /** @var Payment $paymentModel */
            $paymentModel = $payment instanceof Payment ? $payment : Payment::withoutGlobalScopes()->find($allocation->payment_id);
            if (! $paymentModel) {
                return;
            }

            $cashAccount = $this->accountResolver->resolvePaymentAccount($businessUnit, $paymentModel->method);
            $arAccount = $this->accountResolver->resolve($businessUnit, 'default_accounts_receivable_account_id');

            $methodName = $paymentModel->method instanceof \BackedEnum ? $paymentModel->method->value : (string) $paymentModel->method;

            $this->financialService->postJournal(
                businessUnit: $businessUnit,
                description: "Customer Payment Allocated for Invoice Allocation #{$allocation->id}",
                postingDate: $allocation->allocation_date ?? now(),
                lines: [
                    [
                        'account_id' => $cashAccount->id,
                        'debit' => $allocation->amount,
                        'credit' => 0,
                        'description' => "Payment received via {$methodName}",
                    ],
                    [
                        'account_id' => $arAccount->id,
                        'debit' => 0,
                        'credit' => $allocation->amount,
                        'description' => 'Accounts Receivable credited via payment allocation',
                    ],
                ],
                reference: $allocation,
                userId: $allocation->created_by,
                type: JournalType::OPERATIONAL
            );
        });
    }
}

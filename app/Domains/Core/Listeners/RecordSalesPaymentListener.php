<?php

namespace App\Domains\Core\Listeners;

use App\Domains\Core\Enums\JournalType;
use App\Domains\Core\Events\SalesPaymentReceived;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Services\AccountResolver;
use App\Domains\Core\Services\FinancialService;

class RecordSalesPaymentListener
{
    public function __construct(
        private FinancialService $financialService,
        private AccountResolver $accountResolver
    ) {}

    public function handle(SalesPaymentReceived $event): void
    {
        $payment = $event->payment;

        ProcessedEvent::process(get_class($event), (string) $payment->id, function () use ($payment) {
            $order = $payment->order;
            $businessUnit = $order->branch->businessUnit;

            // Resolve receipt asset account based on payment method and Accounts Receivable account
            $receiptAccount = $this->accountResolver->resolvePaymentAccount($businessUnit, $payment->method);
            $arAccount = $this->accountResolver->resolve($businessUnit, 'default_accounts_receivable_account_id');

            $methodName = $payment->method instanceof \BackedEnum ? $payment->method->value : (string) $payment->method;

            $this->financialService->postJournal(
                businessUnit: $businessUnit,
                description: "Payment received for Order #{$order->id}",
                postingDate: now(),
                lines: [
                    [
                        'account_id' => $receiptAccount->id,
                        'debit' => $payment->amount,
                        'credit' => 0,
                        'description' => 'Payment received via '.$methodName,
                    ],
                    [
                        'account_id' => $arAccount->id,
                        'debit' => 0,
                        'credit' => $payment->amount,
                        'description' => 'Accounts Receivable credited',
                    ],
                ],
                reference: $payment,
                userId: $payment->created_by,
                type: JournalType::OPERATIONAL
            );
        });
    }
}

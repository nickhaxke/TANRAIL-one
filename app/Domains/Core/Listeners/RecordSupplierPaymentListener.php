<?php

namespace App\Domains\Core\Listeners;

use App\Domains\Core\Enums\JournalType;
use App\Domains\Core\Events\SupplierPaymentRecorded;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Services\AccountResolver;
use App\Domains\Core\Services\FinancialService;

class RecordSupplierPaymentListener
{
    public function __construct(
        private FinancialService $financialService,
        private AccountResolver $accountResolver
    ) {}

    public function handle(SupplierPaymentRecorded $event): void
    {
        $payment = $event->payment;

        ProcessedEvent::process(get_class($event), (string) $payment->id, function () use ($payment) {
            $order = $payment->purchaseOrder;
            $businessUnit = $order->businessUnit;

            $disbursementAccount = $this->accountResolver->resolvePaymentAccount($businessUnit, $payment->method);
            $apAccount = $this->accountResolver->resolve($businessUnit, 'default_accounts_payable_account_id');

            $methodName = $payment->method instanceof \BackedEnum ? $payment->method->value : (string) $payment->method;

            $this->financialService->postJournal(
                businessUnit: $businessUnit,
                description: "Payment to Supplier for PO #{$order->id}",
                postingDate: now(),
                lines: [
                    [
                        'account_id' => $apAccount->id,
                        'debit' => $payment->amount,
                        'credit' => 0,
                        'description' => 'Accounts Payable debited',
                    ],
                    [
                        'account_id' => $disbursementAccount->id,
                        'debit' => 0,
                        'credit' => $payment->amount,
                        'description' => 'Disbursed via '.$methodName,
                    ],
                ],
                reference: $payment,
                userId: $payment->created_by,
                type: JournalType::OPERATIONAL
            );
        });
    }
}

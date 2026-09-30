<?php

namespace App\Domains\Core\Events;

use App\Domains\Core\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalesPaymentReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(public Payment $payment) {}
}

<?php

namespace App\Domains\Core\Events;

use App\Domains\Core\Models\SupplierPayment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupplierPaymentRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(public SupplierPayment $payment) {}
}

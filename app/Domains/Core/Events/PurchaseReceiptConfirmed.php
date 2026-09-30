<?php

namespace App\Domains\Core\Events;

use App\Domains\Core\Models\PurchaseReceipt;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseReceiptConfirmed
{
    use Dispatchable, SerializesModels;

    public function __construct(public PurchaseReceipt $receipt) {}
}

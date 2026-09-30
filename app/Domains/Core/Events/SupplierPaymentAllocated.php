<?php

namespace App\Domains\Core\Events;

use App\Domains\Core\Models\PaymentAllocation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SupplierPaymentAllocated
{
    use Dispatchable, SerializesModels;

    public string $eventId;

    public function __construct(
        public PaymentAllocation $allocation,
        ?string $eventId = null
    ) {
        $this->eventId = $eventId ?? (string) Str::uuid();
    }
}

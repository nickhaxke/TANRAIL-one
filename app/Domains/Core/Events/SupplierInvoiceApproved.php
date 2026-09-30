<?php

namespace App\Domains\Core\Events;

use App\Domains\Core\Models\SupplierInvoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SupplierInvoiceApproved
{
    use Dispatchable, SerializesModels;

    public string $eventId;

    public function __construct(
        public SupplierInvoice $supplierInvoice,
        ?string $eventId = null
    ) {
        $this->eventId = $eventId ?? (string) Str::uuid();
    }
}

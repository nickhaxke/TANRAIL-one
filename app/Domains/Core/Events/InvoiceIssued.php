<?php

namespace App\Domains\Core\Events;

use App\Domains\Core\Models\Invoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class InvoiceIssued
{
    use Dispatchable, SerializesModels;

    public string $eventId;

    public function __construct(
        public Invoice $invoice,
        ?string $eventId = null
    ) {
        $this->eventId = $eventId ?? (string) Str::uuid();
    }
}

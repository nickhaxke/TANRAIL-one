<?php

namespace App\Domains\Core\Events;

use App\Domains\Core\Models\CreditNote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreditNoteIssued
{
    use Dispatchable, SerializesModels;

    public string $eventId;

    public function __construct(
        public CreditNote $creditNote,
        ?string $eventId = null
    ) {
        $this->eventId = $eventId ?? (string) Str::uuid();
    }
}

<?php

namespace App\Domains\Modules\Production\Events;

use App\Domains\Modules\Production\Models\ProductionOrder;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProductionCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $eventId;

    public function __construct(
        public ProductionOrder $productionOrder
    ) {
        $this->eventId = (string) Str::uuid();
    }
}

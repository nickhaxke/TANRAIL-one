<?php

namespace App\Domains\Core\Listeners;

use App\Domains\Core\Events\PurchaseReceiptConfirmed;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Services\InventoryService;

class ReceiveInventoryListener
{
    public function __construct(private InventoryService $inventoryService) {}

    public function handle(PurchaseReceiptConfirmed $event): void
    {
        $receipt = $event->receipt;

        ProcessedEvent::process(get_class($event), (string) $receipt->id, function () use ($receipt) {
            foreach ($receipt->lines as $line) {
                $this->inventoryService->receive(
                    item: $line->item,
                    location: $receipt->inventoryLocation,
                    quantity: $line->quantity,
                    referenceType: 'purchase_receipt',
                    referenceId: $receipt->id,
                    userId: $receipt->created_by
                );
            }
        });
    }
}

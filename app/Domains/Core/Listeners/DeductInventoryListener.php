<?php

namespace App\Domains\Core\Listeners;

use App\Domains\Core\Events\OrderConfirmed;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Services\InventoryService;
use Exception;

class DeductInventoryListener
{
    private InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Handle the event.
     *
     * @throws Exception
     */
    public function handle(OrderConfirmed $event): void
    {
        $order = $event->order;

        ProcessedEvent::process(get_class($event), (string) $order->id, function () use ($order) {
            $location = $order->branch->defaultSalesLocation;

            if (! $location) {
                throw new Exception("Branch [{$order->branch->name}] does not have a default sales location configured.");
            }

            foreach ($order->lines as $line) {
                if ($line->item->track_inventory) {
                    $this->inventoryService->issue(
                        $line->item,
                        $location,
                        $line->quantity,
                        'order',
                        $order->id,
                        $order->created_by
                    );
                }
            }
        });
    }
}

<?php

namespace App\Domains\Modules\Production\Listeners;

use App\Domains\Core\Enums\JournalType;
use App\Domains\Core\Exceptions\FinancialConfigurationException;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\FinancialSettings;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\ProcessedEvent;
use App\Domains\Core\Services\AccountResolver;
use App\Domains\Core\Services\FinancialService;
use App\Domains\Modules\Production\Events\ProductionCompleted;

class PostProductionCompletedToGlListener
{
    public function __construct(
        private FinancialService $financialService,
        private AccountResolver $accountResolver
    ) {}

    public function handle(ProductionCompleted $event): void
    {
        $order = $event->productionOrder;

        ProcessedEvent::process(get_class($event), $event->eventId, function () use ($order) {
            $businessUnit = $order->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($order->business_unit_id);
            if (! $businessUnit) {
                return;
            }

            $hasSettings = FinancialSettings::withoutGlobalScopes()
                ->where('organization_id', $businessUnit->organization_id)
                ->exists();

            if (! $hasSettings) {
                return; // Silently abort if no settings at all (Phase 4 existing pattern)
            }

            // We must have the required accounts for Production GL
            $rawMaterialsAccount = $this->accountResolver->resolve($businessUnit, 'default_raw_materials_account_id');
            $finishedGoodsAccount = $this->accountResolver->resolve($businessUnit, 'default_finished_goods_account_id');

            // Calculate exact total cost based on the standard_cost of consumed ingredients
            $totalCost = '0.0000';

            // Re-fetch items with relationships if needed, or query directly
            foreach ($order->items as $snapshotItem) {
                $ingredientItem = Item::withoutGlobalScopes()->find($snapshotItem->ingredient_item_id);

                $costPerUnit = $ingredientItem->standard_cost ?? '0.0000';
                if (bccomp((string) $costPerUnit, '0', 4) <= 0) {
                    throw new FinancialConfigurationException("Ingredient '{$ingredientItem->name}' has no valid standard cost. Production GL posting requires standard cost to be greater than 0.");
                }

                $lineCost = bcmul((string) $costPerUnit, (string) $snapshotItem->required_quantity, 4);
                $totalCost = bcadd($totalCost, $lineCost, 4);
            }

            if (bccomp($totalCost, '0', 4) <= 0) {
                throw new FinancialConfigurationException('Total production cost calculated to 0. Cannot post empty journal.');
            }

            $lines = [
                [
                    'account_id' => $finishedGoodsAccount->id,
                    'debit' => $totalCost,
                    'credit' => 0,
                    'description' => "Finished Goods Inventory debited for Production Order #{$order->production_number}",
                ],
                [
                    'account_id' => $rawMaterialsAccount->id,
                    'debit' => 0,
                    'credit' => $totalCost,
                    'description' => "Raw Materials Inventory credited for Production Order #{$order->production_number}",
                ],
            ];

            $this->financialService->postJournal(
                businessUnit: $businessUnit,
                description: "Production Order Completed #{$order->production_number}",
                postingDate: $order->completed_at ?? now(),
                lines: $lines,
                reference: $order,
                userId: $order->completed_by,
                type: JournalType::OPERATIONAL
            );
        });
    }
}

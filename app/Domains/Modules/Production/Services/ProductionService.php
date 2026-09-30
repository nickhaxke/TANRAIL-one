<?php

namespace App\Domains\Modules\Production\Services;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Exceptions\InvalidStateTransitionException;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\StockBalance;
use App\Domains\Core\Models\User;
use App\Domains\Core\Services\InventoryService;
use App\Domains\Modules\Production\Enums\ProductionOrderStatus;
use App\Domains\Modules\Production\Events\ProductionCompleted;
use App\Domains\Modules\Production\Models\BillOfMaterials;
use App\Domains\Modules\Production\Models\ProductionOrder;
use App\Domains\Modules\Production\Models\ProductionOrderItem;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductionService
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    public function createProductionOrder(
        Branch $branch,
        BillOfMaterials $bom,
        InventoryLocation $sourceLocation,
        InventoryLocation $destinationLocation,
        string $productionNumber,
        float|string $targetQuantity,
        ?string $notes = null,
        ?User $user = null
    ): ProductionOrder {
        $bu = $branch->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($branch->business_unit_id);

        if ((int) $bom->organization_id !== (int) $bu->organization_id) {
            throw new CrossOrganizationException(
                "BOM Organization {$bom->organization_id} does not match Branch Organization {$bu->organization_id}."
            );
        }

        $sourceBranch = $sourceLocation->branch ?? Branch::withoutGlobalScopes()->find($sourceLocation->branch_id);
        $sourceBu = $sourceBranch->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($sourceBranch->business_unit_id);

        if ((int) $sourceBu->organization_id !== (int) $bu->organization_id) {
            throw new CrossOrganizationException("Source Location Organization {$sourceBu->organization_id} does not match Branch Organization {$bu->organization_id}.");
        }
        if ((int) $sourceBu->id !== (int) $bu->id) {
            throw new CrossOrganizationException("Source Location Business Unit {$sourceBu->id} does not match Branch Business Unit {$bu->id}.");
        }

        $destBranch = $destinationLocation->branch ?? Branch::withoutGlobalScopes()->find($destinationLocation->branch_id);
        $destBu = $destBranch->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($destBranch->business_unit_id);

        if ((int) $destBu->organization_id !== (int) $bu->organization_id) {
            throw new CrossOrganizationException("Destination Location Organization {$destBu->organization_id} does not match Branch Organization {$bu->organization_id}.");
        }
        if ((int) $destBu->id !== (int) $bu->id) {
            throw new CrossOrganizationException("Destination Location Business Unit {$destBu->id} does not match Branch Business Unit {$bu->id}.");
        }

        if (! $bom->is_active) {
            throw new InvalidArgumentException("Bill of Materials '{$bom->name}' (ID: {$bom->id}) is inactive.");
        }

        if (bccomp((string) $targetQuantity, '0', 4) <= 0) {
            throw new InvalidArgumentException('Target production quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($branch, $bu, $bom, $sourceLocation, $destinationLocation, $productionNumber, $targetQuantity, $notes, $user) {
            $order = new ProductionOrder;
            $order->forceFill([
                'organization_id' => $bu->organization_id,
                'business_unit_id' => $bu->id,
                'branch_id' => $branch->id,
                'bill_of_materials_id' => $bom->id,
                'finished_item_id' => $bom->finished_item_id,
                'production_number' => $productionNumber,
                'target_quantity' => $targetQuantity,
                'status' => ProductionOrderStatus::DRAFT,
                'source_location_id' => $sourceLocation->id,
                'destination_location_id' => $destinationLocation->id,
                'notes' => $notes,
                'created_by' => $user?->id,
            ]);
            $order->save();

            return $order;
        });
    }

    public function confirmProductionOrder(ProductionOrder $order, ?User $user = null): ProductionOrder
    {
        if ($order->status !== ProductionOrderStatus::DRAFT) {
            throw new InvalidStateTransitionException(
                "Cannot confirm production order {$order->production_number} from status '{$order->status->value}'."
            );
        }

        $bom = $order->billOfMaterials ?? BillOfMaterials::withoutGlobalScopes()->find($order->bill_of_materials_id);
        if (! $bom || ! $bom->is_active) {
            throw new InvalidArgumentException("Source Bill of Materials for order {$order->production_number} is inactive or missing.");
        }

        return DB::transaction(function () use ($order, $bom, $user) {
            $lockedOrder = ProductionOrder::withoutGlobalScopes()->where('id', $order->id)->lockForUpdate()->first();

            // BOM Snapshot Calculation:
            // required = (bomItem->quantity / bom->yield_quantity) * target_quantity
            foreach ($bom->items as $bomItem) {
                $unitQuantity = bcdiv((string) $bomItem->quantity, (string) $bom->yield_quantity, 4);
                $totalRequired = bcmul($unitQuantity, (string) $lockedOrder->target_quantity, 4);

                $orderItem = new ProductionOrderItem;
                $orderItem->forceFill([
                    'production_order_id' => $lockedOrder->id,
                    'ingredient_item_id' => $bomItem->ingredient_item_id,
                    'required_quantity' => $totalRequired,
                    'actual_quantity' => $totalRequired,
                    'unit_id' => $bomItem->unit_id,
                    'scrap_percentage' => $bomItem->scrap_percentage,
                ]);
                $orderItem->save();
            }

            $lockedOrder->forceFill([
                'status' => ProductionOrderStatus::CONFIRMED,
                'confirmed_by' => $user?->id,
                'confirmed_at' => now(),
            ])->save();

            return $lockedOrder;
        });
    }

    public function startProduction(ProductionOrder $order): ProductionOrder
    {
        if ($order->status !== ProductionOrderStatus::CONFIRMED) {
            throw new InvalidStateTransitionException(
                "Cannot start production order {$order->production_number} from status '{$order->status->value}'."
            );
        }

        $order->forceFill(['status' => ProductionOrderStatus::IN_PROGRESS])->save();

        return $order;
    }

    public function completeProduction(ProductionOrder $order, ?User $user = null): ProductionOrder
    {
        if (! in_array($order->status, [ProductionOrderStatus::CONFIRMED, ProductionOrderStatus::IN_PROGRESS])) {
            throw new InvalidStateTransitionException(
                "Cannot complete production order {$order->production_number} from status '{$order->status->value}'."
            );
        }

        return DB::transaction(function () use ($order, $user) {
            // 1. Lock Production Order
            $lockedOrder = ProductionOrder::withoutGlobalScopes()
                ->with('items')
                ->where('id', $order->id)
                ->lockForUpdate()
                ->first();

            if (in_array($lockedOrder->status->value, [ProductionOrderStatus::COMPLETED->value, ProductionOrderStatus::CANCELLED->value])) {
                throw new InvalidStateTransitionException(
                    "Production order {$lockedOrder->production_number} was completed or cancelled concurrently."
                );
            }

            $sourceLocation = $lockedOrder->sourceLocation ?? InventoryLocation::withoutGlobalScopes()->find($lockedOrder->source_location_id);
            $destinationLocation = $lockedOrder->destinationLocation ?? InventoryLocation::withoutGlobalScopes()->find($lockedOrder->destination_location_id);

            // 2. Validate Raw Material Availability with Pessimistic Locking
            foreach ($lockedOrder->items as $snapshotItem) {
                $balance = StockBalance::withoutGlobalScopes()
                    ->where('inventory_location_id', $sourceLocation->id)
                    ->where('item_id', $snapshotItem->ingredient_item_id)
                    ->lockForUpdate()
                    ->first();

                $availableQty = $balance ? (string) $balance->quantity : '0.0000';

                if (bccomp($availableQty, (string) $snapshotItem->required_quantity, 4) < 0) {
                    $ingredientName = Item::withoutGlobalScopes()->find($snapshotItem->ingredient_item_id)?->name ?? "ID {$snapshotItem->ingredient_item_id}";
                    throw new InvalidArgumentException(
                        "Insufficient stock for raw material '{$ingredientName}'. Required: {$snapshotItem->required_quantity}, Available: {$availableQty}."
                    );
                }
            }

            // 3. Deduct Raw Materials via InventoryService
            foreach ($lockedOrder->items as $snapshotItem) {
                $ingredientItem = Item::withoutGlobalScopes()->find($snapshotItem->ingredient_item_id);
                $this->inventoryService->issue(
                    item: $ingredientItem,
                    location: $sourceLocation,
                    quantity: (float) $snapshotItem->required_quantity,
                    referenceType: ProductionOrder::class,
                    referenceId: $lockedOrder->id,
                    userId: $user?->id
                );
            }

            // 4. Receive Finished Goods via InventoryService
            $finishedItem = Item::withoutGlobalScopes()->find($lockedOrder->finished_item_id);
            $this->inventoryService->receive(
                item: $finishedItem,
                location: $destinationLocation,
                quantity: (float) $lockedOrder->target_quantity,
                referenceType: ProductionOrder::class,
                referenceId: $lockedOrder->id,
                userId: $user?->id
            );

            // 5. Update Status & Completion Metadata
            $lockedOrder->forceFill([
                'status' => ProductionOrderStatus::COMPLETED,
                'actual_quantity' => $lockedOrder->target_quantity,
                'completed_by' => $user?->id,
                'completed_at' => now(),
            ])->save();

            event(new ProductionCompleted($lockedOrder));

            return $lockedOrder;
        });
    }

    public function cancelProductionOrder(ProductionOrder $order): ProductionOrder
    {
        if (in_array($order->status, [ProductionOrderStatus::COMPLETED, ProductionOrderStatus::CANCELLED])) {
            throw new InvalidStateTransitionException(
                "Cannot cancel production order {$order->production_number} in terminal status '{$order->status->value}'."
            );
        }

        $order->forceFill(['status' => ProductionOrderStatus::CANCELLED])->save();

        return $order;
    }
}

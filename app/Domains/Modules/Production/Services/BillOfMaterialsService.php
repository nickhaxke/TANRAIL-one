<?php

namespace App\Domains\Modules\Production\Services;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\Unit;
use App\Domains\Core\Models\User;
use App\Domains\Modules\Production\Models\BillOfMaterials;
use App\Domains\Modules\Production\Models\BillOfMaterialsItem;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BillOfMaterialsService
{
    public function createBOM(
        BusinessUnit $businessUnit,
        Item $finishedItem,
        string $name,
        string $code,
        float|string $yieldQuantity = 1.0,
        ?User $user = null
    ): BillOfMaterials {
        $finishedItemBu = $finishedItem->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($finishedItem->business_unit_id);
        if (! $finishedItemBu || (int) $finishedItemBu->organization_id !== (int) $businessUnit->organization_id) {
            $itemOrgId = $finishedItemBu ? $finishedItemBu->organization_id : 'Unknown';
            throw new CrossOrganizationException(
                "Finished item Organization {$itemOrgId} does not match Business Unit Organization {$businessUnit->organization_id}."
            );
        }
        if ((int) $finishedItemBu->id !== (int) $businessUnit->id) {
            throw new CrossOrganizationException(
                "Finished item Business Unit {$finishedItemBu->id} does not match Target Business Unit {$businessUnit->id}."
            );
        }

        if (bccomp((string) $yieldQuantity, '0', 4) <= 0) {
            throw new InvalidArgumentException('BOM yield quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($businessUnit, $finishedItem, $name, $code, $yieldQuantity, $user) {
            $bom = new BillOfMaterials;
            $bom->forceFill([
                'organization_id' => $businessUnit->organization_id,
                'business_unit_id' => $businessUnit->id,
                'finished_item_id' => $finishedItem->id,
                'name' => $name,
                'code' => $code,
                'yield_quantity' => $yieldQuantity,
                'is_active' => true,
                'created_by' => $user?->id,
            ]);
            $bom->save();

            return $bom;
        });
    }

    public function addItem(
        BillOfMaterials $bom,
        Item $ingredientItem,
        float|string $quantity,
        Unit $unit,
        float|string $scrapPercentage = 0.0
    ): BillOfMaterialsItem {
        $ingredientBu = $ingredientItem->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($ingredientItem->business_unit_id);
        if (! $ingredientBu || (int) $ingredientBu->organization_id !== (int) $bom->organization_id) {
            $itemOrgId = $ingredientBu ? $ingredientBu->organization_id : 'Unknown';
            throw new CrossOrganizationException(
                "Ingredient item Organization {$itemOrgId} does not match BOM Organization {$bom->organization_id}."
            );
        }
        if ((int) $ingredientBu->id !== (int) $bom->business_unit_id) {
            throw new CrossOrganizationException(
                "Ingredient item Business Unit {$ingredientBu->id} does not match BOM Business Unit {$bom->business_unit_id}."
            );
        }

        if (bccomp((string) $quantity, '0', 4) <= 0) {
            throw new InvalidArgumentException('Ingredient quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($bom, $ingredientItem, $quantity, $unit, $scrapPercentage) {
            $item = new BillOfMaterialsItem;
            $item->forceFill([
                'bill_of_materials_id' => $bom->id,
                'ingredient_item_id' => $ingredientItem->id,
                'quantity' => $quantity,
                'unit_id' => $unit->id,
                'scrap_percentage' => $scrapPercentage,
            ]);
            $item->save();

            return $item;
        });
    }

    public function removeItem(BillOfMaterials $bom, int $itemId): void
    {
        DB::transaction(function () use ($bom, $itemId) {
            $bom->items()->where('id', $itemId)->delete();
        });
    }

    public function toggleActive(BillOfMaterials $bom): BillOfMaterials
    {
        $bom->forceFill(['is_active' => ! $bom->is_active])->save();

        return $bom;
    }
}

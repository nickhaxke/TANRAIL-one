<?php

namespace App\Domains\Modules\Production\Models;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillOfMaterialsItem extends Model
{
    use HasFactory;

    protected $table = 'bill_of_materials_items';

    protected $fillable = [
        'bill_of_materials_id',
        'ingredient_item_id',
        'quantity',
        'unit_id',
        'scrap_percentage',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'scrap_percentage' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (BillOfMaterialsItem $item) {
            if ($item->bill_of_materials_id && $item->ingredient_item_id) {
                $bom = BillOfMaterials::withoutGlobalScopes()->find($item->bill_of_materials_id);
                $ingredient = Item::withoutGlobalScopes()->find($item->ingredient_item_id);
                $ingredientBu = $ingredient ? ($ingredient->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($ingredient->business_unit_id)) : null;

                if ($bom && $ingredientBu && (int) $ingredientBu->organization_id !== (int) $bom->organization_id) {
                    throw new CrossOrganizationException(
                        "Ingredient item ID {$item->ingredient_item_id} belongs to Organization {$ingredientBu->organization_id}, expected {$bom->organization_id}."
                    );
                }
            }
        });
    }

    public function billOfMaterials(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterials::class, 'bill_of_materials_id');
    }

    public function ingredientItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'ingredient_item_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}

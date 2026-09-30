<?php

namespace App\Domains\Modules\Production\Models;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\User;
use App\Domains\Core\Scopes\BusinessUnitScope;
use App\Domains\Core\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillOfMaterials extends Model
{
    use HasFactory;

    protected $table = 'bill_of_materials';

    protected $fillable = [
        'organization_id',
        'business_unit_id',
        'finished_item_id',
        'name',
        'code',
        'yield_quantity',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'yield_quantity' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
        static::addGlobalScope(new BusinessUnitScope);

        static::saving(function (BillOfMaterials $bom) {
            if ($bom->finished_item_id) {
                $item = Item::withoutGlobalScopes()->find($bom->finished_item_id);
                $itemBu = $item ? ($item->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($item->business_unit_id)) : null;
                if ($itemBu && (int) $itemBu->organization_id !== (int) $bom->organization_id) {
                    throw new CrossOrganizationException(
                        "Finished item ID {$bom->finished_item_id} belongs to Organization {$itemBu->organization_id}, expected {$bom->organization_id}."
                    );
                }
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function finishedItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'finished_item_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillOfMaterialsItem::class, 'bill_of_materials_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

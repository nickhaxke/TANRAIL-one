<?php

namespace App\Domains\Modules\Production\Models;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Exceptions\InvalidStateTransitionException;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\User;
use App\Domains\Core\Scopes\BusinessUnitScope;
use App\Domains\Core\Scopes\OrganizationScope;
use App\Domains\Modules\Production\Enums\ProductionOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $table = 'production_orders';

    protected $fillable = [
        'organization_id',
        'business_unit_id',
        'branch_id',
        'bill_of_materials_id',
        'finished_item_id',
        'production_number',
        'target_quantity',
        'actual_quantity',
        'status',
        'source_location_id',
        'destination_location_id',
        'notes',
        'created_by',
        'confirmed_by',
        'completed_by',
        'confirmed_at',
        'completed_at',
    ];

    protected $casts = [
        'target_quantity' => 'decimal:4',
        'actual_quantity' => 'decimal:4',
        'status' => ProductionOrderStatus::class,
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
        static::addGlobalScope(new BusinessUnitScope);

        static::saving(function (ProductionOrder $order) {
            if ($order->finished_item_id) {
                $item = Item::withoutGlobalScopes()->find($order->finished_item_id);
                $itemBu = $item ? ($item->businessUnit ?? BusinessUnit::withoutGlobalScopes()->find($item->business_unit_id)) : null;

                if ($itemBu && (int) $itemBu->organization_id !== (int) $order->organization_id) {
                    throw new CrossOrganizationException(
                        "Finished item ID {$order->finished_item_id} belongs to Organization {$itemBu->organization_id}, expected {$order->organization_id}."
                    );
                }
            }

            if ($order->bill_of_materials_id) {
                $bom = BillOfMaterials::withoutGlobalScopes()->find($order->bill_of_materials_id);
                if ($bom && (int) $bom->organization_id !== (int) $order->organization_id) {
                    throw new CrossOrganizationException(
                        "Bill of Materials ID {$order->bill_of_materials_id} belongs to Organization {$bom->organization_id}, expected {$order->organization_id}."
                    );
                }
            }
        });

        static::updating(function (ProductionOrder $order) {
            $originalStatus = $order->getOriginal('status');
            if ($originalStatus instanceof ProductionOrderStatus) {
                $originalStatus = $originalStatus->value;
            }

            if (in_array($originalStatus, [ProductionOrderStatus::COMPLETED->value, ProductionOrderStatus::CANCELLED->value])) {
                throw new InvalidStateTransitionException(
                    "Production order {$order->production_number} is in terminal status '{$originalStatus}' and cannot be modified."
                );
            }
        });

        static::deleting(function (ProductionOrder $order) {
            $status = $order->status instanceof ProductionOrderStatus ? $order->status->value : $order->status;
            if ($status === ProductionOrderStatus::COMPLETED->value) {
                throw new InvalidStateTransitionException(
                    "Completed production order {$order->production_number} cannot be deleted."
                );
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function billOfMaterials(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterials::class, 'bill_of_materials_id');
    }

    public function finishedItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'finished_item_id');
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'source_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'destination_location_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductionOrderItem::class, 'production_order_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}

<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\ItemType;
use App\Domains\Core\Scopes\BusinessUnitScope;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_unit_id', 'category_id', 'sku', 'name', 'description',
        'type', 'track_inventory', 'base_price', 'standard_cost', 'tax_category_id', 'unit_id', 'status',
        'can_be_sold', 'can_be_purchased',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessUnitScope);
    }

    protected function casts(): array
    {
        return [
            'type' => ItemType::class,
            'track_inventory' => 'boolean',
            'status' => 'boolean',
            'can_be_sold' => 'boolean',
            'can_be_purchased' => 'boolean',
            'base_price' => 'decimal:2',
            'standard_cost' => 'decimal:4',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockBalances()
    {
        return $this->hasMany(StockBalance::class);
    }

    protected static function newFactory()
    {
        return ItemFactory::new();
    }
}

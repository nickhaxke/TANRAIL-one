<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Scopes\InventoryScope;
use Database\Factories\StockBalanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBalance extends Model
{
    /** @use HasFactory<StockBalanceFactory> */
    use HasFactory;

    protected $fillable = [
        'inventory_location_id',
        'item_id',
        'quantity',
        'reserved_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'reserved_quantity' => 'decimal:4',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new InventoryScope);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'inventory_location_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function getAvailableQuantityAttribute(): float
    {
        return (float) ($this->quantity - $this->reserved_quantity);
    }

    protected static function newFactory()
    {
        return StockBalanceFactory::new();
    }
}

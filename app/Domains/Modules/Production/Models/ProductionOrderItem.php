<?php

namespace App\Domains\Modules\Production\Models;

use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionOrderItem extends Model
{
    use HasFactory;

    protected $table = 'production_order_items';

    protected $fillable = [
        'production_order_id',
        'ingredient_item_id',
        'required_quantity',
        'actual_quantity',
        'unit_id',
        'scrap_percentage',
    ];

    protected $casts = [
        'required_quantity' => 'decimal:4',
        'actual_quantity' => 'decimal:4',
        'scrap_percentage' => 'decimal:2',
    ];

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
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

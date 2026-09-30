<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\OrderStatus;
use Database\Factories\OrderLineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class OrderLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'unit_price',
        'tax_amount',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'subtotal' => 'decimal:4',
        'total' => 'decimal:4',
    ];

    protected static function booted()
    {
        static::updating(function (OrderLine $model) {
            if ($model->order && $model->order->status !== OrderStatus::DRAFT) {
                throw new RuntimeException('OrderLine records are immutable once the order is no longer in DRAFT state.');
            }
        });

        static::deleting(function (OrderLine $model) {
            if ($model->order && $model->order->status !== OrderStatus::DRAFT) {
                throw new RuntimeException('OrderLine records cannot be deleted once the order is no longer in DRAFT state.');
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    protected static function newFactory()
    {
        return OrderLineFactory::new();
    }
}

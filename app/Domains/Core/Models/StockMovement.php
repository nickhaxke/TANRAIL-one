<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\MovementType;
use App\Domains\Core\Scopes\InventoryScope;
use Database\Factories\StockMovementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    /** @use HasFactory<StockMovementFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'type',
        'source_location_id',
        'destination_location_id',
        'quantity',
        'reference_type',
        'reference_id',
        'user_id',
        'reason',
    ];

    protected $casts = [
        'type' => MovementType::class,
        'quantity' => 'decimal:4',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new InventoryScope);

        static::updating(function ($model) {
            throw new \RuntimeException('StockMovement records are immutable and cannot be updated.');
        });

        static::deleting(function ($model) {
            throw new \RuntimeException('StockMovement records are immutable and cannot be deleted.');
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'source_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'destination_location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory()
    {
        return StockMovementFactory::new();
    }
}

<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\TransferStatus;
use App\Domains\Core\Scopes\InventoryScope;
use Database\Factories\StockTransferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransfer extends Model
{
    /** @use HasFactory<StockTransferFactory> */
    use HasFactory;

    protected $fillable = [
        'source_location_id',
        'destination_location_id',
        'item_id',
        'requested_qty',
        'approved_qty',
        'status',
    ];

    protected $casts = [
        'status' => TransferStatus::class,
        'requested_qty' => 'decimal:4',
        'approved_qty' => 'decimal:4',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new InventoryScope);
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'source_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'destination_location_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    protected static function newFactory()
    {
        return StockTransferFactory::new();
    }
}

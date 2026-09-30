<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

class PurchaseOrderLine extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'quantity',
        'unit_price',
        'tax_amount',
        'subtotal',
        'total',
        'received_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'subtotal' => 'decimal:4',
        'total' => 'decimal:4',
        'received_quantity' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::updating(function (PurchaseOrderLine $line) {
            // Can only update if DRAFT or SUBMITTED, OR if we are updating received_quantity (done by Service)
            // Wait, we need to allow `received_quantity` to update during PARTIAL_RECEIVED.
            // Let's only block commercial fields if not DRAFT/SUBMITTED.
            if ($line->isDirty(['item_id', 'quantity', 'unit_price', 'tax_amount', 'subtotal', 'total'])) {
                if (! in_array($line->purchaseOrder->status, [PurchaseOrderStatus::DRAFT, PurchaseOrderStatus::SUBMITTED])) {
                    throw new RuntimeException('Cannot modify commercial fields of a Purchase Order Line after approval.');
                }
            }
        });

        static::deleting(function (PurchaseOrderLine $line) {
            if (! in_array($line->purchaseOrder->status, [PurchaseOrderStatus::DRAFT, PurchaseOrderStatus::SUBMITTED])) {
                throw new RuntimeException('Cannot delete a Purchase Order Line after approval.');
            }
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function receiptLines(): HasMany
    {
        return $this->hasMany(PurchaseReceiptLine::class);
    }
}

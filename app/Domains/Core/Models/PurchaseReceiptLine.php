<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class PurchaseReceiptLine extends Model
{
    protected $fillable = [
        'purchase_receipt_id',
        'purchase_order_line_id',
        'item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::updating(function (PurchaseReceiptLine $line) {
            throw new RuntimeException('Purchase Receipt Lines are append-only and cannot be modified.');
        });

        static::deleting(function (PurchaseReceiptLine $line) {
            throw new RuntimeException('Purchase Receipt Lines are append-only and cannot be deleted.');
        });
    }

    public function purchaseReceipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class);
    }

    public function purchaseOrderLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderLine::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

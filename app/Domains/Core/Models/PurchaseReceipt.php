<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

class PurchaseReceipt extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'inventory_location_id',
        'reference_number',
        'received_at',
        'created_by',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // A PurchaseReceipt belongs to a PO, which belongs to a Branch/BU.
        // We can apply a scope indirectly by joining or just ensure the service secures it.
        // For simplicity and performance, we won't add a global scope here directly,
        // because it doesn't have business_unit_id column directly.
        // The policy will protect it.

        static::updating(function (PurchaseReceipt $receipt) {
            throw new RuntimeException('Purchase Receipts are append-only and cannot be modified.');
        });

        static::deleting(function (PurchaseReceipt $receipt) {
            throw new RuntimeException('Purchase Receipts are append-only and cannot be deleted.');
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function inventoryLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseReceiptLine::class);
    }
}

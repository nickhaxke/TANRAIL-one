<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use RuntimeException;

class SupplierPayment extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'supplier_invoice_id',
        'amount',
        'unallocated_amount',
        'method',
        'reference',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'unallocated_amount' => 'decimal:4',
        'method' => PaymentMethod::class,
    ];

    protected static function booted(): void
    {
        static::updating(function (SupplierPayment $payment) {
            if ($payment->isDirty(['purchase_order_id', 'supplier_invoice_id', 'amount', 'method', 'reference'])) {
                throw new RuntimeException('Supplier Payments are append-only and cannot be modified.');
            }
        });

        static::deleting(function (SupplierPayment $payment) {
            throw new RuntimeException('Supplier Payments are append-only and cannot be deleted.');
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function allocations(): MorphMany
    {
        return $this->morphMany(PaymentAllocation::class, 'payment');
    }
}

<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\PaymentMethod;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use RuntimeException;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_id',
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

    protected static function booted()
    {
        static::updating(function (Payment $model) {
            // Allow update of unallocated_amount only during allocation if needed, or enforce append-only
            if ($model->isDirty(['order_id', 'invoice_id', 'amount', 'method', 'reference'])) {
                throw new RuntimeException('Payment records are append-only and cannot be updated.');
            }
        });

        static::deleting(function (Payment $model) {
            throw new RuntimeException('Payment records are append-only and cannot be deleted.');
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function allocations(): MorphMany
    {
        return $this->morphMany(PaymentAllocation::class, 'payment');
    }

    protected static function newFactory()
    {
        return PaymentFactory::new();
    }
}

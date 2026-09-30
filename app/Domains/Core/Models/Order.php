<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\OrderStatus;
use App\Domains\Core\Scopes\BusinessUnitScope;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'business_unit_id',
        'customer_id',
        'created_by',
        'shift_id',
        'subtotal',
        'tax_total',
        'total',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'subtotal' => 'decimal:4',
        'tax_total' => 'decimal:4',
        'total' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessUnitScope);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    protected static function newFactory()
    {
        return OrderFactory::new();
    }
}

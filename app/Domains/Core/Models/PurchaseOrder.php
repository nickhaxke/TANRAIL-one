<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\PurchaseOrderStatus;
use App\Domains\Core\Scopes\BusinessUnitScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'business_unit_id',
        'supplier_id',
        'reference_number',
        'created_by',
    ];

    protected $casts = [
        'status' => PurchaseOrderStatus::class,
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }
}

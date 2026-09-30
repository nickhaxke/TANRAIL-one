<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Scopes\BusinessUnitScope;
use App\Domains\Core\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'business_unit_id',
        'payment_type',
        'payment_id',
        'allocatable_type',
        'allocatable_id',
        'amount',
        'allocation_date',
        'created_by',
    ];

    protected $casts = [
        'allocation_date' => 'date',
        'amount' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
        static::addGlobalScope(new BusinessUnitScope);

        static::saving(function (PaymentAllocation $allocation) {
            if ($allocation->allocatable_type && $allocation->allocatable_id) {
                $targetClass = $allocation->allocatable_type;
                if (class_exists($targetClass)) {
                    $target = $targetClass::withoutGlobalScopes()->find($allocation->allocatable_id);
                    if ($target) {
                        if (isset($target->organization_id) && (int) $target->organization_id !== (int) $allocation->organization_id) {
                            throw new CrossOrganizationException("Allocatable target does not belong to Organization {$allocation->organization_id}.");
                        }
                        if (isset($target->business_unit_id) && (int) $target->business_unit_id !== (int) $allocation->business_unit_id) {
                            throw new CrossOrganizationException("Allocatable target does not belong to Business Unit {$allocation->business_unit_id}.");
                        }
                    }
                }
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payment(): MorphTo
    {
        return $this->morphTo();
    }

    public function allocatable(): MorphTo
    {
        return $this->morphTo();
    }
}

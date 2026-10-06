<?php

namespace App\Domains\Modules\Cleaning\Models;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\User;
use App\Domains\Core\Scopes\BusinessUnitScope;
use Database\Factories\Domains\Modules\Cleaning\Models\CleaningSupervisorAssignmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleaningSupervisorAssignment extends Model
{
    /** @use HasFactory<CleaningSupervisorAssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'business_unit_id',
        'supervisor_id',
        'branch_id',
        'cleaning_service_type_id',
        'start_date',
        'end_date',
        'assigned_by_id',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessUnitScope);
    }

    public function scopeActiveOnDate($query, $date)
    {
        return $query->where('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $date);
            });
    }

    public function scopeActive($query)
    {
        return $this->scopeActiveOnDate($query, today());
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(CleaningServiceType::class, 'cleaning_service_type_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }
}

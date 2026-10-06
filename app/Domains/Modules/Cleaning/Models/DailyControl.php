<?php

namespace App\Domains\Modules\Cleaning\Models;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\User;
use App\Domains\Core\Scopes\BusinessUnitScope;
use Database\Factories\Domains\Modules\Cleaning\Models\DailyControlFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyControl extends Model
{
    /** @use HasFactory<DailyControlFactory> */
    use HasFactory;

    protected $fillable = [
        'business_unit_id',
        'branch_id',
        'supervisor_id',
        'date',
        'workforce_check_status',
        'zero_worker_reason',
        'status', // Added status for Draft -> Submitted workflow
        'shift',
        'submitted_at',
        'submitted_by_id',
        'reviewed_at',
        'reviewed_by_id',
        'review_notes',
        'supervisor_remarks',
        'summary_snapshot',
    ];

    protected $casts = [
        'date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'summary_snapshot' => 'array',
    ];

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function workerAttendances(): HasMany
    {
        return $this->hasMany(WorkerAttendance::class);
    }

    public function workActivities(): HasMany
    {
        return $this->hasMany(WorkActivity::class);
    }

    public function operationalIssues(): HasMany
    {
        return $this->hasMany(OperationalIssue::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessUnitScope);
    }
}

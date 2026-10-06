<?php

namespace App\Domains\Modules\Cleaning\Models;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Scopes\BusinessUnitScope;
use Database\Factories\Domains\Modules\Cleaning\Models\WorkerAttendanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkerAttendance extends Model
{
    /** @use HasFactory<WorkerAttendanceFactory> */
    use HasFactory;

    protected $fillable = [
        'business_unit_id',
        'daily_control_id',
        'cleaning_worker_id',
        'arrival_time',
        'is_present',
        'status',
        'check_in_at',
        'check_out_at',
        'absence_reason',
        'notes',
        'checked_in_by_id',
        'checked_out_by_id',
    ];

    protected $casts = [
        'is_present' => 'boolean',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessUnitScope);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function dailyControl(): BelongsTo
    {
        return $this->belongsTo(DailyControl::class);
    }

    public function cleaningWorker(): BelongsTo
    {
        return $this->belongsTo(CleaningWorker::class);
    }

    public function workActivities(): BelongsToMany
    {
        return $this->belongsToMany(
            WorkActivity::class,
            'work_activity_worker',
            'worker_attendance_id',
            'work_activity_id'
        )->withTimestamps();
    }
}

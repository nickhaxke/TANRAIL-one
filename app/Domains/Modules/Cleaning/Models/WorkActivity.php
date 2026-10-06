<?php

namespace App\Domains\Modules\Cleaning\Models;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Location;
use App\Domains\Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkActivity extends Model
{
    protected $fillable = [
        'business_unit_id',
        'daily_control_id',
        'location_id',
        'activity_name',
        'status',
        'verification_status',
        'notes',
        'started_at',
        'completed_at',
        'cleaning_service_type_id',
        'cleaning_service_template_id',
        'reference',
        'created_by_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function dailyControl(): BelongsTo
    {
        return $this->belongsTo(DailyControl::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function workerAttendances(): BelongsToMany
    {
        return $this->belongsToMany(
            WorkerAttendance::class,
            'work_activity_worker',
            'work_activity_id',
            'worker_attendance_id'
        )->withTimestamps();
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(CleaningServiceType::class, 'cleaning_service_type_id');
    }

    public function serviceTemplate(): BelongsTo
    {
        return $this->belongsTo(CleaningServiceTemplate::class, 'cleaning_service_template_id');
    }

    public function items()
    {
        return $this->hasMany(WorkActivityItem::class, 'work_activity_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}

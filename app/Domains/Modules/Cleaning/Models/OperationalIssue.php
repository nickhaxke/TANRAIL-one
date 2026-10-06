<?php

namespace App\Domains\Modules\Cleaning\Models;

use App\Domains\Core\Models\BusinessUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalIssue extends Model
{
    protected $fillable = [
        'business_unit_id',
        'daily_control_id',
        'issue_type',
        'description',
        'status',
        'resolution_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function dailyControl(): BelongsTo
    {
        return $this->belongsTo(DailyControl::class);
    }
}

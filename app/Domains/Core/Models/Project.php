<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Scopes\BusinessUnitScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_unit_id',
        'name',
        'code',
        'description',
        'status', // 'planned', 'active', 'completed', 'cancelled'
        'start_date',
        'end_date',
        'budget',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessUnitScope);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }
}

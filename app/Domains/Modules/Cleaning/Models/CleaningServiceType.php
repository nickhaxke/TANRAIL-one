<?php

namespace App\Domains\Modules\Cleaning\Models;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Scopes\BusinessUnitScope;
use Database\Factories\Domains\Modules\Cleaning\Models\CleaningServiceTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CleaningServiceType extends Model
{
    /** @use HasFactory<CleaningServiceTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'business_unit_id',
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessUnitScope);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CleaningSupervisorAssignment::class, 'cleaning_service_type_id');
    }

    public function templates(): HasMany
    {
        return $this->hasMany(CleaningServiceTemplate::class, 'cleaning_service_type_id');
    }
}

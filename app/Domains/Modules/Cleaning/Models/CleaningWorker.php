<?php

namespace App\Domains\Modules\Cleaning\Models;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\User;
use App\Domains\Core\Scopes\BusinessUnitScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleaningWorker extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_unit_id',
        'current_branch_id',
        'current_supervisor_id',
        'worker_id',
        'first_name',
        'last_name',
        'phone_number',
        'id_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new BusinessUnitScope);
    }

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function currentBranch()
    {
        return $this->belongsTo(Branch::class, 'current_branch_id');
    }

    public function currentSupervisor()
    {
        return $this->belongsTo(User::class, 'current_supervisor_id');
    }

    public function assignments()
    {
        return $this->hasMany(CleaningWorkerAssignment::class)->orderByDesc('start_date');
    }

    public function assignTo(
        Branch $branch,
        ?User $supervisor = null,
        ?User $assignedBy = null,
        ?string $notes = null
    ): CleaningWorkerAssignment {
        // End existing active assignment if any
        $this->assignments()
            ->whereNull('end_date')
            ->update(['end_date' => now()->toDateString()]);

        // Create new assignment
        $assignment = $this->assignments()->create([
            'business_unit_id' => $this->business_unit_id,
            'branch_id' => $branch->id,
            'supervisor_id' => $supervisor?->id,
            'assigned_by_id' => $assignedBy?->id,
            'start_date' => now()->toDateString(),
            'notes' => $notes,
        ]);

        $this->update([
            'current_branch_id' => $branch->id,
            'current_supervisor_id' => $supervisor?->id,
        ]);

        return $assignment;
    }
}

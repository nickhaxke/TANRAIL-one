<?php

namespace App\Policies\Cleaning;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\User;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Modules\Cleaning\Models\WorkActivity;

class WorkActivityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkActivity $workActivity): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkActivity $workActivity): bool
    {
        $contextManager = app(ContextManager::class);
        $buId = $contextManager->getActiveBusinessUnitId();

        if (! $buId || $workActivity->business_unit_id != $buId) {
            return false;
        }

        $branchId = $workActivity->dailyControl->branch_id;
        $supervisorId = $workActivity->dailyControl->supervisor_id;

        $isCoordinator = $user->hasAccessToScope(BusinessUnit::class, $buId);
        $isOwner = $user->hasAccessToScope(Branch::class, $branchId) && $supervisorId === $user->id;

        return $isCoordinator || $isOwner;
    }
}

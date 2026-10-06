<?php

namespace App\Policies\Cleaning;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\User;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Modules\Cleaning\Models\DailyControl;

class DailyControlPolicy
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
    public function view(User $user, DailyControl $dailyControl): bool
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
    public function update(User $user, DailyControl $dailyControl): bool
    {
        $contextManager = app(ContextManager::class);
        $buId = $contextManager->getActiveBusinessUnitId();

        if (! $buId || $dailyControl->business_unit_id != $buId) {
            return false;
        }

        $isCoordinator = $user->hasAccessToScope(BusinessUnit::class, $buId);
        $isOwner = $user->hasAccessToScope(Branch::class, $dailyControl->branch_id) && $dailyControl->supervisor_id === $user->id;

        return $isCoordinator || $isOwner;
    }
}

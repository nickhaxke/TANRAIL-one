<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\PaymentAllocation;
use App\Domains\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentAllocationPolicy
{
    use HandlesAuthorization;

    protected function authorizeTenant(User $user, int $organizationId, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasAccessToScope(BusinessUnit::class, $businessUnitId);
        }

        return $user->hasAccessToScope(Organization::class, $organizationId);
    }

    public function viewAny(User $user): bool
    {
        return $user->roles()->exists();
    }

    public function view(User $user, PaymentAllocation $allocation): bool
    {
        return $this->authorizeTenant($user, (int) $allocation->organization_id, (int) $allocation->business_unit_id);
    }

    public function create(User $user, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasAccessToScope(BusinessUnit::class, $businessUnitId);
        }

        return $user->roles()->exists();
    }

    public function allocate(User $user, PaymentAllocation $allocation): bool
    {
        return $this->authorizeTenant($user, (int) $allocation->organization_id, (int) $allocation->business_unit_id);
    }

    public function delete(User $user, PaymentAllocation $allocation): bool
    {
        return false; // Physical deletion of commercial records is prohibited
    }
}

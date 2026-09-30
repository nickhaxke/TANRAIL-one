<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\CreditNote;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreditNotePolicy
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

    public function view(User $user, CreditNote $creditNote): bool
    {
        return $this->authorizeTenant($user, (int) $creditNote->organization_id, (int) $creditNote->business_unit_id);
    }

    public function create(User $user, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasAccessToScope(BusinessUnit::class, $businessUnitId);
        }

        return $user->roles()->exists();
    }

    public function update(User $user, CreditNote $creditNote): bool
    {
        return $this->authorizeTenant($user, (int) $creditNote->organization_id, (int) $creditNote->business_unit_id);
    }

    public function issue(User $user, CreditNote $creditNote): bool
    {
        return $this->authorizeTenant($user, (int) $creditNote->organization_id, (int) $creditNote->business_unit_id);
    }

    public function apply(User $user, CreditNote $creditNote): bool
    {
        return $this->authorizeTenant($user, (int) $creditNote->organization_id, (int) $creditNote->business_unit_id);
    }

    public function cancel(User $user, CreditNote $creditNote): bool
    {
        return $this->authorizeTenant($user, (int) $creditNote->organization_id, (int) $creditNote->business_unit_id);
    }

    public function delete(User $user, CreditNote $creditNote): bool
    {
        return false; // Physical deletion of commercial records is prohibited
    }
}

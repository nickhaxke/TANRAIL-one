<?php

namespace App\Domains\Modules\Production\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\User;
use App\Domains\Modules\Production\Models\BillOfMaterials;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillOfMaterialsPolicy
{
    use HandlesAuthorization;

    protected function authorizeTenant(User $user, int $organizationId, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasAccessToScope(BusinessUnit::class, $businessUnitId);
        }

        return $user->hasAccessToScope(Organization::class, $organizationId);
    }

    protected function authorizeAction(User $user, string $permission, int $organizationId, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasPermissionTo($permission, BusinessUnit::class, $businessUnitId);
        }

        return $user->hasPermissionTo($permission, Organization::class, $organizationId);
    }

    public function viewAny(User $user): bool
    {
        return $user->roles()->exists();
    }

    public function view(User $user, BillOfMaterials $bom): bool
    {
        return $this->authorizeTenant($user, (int) $bom->organization_id, (int) $bom->business_unit_id) ||
               $this->authorizeAction($user, 'manage-production', (int) $bom->organization_id, (int) $bom->business_unit_id);
    }

    public function create(User $user, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasPermissionTo('manage-production', BusinessUnit::class, $businessUnitId);
        }

        return false;
    }

    public function update(User $user, BillOfMaterials $bom): bool
    {
        return $this->authorizeAction($user, 'manage-production', (int) $bom->organization_id, (int) $bom->business_unit_id);
    }

    public function delete(User $user, BillOfMaterials $bom): bool
    {
        return $this->authorizeAction($user, 'manage-production', (int) $bom->organization_id, (int) $bom->business_unit_id);
    }
}

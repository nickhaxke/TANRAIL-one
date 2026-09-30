<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\SupplierInvoice;
use App\Domains\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierInvoicePolicy
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

    public function view(User $user, SupplierInvoice $invoice): bool
    {
        return $this->authorizeTenant($user, (int) $invoice->organization_id, (int) $invoice->business_unit_id);
    }

    public function create(User $user, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasAccessToScope(BusinessUnit::class, $businessUnitId);
        }

        return $user->roles()->exists();
    }

    public function update(User $user, SupplierInvoice $invoice): bool
    {
        return $this->authorizeTenant($user, (int) $invoice->organization_id, (int) $invoice->business_unit_id);
    }

    public function approve(User $user, SupplierInvoice $invoice): bool
    {
        return $this->authorizeTenant($user, (int) $invoice->organization_id, (int) $invoice->business_unit_id);
    }

    public function cancel(User $user, SupplierInvoice $invoice): bool
    {
        return $this->authorizeTenant($user, (int) $invoice->organization_id, (int) $invoice->business_unit_id);
    }

    public function delete(User $user, SupplierInvoice $invoice): bool
    {
        return false; // Physical deletion of commercial records is prohibited
    }
}

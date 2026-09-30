<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\Supplier;
use App\Domains\Core\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user)
    {
        return true; // Controlled by Global Scope
    }

    public function view(User $user, Supplier $supplier)
    {
        return $user->hasAccessToScope(Organization::class, $supplier->organization_id);
    }

    public function create(User $user, int $organizationId)
    {
        return $user->hasAccessToScope(Organization::class, $organizationId);
    }

    public function update(User $user, Supplier $supplier)
    {
        return $user->hasAccessToScope(Organization::class, $supplier->organization_id);
    }

    public function delete(User $user, Supplier $supplier)
    {
        return $user->hasAccessToScope(Organization::class, $supplier->organization_id);
    }
}

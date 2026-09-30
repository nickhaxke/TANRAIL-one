<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\Customer;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user)
    {
        return true; // Controlled by Global Scope
    }

    public function view(User $user, Customer $customer)
    {
        return $user->hasAccessToScope(Organization::class, $customer->organization_id);
    }

    public function create(User $user, int $organizationId)
    {
        return $user->hasAccessToScope(Organization::class, $organizationId);
    }

    public function update(User $user, Customer $customer)
    {
        return $user->hasAccessToScope(Organization::class, $customer->organization_id);
    }

    public function delete(User $user, Customer $customer)
    {
        return $user->hasAccessToScope(Organization::class, $customer->organization_id);
    }
}

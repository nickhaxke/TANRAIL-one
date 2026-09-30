<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\User;

class InventoryLocationPolicy
{
    public function viewAny(User $user)
    {
        return true; // Filtered by global scope
    }

    public function view(User $user, InventoryLocation $location)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $location->branch->business_unit_id);
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, InventoryLocation $location)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $location->branch->business_unit_id);
    }

    public function delete(User $user, InventoryLocation $location)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $location->branch->business_unit_id);
    }
}

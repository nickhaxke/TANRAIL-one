<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\User;

class ItemPolicy
{
    public function viewAny(User $user)
    {
        return true; // Controlled by Global Scope
    }

    public function view(User $user, Item $item)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $item->business_unit_id);
    }

    public function create(User $user, int $businessUnitId)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $businessUnitId);
    }

    public function update(User $user, Item $item)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $item->business_unit_id);
    }

    public function delete(User $user, Item $item)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $item->business_unit_id);
    }
}

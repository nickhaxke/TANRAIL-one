<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\StockMovement;
use App\Domains\Core\Models\User;

class StockMovementPolicy
{
    public function viewAny(User $user)
    {
        return true; // Scope handles this
    }

    public function view(User $user, StockMovement $movement)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $movement->item->business_unit_id);
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, StockMovement $movement)
    {
        // Movements are absolutely immutable
        return false;
    }

    public function delete(User $user, StockMovement $movement)
    {
        // Movements are absolutely immutable
        return false;
    }
}

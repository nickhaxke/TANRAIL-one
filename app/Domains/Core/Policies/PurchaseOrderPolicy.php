<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAccessToScope(BusinessUnit::class);
    }

    public function view(User $user, PurchaseOrder $order)
    {
        return clone $this->viewAny($user);
    }

    public function create(User $user)
    {
        return clone $this->viewAny($user);
    }

    public function update(User $user, PurchaseOrder $order)
    {
        return clone $this->viewAny($user);
    }

    public function delete(User $user, PurchaseOrder $order)
    {
        return false; // Physical deletion is prohibited
    }
}

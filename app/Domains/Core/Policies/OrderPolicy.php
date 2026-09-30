<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Order;
use App\Domains\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAccessToScope(BusinessUnit::class);
    }

    public function view(User $user, Order $order)
    {
        return clone $this->viewAny($user);
    }

    public function create(User $user)
    {
        return clone $this->viewAny($user);
    }

    public function update(User $user, Order $order)
    {
        return clone $this->viewAny($user);
    }

    public function delete(User $user, Order $order)
    {
        // Physical deletion is explicitly prohibited by architecture.
        return false;
    }
}

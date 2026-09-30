<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\Account;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAccessToScope(Organization::class);
    }

    public function view(User $user, Account $account)
    {
        return clone $this->viewAny($user);
    }

    public function create(User $user)
    {
        return clone $this->viewAny($user);
    }

    public function update(User $user, Account $account)
    {
        return clone $this->viewAny($user);
    }

    public function delete(User $user, Account $account)
    {
        return clone $this->viewAny($user);
    }
}

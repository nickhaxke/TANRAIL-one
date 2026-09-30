<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Journal;
use App\Domains\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JournalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAccessToScope(BusinessUnit::class);
    }

    public function view(User $user, Journal $journal)
    {
        return clone $this->viewAny($user);
    }

    public function create(User $user)
    {
        return clone $this->viewAny($user);
    }

    public function update(User $user, Journal $journal)
    {
        return false; // Immutable, reversal goes through Service
    }

    public function delete(User $user, Journal $journal)
    {
        return false; // Physical deletion is prohibited
    }
}

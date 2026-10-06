<?php

namespace App\Policies\Cleaning;

use App\Domains\Core\Models\User;
use App\Domains\Modules\Cleaning\Models\CleaningSupervisorAssignment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CleaningSupervisorAssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('cleaning.operations.monitor');
    }

    public function create(User $user)
    {
        return $user->can('cleaning.operations.monitor');
    }

    public function update(User $user, CleaningSupervisorAssignment $assignment)
    {
        // Supervisor cannot modify their own assignment
        if ($user->id === $assignment->supervisor_id) {
            return false;
        }

        return $user->can('cleaning.operations.monitor');
    }

    public function delete(User $user, CleaningSupervisorAssignment $assignment)
    {
        if ($user->id === $assignment->supervisor_id) {
            return false;
        }

        return $user->can('cleaning.operations.monitor');
    }
}

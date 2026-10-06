<?php

namespace App\Policies\Cleaning;

use App\Domains\Core\Models\User;
use App\Domains\Modules\Cleaning\Models\CleaningServiceTemplate;

class CleaningServiceTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('cleaning.operations.monitor') || $user->can('cleaning.workers.manage');
    }

    public function view(User $user, CleaningServiceTemplate $template): bool
    {
        return $user->can('cleaning.operations.monitor') || $user->can('cleaning.workers.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('cleaning.operations.monitor');
    }

    public function update(User $user, CleaningServiceTemplate $template): bool
    {
        return $user->can('cleaning.operations.monitor');
    }

    public function delete(User $user, CleaningServiceTemplate $template): bool
    {
        return $user->can('cleaning.operations.monitor');
    }

    public function restore(User $user, CleaningServiceTemplate $template): bool
    {
        return false;
    }

    public function forceDelete(User $user, CleaningServiceTemplate $template): bool
    {
        return false;
    }
}

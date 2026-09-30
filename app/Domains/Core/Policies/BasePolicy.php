<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\User;
use App\Domains\Core\Services\ContextManager;

abstract class BasePolicy
{
    protected ContextManager $contextManager;

    public function __construct(ContextManager $contextManager)
    {
        $this->contextManager = $contextManager;
    }

    /**
     * Common pre-authorization check for all policies.
     */
    public function before(User $user, string $ability)
    {
        if ($user->hasRole('Super Admin', 'Organization', 1)) {
            return true;
        }

        return null;
    }
}

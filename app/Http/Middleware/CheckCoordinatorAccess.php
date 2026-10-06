<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCoordinatorAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        if ($user->can('cleaning.operations.monitor') ||
            $user->can('cleaning.workers.manage') ||
            $user->can('cleaning.store.view')) {
            return $next($request);
        }

        $roles = $user->roles->pluck('name')->all();
        if (! in_array('Cleaning Manager', $roles) && ! in_array('Super Admin', $roles)) {
            abort(403, 'Unauthorized access. Only Cleaning Managers can perform this action.');
        }

        return $next($request);
    }
}

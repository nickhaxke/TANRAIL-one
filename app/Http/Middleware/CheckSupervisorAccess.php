<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSupervisorAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        if ($user->can('cleaning.operations.manage') ||
            $user->can('cleaning.operations.monitor')) {
            return $next($request);
        }

        $roles = $user->roles->pluck('name')->all();
        if (! in_array('Cleaning Supervisor', $roles) && ! in_array('Cleaning Manager', $roles) && ! in_array('Super Admin', $roles)) {
            abort(403, 'Unauthorized access. Only Cleaning Supervisors can perform this action.');
        }

        return $next($request);
    }
}

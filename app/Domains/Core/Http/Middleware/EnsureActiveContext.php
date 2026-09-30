<?php

namespace App\Domains\Core\Http\Middleware;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Services\ContextManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $contextManager = app(ContextManager::class);
        $buId = $contextManager->getActiveBusinessUnitId();

        if (! $buId) {
            return redirect()->route('context.switcher');
        }

        $user = Auth::user();
        if ($user && ! $user->hasAccessToScope(BusinessUnit::class, $buId)) {
            $contextManager->clear();
            abort(403, 'Your access to this Business Unit has been revoked.');
        }

        return $next($request);
    }
}

<?php

namespace App\Domains\Core\Http\Controllers;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Services\ContextManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ContextController extends Controller
{
    public function showSwitcher()
    {
        $user = Auth::user();

        $directBuIds = $user->roles()
            ->wherePivot('scope_type', BusinessUnit::class)
            ->pluck('scope_id')
            ->toArray();

        $orgIds = $user->roles()
            ->wherePivot('scope_type', Organization::class)
            ->pluck('scope_id')
            ->toArray();

        $businessUnits = BusinessUnit::where('status', true)
            ->where(function ($query) use ($directBuIds, $orgIds) {
                $query->whereIn('id', $directBuIds)
                    ->orWhereIn('organization_id', $orgIds);
            })
            ->get();

        return view('context.switcher', compact('businessUnits'));
    }

    public function switchContext(Request $request, ContextManager $contextManager)
    {
        $request->validate([
            'business_unit_id' => 'required|integer|exists:business_units,id',
        ]);

        $buId = $request->input('business_unit_id');
        $user = Auth::user();

        if (! $user->hasAccessToScope(BusinessUnit::class, $buId)) {
            abort(403, 'Unauthorized access to this Business Unit.');
        }

        $bu = BusinessUnit::findOrFail($buId);
        $contextManager->setActiveBusinessUnit($bu);

        return redirect()->route('dashboard');
    }
}

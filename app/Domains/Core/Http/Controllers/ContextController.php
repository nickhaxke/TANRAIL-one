<?php

namespace App\Domains\Core\Http\Controllers;

use App\Domains\Core\Models\Branch;
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

        $branchBuIds = Branch::whereIn('id', function ($query) use ($user) {
            $query->select('scope_id')
                ->from('role_user')
                ->where('user_id', $user->id)
                ->where('scope_type', Branch::class);
        })->pluck('business_unit_id')->toArray();

        $allowedBuIds = array_unique(array_merge($directBuIds, $branchBuIds));

        $businessUnits = BusinessUnit::where('status', true)
            ->where(function ($query) use ($allowedBuIds, $orgIds) {
                $query->whereIn('id', $allowedBuIds)
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

<?php

namespace App\Http\Controllers\Cleaning;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\User;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Modules\Cleaning\Models\DailyControl;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CleaningCoordinatorController extends Controller
{
    public function operations(ContextManager $context)
    {
        if (! Auth::user()->can('cleaning.operations.monitor')) {
            abort(403, 'Unauthorized access to operational monitoring.');
        }

        $buId = $context->getActiveBusinessUnitId();

        $dailyControls = DailyControl::with(['branch', 'supervisor'])
            ->where('business_unit_id', $buId)
            ->orderByDesc('date')
            ->paginate(30);

        return view('cleaning.coordinator.operations', compact('dailyControls'));
    }

    public function supervisors(ContextManager $context)
    {
        if (! Auth::user()->can('cleaning.operations.monitor')) {
            abort(403, 'Unauthorized access to supervisor directory.');
        }

        $buId = $context->getActiveBusinessUnitId();

        $supervisors = User::whereHas('roles', function ($q) use ($buId) {
            $q->where('name', 'Cleaning Supervisor')
                ->where(function ($sub) use ($buId) {
                    $sub->where('role_user.scope_type', Branch::class)
                        ->whereIn('role_user.scope_id', function ($bQuery) use ($buId) {
                            $bQuery->select('id')->from('branches')->where('business_unit_id', $buId);
                        });
                });
        })->with(['cleaningSupervisorAssignments' => function ($query) use ($buId) {
            $query->where('business_unit_id', $buId)
                  ->with(['serviceType', 'branch'])
                  ->orderByDesc('start_date');
        }])->get();

        $branches = Branch::where('business_unit_id', $buId)->where('status', 1)->get();
        $serviceTypes = \App\Domains\Modules\Cleaning\Models\CleaningServiceType::where('business_unit_id', $buId)->where('is_active', true)->get();

        return view('cleaning.coordinator.supervisors', compact('supervisors', 'branches', 'serviceTypes'));
    }

    public function reports()
    {
        if (! Auth::user()->can('cleaning.operations.monitor')) {
            abort(403, 'Unauthorized access to operational reports.');
        }

        return view('cleaning.coordinator.reports');
    }
}

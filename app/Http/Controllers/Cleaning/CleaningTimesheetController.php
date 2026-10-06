<?php

namespace App\Http\Controllers\Cleaning;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Modules\Cleaning\Services\CleaningTimesheetService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CleaningTimesheetController extends Controller
{
    public function index(Request $request, ContextManager $contextManager, CleaningTimesheetService $timesheetService)
    {
        $user = Auth::user();
        if (! $user->can('cleaning.operations.manage') && ! $user->can('cleaning.operations.monitor')) {
            abort(403, 'Unauthorized access to operational timesheets.');
        }

        $buId = $contextManager->getActiveBusinessUnitId();
        $date = $request->input('date', today()->toDateString());

        $branchId = null;
        if (! $user->can('cleaning.operations.monitor')) {
            // Station Supervisor is strictly isolated to their assigned branch
            $branchId = $user->roles()
                ->wherePivot('scope_type', Branch::class)
                ->value('scope_id');

            if (! $branchId) {
                abort(403, 'You are not assigned to any station branch.');
            }
        } elseif ($request->filled('branch_id')) {
            // Manager can filter by branch
            $branchId = (int) $request->input('branch_id');
        }

        $timesheetEntries = $timesheetService->getDailyTimesheet($buId, $branchId, $date);
        $branches = Branch::where('business_unit_id', $buId)->where('status', true)->get();

        return view('cleaning.timesheets.index', compact('timesheetEntries', 'date', 'branches', 'branchId'));
    }
}

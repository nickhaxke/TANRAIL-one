<?php

namespace App\Http\Controllers\Cleaning;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\User;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Modules\Cleaning\Models\CleaningSupervisorAssignment;
use App\Domains\Modules\Cleaning\Models\DailyControl;
use App\Domains\Modules\Cleaning\Models\WorkerAttendance;
use App\Http\Controllers\Controller;

class CleaningDashboardController extends Controller
{
    public function index(ContextManager $contextManager)
    {
        $buId = $contextManager->getActiveBusinessUnitId();
        $businessUnit = BusinessUnit::find($buId);

        // Basic check to ensure context is correctly passed
        if (! $businessUnit || ! in_array($businessUnit->category, ['Cleaning Operations', 'Facilities Management'])) {
            return redirect()->route('context.switcher')->with('error', 'Please select a Cleaning Business Unit.');
        }

        $user = auth()->user();

        if ($user->can('cleaning.operations.monitor')) {
            $stats = [
                'active_branches' => Branch::where('business_unit_id', $buId)->where('status', true)->count(),
                'active_supervisors' => User::whereHas('roles', function ($q) use ($buId) {
                    $q->where('name', 'Cleaning Supervisor')
                        ->where(function ($sub) use ($buId) {
                            $sub->where('role_user.scope_type', Branch::class)
                                ->whereIn('role_user.scope_id', function ($bQuery) use ($buId) {
                                    $bQuery->select('id')->from('branches')->where('business_unit_id', $buId);
                                });
                        });
                })->count(),
                'daily_controls_today' => DailyControl::where('business_unit_id', $buId)->whereDate('date', today())->count(),
                'workers_present' => WorkerAttendance::where('business_unit_id', $buId)
                    ->whereHas('dailyControl', fn ($q) => $q->whereDate('date', today()))
                    ->where('is_present', true)
                    ->count(),
            ];

            return view('cleaning.dashboard_coordinator', compact('businessUnit', 'stats'));
        }

        if ($user->can('cleaning.store.receive') && ! $user->can('cleaning.operations.manage')) {
            return redirect()->route('cleaning.store.index');
        }

        $branchId = session('active_branch_id'); // We need the branch context
        if (! $branchId) {
            // Attempt to resolve branch from assignments or default context
            $assignment = CleaningSupervisorAssignment::where('supervisor_id', $user->id)
                ->active()
                ->first();
            $branchId = $assignment ? $assignment->branch_id : null;
        }

        $dailyControl = null;
        $stats = [
            'assigned' => 0,
            'present' => 0,
            'absent' => 0,
            'checked_out' => 0,
            'ops_planned' => 0,
            'ops_in_progress' => 0,
            'ops_completed' => 0,
            'ops_incomplete' => 0,
            'issues_open' => 0,
            'issues_resolved' => 0,
        ];

        if ($branchId) {
            $dailyControl = DailyControl::where('supervisor_id', $user->id)
                ->where('branch_id', $branchId)
                ->whereDate('date', today())
                ->with(['workerAttendances', 'workActivities', 'operationalIssues'])
                ->first();

            if ($dailyControl) {
                $stats['assigned'] = $dailyControl->workerAttendances->count();
                $stats['present'] = $dailyControl->workerAttendances->where('status', 'Present')->count();
                $stats['absent'] = $dailyControl->workerAttendances->where('status', 'Absent')->count();
                $stats['checked_out'] = $dailyControl->workerAttendances->where('status', 'CheckedOut')->count();

                $stats['ops_planned'] = $dailyControl->workActivities->where('status', 'Pending')->count();
                $stats['ops_in_progress'] = $dailyControl->workActivities->where('status', 'Started')->count();
                $stats['ops_completed'] = $dailyControl->workActivities->where('status', 'Completed')->count();
                $stats['ops_incomplete'] = $dailyControl->workActivities->whereIn('status', ['Incomplete', 'Cancelled'])->count();

                $stats['issues_open'] = $dailyControl->operationalIssues->where('status', 'Open')->count();
                $stats['issues_resolved'] = $dailyControl->operationalIssues->where('status', 'Resolved')->count();
            }
        }

        return view('cleaning.dashboard', compact('businessUnit', 'dailyControl', 'stats', 'branchId'));
    }
}

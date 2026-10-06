<?php

namespace App\Http\Controllers\Cleaning;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Location;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Modules\Cleaning\Models\CleaningServiceTemplate;
use App\Domains\Modules\Cleaning\Models\CleaningSupervisorAssignment;
use App\Domains\Modules\Cleaning\Models\CleaningWorker;
use App\Domains\Modules\Cleaning\Models\DailyControl;
use App\Domains\Modules\Cleaning\Models\OperationalIssue;
use App\Domains\Modules\Cleaning\Models\WorkActivity;
use App\Domains\Modules\Cleaning\Models\WorkActivityItem;
use App\Domains\Modules\Cleaning\Models\WorkerAttendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CleaningDailyControlController extends Controller
{
    public function index(Request $request, ContextManager $contextManager)
    {
        $user = Auth::user();
        $buId = $contextManager->getActiveBusinessUnitId();
        if (! $buId) {
            return redirect()->route('context.switcher')->with('error', 'Please select a Cleaning Business Unit.');
        }

        $branch = $this->resolveActiveBranch($user, $request);
        if (! $branch) {
            return redirect()->route('context.switcher')->with('error', 'No cleaning branches found or you lack access.');
        }

        $dailyControl = DailyControl::where('branch_id', $branch->id)
            ->where('supervisor_id', $user->id)
            ->whereDate('date', today())
            ->with(['workerAttendances.cleaningWorker', 'workActivities.workerAttendances.cleaningWorker', 'workActivities.location', 'operationalIssues'])
            ->orderBy('id', 'desc')
            ->first();

        // Get active workers assigned to this station/branch (or already recorded in this control)
        $workers = CleaningWorker::where('is_active', true)
            ->where(function ($q) use ($branch, $dailyControl) {
                $q->where('current_branch_id', $branch->id);
                if ($dailyControl) {
                    $q->orWhereIn('id', $dailyControl->workerAttendances->pluck('cleaning_worker_id'));
                }
            })
            ->get();

        $locations = Location::where('branch_id', $branch->id)->get();

        $activeServiceTypes = CleaningSupervisorAssignment::where('supervisor_id', $user->id)
            ->where('branch_id', $branch->id)
            ->activeOnDate(today())
            ->with('serviceType')
            ->get()
            ->pluck('serviceType')
            ->filter();

        $tab = request('tab', 'team');

        if (! in_array($tab, ['team', 'operations', 'execute', 'issues', 'checkout', 'review'])) {
            $tab = 'team';
        }

        $workActivity = null;
        if ($tab === 'execute' && request()->has('activity_id')) {
            $workActivity = WorkActivity::where('id', request('activity_id'))
                ->where('daily_control_id', $dailyControl->id)
                ->with(['items', 'serviceType', 'serviceTemplate'])
                ->firstOrFail();
        }

        return view('cleaning.daily-control.workspace', compact('dailyControl', 'branch', 'workers', 'locations', 'activeServiceTypes', 'tab', 'workActivity'));
    }

    public function history(ContextManager $contextManager)
    {
        $user = Auth::user();

        $history = DailyControl::where('supervisor_id', $user->id)
            ->whereDate('date', '<=', today()) // include today just in case, or show all
            ->with(['branch', 'workActivities'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('cleaning.daily-control.history', compact('history'));
    }

    public function store(Request $request, ContextManager $contextManager)
    {
        $user = Auth::user();
        $buId = $contextManager->getActiveBusinessUnitId();
        $branch = $this->resolveActiveBranch($user, $request);

        if (! $buId || ! $branch) {
            return back()->with('error', 'Invalid context.');
        }

        if ($branch->business_unit_id !== $buId) {
            return back()->with('error', 'Branch does not belong to the active business unit.');
        }

        $shift = $request->input('shift', 'Day');
        $exists = DailyControl::where('branch_id', $branch->id)
            ->where('supervisor_id', $user->id)
            ->where('shift', $shift)
            ->whereDate('date', today())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Daily control already exists for today.');
        }

        DB::transaction(function () use ($buId, $branch, $user, $shift) {
            $dailyControl = DailyControl::create([
                'business_unit_id' => $buId,
                'branch_id' => $branch->id,
                'supervisor_id' => $user->id,
                'date' => today(),
                'shift' => $shift,
                'workforce_check_status' => 'Pending',
                'status' => 'Open',
            ]);

            // Roster snapshot
            $workers = CleaningWorker::where('is_active', true)
                ->where('current_branch_id', $branch->id)
                ->get();

            foreach ($workers as $worker) {
                WorkerAttendance::create([
                    'business_unit_id' => $buId,
                    'daily_control_id' => $dailyControl->id,
                    'cleaning_worker_id' => $worker->id,
                    'status' => 'Expected',
                    'is_present' => false,
                ]);
            }
        });

        return back()->with('success', 'Today\'s operations started.');
    }

    public function updateWorkforceCheck(Request $request, DailyControl $dailyControl)
    {
        Gate::authorize('update', $dailyControl);

        // Validation
        $request->validate([
            'attendances' => 'nullable|array',
            'attendances.*.cleaning_worker_id' => 'required|exists:cleaning_workers,id',
            'attendances.*.arrival_time' => 'nullable|date_format:H:i',
            'attendances.*.is_present' => 'nullable|boolean',
            'zero_worker_reason' => 'nullable|string',
            'action' => 'required|in:save,complete',
        ]);

        if ($dailyControl->workforce_check_status === 'Completed') {
            return back()->with('error', 'Workforce check is already completed.');
        }

        $attendances = collect($request->input('attendances', []))->filter(function ($a) {
            return ! empty($a['is_present']);
        });

        // Ensure all attached workers belong to this branch
        $workerIds = collect($request->input('attendances', []))->pluck('cleaning_worker_id')->filter();
        if ($workerIds->isNotEmpty()) {
            $validWorkersCount = CleaningWorker::whereIn('id', $workerIds)
                ->where(function ($q) use ($dailyControl) {
                    $q->where('current_branch_id', $dailyControl->branch_id)
                        ->orWhereIn('id', function ($sub) use ($dailyControl) {
                            $sub->select('cleaning_worker_id')->from('worker_attendances')->where('daily_control_id', $dailyControl->id);
                        });
                })
                ->count();

            if ($validWorkersCount !== $workerIds->unique()->count()) {
                return back()->withErrors(['attendances.0.cleaning_worker_id' => 'One or more workers are not assigned to this station.']);
            }
        }

        if ($request->input('action') === 'complete') {
            if ($attendances->isEmpty() && empty($request->input('zero_worker_reason'))) {
                return back()->with('error', 'You must record at least one worker or provide a reason for zero workers.');
            }
        }

        DB::transaction(function () use ($dailyControl, $attendances, $request) {
            $existing = WorkerAttendance::where('daily_control_id', $dailyControl->id)
                ->get()
                ->keyBy('cleaning_worker_id');

            $submittedIds = $attendances->pluck('cleaning_worker_id')->toArray();

            foreach ($attendances as $att) {
                $workerId = $att['cleaning_worker_id'];
                if ($existing->has($workerId)) {
                    $attendance = $existing->get($workerId);
                    if ($attendance->status === 'Expected' || $attendance->status === 'Absent') {
                        $attendance->update([
                            'status' => 'Present',
                            'is_present' => true,
                            'check_in_at' => now(),
                            'arrival_time' => $att['arrival_time'] ?? null,
                            'checked_in_by_id' => Auth::id(),
                        ]);
                    }
                } else {
                    // D7 Rule: Supervisors cannot add unrostered workers directly.
                    // Any worker must already be in the roster snapshot generated at start of day.
                }
            }

            if ($request->input('action') === 'complete') {
                $dailyControl->update([
                    'workforce_check_status' => 'Completed',
                    'zero_worker_reason' => $attendances->isEmpty() ? $request->input('zero_worker_reason') : null,
                ]);

                // Mark remaining Expected as Absent
                WorkerAttendance::where('daily_control_id', $dailyControl->id)
                    ->where('status', 'Expected')
                    ->whereNotIn('cleaning_worker_id', $submittedIds)
                    ->update([
                        'status' => 'Absent',
                        'is_present' => false,
                    ]);
            } else {
                $dailyControl->update([
                    'zero_worker_reason' => $request->input('zero_worker_reason'),
                ]);
            }
        });

        return back()->with('success', 'Workforce check updated.');
    }

    public function checkoutWorker(Request $request, DailyControl $dailyControl, WorkerAttendance $workerAttendance)
    {
        Gate::authorize('update', $dailyControl);

        if (in_array($dailyControl->status, ['Submitted', 'Approved'])) {
            return back()->with('error', 'Cannot modify submitted operations.');
        }

        if ($workerAttendance->daily_control_id !== $dailyControl->id) {
            return back()->with('error', 'Worker attendance does not belong to this daily control.');
        }

        if ($workerAttendance->status !== 'Present' || ! $workerAttendance->is_present) {
            return back()->with('error', 'Only present workers can be checked out.');
        }

        $workerAttendance->update([
            'status' => 'CheckedOut',
            'check_out_at' => now(),
            'checked_out_by_id' => Auth::id(),
        ]);

        return back()->with('success', 'Worker checked out successfully.');
    }

    public function bulkCheckoutWorkers(Request $request, DailyControl $dailyControl)
    {
        Gate::authorize('update', $dailyControl);

        if (in_array($dailyControl->status, ['Submitted', 'Approved'])) {
            return back()->with('error', 'Cannot modify submitted operations.');
        }

        $now = now();
        $userId = Auth::id();

        $updated = WorkerAttendance::where('daily_control_id', $dailyControl->id)
            ->where('status', 'Present')
            ->where('is_present', true)
            ->update([
                'status' => 'CheckedOut',
                'check_out_at' => $now,
                'checked_out_by_id' => $userId,
            ]);

        if ($updated > 0) {
            return back()->with('success', "{$updated} worker(s) checked out successfully.");
        }

        return back()->with('error', 'No eligible workers to check out.');
    }

    private function resolveActiveBranch($user, Request $request): ?Branch
    {
        $contextManager = app(ContextManager::class);
        $buId = $contextManager->getActiveBusinessUnitId();

        $branch = null;

        if ($request->has('branch_id')) {
            $branch = Branch::find($request->input('branch_id'));
        }

        if (! $branch) {
            $scopedBranchId = $user->roles()
                ->wherePivot('scope_type', Branch::class)
                ->value('scope_id');

            if ($scopedBranchId) {
                $branch = Branch::find($scopedBranchId);
            }
        }

        if (! $branch) {
            // Coordinator fallback to first active branch in BU if they didn't specify one
            $branch = Branch::where('business_unit_id', $buId)->where('status', true)->first();
        }

        // Authorization check!
        if ($branch) {
            // Must belong to the active business unit
            if ($branch->business_unit_id != $buId) {
                return null;
            }

            // Check if user has explicit access to this branch, or if they have access to its parent BU (Coordinator)
            if (! $user->hasAccessToScope(Branch::class, $branch->id) && ! $user->hasAccessToScope(BusinessUnit::class, $buId)) {
                return null;
            }
        }

        return $branch;
    }

    public function storeActivity(Request $request, DailyControl $dailyControl)
    {
        Gate::authorize('update', $dailyControl);

        if (in_array($dailyControl->status, ['Submitted', 'Approved'])) {
            return back()->with('error', 'Cannot modify submitted operations.');
        }

        $validated = $request->validate([
            'activity_name' => 'required|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'cleaning_service_type_id' => 'required|exists:cleaning_service_types,id',
            'cleaning_service_template_id' => 'required|exists:cleaning_service_templates,id',
            'worker_attendance_ids' => 'required|array|min:1',
            'worker_attendance_ids.*' => 'exists:worker_attendances,id',
        ]);

        $serviceTypeId = $validated['cleaning_service_type_id'];
        $templateId = $validated['cleaning_service_template_id'];

        // Enforce Assignment Authorization
        $hasAssignment = CleaningSupervisorAssignment::where('supervisor_id', Auth::id())
            ->where('branch_id', $dailyControl->branch_id)
            ->where('cleaning_service_type_id', $serviceTypeId)
            ->activeOnDate($dailyControl->date)
            ->exists();

        if (! $hasAssignment) {
            return back()->with('error', 'You do not have an active assignment for this Service Type at this branch.');
        }

        $template = CleaningServiceTemplate::where('id', $templateId)
            ->where('cleaning_service_type_id', $serviceTypeId)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return back()->with('error', 'The selected service template is invalid or inactive.');
        }

        DB::transaction(function () use ($validated, $dailyControl, $template) {
            $activity = $dailyControl->workActivities()->create([
                'business_unit_id' => $dailyControl->business_unit_id,
                'activity_name' => $validated['activity_name'],
                'location_id' => $validated['location_id'] ?? null,
                'status' => 'Pending',
                'verification_status' => 'Pending',
                'cleaning_service_type_id' => $template->cleaning_service_type_id,
                'cleaning_service_template_id' => $template->id,
                'created_by_id' => Auth::id(),
            ]);

            $activity->workerAttendances()->sync($validated['worker_attendance_ids']);

            // Snapshot template items
            $items = [];
            foreach ($template->items as $item) {
                $items[] = [
                    'cleaning_service_template_item_id' => $item->id,
                    'work_activity_id' => $activity->id,
                    'label' => $item->label,
                    'status' => 'Pending',
                    'target_qty' => $item->default_target_qty,
                    'unit' => $item->unit,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (count($items) > 0) {
                $activity->items()->insert($items);
            }
        });

        return back()->with('success', 'Operation created from template.');
    }

    public function updateActivityStatus(Request $request, WorkActivity $workActivity)
    {
        Gate::authorize('update', $workActivity);

        if (in_array($workActivity->dailyControl->status, ['Submitted', 'Approved'])) {
            return back()->with('error', 'Cannot modify submitted operations.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Pending,Started,Completed,Incomplete,Cancelled',
            'notes' => 'exclude_if:status,Pending,Started,Completed|required_if:status,Incomplete,Cancelled|string',
        ]);

        $updates = [
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $workActivity->notes,
        ];

        if ($validated['status'] === 'Started' && ! $workActivity->started_at) {
            $updates['started_at'] = now();
        }

        if (in_array($validated['status'], ['Completed', 'Incomplete', 'Cancelled']) && ! $workActivity->completed_at) {
            $updates['completed_at'] = now();
        }

        $workActivity->update($updates);

        return back()->with('success', 'Activity status updated.');
    }

    public function updateActivityVerification(Request $request, WorkActivity $workActivity)
    {
        Gate::authorize('update', $workActivity);

        if (in_array($workActivity->dailyControl->status, ['Submitted', 'Approved'])) {
            return back()->with('error', 'Cannot modify submitted operations.');
        }

        $validated = $request->validate([
            'verification_status' => 'required|in:Pending,Verified,Rejected',
        ]);

        $workActivity->update(['verification_status' => $validated['verification_status']]);

        return back()->with('success', 'Activity verification updated.');
    }

    public function updateActivityItems(Request $request, WorkActivity $workActivity)
    {
        Gate::authorize('update', $workActivity);

        if (in_array($workActivity->dailyControl->status, ['Submitted', 'Approved'])) {
            return back()->with('error', 'Cannot modify submitted operations.');
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:work_activity_items,id',
            'items.*.status' => 'required|in:Pending,Completed,Incomplete',
            'items.*.done_qty' => 'nullable|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ]);

        $itemIds = collect($validated['items'])->pluck('id')->toArray();

        // Ensure all items belong to this work activity
        $validItemsCount = WorkActivityItem::whereIn('id', $itemIds)
            ->where('work_activity_id', $workActivity->id)
            ->count();

        if ($validItemsCount !== count(array_unique($itemIds))) {
            return back()->with('error', 'Invalid items provided.');
        }

        DB::transaction(function () use ($validated) {
            foreach ($validated['items'] as $itemData) {
                WorkActivityItem::where('id', $itemData['id'])
                    ->update([
                        'status' => $itemData['status'],
                        'done_qty' => $itemData['done_qty'] ?? null,
                        'remarks' => $itemData['remarks'] ?? null,
                    ]);
            }
        });

        return back()->with('success', 'Activity items updated.');
    }

    public function storeIssue(Request $request, DailyControl $dailyControl)
    {
        Gate::authorize('update', $dailyControl);

        if (in_array($dailyControl->status, ['Submitted', 'Approved'])) {
            return back()->with('error', 'Cannot modify submitted operations.');
        }

        $validated = $request->validate([
            'issue_type' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $dailyControl->operationalIssues()->create([
            'business_unit_id' => $dailyControl->business_unit_id,
            'issue_type' => $validated['issue_type'],
            'description' => $validated['description'],
            'status' => 'Open',
        ]);

        return back()->with('success', 'Operational issue recorded.');
    }

    public function resolveIssue(Request $request, OperationalIssue $operationalIssue)
    {
        Gate::authorize('update', $operationalIssue);

        if (in_array($operationalIssue->dailyControl->status, ['Submitted', 'Approved'])) {
            return back()->with('error', 'Cannot modify submitted operations.');
        }

        $validated = $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $operationalIssue->update([
            'status' => 'Resolved',
            'resolution_notes' => $validated['resolution_notes'],
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Issue resolved.');
    }

    public function submit(Request $request, DailyControl $dailyControl)
    {
        Gate::authorize('update', $dailyControl);

        if (in_array($dailyControl->status, ['Submitted', 'Approved'])) {
            return back()->with('error', 'Operations are already submitted.');
        }

        if ($dailyControl->workforce_check_status !== 'Completed') {
            return back()->with('error', 'You must complete the morning workforce check first.');
        }

        $uncheckedOutCount = WorkerAttendance::where('daily_control_id', $dailyControl->id)
            ->where('status', 'Present')
            ->where('is_present', true)
            ->count();

        if ($uncheckedOutCount > 0) {
            $s = $uncheckedOutCount === 1 ? '' : 's';
            $them = $uncheckedOutCount === 1 ? 'the worker' : 'them';
            $are = $uncheckedOutCount === 1 ? 'is' : 'are';

            return back()->with('error', "{$uncheckedOutCount} worker{$s} {$are} still checked in. Check {$them} out before submitting the day.");
        }

        $incompleteOps = $dailyControl->workActivities()->whereIn('status', ['Pending', 'Started'])->count();
        if ($incompleteOps > 0) {
            return back()->with('error', "There are {$incompleteOps} operations still In Progress or Planned. They must be Completed, Incomplete, or Cancelled.");
        }

        $dailyControl->update([
            'status' => 'Submitted',
            'submitted_at' => now(),
            'submitted_by_id' => Auth::id(),
            'supervisor_remarks' => $request->input('supervisor_remarks'),
        ]);

        return redirect()->route('cleaning.dashboard')->with('success', 'Daily operations submitted successfully.');
    }
}

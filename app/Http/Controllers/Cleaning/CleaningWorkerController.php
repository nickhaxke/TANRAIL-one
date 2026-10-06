<?php

namespace App\Http\Controllers\Cleaning;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\User;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Modules\Cleaning\Models\CleaningWorker;
use App\Http\Controllers\Controller;
use App\Http\Requests\CleaningWorkerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CleaningWorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ContextManager $contextManager)
    {
        $user = Auth::user();
        if (! $user->can('cleaning.workers.view') && ! $user->can('cleaning.workers.manage')) {
            abort(403, 'Unauthorized access to Cleaning Workers.');
        }

        $buId = $contextManager->getActiveBusinessUnitId();

        $query = CleaningWorker::with(['currentBranch', 'currentSupervisor'])->latest();

        // If user is a Station Supervisor without manager authority, isolate to their assigned branch
        if (! $user->can('cleaning.workers.manage')) {
            $scopedBranchId = $user->roles()
                ->wherePivot('scope_type', Branch::class)
                ->value('scope_id');

            if ($scopedBranchId) {
                $query->where('current_branch_id', $scopedBranchId);
            }
        } elseif ($request->filled('branch_id')) {
            $query->where('current_branch_id', $request->input('branch_id'));
        }

        $workers = $query->paginate(15);
        $branches = Branch::where('business_unit_id', $buId)->where('status', true)->get();

        return view('cleaning.workers.index', compact('workers', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ContextManager $contextManager)
    {
        if (! Auth::user()->can('cleaning.workers.manage')) {
            abort(403, 'Unauthorized. Only Cleaning Managers can create workers.');
        }

        $buId = $contextManager->getActiveBusinessUnitId();
        $branches = Branch::where('business_unit_id', $buId)->where('status', true)->get();
        $supervisors = User::whereHas('roles', function ($q) {
            $q->where('name', 'Cleaning Supervisor');
        })->get();

        return view('cleaning.workers.create', compact('branches', 'supervisors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CleaningWorkerRequest $request, ContextManager $contextManager)
    {
        $validated = $request->validated();

        $buId = $contextManager->getActiveBusinessUnitId();
        $validated['business_unit_id'] = $buId;
        $validated['is_active'] = $request->has('is_active') ? $request->is_active : true;

        $worker = CleaningWorker::create($validated);

        if (! empty($validated['current_branch_id'])) {
            $branch = Branch::where('business_unit_id', $buId)->findOrFail($validated['current_branch_id']);
            $supervisor = ! empty($validated['current_supervisor_id']) ? User::find($validated['current_supervisor_id']) : null;
            $worker->assignTo($branch, $supervisor, Auth::user(), 'Initial station assignment on creation');
        }

        return redirect()->route('cleaning.workers.index')
            ->with('success', 'Cleaning worker created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CleaningWorker $worker, ContextManager $contextManager)
    {
        $user = Auth::user();
        if (! $user->can('cleaning.workers.view') && ! $user->can('cleaning.workers.manage')) {
            abort(403, 'Unauthorized access to Cleaning Worker details.');
        }

        // Supervisor can only view workers assigned to their branch
        if (! $user->can('cleaning.workers.manage')) {
            $scopedBranchId = $user->roles()
                ->wherePivot('scope_type', Branch::class)
                ->value('scope_id');

            if ($scopedBranchId && $worker->current_branch_id !== $scopedBranchId) {
                abort(403, 'Unauthorized. This worker is not assigned to your station.');
            }
        }

        $worker->load(['currentBranch', 'currentSupervisor', 'assignments.branch', 'assignments.supervisor', 'assignments.assignedBy']);

        $buId = $contextManager->getActiveBusinessUnitId();
        $branches = Branch::where('business_unit_id', $buId)->where('status', true)->get();
        $supervisors = User::whereHas('roles', function ($q) {
            $q->where('name', 'Cleaning Supervisor');
        })->get();

        return view('cleaning.workers.show', compact('worker', 'branches', 'supervisors'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CleaningWorker $worker, ContextManager $contextManager)
    {
        if (! Auth::user()->can('cleaning.workers.manage')) {
            abort(403, 'Unauthorized. Only Cleaning Managers can edit workers.');
        }

        $buId = $contextManager->getActiveBusinessUnitId();
        $branches = Branch::where('business_unit_id', $buId)->where('status', true)->get();
        $supervisors = User::whereHas('roles', function ($q) {
            $q->where('name', 'Cleaning Supervisor');
        })->get();

        return view('cleaning.workers.edit', compact('worker', 'branches', 'supervisors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CleaningWorkerRequest $request, CleaningWorker $worker, ContextManager $contextManager)
    {
        $validated = $request->validated();
        $buId = $contextManager->getActiveBusinessUnitId();

        $validated['is_active'] = $request->boolean('is_active');
        $worker->update($validated);

        if (! empty($validated['current_branch_id']) && $validated['current_branch_id'] != $worker->current_branch_id) {
            $branch = Branch::where('business_unit_id', $buId)->findOrFail($validated['current_branch_id']);
            $supervisor = ! empty($validated['current_supervisor_id']) ? User::find($validated['current_supervisor_id']) : null;
            $worker->assignTo($branch, $supervisor, Auth::user(), 'Updated station assignment');
        }

        return redirect()->route('cleaning.workers.index')
            ->with('success', 'Cleaning worker updated successfully.');
    }

    /**
     * Assign or reassign worker to a branch and supervisor.
     */
    public function assign(Request $request, CleaningWorker $worker, ContextManager $contextManager)
    {
        if (! Auth::user()->can('cleaning.workers.manage')) {
            abort(403, 'Unauthorized. Only Cleaning Managers can assign workers.');
        }

        $buId = $contextManager->getActiveBusinessUnitId();

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $branch = Branch::where('business_unit_id', $buId)->findOrFail($validated['branch_id']);
        $supervisor = ! empty($validated['supervisor_id']) ? User::findOrFail($validated['supervisor_id']) : null;

        $worker->assignTo($branch, $supervisor, Auth::user(), $validated['notes'] ?? 'Manager reassignment');

        return redirect()->back()->with('success', "Worker {$worker->first_name} {$worker->last_name} assigned to {$branch->name} successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CleaningWorker $worker)
    {
        if (! Auth::user()->can('cleaning.workers.manage')) {
            abort(403, 'Unauthorized. Only Cleaning Managers can delete workers.');
        }

        $worker->delete();

        return redirect()->route('cleaning.workers.index')
            ->with('success', 'Cleaning worker deactivated and removed successfully.');
    }
}

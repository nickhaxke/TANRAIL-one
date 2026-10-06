<?php

namespace App\Http\Controllers\Cleaning;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\User;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Modules\Cleaning\Models\CleaningServiceType;
use App\Domains\Modules\Cleaning\Models\CleaningSupervisorAssignment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CleaningSupervisorAssignmentController extends Controller
{
    public function store(Request $request, ContextManager $context)
    {
        Gate::authorize('create', CleaningSupervisorAssignment::class);

        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'cleaning_service_type_id' => 'required|exists:cleaning_service_types,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $buId = $context->getActiveBusinessUnitId();

        // 1. Supervisor must be an authorized Cleaning Supervisor
        $isSupervisor = User::where('id', $validated['supervisor_id'])
            ->whereHas('roles', fn ($q) => $q->where('name', 'Cleaning Supervisor'))
            ->exists();
        if (! $isSupervisor) {
            return back()->with('error', 'User is not a Cleaning Supervisor.');
        }

        // 2. Branch must belong to the Cleaning Business Unit
        $branch = Branch::where('id', $validated['branch_id'])->where('business_unit_id', $buId)->first();
        if (! $branch) {
            return back()->with('error', 'Branch does not belong to the active Business Unit.');
        }

        // 3. Service Type must belong to the same Cleaning Business Unit
        $serviceType = CleaningServiceType::where('id', $validated['cleaning_service_type_id'])->where('business_unit_id', $buId)->first();
        if (! $serviceType) {
            return back()->with('error', 'Service Type does not belong to the active Business Unit.');
        }

        // 6. Duplicate overlapping assignments should be prevented
        $overlap = CleaningSupervisorAssignment::where('supervisor_id', $validated['supervisor_id'])
            ->where('branch_id', $validated['branch_id'])
            ->where('cleaning_service_type_id', $validated['cleaning_service_type_id'])
            ->where(function ($q) use ($validated) {
                $q->where(function ($sub) use ($validated) {
                    $sub->whereNull('end_date')
                        ->orWhere('end_date', '>=', $validated['start_date']);
                });
                if (! empty($validated['end_date'])) {
                    $q->where('start_date', '<=', $validated['end_date']);
                }
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'An overlapping assignment already exists for this supervisor, branch, and service type.');
        }

        CleaningSupervisorAssignment::create([
            'business_unit_id' => $buId,
            'supervisor_id' => $validated['supervisor_id'],
            'branch_id' => $validated['branch_id'],
            'cleaning_service_type_id' => $validated['cleaning_service_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'assigned_by_id' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Supervisor assigned successfully.');
    }

    public function end(Request $request, CleaningSupervisorAssignment $assignment)
    {
        Gate::authorize('update', $assignment);

        $validated = $request->validate([
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $assignment->update([
            'end_date' => $validated['end_date'],
        ]);

        return back()->with('success', 'Assignment ended successfully.');
    }
}

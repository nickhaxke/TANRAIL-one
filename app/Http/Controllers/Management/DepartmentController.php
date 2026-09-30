<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\Department;
use App\Domains\Core\Models\User;
use App\Domains\Core\Services\ContextManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::with(['branch', 'head'])->get();

        return view('management.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();
        $users = User::all();

        return view('management.departments.form', compact('branches', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|string|unique:departments,department_id',
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'head_id' => 'nullable|exists:users,id',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status');
        $validated['organization_id'] = app(ContextManager::class)->getActiveOrganizationId() ?? 1;

        Department::create($validated);

        return redirect()->route('management.organization.departments')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $branches = Branch::all();
        $users = User::all();

        return view('management.departments.form', compact('department', 'branches', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'department_id' => 'required|string|unique:departments,department_id,'.$department->id,
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'head_id' => 'nullable|exists:users,id',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status');

        $department->update($validated);

        return redirect()->route('management.organization.departments')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('management.organization.departments')
            ->with('success', 'Department deleted successfully.');
    }
}

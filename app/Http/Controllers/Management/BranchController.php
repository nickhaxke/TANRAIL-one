<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        return redirect()->route('management.organization.branches');
    }

    public function create(?BusinessUnit $businessUnit = null)
    {
        $businessUnits = BusinessUnit::where('status', true)->get();
        $users = User::orderBy('name')->get();

        return view('management.branches.create', compact('businessUnit', 'businessUnits', 'users'));
    }

    public function store(Request $request, ?BusinessUnit $businessUnit = null)
    {
        if (! $businessUnit || ! $businessUnit->exists) {
            $buId = $request->input('business_unit_id');
            $businessUnit = BusinessUnit::findOrFail($buId);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|boolean',
            'facility_type' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'manager_user_id' => 'nullable|exists:users,id',
            'manager_name' => 'nullable|string|max:255',
        ]);

        if (! empty($validated['manager_user_id']) && empty($validated['manager_name'])) {
            $manager = User::find($validated['manager_user_id']);
            if ($manager) {
                $validated['manager_name'] = $manager->name;
                $validated['email'] = $validated['email'] ?? $manager->email;
            }
        }

        $validated['status'] = $request->boolean('status', true);

        $branch = $businessUnit->branches()->create($validated);

        return redirect()->route('management.organization.branches')->with('success', 'Branch "'.$branch->name.'" was created successfully.');
    }

    public function show(Branch $branch)
    {
        $branch->load(['businessUnit.organization', 'departments', 'managerUser']);

        return view('management.branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $branch->load(['businessUnit.organization', 'managerUser']);
        $businessUnits = BusinessUnit::where('status', true)->get();
        $users = User::orderBy('name')->get();

        return view('management.branches.edit', compact('branch', 'businessUnits', 'users'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'business_unit_id' => 'nullable|exists:business_units,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,'.$branch->id,
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|boolean',
            'facility_type' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'manager_user_id' => 'nullable|exists:users,id',
            'manager_name' => 'nullable|string|max:255',
        ]);

        if (! empty($validated['manager_user_id']) && empty($validated['manager_name'])) {
            $manager = User::find($validated['manager_user_id']);
            if ($manager) {
                $validated['manager_name'] = $manager->name;
                $validated['email'] = $validated['email'] ?? $manager->email;
            }
        }

        $validated['status'] = $request->boolean('status');

        $branch->update($validated);

        return redirect()->route('management.organization.branches')->with('success', 'Branch "'.$branch->name.'" was updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $name = $branch->name;
        $branch->delete();

        return redirect()->route('management.organization.branches')->with('success', 'Branch "'.$name.'" was deleted successfully.');
    }

    public function toggleStatus(Branch $branch)
    {
        $branch->status = ! $branch->status;
        $branch->save();

        $statusText = $branch->status ? 'activated' : 'deactivated';

        return redirect()->route('management.organization.branches')->with('success', 'Branch "'.$branch->name.'" was '.$statusText.' successfully.');
    }
}

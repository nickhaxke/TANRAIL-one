<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusinessUnitController extends Controller
{
    public function index()
    {
        return redirect()->route('management.organization.business-units');
    }

    public function create(?Organization $organization = null)
    {
        if (! $organization || ! $organization->exists) {
            $organization = Organization::first();
            if (! $organization) {
                $organization = Organization::create([
                    'name' => 'TANRAIL Investments Limited',
                    'code' => 'TANRAIL',
                    'status' => true,
                ]);
            }
        }

        $users = User::orderBy('name')->get();

        return view('management.business-units.create', compact('organization', 'users'));
    }

    public function store(Request $request, ?Organization $organization = null)
    {
        if (! $organization || ! $organization->exists) {
            $organization = Organization::find($request->input('organization_id')) ?? Organization::first();
            if (! $organization) {
                $organization = Organization::create([
                    'name' => 'TANRAIL Investments Limited',
                    'code' => 'TANRAIL',
                    'status' => true,
                ]);
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:business_units,code',
            'status' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'cost_center' => 'nullable|string|max:100',
            'manager_user_id' => 'nullable|exists:users,id',
            'manager_name' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
            'manager_phone' => 'nullable|string|max:50',
        ]);

        if (! empty($validated['manager_user_id']) && empty($validated['manager_name'])) {
            $manager = User::find($validated['manager_user_id']);
            if ($manager) {
                $validated['manager_name'] = $manager->name;
                $validated['manager_email'] = $validated['manager_email'] ?? $manager->email;
            }
        }

        $validated['status'] = $request->boolean('status', true);

        $businessUnit = $organization->businessUnits()->create($validated);

        return redirect()->route('management.organization.business-units')->with('success', 'Business Unit "'.$businessUnit->name.'" was created successfully.');
    }

    public function show(BusinessUnit $businessUnit)
    {
        $businessUnit->load(['organization', 'branches']);

        return view('management.business-units.show', compact('businessUnit'));
    }

    public function edit(BusinessUnit $businessUnit)
    {
        $businessUnit->load(['organization', 'managerUser']);
        $users = User::orderBy('name')->get();

        return view('management.business-units.edit', compact('businessUnit', 'users'));
    }

    public function update(Request $request, BusinessUnit $businessUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:business_units,code,'.$businessUnit->id,
            'status' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'cost_center' => 'nullable|string|max:100',
            'manager_user_id' => 'nullable|exists:users,id',
            'manager_name' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
            'manager_phone' => 'nullable|string|max:50',
        ]);

        if (! empty($validated['manager_user_id']) && empty($validated['manager_name'])) {
            $manager = User::find($validated['manager_user_id']);
            if ($manager) {
                $validated['manager_name'] = $manager->name;
                $validated['manager_email'] = $validated['manager_email'] ?? $manager->email;
            }
        }

        $validated['status'] = $request->boolean('status');

        $businessUnit->update($validated);

        return redirect()->route('management.organization.business-units')->with('success', 'Business Unit "'.$businessUnit->name.'" was updated successfully.');
    }

    public function destroy(BusinessUnit $businessUnit)
    {
        $name = $businessUnit->name;
        $businessUnit->delete();

        return redirect()->route('management.organization.business-units')->with('success', 'Business Unit "'.$name.'" was deleted successfully.');
    }

    public function toggleStatus(BusinessUnit $businessUnit)
    {
        $businessUnit->status = ! $businessUnit->status;
        $businessUnit->save();

        $statusText = $businessUnit->status ? 'activated' : 'deactivated';

        return redirect()->route('management.organization.business-units')->with('success', 'Business Unit "'.$businessUnit->name.'" was '.$statusText.' successfully.');
    }
}

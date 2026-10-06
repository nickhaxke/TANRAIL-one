<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\Organization;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $organization = Organization::with(['businessUnits.branches'])->first();
        if (! $organization) {
            $organization = Organization::create([
                'name' => 'TANRAIL Investments Limited',
                'code' => 'TANRAIL',
                'status' => true,
            ]);
        }

        return view('management.organization.index', compact('organization'));
    }

    public function businessUnits()
    {
        $organization = Organization::with(['businessUnits.branches'])->first();
        if (! $organization) {
            $organization = Organization::create([
                'name' => 'TANRAIL Investments Limited',
                'code' => 'TANRAIL',
                'status' => true,
            ]);
        }

        $businessUnits = $organization->businessUnits()->withCount('branches')->with('branches')->get();

        return view('management.business-units.index', compact('organization', 'businessUnits'));
    }

    public function branches()
    {
        $organization = Organization::first();
        $businessUnits = $organization->businessUnits()->where('status', true)->get();
        $branches = Branch::whereIn('business_unit_id', $businessUnits->pluck('id'))->with('businessUnit')->latest()->get();

        return view('management.branches.index', compact('organization', 'businessUnits', 'branches'));
    }

    public function departments()
    {
        return redirect()->route('management.organization.business-units');
    }

    public function locations()
    {
        return redirect()->route('management.organization.branches');
    }

    public function structure()
    {
        $organization = Organization::with([
            'businessUnits.managerUser',
            'businessUnits.branches.managerUser',
        ])->first();

        if (! $organization) {
            $organization = Organization::create([
                'name' => 'TANRAIL Investments Limited',
                'code' => 'TANRAIL',
                'status' => true,
            ]);
        }

        return view('management.organization.structure', compact('organization'));
    }

    public function create()
    {
        return view('management.organization.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:organizations,code',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status');

        Organization::create($validated);

        return redirect()->route('management.organization.index')->with('success', 'Organization created successfully.');
    }

    public function edit(Organization $organization)
    {
        return view('management.organization.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:organizations,code,'.$organization->id,
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status');

        $organization->update($validated);

        return redirect()->route('management.organization.index')->with('success', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('management.organization.index')->with('success', 'Organization deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\Location;
use App\Domains\Core\Services\ContextManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::with('branch')->get();

        return view('management.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();

        return view('management.locations.form', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status');
        $validated['organization_id'] = app(ContextManager::class)->getActiveOrganizationId() ?? 1;

        Location::create($validated);

        return redirect()->route('management.organization.locations')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        $branches = Branch::all();

        return view('management.locations.form', compact('location', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->has('status');

        $location->update($validated);

        return redirect()->route('management.organization.locations')
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('management.organization.locations')
            ->with('success', 'Location deleted successfully.');
    }
}

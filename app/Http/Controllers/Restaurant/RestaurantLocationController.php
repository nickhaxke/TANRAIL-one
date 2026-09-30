<?php

namespace App\Http\Controllers\Restaurant;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\StockBalance;
use App\Domains\Core\Services\ContextManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RestaurantLocationController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        if ($branch->businessUnit) {
            app(ContextManager::class)->setActiveBusinessUnit($branch->businessUnit);
        }
        app(ContextManager::class)->setActiveBranch($branch);

        $locations = InventoryLocation::with('branch')
            ->where('branch_id', $branch->id)
            ->get();

        // Get stock summary for each location
        $locations->each(function ($loc) {
            $loc->total_stock = StockBalance::where('inventory_location_id', $loc->id)->sum('quantity');
        });

        $allBranches = Branch::where('status', true)->get();

        return view('restaurant.locations.index', compact('branch', 'locations', 'allBranches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'status' => 'required|string|in:active,inactive',
        ]);

        $validated['branch_id'] = $branch->id;

        // Ensure unique code per branch
        if (InventoryLocation::where('branch_id', $branch->id)->where('code', $validated['code'])->exists()) {
            return redirect()->back()->with('error', 'Location code already exists for this branch.');
        }

        InventoryLocation::create($validated);

        return redirect()->back()->with('success', 'Location created successfully.');
    }

    public function update(Request $request, InventoryLocation $location): RedirectResponse
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        if ($location->branch_id !== $branch->id) {
            return redirect()->back()->with('error', 'Unauthorized access to this location.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'status' => 'required|string|in:active,inactive',
        ]);

        if (InventoryLocation::where('branch_id', $branch->id)->where('code', $validated['code'])->where('id', '!=', $location->id)->exists()) {
            return redirect()->back()->with('error', 'Location code already exists for this branch.');
        }

        $location->update($validated);

        return redirect()->back()->with('success', 'Location updated successfully.');
    }
    
    public function setDefaultSalesLocation(Request $request, InventoryLocation $location): RedirectResponse
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        if ($location->branch_id !== $branch->id) {
            return redirect()->back()->with('error', 'Unauthorized access to this location.');
        }
        
        if ($location->status !== 'active') {
            return redirect()->back()->with('error', 'Cannot set an inactive location as default.');
        }

        $branch->default_sales_location_id = $location->id;
        $branch->save();

        return redirect()->back()->with('success', 'Default sales location updated successfully.');
    }

    protected function resolveActiveBranch($user, Request $request): Branch
    {
        // Use session branch first (set at login or explicit branch switch)
        $sessionBranchId = session('active_branch_id');
        if ($sessionBranchId) {
            $branch = Branch::find($sessionBranchId);
            if ($branch) {
                return $branch;
            }
        }

        // Fall back to role-scoped branch
        $scopedBranchId = $user->roles()
            ->wherePivot('scope_type', Branch::class)
            ->value('scope_id');

        if ($scopedBranchId) {
            $branch = Branch::find($scopedBranchId);
            if ($branch) {
                return $branch;
            }
        }

        return Branch::where('facility_type', 'like', '%Restaurant%')
            ->orWhere('name', 'like', '%Restaurant%')
            ->first() ?? Branch::first();
    }
}

<?php

namespace App\Http\Controllers\Restaurant;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\ItemCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantCategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);
        $businessUnit = $branch->businessUnit ?? BusinessUnit::first();

        $categories = ItemCategory::where('business_unit_id', $businessUnit->id)
            ->withCount('items')
            ->orderBy('name')
            ->get();

        return view('restaurant.menu.categories', compact('categories', 'branch', 'businessUnit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'business_unit_id' => 'required|exists:business_units,id',
        ]);

        ItemCategory::create([
            'business_unit_id' => $request->business_unit_id,
            'name' => $request->name,
            'description' => $request->description,
            'status' => true,
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, ItemCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->has('status'),
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(ItemCategory $category)
    {
        if ($category->items()->count() > 0) {
            return back()->with('error', 'Cannot delete category because it has items attached. Please reassign the items first.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

    private function resolveActiveBranch($user, Request $request): Branch
    {
        if ($request->has('branch_id')) {
            $branch = Branch::find($request->input('branch_id'));
            if ($branch) {
                return $branch;
            }
        }

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

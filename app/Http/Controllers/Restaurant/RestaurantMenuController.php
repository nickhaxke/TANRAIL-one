<?php

namespace App\Http\Controllers\Restaurant;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\ItemCategory;
use App\Domains\Core\Models\StockBalance;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Core\Services\InventoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantMenuController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        $businessUnit = $branch->businessUnit ?? BusinessUnit::first();

        if ($businessUnit) {
            app(ContextManager::class)->setActiveBusinessUnit($businessUnit);
        }
        app(ContextManager::class)->setActiveBranch($branch);

        $itemsQuery = Item::withoutGlobalScopes()->where('can_be_sold', true);
        if ($businessUnit) {
            $itemsQuery->where('business_unit_id', $businessUnit->id);
        }

        $search = $request->query('search');
        if ($search) {
            $itemsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categoryFilter = $request->query('category');
        if ($categoryFilter) {
            $itemsQuery->where('category_id', $categoryFilter);
        }

        $items = $itemsQuery->with('category')->orderBy('name')->paginate(20)->withQueryString();

        $location = InventoryLocation::withoutGlobalScopes()->find($branch->default_sales_location_id);
        $stockBalances = [];
        if ($location) {
            $stockBalances = StockBalance::withoutGlobalScopes()
                ->where('inventory_location_id', $location->id)
                ->pluck('quantity', 'item_id')
                ->toArray();
        }

        $businessUnit = $branch->businessUnit ?? BusinessUnit::first();
        $categories = ItemCategory::where('business_unit_id', $businessUnit->id)->orderBy('name')->get();

        $units = \App\Domains\Core\Models\Unit::all();
        return view('restaurant.menu.index', compact('items', 'user', 'branch', 'stockBalances', 'search', 'categories', 'units'));
    }

    public function toggleStatus(int $itemId)
    {
        $item = Item::withoutGlobalScopes()->findOrFail($itemId);
        $item->update(['status' => ! $item->status]);

        return back()->with('success', "Item {$item->name} status updated to ".($item->status ? 'Available' : 'Sold Out').'.');
    }

    public function restock(Request $request, int $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $item = Item::withoutGlobalScopes()->findOrFail($itemId);
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        if ($branch->businessUnit) {
            app(ContextManager::class)->setActiveBusinessUnit($branch->businessUnit);
        }
        app(ContextManager::class)->setActiveBranch($branch);

        $location = InventoryLocation::withoutGlobalScopes()->find($branch->default_sales_location_id);

        if (! $location) {
            return back()->with('error', "No active kitchen inventory location configured for station {$branch->name}.");
        }

        if ($branch->businessUnit) {
            app(ContextManager::class)->setActiveBusinessUnit($branch->businessUnit);
        }
        app(ContextManager::class)->setActiveBranch($branch);

        $inventoryService = app(InventoryService::class);
        $qty = (float) $validated['quantity'];

        $inventoryService->receive(
            $item,
            $location,
            $qty,
            'restock',
            null,
            Auth::id()
        );

        return back()->with('success', "Successfully restocked +{$qty} portions/units for {$item->name}.");
    }

    public function search(Request $request)
    {
        $query = $request->query('query');
        if (!$query) {
            return response()->json([]);
        }

        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);
        $businessUnit = $branch->businessUnit ?? BusinessUnit::first();

        $items = Item::withoutGlobalScopes()
            ->where('business_unit_id', $businessUnit->id)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'sku', 'base_price', 'standard_cost', 'category_id', 'can_be_sold', 'can_be_purchased', 'track_inventory']);

        // Attach stock balances for context in UI
        $location = InventoryLocation::withoutGlobalScopes()->find($branch->default_sales_location_id);
        
        if ($location) {
            $itemIds = $items->pluck('id')->toArray();
            $balances = StockBalance::withoutGlobalScopes()
                ->where('inventory_location_id', $location->id)
                ->whereIn('item_id', $itemIds)
                ->pluck('quantity', 'item_id')
                ->toArray();
                
            $items->each(function($item) use ($balances) {
                $item->stock = $balances[$item->id] ?? 0;
            });
        }

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $request->validate([
            'existing_item_id' => 'nullable|exists:items,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:items,sku,' . $request->input('existing_item_id'),
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'standard_cost' => 'required|numeric|min:0',
            'category_id' => 'required|exists:item_categories,id',
            'type' => 'required|in:physical,service,package',
            'unit_id' => 'required_without:existing_item_id|exists:units,id',
        ]);

        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);
        $businessUnit = $branch->businessUnit ?? BusinessUnit::first();

        if ($request->filled('existing_item_id')) {
            $item = Item::withoutGlobalScopes()->findOrFail($request->input('existing_item_id'));
            $item->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'sku' => $request->sku,
                'description' => $request->description,
                'base_price' => $request->base_price,
                'standard_cost' => $request->standard_cost,
                'type' => $request->type,
                'track_inventory' => $request->has('track_inventory'),
                'can_be_sold' => $request->has('can_be_sold'),
                'can_be_purchased' => $request->has('can_be_purchased'),
            ]);
            return back()->with('success', 'Existing item successfully configured as product.');
        }

        Item::create([
            'business_unit_id' => $businessUnit->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $request->sku,
            'description' => $request->description,
            'base_price' => $request->base_price,
            'standard_cost' => $request->standard_cost,
            'type' => $request->type,
            'track_inventory' => $request->has('track_inventory'),
            'can_be_sold' => $request->has('can_be_sold'),
            'can_be_purchased' => $request->has('can_be_purchased'),
            'status' => true,
            'unit_id' => $request->unit_id,
        ]);

        return back()->with('success', 'New product created successfully.');
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'standard_cost' => 'required|numeric|min:0',
            'category_id' => 'required|exists:item_categories,id',
            'type' => 'required|in:physical,service,package',
        ]);

        $item->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'base_price' => $request->base_price,
            'standard_cost' => $request->standard_cost,
            'type' => $request->type,
            'can_be_sold' => $request->has('can_be_sold'),
            'can_be_purchased' => $request->has('can_be_purchased'),
        ]);

        return back()->with('success', 'Product updated successfully.');
    }

    public function destroy(Item $item)
    {
        if ($item->stockBalances()->sum('quantity') > 0) {
            return back()->with('error', 'Cannot delete product because it currently has stock. Please adjust stock to 0 first.');
        }

        $item->delete();

        return back()->with('success', 'Product deleted successfully.');
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

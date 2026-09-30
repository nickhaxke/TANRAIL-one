<?php

namespace App\Http\Controllers\Restaurant;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\StockBalance;
use App\Domains\Core\Models\StockMovement;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Core\Services\InventoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RestaurantStoreController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        if ($branch->businessUnit) {
            app(ContextManager::class)->setActiveBusinessUnit($branch->businessUnit);
        }
        app(ContextManager::class)->setActiveBranch($branch);

        $location = InventoryLocation::withoutGlobalScopes()->find($branch->default_sales_location_id);

        $items = Item::withoutGlobalScopes()
            ->where('business_unit_id', $branch->business_unit_id)
            ->where('status', true)
            ->where('track_inventory', true)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        // Stock balances mapping
        $balances = [];
        if ($location) {
            $rawBalances = StockBalance::withoutGlobalScopes()
                ->where('inventory_location_id', $location->id)
                ->get()
                ->keyBy('item_id');

            foreach ($items as $item) {
                $b = $rawBalances->get($item->id);
                $qty = $b ? (float) $b->quantity : 0.0;
                $balances[$item->id] = [
                    'quantity' => $qty,
                    'reserved' => $b ? (float) $b->reserved_quantity : 0.0,
                    'available' => $b ? (float) ($b->quantity - $b->reserved_quantity) : 0.0,
                    'valuation' => $qty * (float) $item->standard_cost,
                ];
            }
        }

        $totalValuation = array_sum(array_column($balances, 'valuation'));
        $lowStockCount = 0;
        $outOfStockCount = 0;

        foreach ($items as $item) {
            $qty = $balances[$item->id]['quantity'] ?? 0;
            if ($qty <= 0) {
                $outOfStockCount++;
            } elseif ($qty <= 10) {
                $lowStockCount++;
            }
        }

        // Recent stock movements
        $recentMovements = collect();
        if ($location) {
            $recentMovements = StockMovement::withoutGlobalScopes()
                ->where(function ($q) use ($location) {
                    $q->where('source_location_id', $location->id)
                        ->orWhere('destination_location_id', $location->id);
                })
                ->with(['item' => fn ($q) => $q->withoutGlobalScopes(), 'user'])
                ->orderBy('created_at', 'desc')
                ->take(30)
                ->get();
        }

        $allBranches = Branch::where('status', true)->get();

        return view('restaurant.store.index', compact(
            'branch',
            'location',
            'items',
            'balances',
            'totalValuation',
            'lowStockCount',
            'outOfStockCount',
            'recentMovements',
            'allBranches'
        ));
    }

    public function receive(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.1',
            'supplier_name' => 'nullable|string|max:255',
            'delivery_note' => 'nullable|string|max:100',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);

        $branch = Branch::with('businessUnit')->findOrFail($validated['branch_id']);
        if ($branch->businessUnit) {
            app(ContextManager::class)->setActiveBusinessUnit($branch->businessUnit);
        }
        app(ContextManager::class)->setActiveBranch($branch);

        $location = InventoryLocation::withoutGlobalScopes()->find($branch->default_sales_location_id);
        if (! $location) {
            return redirect()->back()->with('error', 'Default store inventory location not configured for this station.');
        }

        $item = Item::withoutGlobalScopes()->findOrFail($validated['item_id']);

        $inventoryService = app(InventoryService::class);
        try {
            $inventoryService->receive(
                $item,
                $location,
                (float) $validated['quantity'],
                'store_receipt',
                null,
                Auth::id()
            );

            if ($request->filled('unit_cost') && (float) $validated['unit_cost'] > 0) {
                $item->standard_cost = $validated['unit_cost'];
                $item->save();
            }

            return redirect()->back()->with('success', "Successfully received {$validated['quantity']} {$item->sku} into {$location->name}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Store Receive Failed: '.$e->getMessage());
        }
    }

    public function wastage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.1',
            'reason' => 'required|string|max:100',
            'notes' => 'nullable|string|max:255',
        ]);

        $branch = Branch::with('businessUnit')->findOrFail($validated['branch_id']);
        if ($branch->businessUnit) {
            app(ContextManager::class)->setActiveBusinessUnit($branch->businessUnit);
        }
        app(ContextManager::class)->setActiveBranch($branch);

        $location = InventoryLocation::withoutGlobalScopes()->find($branch->default_sales_location_id);
        if (! $location) {
            return redirect()->back()->with('error', 'Default store inventory location not found.');
        }

        $item = Item::withoutGlobalScopes()->findOrFail($validated['item_id']);

        $inventoryService = app(InventoryService::class);
        try {
            $reasonNote = 'Wastage: '.$validated['reason'].($request->filled('notes') ? ' - '.$validated['notes'] : '');
            $inventoryService->adjust(
                $item,
                $location,
                -((float) $validated['quantity']),
                $reasonNote,
                Auth::id()
            );

            $lossValue = (float) $validated['quantity'] * (float) $item->standard_cost;

            return redirect()->back()->with('success', "Wastage/Spoilage written off: {$validated['quantity']} {$item->name} (Estimated loss value: TZS ".number_format($lossValue, 2).').');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Wastage Write-Off Failed: '.$e->getMessage());
        }
    }

    protected function resolveActiveBranch($user, Request $request): Branch
    {
        if ($request->filled('branch_id')) {
            $b = Branch::find($request->input('branch_id'));
            if ($b) {
                return $b;
            }
        }

        if ($user->branch_id) {
            $b = Branch::find($user->branch_id);
            if ($b) {
                return $b;
            }
        }

        return Branch::first() ?? Branch::create([
            'business_unit_id' => 1,
            'name' => 'Morogoro Station Dining Outlet',
            'code' => 'BR-MOR-01',
            'city' => 'Morogoro',
            'status' => true,
        ]);
    }
}

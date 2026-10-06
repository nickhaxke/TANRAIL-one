<?php

namespace App\Http\Controllers\Cleaning;

use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\StockBalance;
use App\Domains\Core\Models\StockMovement;
use App\Domains\Core\Services\ContextManager;
use App\Domains\Core\Services\InventoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CleaningStoreController extends Controller
{
    private function getCentralStore(int $buId): ?InventoryLocation
    {
        return InventoryLocation::where('code', 'LOC-CLN-CENTRAL')
            ->whereHas('branch', fn ($q) => $q->where('business_unit_id', $buId))
            ->first() ?? InventoryLocation::whereHas('branch', fn ($q) => $q->where('business_unit_id', $buId))->first();
    }

    public function index(ContextManager $context)
    {
        if (! Auth::user()->can('cleaning.store.view')) {
            abort(403, 'Unauthorized access to Central Cleaning Store.');
        }

        $buId = $context->getActiveBusinessUnitId();
        $centralStore = $this->getCentralStore($buId);

        $locations = $centralStore ? collect([$centralStore]) : collect();
        $locationIds = $locations->pluck('id');

        $items = Item::where('business_unit_id', $buId)
            ->where('status', true)
            ->where('track_inventory', true)
            ->get();

        $balances = StockBalance::whereIn('inventory_location_id', $locationIds)
            ->get()
            ->groupBy('item_id');

        $summary = [];
        foreach ($items as $item) {
            $itemBalances = $balances->get($item->id, collect());
            $quantity = $itemBalances->sum('quantity');
            $summary[$item->id] = [
                'item' => $item,
                'quantity' => $quantity,
                'value' => $quantity * $item->standard_cost,
            ];
        }

        return view('cleaning.store.index', compact('summary', 'locations', 'centralStore'));
    }

    public function receiveForm(ContextManager $context)
    {
        if (! Auth::user()->can('cleaning.store.receive')) {
            abort(403, 'Unauthorized. Only Store Keepers can receive stock into the Central Cleaning Store.');
        }

        $buId = $context->getActiveBusinessUnitId();
        $centralStore = $this->getCentralStore($buId);
        $items = Item::where('business_unit_id', $buId)->where('status', true)->get();
        $locations = $centralStore ? collect([$centralStore]) : collect();

        return view('cleaning.store.receive', compact('items', 'locations', 'centralStore'));
    }

    public function receive(Request $request, ContextManager $context)
    {
        if (! Auth::user()->can('cleaning.store.receive')) {
            abort(403, 'Unauthorized. Only Store Keepers can receive stock into the Central Cleaning Store.');
        }

        $validated = $request->validate([
            'inventory_location_id' => 'required|exists:inventory_locations,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.1',
        ]);

        $buId = $context->getActiveBusinessUnitId();

        $location = InventoryLocation::with('branch')->findOrFail($validated['inventory_location_id']);
        if ($location->branch->business_unit_id != $buId) {
            return back()->with('error', 'Unauthorized location.');
        }

        $item = Item::findOrFail($validated['item_id']);
        if ($item->business_unit_id != $buId) {
            return back()->with('error', 'Unauthorized item.');
        }

        try {
            app(InventoryService::class)->receive(
                $item,
                $location,
                (float) $validated['quantity'],
                'cleaning_store_receipt',
                null,
                Auth::id()
            );

            return redirect()->route('cleaning.store.index')->with('success', 'Materials received successfully into Central Store.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function issueForm(ContextManager $context)
    {
        if (! Auth::user()->can('cleaning.store.issue')) {
            abort(403, 'Unauthorized. Only Store Keepers can issue stock from the Central Cleaning Store.');
        }

        $buId = $context->getActiveBusinessUnitId();
        $centralStore = $this->getCentralStore($buId);
        $items = Item::where('business_unit_id', $buId)->where('status', true)->get();
        $locations = $centralStore ? collect([$centralStore]) : collect();

        return view('cleaning.store.issue', compact('items', 'locations', 'centralStore'));
    }

    public function issue(Request $request, ContextManager $context)
    {
        if (! Auth::user()->can('cleaning.store.issue')) {
            abort(403, 'Unauthorized. Only Store Keepers can issue stock from the Central Cleaning Store.');
        }

        $validated = $request->validate([
            'inventory_location_id' => 'required|exists:inventory_locations,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.1',
        ]);

        $buId = $context->getActiveBusinessUnitId();

        $location = InventoryLocation::with('branch')->findOrFail($validated['inventory_location_id']);
        if ($location->branch->business_unit_id != $buId) {
            return back()->with('error', 'Unauthorized location.');
        }

        $item = Item::findOrFail($validated['item_id']);
        if ($item->business_unit_id != $buId) {
            return back()->with('error', 'Unauthorized item.');
        }

        try {
            app(InventoryService::class)->issue(
                $item,
                $location,
                (float) $validated['quantity'],
                'cleaning_store_issue',
                null,
                Auth::id()
            );

            return redirect()->route('cleaning.store.index')->with('success', 'Materials issued successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function movements(ContextManager $context)
    {
        if (! Auth::user()->can('cleaning.store.view')) {
            abort(403, 'Unauthorized access to stock movements.');
        }

        $buId = $context->getActiveBusinessUnitId();

        $locationIds = InventoryLocation::whereHas('branch', function ($q) use ($buId) {
            $q->where('business_unit_id', $buId);
        })->pluck('id');

        $movements = StockMovement::with(['item', 'user', 'sourceLocation', 'destinationLocation'])
            ->where(function ($q) use ($locationIds) {
                $q->whereIn('source_location_id', $locationIds)
                    ->orWhereIn('destination_location_id', $locationIds);
            })
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('cleaning.store.movements', compact('movements'));
    }
}

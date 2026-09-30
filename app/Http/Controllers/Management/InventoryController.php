<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\StockMovement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $orgId = $request->input('organization_id');
        $buId = $request->input('business_unit_id');

        $organizations = Organization::all();
        $businessUnits = $orgId ? BusinessUnit::where('organization_id', $orgId)->get() : BusinessUnit::all();

        // Stock Balance Query
        $stockQuery = DB::table('stock_balances')
            ->join('items', 'stock_balances.item_id', '=', 'items.id')
            ->join('inventory_locations', 'stock_balances.inventory_location_id', '=', 'inventory_locations.id');

        if ($buId) {
            $stockQuery->where('inventory_locations.business_unit_id', $buId);
        } elseif ($orgId) {
            $stockQuery->where('inventory_locations.organization_id', $orgId);
        }

        // 1. Total Inventory Value (Qty * Standard Cost)
        $totalInventoryValue = (clone $stockQuery)
            ->selectRaw('SUM(stock_balances.quantity * items.standard_cost) as total_value')
            ->value('total_value') ?? 0;

        // 2. Low Stock Items (Quantity < 10 for simplicity, ideally would use a reorder_level column if exists)
        $lowStockCount = (clone $stockQuery)
            ->where('stock_balances.quantity', '<', 10)
            ->where('stock_balances.quantity', '>', 0)
            ->count();

        // 3. Stock by Location
        $stockByLocation = (clone $stockQuery)
            ->select(
                'inventory_locations.name as location_name',
                DB::raw('COUNT(stock_balances.item_id) as items_count'),
                DB::raw('SUM(stock_balances.quantity * items.standard_cost) as total_value')
            )
            ->groupBy('inventory_locations.id', 'inventory_locations.name')
            ->orderByDesc('total_value')
            ->get();

        // 4. Recent Stock Movements
        $movementsQuery = StockMovement::with(['item', 'sourceLocation', 'destinationLocation'])
            ->orderByDesc('created_at')
            ->limit(10);

        if ($buId) {
            $movementsQuery->where(function ($q) use ($buId) {
                $q->whereHas('sourceLocation', fn($sq) => $sq->where('business_unit_id', $buId))
                  ->orWhereHas('destinationLocation', fn($sq) => $sq->where('business_unit_id', $buId));
            });
        } elseif ($orgId) {
            $movementsQuery->where(function ($q) use ($orgId) {
                $q->whereHas('sourceLocation', fn($sq) => $sq->where('organization_id', $orgId))
                  ->orWhereHas('destinationLocation', fn($sq) => $sq->where('organization_id', $orgId));
            });
        }

        $recentMovements = $movementsQuery->get();

        return view('management.inventory.index', compact(
            'organizations', 'businessUnits',
            'orgId', 'buId',
            'totalInventoryValue', 'lowStockCount',
            'stockByLocation', 'recentMovements'
        ));
    }
}

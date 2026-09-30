<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Modules\Production\Models\ProductionOrder;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $orgId = $request->input('organization_id');
        $buId = $request->input('business_unit_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $organizations = Organization::all();
        $businessUnits = $orgId ? BusinessUnit::where('organization_id', $orgId)->get() : BusinessUnit::all();

        $poQuery = ProductionOrder::whereBetween('created_at', [$startDate, $endDate]);

        if ($buId) {
            $poQuery->where('business_unit_id', $buId);
        } elseif ($orgId) {
            $poQuery->where('organization_id', $orgId);
        }

        // KPIs
        $totalOrders = (clone $poQuery)->count();
        $completedOrders = (clone $poQuery)->where('status', 'completed')->count();

        $totalPlannedQty = (clone $poQuery)->sum('target_quantity');
        $totalActualQty = (clone $poQuery)->sum('actual_quantity');

        // Efficiency (Actual / Planned)
        $efficiency = $totalPlannedQty > 0 ? ($totalActualQty / $totalPlannedQty) * 100 : 0;

        // Recent Production Orders
        $recentOrders = (clone $poQuery)->with(['finishedItem', 'businessUnit'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Top Consumed Materials (Raw Material Consumption)
        $topMaterialsQuery = DB::table('production_order_items')
            ->join('production_orders', 'production_order_items.production_order_id', '=', 'production_orders.id')
            ->join('items', 'production_order_items.ingredient_item_id', '=', 'items.id')
            ->whereBetween('production_orders.created_at', [$startDate, $endDate]);

        if ($buId) {
            $topMaterialsQuery->where('production_orders.business_unit_id', $buId);
        } elseif ($orgId) {
            $topMaterialsQuery->where('production_orders.organization_id', $orgId);
        }

        $topMaterials = $topMaterialsQuery->select(
            'items.name',
            DB::raw('SUM(production_order_items.required_quantity) as total_planned'),
            DB::raw('SUM(production_order_items.actual_quantity) as total_actual')
        )
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_actual')
            ->limit(5)
            ->get();

        return view('management.production.index', compact(
            'organizations', 'businessUnits',
            'orgId', 'buId', 'startDate', 'endDate',
            'totalOrders', 'completedOrders',
            'totalPlannedQty', 'totalActualQty', 'efficiency',
            'recentOrders', 'topMaterials'
        ));
    }
}

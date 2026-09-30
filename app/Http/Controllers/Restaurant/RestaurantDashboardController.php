<?php

namespace App\Http\Controllers\Restaurant;

use App\Domains\Core\Enums\OrderStatus;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Order;
use App\Domains\Core\Models\OrderLine;
use App\Domains\Core\Models\StockBalance;
use App\Domains\Modules\Restaurant\Models\RestaurantExpense;
use App\Domains\Modules\Restaurant\Models\RestaurantShift;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RestaurantDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);
        $allBranches = Branch::where('status', true)->get();

        // Today's revenue & order volume
        $todayOrders = Order::withoutGlobalScopes()
            ->where('branch_id', $branch->id)
            ->whereDate('created_at', today())
            ->where('status', '!=', OrderStatus::CANCELLED);

        $todayRevenue = (float) (clone $todayOrders)->sum('total');
        $todayOrdersCount = (clone $todayOrders)->count();
        $avgOrderValue = $todayOrdersCount > 0 ? ($todayRevenue / $todayOrdersCount) : 0;

        // Cost of Goods Sold (COGS) & Profit Margins
        $todayCogs = (float) OrderLine::whereHas('order', function ($q) use ($branch) {
            $q->withoutGlobalScopes()
                ->where('branch_id', $branch->id)
                ->whereDate('created_at', today())
                ->where('status', '!=', OrderStatus::CANCELLED);
        })
            ->join('items', 'order_lines.item_id', '=', 'items.id')
            ->sum(DB::raw('order_lines.quantity * items.standard_cost'));

        $grossProfit = $todayRevenue - $todayCogs;
        $grossMarginPercent = $todayRevenue > 0 ? round(($grossProfit / $todayRevenue) * 100, 1) : 0;
        $foodCostPercent = $todayRevenue > 0 ? round(($todayCogs / $todayRevenue) * 100, 1) : 0;

        // Daily Operating Expenses & Net Operating Profit
        $todayExpenses = (float) RestaurantExpense::where('branch_id', $branch->id)
            ->whereDate('expense_date', today())
            ->sum('amount');

        $netProfit = $grossProfit - $todayExpenses;
        $netMarginPercent = $todayRevenue > 0 ? round(($netProfit / $todayRevenue) * 100, 1) : 0;

        // Active Cashier Shifts at this branch
        $openShifts = RestaurantShift::where('branch_id', $branch->id)
            ->where('status', 'open')
            ->with('user')
            ->get();

        // Top 5 selling items today
        $topItems = OrderLine::whereHas('order', function ($q) use ($branch) {
            $q->withoutGlobalScopes()
                ->where('branch_id', $branch->id)
                ->whereDate('created_at', today())
                ->where('status', '!=', OrderStatus::CANCELLED);
        })
            ->with(['item' => fn ($q) => $q->withoutGlobalScopes()])
            ->select('item_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as total_amount'))
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Low stock alerts for active restaurant store
        $location = InventoryLocation::withoutGlobalScopes()->find($branch->default_sales_location_id);
        $lowStockItems = collect();
        $totalStockValuation = 0;

        if ($location) {
            $lowStockItems = StockBalance::withoutGlobalScopes()
                ->where('inventory_location_id', $location->id)
                ->with(['item' => fn ($q) => $q->withoutGlobalScopes()])
                ->where('quantity', '<=', 15)
                ->orderBy('quantity')
                ->get();

            $totalStockValuation = (float) StockBalance::withoutGlobalScopes()
                ->where('inventory_location_id', $location->id)
                ->join('items', 'stock_balances.item_id', '=', 'items.id')
                ->sum(DB::raw('stock_balances.quantity * items.standard_cost'));
        }

        // Recent 10 orders
        $recentOrders = Order::withoutGlobalScopes()
            ->with(['lines.item' => fn ($q) => $q->withoutGlobalScopes(), 'createdBy'])
            ->where('branch_id', $branch->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('restaurant.dashboard.index', compact(
            'branch',
            'allBranches',
            'user',
            'todayRevenue',
            'todayOrdersCount',
            'avgOrderValue',
            'todayCogs',
            'grossProfit',
            'grossMarginPercent',
            'todayExpenses',
            'netProfit',
            'netMarginPercent',
            'openShifts',
            'foodCostPercent',
            'lowStockItems',
            'totalStockValuation',
            'topItems',
            'recentOrders'
        ));
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

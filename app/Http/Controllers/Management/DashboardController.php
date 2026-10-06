<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Enums\OrderStatus;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\Order;
use App\Domains\Core\Models\OrderLine;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\Role;
use App\Domains\Core\Models\SupplierInvoice;
use App\Domains\Core\Models\User;
use App\Domains\Modules\Restaurant\Models\RestaurantExpense;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $organization = Organization::with([
            'businessUnits.managerUser',
            'businessUnits.branches.managerUser',
        ])->first();

        if (! $organization) {
            $organization = Organization::create([
                'name' => 'TANRAIL Investments Limited',
                'code' => 'TANRAIL',
                'trading_name' => 'TANRAIL',
                'status' => true,
                'country' => 'Tanzania',
                'city' => 'Dar es Salaam',
                'tin_number' => '108-342-880',
                'registration_number' => '154872-TZ',
            ]);
        }

        // Global Filters
        $orgId = $request->input('organization_id');
        $buId = $request->input('business_unit_id');

        $organizations = Organization::all();
        $businessUnits = $organization->businessUnits()->with(['branches', 'managerUser'])->get();
        $branches = Branch::whereIn('business_unit_id', $businessUnits->pluck('id'))->with(['businessUnit', 'managerUser'])->latest()->get();

        // Administrative Metrics
        $totalBusinessUnits = $businessUnits->count();
        $activeBusinessUnits = $businessUnits->where('status', true)->count();
        $totalBranches = $branches->count();
        $activeBranches = $branches->where('status', true)->count();
        $totalStaff = User::count();
        $totalRoles = Role::count();

        $regionsCovered = Branch::whereNotNull('city')->where('city', '!=', '')->pluck('city')->unique()->count();
        if ($regionsCovered === 0 && $organization->city) {
            $regionsCovered = 1;
        }

        // Units and Branches with designated managers
        $unitsWithManagers = $businessUnits->whereNotNull('manager_user_id')->count();
        $branchesWithSupervisors = $branches->whereNotNull('manager_user_id')->count();

        $totalGovernanceNodes = $totalBusinessUnits + $totalBranches;
        $governedNodes = $unitsWithManagers + $branchesWithSupervisors;
        $governanceReadinessPercent = $totalGovernanceNodes > 0
            ? (int) round(($governedNodes / $totalGovernanceNodes) * 100)
            : 100;

        // Recent key operational branches
        $recentBranches = Branch::with(['businessUnit', 'managerUser'])->latest()->take(6)->get();

        // Recent onboarded personnel
        $recentStaff = User::with(['roles'])->latest()->take(5)->get();

        // ERM Business Operations Oversight (Enterprise Monitoring & Governance)
        $totalProcurementCommitted = (float) PurchaseOrder::whereIn('status', ['approved', 'partial_received'])->sum('total');
        $pendingApprovalsCount = PurchaseOrder::where('status', 'submitted')->count()
            + SupplierInvoice::where('status', 'draft')->count();

        $totalInventoryValue = (float) (DB::table('stock_balances')
            ->join('items', 'stock_balances.item_id', '=', 'items.id')
            ->sum(DB::raw('stock_balances.quantity * items.standard_cost')) ?? 0);

        // Station Restaurant Live Revenue & Margin
        $todayDate = now()->format('Y-m-d');
        $todayRestaurantOrders = Order::withoutGlobalScopes()
            ->where('created_at', 'like', $todayDate.'%')
            ->where('status', '!=', OrderStatus::CANCELLED->value);
        $todayRestaurantRevenue = (float) (clone $todayRestaurantOrders)->sum('total');
        $todayRestaurantOrdersCount = (clone $todayRestaurantOrders)->count();

        $todayRestaurantCogs = (float) OrderLine::whereHas('order', function ($q) use ($todayDate) {
            $q->withoutGlobalScopes()
                ->where('created_at', 'like', $todayDate.'%')
                ->where('status', '!=', OrderStatus::CANCELLED->value);
        })
            ->join('items', 'order_lines.item_id', '=', 'items.id')
            ->sum(DB::raw('order_lines.quantity * items.standard_cost'));

        $todayRestaurantGrossProfit = max(0, $todayRestaurantRevenue - $todayRestaurantCogs);
        $todayRestaurantMarginPercent = $todayRestaurantRevenue > 0
            ? round(($todayRestaurantGrossProfit / $todayRestaurantRevenue) * 100, 1)
            : 0;

        $todayRestaurantExpenses = (float) RestaurantExpense::whereDate('expense_date', $todayDate)->sum('amount');
        $todayRestaurantNetProfit = $todayRestaurantGrossProfit - $todayRestaurantExpenses;
        $todayRestaurantNetMarginPercent = $todayRestaurantRevenue > 0
            ? round(($todayRestaurantNetProfit / $todayRestaurantRevenue) * 100, 1)
            : 0;

        return view('management.dashboard', compact(
            'organization',
            'organizations',
            'businessUnits',
            'branches',
            'orgId',
            'buId',
            'totalBusinessUnits',
            'activeBusinessUnits',
            'totalBranches',
            'activeBranches',
            'totalStaff',
            'totalRoles',
            'regionsCovered',
            'unitsWithManagers',
            'branchesWithSupervisors',
            'governanceReadinessPercent',
            'recentBranches',
            'recentStaff',
            'totalProcurementCommitted',
            'pendingApprovalsCount',
            'totalInventoryValue',
            'todayRestaurantRevenue',
            'todayRestaurantOrdersCount',
            'todayRestaurantGrossProfit',
            'todayRestaurantMarginPercent',
            'todayRestaurantExpenses',
            'todayRestaurantNetProfit',
            'todayRestaurantNetMarginPercent'
        ));
    }
}

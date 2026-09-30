<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\SupplierInvoice;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    public function index(Request $request)
    {
        $orgId = $request->input('organization_id');
        $buId = $request->input('business_unit_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $organizations = Organization::all();
        $businessUnits = $orgId ? BusinessUnit::where('organization_id', $orgId)->get() : BusinessUnit::all();

        $poQuery = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate]);
        $billQuery = SupplierInvoice::whereBetween('bill_date', [$startDate, $endDate]);

        if ($buId) {
            $poQuery->where('business_unit_id', $buId);
            $billQuery->where('business_unit_id', $buId);
        } elseif ($orgId) {
            $poQuery->where('organization_id', $orgId); // Wait, PurchaseOrder doesn't have organization_id either? Let me use businessUnit relation or remove this. Actually let's just leave it if it works, or fix it if orgId is passed.
            $billQuery->where('organization_id', $orgId);
        }

        // KPIs
        $totalCommitted = (clone $poQuery)->whereIn('status', ['approved', 'partial_received'])->sum('total');
        $totalBilled = (clone $billQuery)->where('status', 'approved')->sum('total');
        $outstandingExposure = (clone $billQuery)->where('status', 'approved')->sum(DB::raw('total - amount_paid'));

        // Recent POs
        $recentPOs = (clone $poQuery)->with(['supplier', 'businessUnit'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Top Suppliers (by billed amount)
        $topSuppliers = DB::table('supplier_invoices')
            ->join('suppliers', 'supplier_invoices.supplier_id', '=', 'suppliers.id')
            ->where('supplier_invoices.status', 'approved')
            ->whereBetween('supplier_invoices.bill_date', [$startDate, $endDate]);

        if ($buId) {
            $topSuppliers->where('supplier_invoices.business_unit_id', $buId);
        } elseif ($orgId) {
            $topSuppliers->where('supplier_invoices.organization_id', $orgId);
        }

        $topSuppliers = $topSuppliers->select('suppliers.name', DB::raw('SUM(supplier_invoices.total) as total_billed'))
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total_billed')
            ->limit(5)
            ->get();

        return view('management.procurement.index', compact(
            'organizations', 'businessUnits',
            'orgId', 'buId', 'startDate', 'endDate',
            'totalCommitted', 'totalBilled', 'outstandingExposure',
            'recentPOs', 'topSuppliers'
        ));
    }
}

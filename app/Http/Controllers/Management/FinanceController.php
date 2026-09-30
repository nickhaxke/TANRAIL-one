<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Enums\AccountType;
use App\Domains\Core\Models\Account;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $orgId = $request->input('organization_id');
        $buId = $request->input('business_unit_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $organizations = Organization::all();
        $businessUnits = $orgId ? BusinessUnit::where('organization_id', $orgId)->get() : BusinessUnit::all();

        // Income Statement summary (Revenue vs Expenses)
        $revenueAccounts = Account::where('type', AccountType::REVENUE)->pluck('id');
        $expenseAccounts = Account::where('type', AccountType::EXPENSE)->pluck('id');
        $assetAccounts = Account::where('type', AccountType::ASSET)->pluck('id');
        $liabilityAccounts = Account::where('type', AccountType::LIABILITY)->pluck('id');

        // Note: Real trial balance needs join with journals for dates and BU filtering
        $queryBalances = function ($accountIds) use ($buId, $startDate, $endDate) {
            $q = DB::table('journal_entries')
                ->join('journals', 'journal_entries.journal_id', '=', 'journals.id')
                ->whereIn('journal_entries.account_id', $accountIds)
                ->whereBetween('journals.posting_date', [$startDate, $endDate]);

            if ($buId) {
                $q->where('journals.business_unit_id', $buId);
            }

            return $q->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')->first();
        };

        $revBalances = $queryBalances($revenueAccounts);
        $totalRevenue = ($revBalances->total_credit ?? 0) - ($revBalances->total_debit ?? 0);

        $expBalances = $queryBalances($expenseAccounts);
        $totalExpenses = ($expBalances->total_debit ?? 0) - ($expBalances->total_credit ?? 0);

        $netIncome = $totalRevenue - $totalExpenses;

        // Receivables and Payables detailed view metrics
        $arBalances = DB::table('invoices')
            ->where('status', 'issued')
            ->selectRaw('SUM(total) as total_invoiced, SUM(amount_paid) as total_paid')
            ->first();

        $apBalances = DB::table('supplier_invoices')
            ->where('status', 'approved')
            ->selectRaw('SUM(total) as total_invoiced, SUM(amount_paid) as total_paid')
            ->first();

        // Fetch top 5 revenue accounts
        $topRevenueAccounts = DB::table('journal_entries')
            ->join('journals', 'journal_entries.journal_id', '=', 'journals.id')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->where('accounts.type', AccountType::REVENUE)
            ->whereBetween('journals.posting_date', [$startDate, $endDate])
            ->groupBy('accounts.id', 'accounts.name', 'accounts.code')
            ->selectRaw('accounts.name, accounts.code, SUM(credit) - SUM(debit) as balance')
            ->orderByDesc('balance')
            ->limit(5)
            ->get();

        return view('management.finance.index', compact(
            'organizations', 'businessUnits',
            'orgId', 'buId', 'startDate', 'endDate',
            'totalRevenue', 'totalExpenses', 'netIncome',
            'arBalances', 'apBalances',
            'topRevenueAccounts'
        ));
    }
}

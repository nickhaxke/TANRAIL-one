<?php

namespace App\Http\Controllers\Restaurant;

use App\Domains\Core\Models\Branch;
use App\Domains\Modules\Restaurant\Models\RestaurantShift;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RestaurantShiftController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        $activeShift = RestaurantShift::where('branch_id', $branch->id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        $activeShiftStats = null;
        if ($activeShift) {
            $orders = $activeShift->orders()->withoutGlobalScopes()->with('payments')->get();
            $cashSales = 0;
            $cardSales = 0;
            $mobileSales = 0;

            foreach ($orders as $order) {
                foreach ($order->payments as $payment) {
                    $method = $payment->method instanceof \BackedEnum ? $payment->method->value : (string) $payment->method;
                    if ($method === 'cash') {
                        $cashSales += (float) $payment->amount;
                    } elseif ($method === 'card') {
                        $cardSales += (float) $payment->amount;
                    } elseif ($method === 'mobile_money') {
                        $mobileSales += (float) $payment->amount;
                    }
                }
            }

            $cashExpenses = (float) $activeShift->expenses()->where('payment_method', 'cash')->sum('amount');
            $expectedCash = (float) $activeShift->opening_float + $cashSales - $cashExpenses;

            $activeShiftStats = [
                'cash_sales' => $cashSales,
                'card_sales' => $cardSales,
                'mobile_sales' => $mobileSales,
                'gross_sales' => $cashSales + $cardSales + $mobileSales,
                'cash_expenses' => $cashExpenses,
                'expected_cash' => $expectedCash,
                'orders_count' => $orders->count(),
            ];
        }

        $shiftHistory = RestaurantShift::where('branch_id', $branch->id)
            ->with(['user'])
            ->orderBy('opened_at', 'desc')
            ->paginate(15);

        $allBranches = Branch::where('status', true)->get();

        return view('restaurant.shifts.index', compact(
            'branch',
            'activeShift',
            'activeShiftStats',
            'shiftHistory',
            'allBranches'
        ));
    }

    public function open(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'opening_float' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $existing = RestaurantShift::where('branch_id', $validated['branch_id'])
            ->where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You already have an active open shift at this branch.');
        }

        RestaurantShift::create([
            'branch_id' => $validated['branch_id'],
            'user_id' => Auth::id(),
            'opened_at' => now(),
            'status' => 'open',
            'opening_float' => $validated['opening_float'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Shift opened successfully! Starting float: TZS '.number_format($validated['opening_float'], 2));
    }

    public function close(Request $request, RestaurantShift $shift): RedirectResponse
    {
        if ($shift->status !== 'open') {
            return redirect()->back()->with('error', 'This shift is already closed.');
        }

        $validated = $request->validate([
            'closing_cash_counted' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $orders = $shift->orders()->withoutGlobalScopes()->with('payments')->get();
        $cashSales = 0;
        foreach ($orders as $order) {
            foreach ($order->payments as $payment) {
                $method = $payment->method instanceof \BackedEnum ? $payment->method->value : (string) $payment->method;
                if ($method === 'cash') {
                    $cashSales += (float) $payment->amount;
                }
            }
        }

        $cashExpenses = (float) $shift->expenses()->where('payment_method', 'cash')->sum('amount');
        $expectedCash = (float) $shift->opening_float + $cashSales - $cashExpenses;
        $countedCash = (float) $validated['closing_cash_counted'];
        $difference = $countedCash - $expectedCash;

        $totalSales = (float) $orders->sum('total');
        $totalExpenses = (float) $shift->expenses()->sum('amount');
        $totalOrdersCount = $orders->count();

        $shift->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closing_cash_counted' => $countedCash,
            'expected_cash' => $expectedCash,
            'cash_difference' => $difference,
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
            'total_orders_count' => $totalOrdersCount,
            'notes' => $validated['notes'] ?? $shift->notes,
        ]);

        $varianceText = $difference >= 0 ? '+TZS '.number_format($difference, 2) : '-TZS '.number_format(abs($difference), 2);

        return redirect()->back()->with('success', "Shift #{$shift->id} closed & Z-Report generated. Expected: TZS ".number_format($expectedCash, 2).', Counted: TZS '.number_format($countedCash, 2)." (Variance: {$varianceText})");
    }

    public function report(RestaurantShift $shift): View
    {
        $shift->load(['user', 'branch', 'orders' => fn ($q) => $q->withoutGlobalScopes()->with('payments', 'lines.item'), 'expenses']);

        $cashSales = 0;
        $cardSales = 0;
        $mobileSales = 0;

        foreach ($shift->orders as $order) {
            foreach ($order->payments as $payment) {
                $method = $payment->method instanceof \BackedEnum ? $payment->method->value : (string) $payment->method;
                if ($method === 'cash') {
                    $cashSales += (float) $payment->amount;
                } elseif ($method === 'card') {
                    $cardSales += (float) $payment->amount;
                } elseif ($method === 'mobile_money') {
                    $mobileSales += (float) $payment->amount;
                }
            }
        }

        return view('restaurant.shifts.report', compact('shift', 'cashSales', 'cardSales', 'mobileSales'));
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

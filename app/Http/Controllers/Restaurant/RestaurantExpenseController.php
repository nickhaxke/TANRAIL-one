<?php

namespace App\Http\Controllers\Restaurant;

use App\Domains\Core\Models\Branch;
use App\Domains\Modules\Restaurant\Models\RestaurantExpense;
use App\Domains\Modules\Restaurant\Models\RestaurantShift;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RestaurantExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $branch = $this->resolveActiveBranch($user, $request);

        $query = RestaurantExpense::where('branch_id', $branch->id)->with('user', 'shift');

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->input('date_to'));
        }

        $expenses = $query->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Summary metrics
        $allExpensesQuery = RestaurantExpense::where('branch_id', $branch->id);
        if ($request->filled('date_from')) {
            $allExpensesQuery->whereDate('expense_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $allExpensesQuery->whereDate('expense_date', '<=', $request->input('date_to'));
        }

        $totalExpenses = (float) $allExpensesQuery->sum('amount');
        $cashExpenses = (float) (clone $allExpensesQuery)->where('payment_method', 'cash')->sum('amount');
        $digitalExpenses = (float) (clone $allExpensesQuery)->where('payment_method', '!=', 'cash')->sum('amount');

        // Check active shift for current user
        $activeShift = RestaurantShift::where('branch_id', $branch->id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        $allBranches = Branch::where('status', true)->get();

        $categories = [
            'Cooking Gas & Fuel',
            'Fresh Produce & Vegetables',
            'Meat & Seafood',
            'Dairy & Bakery',
            'Spices & Dry Store',
            'Cleaning & Sanitation',
            'Packaging & Takeaway Materials',
            'Station Maintenance & Repairs',
            'Staff Meals & Welfare',
            'Petty Cash / Incidental',
        ];

        return view('restaurant.expenses.index', compact(
            'branch',
            'expenses',
            'totalExpenses',
            'cashExpenses',
            'digitalExpenses',
            'activeShift',
            'allBranches',
            'categories'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer',
            'paid_to' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $branch = Branch::with('businessUnit')->findOrFail($validated['branch_id']);

        // Check if there is an active shift for cashier if paid by cash
        $activeShift = null;
        if ($validated['payment_method'] === 'cash') {
            $activeShift = RestaurantShift::where('branch_id', $branch->id)
                ->where('user_id', Auth::id())
                ->where('status', 'open')
                ->latest()
                ->first();
        }

        $expense = RestaurantExpense::create([
            'branch_id' => $branch->id,
            'business_unit_id' => $branch->business_unit_id,
            'user_id' => Auth::id(),
            'shift_id' => $activeShift?->id,
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'payment_method' => $validated['payment_method'],
            'paid_to' => $validated['paid_to'] ?? null,
            'receipt_number' => $validated['receipt_number'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => 'approved',
        ]);

        if ($activeShift) {
            $activeShift->increment('total_expenses', $validated['amount']);
        }

        return redirect()->back()->with('success', 'Operating expense recorded successfully. TZS '.number_format($expense->amount, 2).' accounted.');
    }

    public function destroy(RestaurantExpense $expense): RedirectResponse
    {
        if ($expense->shift_id) {
            $shift = RestaurantShift::find($expense->shift_id);
            if ($shift && $shift->status === 'open') {
                $shift->decrement('total_expenses', min($shift->total_expenses, $expense->amount));
            }
        }

        $expense->delete();

        return redirect()->back()->with('success', 'Expense record deleted successfully.');
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

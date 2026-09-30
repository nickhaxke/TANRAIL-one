@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full">

    <!-- Header & Action Row -->
    <div class="mb-8 border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">Business Accountability</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">{{ $branch->name }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Operating Expenses</h1>
        </div>

        <button type="button" onclick="document.getElementById('recordExpenseModal').classList.remove('hidden')" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors flex items-center gap-2">
            Record Expense &rarr;
        </button>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="mb-6 p-4 border-2 border-green-600 bg-green-50 text-green-800 font-bold text-sm uppercase tracking-wider">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 border-2 border-red-600 bg-red-50 text-red-800 font-bold text-sm uppercase tracking-wider">
            {{ session('error') }}
        </div>
    @endif

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Operating Expenses Card -->
        <div class="bg-white border-2 border-gray-200 p-5 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-gray-900"></div>
            <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Total Station Expenses</div>
            <div class="text-3xl font-black text-gray-900">
                TZS {{ number_format($totalExpenses, 2) }}
            </div>
            <p class="text-[10px] uppercase font-bold text-gray-400 mt-2">Direct operating overhead</p>
        </div>

        <!-- Cash Drawer Deductions (Petty Cash) -->
        <div class="bg-white border-2 border-gray-200 p-5 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-yellow-600"></div>
            <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Petty Cash (Cash Drawer)</div>
            <div class="text-3xl font-black text-gray-900">
                TZS {{ number_format($cashExpenses, 2) }}
            </div>
            <p class="text-[10px] uppercase font-bold text-gray-400 mt-2">Deducted from cashier floats</p>
        </div>

        <!-- Digital & Bank Transfers -->
        <div class="bg-white border-2 border-gray-200 p-5 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-600"></div>
            <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Mobile / Bank</div>
            <div class="text-3xl font-black text-gray-900">
                TZS {{ number_format($digitalExpenses, 2) }}
            </div>
            <p class="text-[10px] uppercase font-bold text-gray-400 mt-2">Direct transfers</p>
        </div>
    </div>

    <!-- Filters & Table Section -->
    <div class="bg-white border-2 border-gray-200">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('restaurant.expenses') }}" class="p-4 border-b-2 border-gray-200 bg-gray-50 flex flex-wrap items-center gap-3">
            <input type="hidden" name="branch_id" value="{{ $branch->id }}">

            <div class="w-48">
                <select name="category" class="w-full bg-white border-2 border-gray-300 text-xs font-bold uppercase rounded-none px-3 py-2 text-gray-900 focus:outline-none focus:border-gray-900">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-48">
                <select name="payment_method" class="w-full bg-white border-2 border-gray-300 text-xs font-bold uppercase rounded-none px-3 py-2 text-gray-900 focus:outline-none focus:border-gray-900">
                    <option value="">All Payment Modes</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash Drawer</option>
                    <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                </select>
            </div>

            <div class="w-36">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-white border-2 border-gray-300 text-xs font-bold rounded-none px-3 py-2 text-gray-900 focus:outline-none focus:border-gray-900">
            </div>

            <div class="w-36">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-white border-2 border-gray-300 text-xs font-bold rounded-none px-3 py-2 text-gray-900 focus:outline-none focus:border-gray-900">
            </div>

            <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold uppercase tracking-wider transition-colors">
                Filter
            </button>
            <a href="{{ route('restaurant.expenses', ['branch_id' => $branch->id]) }}" class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-900 uppercase tracking-wider transition-colors">
                Reset
            </a>
        </form>

        <!-- Expenses Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-200">
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Date</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Category</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Paid To / Vendor</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Method</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Shift Ref</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Recorded By</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Amount</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 font-bold text-gray-900">
                                {{ $expense->expense_date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 border border-gray-300 text-[10px] font-bold uppercase tracking-wider text-gray-700 bg-gray-100">
                                    {{ $expense->category }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-900">{{ $expense->paid_to ?? '-' }}</div>
                                @if($expense->description)
                                    <div class="text-[10px] font-bold text-gray-500 uppercase mt-0.5 truncate max-w-[200px]">{{ $expense->description }}</div>
                                @endif
                                @if($expense->receipt_number)
                                    <div class="text-[10px] font-mono text-gray-500 mt-0.5">REF: {{ $expense->receipt_number }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs font-bold uppercase tracking-wider {{ $expense->payment_method === 'cash' ? 'text-yellow-600' : ($expense->payment_method === 'mobile_money' ? 'text-green-600' : 'text-blue-600') }}">
                                    {{ str_replace('_', ' ', $expense->payment_method) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs font-bold text-gray-600">
                                @if($expense->shift_id)
                                    SHIFT #{{ $expense->shift_id }}
                                @else
                                    GENERAL
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">
                                {{ $expense->user->name ?? 'Staff' }}
                            </td>
                            <td class="py-3 px-4 text-right font-black text-gray-900">
                                TZS {{ number_format($expense->amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="{{ route('restaurant.expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-red-600 hover:underline uppercase">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 font-medium">
                                No expense records found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="p-4 border-t-2 border-gray-200 bg-gray-50">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Record Operating Expense -->
<div id="recordExpenseModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border-2 border-gray-900 max-w-lg w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between border-b-2 border-gray-200 pb-4 mb-4">
            <div>
                <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">Record Expense</h3>
            </div>
            <button type="button" onclick="document.getElementById('recordExpenseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-900 text-2xl font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('restaurant.expenses.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="branch_id" value="{{ $branch->id }}">

            @if($activeShift)
                <div class="p-3 border-2 border-yellow-600 bg-yellow-50 text-yellow-800 text-[10px] font-bold uppercase tracking-wider">
                    <strong>Shift #{{ $activeShift->id }} Active:</strong> "Cash" expenses deduct from your drawer.
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Category *</label>
                    <select name="category" required class="w-full bg-white border-2 border-gray-300 text-xs font-bold uppercase px-3 py-2 text-gray-900 focus:border-gray-900">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Amount (TZS) *</label>
                    <input type="number" step="0.01" min="1" name="amount" required class="w-full bg-white border-2 border-gray-300 text-xs font-bold px-3 py-2 text-gray-900 focus:border-gray-900" placeholder="e.g. 35000">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Date *</label>
                    <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="w-full bg-white border-2 border-gray-300 text-xs font-bold px-3 py-2 text-gray-900 focus:border-gray-900">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Method *</label>
                    <select name="payment_method" required class="w-full bg-white border-2 border-gray-300 text-xs font-bold uppercase px-3 py-2 text-gray-900 focus:border-gray-900">
                        <option value="cash">Cash Drawer</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Paid To</label>
                    <input type="text" name="paid_to" class="w-full bg-white border-2 border-gray-300 text-xs font-bold px-3 py-2 text-gray-900 focus:border-gray-900">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Receipt / Ref #</label>
                    <input type="text" name="receipt_number" class="w-full bg-white border-2 border-gray-300 text-xs font-bold px-3 py-2 text-gray-900 focus:border-gray-900">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full bg-white border-2 border-gray-300 text-xs font-bold px-3 py-2 text-gray-900 focus:border-gray-900"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t-2 border-gray-200">
                <button type="button" onclick="document.getElementById('recordExpenseModal').classList.add('hidden')" class="px-4 py-2 border-2 border-gray-300 text-xs font-bold uppercase text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-xs font-bold uppercase text-white">
                    Save Expense
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

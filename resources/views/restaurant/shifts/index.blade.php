@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full">

    <!-- Header & Action Row -->
    <div class="mb-8 border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">Cashier Accountability</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">{{ $branch->name }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Shifts & Reconciliations</h1>
        </div>

        <div>
            @if(!$activeShift)
                <button type="button" onclick="document.getElementById('openShiftModal').classList.remove('hidden')" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors flex items-center gap-2">
                    Open Shift (Float) &rarr;
                </button>
            @else
                <button type="button" onclick="document.getElementById('closeShiftModal').classList.remove('hidden')" class="px-5 py-2 border-2 border-red-600 bg-red-600 text-white font-bold text-sm uppercase tracking-wider hover:bg-red-700 transition-colors flex items-center gap-2">
                    Close Shift & Report &rarr;
                </button>
            @endif
        </div>
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

    <!-- Active Shift Drawer Card -->
    @if($activeShift)
        <div class="mb-8 bg-white border-2 border-gray-900 p-6 relative">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gray-900"></div>
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest bg-gray-100 border border-gray-300 text-gray-900">
                            <span class="h-1.5 w-1.5 bg-green-500 animate-pulse"></span>
                            ACTIVE SHIFT #{{ $activeShift->id }}
                        </span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase">STARTED: {{ $activeShift->opened_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="text-xl font-black text-gray-900 uppercase">
                        CASHIER: {{ auth()->user()->name }}
                    </div>
                    <p class="text-xs font-bold text-gray-500 uppercase">All POS sales and station expenses are attached to this session.</p>
                </div>

                <div class="bg-gray-50 border-2 border-gray-200 p-5 min-w-[280px]">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Expected Physical Cash</div>
                    <div class="text-3xl font-black text-gray-900 font-mono mt-1">
                        TZS {{ number_format($activeShiftStats['expected_cash'], 2) }}
                    </div>
                    <div class="text-[10px] font-bold text-gray-500 mt-2 flex items-center justify-between uppercase">
                        <span>Float: {{ number_format($activeShift->opening_float, 0) }}</span>
                        <span>Sales: +{{ number_format($activeShiftStats['cash_sales'], 0) }}</span>
                        <span>Paid: -{{ number_format($activeShiftStats['cash_expenses'], 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t-2 border-gray-100">
                <div class="p-3 border-2 border-gray-100 bg-gray-50">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Opening Float</div>
                    <div class="text-sm font-black text-gray-900 font-mono mt-1">TZS {{ number_format($activeShift->opening_float, 2) }}</div>
                </div>
                <div class="p-3 border-2 border-green-200 bg-green-50">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-green-800">+ Cash Sales</div>
                    <div class="text-sm font-black text-green-900 font-mono mt-1">TZS {{ number_format($activeShiftStats['cash_sales'], 2) }}</div>
                </div>
                <div class="p-3 border-2 border-red-200 bg-red-50">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-red-800">- Petty Cash (Out)</div>
                    <div class="text-sm font-black text-red-900 font-mono mt-1">TZS {{ number_format($activeShiftStats['cash_expenses'], 2) }}</div>
                </div>
                <div class="p-3 border-2 border-blue-200 bg-blue-50">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-800">Electronic Sales</div>
                    <div class="text-sm font-black text-blue-900 font-mono mt-1">TZS {{ number_format($activeShiftStats['card_sales'] + $activeShiftStats['mobile_sales'], 2) }}</div>
                </div>
            </div>
        </div>
    @else
        <div class="mb-8 bg-gray-50 border-2 border-gray-300 p-8 text-center space-y-4">
            <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">No Active Cashier Shift</h3>
            <p class="text-xs font-bold text-gray-500 uppercase max-w-md mx-auto">Open a shift and register your starting cash float to begin taking orders.</p>
            <button type="button" onclick="document.getElementById('openShiftModal').classList.remove('hidden')" class="px-6 py-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-white font-bold text-sm uppercase tracking-wider transition-colors mt-2">
                Open Shift Now
            </button>
        </div>
    @endif

    <!-- Shift History & Z-Reports -->
    <div class="bg-white border-2 border-gray-200">
        <div class="p-4 border-b-2 border-gray-200 bg-gray-50">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Shift Audit Trail</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-200">
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Shift #</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Cashier</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Opened/Closed</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Start Float</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Total Sales</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Expected</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Counted</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Variance</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($shiftHistory as $shift)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 font-black font-mono text-gray-900">
                                #{{ $shift->id }}
                                @if($shift->status === 'open')
                                    <span class="ml-1.5 px-2 py-0.5 bg-green-50 text-green-800 border border-green-200 text-[10px] font-bold uppercase tracking-wider">OPEN</span>
                                @else
                                    <span class="ml-1.5 px-2 py-0.5 bg-gray-100 text-gray-600 border border-gray-300 text-[10px] font-bold uppercase tracking-wider">CLOSED</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs font-bold text-gray-900 uppercase">
                                {{ $shift->user->name ?? 'Staff' }}
                            </td>
                            <td class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">
                                <div>{{ $shift->opened_at->format('d M y, H:i') }}</div>
                                @if($shift->closed_at)
                                    <div>CL: {{ $shift->closed_at->format('H:i') }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-gray-600">
                                {{ number_format($shift->opening_float, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-black text-gray-900">
                                {{ number_format($shift->total_sales, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-gray-600">
                                {{ $shift->expected_cash !== null ? number_format($shift->expected_cash, 2) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-black text-gray-900">
                                {{ $shift->closing_cash_counted !== null ? number_format($shift->closing_cash_counted, 2) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($shift->cash_difference !== null)
                                    @if(abs($shift->cash_difference) < 0.01)
                                        <span class="px-2 py-0.5 bg-green-50 text-green-800 border border-green-200 text-[10px] font-bold uppercase tracking-wider">
                                            EXACT MATCH
                                        </span>
                                    @elseif($shift->cash_difference > 0)
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 text-[10px] font-bold uppercase tracking-wider">
                                            +{{ number_format($shift->cash_difference, 2) }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-50 text-red-800 border border-red-200 text-[10px] font-bold uppercase tracking-wider">
                                            -{{ number_format(abs($shift->cash_difference), 2) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('restaurant.shifts.report', $shift) }}" target="_blank" class="text-xs font-bold text-gray-900 hover:underline uppercase">
                                    Z-Report
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-500 font-medium">
                                No shift history recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shiftHistory->hasPages())
            <div class="p-4 border-t-2 border-gray-200 bg-gray-50">
                {{ $shiftHistory->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Open Shift -->
<div id="openShiftModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border-2 border-gray-900 max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between border-b-2 border-gray-200 pb-4 mb-4">
            <div>
                <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">Open Shift</h3>
            </div>
            <button type="button" onclick="document.getElementById('openShiftModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
        </div>

        <form method="POST" action="{{ route('restaurant.shifts.open') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="branch_id" value="{{ $branch->id }}">

            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Starting Cash Float (TZS) *</label>
                <input type="number" step="0.01" min="0" name="opening_float" required value="50000" class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Shift Notes</label>
                <textarea name="notes" rows="2" class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t-2 border-gray-200 mt-4">
                <button type="button" onclick="document.getElementById('openShiftModal').classList.add('hidden')" class="px-4 py-2 border-2 border-gray-300 text-xs font-bold uppercase text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-xs font-bold uppercase text-white">
                    Open Shift
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Close Shift -->
@if($activeShift)
<div id="closeShiftModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border-2 border-gray-900 max-w-lg w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between border-b-2 border-gray-200 pb-4 mb-4">
            <div>
                <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">Close Shift</h3>
                <p class="text-[10px] font-bold text-gray-500 uppercase mt-1">Shift #{{ $activeShift->id }}</p>
            </div>
            <button type="button" onclick="document.getElementById('closeShiftModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
        </div>

        <form method="POST" action="{{ route('restaurant.shifts.close', $activeShift) }}" class="space-y-4">
            @csrf

            <!-- Expected Cash Box -->
            <div class="p-4 border-2 border-gray-200 bg-gray-50 space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-gray-600 uppercase">
                    <span>Float:</span>
                    <span class="font-mono text-gray-900">TZS {{ number_format($activeShift->opening_float, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs font-bold text-green-700 uppercase">
                    <span>+ Sales:</span>
                    <span class="font-mono">+TZS {{ number_format($activeShiftStats['cash_sales'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs font-bold text-red-700 uppercase">
                    <span>- Expenses:</span>
                    <span class="font-mono">-TZS {{ number_format($activeShiftStats['cash_expenses'], 2) }}</span>
                </div>
                <div class="border-t-2 border-gray-200 pt-2 mt-2 flex items-center justify-between font-black uppercase">
                    <span class="text-xs text-gray-900">Expected:</span>
                    <span class="text-sm font-mono text-gray-900" id="expectedCashVal" data-amount="{{ $activeShiftStats['expected_cash'] }}">
                        TZS {{ number_format($activeShiftStats['expected_cash'], 2) }}
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Physical Cash Counted *</label>
                <input type="number" step="0.01" min="0" id="closingCountedCash" name="closing_cash_counted" required class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900">
            </div>

            <div id="liveVarianceDisplay" class="p-3 border-2 border-gray-200 bg-gray-50 text-xs flex items-center justify-between hidden uppercase font-bold">
                <span class="text-gray-600">Discrepancy:</span>
                <span id="varianceText" class="font-mono">0.00</span>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Handover Notes</label>
                <textarea name="notes" rows="2" class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t-2 border-gray-200 mt-4">
                <button type="button" onclick="document.getElementById('closeShiftModal').classList.add('hidden')" class="px-4 py-2 border-2 border-gray-300 text-xs font-bold uppercase text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-xs font-bold uppercase text-white">
                    Close Shift
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('closingCountedCash');
        const expectedEl = document.getElementById('expectedCashVal');
        const varianceDisplay = document.getElementById('liveVarianceDisplay');
        const varianceText = document.getElementById('varianceText');

        if (input && expectedEl) {
            const expected = parseFloat(expectedEl.getAttribute('data-amount')) || 0;
            input.addEventListener('input', () => {
                const counted = parseFloat(input.value) || 0;
                const diff = counted - expected;
                varianceDisplay.classList.remove('hidden');

                if (Math.abs(diff) < 0.01) {
                    varianceText.className = 'font-mono text-green-700';
                    varianceText.textContent = 'Exact Match';
                } else if (diff > 0) {
                    varianceText.className = 'font-mono text-blue-700';
                    varianceText.textContent = '+TZS ' + diff.toLocaleString('en-US', {minimumFractionDigits: 2});
                } else {
                    varianceText.className = 'font-mono text-red-700';
                    varianceText.textContent = '-TZS ' + Math.abs(diff).toLocaleString('en-US', {minimumFractionDigits: 2});
                }
            });
        }
    });
</script>
@endif
@endsection

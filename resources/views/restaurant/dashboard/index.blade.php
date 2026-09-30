@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full">
    
    <!-- Dashboard Header -->
    <div class="mb-8 border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="text-xs font-bold tracking-widest text-gray-500 uppercase mb-1">Daily Operations Overview</div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">
                {{ $branch->name }}
            </h1>
        </div>

        <!-- Station Switcher & POS Button -->
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('restaurant.dashboard') }}" class="flex items-center gap-2 m-0">
                <select name="branch_id" onchange="this.form.submit()" class="border-2 border-gray-300 rounded-none px-3 py-1.5 text-sm font-bold text-gray-800 focus:outline-none focus:border-gray-900 focus:ring-0">
                    @foreach($allBranches as $b)
                        <option value="{{ $b->id }}" {{ $branch->id == $b->id ? 'selected' : '' }}>{{ strtoupper($b->name) }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('restaurant.pos', ['branch_id' => $branch->id]) }}" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors flex items-center gap-2">
                Launch POS &rarr;
            </a>
        </div>
    </div>

    <!-- 6 Daily Financial P&L Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <!-- 1. Gross Revenue -->
        <div class="bg-white border-2 border-gray-200 p-5 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-green-600"></div>
            <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Today's Gross Sales</div>
            <div class="text-3xl font-black text-gray-900">
                TZS {{ number_format($todayRevenue, 2) }}
            </div>
            <p class="text-xs text-gray-500 mt-2 font-medium">Confirmed dining sales</p>
        </div>

        <!-- 2. Cost of Goods Sold (COGS) -->
        <div class="bg-white border-2 border-gray-200 p-5 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-red-600"></div>
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-bold uppercase tracking-wider text-gray-500">COGS</div>
                <div class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 border border-red-200">{{ $foodCostPercent }}% FOOD COST</div>
            </div>
            <div class="text-3xl font-black text-gray-900">
                TZS {{ number_format($todayCogs, 2) }}
            </div>
            <p class="text-xs text-gray-500 mt-2 font-medium">Direct food ingredient cost</p>
        </div>

        <!-- 3. Gross Profit -->
        <div class="bg-white border-2 border-gray-200 p-5 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-600"></div>
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-bold uppercase tracking-wider text-gray-500">Gross Profit (Faida)</div>
                <div class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 border border-blue-200">{{ $grossMarginPercent }}% MARGIN</div>
            </div>
            <div class="text-3xl font-black text-gray-900">
                TZS {{ number_format($grossProfit, 2) }}
            </div>
            <p class="text-xs text-gray-500 mt-2 font-medium">Revenue minus COGS</p>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border-2 border-gray-200 p-5 flex flex-col justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Operating Expenses</div>
                <div class="text-xl font-bold text-gray-900">TZS {{ number_format($todayExpenses, 2) }}</div>
            </div>
            <a href="{{ route('restaurant.expenses') }}" class="text-xs font-bold text-blue-600 hover:underline uppercase tracking-wider mt-4">View Report &rarr;</a>
        </div>

        <div class="bg-white border-2 border-gray-200 p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Net Profit</div>
                    <div class="text-xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">TZS {{ number_format($netProfit, 2) }}</div>
                </div>
                <div class="text-xs font-bold {{ $netProfit >= 0 ? 'text-green-600 bg-green-50 border-green-200' : 'text-red-600 bg-red-50 border-red-200' }} px-2 py-0.5 border">
                    {{ $netMarginPercent }}% NET
                </div>
            </div>
        </div>

        <div class="bg-white border-2 border-gray-200 p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Order Volume</div>
                    <div class="text-xl font-bold text-gray-900">{{ $todayOrdersCount }}</div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Avg Ticket</div>
                    <div class="text-sm font-bold text-gray-900">TZS {{ number_format($avgOrderValue, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Selling Dishes -->
        <div>
            <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wider mb-4 border-b-2 border-gray-200 pb-2">Top Selling Dishes Today</h2>
            <div class="bg-white border-2 border-gray-200">
                @if(count($topItems) > 0)
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-4 py-3 font-bold text-gray-600 uppercase">Item</th>
                                <th class="px-4 py-3 font-bold text-gray-600 uppercase text-right">Qty</th>
                                <th class="px-4 py-3 font-bold text-gray-600 uppercase text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($topItems as $dish)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $dish->item->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600 font-bold">{{ $dish->total_qty }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900 font-bold">TZS {{ number_format($dish->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-8 text-center text-gray-500 font-medium">
                        No orders processed yet today.
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Station Orders -->
        <div>
            <div class="flex items-center justify-between mb-4 border-b-2 border-gray-200 pb-2">
                <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wider">Recent Orders</h2>
                <a href="{{ route('restaurant.orders') }}" class="text-xs font-bold text-blue-600 hover:underline uppercase">View All &rarr;</a>
            </div>
            
            <div class="bg-white border-2 border-gray-200">
                @if(count($recentOrders) > 0)
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-4 py-3 font-bold text-gray-600 uppercase">Order #</th>
                                <th class="px-4 py-3 font-bold text-gray-600 uppercase">Time</th>
                                <th class="px-4 py-3 font-bold text-gray-600 uppercase text-right">Total</th>
                                <th class="px-4 py-3 font-bold text-gray-600 uppercase text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-bold text-blue-600">
                                        <a href="#">#{{ $order->order_number }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $order->created_at->format('H:i') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900">TZS {{ number_format($order->total, 2) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border 
                                            @if($order->status->value == 'completed') bg-green-50 text-green-700 border-green-200
                                            @elseif($order->status->value == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                            @elseif($order->status->value == 'kitchen') bg-purple-50 text-purple-700 border-purple-200
                                            @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                            {{ $order->status->value }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-8 text-center text-gray-500 font-medium">
                        No recent orders yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

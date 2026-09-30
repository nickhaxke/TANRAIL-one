@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full">
    
    <!-- Orders Header -->
    <div class="mb-8 border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">Station Sales</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">{{ $branch->name }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Order History</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('restaurant.pos') }}" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors flex items-center gap-2">
                Open POS Terminal &rarr;
            </a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border-2 border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-200">
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Receipt #</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Date & Time</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Cashier</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Items</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Payment</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Total (TZS)</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-xs text-gray-900">
                            TN-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-3 px-4 text-xs font-bold text-gray-600">
                            {{ $order->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="py-3 px-4 text-xs font-bold text-gray-900 uppercase">
                            {{ $order->createdBy?->name ?? 'POS Terminal' }}
                        </td>
                        <td class="py-3 px-4 text-xs font-bold text-gray-500 uppercase max-w-xs truncate">
                            {{ $order->lines->map(fn($l) => (int)$l->quantity . 'x ' . $l->item->name)->join(', ') }}
                        </td>
                        <td class="py-3 px-4 text-xs">
                            @php
                                $payment = $order->payments->first();
                            @endphp
                            <span class="px-2 py-0.5 border border-gray-300 text-[10px] font-bold uppercase tracking-wider text-gray-700 bg-gray-100">
                                {{ strtoupper(str_replace('_', ' ', $payment?->method?->value ?? 'Cash')) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-black font-mono text-gray-900">
                            {{ number_format($order->total, 2) }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($order->status->value === 'completed' || $order->status->value === 'fulfilled')
                                <span class="px-2 py-0.5 bg-green-50 text-green-800 border border-green-200 font-bold uppercase tracking-wider text-[10px]">
                                    Completed
                                </span>
                            @elseif($order->status->value === 'ready')
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 font-bold uppercase tracking-wider text-[10px]">
                                    Ready
                                </span>
                            @elseif($order->status->value === 'preparing')
                                <span class="px-2 py-0.5 bg-yellow-50 text-yellow-800 border border-yellow-200 font-bold uppercase tracking-wider text-[10px]">
                                    Preparing
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 border border-gray-300 font-bold uppercase tracking-wider text-[10px]">
                                    {{ $order->status->value }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500 font-medium">
                            No sales orders recorded at this station yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="p-4 border-t-2 border-gray-200 bg-gray-50">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

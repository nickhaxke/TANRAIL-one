@extends('layouts.cleaning')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Store / Stock Balances</h1>
            <p class="text-slate-500 text-sm mt-1">Manage cleaning materials</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('cleaning.store.receive') }}" class="px-4 py-2 bg-green-500 text-white font-bold rounded-lg hover:bg-green-600 transition-colors">Receive Materials</a>
            <a href="{{ route('cleaning.store.issue') }}" class="px-4 py-2 bg-blue-500 text-white font-bold rounded-lg hover:bg-blue-600 transition-colors">Issue Materials</a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-100 text-green-700 rounded-xl font-medium border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-700 rounded-xl font-medium border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">SKU</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Item Name</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Quantity</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Valuation</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($summary as $row)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $row['item']->sku }}</td>
                    <td class="px-6 py-4 font-bold text-slate-700">{{ $row['item']->name }}</td>
                    <td class="px-6 py-4 text-right font-black {{ $row['quantity'] <= 0 ? 'text-red-500' : 'text-slate-800' }}">
                        {{ number_format($row['quantity'], 2) }} {{ $row['item']->unit_of_measure }}
                    </td>
                    <td class="px-6 py-4 text-right text-slate-500">
                        {{ number_format($row['value'], 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">No stock balances found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

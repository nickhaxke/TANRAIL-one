@extends('layouts.cleaning')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Stock Movements</h1>
        <p class="text-slate-500 text-sm mt-1">Transaction history for cleaning materials</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Date</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Type</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Item</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Quantity</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">User</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($movements as $mov)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-slate-500">{{ $mov->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider
                            {{ $mov->type->value === 'receive' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $mov->type->value === 'issue' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $mov->type->value === 'adjust' ? 'bg-amber-100 text-amber-700' : '' }}">
                            {{ $mov->type->value }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-700">{{ $mov->item->name }}</td>
                    <td class="px-6 py-4 text-right font-medium {{ in_array($mov->type->value, ['issue', 'adjust']) && $mov->source_location_id ? 'text-red-500' : 'text-green-600' }}">
                        {{ in_array($mov->type->value, ['issue']) ? '-' : '+' }}{{ number_format($mov->quantity, 2) }}
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $mov->user->name ?? 'System' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">No recent stock movements.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($movements->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $movements->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.cleaning')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Daily Controls</h1>
        <p class="text-slate-500 text-sm mt-1">Monitor branch operations</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Date</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Branch</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Supervisor</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Workforce Status</th>
                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Final Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($dailyControls as $dc)
                <tr>
                    <td class="px-6 py-4">{{ $dc->date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 font-bold text-slate-700">{{ $dc->branch->name }}</td>
                    <td class="px-6 py-4">{{ $dc->supervisor->name }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 rounded text-xs font-bold {{ $dc->workforce_check_status === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $dc->workforce_check_status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 rounded text-xs font-bold {{ $dc->status === 'Submitted' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $dc->status ?? 'Draft' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">No daily controls found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($dailyControls->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $dailyControls->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.cleaning')

@section('content')
<div class="max-w-6xl mx-auto w-full">
    <!-- Header -->
    <div class="mb-8 border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">TANRAIL ONE</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">Cleaning Operations</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Daily Control History</h1>
        </div>
        <div class="text-right">
            <a href="{{ route('cleaning.dashboard') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2 px-6 rounded-lg transition-colors shadow-sm text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- History List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-bold border-b">Date</th>
                        <th class="p-4 font-bold border-b">Station</th>
                        <th class="p-4 font-bold border-b">Shift</th>
                        <th class="p-4 font-bold border-b">Operations</th>
                        <th class="p-4 font-bold border-b text-center">Status</th>
                        <th class="p-4 font-bold border-b text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($history as $record)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}
                                @if($record->date === today()->format('Y-m-d'))
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">Today</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm font-semibold text-gray-700">
                                {{ $record->branch ? $record->branch->name : 'Unknown Station' }}
                            </td>
                            <td class="p-4 text-sm font-semibold text-gray-700">
                                {{ $record->shift }}
                            </td>
                            <td class="p-4 text-sm text-gray-500">
                                <span class="font-bold text-gray-900">{{ $record->workActivities->count() }}</span> recorded
                            </td>
                            <td class="p-4 text-center">
                                @if($record->status === 'Submitted')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-wider">Submitted</span>
                                @elseif($record->status === 'Approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider shadow-sm border border-emerald-200">Approved</span>
                                @elseif($record->status === 'Returned')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 uppercase tracking-wider">Returned</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">In Progress</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                @if($record->date === today()->format('Y-m-d') && $record->status !== 'Submitted')
                                    <a href="{{ route('cleaning.daily-control.index') }}" class="text-cyan-600 hover:text-cyan-800 font-bold text-sm">Resume Shift</a>
                                @else
                                    <a href="{{ route('cleaning.daily-control.index', ['date' => $record->date]) }}" class="text-gray-500 hover:text-gray-900 font-bold text-sm">View Report</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500 italic">No shift history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($history->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $history->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

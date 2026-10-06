@extends('layouts.cleaning')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Workforce Timesheets & Activity Summary</h1>
            <p class="text-sm text-slate-500 mt-1">Operational work-time derived from actual morning attendance and verified cleaning activities</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('cleaning.timesheets.index') }}" class="flex flex-wrap items-center gap-3">
            @can('cleaning.operations.monitor')
            <div>
                <select name="branch_id" class="text-sm border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-cyan-500 focus:ring focus:ring-cyan-200">
                    <option value="">All Station Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            @endcan

            <div>
                <input type="date" name="date" value="{{ $date }}" class="text-sm border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-700 shadow-sm focus:border-cyan-500 focus:ring focus:ring-cyan-200">
            </div>

            <button type="submit" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Timesheet Entries Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-800 text-base">Derived Work Time — {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</h2>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                {{ $timesheetEntries->count() }} Workers On-Duty
            </span>
        </div>

        @if($timesheetEntries->isEmpty())
            <div class="py-16 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-700">No timesheet records found</h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1">There are no recorded attendance or work activities for the selected date and station.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/75 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">Worker</th>
                            <th class="px-6 py-3.5">Station / Branch</th>
                            <th class="px-6 py-3.5">Arrival</th>
                            <th class="px-6 py-3.5">Assigned Activities</th>
                            <th class="px-6 py-3.5">Completed</th>
                            <th class="px-6 py-3.5">Active Time</th>
                            <th class="px-6 py-3.5">Activity Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($timesheetEntries as $entry)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    <div class="font-semibold">{{ $entry['worker_name'] }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $entry['worker_code'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $entry['branch_name'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-slate-600">
                                    {{ $entry['arrival_time'] ? \Carbon\Carbon::parse($entry['arrival_time'])->format('H:i') : 'Recorded' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">
                                    {{ $entry['total_activities'] }} tasks
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $entry['completed_activities'] > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $entry['completed_activities'] }} / {{ $entry['total_activities'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-cyan-700 font-mono text-sm">{{ $entry['total_work_formatted'] }}</div>
                                    <div class="text-[11px] text-slate-400 font-medium">({{ $entry['total_work_hours'] }} hrs)</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1 max-w-xs">
                                        @foreach($entry['activities'] as $act)
                                            <div class="text-xs flex items-center justify-between gap-2 p-1.5 rounded bg-slate-50 border border-slate-100">
                                                <span class="truncate font-medium text-slate-700">{{ $act['name'] }}</span>
                                                <span class="text-slate-400 font-mono text-[11px] shrink-0">{{ $act['started_at'] ?? '--' }} - {{ $act['completed_at'] ?? '--' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

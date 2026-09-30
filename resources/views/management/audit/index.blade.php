@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Header with Navy Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A5F] p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold border border-blue-400/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Corporate Governance & Compliance
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Audit & Control Center</h1>
                <p class="mt-1 text-sm text-slate-300">System event audit trail, administrative security logs, and regulatory compliance records.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('management.audit.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 px-4 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export CSV Logs
                </a>
                <a href="{{ route('management.roles.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 text-sm font-semibold shadow-lg shadow-blue-600/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Roles & Security Policy
                </a>
            </div>
        </div>
        <!-- Background Glow -->
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Global Date Filters -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
        <form method="GET" action="{{ route('management.audit') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="start_date" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>

            <div class="flex-1 min-w-[200px]">
                <label for="end_date" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>
            
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors cursor-pointer">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter Logs
                </button>
                <a href="{{ route('management.audit') }}" class="inline-flex items-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 text-sm font-medium transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Audit & Governance KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Processed Events</span>
                <span class="p-2 rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ number_format($processedEventsCount) }}</span>
                <span class="text-xs text-slate-500 font-medium">Domain events</span>
            </div>
            <p class="mt-1 text-xs text-slate-500">Idempotency & transaction logs</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Personnel</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-emerald-600">{{ number_format($activeUsers) }}</span>
                <span class="text-xs text-slate-500 font-medium">/ {{ $totalUsers }} staff</span>
            </div>
            <p class="mt-1 text-xs text-slate-500">Active in the last 7 days</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Security Roles</span>
                <span class="p-2 rounded-xl bg-purple-50 text-purple-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ number_format($totalRoles) }}</span>
                <span class="text-xs text-purple-600 font-medium">Configured roles</span>
            </div>
            <p class="mt-1 text-xs text-slate-500">RBAC privilege groups</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Governance Index</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-amber-600">{{ $governanceScore }}%</span>
                <span class="text-xs text-slate-500 font-medium">Compliance</span>
            </div>
            <p class="mt-1 text-xs text-slate-500">Manager appointment coverage</p>
        </div>
    </div>

    <!-- Recent Processed Domain Events Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Recent Domain Activity & System Audit</h2>
                <p class="text-xs text-slate-500 mt-0.5">Verified events recorded by the system ensuring transaction idempotency and security integrity.</p>
            </div>
            <a href="{{ route('management.audit.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download Full Log (CSV)
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Record ID</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Event Class / Operation</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Reference / Idempotency Key</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Processed At</th>
                        <th scope="col" class="px-3 py-3.5 text-right pr-6 text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($recentEvents as $event)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-6 pr-3 text-xs font-mono font-medium text-slate-500">
                            #{{ $event->id }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-slate-900">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 text-xs font-mono">
                                {{ class_basename($event->event_class) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-xs font-mono text-slate-600">
                            {{ $event->reference_id }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-xs text-slate-500">
                            {{ $event->created_at ? \Carbon\Carbon::parse($event->created_at)->format('d M Y, H:i:s') : 'N/A' }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-right pr-6 text-xs">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Processed
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-sm text-slate-500">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 mb-3">
                                <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="font-semibold text-slate-800">No audit event records found for the selected time period</p>
                            <p class="text-xs text-slate-500 mt-1">Adjust the date range above or execute transactions within the system to view audit trails.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Header with Navy Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A5F] p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold border border-blue-400/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Statutory & Executive Intelligence
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Executive Management Reports</h1>
                <p class="mt-1 text-sm text-slate-300">Official administrative registers, managerial assignments, station networks, and institutional personnel rosters.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('management.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 px-4 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
        <!-- Background Glow -->
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- 4 Core Administrative Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Report 1: Divisions & Business Units Directory -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-start justify-between">
                    <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        {{ $totalBusinessUnits }} Active Units
                    </span>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">Commercial Divisions Directory</h3>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Comprehensive roster of all business divisions under TANRAIL, division codes, appointed unit managers, and assigned branches.
                </p>
                <div class="mt-4 py-3 px-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs text-slate-600">
                    <span>Designated Managers: <strong class="text-slate-900">{{ $unitsWithManagers }} / {{ $totalBusinessUnits }}</strong></span>
                    <span class="text-emerald-600 font-semibold">Level 2 Hierarchy</span>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                <a href="{{ route('management.organization.business-units') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    View in System &rarr;
                </a>
                <a href="{{ route('management.reports.export', ['type' => 'divisions']) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 text-xs font-semibold shadow-sm transition-colors">
                    <svg class="w-4 h-4 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download CSV (Excel)
                </a>
            </div>
        </div>

        <!-- Report 2: Stations & Regional Network -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-start justify-between">
                    <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $totalBranches }} Stations / Outlets
                    </span>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">Stations & Regional Corridor Network</h3>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Operational analytics for terminal counters across the rail corridor (SGR & MGR), regional jurisdictions, station codes, and supervisors.
                </p>
                <div class="mt-4 py-3 px-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs text-slate-600">
                    <span>Regional Corridors: <strong class="text-slate-900">{{ $regionsCount }} Regions</strong></span>
                    <span>Station Supervisors: <strong class="text-slate-900">{{ $branchesWithSupervisors }} / {{ $totalBranches }}</strong></span>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                <a href="{{ route('management.organization.branches') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    View in System &rarr;
                </a>
                <a href="{{ route('management.reports.export', ['type' => 'stations']) }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 text-xs font-semibold shadow-sm transition-colors">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download CSV (Excel)
                </a>
            </div>
        </div>

        <!-- Report 3: Staff Roster & Roles Allocation -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-start justify-between">
                    <div class="p-3 rounded-xl bg-purple-50 text-purple-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                        {{ $totalStaff }} Personnel
                    </span>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">Personnel Roster & Access Matrix</h3>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Official registry of administrative personnel, designated leaders, employee identifiers, and jurisdictional privileges (HQ, Division, or Station).
                </p>
                <div class="mt-4 py-3 px-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs text-slate-600">
                    <span>Institutional Roles: <strong class="text-slate-900">{{ $totalRoles }} Groups</strong></span>
                    <span class="text-purple-600 font-semibold">RBAC Matrix</span>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                <a href="{{ route('management.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    View in System &rarr;
                </a>
                <a href="{{ route('management.reports.export', ['type' => 'staff']) }}" class="inline-flex items-center gap-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 text-xs font-semibold shadow-sm transition-colors">
                    <svg class="w-4 h-4 text-purple-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download CSV (Excel)
                </a>
            </div>
        </div>

        <!-- Report 4: Corporate Governance & Leadership Compliance -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-start justify-between">
                    <div class="p-3 rounded-xl bg-amber-50 text-amber-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        {{ $governanceScore }}% Compliance
                    </span>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">Governance & Leadership Compliance</h3>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Statutory oversight report identifying commercial units and stations with appointed leaders vs nodes requiring nomination actions.
                </p>
                <div class="mt-4 py-3 px-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs text-slate-600">
                    <span>Appointed Supervisors: <strong class="text-slate-900">{{ $branchesWithSupervisors }} / {{ $totalBranches }}</strong></span>
                    <span class="text-amber-700 font-semibold">Statutory Governance</span>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                <a href="{{ route('management.organization.structure') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    Inspect Topology &rarr;
                </a>
                <a href="{{ route('management.reports.export', ['type' => 'governance']) }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white px-4 py-2 text-xs font-semibold shadow-sm transition-colors">
                    <svg class="w-4 h-4 text-amber-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download CSV (Excel)
                </a>
            </div>
        </div>

    </div>

    <!-- Quick Link Banner to Audit Center -->
    <div class="rounded-2xl bg-slate-900 p-6 text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="p-3 rounded-xl bg-white/10 text-blue-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-white">Need complete system event audit trails?</h4>
                <p class="text-xs text-slate-400 mt-0.5">Inspect and export low-level domain events and transactional idempotency logs.</p>
            </div>
        </div>
        <a href="{{ route('management.audit') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 text-xs font-semibold shadow-sm transition-colors whitespace-nowrap">
            Go to Audit & Control Center &rarr;
        </a>
    </div>
</div>
@endsection

@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                <a href="{{ route('management.organization.branches') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Branches & Outlets Directory
                </a>
                <span>/</span>
                <span class="text-gray-900 font-mono">{{ $branch->code }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#0A1A2F]">{{ $branch->name }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md font-mono text-xs font-bold bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-600/10">
                    {{ $branch->code }}
                </span>
                @if($branch->facility_type)
                    <span class="inline-flex items-center rounded-md bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                        {{ $branch->facility_type }}
                    </span>
                @endif
                @if($branch->status)
                    <span class="inline-flex items-center gap-x-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active Operating Outlet
                    </span>
                @else
                    <span class="inline-flex items-center gap-x-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                        Inactive / Suspended
                    </span>
                @endif
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Operating outlet governed under 
                @if($branch->businessUnit)
                    <a href="{{ route('management.business-units.show', $branch->businessUnit) }}" class="font-semibold text-blue-600 hover:text-blue-700 underline underline-offset-2">
                        {{ $branch->businessUnit->name }}
                    </a>
                @else
                    <span class="text-gray-400 italic">Unassigned Business Unit</span>
                @endif
                {{ $branch->city ? '• Located in ' . $branch->city : '' }}
            </p>
        </div>

        <!-- Action Controls -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <form action="{{ route('management.branches.toggle', $branch) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-lg px-3.5 py-2 text-xs font-semibold shadow-sm ring-1 ring-inset transition-colors {{ $branch->status ? 'bg-white text-gray-700 ring-gray-300 hover:bg-amber-50 hover:text-amber-700 hover:ring-amber-300' : 'bg-emerald-600 text-white hover:bg-emerald-500 ring-transparent shadow-emerald-500/20' }}">
                    @if($branch->status)
                        <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Deactivate Branch
                    @else
                        <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Activate Branch
                    @endif
                </button>
            </form>

            <a href="{{ route('management.branches.edit', $branch) }}" class="inline-flex items-center rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                Edit Outlet
            </a>

            <form action="{{ route('management.branches.destroy', $branch) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete branch {{ $branch->name }}? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-xs font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-200 hover:bg-red-50 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-200 flex items-center gap-3">
        <svg class="h-5 w-5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- 4 KPI Metrics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Status Card -->
        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Operational Status</p>
                    <p class="mt-2 text-xl font-black {{ $branch->status ? 'text-emerald-600' : 'text-gray-500' }}">
                        {{ $branch->status ? 'ACTIVE' : 'INACTIVE' }}
                    </p>
                </div>
                <div class="p-3 rounded-xl {{ $branch->status ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                {{ $branch->status ? 'Authorized for daily trading & services' : 'Suspended from terminal operations' }}
            </div>
        </div>

        <!-- Facility Type Card -->
        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Facility Type</p>
                    <p class="mt-2 text-xl font-black text-[#0A1A2F] truncate max-w-[150px]">
                        {{ $branch->facility_type ?? 'Standard Outlet' }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                City / Region: <strong class="text-gray-700">{{ $branch->city ?? 'Not specified' }}</strong>
            </div>
        </div>

        <!-- Parent Business Unit Card -->
        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Parent Division</p>
                    <p class="mt-2 text-xl font-black text-blue-600 truncate max-w-[150px]">
                        {{ $branch->businessUnit->code ?? 'N/A' }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 truncate">
                {{ $branch->businessUnit->name ?? 'Unassigned' }}
            </div>
        </div>

        <!-- Station Supervisor Card -->
        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Station Lead</p>
                    <p class="mt-2 text-xl font-black text-gray-900 truncate max-w-[150px]">
                        {{ $branch->manager_name ? Str::before($branch->manager_name, ' ') : 'Unassigned' }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-violet-50 text-violet-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 truncate">
                @if($branch->managerUser)
                    <span class="inline-flex items-center gap-1 font-semibold text-blue-600">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ $branch->managerUser->name }}
                    </span>
                @else
                    {{ $branch->manager_name ?? 'Pending Nomination' }}
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Profile & Division Details (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Outlet Identity & Specifications -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-6 sm:p-7">
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    Station & Physical Location Details
                </h3>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">Official Branch Name</dt>
                        <dd class="mt-1 font-bold text-gray-900 text-base">{{ $branch->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">Station / Outlet Code</dt>
                        <dd class="mt-1 font-mono font-bold text-blue-700 text-base">{{ $branch->code }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">Facility Classification</dt>
                        <dd class="mt-1 text-gray-800 font-medium">
                            {{ $branch->facility_type ?? 'Standard Service Counter' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">Station City / Region</dt>
                        <dd class="mt-1 text-gray-800 font-medium flex items-center gap-1.5">
                            <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
                            {{ $branch->city ?? 'Not configured' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">Physical Address / Station Landmark</dt>
                        <dd class="mt-1 text-gray-700 bg-slate-50 p-3 rounded-xl border border-gray-200/80 leading-relaxed font-normal">
                            {{ $branch->address ?? 'No physical address or station platform details provided.' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">Registration Date</dt>
                        <dd class="mt-1 text-gray-600">{{ $branch->created_at ? $branch->created_at->format('M d, Y') : 'System Origin' }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-400">Last Configuration Update</dt>
                        <dd class="mt-1 text-gray-600">{{ $branch->updated_at ? $branch->updated_at->diffForHumans() : 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Parent Business Unit Governance Details -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-6 sm:p-7">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                        Governing Commercial Division
                    </h3>
                    @if($branch->businessUnit)
                        <a href="{{ route('management.business-units.show', $branch->businessUnit) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                            Division Overview &rarr;
                        </a>
                    @endif
                </div>

                @if($branch->businessUnit)
                    <div class="bg-gradient-to-br from-blue-50/60 to-slate-50 rounded-xl p-5 border border-blue-100">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-blue-700 bg-blue-100/70 px-2 py-0.5 rounded">
                                        {{ $branch->businessUnit->code }}
                                    </span>
                                    <h4 class="text-base font-bold text-gray-900">{{ $branch->businessUnit->name }}</h4>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $branch->businessUnit->category ?? 'Commercial Business Unit' }} 
                                    @if($branch->businessUnit->cost_center)
                                        • Cost Center: <strong class="font-mono text-gray-700">{{ $branch->businessUnit->cost_center }}</strong>
                                    @endif
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $branch->businessUnit->status ? 'text-emerald-700 bg-emerald-100/70' : 'text-gray-600 bg-gray-100' }} px-2.5 py-1 rounded-full shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full {{ $branch->businessUnit->status ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $branch->businessUnit->status ? 'Division Active' : 'Division Suspended' }}
                            </span>
                        </div>

                        @if($branch->businessUnit->description)
                            <p class="mt-3 text-xs text-gray-600 italic border-t border-blue-100/80 pt-2.5">
                                "{{ $branch->businessUnit->description }}"
                            </p>
                        @endif

                        @if($branch->businessUnit->manager_name)
                            <div class="mt-3 pt-3 border-t border-blue-100/80 flex items-center justify-between text-xs text-gray-600">
                                <span>Division Executive: <strong class="text-gray-900">{{ $branch->businessUnit->manager_name }}</strong></span>
                                <span>{{ $branch->businessUnit->manager_phone ?? $branch->businessUnit->manager_email }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-6 bg-amber-50 rounded-xl border border-amber-200 text-amber-800">
                        <p class="text-sm font-semibold">No Parent Business Unit Linked</p>
                        <p class="text-xs mt-1 text-amber-700">This outlet needs to be mapped to a commercial division for revenue accounting.</p>
                        <a href="{{ route('management.branches.edit', $branch) }}" class="mt-3 inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-700">
                            + Map Business Unit Now
                        </a>
                    </div>
                @endif
            </div>

            <!-- Internal Functional Departments (if applicable) -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-6 sm:p-7">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Functional Departments & Counters</h3>
                        <p class="text-xs text-gray-500">Internal operational sections under this outlet location.</p>
                    </div>
                    <a href="{{ route('management.departments.create') }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-700">
                        + Add Department
                    </a>
                </div>

                @if($branch->departments && $branch->departments->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($branch->departments as $dept)
                            <div class="p-3.5 rounded-xl border border-gray-200/80 bg-slate-50/50 flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-bold text-gray-900">{{ $dept->name }}</h5>
                                    <p class="text-xs text-gray-500 font-mono">{{ $dept->department_id }}</p>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $dept->status ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $dept->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 border border-dashed border-gray-200 rounded-xl">
                        <p class="text-xs text-gray-400">No sub-departments configured yet.</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">This outlet currently functions as a direct unitary station point.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Leadership & Quick Controls (1 col) -->
        <div class="space-y-6">
            <!-- Leadership & Contact Information -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Outlet Leadership & Contacts
                </h3>

                <div class="text-center pb-4">
                    <div class="h-16 w-16 mx-auto rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-black text-xl mb-3 shadow-inner">
                        {{ $branch->manager_name ? substr($branch->manager_name, 0, 1) : 'S' }}
                    </div>
                    <h4 class="text-base font-bold text-gray-900">
                        {{ $branch->manager_name ?? 'Supervisor Unassigned' }}
                    </h4>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Station Manager / Lead Operator
                    </p>
                </div>

                <div class="space-y-3 pt-3 border-t border-gray-100 text-xs">
                    <div class="flex items-center gap-3 text-gray-600">
                        <div class="h-8 w-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                        </div>
                        <div class="truncate">
                            <span class="block text-[10px] uppercase font-semibold text-gray-400">Direct Telephone</span>
                            @if($branch->phone)
                                <a href="tel:{{ $branch->phone }}" class="font-medium text-blue-600 hover:text-blue-700">
                                    {{ $branch->phone }}
                                </a>
                            @else
                                <span class="text-gray-400 italic">No phone configured</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-gray-600">
                        <div class="h-8 w-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div class="truncate">
                            <span class="block text-[10px] uppercase font-semibold text-gray-400">Official Email</span>
                            @if($branch->email)
                                <a href="mailto:{{ $branch->email }}" class="font-medium text-blue-600 hover:text-blue-700 truncate">
                                    {{ $branch->email }}
                                </a>
                            @else
                                <span class="text-gray-400 italic">No email configured</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100">
                    <a href="{{ route('management.branches.edit', $branch) }}" class="w-full inline-flex items-center justify-center rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition-colors">
                        Update Supervisor Details
                    </a>
                </div>
            </div>

            <!-- Administrative Navigation & Actions -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-6">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                    Administrative Actions
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('management.branches.edit', $branch) }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 text-xs font-semibold text-gray-700 border border-transparent hover:border-gray-200 transition-all">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Edit Outlet Configuration
                        </span>
                        &rarr;
                    </a>

                    @if($branch->businessUnit)
                        <a href="{{ route('management.business-units.show', $branch->businessUnit) }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 text-xs font-semibold text-gray-700 border border-transparent hover:border-gray-200 transition-all">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                View Governing Unit ({{ $branch->businessUnit->code }})
                            </span>
                            &rarr;
                        </a>
                    @endif

                    <a href="{{ route('management.organization.branches') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 text-xs font-semibold text-gray-700 border border-transparent hover:border-gray-200 transition-all">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                            </svg>
                            All Branches Directory
                        </span>
                        &rarr;
                    </a>

                    <a href="{{ route('management.organization.structure') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 text-xs font-semibold text-gray-700 border border-transparent hover:border-gray-200 transition-all">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                            </svg>
                            Enterprise Hierarchy
                        </span>
                        &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

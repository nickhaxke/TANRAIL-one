@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">
                <a href="{{ route('management.organization.business-units') }}" class="hover:underline">Business Units Hub</a>
                <span>/</span>
                <span class="text-gray-400">{{ $businessUnit->code }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#0A1A2F]">{{ $businessUnit->name }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">
                    {{ $businessUnit->code }}
                </span>
                @if($businessUnit->status)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Active Division
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                        Inactive
                    </span>
                @endif
            </div>
            <p class="mt-1 text-sm text-gray-500">
                {{ $businessUnit->category ?? 'Commercial Business Division' }} &bull; Under <strong>{{ $businessUnit->organization->name ?? 'TANRAIL Investments Limited' }}</strong>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Toggle Status -->
            <form action="{{ route('management.business-units.toggle', $businessUnit) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-semibold shadow-xs ring-1 ring-inset transition-colors {{ $businessUnit->status ? 'bg-white text-gray-700 ring-gray-300 hover:bg-amber-50 hover:text-amber-700' : 'bg-emerald-600 text-white hover:bg-emerald-500' }}">
                    {{ $businessUnit->status ? 'Deactivate Division' : 'Activate Division' }}
                </button>
            </form>

            <a href="{{ route('management.business-units.edit', $businessUnit) }}" class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-xs ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                Edit Division
            </a>

            <a href="{{ route('management.branches.create', $businessUnit) }}" class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-md hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors shadow-blue-500/20">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Branch Outlet
            </a>
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

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-2xl p-5 shadow-xs ring-1 ring-gray-900/5">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Operating Outlets</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-black text-[#0A1A2F]">{{ $businessUnit->branches->count() }}</span>
                <span class="text-xs font-semibold text-emerald-600">({{ $businessUnit->branches->where('status', true)->count() }} Active)</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">Station counters and catering hubs</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-xs ring-1 ring-gray-900/5">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Cost Center</p>
            <p class="mt-2 text-2xl font-black font-mono text-[#0A1A2F] truncate">{{ $businessUnit->cost_center ?? 'CC-PENDING' }}</p>
            <p class="text-xs text-gray-500 mt-2">Ledger budgetary allocation code</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-xs ring-1 ring-gray-900/5">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Head of Unit</p>
            <p class="mt-2 text-lg font-bold text-gray-900 truncate">{{ $businessUnit->manager_name ?? 'Pending Nomination' }}</p>
            <p class="text-xs text-gray-500 mt-1 truncate">{{ $businessUnit->manager_email ?? 'No email configured' }}</p>
            @if($businessUnit->managerUser)
                <span class="inline-flex items-center gap-1 mt-2 text-[11px] font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-100">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    User: {{ $businessUnit->managerUser->name }}
                </span>
            @endif
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-xs ring-1 ring-gray-900/5">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Division Status</p>
            <div class="mt-2 flex items-center gap-2">
                <span class="w-3 h-3 rounded-full {{ $businessUnit->status ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                <span class="text-xl font-bold {{ $businessUnit->status ? 'text-emerald-700' : 'text-gray-600' }}">
                    {{ $businessUnit->status ? 'Operational' : 'Suspended' }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-2">Governed by Executive Admin</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left: Branches Directory Table (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white shadow-xs ring-1 ring-gray-900/5 rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Operating Outlets & Branches ({{ $businessUnit->branches->count() }})</h2>
                        <p class="text-xs text-gray-500">Retail points, station canteens, and on-board service centers mapped to this division.</p>
                    </div>
                    <a href="{{ route('management.branches.create', $businessUnit) }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-700">
                        + New Branch
                    </a>
                </div>

                @if($businessUnit->branches->isEmpty())
                <div class="text-center py-16 px-4">
                    <div class="mx-auto h-12 w-12 text-gray-300">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-sm font-bold text-gray-900">No Branches Configured Yet</h3>
                    <p class="mt-1 text-xs text-gray-500 max-w-sm mx-auto">
                        Add the first physical outlet or service counter under this business unit.
                    </p>
                    <div class="mt-5">
                        <a href="{{ route('management.branches.create', $businessUnit) }}" class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-blue-500">
                            + Add First Branch
                        </a>
                    </div>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/70">
                            <tr>
                                <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Branch / Outlet</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Facility Type</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Station / City</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($businessUnit->branches as $branch)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-lg flex items-center justify-center shrink-0 font-bold text-xs {{ $branch->status ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-400' }}">
                                            {{ substr($branch->code, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm text-gray-900">{{ $branch->name }}</div>
                                            <div class="text-xs font-mono text-gray-500">{{ $branch->code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs font-medium text-gray-700">
                                    {{ $branch->facility_type ?? 'Station Outlet' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs text-gray-500">
                                    {{ $branch->city ?? ($branch->address ?? 'Main Terminal') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($branch->status)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right text-xs font-medium space-x-2">
                                    <form action="{{ route('management.branches.toggle', $branch) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold shadow-xs ring-1 ring-inset transition-colors {{ $branch->status ? 'bg-white text-gray-700 ring-gray-300 hover:bg-amber-50 hover:text-amber-700' : 'bg-emerald-600 text-white hover:bg-emerald-500' }}">
                                            {{ $branch->status ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('management.branches.edit', $branch) }}" class="inline-flex items-center rounded-md bg-white px-2.5 py-1 text-xs font-semibold text-gray-700 shadow-xs ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <!-- Right: Division Profile & Details (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Mandate Card -->
            <div class="bg-white shadow-xs ring-1 ring-gray-900/5 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 pb-2 border-b border-gray-100">
                    Division Profile
                </h3>

                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase">Operational Mandate</label>
                    <p class="mt-1 text-xs text-gray-600 leading-relaxed">
                        {{ $businessUnit->description ?? 'No operational mandate description recorded for this division yet. You can add one via the Edit Division form.' }}
                    </p>
                </div>

                <div class="pt-3 border-t border-gray-100 space-y-2 text-xs">
                    <div>
                        <span class="text-gray-400 block">Parent Entity:</span>
                        <span class="font-semibold text-gray-900">{{ $businessUnit->organization->name ?? 'TANRAIL Investments Limited' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Established Date:</span>
                        <span class="font-medium text-gray-700">{{ $businessUnit->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Official Contact:</span>
                        <span class="font-medium text-gray-700">{{ $businessUnit->manager_email ?? 'N/A' }} {{ $businessUnit->manager_phone ? '('.$businessUnit->manager_phone.')' : '' }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Administrator Controls
                </h3>
                <div class="space-y-2 text-xs">
                    <a href="{{ route('management.branches.create', $businessUnit) }}" class="flex items-center justify-between p-3 rounded-xl bg-white/10 hover:bg-white/15 transition-colors">
                        <span class="font-semibold text-white">+ Create Branch for this Unit</span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('management.business-units.edit', $businessUnit) }}" class="flex items-center justify-between p-3 rounded-xl bg-white/10 hover:bg-white/15 transition-colors">
                        <span class="font-semibold text-white">Configure Division Settings</span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('management.organization.structure') }}" class="flex items-center justify-between p-3 rounded-xl bg-white/10 hover:bg-white/15 transition-colors">
                        <span class="font-semibold text-white">View in Hierarchy Tree</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

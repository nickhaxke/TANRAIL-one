@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-x-3">
                    <h1 class="text-xl font-bold text-gray-900">Branches & Outlets</h1>
                    <span class="inline-flex items-center rounded bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 border border-gray-200">
                        Operating Network
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Administration of station restaurants, catering hubs, and retail counters.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('management.organization.business-units') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-colors">
                    Business Units Hub
                </a>
                <a href="{{ route('management.organization.structure') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-colors">
                    Structure
                </a>
                <a href="{{ route('management.branches.create-direct') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition-colors border border-blue-600">
                    + Add Branch / Outlet
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4 border border-green-200 flex items-center gap-3">
        <svg class="h-5 w-5 text-green-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Outlets</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $branches->count() }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500">Operational network counters</p>
        </div>

        <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Active Outlets</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $branches->where('status', true)->count() }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500">Authorized for service</p>
        </div>

        <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Inactive Outlets</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $branches->where('status', false)->count() }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500">Suspended or under setup</p>
        </div>

        <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Parent Divisions</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $businessUnits->count() }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500">Commercial business unit parents</p>
        </div>
    </div>

    <!-- Branches Directory Container -->
    <div class="bg-white border border-gray-300 rounded-md shadow-sm overflow-hidden" 
         x-data="{ 
             search: '', 
             selectedBu: 'all', 
             selectedType: 'all', 
             selectedStatus: 'all',
             matches(name, code, buId, type, status, city, lead) {
                 const q = this.search.toLowerCase().trim();
                 const matchQuery = !q || name.includes(q) || code.includes(q) || city.includes(q) || lead.includes(q);
                 const matchBu = this.selectedBu === 'all' || buId === this.selectedBu;
                 const matchType = this.selectedType === 'all' || type === this.selectedType;
                 const matchStatus = this.selectedStatus === 'all' || status === this.selectedStatus;
                 return matchQuery && matchBu && matchType && matchStatus;
             }
         }">
        
        <!-- Filter Toolbar -->
        <div class="p-5 border-b border-gray-200 bg-gray-50 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Branches & Outlets Directory</h2>
                </div>

                <!-- Status Filter Tabs -->
                <div class="inline-flex rounded border border-gray-300 bg-white">
                    <button type="button" @click="selectedStatus = 'all'" :class="{ 'bg-gray-100 font-bold text-gray-900': selectedStatus === 'all', 'text-gray-600 hover:bg-gray-50 font-medium': selectedStatus !== 'all' }" class="px-3 py-1.5 text-xs transition-colors border-r border-gray-300">
                        All ({{ $branches->count() }})
                    </button>
                    <button type="button" @click="selectedStatus = 'active'" :class="{ 'bg-gray-100 font-bold text-gray-900': selectedStatus === 'active', 'text-gray-600 hover:bg-gray-50 font-medium': selectedStatus !== 'active' }" class="px-3 py-1.5 text-xs transition-colors border-r border-gray-300">
                        Active ({{ $branches->where('status', true)->count() }})
                    </button>
                    <button type="button" @click="selectedStatus = 'inactive'" :class="{ 'bg-gray-100 font-bold text-gray-900': selectedStatus === 'inactive', 'text-gray-600 hover:bg-gray-50 font-medium': selectedStatus !== 'inactive' }" class="px-3 py-1.5 text-xs transition-colors">
                        Inactive ({{ $branches->where('status', false)->count() }})
                    </button>
                </div>
            </div>

            <!-- Search & Dropdown Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Search Input -->
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" x-model="search" placeholder="Search outlet name, code..." class="block w-full rounded border border-gray-300 py-2 pl-9 pr-3 text-xs text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Business Unit Filter -->
                <div>
                    <select x-model="selectedBu" class="block w-full rounded border border-gray-300 py-2 pl-3 pr-8 text-xs text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="all">All Business Units</option>
                        @foreach($businessUnits as $bu)
                            <option value="{{ $bu->id }}">{{ $bu->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Facility Type Filter -->
                <div>
                    <select x-model="selectedType" class="block w-full rounded border border-gray-300 py-2 pl-3 pr-8 text-xs text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="all">All Facility Types</option>
                        <option value="Station Restaurant">Station Restaurant</option>
                        <option value="On-board Train Pantry">On-board Train Pantry</option>
                        <option value="Platform Retail Counter">Platform Retail Counter</option>
                        <option value="Central Production Hub">Central Production Hub</option>
                        <option value="Water Bottling Plant">Water Bottling Plant</option>
                        <option value="Regional Depot">Regional Depot</option>
                    </select>
                </div>
            </div>
        </div>

        @if($branches->isEmpty())
        <div class="text-center py-12 px-4">
            <h3 class="mt-2 text-sm font-semibold text-gray-900">No Branches Registered</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding your first outlet.</p>
            <div class="mt-4">
                <a href="{{ route('management.branches.create-direct') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Add Outlet
                </a>
            </div>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-white">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Branch / Outlet</th>
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Parent Division</th>
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Facility & Location</th>
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Station Lead</th>
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($branches as $branch)
                    <tr class="hover:bg-gray-50"
                        x-show="matches('{{ strtolower(addslashes($branch->name)) }}', '{{ strtolower(addslashes($branch->code)) }}', '{{ $branch->business_unit_id }}', '{{ addslashes($branch->facility_type ?? '') }}', '{{ $branch->status ? 'active' : 'inactive' }}', '{{ strtolower(addslashes($branch->city ?? '')) }}', '{{ strtolower(addslashes($branch->manager_name ?? '')) }}')">
                        
                        <!-- Outlet Name & Code -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-gray-100 rounded text-gray-600 flex items-center justify-center font-bold text-sm border border-gray-200 shrink-0">
                                    {{ substr($branch->code, 0, 2) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('management.branches.show', $branch) }}" class="text-sm font-semibold text-blue-600 hover:underline">
                                            {{ $branch->name }}
                                        </a>
                                        <span class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800 border border-gray-200">
                                            {{ $branch->code }}
                                        </span>
                                    </div>
                                    @if($branch->address)
                                        <div class="mt-1 text-[11px] text-gray-500 truncate max-w-[200px]" title="{{ $branch->address }}">
                                            {{ $branch->address }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Parent Division -->
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($branch->businessUnit)
                                <a href="{{ route('management.business-units.show', $branch->businessUnit) }}" class="text-blue-600 hover:underline font-medium">
                                    {{ $branch->businessUnit->name }}
                                </a>
                            @else
                                <span class="text-gray-400 italic">Unassigned</span>
                            @endif
                        </td>

                        <!-- Facility Type & City -->
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="text-gray-900 font-medium">{{ $branch->facility_type ?? 'Service Counter' }}</div>
                            @if($branch->city)
                                <div class="text-xs">{{ $branch->city }}</div>
                            @endif
                        </td>

                        <!-- Station Lead / Contact -->
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($branch->manager_name)
                                <div class="text-gray-900">{{ $branch->manager_name }}</div>
                                <div class="text-xs">{{ $branch->phone ?? $branch->email }}</div>
                            @else
                                <span class="text-gray-400 italic">Unassigned</span>
                            @endif
                        </td>

                        <!-- Operational Status -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($branch->status)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('management.branches.show', $branch) }}" class="text-blue-600 hover:text-blue-900 border border-gray-300 rounded px-2 py-1 hover:bg-gray-50 transition-colors">View</a>
                                
                                <form action="{{ route('management.branches.toggle', $branch) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-700 hover:text-gray-900 border border-gray-300 rounded px-2 py-1 hover:bg-gray-50 transition-colors">
                                        {{ $branch->status ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                
                                <a href="{{ route('management.branches.edit', $branch) }}" class="text-gray-700 hover:text-gray-900 border border-gray-300 rounded px-2 py-1 hover:bg-gray-50 transition-colors">Edit</a>
                                
                                <form action="{{ route('management.branches.destroy', $branch) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete branch {{ $branch->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 border border-gray-300 rounded px-2 py-1 hover:bg-gray-50 transition-colors">
                                        Delete
                                    </button>
                                </form>
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

@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-x-3">
                    <h1 class="text-xl font-bold text-gray-900">Business Units Hub</h1>
                    <span class="inline-flex items-center rounded bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 border border-gray-200">
                        {{ $organization->name ?? 'TANRAIL Investments Limited' }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Central management of commercial business units and branch networks.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('management.organization.branches') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-colors">
                    <svg class="-ml-0.5 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                    </svg>
                    View Branches
                </a>
                <a href="{{ route('management.business-units.create-direct') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition-colors border border-blue-600">
                    <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Business Unit
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4 border border-green-200 flex items-center gap-3">
        <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Business Units</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $businessUnits->count() }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500">Established under {{ $organization->code ?? 'TANRAIL' }}</p>
        </div>

        <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Active Units</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $businessUnits->where('status', true)->count() }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500">Currently operational</p>
        </div>

        <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Operating Branches</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $businessUnits->sum('branches_count') }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500">Total service outlets</p>
        </div>
    </div>

    <!-- Business Units Table -->
    <div class="bg-white border border-gray-300 rounded-md shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Registered Business Units</h2>
            </div>
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium text-gray-900">{{ $businessUnits->count() }}</span> units
            </div>
        </div>

        @if($businessUnits->isEmpty())
        <div class="text-center py-12 px-4">
            <h3 class="mt-2 text-sm font-semibold text-gray-900">No Business Units Found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first commercial division.</p>
            <div class="mt-4">
                <a href="{{ route('management.business-units.create-direct') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Add Business Unit
                </a>
            </div>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-white">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit Info</th>
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Branches</th>
                        <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($businessUnits as $unit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-gray-100 rounded text-gray-600 flex items-center justify-center font-bold text-sm border border-gray-200 shrink-0">
                                    {{ substr($unit->code, 0, 2) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('management.business-units.show', $unit) }}" class="text-sm font-semibold text-blue-600 hover:underline">
                                            {{ $unit->name }}
                                        </a>
                                        <span class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800 border border-gray-200">
                                            {{ $unit->code }}
                                        </span>
                                    </div>
                                    <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
                                        @if($unit->category)
                                            <span class="text-gray-700">{{ $unit->category }}</span>
                                        @endif
                                        @if($unit->cost_center)
                                            <span>| Cost Center: {{ $unit->cost_center }}</span>
                                        @endif
                                        @if($unit->manager_name)
                                            <span>| Lead: {{ $unit->manager_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm text-gray-900 font-medium">{{ $unit->branches->count() }} branches</span>
                                <a href="{{ route('management.branches.create', $unit) }}" class="text-xs text-blue-600 hover:underline inline-flex items-center">
                                    + Add branch
                                </a>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($unit->status)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('management.business-units.show', $unit) }}" class="text-blue-600 hover:text-blue-900 border border-gray-300 rounded px-2 py-1 hover:bg-gray-50 transition-colors">View</a>
                                
                                <form action="{{ route('management.business-units.toggle', $unit) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-700 hover:text-gray-900 border border-gray-300 rounded px-2 py-1 hover:bg-gray-50 transition-colors">
                                        {{ $unit->status ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                
                                <a href="{{ route('management.business-units.edit', $unit) }}" class="text-gray-700 hover:text-gray-900 border border-gray-300 rounded px-2 py-1 hover:bg-gray-50 transition-colors">Edit</a>
                                
                                <form action="{{ route('management.business-units.destroy', $unit) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete {{ $unit->name }}?');">
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

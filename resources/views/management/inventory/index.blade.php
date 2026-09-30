@extends('layouts.management')

@section('content')
<div>
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Inventory Management</h2>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0 gap-x-3">
            <a href="{{ route('management.inventory') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Live Stock Balances</a>
            <a href="{{ route('management.organization.branches') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Station Locations</a>
        </div>
    </div>

    <!-- Global Filters -->
    <div class="bg-white p-4 shadow sm:rounded-lg mb-8 border border-gray-100">
        <form method="GET" action="{{ route('management.inventory') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 items-end">
            <div>
                <label for="organization_id" class="block text-sm font-medium text-gray-700">Organization</label>
                <select id="organization_id" name="organization_id" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm shadow-sm ring-1 ring-inset ring-gray-300" onchange="this.form.submit()">
                    <option value="">All Organizations</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ $orgId == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="business_unit_id" class="block text-sm font-medium text-gray-700">Business Unit</label>
                <select id="business_unit_id" name="business_unit_id" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm shadow-sm ring-1 ring-inset ring-gray-300" onchange="this.form.submit()">
                    <option value="">All Business Units</option>
                    @foreach($businessUnits as $bu)
                        <option value="{{ $bu->id }}" {{ $buId == $bu->id ? 'selected' : '' }}>{{ $bu->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Inventory KPIs -->
    <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Total Inventory Value (Est.)</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-emerald-600">TZS {{ number_format($totalInventoryValue, 2) }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Active Locations</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ count($stockByLocation) }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Items Low on Stock</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-gray-900' }}">{{ $lowStockCount }}</dd>
        </div>
    </dl>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Stock Value by Location -->
        <div class="bg-white shadow sm:rounded-lg border border-gray-100 lg:col-span-1">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Value by Location</h3>
            </div>
            <ul role="list" class="divide-y divide-gray-100">
                @forelse($stockByLocation as $location)
                <li class="py-4 px-4 sm:px-6">
                    <p class="text-sm font-semibold text-gray-900">{{ $location->location_name }}</p>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-500">{{ $location->items_count }} items</p>
                        <p class="text-sm text-emerald-600 font-medium">TZS {{ number_format($location->total_value, 2) }}</p>
                    </div>
                </li>
                @empty
                <li class="py-4 px-6 text-sm text-gray-500 text-center">No active inventory locations.</li>
                @endforelse
            </ul>
        </div>

        <!-- Recent Stock Movements -->
        <div class="bg-white shadow sm:rounded-lg border border-gray-100 lg:col-span-2">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Recent Movements</h3>
            </div>
            <ul role="list" class="divide-y divide-gray-100">
                @forelse($recentMovements as $movement)
                <li class="flex items-center justify-between py-4 px-4 sm:px-6 hover:bg-gray-50">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold leading-6 text-gray-900">{{ $movement->item->name ?? 'Unknown Item' }}</p>
                        <p class="text-xs text-gray-500">{{ $movement->destinationLocation->name ?? $movement->sourceLocation->name ?? 'Unknown Location' }} &middot; {{ $movement->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex flex-none items-center gap-x-4">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $movement->quantity > 0 ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-red-50 text-red-700 ring-red-600/10' }}">
                            {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity, 2) }}
                        </span>
                        <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $movement->type->value ?? $movement->type)) }}</p>
                    </div>
                </li>
                @empty
                <li class="py-4 px-6 text-sm text-gray-500 text-center">No recent stock movements found.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

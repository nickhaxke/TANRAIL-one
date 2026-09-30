@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-x-3">
                    <h1 class="text-xl font-bold text-gray-900">Organization Structure</h1>
                    <span class="inline-flex items-center rounded bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 border border-gray-200">
                        Enterprise Topology
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Complete administrative topology from parent enterprise down to divisions and branches.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('management.business-units.create-direct') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-colors">
                    + New Business Unit
                </a>
                <a href="{{ route('management.branches.create-direct') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition-colors border border-blue-600">
                    + New Branch
                </a>
            </div>
        </div>
    </div>

    <!-- Hierarchy Container -->
    <div class="bg-white shadow-sm border border-gray-300 rounded-md p-6 sm:p-8">
        
        <!-- LEVEL 1: Enterprise Root -->
        <div class="max-w-2xl mx-auto text-center mb-8">
            <div class="inline-block w-full rounded-md bg-white border border-gray-300 p-6 shadow-sm relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
                    <div class="flex items-center gap-3 text-left">
                        <div class="h-10 w-10 rounded border border-gray-300 bg-gray-50 text-gray-900 flex items-center justify-center font-bold text-lg">
                            {{ substr($organization->code ?? 'T', 0, 1) }}
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-gray-500">ENTERPRISE ROOT &bull; LEVEL 1</span>
                            <h2 class="text-lg font-bold text-gray-900">{{ $organization->name }}</h2>
                        </div>
                    </div>
                    <a href="{{ route('management.settings.organization') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                        Edit Profile
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 text-xs text-left">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-gray-500 block">Entity Code:</span>
                        <span class="font-medium text-gray-900">{{ $organization->code }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-gray-500 block">Headquarters:</span>
                        <span class="font-medium text-gray-900">{{ $organization->city ?? 'Dar es Salaam' }}, {{ $organization->country ?? 'Tanzania' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-gray-500 block">Operating Scope:</span>
                        <span class="font-medium text-gray-900">{{ $organization->businessUnits->count() }} Units, {{ $organization->businessUnits->flatMap->branches->count() }} Branches</span>
                    </div>
                </div>
            </div>

            <!-- Visual Connecting Stem -->
            <div class="w-px h-8 bg-gray-300 mx-auto -mt-px relative z-0"></div>
            <div class="h-px w-3/4 max-w-lg bg-gray-300 mx-auto"></div>
        </div>

        <!-- LEVEL 2: Commercial Business Units -->
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase text-gray-500">LEVEL 2: COMMERCIAL BUSINESS DIVISIONS</span>
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                        {{ $organization->businessUnits->count() }} Registered
                    </span>
                </div>
                <a href="{{ route('management.organization.business-units') }}" class="text-xs font-medium text-blue-600 hover:underline">
                    View Directory &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-2">
                @forelse($organization->businessUnits as $bu)
                <div class="rounded-md border border-gray-300 bg-white p-5 shadow-sm flex flex-col relative">
                    <!-- Stem connector from top -->
                    <div class="absolute -top-6 left-1/2 w-px h-6 bg-gray-300"></div>

                    <!-- Unit Header -->
                    <div class="flex items-start justify-between gap-2 mb-4">
                        <div>
                            <span class="text-xs font-medium text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200">
                                {{ $bu->code }}
                            </span>
                            <h3 class="text-base font-bold text-gray-900 mt-1">{{ $bu->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $bu->category ?? 'Commercial Division' }}</p>
                        </div>
                        @if($bu->status)
                            <span class="inline-flex items-center rounded bg-green-100 px-2 py-0.5 text-[11px] font-medium text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-800">
                                Inactive
                            </span>
                        @endif
                    </div>

                    <!-- Unit Manager -->
                    <div class="p-3 rounded bg-gray-50 border border-gray-200 mb-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-6 rounded bg-gray-200 text-gray-600 font-bold text-xs flex items-center justify-center shrink-0">
                                {{ substr($bu->manager_name ?? ($bu->managerUser->name ?? 'M'), 0, 1) }}
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase text-gray-500 block">Unit Manager</span>
                                <p class="text-xs font-medium text-gray-900">
                                    {{ $bu->managerUser->name ?? ($bu->manager_name ?? 'Unassigned') }}
                                </p>
                            </div>
                        </div>
                        @if($bu->cost_center)
                            <span class="text-[10px] font-medium text-gray-600">
                                {{ $bu->cost_center }}
                            </span>
                        @endif
                    </div>

                    <!-- LEVEL 3: Branches -->
                    <div class="flex-grow flex flex-col">
                        <div class="flex items-center justify-between text-[11px] font-bold text-gray-500 mb-2">
                            <span>LEVEL 3: Outlets ({{ $bu->branches->count() }})</span>
                            <a href="{{ route('management.branches.create', $bu) }}" class="text-blue-600 hover:underline font-medium">
                                + Add
                            </a>
                        </div>

                        @if($bu->branches->isEmpty())
                            <div class="text-center py-3 bg-gray-50 rounded border border-dashed border-gray-300 text-gray-400 text-xs">
                                No branches assigned.
                            </div>
                        @else
                            <ul class="space-y-1.5 flex-grow">
                                @foreach($bu->branches as $branch)
                                <li class="flex items-center justify-between text-xs bg-white p-2 rounded border border-gray-200">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $branch->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                        <div class="truncate">
                                            <a href="{{ route('management.branches.show', $branch) }}" class="font-medium text-gray-900 hover:text-blue-600 truncate block">
                                                {{ $branch->name }}
                                            </a>
                                            <div class="text-[10px] text-gray-500 mt-0.5">
                                                {{ $branch->code }} @if($branch->city) &bull; {{ $branch->city }} @endif
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('management.branches.edit', $branch) }}" class="text-gray-400 hover:text-blue-600 p-1">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="mt-4 pt-3 border-t border-gray-200 flex items-center justify-between text-xs">
                        <a href="{{ route('management.business-units.show', $bu) }}" class="font-medium text-blue-600 hover:underline">
                            Overview
                        </a>
                        <a href="{{ route('management.business-units.edit', $bu) }}" class="font-medium text-gray-600 hover:underline">
                            Edit Unit
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-8 bg-gray-50 rounded border border-dashed border-gray-300">
                    <p class="text-sm font-medium text-gray-600">No divisions registered yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

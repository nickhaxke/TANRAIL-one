@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Corporate Header Banner -->
    <div class="bg-slate-800 border-b-4 border-blue-600 rounded-md p-6 text-white shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                <span>{{ $organization->name }}</span>
                <span>|</span>
                <span>Executive Dashboard</span>
            </div>
            <h1 class="text-2xl font-bold text-white">Administration Portal</h1>
            <p class="mt-1 text-sm text-slate-300 max-w-2xl">
                Central management system for enterprise operations, branches, and leadership assignments.
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('restaurant.pos') }}" class="inline-flex items-center gap-2 rounded-md bg-white text-slate-900 px-4 py-2 text-sm font-medium border border-gray-300 hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                POS Terminal
            </a>
            <a href="{{ route('management.business-units.create-direct') }}" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Business Unit
            </a>
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

    <!-- Core KPIs -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Business Units -->
        <div class="bg-white rounded-md p-5 border border-gray-300 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase text-gray-500">Commercial Units</span>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalBusinessUnits }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $activeBusinessUnits }} active</p>
                </div>
                <div class="h-10 w-10 bg-gray-100 rounded flex items-center justify-center text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Branches & Station Counters -->
        <div class="bg-white rounded-md p-5 border border-gray-300 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase text-gray-500">Branches & Outlets</span>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalBranches }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $activeBranches }} operational</p>
                </div>
                <div class="h-10 w-10 bg-gray-100 rounded flex items-center justify-center text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Staff & Personnel -->
        <div class="bg-white rounded-md p-5 border border-gray-300 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase text-gray-500">Total Staff</span>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalStaff }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $totalRoles }} access roles</p>
                </div>
                <div class="h-10 w-10 bg-gray-100 rounded flex items-center justify-center text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Regional Corridor Coverage -->
        <div class="bg-white rounded-md p-5 border border-gray-300 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase text-gray-500">Corridor Hubs</span>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $regionsCovered }}</p>
                    <p class="mt-1 text-xs text-gray-500">Regional coverage</p>
                </div>
                <div class="h-10 w-10 bg-gray-100 rounded flex items-center justify-center text-gray-600 border border-gray-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial & ERM Overviews -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Revenue -->
        <div class="bg-white rounded-md border border-gray-300 p-5 shadow-sm">
            <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Today's Total Revenue</h3>
            <div class="text-xl font-bold text-gray-900">TZS {{ number_format($todayRestaurantRevenue, 2) }}</div>
            <div class="mt-2 text-sm text-gray-600">
                Net Revenue: <strong>TZS {{ number_format($todayRestaurantNetProfit, 0) }}</strong>
            </div>
            <div class="text-xs text-gray-500 mt-1">Operating Expenses: TZS {{ number_format($todayRestaurantExpenses, 0) }}</div>
        </div>
        
        <!-- Procurement -->
        <div class="bg-white rounded-md border border-gray-300 p-5 shadow-sm">
            <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Procurement Spends</h3>
            <div class="text-xl font-bold text-gray-900">TZS {{ number_format($totalProcurementCommitted, 2) }}</div>
            <div class="mt-2 text-sm text-gray-600">Approved POs across divisions</div>
        </div>
        
        <!-- Inventory -->
        <div class="bg-white rounded-md border border-gray-300 p-5 shadow-sm">
            <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Total Stock Value</h3>
            <div class="text-xl font-bold text-gray-900">TZS {{ number_format($totalInventoryValue, 2) }}</div>
            <div class="mt-2 text-sm text-gray-600">Physical inventory across stores</div>
        </div>
    </div>

    <!-- Main Content 2-Column Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <!-- Left Column: Tables (2 cols) -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- Commercial Divisions Table -->
            <div class="bg-white rounded-md border border-gray-300 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-900">Commercial Divisions</h2>
                    <a href="{{ route('management.organization.business-units') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        View All
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th scope="col" class="py-3 px-5 text-left text-xs font-semibold text-gray-600 uppercase">Code</th>
                                <th scope="col" class="py-3 px-5 text-left text-xs font-semibold text-gray-600 uppercase">Division Name</th>
                                <th scope="col" class="py-3 px-5 text-left text-xs font-semibold text-gray-600 uppercase">Unit Manager</th>
                                <th scope="col" class="py-3 px-5 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($businessUnits as $bu)
                            <tr>
                                <td class="whitespace-nowrap py-3 px-5 text-sm font-medium text-gray-900">{{ $bu->code }}</td>
                                <td class="whitespace-nowrap py-3 px-5 text-sm text-gray-900">
                                    <a href="{{ route('management.business-units.show', $bu) }}" class="hover:underline text-blue-600">{{ $bu->name }}</a>
                                </td>
                                <td class="whitespace-nowrap py-3 px-5 text-sm text-gray-600">
                                    {{ $bu->managerUser->name ?? ($bu->manager_name ?? 'Unassigned') }}
                                </td>
                                <td class="whitespace-nowrap py-3 px-5 text-sm">
                                    @if($bu->status)
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Active</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500 text-sm">No business divisions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Railway Branches -->
            <div class="bg-white rounded-md border border-gray-300 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-900">Branches & Outlets</h2>
                    <a href="{{ route('management.organization.branches') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        View All
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th scope="col" class="py-3 px-5 text-left text-xs font-semibold text-gray-600 uppercase">Branch Name</th>
                                <th scope="col" class="py-3 px-5 text-left text-xs font-semibold text-gray-600 uppercase">Location</th>
                                <th scope="col" class="py-3 px-5 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                <th scope="col" class="py-3 px-5 text-left text-xs font-semibold text-gray-600 uppercase">Lead</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($recentBranches as $branch)
                            <tr>
                                <td class="whitespace-nowrap py-3 px-5 text-sm font-medium">
                                    <a href="{{ route('management.branches.show', $branch) }}" class="text-blue-600 hover:underline">{{ $branch->name }}</a>
                                </td>
                                <td class="whitespace-nowrap py-3 px-5 text-sm text-gray-600">{{ $branch->city ?? 'N/A' }}</td>
                                <td class="whitespace-nowrap py-3 px-5 text-sm text-gray-600">{{ $branch->facility_type ?? 'Outlet' }}</td>
                                <td class="whitespace-nowrap py-3 px-5 text-sm text-gray-600">{{ $branch->manager_name ?? 'Unassigned' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500 text-sm">No branches found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Info & Actions -->
        <div class="xl:col-span-1 space-y-6">
            
            <!-- Corporate Identity -->
            <div class="bg-white rounded-md border border-gray-300 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-3">Statutory Entity Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Name:</span>
                        <span class="font-medium text-gray-900">{{ $organization->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">TIN Number:</span>
                        <span class="font-medium text-gray-900">{{ $organization->tin_number ?? '108-342-880' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Registration:</span>
                        <span class="font-medium text-gray-900">{{ $organization->registration_number ?? '154872-TZ' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Headquarters:</span>
                        <span class="font-medium text-gray-900">{{ $organization->city ?? 'Dar es Salaam' }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('management.settings.organization') }}" class="text-sm text-blue-600 font-medium hover:underline">Edit Corporate Profile &rarr;</a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-md border border-gray-300 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-3">Quick Navigation</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('management.approvals') }}" class="text-blue-600 hover:underline">Pending Approvals ({{ $pendingApprovalsCount }})</a></li>
                    <li><a href="{{ route('management.organization.structure') }}" class="text-blue-600 hover:underline">View Organization Hierarchy</a></li>
                    <li><a href="{{ route('management.roles.index') }}" class="text-blue-600 hover:underline">Manage Staff Roles</a></li>
                    <li><a href="{{ route('management.settings.system') }}" class="text-blue-600 hover:underline">System Preferences</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

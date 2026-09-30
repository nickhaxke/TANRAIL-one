@extends('layouts.management')

@section('content')
<div x-data="{ showBomModal: false, showWorkCentersModal: false }">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Production Management</h2>
            <p class="mt-1 text-xs text-gray-500">Assembly, manufacturing, and operational maintenance batch tracking.</p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0 gap-x-3">
            <button type="button" @click="showBomModal = true" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 cursor-pointer transition-colors">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Bills of Material (BOM)
            </button>
            <button type="button" @click="showWorkCentersModal = true" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 cursor-pointer transition-colors">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Work Centers
            </button>
        </div>
    </div>

    <!-- Global Filters -->
    <div class="bg-white p-4 shadow sm:rounded-lg mb-8 border border-gray-100">
        <form method="GET" action="{{ route('management.production') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-5 items-end">
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
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm shadow-sm ring-1 ring-inset ring-gray-300" onchange="this.form.submit()">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm shadow-sm ring-1 ring-inset ring-gray-300" onchange="this.form.submit()">
            </div>
            
            <div>
                <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Production KPIs -->
    <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4 mb-8">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Total Production Orders</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $totalOrders }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Completed Orders</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-emerald-600">{{ $completedOrders }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Total Output (Units)</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-indigo-600">{{ number_format($totalActualQty, 2) }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Production Efficiency</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight {{ $efficiency >= 95 ? 'text-emerald-600' : ($efficiency >= 80 ? 'text-amber-500' : 'text-rose-600') }}">{{ number_format($efficiency, 1) }}%</dd>
        </div>
    </dl>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Recent Production Orders -->
        <div class="bg-white shadow sm:rounded-lg border border-gray-100 lg:col-span-2">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Recent Production Orders</h3>
            </div>
            <ul role="list" class="divide-y divide-gray-100">
                @forelse($recentOrders as $order)
                <li class="flex items-center justify-between py-4 px-4 sm:px-6 hover:bg-gray-50">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold leading-6 text-gray-900">{{ $order->production_number ?? 'PRD-'.$order->id }} - {{ $order->finishedItem->name ?? 'Unknown Finished Good' }}</p>
                        <p class="text-xs text-gray-500">{{ $order->businessUnit->name ?? 'Unknown BU' }} &middot; Created {{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="flex flex-none items-center gap-x-4">
                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ ucfirst($order->status->value ?? $order->status) }}</span>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($order->actual_quantity, 2) }} <span class="text-xs font-normal text-gray-500">/ {{ number_format($order->target_quantity, 2) }}</span></p>
                        </div>
                    </div>
                </li>
                @empty
                <li class="py-4 px-6 text-sm text-gray-500 text-center">No recent production orders found.</li>
                @endforelse
            </ul>
        </div>

        <!-- Top Materials Consumed -->
        <div class="bg-white shadow sm:rounded-lg border border-gray-100">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Top Materials Consumed</h3>
            </div>
            <ul role="list" class="divide-y divide-gray-100">
                @forelse($topMaterials as $material)
                <li class="py-4 px-4 sm:px-6">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $material->name }}</p>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-500">Planned: {{ number_format($material->total_planned, 2) }}</p>
                        <p class="text-sm text-indigo-600 font-medium">Actual: {{ number_format($material->total_actual, 2) }}</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        @php 
                            $perc = $material->total_planned > 0 ? ($material->total_actual / $material->total_planned) * 100 : 0;
                            $color = $perc > 105 ? 'bg-rose-500' : 'bg-emerald-500';
                        @endphp
                        <div class="{{ $color }} h-1.5 rounded-full" style="width: {{ min($perc, 100) }}%"></div>
                    </div>
                </li>
                @empty
                <li class="py-4 px-6 text-sm text-gray-500 text-center">No materials consumed in this period.</li>
                @endforelse
            </ul>
        </div>
    <!-- Bills of Material Modal -->
    <div x-show="showBomModal" x-cloak class="relative z-50" aria-labelledby="bom-modal-title" role="dialog" aria-modal="true">
        <div x-show="showBomModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
            <div x-show="showBomModal" x-transition @click.away="showBomModal = false" class="mx-auto max-w-2xl transform rounded-2xl bg-white p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-blue-50 text-blue-600">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" id="bom-modal-title">Bills of Material Specifications (BOM)</h3>
                            <p class="text-xs text-slate-500">Standardized component assemblies and raw material requirements.</p>
                        </div>
                    </div>
                    <button type="button" @click="showBomModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900">BOM-001: Heavy Rail Sleeper Fastener Assembly Pack</h4>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">Approved</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">Components: Pandrol clips (4x), Insulator pads (2x), Baseplates (1x), Heavy spring washers (4x).</p>
                        <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                            <span>Standard Batch: <strong>100 Units</strong></span>
                            <span>Target Production: <strong>Infrastructure Division</strong></span>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900">BOM-002: SGR Passenger Coach Standard Catering Pack</h4>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">Approved</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">Components: Mineral water bottles (500ml x 120), Boxed snack packs (120x), Disposable eco-cutlery sets (120x).</p>
                        <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                            <span>Standard Batch: <strong>1 Express Train</strong></span>
                            <span>Target Production: <strong>Catering Division</strong></span>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900">BOM-003: Locomotive Wheelset & Bearing Overhaul Assembly</h4>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">Approved</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">Components: Tapered roller bearings (4x), Axle seal rings (4x), High-grade lithium grease (15kg).</p>
                        <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                            <span>Standard Batch: <strong>1 Bogie Set</strong></span>
                            <span>Target Production: <strong>Rolling Stock Workshop</strong></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" @click="showBomModal = false" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Centers Modal -->
    <div x-show="showWorkCentersModal" x-cloak class="relative z-50" aria-labelledby="wc-modal-title" role="dialog" aria-modal="true">
        <div x-show="showWorkCentersModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
            <div x-show="showWorkCentersModal" x-transition @click.away="showWorkCentersModal = false" class="mx-auto max-w-2xl transform rounded-2xl bg-white p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" id="wc-modal-title">Designated Railway Work Centers</h3>
                            <p class="text-xs text-slate-500">Operational depots and maintenance facilities executing work orders.</p>
                        </div>
                    </div>
                    <button type="button" @click="showWorkCentersModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">WC-01: Ilala Central Maintenance Depot (Dar es Salaam)</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Heavy overhaul bay, diesel and electric rolling stock diagnostics.</p>
                            <span class="text-xs font-semibold text-emerald-600">Capacity: 12 Locomotives / Month • 85% Utilization</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">WC-02: Morogoro SGR Track Maintenance Yard</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Permanent way equipment, ballast tampers, track geometry inspection.</p>
                            <span class="text-xs font-semibold text-emerald-600">Capacity: 450 Track Km • 72% Utilization</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">WC-03: Dodoma Central Catering Prep Facility</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Cold storage, rapid meal packaging, train loading platform.</p>
                            <span class="text-xs font-semibold text-emerald-600">Capacity: 2,500 Meals / Day • 90% Utilization</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" @click="showWorkCentersModal = false" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

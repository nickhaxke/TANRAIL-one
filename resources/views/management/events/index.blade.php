@extends('layouts.management')

@section('content')
<div x-data="{ viewMode: 'list', showEventTypesModal: false }">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Event Management</h2>
            <p class="mt-1 text-xs text-gray-500">Corporate train charters, terminal event venues, and catering bookings.</p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0 gap-x-3">
            <button type="button" @click="viewMode = (viewMode === 'list' ? 'calendar' : 'list')" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 cursor-pointer transition-colors">
                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span x-text="viewMode === 'list' ? 'Switch to Agenda Calendar' : 'Switch to Table List'"></span>
            </button>
            <button type="button" @click="showEventTypesModal = true" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 cursor-pointer transition-colors">
                <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                Event Packages &amp; Types
            </button>
        </div>
    </div>

    <!-- Global Filters -->
    <div class="bg-white p-4 shadow sm:rounded-lg mb-8 border border-gray-100">
        <form method="GET" action="{{ route('management.events') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-5 items-end">
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

    <!-- Event KPIs -->
    <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4 mb-8">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Scheduled Events</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $upcomingEvents }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Confirmed Events</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-emerald-600">{{ $confirmedEvents }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Projected Revenue</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-indigo-600">TZS {{ number_format($totalRevenue, 2) }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100">
            <dt class="truncate text-sm font-medium text-gray-500">Collected Deposits</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-teal-600">TZS {{ number_format($totalDeposits, 2) }}</dd>
        </div>
    </dl>

    <div class="grid grid-cols-1 gap-8 mb-8">
        <!-- List View -->
        <div x-show="viewMode === 'list'" class="bg-white shadow sm:rounded-lg border border-gray-100">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Events in Period (Table View)</h3>
                <span class="text-xs text-gray-500">{{ count($recentEvents) }} Total Bookings</span>
            </div>
            <ul role="list" class="divide-y divide-gray-100">
                @forelse($recentEvents as $event)
                <li class="flex items-center justify-between py-4 px-4 sm:px-6 hover:bg-gray-50">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold leading-6 text-gray-900">{{ $event->event_name }} ({{ $event->booking_number }})</p>
                        <p class="text-xs text-gray-500">{{ $event->customer->name ?? 'Unknown Customer' }} &middot; {{ $event->venue_location }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ Carbon\Carbon::parse($event->start_date_time)->format('M d, Y g:i A') }}</p>
                    </div>
                    <div class="flex flex-none items-center gap-x-4">
                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ ucfirst($event->status->value ?? $event->status) }}</span>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">TZS {{ number_format($event->total_amount, 2) }}</p>
                            <p class="text-xs text-emerald-600">Dep: TZS {{ number_format($event->deposit_paid_amount, 2) }}</p>
                        </div>
                    </div>
                </li>
                @empty
                <li class="py-4 px-6 text-sm text-gray-500 text-center">No events found in this period.</li>
                @endforelse
            </ul>
        </div>

        <!-- Agenda Calendar Cards View -->
        <div x-show="viewMode === 'calendar'" x-cloak class="bg-white shadow sm:rounded-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Agenda Calendar Schedule</h3>
                    <p class="text-xs text-slate-500">Chronological timeline of confirmed and planned bookings.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">Agenda Mode</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($recentEvents as $event)
                <div class="p-4 rounded-xl border border-slate-200 hover:shadow-md transition-shadow bg-slate-50/50">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-mono font-bold text-blue-600">{{ $event->booking_number }}</span>
                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">{{ ucfirst($event->status->value ?? $event->status) }}</span>
                    </div>
                    <h4 class="mt-2 text-sm font-bold text-slate-900">{{ $event->event_name }}</h4>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $event->venue_location }}</p>
                    <div class="mt-3 pt-3 border-t border-slate-200/80 flex items-center justify-between text-xs">
                        <span class="text-slate-600">{{ Carbon\Carbon::parse($event->start_date_time)->format('d M Y') }}</span>
                        <span class="font-bold text-slate-900">TZS {{ number_format($event->total_amount, 0) }}</span>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-10 text-center text-xs text-slate-400">No scheduled calendar events found in this date filter.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Event Packages & Types Modal -->
    <div x-show="showEventTypesModal" x-cloak class="relative z-50" aria-labelledby="event-types-modal-title" role="dialog" aria-modal="true">
        <div x-show="showEventTypesModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
            <div x-show="showEventTypesModal" x-transition @click.away="showEventTypesModal = false" class="mx-auto max-w-2xl transform rounded-2xl bg-white p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-purple-50 text-purple-600">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" id="event-types-modal-title">TANRAIL Event Types &amp; Service Packages</h3>
                            <p class="text-xs text-slate-500">Official catalog of railway corporate charters and venue hires.</p>
                        </div>
                    </div>
                    <button type="button" @click="showEventTypesModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900">1. SGR Executive Charter Train</h4>
                            <span class="text-xs font-mono font-bold text-indigo-700">From TZS 25,000,000</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">Exclusive high-speed train booking between Dar es Salaam and Dodoma for corporate conferences, state delegations, and group summits.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900">2. Station Terminal Concourse &amp; Platform Venue</h4>
                            <span class="text-xs font-mono font-bold text-indigo-700">From TZS 5,000,000 / Day</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">Modern architectural event spaces at Dar es Salaam Central (Tanzanite Station) and Dodoma Central for corporate product launches and exhibitions.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900">3. Private Dining &amp; Banquet Train Car</h4>
                            <span class="text-xs font-mono font-bold text-indigo-700">From TZS 4,500,000</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">Dedicated restaurant carriage hire with chef catering, sound systems, and panoramic scenery for private celebrations and executive luncheons.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" @click="showEventTypesModal = false" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

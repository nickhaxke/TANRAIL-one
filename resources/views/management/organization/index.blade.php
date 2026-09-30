@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-gray-300 rounded-md p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-x-2 text-xs font-medium text-gray-500 mb-1">
                    <span>Corporate Administration</span>
                    <span>/</span>
                    <span>Enterprise Profile</span>
                </div>
                <div class="flex items-center gap-x-3">
                    <h1 class="text-xl font-bold text-gray-900">TANRAIL Corporate Profile</h1>
                    <span class="inline-flex items-center rounded bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 border border-gray-200">
                        TRC Subsidiary
                    </span>
                    <span class="inline-flex items-center rounded bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 border border-green-200">
                        Operational Active
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Statutory corporate identity, legal incorporation details, and administrative governance.
                </p>
            </div>
            <div>
                <a href="{{ route('management.settings.organization') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition-colors border border-blue-600">
                    Edit Corporate Profile
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4 border border-green-200 flex items-center gap-3 shadow-sm">
        <svg class="h-5 w-5 text-green-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Corporate Identity & Legal Standing Card -->
    <div class="bg-white shadow-sm rounded-md border border-gray-300">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-gray-200">
                <div class="flex items-start gap-4">
                    <!-- Standard Corporate Badge -->
                    <div class="h-16 w-16 rounded border border-gray-300 bg-gray-50 text-gray-900 flex flex-col items-center justify-center shrink-0">
                        <span class="text-xl font-bold">{{ substr($organization->code ?? $organization->name, 0, 2) }}</span>
                        <span class="text-[10px] font-bold text-gray-500 mt-0.5">TRC</span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl font-bold text-gray-900">{{ $organization->name }}</h2>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                {{ $organization->code ?? 'TANRAIL' }}
                            </span>
                        </div>
                        <p class="text-sm font-medium text-gray-700">
                            {{ $organization->trading_name ?? 'TANRAIL' }} &bull; {{ $organization->industry ?? 'Railway Commercial Services & Catering' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Commercial Subsidiary of Tanzania Railways Corporation (TRC) &bull; Primary Enterprise Identity
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap lg:flex-col items-start lg:items-end gap-2">
                    <a href="{{ route('management.settings.organization') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-medium text-gray-700 bg-gray-50 border border-gray-300 hover:bg-gray-100 transition-colors">
                        Update Information
                    </a>
                </div>
            </div>

            <!-- Structured Statutory Details Grid -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Statutory Tax ID -->
                <div class="space-y-1">
                    <span class="text-[11px] font-bold uppercase text-gray-500 block">
                        TRA Taxpayer ID (TIN)
                    </span>
                    <p class="font-mono font-bold text-gray-900 text-sm">{{ $organization->tin_number ?? '108-342-880' }}</p>
                    <p class="text-[11px] text-gray-500">Tanzania Revenue Authority</p>
                </div>

                <!-- BRELA Incorporation -->
                <div class="space-y-1 border-l border-gray-200 pl-6">
                    <span class="text-[11px] font-bold uppercase text-gray-500 block">
                        BRELA Reg. Certificate
                    </span>
                    <p class="font-mono font-bold text-gray-900 text-sm">{{ $organization->registration_number ?? '154872-TZ' }}</p>
                    <p class="text-[11px] text-gray-500">Registrar of Companies</p>
                </div>

                <!-- Headquarters Location -->
                <div class="space-y-1 border-l border-gray-200 pl-6">
                    <span class="text-[11px] font-bold uppercase text-gray-500 block">
                        Headquarters Base
                    </span>
                    <p class="font-bold text-gray-900 text-sm">{{ $organization->city ?? 'Dar es Salaam' }}, {{ $organization->country ?? 'Tanzania' }}</p>
                    <p class="text-[11px] text-gray-500 truncate" title="{{ $organization->address ?? 'TRC Headquarters Building, 4th Floor, Sokoine Drive' }}">
                        {{ $organization->address ?? 'TRC Headquarters Building' }}
                    </p>
                </div>

                <!-- Official Communication -->
                <div class="space-y-1 border-l border-gray-200 pl-6">
                    <span class="text-[11px] font-bold uppercase text-gray-500 block">
                        Official Contacts
                    </span>
                    <p class="font-mono font-medium text-gray-900 text-sm">{{ $organization->phone ?? '+255 22 211 0599' }}</p>
                    <p class="text-[11px] text-blue-600 truncate">{{ $organization->email ?? 'info@tanrail.co.tz' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Administrative Structure Metrics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <!-- Business Units -->
        <div class="bg-white shadow-sm rounded-md border border-gray-300 p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase text-gray-500">Commercial Divisions</span>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $organization->businessUnits->count() }}</p>
                <p class="text-[11px] text-gray-500 mt-1">
                    {{ $organization->businessUnits->where('status', true)->count() }} active operational units
                </p>
            </div>
        </div>

        <!-- Branches & Outlets -->
        <div class="bg-white shadow-sm rounded-md border border-gray-300 p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase text-gray-500">Branches & Outlets</span>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $organization->businessUnits->flatMap->branches->count() }}</p>
                <p class="text-[11px] text-gray-500 mt-1">
                    {{ $organization->businessUnits->flatMap->branches->where('status', true)->count() }} operational stations
                </p>
            </div>
        </div>

        <!-- Regional Jurisdictions -->
        <div class="bg-white shadow-sm rounded-md border border-gray-300 p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase text-gray-500">Corridor Coverage</span>
                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ max(1, $organization->businessUnits->flatMap->branches->pluck('city')->filter()->unique()->count()) }}
                </p>
                <p class="text-[11px] text-gray-500 mt-1">
                    Railway corridor regions
                </p>
            </div>
        </div>
    </div>

    <!-- Administrative Hub Navigation -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <a href="{{ route('management.organization.business-units') }}" class="group bg-white rounded-md border border-gray-300 p-5 shadow-sm hover:bg-gray-50 transition-colors block">
            <h3 class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Business Units Hub</h3>
            <p class="text-[11px] text-gray-500 mt-0.5">Commercial division governance</p>
            <div class="mt-4 text-xs font-medium text-blue-600">
                Manage Business Units &rarr;
            </div>
        </a>

        <a href="{{ route('management.organization.branches') }}" class="group bg-white rounded-md border border-gray-300 p-5 shadow-sm hover:bg-gray-50 transition-colors block">
            <h3 class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Branches & Outlets</h3>
            <p class="text-[11px] text-gray-500 mt-0.5">Stations, counters & logistics</p>
            <div class="mt-4 text-xs font-medium text-blue-600">
                Manage Branches &rarr;
            </div>
        </a>

        <a href="{{ route('management.organization.structure') }}" class="group bg-white rounded-md border border-gray-300 p-5 shadow-sm hover:bg-gray-50 transition-colors block">
            <h3 class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Organization Structure</h3>
            <p class="text-[11px] text-gray-500 mt-0.5">Corporate hierarchy & tree</p>
            <div class="mt-4 text-xs font-medium text-blue-600">
                View Structure Topology &rarr;
            </div>
        </a>
    </div>

    <!-- Official Corporate Document Letterhead Specimen -->
    <div class="bg-white rounded-md shadow-sm border border-gray-300 overflow-hidden">
        <div class="flex items-center justify-between p-4 sm:p-5 border-b border-gray-200 bg-gray-50">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Official Document Letterhead Format</h3>
                <p class="text-[11px] text-gray-500">Standardized identity printed atop purchase orders, invoices, and governance documents.</p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-white border border-gray-300 text-gray-600 uppercase tracking-widest shadow-sm">
                ERP SPECIMEN
            </span>
        </div>

        <!-- The Letterhead Paper Simulation -->
        <div class="p-8 sm:p-12 bg-white flex justify-center">
            <div class="w-full max-w-2xl border-b-2 border-black pb-6 text-center space-y-2 relative">
                <!-- Formal Corporate Name -->
                <h4 class="text-lg sm:text-xl font-serif font-bold uppercase tracking-widest text-black">{{ strtoupper($organization->name) }}</h4>
                
                <!-- Subtitle / Association -->
                <div class="text-[10px] font-serif uppercase tracking-widest text-gray-600 mb-4">
                    Subsidiary of Tanzania Railways Corporation
                </div>

                <!-- Contact & Address Block -->
                <p class="text-xs text-black font-medium mt-4">{{ $organization->address ?? 'TRC Headquarters Building, 4th Floor, Sokoine Drive' }}</p>
                <p class="text-[11px] text-black">
                    {{ $organization->postal_code ?? 'P.O. Box 468' }}, {{ $organization->city ?? 'Dar es Salaam' }} &bull; Tel: {{ $organization->phone ?? '+255 22 211 0599' }} &bull; Email: {{ $organization->email ?? 'info@tanrail.co.tz' }}
                </p>

                <!-- Statutory Registration Details -->
                <div class="pt-3 mt-3 border-t border-gray-300 max-w-md mx-auto">
                    <p class="text-[10px] font-bold text-gray-800 tracking-wider">
                        TIN: {{ $organization->tin_number ?? '108-342-880' }} &nbsp;&nbsp;|&nbsp;&nbsp; BRELA REG: {{ $organization->registration_number ?? '154872-TZ' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

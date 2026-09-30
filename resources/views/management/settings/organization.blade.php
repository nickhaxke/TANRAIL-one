@extends('layouts.management')

@section('content')
<div class="space-y-6" x-data="{
    legalName: '{{ old('legal_name', $organization?->name ?? 'TANRAIL Investments Limited') }}',
    tradingName: '{{ old('trading_name', $organization?->trading_name ?? 'TANRAIL') }}',
    code: '{{ old('code', $organization?->code ?? 'TANRAIL') }}',
    tinNumber: '{{ old('tin_number', $organization?->tin_number ?? '108-342-880') }}',
    registrationNumber: '{{ old('registration_number', $organization?->registration_number ?? '154872-TZ') }}',
    industry: '{{ old('industry', $organization?->industry ?? 'Railway Commercial Services & Catering') }}',
    country: '{{ old('country', $organization?->country ?? 'Tanzania') }}',
    city: '{{ old('city', $organization?->city ?? 'Dar es Salaam') }}',
    address: '{{ old('address', $organization?->address ?? 'TRC Headquarters Building, 4th Floor, Sokoine Drive') }}',
    postalCode: '{{ old('postal_code', $organization?->postal_code ?? 'P.O. Box 468') }}',
    phone: '{{ old('phone', $organization?->phone ?? '+255 22 211 0599') }}',
    email: '{{ old('email', $organization?->email ?? 'info@tanrail.co.tz') }}',
    website: '{{ old('website', $organization?->website ?? 'www.tanrail.co.tz') }}',
}">
    <!-- Executive Header Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A8A] p-6 sm:p-7 text-white shadow-xl shadow-blue-950/10 border border-blue-900/30">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-300/90 mb-1.5">
                    <a href="{{ route('management.organization.index') }}" class="hover:text-white transition-colors flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Organization Profile
                    </a>
                    <span>/</span>
                    <span class="text-blue-200">Corporate Governance Settings</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">TANRAIL Corporate Profile</h1>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        TRC Subsidiary
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-blue-200/80">
                    Maintain legal incorporation credentials, statutory tax registration, and primary headquarters for <strong class="text-white font-semibold">{{ $organization?->name ?? 'TANRAIL Investments Limited' }}</strong>.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('management.organization.index') }}" class="inline-flex items-center rounded-xl bg-white/10 backdrop-blur-md px-4 py-2.5 text-xs font-bold text-white border border-white/20 hover:bg-white/20 transition-all shadow-sm">
                    &larr; View Profile Overview
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-200 flex items-center gap-3 shadow-xs">
        <svg class="h-5 w-5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Form & Preview Grid (md:grid-cols-12 for laptop responsiveness) -->
    <form id="org-settings-form" action="{{ route('management.settings.organization.update') }}" method="POST" autocomplete="off" class="grid grid-cols-1 md:grid-cols-12 gap-6">
        @csrf

        <!-- Left Column: Settings Form Fields (7 cols on md, 8 cols on xl) -->
        <div class="md:col-span-7 xl:col-span-8 space-y-6">
            
            <!-- Section 1: Statutory Identity & Legal Registration -->
            <div class="bg-white shadow-xs rounded-2xl border border-slate-200/90 p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0 border border-blue-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Statutory Legal Identity & Registration</h2>
                        <p class="text-xs text-slate-500">Official registered corporate title, BRELA certificates, and TRA tax credentials.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Legal Name -->
                    <div class="sm:col-span-2">
                        <label for="legal_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Official Legal Entity Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                </svg>
                            </div>
                            <input type="text" name="legal_name" id="legal_name" x-model="legalName" autocomplete="off" placeholder="TANRAIL Investments Limited" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm font-semibold text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all" required>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Printed on official tax commercial invoices, procurement contracts, and banking documents.</p>
                        @error('legal_name')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Trading / Brand Name -->
                    <div>
                        <label for="trading_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Trade / Commercial Brand Name
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                </svg>
                            </div>
                            <input type="text" name="trading_name" id="trading_name" x-model="tradingName" autocomplete="off" placeholder="TANRAIL" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm font-semibold text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>

                    <!-- Organization Code -->
                    <div>
                        <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            System Entity Code
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <span class="font-mono font-bold text-xs">#</span>
                            </div>
                            <input type="text" name="code" id="code" x-model="code" autocomplete="off" placeholder="TANRAIL" class="block w-full rounded-xl border border-slate-300 pl-8 pr-3.5 py-2.5 text-sm font-mono font-bold text-slate-900 uppercase bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>

                    <!-- Tax Identification Number (TIN) -->
                    <div>
                        <label for="tin_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            TIN Number (TRA Statutory Tax ID)
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <input type="text" name="tin_number" id="tin_number" x-model="tinNumber" autocomplete="off" placeholder="108-342-880" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm font-mono font-bold text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Tanzania Revenue Authority registered taxpayer ID.</p>
                    </div>

                    <!-- BRELA Certificate of Incorporation Number -->
                    <div>
                        <label for="reg_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            BRELA Certificate / Reg. No.
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                </svg>
                            </div>
                            <input type="text" name="registration_number" id="reg_number" x-model="registrationNumber" autocomplete="off" placeholder="154872-TZ" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm font-mono font-bold text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Registrar of Companies certificate number.</p>
                    </div>

                    <!-- Sector / Industry Mandate -->
                    <div class="sm:col-span-2">
                        <label for="industry" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Core Industry & Commercial Mandate
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <select id="industry" name="industry" x-model="industry" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                                <option value="Railway Commercial Services & Catering">Railway Commercial Services & Catering</option>
                                <option value="On-board Passenger Hospitality & Logistics">On-board Passenger Hospitality & Logistics</option>
                                <option value="Station Retail Concessions & Real Estate">Station Retail Concessions & Real Estate</option>
                                <option value="Multi-Modal Railway Logistics & Freight Support">Multi-Modal Railway Logistics & Freight Support</option>
                                <option value="Diversified Enterprise">Diversified Commercial Enterprise</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Corporate Headquarters & Regional Base -->
            <div class="bg-white shadow-xs rounded-2xl border border-slate-200/90 p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shrink-0 border border-indigo-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Headquarters & Geographic Location</h2>
                        <p class="text-xs text-slate-500">Official executive headquarters address and regional jurisdiction.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Address -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Headquarters Physical Address
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>
                            <input type="text" name="address" id="address" x-model="address" autocomplete="off" placeholder="TRC Headquarters Building, 4th Floor, Sokoine Drive" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium">
                        </div>
                    </div>

                    <!-- City / Region Dropdown (Standard Tanzanian Regions) -->
                    <div>
                        <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Headquarters City / Region <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <select name="city" id="city" x-model="city" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                                <optgroup label="TRC Railway Corridors & Major Hubs">
                                    <option value="Dar es Salaam">Dar es Salaam (Executive HQ & Port)</option>
                                    <option value="Dodoma">Dodoma (Capital / SGR Central Hub)</option>
                                    <option value="Morogoro">Morogoro (SGR Major Hub)</option>
                                    <option value="Tabora">Tabora (MTR Central Junction)</option>
                                    <option value="Mwanza">Mwanza (Lake Victoria Terminal)</option>
                                    <option value="Kigoma">Kigoma (Lake Tanganyika Terminal)</option>
                                    <option value="Tanga">Tanga (Northern Line Terminal)</option>
                                    <option value="Arusha">Arusha</option>
                                    <option value="Kilimanjaro">Kilimanjaro</option>
                                </optgroup>
                                <optgroup label="Other Administrative Regions">
                                    <option value="Geita">Geita</option>
                                    <option value="Iringa">Iringa</option>
                                    <option value="Kagera">Kagera</option>
                                    <option value="Mbeya">Mbeya</option>
                                    <option value="Mtwara">Mtwara</option>
                                    <option value="Pwani">Pwani</option>
                                    <option value="Shinyanga">Shinyanga</option>
                                    <option value="Singida">Singida</option>
                                    <option value="Zanzibar">Zanzibar</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <!-- Postal Code / Box -->
                    <div>
                        <label for="postal_code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Postal Address / P.O. Box
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <input type="text" name="postal_code" id="postal_code" x-model="postalCode" autocomplete="off" placeholder="P.O. Box 468" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>

                    <!-- Country -->
                    <div class="sm:col-span-2">
                        <label for="country" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Sovereign Country of Origin
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <select id="country" name="country" x-model="country" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                                <option value="Tanzania">Tanzania (United Republic of Tanzania)</option>
                                <option value="East African Community">East African Community Corridor</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Official Communication & Digital Presence -->
            <div class="bg-white shadow-xs rounded-2xl border border-slate-200/90 p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center font-bold shrink-0 border border-violet-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Official Communication & Web Channels</h2>
                        <p class="text-xs text-slate-500">Corporate executive telephone, public registry email, and official portal.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Telephone -->
                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Executive Telephone
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </div>
                            <input type="text" name="phone" id="phone" x-model="phone" autocomplete="off" placeholder="+255 22 211 0599" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Official Inquiries Email
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" x-model="email" autocomplete="off" placeholder="info@tanrail.co.tz" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>

                    <!-- Website -->
                    <div class="sm:col-span-2">
                        <label for="website" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Corporate Portal / Website
                        </label>
                        <div class="relative rounded-xl shadow-2xs flex">
                            <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 px-3.5 text-slate-500 font-mono text-xs">
                                https://
                            </span>
                            <input type="text" name="website" id="website" x-model="website" autocomplete="off" placeholder="www.tanrail.co.tz" class="block w-full rounded-r-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex items-center justify-end gap-x-4 bg-white shadow-xs rounded-2xl border border-slate-200/90 p-4 sm:p-5">
                <a href="{{ route('management.organization.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all shadow-blue-500/25 active:scale-98 cursor-pointer">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Save Corporate Profile
                </button>
            </div>
        </div>

        <!-- Right Column: Live Interactive Corporate Card & Letterhead Preview (5 cols on md, 4 cols on xl) -->
        <div class="md:col-span-5 xl:col-span-4 space-y-6">
            <div class="sticky top-20 space-y-5">
                <!-- Preview Header Pill -->
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Live Corporate Preview</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        Real-time Identity
                    </span>
                </div>

                <!-- Executive Corporate Identity Card -->
                <div class="rounded-2xl bg-gradient-to-br from-[#0A1A2F] via-[#112642] to-[#1E3A8A] text-white p-6 shadow-xl shadow-blue-950/20 border border-blue-900/40 relative overflow-hidden space-y-5">
                    <!-- Subtle Background Seal -->
                    <div class="absolute -right-6 -bottom-6 w-36 h-36 rounded-full bg-blue-500/10 pointer-events-none blur-xl"></div>

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 flex items-center justify-center font-black text-xl shadow-lg shadow-amber-500/20">
                                <span x-text="code ? code.substring(0, 1) : 'T'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-amber-300/90 block">Corporate Identity</span>
                                <h3 class="text-base font-extrabold text-white leading-tight" x-text="tradingName ? tradingName : 'TANRAIL'"></h3>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-blue-200 border border-white/15">
                            <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Verified
                        </span>
                    </div>

                    <div class="pt-2">
                        <p class="text-sm font-bold text-white leading-snug" x-text="legalName ? legalName : 'TANRAIL Investments Limited'"></p>
                        <p class="text-xs text-blue-200/80 mt-1" x-text="industry ? industry : 'Railway Commercial Services'"></p>
                    </div>

                    <div class="pt-4 border-t border-blue-800/40 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-blue-200/70">
                            <span>TIN Number:</span>
                            <span class="font-mono font-bold text-amber-300" x-text="tinNumber ? tinNumber : 'Not Set'"></span>
                        </div>
                        <div class="flex items-center justify-between text-blue-200/70">
                            <span>BRELA Reg:</span>
                            <span class="font-mono font-semibold text-white" x-text="registrationNumber ? registrationNumber : 'Not Set'"></span>
                        </div>
                        <div class="flex items-center justify-between text-blue-200/70">
                            <span>Jurisdiction:</span>
                            <span class="font-medium text-white" x-text="(city ? city : 'Dar es Salaam') + ', ' + (country ? country : 'Tanzania')"></span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-blue-800/40 flex items-center justify-between text-[11px] text-blue-300/80">
                        <span class="truncate max-w-[160px]" x-text="email ? email : 'info@tanrail.co.tz'"></span>
                        <span class="font-mono" x-text="phone ? phone : '+255 22 211 0599'"></span>
                    </div>
                </div>

                <!-- Invoice & Letterhead Header Preview -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5 space-y-3">
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Official Document Header Preview</span>
                        <span class="text-[10px] font-mono text-slate-400">INVOICE / PO</span>
                    </div>
                    
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900" x-text="legalName ? legalName.toUpperCase() : 'TANRAIL INVESTMENTS LIMITED'"></h4>
                        <p class="text-[11px] text-slate-600 font-medium" x-text="address ? address : 'TRC Headquarters Building, Sokoine Drive'"></p>
                        <p class="text-[10px] text-slate-500 font-mono">
                            <span x-text="postalCode ? postalCode : 'P.O. Box 468'"></span>, <span x-text="city ? city : 'Dar es Salaam'"></span> &bull; Tel: <span x-text="phone ? phone : '+255 22 211 0599'"></span>
                        </p>
                        <p class="text-[10px] font-bold text-blue-600 font-mono pt-1">
                            TIN: <span x-text="tinNumber ? tinNumber : '108-342-880'"></span> &bull; VAT REG: <span x-text="registrationNumber ? registrationNumber : '154872-TZ'"></span>
                        </p>
                    </div>
                    <p class="text-[11px] text-slate-400 text-center">Appears atop all commercial invoices, supplier orders, and payment receipts.</p>
                </div>

                <!-- Strategic Architecture Summary -->
                <div class="bg-blue-50/70 rounded-2xl p-4 sm:p-5 border border-blue-200/80 space-y-2">
                    <div class="flex items-center gap-2 text-blue-900 font-bold text-xs">
                        <svg class="h-4 w-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <span>Enterprise Governance Scope</span>
                    </div>
                    <p class="text-xs text-blue-800/80 leading-relaxed">
                        TANRAIL operates as the primary commercial investment subsidiary of Tanzania Railways Corporation (TRC), managing retail stations, train catering hubs, and logistics.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

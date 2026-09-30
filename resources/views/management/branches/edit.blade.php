@extends('layouts.management')

@section('content')
<div class="space-y-6" x-data="{
    name: '{{ old('name', $branch->name) }}',
    code: '{{ old('code', $branch->code) }}',
    facilityType: '{{ old('facility_type', $branch->facility_type ?? 'Station Restaurant') }}',
    city: '{{ old('city', $branch->city ?? 'Dar es Salaam') }}',
    address: '{{ old('address', $branch->address) }}',
    managerUserId: '{{ old('manager_user_id', $branch->manager_user_id) }}',
    managerName: '{{ old('manager_name', $branch->manager_name) }}',
    phone: '{{ old('phone', $branch->phone) }}',
    email: '{{ old('email', $branch->email) }}',
    status: {{ old('status', $branch->status) ? 'true' : 'false' }},
    selectedBuName: '{{ $branch->businessUnit->name ?? 'Division' }}',
    users: {{ Js::from($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])) }},
    onManagerChange() {
        if (!this.managerUserId) {
            return;
        }
        const user = this.users.find(u => u.id == this.managerUserId);
        if (user) {
            this.managerName = user.name;
            this.email = user.email;
        }
    }
}">
    <!-- Executive Header Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A8A] p-6 sm:p-7 text-white shadow-xl shadow-blue-950/10 border border-blue-900/30">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-300/90 mb-1.5">
                    <a href="{{ route('management.organization.branches') }}" class="hover:text-white transition-colors flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Branches & Outlets
                    </a>
                    <span>/</span>
                    <a href="{{ route('management.branches.show', $branch) }}" class="text-blue-200 hover:text-white">{{ $branch->code }}</a>
                    <span>/</span>
                    <span class="text-blue-300">Settings</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Edit Operating Outlet</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-white/10 text-white border border-white/20">
                        {{ $branch->code }}
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-blue-200/80">
                    Modify branch configuration, physical station location, and operational contacts for <strong class="text-white font-semibold">{{ $branch->name }}</strong>.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('management.branches.show', $branch) }}" class="inline-flex items-center rounded-xl bg-white/10 backdrop-blur-md px-4 py-2.5 text-xs font-bold text-white border border-white/20 hover:bg-white/20 transition-all shadow-sm">
                    Branch Overview &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Form & Preview Grid (md:grid-cols-12 for laptop responsiveness) -->
    <form action="{{ route('management.branches.update', $branch) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        <!-- Left Column: Form Fields (7 cols on md, 8 cols on xl) -->
        <div class="md:col-span-7 xl:col-span-8 space-y-6">
            <!-- Section 1: Parent Division & Identification -->
            <div class="bg-white shadow-xs rounded-2xl border border-slate-200/90 p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shrink-0 border border-indigo-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Parent Division & Outlet Identity</h2>
                        <p class="text-xs text-slate-500">Commercial trade title, parent division mapping, and classification.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Parent Business Unit Selection -->
                    <div class="sm:col-span-2">
                        <label for="business_unit_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Parent Commercial Division <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <select name="business_unit_id" id="business_unit_id" @change="selectedBuName = $event.target.options[$event.target.selectedIndex].text" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium">
                                @foreach($businessUnits as $bu)
                                    <option value="{{ $bu->id }}" {{ old('business_unit_id', $branch->business_unit_id) == $bu->id ? 'selected' : '' }}>
                                        {{ $bu->name }} ({{ $bu->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Branch / Outlet Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                                </svg>
                            </div>
                            <input type="text" name="name" id="name" x-model="name" value="{{ old('name', $branch->name) }}" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium" required>
                        </div>
                        @error('name')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Branch Code <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <span class="font-mono font-bold text-xs">#</span>
                            </div>
                            <input type="text" name="code" id="code" x-model="code" value="{{ old('code', $branch->code) }}" class="block w-full rounded-xl border border-slate-300 pl-8 pr-3.5 py-2.5 text-sm font-mono font-bold text-slate-900 uppercase bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all" required>
                        </div>
                        @error('code')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Facility Type -->
                    <div>
                        <label for="facility_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Facility Classification
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <select name="facility_type" id="facility_type" x-model="facilityType" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium">
                                <option value="Station Restaurant" {{ old('facility_type', $branch->facility_type) == 'Station Restaurant' ? 'selected' : '' }}>Station Restaurant</option>
                                <option value="On-board Train Pantry" {{ old('facility_type', $branch->facility_type) == 'On-board Train Pantry' ? 'selected' : '' }}>On-board Train Pantry</option>
                                <option value="Platform Retail Counter" {{ old('facility_type', $branch->facility_type) == 'Platform Retail Counter' ? 'selected' : '' }}>Platform Retail Counter</option>
                                <option value="Central Production Hub" {{ old('facility_type', $branch->facility_type) == 'Central Production Hub' ? 'selected' : '' }}>Central Production Hub</option>
                                <option value="Water Bottling Plant" {{ old('facility_type', $branch->facility_type) == 'Water Bottling Plant' ? 'selected' : '' }}>Water Bottling Plant</option>
                                <option value="Regional Depot" {{ old('facility_type', $branch->facility_type) == 'Regional Depot' ? 'selected' : '' }}>Regional Depot</option>
                                <option value="Other Service Point" {{ old('facility_type', $branch->facility_type) == 'Other Service Point' ? 'selected' : '' }}>Other Service Point</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Physical Station Location -->
            <div class="bg-white shadow-xs rounded-2xl border border-slate-200/90 p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0 border border-blue-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Physical Station Location</h2>
                        <p class="text-xs text-slate-500">Railway station, regional city, and physical station address.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- City / Region Dropdown (Standard Tanzanian Regions) -->
                    <div class="sm:col-span-2">
                        <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Location / Region (City) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>
                            <select name="city" id="city" x-model="city" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium">
                                <optgroup label="TRC Railway Corridors & Key Hubs">
                                    @php
                                        $currentCity = old('city', $branch->city);
                                    @endphp
                                    <option value="Dar es Salaam" {{ $currentCity == 'Dar es Salaam' ? 'selected' : '' }}>Dar es Salaam (Main Terminal / Port / TRC HQ)</option>
                                    <option value="Dodoma" {{ $currentCity == 'Dodoma' ? 'selected' : '' }}>Dodoma (Capital / SGR Central Hub)</option>
                                    <option value="Morogoro" {{ $currentCity == 'Morogoro' ? 'selected' : '' }}>Morogoro (SGR Major Stop)</option>
                                    <option value="Tabora" {{ $currentCity == 'Tabora' ? 'selected' : '' }}>Tabora (MTR Central Railway Junction)</option>
                                    <option value="Mwanza" {{ $currentCity == 'Mwanza' ? 'selected' : '' }}>Mwanza (Lake Victoria Hub)</option>
                                    <option value="Kigoma" {{ $currentCity == 'Kigoma' ? 'selected' : '' }}>Kigoma (Lake Tanganyika Terminal)</option>
                                    <option value="Tanga" {{ $currentCity == 'Tanga' ? 'selected' : '' }}>Tanga (Northern Line Terminal)</option>
                                    <option value="Kilimanjaro" {{ $currentCity == 'Kilimanjaro' ? 'selected' : '' }}>Kilimanjaro / Moshi</option>
                                    <option value="Arusha" {{ $currentCity == 'Arusha' ? 'selected' : '' }}>Arusha (Northern Tourist Hub)</option>
                                    <option value="Singida" {{ $currentCity == 'Singida' ? 'selected' : '' }}>Singida (Central Line)</option>
                                    <option value="Shinyanga" {{ $currentCity == 'Shinyanga' ? 'selected' : '' }}>Shinyanga (Mwanza Line Junction)</option>
                                    <option value="Katavi" {{ $currentCity == 'Katavi' ? 'selected' : '' }}>Katavi (Mpanda Line)</option>
                                </optgroup>
                                <optgroup label="Other Administrative Regions (Tanzania)">
                                    <option value="Geita" {{ $currentCity == 'Geita' ? 'selected' : '' }}>Geita</option>
                                    <option value="Iringa" {{ $currentCity == 'Iringa' ? 'selected' : '' }}>Iringa</option>
                                    <option value="Kagera" {{ $currentCity == 'Kagera' ? 'selected' : '' }}>Kagera (Bukoba)</option>
                                    <option value="Lindi" {{ $currentCity == 'Lindi' ? 'selected' : '' }}>Lindi</option>
                                    <option value="Manyara" {{ $currentCity == 'Manyara' ? 'selected' : '' }}>Manyara (Babati)</option>
                                    <option value="Mara" {{ $currentCity == 'Mara' ? 'selected' : '' }}>Mara (Musoma)</option>
                                    <option value="Mbeya" {{ $currentCity == 'Mbeya' ? 'selected' : '' }}>Mbeya</option>
                                    <option value="Mtwara" {{ $currentCity == 'Mtwara' ? 'selected' : '' }}>Mtwara</option>
                                    <option value="Njombe" {{ $currentCity == 'Njombe' ? 'selected' : '' }}>Njombe</option>
                                    <option value="Pwani" {{ $currentCity == 'Pwani' ? 'selected' : '' }}>Pwani (Coast / Kibaha / Ruvu)</option>
                                    <option value="Rukwa" {{ $currentCity == 'Rukwa' ? 'selected' : '' }}>Rukwa (Sumbawanga)</option>
                                    <option value="Ruvuma" {{ $currentCity == 'Ruvuma' ? 'selected' : '' }}>Ruvuma (Songea)</option>
                                    <option value="Simiyu" {{ $currentCity == 'Simiyu' ? 'selected' : '' }}>Simiyu</option>
                                    <option value="Songwe" {{ $currentCity == 'Songwe' ? 'selected' : '' }}>Songwe</option>
                                    <option value="Zanzibar" {{ $currentCity == 'Zanzibar' ? 'selected' : '' }}>Zanzibar / Pemba</option>
                                </optgroup>
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Standardized Tanzanian administrative region.</p>
                        @error('city')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Physical Address -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Physical Address & Station Details
                        </label>
                        <textarea name="address" id="address" x-model="address" rows="2" class="block w-full rounded-xl border border-slate-300 p-3 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">{{ old('address', $branch->address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Leadership & Governance -->
            <div class="bg-white shadow-xs rounded-2xl border border-slate-200/90 p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center font-bold shrink-0 border border-violet-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Station Supervisor & Contacts</h2>
                        <p class="text-xs text-slate-500">Designated lead operator, direct phone, and official outlet email.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Assign Supervisor Dropdown (Automates Supervisor Name & Email) -->
                    <div class="sm:col-span-2">
                        <label for="manager_user_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Assign Station Supervisor / Manager (Registered User)
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <select name="manager_user_id" id="manager_user_id" x-model="managerUserId" @change="onManagerChange()" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium">
                                <option value="">-- Manual / External Supervisor Nomination --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('manager_user_id', $branch->manager_user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} &bull; {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Selecting a user auto-populates supervisor credentials and assigns outlet oversight.</p>
                        @error('manager_user_id')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Manager Name -->
                    <div class="sm:col-span-2">
                        <label for="manager_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Supervisor Display Name
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <input type="text" name="manager_name" id="manager_name" x-model="managerName" value="{{ old('manager_name', $branch->manager_name) }}" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium">
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Auto-filled from assigned user or custom supervisor.</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Direct Telephone
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </div>
                            <input type="text" name="phone" id="phone" x-model="phone" value="{{ old('phone', $branch->phone) }}" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Station Email
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" x-model="email" value="{{ old('email', $branch->email) }}" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Operational Status -->
            <div class="bg-white shadow-xs rounded-2xl border border-slate-200/90 p-5 sm:p-7">
                <div class="flex items-center justify-between gap-4 p-4 rounded-xl border border-slate-200 bg-slate-50/70">
                    <div>
                        <span class="text-sm font-bold text-slate-900 block">Active Operational Status</span>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Controls whether this outlet can process sales and receive inventory stock.
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" name="status" value="1" x-model="status" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex items-center justify-end gap-x-4 bg-white shadow-xs rounded-2xl border border-slate-200/90 p-4 sm:p-5">
                <a href="{{ route('management.organization.branches') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all shadow-blue-500/25 active:scale-98 cursor-pointer">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Update Branch Details
                </button>
            </div>
        </div>

        <!-- Right Column: Live Interactive Preview (5 cols on md, 4 cols on xl) -->
        <div class="md:col-span-5 xl:col-span-4 space-y-6">
            <div class="sticky top-20 space-y-5">
                <!-- Preview Header Pill -->
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Live Outlet Preview</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                        Real-time
                    </span>
                </div>

                <!-- Outlet Card Mockup -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-5 sm:p-6 space-y-4 overflow-hidden relative transition-all duration-300">
                    <div class="absolute top-0 left-0 right-0 h-2" :class="status ? 'bg-gradient-to-r from-indigo-600 to-blue-600' : 'bg-slate-300'"></div>

                    <div class="flex items-start justify-between gap-3 pt-1">
                        <div class="h-12 w-12 rounded-xl flex items-center justify-center font-black text-base shadow-sm text-white transition-colors" :class="status ? 'bg-indigo-600 shadow-indigo-500/30' : 'bg-slate-400'">
                            <span x-text="code ? code.substring(0, 2).toUpperCase() : 'BR'"></span>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset" :class="status ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-100 text-slate-600 ring-slate-500/20'">
                                <span class="w-1.5 h-1.5 rounded-full" :class="status ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                <span x-text="status ? 'Active' : 'Inactive'"></span>
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-100 text-slate-700 uppercase" x-text="code ? code.toUpperCase() : 'CODE-PENDING'"></span>
                        <h3 class="text-base font-bold text-slate-900 mt-1.5" x-text="name ? name : 'New Branch Name'"></h3>
                        <p class="text-xs text-indigo-600 font-semibold mt-0.5" x-text="facilityType"></p>
                    </div>

                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed" x-text="address ? address : 'Physical platform or station location...'"></p>

                    <div class="pt-3.5 border-t border-slate-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Parent Division:</span>
                            <span class="font-semibold text-blue-700 truncate max-w-[150px]" x-text="selectedBuName"></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Station City:</span>
                            <span class="font-semibold text-slate-800" x-text="city ? city : 'Not set'"></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Supervisor:</span>
                            <span class="font-semibold text-slate-800" x-text="managerName ? managerName : 'Pending Nomination'"></span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                        <span>Management: <strong>Assigned</strong></span>
                        <a href="{{ route('management.branches.show', $branch) }}" class="text-blue-600 font-semibold hover:underline">View details &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

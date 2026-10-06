@extends('layouts.management')

@section('content')
<div class="space-y-6" x-data="{
    name: '{{ old('name', $businessUnit->name) }}',
    code: '{{ old('code', $businessUnit->code) }}',
    category: '{{ old('category', $businessUnit->category ?? 'Restaurant & Food Services') }}',
    description: '{{ old('description', $businessUnit->description) }}',
    costCenter: '{{ old('cost_center', $businessUnit->cost_center) }}',
    managerUserId: '{{ old('manager_user_id', $businessUnit->manager_user_id) }}',
    managerName: '{{ old('manager_name', $businessUnit->manager_name) }}',
    managerEmail: '{{ old('manager_email', $businessUnit->manager_email) }}',
    managerPhone: '{{ old('manager_phone', $businessUnit->manager_phone) }}',
    status: {{ old('status', $businessUnit->status) ? 'true' : 'false' }},
    users: {{ Js::from($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])) }},
    onManagerChange() {
        if (!this.managerUserId) {
            return;
        }
        const user = this.users.find(u => u.id == this.managerUserId);
        if (user) {
            this.managerName = user.name;
            this.managerEmail = user.email;
        }
    }
}">
    <!-- Executive Header Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A8A] p-6 sm:p-7 text-white shadow-xl shadow-blue-950/10 border border-blue-900/30">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-300/90 mb-1.5">
                    <a href="{{ route('management.organization.business-units') }}" class="hover:text-white transition-colors flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Business Units Hub
                    </a>
                    <span>/</span>
                    <a href="{{ route('management.business-units.show', $businessUnit) }}" class="text-blue-200 hover:text-white">{{ $businessUnit->code }}</a>
                    <span>/</span>
                    <span class="text-blue-300">Settings</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Edit Commercial Division</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-white/10 text-white border border-white/20">
                        {{ $businessUnit->code }}
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-blue-200/80">
                    Update administrative identity, governance, and operational parameters for <strong class="text-white font-semibold">{{ $businessUnit->name }}</strong>.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('management.business-units.show', $businessUnit) }}" class="inline-flex items-center rounded-xl bg-white/10 backdrop-blur-md px-4 py-2.5 text-xs font-bold text-white border border-white/20 hover:bg-white/20 transition-all shadow-sm">
                    Division Overview &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Form & Preview Grid (md:grid-cols-12 for laptop responsiveness) -->
    <form action="{{ route('management.business-units.update', $businessUnit) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        <!-- Left Column: Form Fields (7 cols on md, 8 cols on xl) -->
        <div class="md:col-span-7 xl:col-span-8 space-y-6">
            <!-- Section 1: Core Division Identity -->
            <div class="bg-white shadow-xs rounded-2xl border border-slate-200/90 p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0 border border-blue-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Division Identity & Classification</h2>
                        <p class="text-xs text-slate-500">Commercial trade title, classification category, and system abbreviation code.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Business Unit Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                </svg>
                            </div>
                            <input type="text" name="name" id="name" x-model="name" value="{{ old('name', $businessUnit->name) }}" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium" required>
                        </div>
                        @error('name')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Division Code (Unique Identifier) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <span class="font-mono font-bold text-xs">#</span>
                            </div>
                            <input type="text" name="code" id="code" x-model="code" value="{{ old('code', $businessUnit->code) }}" class="block w-full rounded-xl border border-slate-300 pl-8 pr-3.5 py-2.5 text-sm font-mono font-bold text-slate-900 uppercase bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all" required>
                        </div>
                        @error('code')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Category / Sector -->
                    <div>
                        <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Operational Sector / Category
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <select name="category" id="category" x-model="category" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium">
                                <option value="Restaurant & Food Services" {{ old('category', $businessUnit->category) == 'Restaurant & Food Services' ? 'selected' : '' }}>Restaurant & Food Services</option>
                                <option value="On-board Train Catering" {{ old('category', $businessUnit->category) == 'On-board Train Catering' ? 'selected' : '' }}>On-board Train Catering</option>
                                <option value="Station Kiosks & Retail" {{ old('category', $businessUnit->category) == 'Station Kiosks & Retail' ? 'selected' : '' }}>Station Kiosks & Retail</option>
                                <option value="Commercial Services & Facilities" {{ old('category', $businessUnit->category) == 'Commercial Services & Facilities' ? 'selected' : '' }}>Commercial Services & Facilities</option>
                                <option value="Facilities Management" {{ old('category', $businessUnit->category) == 'Facilities Management' ? 'selected' : '' }}>Facilities Management</option>
                                <option value="Cleaning Operations" {{ old('category', $businessUnit->category) == 'Cleaning Operations' ? 'selected' : '' }}>Cleaning Operations</option>
                                <option value="Logistics & Supply Chain" {{ old('category', $businessUnit->category) == 'Logistics & Supply Chain' ? 'selected' : '' }}>Logistics & Supply Chain</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Operational Mandate & Description
                        </label>
                        <textarea name="description" id="description" x-model="description" rows="3" class="block w-full rounded-xl border border-slate-300 p-3 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">{{ old('description', $businessUnit->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Administrative Governance & Cost Center -->
            <div class="bg-white shadow-xs rounded-2xl border border-slate-200/90 p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shrink-0 border border-indigo-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Governance Leadership & Cost Allocation</h2>
                        <p class="text-xs text-slate-500">Nominate executive management and assign general ledger cost center.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Assign Manager Dropdown (Automates Manager Name & Email) -->
                    <div class="sm:col-span-2">
                        <label for="manager_user_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Assign Unit Head / General Manager (Registered User)
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <select name="manager_user_id" id="manager_user_id" x-model="managerUserId" @change="onManagerChange()" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium">
                                <option value="">-- Manual / External Manager Nomination --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('manager_user_id', $businessUnit->manager_user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} &bull; {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Selecting an existing user auto-fills manager credentials and assigns administrative oversight.</p>
                        @error('manager_user_id')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Manager Name -->
                    <div>
                        <label for="manager_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Unit Head Display Name
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <input type="text" name="manager_name" id="manager_name" x-model="managerName" value="{{ old('manager_name', $businessUnit->manager_name) }}" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all font-medium">
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Auto-filled from selected user or custom external name.</p>
                    </div>

                    <!-- Cost Center -->
                    <div>
                        <label for="cost_center" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Financial Cost Center Code
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="text" name="cost_center" id="cost_center" x-model="costCenter" value="{{ old('cost_center', $businessUnit->cost_center) }}" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm font-mono font-bold text-slate-900 uppercase bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>

                    <!-- Manager Email -->
                    <div>
                        <label for="manager_email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Official Email Address
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <input type="email" name="manager_email" id="manager_email" x-model="managerEmail" value="{{ old('manager_email', $businessUnit->manager_email) }}" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>

                    <!-- Manager Phone -->
                    <div>
                        <label for="manager_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Contact Phone Number
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </div>
                            <input type="text" name="manager_phone" id="manager_phone" x-model="managerPhone" value="{{ old('manager_phone', $businessUnit->manager_phone) }}" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Operational Status -->
            <div class="bg-white shadow-xs rounded-2xl border border-slate-200/90 p-5 sm:p-7">
                <div class="flex items-center justify-between gap-4 p-4 rounded-xl border border-slate-200 bg-slate-50/70">
                    <div>
                        <span class="text-sm font-bold text-slate-900 block">Active Operational Status</span>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Controls whether this unit can create branches, record expenses, or trade.
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
                <a href="{{ route('management.business-units.show', $businessUnit) }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all shadow-blue-500/25 active:scale-98 cursor-pointer">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Update Business Unit
                </button>
            </div>
        </div>

        <!-- Right Column: Live Interactive Preview (5 cols on md, 4 cols on xl) -->
        <div class="md:col-span-5 xl:col-span-4 space-y-6">
            <div class="sticky top-20 space-y-5">
                <!-- Preview Header Pill -->
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Live Card Preview</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        Real-time
                    </span>
                </div>

                <!-- Division Card Mockup -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-5 sm:p-6 space-y-4 overflow-hidden relative transition-all duration-300">
                    <div class="absolute top-0 left-0 right-0 h-2" :class="status ? 'bg-gradient-to-r from-blue-600 to-indigo-600' : 'bg-slate-300'"></div>

                    <div class="flex items-start justify-between gap-3 pt-1">
                        <div class="h-12 w-12 rounded-xl flex items-center justify-center font-black text-base shadow-sm text-white transition-colors" :class="status ? 'bg-blue-600 shadow-blue-500/30' : 'bg-slate-400'">
                            <span x-text="code ? code.substring(0, 2).toUpperCase() : 'BU'"></span>
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
                        <h3 class="text-base font-bold text-slate-900 mt-1.5" x-text="name ? name : 'New Division Title'"></h3>
                        <p class="text-xs text-blue-600 font-semibold mt-0.5" x-text="category"></p>
                    </div>

                    <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed" x-text="description ? description : 'No division mandate description entered yet.'"></p>

                    <div class="pt-3.5 border-t border-slate-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Cost Center:</span>
                            <span class="font-mono font-semibold text-slate-800" x-text="costCenter ? costCenter : 'Not Assigned'"></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Head of Unit:</span>
                            <span class="font-semibold text-slate-800" x-text="managerName ? managerName : 'Pending Nomination'"></span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                        <span>Branches: <strong>{{ $businessUnit->branches->count() }} Outlets</strong></span>
                        <a href="{{ route('management.business-units.show', $businessUnit) }}" class="text-blue-600 font-semibold hover:underline">View details &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

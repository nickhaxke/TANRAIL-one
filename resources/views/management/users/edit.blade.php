@extends('layouts.management')

@section('content')
@php
    $currentScopeType = 'organization';
    $currentScopeId = $organizations->first()?->id ?? 1;
    $currentScopeTitle = $organizations->first()?->name ?? 'TANRAIL';

    if (isset($activeRole) && $activeRole->pivot) {
        if (str_contains($activeRole->pivot->scope_type, 'BusinessUnit')) {
            $currentScopeType = 'business_unit';
            $currentScopeId = $activeRole->pivot->scope_id;
            $bu = $businessUnits->firstWhere('id', $currentScopeId);
            $currentScopeTitle = $bu ? $bu->name : 'Business Unit #'.$currentScopeId;
        } elseif (str_contains($activeRole->pivot->scope_type, 'Branch')) {
            $currentScopeType = 'branch';
            $currentScopeId = $activeRole->pivot->scope_id;
            $br = $branches->firstWhere('id', $currentScopeId);
            $currentScopeTitle = $br ? $br->name : 'Branch #'.$currentScopeId;
        } else {
            $currentScopeType = 'organization';
            $currentScopeId = $activeRole->pivot->scope_id;
            $currentScopeTitle = $organizations->first()?->name ?? 'TANRAIL';
        }
    }
@endphp

<div class="space-y-6" x-data="{
    name: '{{ old('name', $user->name) }}',
    email: '{{ old('email', $user->email) }}',
    roleId: '{{ old('role_id', $activeRole?->id ?? '') }}',
    roleName: '{{ $activeRole?->name ?? '' }}',
    scopeType: '{{ old('scope_type', $currentScopeType) }}',
    scopeId: '{{ old('scope_id', $currentScopeId) }}',
    scopeTitle: '{{ $currentScopeTitle }}',
    updateRole(el) {
        this.roleName = el.options[el.selectedIndex]?.text || '';
    },
    updateScopeTitle(el) {
        this.scopeTitle = el.options[el.selectedIndex]?.text || '';
    }
}">
    <!-- Executive Header Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A8A] p-6 sm:p-7 text-white shadow-xl shadow-blue-950/10 border border-blue-900/30">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-300/90 mb-1.5">
                    <a href="{{ route('management.users.index') }}" class="hover:text-white transition-colors flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Staff Directory
                    </a>
                    <span>/</span>
                    <span class="text-blue-200">Personnel Configuration</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Edit Staff: {{ $user->name }}</h1>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                        Profile & Roles
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-blue-200/80">
                    Modify personnel profile credentials, access roles, or reassign operational station jurisdiction.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('management.users.index') }}" class="inline-flex items-center rounded-xl bg-white/10 backdrop-blur-md px-4 py-2.5 text-xs font-bold text-white border border-white/20 hover:bg-white/20 transition-all shadow-sm">
                    &larr; Back to Directory
                </a>
            </div>
        </div>
    </div>

    <!-- Form & Preview Grid -->
    <form action="{{ route('management.users.update', $user) }}" method="POST" autocomplete="off" class="grid grid-cols-1 md:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        <!-- Left: Form Fields (7 cols md / 8 cols xl) -->
        <div class="md:col-span-7 xl:col-span-8 space-y-6">
            
            <!-- Section 1: Personal & Account Credentials -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0 border border-blue-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Personnel Identity & Credentials</h2>
                        <p class="text-xs text-slate-500">Update official name and login credentials.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Full Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Full Official Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <input type="text" name="name" id="name" x-model="name" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm font-semibold text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all" required>
                        </div>
                        @error('name')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Official Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" x-model="email" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm font-semibold text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all" required>
                        </div>
                        @error('email')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Password (Optional on update) -->
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            New Password <span class="text-slate-400 font-normal">(leave blank to keep current)</span>
                        </label>
                        <input type="password" name="password" id="password" placeholder="Leave empty to retain" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                        @error('password')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Confirm New Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Repeat new password" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Role & Jurisdiction Scope -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shrink-0 border border-indigo-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-2.18-4.965a4.5 4.5 0 016.36 6.36m-6.36-6.36a4.5 4.5 0 00-6.36 6.36m6.36-6.36l6.36 6.36" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Institutional Role & Operational Scope</h2>
                        <p class="text-xs text-slate-500">Determine access permissions and the structural branch/division governed.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Role Dropdown -->
                    <div class="sm:col-span-2">
                        <label for="role_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Designated Role / Title
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <select id="role_id" name="role_id" x-model="roleId" @change="updateRole($el)" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-semibold text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                                <option value="">-- No Assigned Role (Basic User) --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ (isset($activeRole) && $activeRole->id == $role->id) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Scope Level Selection -->
                    <div>
                        <label for="scope_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Jurisdiction Level
                        </label>
                        <select id="scope_type" name="scope_type" x-model="scopeType" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                            <option value="organization">Entire Organization (HQ Level)</option>
                            <option value="business_unit">Commercial Business Unit (Division)</option>
                            <option value="branch">Station Branch / Outlet (Location)</option>
                        </select>
                    </div>

                    <!-- Scope Destination Entity -->
                    <div>
                        <label for="scope_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Assigned Location / Entity
                        </label>

                        <!-- If Organization -->
                        <div x-show="scopeType === 'organization'">
                            <select id="scope_id_org" :name="scopeType === 'organization' ? 'scope_id' : ''" @change="updateScopeTitle($el)" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                                @foreach($organizations as $org)
                                    <option value="{{ $org->id }}" {{ $currentScopeId == $org->id ? 'selected' : '' }}>{{ $org->name }} (Executive HQ)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- If Business Unit -->
                        <div x-show="scopeType === 'business_unit'" x-cloak>
                            <select id="scope_id_bu" :name="scopeType === 'business_unit' ? 'scope_id' : ''" @change="updateScopeTitle($el)" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                                @foreach($businessUnits as $bu)
                                    <option value="{{ $bu->id }}" {{ $currentScopeId == $bu->id ? 'selected' : '' }}>{{ $bu->name }} ({{ $bu->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- If Branch -->
                        <div x-show="scopeType === 'branch'" x-cloak>
                            <select id="scope_id_br" :name="scopeType === 'branch' ? 'scope_id' : ''" @change="updateScopeTitle($el)" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $currentScopeId == $branch->id ? 'selected' : '' }}>{{ $branch->name }} ({{ $branch->code }} &bull; {{ $branch->city }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="flex items-center justify-end gap-x-4 bg-white shadow-xs rounded-2xl border border-slate-200/90 p-4 sm:p-5">
                <a href="{{ route('management.users.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all shadow-blue-500/25 active:scale-98 cursor-pointer">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Save Personnel Changes
                </button>
            </div>
        </div>

        <!-- Right: Live Interactive Personnel ID Badge Preview (5 cols md / 4 cols xl) -->
        <div class="md:col-span-5 xl:col-span-4 space-y-6">
            <div class="sticky top-20 space-y-5">
                <!-- Preview Pill -->
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Live Identity Badge</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        Current Standing
                    </span>
                </div>

                <!-- Executive Personnel ID Card -->
                <div class="rounded-2xl bg-gradient-to-br from-[#0A1A2F] via-[#112642] to-[#1E3A8A] text-white p-6 shadow-xl shadow-blue-950/20 border border-blue-900/40 relative overflow-hidden space-y-5">
                    <div class="absolute -right-6 -bottom-6 w-36 h-36 rounded-full bg-blue-500/10 pointer-events-none blur-xl"></div>

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 flex items-center justify-center font-black text-xl shadow-lg shadow-amber-500/20">
                                <span x-text="name ? name.substring(0, 1).toUpperCase() : 'U'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-amber-300 block">TANRAIL PERSONNEL</span>
                                <h3 class="text-base font-extrabold text-white leading-tight" x-text="name ? name : 'Personnel Name'"></h3>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-blue-200 border border-white/15">
                            Active
                        </span>
                    </div>

                    <div class="pt-2">
                        <span class="text-[11px] uppercase font-bold text-blue-300 block">Designated Role</span>
                        <p class="text-sm font-bold text-white mt-0.5" x-text="roleName ? roleName : (roleId ? 'Selected Role' : 'General User')"></p>
                    </div>

                    <div class="pt-3 border-t border-blue-800/40 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-blue-200/70">
                            <span>Official Email:</span>
                            <span class="font-medium text-white truncate max-w-[160px]" x-text="email ? email : 'user@tanrail.co.tz'"></span>
                        </div>
                        <div class="flex items-center justify-between text-blue-200/70">
                            <span>Jurisdiction Level:</span>
                            <span class="font-bold text-amber-300 uppercase font-mono text-[11px]" x-text="scopeType"></span>
                        </div>
                        <div class="flex items-center justify-between text-blue-200/70">
                            <span>Operational Scope:</span>
                            <span class="font-medium text-white truncate max-w-[150px]" x-text="scopeTitle ? scopeTitle : 'Assigned Entity'"></span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-blue-800/40 flex items-center justify-between text-[11px] text-blue-300/80">
                        <span>Personnel ID #{{ $user->id }}</span>
                        <span class="font-mono">TRC-SYSTEM</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

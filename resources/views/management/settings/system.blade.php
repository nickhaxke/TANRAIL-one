@extends('layouts.management')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A5F] p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold border border-blue-400/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Corporate Operations
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">System Preferences</h1>
                <p class="mt-1 text-sm text-slate-300">Global administrative settings and core operational parameters for the TANRAIL enterprise platform.</p>
            </div>
            <a href="{{ route('management.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 px-4 py-2.5 text-sm font-semibold shadow-sm transition-colors whitespace-nowrap">
                Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Configuration Form -->
    <form action="{{ route('management.settings.system.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="sm:col-span-2">
                <label for="system_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">System Name / Portal Title</label>
                <input type="text" name="system_name" id="system_name" value="{{ old('system_name', $settings['system_name'] ?? 'TANRAIL ONE - Enterprise Management') }}" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                <p class="mt-1 text-xs text-slate-500">Main system title displayed across portal headers, navigation, and executive reports</p>
                @error('system_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="headquarters_city" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Headquarters City</label>
                <input type="text" name="headquarters_city" id="headquarters_city" value="{{ old('headquarters_city', $settings['headquarters_city'] ?? 'Dar es Salaam') }}" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                @error('headquarters_city') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="timezone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">System Timezone</label>
                <select name="timezone" id="timezone" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 bg-white">
                    <option value="Africa/Dar_es_Salaam" {{ ($settings['timezone'] ?? '') === 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Africa/Dar es Salaam (EAT +03:00)</option>
                    <option value="Africa/Nairobi" {{ ($settings['timezone'] ?? '') === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (EAT +03:00)</option>
                    <option value="UTC" {{ ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                </select>
                @error('timezone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="primary_language" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Primary System Language</label>
                <select name="primary_language" id="primary_language" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 bg-white">
                    <option value="en" {{ ($settings['primary_language'] ?? '') === 'en' ? 'selected' : '' }}>English (Corporate & Operational)</option>
                    <option value="sw" {{ ($settings['primary_language'] ?? '') === 'sw' ? 'selected' : '' }}>Kiswahili</option>
                </select>
                @error('primary_language') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="session_timeout" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Session Timeout (Minutes)</label>
                <input type="number" name="session_timeout" id="session_timeout" value="{{ old('session_timeout', $settings['session_timeout'] ?? 60) }}" min="15" max="480" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                <p class="mt-1 text-xs text-slate-500">Automatic session expiration threshold for idle user sessions (Minutes)</p>
                @error('session_timeout') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Statutory Entity</h4>
                <p class="text-xs text-slate-500">Statutory registration, TIN, and official addresses are managed under the Corporate Profile.</p>
            </div>
            <a href="{{ route('management.settings.organization') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                Edit Corporate Profile &rarr;
            </a>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 text-sm font-semibold shadow-md shadow-blue-600/30 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save System Preferences
            </button>
        </div>
    </form>
</div>
@endsection

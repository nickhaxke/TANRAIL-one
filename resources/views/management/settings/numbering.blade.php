@extends('layouts.management')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="{
    buPrefix: '{{ old('business_unit_prefix', $settings['business_unit_prefix'] ?? 'BU-') }}',
    branchPrefix: '{{ old('branch_prefix', $settings['branch_prefix'] ?? 'BR-') }}',
    staffPrefix: '{{ old('staff_id_prefix', $settings['staff_id_prefix'] ?? 'TRC-EMP-') }}',
    padding: {{ old('code_padding', $settings['code_padding'] ?? 3) }}
}">
    <!-- Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A5F] p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold border border-blue-400/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                    </svg>
                    Administrative Identifiers
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Numbering & Official Codes</h1>
                <p class="mt-1 text-sm text-slate-300">Configure prefix standards and numbering schemas for railway operational divisions, station branches, and employee IDs.</p>
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

    <!-- Live Preview Badges -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Live Numbering Formats Preview</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-xs text-slate-500 font-medium block">Division / Business Unit:</span>
                <div class="mt-1.5 flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 font-mono text-sm font-bold" x-text="buPrefix + '01'.padStart(padding, '0')"></span>
                    <span class="text-xs text-slate-400">Sample</span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-xs text-slate-500 font-medium block">Branch / Station:</span>
                <div class="mt-1.5 flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-mono text-sm font-bold" x-text="branchPrefix + '01'.padStart(padding, '0')"></span>
                    <span class="text-xs text-slate-400">Sample</span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-xs text-slate-500 font-medium block">Staff ID / Personnel:</span>
                <div class="mt-1.5 flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800 font-mono text-sm font-bold" x-text="staffPrefix + '01'.padStart(padding, '0')"></span>
                    <span class="text-xs text-slate-400">Sample</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Form -->
    <form action="{{ route('management.settings.numbering.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="business_unit_prefix" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Business Unit Code Prefix</label>
                <input type="text" name="business_unit_prefix" id="business_unit_prefix" x-model="buPrefix" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 font-mono">
                <p class="mt-1 text-xs text-slate-500">Prefix for operational business units (e.g., BU- or DIV-)</p>
                @error('business_unit_prefix') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="branch_prefix" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Branch / Station Code Prefix</label>
                <input type="text" name="branch_prefix" id="branch_prefix" x-model="branchPrefix" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 font-mono">
                <p class="mt-1 text-xs text-slate-500">Prefix for railway stations & corridor branches (e.g., BR- or STN-)</p>
                @error('branch_prefix') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="staff_id_prefix" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Staff ID Prefix</label>
                <input type="text" name="staff_id_prefix" id="staff_id_prefix" x-model="staffPrefix" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 font-mono">
                <p class="mt-1 text-xs text-slate-500">Prefix for corporate staff identity code (e.g., TRC-EMP-)</p>
                @error('staff_id_prefix') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="code_padding" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Zero Padding Digits</label>
                <input type="number" name="code_padding" id="code_padding" x-model="padding" min="2" max="6" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 font-mono">
                <p class="mt-1 text-xs text-slate-500">Digit padding length (e.g., 3 yields 001, 4 yields 0001)</p>
                @error('code_padding') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="auto_generate_codes" value="1" {{ !empty($settings['auto_generate_codes']) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4">
                <span class="text-sm font-medium text-slate-700">Auto-suggest standardized sequential codes during Business Unit & Branch creation</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 text-sm font-semibold shadow-md shadow-blue-600/30 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Numbering Preferences
            </button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.management')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A5F] p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold border border-blue-400/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                    </svg>
                    Fiscal Compliance & Statutory Rates
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Tax & Regulatory Configuration</h1>
                <p class="mt-1 text-sm text-slate-300">Manage TRA statutory Value Added Tax (VAT), withholding tax (WHT) rates, and railway development levies.</p>
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

    <!-- Form -->
    <form action="{{ route('management.settings.tax.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- VAT Rate -->
            <div>
                <label for="vat_rate" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Standard Value Added Tax (VAT %) <span class="text-red-500">*</span>
                </label>
                <div class="relative rounded-xl shadow-2xs">
                    <input type="number" step="0.01" min="0" max="100" name="vat_rate" id="vat_rate" value="{{ old('vat_rate', $settings['vat_rate'] ?? 18.00) }}" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 font-mono">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 font-semibold text-xs">
                        %
                    </div>
                </div>
                <p class="mt-1 text-xs text-slate-500">Standard Tanzania statutory rate (TRA default: 18.00%).</p>
                @error('vat_rate') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- VAT Registration Number / VRN -->
            <div>
                <label for="vat_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    VAT Registration Number (VRN)
                </label>
                <input type="text" name="vat_number" id="vat_number" value="{{ old('vat_number', $settings['vat_number'] ?? '40012345678') }}" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 font-mono">
                <p class="mt-1 text-xs text-slate-500">Printed on official tax invoices and customer fiscal receipts.</p>
                @error('vat_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Withholding Tax on Goods -->
            <div>
                <label for="wht_goods_rate" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Withholding Tax - Goods Supply (WHT %) <span class="text-red-500">*</span>
                </label>
                <div class="relative rounded-xl shadow-2xs">
                    <input type="number" step="0.01" min="0" max="100" name="wht_goods_rate" id="wht_goods_rate" value="{{ old('wht_goods_rate', $settings['wht_goods_rate'] ?? 2.00) }}" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 font-mono">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 font-semibold text-xs">
                        %
                    </div>
                </div>
                <p class="mt-1 text-xs text-slate-500">Withholding deduction applied when settling local merchandise vendor bills.</p>
                @error('wht_goods_rate') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Withholding Tax on Services -->
            <div>
                <label for="wht_services_rate" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Withholding Tax - Services & Consulting (WHT %) <span class="text-red-500">*</span>
                </label>
                <div class="relative rounded-xl shadow-2xs">
                    <input type="number" step="0.01" min="0" max="100" name="wht_services_rate" id="wht_services_rate" value="{{ old('wht_services_rate', $settings['wht_services_rate'] ?? 5.00) }}" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 font-mono">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 font-semibold text-xs">
                        %
                    </div>
                </div>
                <p class="mt-1 text-xs text-slate-500">Standard statutory rate on contractor services and engineering consultations.</p>
                @error('wht_services_rate') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Railway Development Levy -->
            <div class="sm:col-span-2">
                <label for="railway_development_levy" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Railway Infrastructure Development Levy (RDL %) <span class="text-red-500">*</span>
                </label>
                <div class="relative rounded-xl shadow-2xs max-w-sm">
                    <input type="number" step="0.01" min="0" max="100" name="railway_development_levy" id="railway_development_levy" value="{{ old('railway_development_levy', $settings['railway_development_levy'] ?? 1.50) }}" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 font-mono">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 font-semibold text-xs">
                        %
                    </div>
                </div>
                <p class="mt-1 text-xs text-slate-500">Statutory infrastructure levy assessed on designated cargo and transit consignments.</p>
                @error('railway_development_levy') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Tax Exempt Sovereign Consignments -->
        <div class="pt-4 border-t border-slate-100">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="tax_exempt_sovereign" value="1" {{ !empty($settings['tax_exempt_sovereign']) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4 mt-0.5">
                <div>
                    <span class="text-sm font-semibold text-slate-900 block">Sovereign & Diplomatic Cargo Tax Exemption</span>
                    <span class="text-xs text-slate-500">Enable zero-rating for verified government freight, diplomatic shipments, and special emergency assistance cargo.</span>
                </div>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 text-sm font-semibold shadow-md shadow-blue-600/30 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Tax Configuration
            </button>
        </div>
    </form>
</div>
@endsection

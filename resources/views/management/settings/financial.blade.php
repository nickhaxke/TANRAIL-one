@extends('layouts.management')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A5F] p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold border border-emerald-400/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Corporate Treasury & Ledger
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Financial & Ledger Settings</h1>
                <p class="mt-1 text-sm text-slate-300">Configure base reporting currency, fiscal year calendar, payment terms, and GL posting rules.</p>
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
    <form action="{{ route('management.settings.financial.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Base Currency -->
            <div>
                <label for="base_currency" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Base Operational Currency <span class="text-red-500">*</span>
                </label>
                <select name="base_currency" id="base_currency" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 bg-white font-medium">
                    <option value="TZS" {{ old('base_currency', $settings['base_currency'] ?? '') === 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling (Default)</option>
                    <option value="USD" {{ old('base_currency', $settings['base_currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD - United States Dollar</option>
                    <option value="EUR" {{ old('base_currency', $settings['base_currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                </select>
                <p class="mt-1 text-xs text-slate-500">Official statutory reporting currency for TANRAIL financial ledgers.</p>
                @error('base_currency') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Secondary Currency -->
            <div>
                <label for="secondary_currency" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Secondary Multi-Currency
                </label>
                <select name="secondary_currency" id="secondary_currency" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 bg-white font-medium">
                    <option value="USD" {{ old('secondary_currency', $settings['secondary_currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD - United States Dollar (International Contracts)</option>
                    <option value="TZS" {{ old('secondary_currency', $settings['secondary_currency'] ?? '') === 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling</option>
                    <option value="" {{ empty($settings['secondary_currency']) ? 'selected' : '' }}>None (Single Currency Only)</option>
                </select>
                <p class="mt-1 text-xs text-slate-500">Used for cross-border freight billing and import procurement.</p>
                @error('secondary_currency') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Fiscal Year Start -->
            <div>
                <label for="fiscal_year_start" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Fiscal Year Start Month <span class="text-red-500">*</span>
                </label>
                <select name="fiscal_year_start" id="fiscal_year_start" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 bg-white font-medium">
                    <option value="July" {{ old('fiscal_year_start', $settings['fiscal_year_start'] ?? '') === 'July' ? 'selected' : '' }}>July (Government & Statutory Year: 01 Jul - 30 Jun)</option>
                    <option value="January" {{ old('fiscal_year_start', $settings['fiscal_year_start'] ?? '') === 'January' ? 'selected' : '' }}>January (Calendar Year: 01 Jan - 31 Dec)</option>
                    <option value="April" {{ old('fiscal_year_start', $settings['fiscal_year_start'] ?? '') === 'April' ? 'selected' : '' }}>April (01 Apr - 31 Mar)</option>
                    <option value="October" {{ old('fiscal_year_start', $settings['fiscal_year_start'] ?? '') === 'October' ? 'selected' : '' }}>October (01 Oct - 30 Sep)</option>
                </select>
                <p class="mt-1 text-xs text-slate-500">Determines accounting period closings and annual budget cycles.</p>
                @error('fiscal_year_start') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Default Payment Terms -->
            <div>
                <label for="payment_terms" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Default Vendor Payment Terms <span class="text-red-500">*</span>
                </label>
                <select name="payment_terms" id="payment_terms" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 bg-white font-medium">
                    <option value="Net 30" {{ old('payment_terms', $settings['payment_terms'] ?? '') === 'Net 30' ? 'selected' : '' }}>Net 30 Days (Standard Corporate)</option>
                    <option value="Net 15" {{ old('payment_terms', $settings['payment_terms'] ?? '') === 'Net 15' ? 'selected' : '' }}>Net 15 Days</option>
                    <option value="Net 60" {{ old('payment_terms', $settings['payment_terms'] ?? '') === 'Net 60' ? 'selected' : '' }}>Net 60 Days (Major Consignments)</option>
                    <option value="Immediate" {{ old('payment_terms', $settings['payment_terms'] ?? '') === 'Immediate' ? 'selected' : '' }}>Immediate / Due Upon Receipt</option>
                </select>
                <p class="mt-1 text-xs text-slate-500">Default credit period applied on newly created purchase orders.</p>
                @error('payment_terms') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Accounting Method -->
            <div>
                <label for="accounting_method" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Accounting Recognition Standard <span class="text-red-500">*</span>
                </label>
                <select name="accounting_method" id="accounting_method" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 bg-white font-medium">
                    <option value="accrual" {{ old('accounting_method', $settings['accounting_method'] ?? '') === 'accrual' ? 'selected' : '' }}>Accrual Basis (Recognize upon invoice generation)</option>
                    <option value="cash" {{ old('accounting_method', $settings['accounting_method'] ?? '') === 'cash' ? 'selected' : '' }}>Cash Basis (Recognize upon receipt/payment)</option>
                </select>
                <p class="mt-1 text-xs text-slate-500">Corporate financial governance policy for revenue and expenses.</p>
                @error('accounting_method') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Decimal Places -->
            <div>
                <label for="decimal_places" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Currency Decimal Precision <span class="text-red-500">*</span>
                </label>
                <input type="number" name="decimal_places" id="decimal_places" value="{{ old('decimal_places', $settings['decimal_places'] ?? 2) }}" min="0" max="4" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3 font-mono">
                <p class="mt-1 text-xs text-slate-500">Number of decimal places shown on financial statements (e.g. 2 for 0.00).</p>
                @error('decimal_places') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Auto-post GL entries toggle -->
        <div class="pt-4 border-t border-slate-100">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="auto_post_gl" value="1" {{ !empty($settings['auto_post_gl']) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4 mt-0.5">
                <div>
                    <span class="text-sm font-semibold text-slate-900 block">Automate Real-Time General Ledger Posting</span>
                    <span class="text-xs text-slate-500">Automatically post double-entry journal movements when invoices, receipts, and inventory disbursements are approved.</span>
                </div>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 text-sm font-semibold shadow-md shadow-blue-600/30 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Financial Defaults
            </button>
        </div>
    </form>
</div>
@endsection

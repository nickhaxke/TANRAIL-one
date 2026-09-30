@extends('layouts.management')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A5F] p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold border border-blue-400/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Administrative Alerts
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Notifications & Alerts</h1>
                <p class="mt-1 text-sm text-slate-300">Configure operational notification triggers and recipient rules for key administrative events, manager appointments, and station status updates.</p>
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
    <form action="{{ route('management.settings.notifications.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-6">
        @csrf

        <div>
            <label for="notification_email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Official Administrative Alert Email</label>
            <input type="email" name="notification_email" id="notification_email" value="{{ old('notification_email', $settings['notification_email'] ?? 'admin@tanrail.co.tz') }}" required class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            <p class="mt-1 text-xs text-slate-500">Designated administrative email address to receive real-time notifications and operational summaries.</p>
            @error('notification_email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Notification Triggers</h4>

            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
                <input type="checkbox" name="notify_manager_assigned" value="1" {{ !empty($settings['notify_manager_assigned']) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4 mt-0.5">
                <div>
                    <span class="text-sm font-semibold text-slate-900 block">Manager Assignment Alerts</span>
                    <span class="text-xs text-slate-500">Dispatch an alert immediately when a division manager or station supervisor is assigned, reallocated, or removed.</span>
                </div>
            </label>

            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
                <input type="checkbox" name="notify_branch_status" value="1" {{ !empty($settings['notify_branch_status']) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4 mt-0.5">
                <div>
                    <span class="text-sm font-semibold text-slate-900 block">Station Status Changes</span>
                    <span class="text-xs text-slate-500">Dispatch an alert when a railway station or corridor branch operational status toggles between Active and Inactive.</span>
                </div>
            </label>

            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
                <input type="checkbox" name="notify_audit_events" value="1" {{ !empty($settings['notify_audit_events']) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4 mt-0.5">
                <div>
                    <span class="text-sm font-semibold text-slate-900 block">Security & Role Changes</span>
                    <span class="text-xs text-slate-500">Dispatch an alert when administrative roles, privileges, or security configurations are updated.</span>
                </div>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 text-sm font-semibold shadow-md shadow-blue-600/30 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Notification Preferences
            </button>
        </div>
    </form>
</div>
@endsection

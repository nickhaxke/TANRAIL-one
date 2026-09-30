@extends('layouts.management')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Executive Header Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A8A] p-6 sm:p-7 text-white shadow-xl shadow-blue-950/10 border border-blue-900/30">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-300/90 mb-1.5">
                    <a href="{{ route('management.roles.index') }}" class="hover:text-white transition-colors flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Roles Matrix
                    </a>
                    <span>/</span>
                    <span class="text-blue-200">New Policy</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Create Enterprise Role</h1>
                <p class="mt-1 text-xs sm:text-sm text-blue-200/80">
                    Define a new institutional role title and select governing access permissions.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('management.roles.index') }}" class="inline-flex items-center rounded-xl bg-white/10 backdrop-blur-md px-4 py-2.5 text-xs font-bold text-white border border-white/20 hover:bg-white/20 transition-all shadow-sm">
                    &larr; Back to Roles
                </a>
            </div>
        </div>
    </div>

    <!-- Role Form -->
    <form action="{{ route('management.roles.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 sm:p-8 space-y-6">
        @csrf

        <!-- Role Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pb-6 border-b border-slate-100">
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Role Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Station Operations Supervisor" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-semibold text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all" required>
                @error('name')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Role Operational Mandate / Description
                </label>
                <input type="text" name="description" id="description" value="{{ old('description') }}" placeholder="Brief summary of duties and authority" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 bg-white hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
            </div>
        </div>

        <!-- Permissions Checklist -->
        <div class="space-y-4" x-data="{
            selectAll() {
                document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
            },
            deselectAll() {
                document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
            }
        }">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Assigned System Permissions</h3>
                    <p class="text-xs text-slate-500">Check the statutory authorities granted to this role.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="selectAll()" class="text-xs font-bold text-blue-600 hover:text-blue-700 px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors">
                        Select All
                    </button>
                    <button type="button" @click="deselectAll()" class="text-xs font-semibold text-slate-500 hover:text-slate-700 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors">
                        Deselect All
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                @foreach($permissions as $permission)
                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-slate-100/70 hover:border-blue-300 transition-all cursor-pointer select-none">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="permission-checkbox h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 mt-0.5">
                    <div class="text-xs">
                        <span class="font-bold text-slate-900 block font-mono">{{ $permission->name }}</span>
                        <span class="text-slate-500 block mt-0.5">{{ $permission->description }}</span>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Action Bar -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('management.roles.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-colors">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all shadow-blue-500/25 active:scale-98 cursor-pointer">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create Role
            </button>
        </div>
    </form>
</div>
@endsection

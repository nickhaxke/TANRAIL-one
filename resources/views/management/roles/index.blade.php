@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Executive Header Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A8A] p-6 sm:p-7 text-white shadow-xl shadow-blue-950/10 border border-blue-900/30">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-300/90 mb-1.5">
                    <a href="{{ route('management.users.index') }}" class="hover:text-white transition-colors">System Administration</a>
                    <span>/</span>
                    <span class="text-blue-200">Security Policies</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Roles & Permissions Matrix</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        Security Sovereignty
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-blue-200/80">
                    Define institutional authorities, configure permission boundaries, and enforce enterprise governance.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('management.users.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 backdrop-blur-md px-4 py-2.5 text-xs font-bold text-white border border-white/20 hover:bg-white/20 transition-all shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Staff Directory
                </a>
                <a href="{{ route('management.roles.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-600/30 hover:bg-blue-500 transition-all active:scale-98">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    + Create New Role
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

    @if(session('error'))
    <div class="rounded-xl bg-red-50 p-4 border border-red-200 flex items-center gap-3 shadow-xs">
        <svg class="h-5 w-5 text-red-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Roles Matrix Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($roles as $role)
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs hover:shadow-md hover:border-blue-300 transition-all flex flex-col justify-between space-y-5">
            <div>
                <!-- Role Header -->
                <div class="flex items-start justify-between gap-3 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-700 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                            {{ substr($role->name, 0, 2) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900">{{ $role->name }}</h3>
                                @if(strtolower($role->name) === 'super admin')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        SYSTEM ROOT
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $role->description ?? 'Institutional role governing system activities.' }}</p>
                        </div>
                    </div>

                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 shrink-0">
                        {{ $role->permissions->count() }} Perms
                    </span>
                </div>

                <!-- Assigned Permissions Badges -->
                <div class="pt-4 space-y-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Assigned Permissions</span>
                    <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto pr-1">
                        @forelse($role->permissions as $permission)
                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                {{ $permission->name }}
                            </span>
                        @empty
                            <span class="text-xs text-slate-400 italic">No permissions assigned to this role yet.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="{{ route('management.roles.edit', $role) }}" class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-100 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Configure Permissions
                    </a>
                </div>

                @if(strtolower($role->name) !== 'super admin')
                <form action="{{ route('management.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete role {{ $role->name }}? Staff assigned to this role will lose associated permissions.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Delete
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

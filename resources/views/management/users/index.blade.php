@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Executive Header Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-[#0A1A2F] via-[#112642] to-[#1E3A8A] p-6 sm:p-7 text-white shadow-xl shadow-blue-950/10 border border-blue-900/30">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-300/90 mb-1.5">
                    <span>Corporate Administration</span>
                    <span>/</span>
                    <span class="text-blue-200">Staff & Identity</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">User & Staff Management</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        Access Governance
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-blue-200/80">
                    Oversee registered users, station supervisors, unit leaders, and role assignments across TANRAIL.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('management.roles.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 backdrop-blur-md px-4 py-2.5 text-xs font-bold text-white border border-white/20 hover:bg-white/20 transition-all shadow-sm">
                    <svg class="h-4 w-4 text-blue-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-2.18-4.965a4.5 4.5 0 016.36 6.36m-6.36-6.36a4.5 4.5 0 00-6.36 6.36m6.36-6.36l6.36 6.36" />
                    </svg>
                    Roles & Permissions
                </a>
                <a href="{{ route('management.users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-600/30 hover:bg-blue-500 transition-all active:scale-98">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    + Add New Staff
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

    <!-- 3 KPI Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total System Personnel</span>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $users->total() }}</p>
                <p class="mt-1 text-xs text-slate-500">Registered ERP user accounts</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Assigned Roles</span>
                <p class="mt-2 text-3xl font-black text-indigo-600">{{ $roles->count() }}</p>
                <p class="mt-1 text-xs text-slate-500">Configured access categories</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-2.18-4.965a4.5 4.5 0 016.36 6.36m-6.36-6.36a4.5 4.5 0 00-6.36 6.36m6.36-6.36l6.36 6.36" />
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Security & Sovereignty</span>
                <p class="mt-2 text-xl font-black text-emerald-600">Enterprise Guarded</p>
                <p class="mt-1 text-xs text-slate-500">Role & Scope level security active</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs">
        <form method="GET" action="{{ route('management.users.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="flex-1 relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" name="search" id="search" value="{{ $search ?? '' }}" class="block w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 font-medium transition-all" placeholder="Search staff by full name or official email...">
            </div>

            <div class="sm:w-56">
                <select name="role_id" onchange="this.form.submit()" class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 transition-all">
                    <option value="">All Assigned Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ (isset($roleFilter) && $roleFilter == $role->id) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition-colors shadow-xs">
                    Search
                </button>
                @if(($search ?? false) || ($roleFilter ?? false))
                    <a href="{{ route('management.users.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-3.5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-200 transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Staff Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Personnel Directory</h2>
                <p class="text-xs text-slate-500">Official officers, unit directors, and station personnel.</p>
            </div>
            <a href="{{ route('management.users.create') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700">
                + Register Staff
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Staff Member</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Assigned Role</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Operational Jurisdiction</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Registered</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- Staff Name & Avatar -->
                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                        <span>{{ $user->name }}</span>
                                        @if(auth()->id() === $user->id)
                                            <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                You
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-500 font-medium">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Role -->
                        <td class="whitespace-nowrap px-3 py-4 text-xs">
                            @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        <svg class="h-3 w-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-2.18-4.965a4.5 4.5 0 016.36 6.36m-6.36-6.36a4.5 4.5 0 00-6.36 6.36m6.36-6.36l6.36 6.36" />
                                        </svg>
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-xs text-slate-400 italic">No role assigned</span>
                            @endif
                        </td>

                        <!-- Jurisdiction Scope -->
                        <td class="whitespace-nowrap px-3 py-4 text-xs">
                            @php
                                $firstRole = $user->roles->first();
                                $pivot = $firstRole?->pivot;
                            @endphp
                            @if($pivot)
                                @if(str_contains($pivot->scope_type, 'Organization'))
                                    <span class="font-medium text-slate-800 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                        Enterprise Sovereignty (All Units)
                                    </span>
                                @elseif(str_contains($pivot->scope_type, 'BusinessUnit'))
                                    @php $bu = \App\Domains\Core\Models\BusinessUnit::find($pivot->scope_id); @endphp
                                    <span class="font-medium text-blue-700 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Division: {{ $bu?->name ?? 'Unit #'.$pivot->scope_id }}
                                    </span>
                                @elseif(str_contains($pivot->scope_type, 'Branch'))
                                    @php $br = \App\Domains\Core\Models\Branch::find($pivot->scope_id); @endphp
                                    <span class="font-medium text-emerald-700 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Station: {{ $br?->name ?? 'Branch #'.$pivot->scope_id }}
                                    </span>
                                @else
                                    <span class="text-slate-500">{{ class_basename($pivot->scope_type) }} #{{ $pivot->scope_id }}</span>
                                @endif
                            @else
                                <span class="text-slate-400 italic">General User</span>
                            @endif
                        </td>

                        <!-- Registered Date -->
                        <td class="whitespace-nowrap px-3 py-4 text-xs text-slate-500 font-mono">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                        </td>

                        <!-- Action Buttons -->
                        <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right text-xs font-semibold space-x-2">
                            <a href="{{ route('management.users.edit', $user) }}" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-colors">
                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Edit
                            </a>

                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('management.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to remove staff member {{ $user->name }}? This will terminate system access.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500 bg-slate-50/50">
                            <p class="text-sm font-semibold text-slate-700">No staff members found matching criteria.</p>
                            <a href="{{ route('management.users.create') }}" class="mt-3 inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-700">
                                + Register First Staff Member
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

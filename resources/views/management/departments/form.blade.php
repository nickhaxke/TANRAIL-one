@extends('layouts.management')

@section('content')
<div class="space-y-6" x-data="{
    deptId: '{{ old('department_id', $department->department_id ?? 'DEP-') }}',
    name: '{{ old('name', $department->name ?? '') }}',
    branchId: '{{ old('branch_id', $department->branch_id ?? '') }}',
    headId: '{{ old('head_id', $department->head_id ?? '') }}',
    status: {{ old('status', isset($department) ? $department->status : true) ? 'true' : 'false' }}
}">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                <a href="{{ route('management.organization.departments') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Departments Directory
                </a>
                <span>/</span>
                <span>{{ isset($department) ? 'Modify Department' : 'New Department' }}</span>
            </div>
            <div class="flex items-center gap-x-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#0A1A2F]">
                    {{ isset($department) ? 'Edit Department: ' . $department->name : 'Register Organizational Department' }}
                </h1>
                <span class="inline-flex items-center rounded-md bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                    Administration
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Configure operational and functional departments assigned to station branches and hubs.
            </p>
        </div>
        <div>
            <a href="{{ route('management.organization.departments') }}" class="inline-flex items-center rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>
    </div>

    <!-- 2-Column Responsive Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Department Configuration Form (2 Columns) -->
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ isset($department) ? route('management.departments.update', $department) : route('management.departments.store') }}" method="POST" class="space-y-6">
                @csrf
                @if(isset($department))
                    @method('PUT')
                @endif

                <!-- Section 1: Department Identification -->
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-2xl p-6 sm:p-7 space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-bold">1</span>
                            Department Identification
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Specify official naming and code structure for administrative indexing.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">Department Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" x-model="name" required placeholder="e.g., Executive Kitchen Operations, Station Cashier Desk" value="{{ old('name', $department->name ?? '') }}" class="mt-1.5 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="department_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700">Department ID / Code <span class="text-red-500">*</span></label>
                            <input type="text" name="department_id" id="department_id" x-model="deptId" required placeholder="e.g., DEP-KTCH-01" value="{{ old('department_id', $department->department_id ?? '') }}" class="mt-1.5 block w-full rounded-xl border-gray-300 font-mono shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 uppercase">
                            @error('department_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="branch_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700">Parent Branch / Outlet <span class="text-red-500">*</span></label>
                            <select id="branch_id" name="branch_id" x-model="branchId" required class="mt-1.5 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                                <option value="">Select Branch</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ old('branch_id', $department->branch_id ?? '') == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }} ({{ $b->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Department Governance & Head -->
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-2xl p-6 sm:p-7 space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">2</span>
                            Leadership & Operational Status
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Assign supervisory responsibility and set operational state.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label for="head_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700">Department Head / Lead</label>
                            <select id="head_id" name="head_id" x-model="headId" class="mt-1.5 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                                <option value="">Select Staff Member (Optional)</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('head_id', $department->head_id ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('head_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Toggle Switch -->
                        <div class="sm:col-span-2 flex items-center justify-between p-4 rounded-xl border border-gray-200 bg-slate-50/70">
                            <div>
                                <span class="text-sm font-bold text-gray-900">Active Operational Status</span>
                                <p class="text-xs text-gray-500">When active, this department can be selected in staff rotations, tasks, and asset allocations.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="status" value="1" x-model="status" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('management.organization.departments') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-500/20 hover:bg-blue-500 transition-colors">
                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        {{ isset($department) ? 'Update Department' : 'Save Department' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Interactive Preview Card (1 Column) -->
        <div class="space-y-6">
            <div class="sticky top-24">
                <div class="rounded-2xl border border-blue-200/80 bg-gradient-to-b from-blue-50/40 via-white to-white p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700 bg-blue-100/70 px-2 py-0.5 rounded">
                            Interactive Preview
                        </span>
                        <span class="text-xs text-gray-400">Card View</span>
                    </div>

                    <!-- Department Badge -->
                    <div class="flex items-start gap-3.5 mb-4">
                        <div class="h-12 w-12 rounded-xl flex items-center justify-center shrink-0 font-black text-sm text-white shadow-sm" :class="status ? 'bg-indigo-600' : 'bg-gray-400'">
                            <span x-text="name ? name.substring(0, 2).toUpperCase() : 'DP'"></span>
                        </div>
                        <div class="truncate">
                            <h3 class="font-bold text-gray-900 text-base truncate" x-text="name || 'Department Name'"></h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded" x-text="deptId || 'DEP-CODE'"></span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold" :class="status ? 'text-emerald-700' : 'text-gray-500'">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="status ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                                    <span x-text="status ? 'Active' : 'Inactive'"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Department Metadata Preview -->
                    <div class="space-y-2.5 pt-3 border-t border-gray-100 text-xs">
                        <div class="flex items-center justify-between text-gray-600">
                            <span class="text-gray-400">Branch Outlet:</span>
                            <span class="font-semibold text-gray-800" x-text="branchId ? 'Configured Outlet' : 'Select Branch'"></span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600">
                            <span class="text-gray-400">Leadership:</span>
                            <span class="font-semibold text-gray-800" x-text="headId ? 'Designated Lead' : 'Unassigned'"></span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-400 text-center">
                        TANRAIL ONE Governance System
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

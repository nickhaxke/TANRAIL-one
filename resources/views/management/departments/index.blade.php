@extends('layouts.management')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-x-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#0A1A2F]">Organizational Departments</h1>
                <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                    Administrative Structure
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Manage operational and functional departments operating across all station branches and hubs.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('management.organization.branches') }}" class="inline-flex items-center rounded-lg bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                <svg class="-ml-0.5 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                </svg>
                Branches
            </a>
            <a href="{{ route('management.departments.create') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors shadow-blue-500/20">
                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Department
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-200 flex items-center gap-3">
        <svg class="h-5 w-5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Departments</p>
                    <p class="mt-2 text-3xl font-black text-[#0A1A2F]">{{ $departments->count() }}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-xl text-blue-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 flex items-center gap-1.5">
                <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
                Operational sections & work desks
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Departments</p>
                    <p class="mt-2 text-3xl font-black text-emerald-600">{{ $departments->where('status', true)->count() }}</p>
                </div>
                <div class="bg-emerald-50 p-3 rounded-xl text-emerald-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 flex items-center gap-1.5">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                Active for staffing & daily workflows
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Operating Branches</p>
                    <p class="mt-2 text-3xl font-black text-indigo-600">{{ $departments->pluck('branch_id')->filter()->unique()->count() }}</p>
                </div>
                <div class="bg-indigo-50 p-3 rounded-xl text-indigo-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.39 3.822A2.02 2.02 0 015.825 3h12.35a2.02 2.02 0 011.435.822l2.76 3.837m-16.5 0h16.5" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 flex items-center gap-1.5">
                <span class="inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
                Locations with designated departments
            </div>
        </div>
    </div>

    <!-- Departments Directory Table -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-2xl overflow-hidden" x-data="{ search: '' }">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Departments Directory</h2>
                <p class="text-xs text-gray-500">Functional sections mapped to their parent branches and designated leadership.</p>
            </div>
            <div class="w-full sm:w-72">
                <input type="text" x-model="search" placeholder="Search departments..." class="block w-full rounded-lg border-0 py-1.5 pl-3 pr-3 text-xs text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600">
            </div>
        </div>

        @if($departments->isEmpty())
        <div class="text-center py-16 px-4">
            <div class="mx-auto h-16 w-16 text-gray-300">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </div>
            <h3 class="mt-4 text-base font-bold text-gray-900">No Departments Configured</h3>
            <p class="mt-1 text-sm text-gray-500 max-w-md mx-auto">
                Create departments (e.g. Executive Kitchen, Catering Service, Cashier Counter) under your operating branches.
            </p>
            <div class="mt-6">
                <a href="{{ route('management.departments.create') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 shadow-blue-500/20">
                    + Register First Department
                </a>
            </div>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Department</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Department Head</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Parent Branch</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($departments as $department)
                    <tr class="hover:bg-slate-50/80 transition-colors" x-show="!search || '{{ strtolower(addslashes($department->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($department->department_id)) }}'.includes(search.toLowerCase())">
                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl flex items-center justify-center shrink-0 font-bold text-xs {{ $department->status ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-400' }}">
                                    {{ substr($department->department_id, 0, 3) }}
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-gray-900">{{ $department->name }}</div>
                                    <div class="text-xs font-mono text-gray-500">{{ $department->department_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-xs text-gray-700">
                            @if($department->head)
                                <div class="font-medium text-gray-900">{{ $department->head->name }}</div>
                                <div class="text-[11px] text-gray-400">{{ $department->head->email }}</div>
                            @else
                                <span class="text-xs text-gray-400 italic">Unassigned</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-xs">
                            @if($department->branch)
                                <a href="{{ route('management.branches.show', $department->branch) }}" class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 px-2 py-1 font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 hover:bg-blue-100 transition-colors">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    {{ $department->branch->name }}
                                </a>
                            @else
                                <span class="text-xs text-gray-400 italic">Unassigned</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-xs">
                            @if($department->status)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right text-xs font-semibold space-x-2">
                            <a href="{{ route('management.departments.edit', $department) }}" class="inline-flex items-center rounded-lg bg-white px-2.5 py-1.5 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                                Edit
                            </a>
                            <form action="{{ route('management.departments.destroy', $department) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete department {{ $department->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center rounded-lg bg-white p-1.5 text-xs font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-200 hover:bg-red-50 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

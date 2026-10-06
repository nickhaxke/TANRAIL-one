@extends('layouts.cleaning')

@section('content')
<div class="max-w-7xl mx-auto w-full">

    <div class="mb-8 border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="text-xs font-bold tracking-widest text-gray-500 uppercase mb-1">Personnel Management</div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Cleaning Workforce</h1>
        </div>
        <div class="flex items-center gap-3">
            @can('cleaning.workers.manage')
            <form method="GET" action="{{ route('cleaning.workers.index') }}" class="flex items-center gap-2">
                <select name="branch_id" onchange="this.form.submit()" class="text-xs border-2 border-gray-300 font-semibold px-2 py-1.5 bg-white text-gray-700">
                    <option value="">All Stations</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('cleaning.workers.create') }}" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors flex items-center gap-2">
                + Add Worker
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-600 p-4 mb-6">
            <p class="text-sm font-bold text-green-900">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white border-2 border-gray-200 shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b-2 border-gray-200">
                <tr>
                    <th class="px-4 py-3 font-bold text-gray-600 uppercase">Worker ID</th>
                    <th class="px-4 py-3 font-bold text-gray-600 uppercase">Name</th>
                    <th class="px-4 py-3 font-bold text-gray-600 uppercase">Station / Branch</th>
                    <th class="px-4 py-3 font-bold text-gray-600 uppercase">Responsible Supervisor</th>
                    <th class="px-4 py-3 font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 font-bold text-gray-600 uppercase text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($workers as $worker)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-bold text-gray-900 font-mono">{{ $worker->worker_id }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <div>{{ $worker->first_name }} {{ $worker->last_name }}</div>
                            <div class="text-xs text-gray-400">{{ $worker->phone_number ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                {{ $worker->currentBranch?->name ?? 'Unassigned' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $worker->currentSupervisor?->name ?? 'None' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($worker->is_active)
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border bg-green-50 text-green-700 border-green-200">Active</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border bg-red-50 text-red-700 border-red-200">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('cleaning.workers.show', $worker) }}" class="text-xs font-bold text-cyan-700 hover:underline uppercase">View</a>
                            @can('cleaning.workers.manage')
                            <a href="{{ route('cleaning.workers.edit', $worker) }}" class="text-xs font-bold text-blue-600 hover:underline uppercase">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 font-medium">
                            No cleaning workers found for this station.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $workers->links() }}
    </div>
</div>
@endsection

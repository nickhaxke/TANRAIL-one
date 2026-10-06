@extends('layouts.cleaning')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Supervisor Service Assignments</h1>
        <p class="text-slate-500 text-sm mt-1">Manage which services supervisors are authorized to run at which stations.</p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-sm border border-emerald-100">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 text-rose-700 font-bold text-sm border border-rose-100">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 text-rose-700 font-bold text-sm border border-rose-100">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50">
            <h2 class="font-bold text-slate-800">Assign Service to Supervisor</h2>
            <form action="{{ route('cleaning.assignments.store') }}" method="POST" class="mt-4 flex flex-wrap items-end gap-4">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Supervisor</label>
                    <select name="supervisor_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                        <option value="">Select Supervisor...</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Station/Branch</label>
                    <select name="branch_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                        <option value="">Select Station...</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Service Type</label>
                    <select name="cleaning_service_type_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                        <option value="">Select Service...</option>
                        @foreach($serviceTypes as $service)
                            <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Start Date</label>
                    <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                </div>
                <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition-colors shadow-sm h-[38px]">
                    Assign
                </button>
            </form>
        </div>

        <table class="w-full text-left text-sm">
            <thead class="bg-white border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 font-bold text-slate-400 uppercase tracking-wider text-[10px]">Supervisor</th>
                    <th class="px-6 py-4 font-bold text-slate-400 uppercase tracking-wider text-[10px]">Active Assignments</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($supervisors as $supervisor)
                <tr>
                    <td class="px-6 py-6 align-top">
                        <div class="font-bold text-slate-800 text-base">{{ $supervisor->name }}</div>
                        <div class="text-slate-500 text-xs">{{ $supervisor->email }}</div>
                    </td>
                    <td class="px-6 py-6">
                        @if($supervisor->cleaningSupervisorAssignments->isEmpty())
                            <span class="text-slate-400 text-xs italic">No active assignments</span>
                        @else
                            <div class="flex flex-col gap-2">
                                @foreach($supervisor->cleaningSupervisorAssignments as $assignment)
                                    @php
                                        $isActive = $assignment->start_date <= today() && (!$assignment->end_date || $assignment->end_date >= today());
                                    @endphp
                                    <div class="flex items-center justify-between p-3 rounded-lg border {{ $isActive ? 'border-cyan-200 bg-cyan-50/50' : 'border-slate-200 bg-slate-50 opacity-60' }}">
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm">{{ $assignment->serviceType->name ?? 'Unknown Service' }}</div>
                                            <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="font-semibold">{{ $assignment->branch->name ?? 'Unknown Branch' }}</span>
                                                <span>&bull;</span>
                                                <span>Since {{ $assignment->start_date->format('M j, Y') }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            @if($isActive)
                                                <span class="inline-flex px-2 py-1 rounded text-[10px] font-bold bg-cyan-100 text-cyan-800 uppercase tracking-wider">Active</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 rounded text-[10px] font-bold bg-slate-200 text-slate-600 uppercase tracking-wider">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-6 py-8 text-center text-slate-500">No supervisors found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

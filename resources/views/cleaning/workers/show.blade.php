@extends('layouts.cleaning')

@section('content')
<div class="max-w-6xl mx-auto w-full space-y-8">

    <!-- Header -->
    <div class="border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="text-xs font-bold tracking-widest text-gray-500 uppercase mb-1">Worker Profile & Traceability</div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">{{ $worker->first_name }} {{ $worker->last_name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('cleaning.workers.index') }}" class="px-4 py-2 border-2 border-gray-300 text-gray-700 font-bold text-xs uppercase hover:bg-gray-100 transition-colors">
                &larr; Back to Directory
            </a>
            @can('cleaning.workers.manage')
            <a href="{{ route('cleaning.workers.edit', $worker) }}" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-xs uppercase tracking-wider hover:bg-gray-800 transition-colors">
                Edit Profile
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-600 p-4">
            <p class="text-sm font-bold text-green-900">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Column 1 & 2: Overview & Assignment Timeline -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Profile Summary Card -->
            <div class="bg-white border-2 border-gray-200 p-6 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-600 mb-4 pb-2 border-b border-gray-100">
                    Personnel Credentials
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <div>
                        <div class="text-xs text-gray-400 font-bold uppercase">Worker ID</div>
                        <div class="text-base font-black text-gray-900 font-mono mt-0.5">{{ $worker->worker_id }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-bold uppercase">Status</div>
                        <div class="mt-0.5">
                            @if($worker->is_active)
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border bg-green-50 text-green-700 border-green-200">Active</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border bg-red-50 text-red-700 border-red-200">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-bold uppercase">National / ID #</div>
                        <div class="text-sm font-semibold text-gray-800 mt-0.5">{{ $worker->id_number ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-bold uppercase">Phone Number</div>
                        <div class="text-sm font-semibold text-gray-800 mt-0.5">{{ $worker->phone_number ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-bold uppercase">Current Station</div>
                        <div class="text-sm font-bold text-cyan-800 mt-0.5">{{ $worker->currentBranch?->name ?? 'Unassigned' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-bold uppercase">Current Supervisor</div>
                        <div class="text-sm font-semibold text-gray-800 mt-0.5">{{ $worker->currentSupervisor?->name ?? 'None' }}</div>
                    </div>
                </div>
            </div>

            <!-- Historical Assignment Log -->
            <div class="bg-white border-2 border-gray-200 p-6 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-600 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                    <span>Assignment & Station History</span>
                    <span class="text-xs font-normal text-gray-400">Traceable Operational Log</span>
                </h2>

                <div class="divide-y divide-gray-100">
                    @forelse($worker->assignments as $assignment)
                        <div class="py-4 first:pt-0 last:pb-0 flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-900 text-sm">{{ $assignment->branch?->name }}</span>
                                    @if(is_null($assignment->end_date))
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-cyan-100 text-cyan-800 rounded">Current Station</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Supervisor: <span class="font-medium text-gray-700">{{ $assignment->supervisor?->name ?? 'Unassigned' }}</span>
                                    &bull; Assigned by: <span class="font-medium text-gray-700">{{ $assignment->assignedBy?->name ?? 'System' }}</span>
                                </div>
                                @if($assignment->notes)
                                    <div class="text-xs text-gray-600 italic mt-1 bg-gray-50 p-2 rounded border border-gray-100">
                                        "{{ $assignment->notes }}"
                                    </div>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-xs font-bold text-gray-700 font-mono">
                                    {{ $assignment->start_date->format('M d, Y') }} &rarr; 
                                    {{ $assignment->end_date ? $assignment->end_date->format('M d, Y') : 'Present' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400 text-sm">
                            No assignment history recorded for this worker.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Column 3: Station Reassignment Action (Manager Only) -->
        <div>
            @can('cleaning.workers.manage')
            <div class="bg-white border-2 border-gray-900 p-6 shadow-md sticky top-6">
                <div class="text-xs font-bold tracking-widest text-cyan-600 uppercase mb-1">Operational Control</div>
                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight mb-4">Reassign Worker</h3>
                <p class="text-xs text-gray-500 mb-6">
                    Move worker to another railway station and assign them under the station supervisor. Past history is permanently preserved.
                </p>

                <form method="POST" action="{{ route('cleaning.workers.assign', $worker) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Target Station / Branch *</label>
                        <select name="branch_id" required class="w-full text-sm border-2 border-gray-300 rounded px-3 py-2 bg-white text-gray-800 focus:border-cyan-500">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $worker->current_branch_id == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Station Supervisor</label>
                        <select name="supervisor_id" class="w-full text-sm border-2 border-gray-300 rounded px-3 py-2 bg-white text-gray-800 focus:border-cyan-500">
                            <option value="">-- No Supervisor Assigned --</option>
                            @foreach($supervisors as $sup)
                                <option value="{{ $sup->id }}" {{ $worker->current_supervisor_id == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Reassignment Reason / Notes</label>
                        <textarea name="notes" rows="3" placeholder="e.g. Relocated to cover Morogoro station shifts" class="w-full text-sm border-2 border-gray-300 rounded px-3 py-2 text-gray-800 focus:border-cyan-500"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow">
                        Confirm Reassignment
                    </button>
                </form>
            </div>
            @else
            <div class="bg-gray-50 border-2 border-gray-200 p-6 text-center text-xs text-gray-500">
                Worker station assignments are managed exclusively by the Cleaning Manager.
            </div>
            @endcan
        </div>

    </div>

</div>
@endsection

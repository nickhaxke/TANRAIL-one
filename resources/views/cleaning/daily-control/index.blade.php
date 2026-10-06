@extends('layouts.cleaning')

@section('content')
<div class="max-w-4xl mx-auto w-full">
    <!-- Header -->
    <div class="mb-8 border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">TANRAIL ONE</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">Cleaning Operations</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-500 uppercase">{{ $branch->name }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Today's Operations</h1>
        </div>
        <div class="text-right">
            <div class="text-sm font-medium text-gray-500">{{ today()->format('l, F j, Y') }}</div>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-50 text-red-700 p-4 rounded-xl text-sm border border-red-100 font-medium">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl text-sm border border-green-100 font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 p-4 rounded-xl text-sm border border-red-100 font-medium">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!$dailyControl)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Ready to Start Today's Operations?</h2>
            <p class="text-gray-500 text-sm mb-6 max-w-md mx-auto">Start the daily control to track workforce attendance and operational activities for {{ $branch->name }}.</p>
            <form method="POST" action="{{ route('cleaning.daily-control.store') }}">
                @csrf
                <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 px-8 rounded-lg shadow-md shadow-cyan-600/20 transition-all">
                    Start Today's Operations
                </button>
            </form>
        </div>
    @else
        <!-- Workforce Check Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        @if($dailyControl->workforce_check_status === 'Completed')
                            Team &mdash; Today's Shift
                            <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Completed</span>
                        @else
                            Morning Workforce Check
                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Pending</span>
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($dailyControl->workforce_check_status === 'Completed')
                            Manage team checkout and status.
                        @else
                            Record attendance for today's shift.
                        @endif
                    </p>
                </div>
                @if($dailyControl->workforce_check_status === 'Completed' && $dailyControl->status !== 'Submitted' && $dailyControl->status !== 'Approved')
                    @php
                        $eligibleForBulk = $dailyControl->workerAttendances->where('status', 'Present')->count();
                    @endphp
                    @if($eligibleForBulk > 0)
                        <form method="POST" action="{{ route('cleaning.daily-control.workforce.checkout-all', $dailyControl) }}">
                            @csrf
                            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm text-sm" onclick="return confirm('Check out all {{ $eligibleForBulk }} present workers now?')">
                                Check Out All
                            </button>
                        </form>
                    @endif
                @endif
            </div>

            <div class="p-6">
                @if($dailyControl->workforce_check_status !== 'Completed')
                    <form method="POST" action="{{ route('cleaning.daily-control.workforce', $dailyControl) }}">
                        @csrf
                        
                        <div class="overflow-x-auto border rounded-xl mb-6">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                        <th class="p-3 font-semibold border-b">Worker</th>
                                        <th class="p-3 font-semibold border-b w-32 text-center">Present</th>
                                        <th class="p-3 font-semibold border-b w-48">Arrival Time</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($workers as $index => $worker)
                                        @php
                                            $attendance = $dailyControl->workerAttendances->where('cleaning_worker_id', $worker->id)->first();
                                            $isPresent = $attendance ? true : false;
                                            $arrivalTime = $attendance && $attendance->arrival_time ? \Carbon\Carbon::parse($attendance->arrival_time)->format('H:i') : '';
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="p-3">
                                                <div class="font-medium text-gray-900">{{ $worker->first_name }} {{ $worker->last_name }}</div>
                                                <div class="text-xs text-gray-500">ID: {{ $worker->id_number }}</div>
                                                <input type="hidden" name="attendances[{{ $index }}][cleaning_worker_id]" value="{{ $worker->id }}">
                                            </td>
                                            <td class="p-3 text-center">
                                                <input type="checkbox" 
                                                    name="attendances[{{ $index }}][is_present]" 
                                                    value="1"
                                                    class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500 w-5 h-5 cursor-pointer"
                                                    {{ $isPresent ? 'checked' : '' }}>
                                            </td>
                                            <td class="p-3">
                                                <input type="time" 
                                                    name="attendances[{{ $index }}][arrival_time]" 
                                                    value="{{ $arrivalTime }}"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="p-4 text-center text-sm text-gray-500">No active workers found in this Business Unit.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Zero Worker Reason <span class="text-gray-400 font-normal">(Required if completing with 0 workers)</span></label>
                            <textarea name="zero_worker_reason" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">{{ $dailyControl->zero_worker_reason }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="submit" name="action" value="save" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                                Save Draft
                            </button>
                            <button type="submit" name="action" value="complete" class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm shadow-cyan-600/20 transition-all text-sm">
                                Complete Workforce Check
                            </button>
                        </div>
                    </form>
                @else
                    <!-- Post-Check Workforce Status -->
                    <div class="flex items-center gap-6 mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="text-center">
                            <div class="text-2xl font-black text-gray-900">{{ $dailyControl->workerAttendances->count() }}</div>
                            <div class="text-xs font-semibold text-gray-500 uppercase">Assigned</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-black text-blue-600">{{ $dailyControl->workerAttendances->whereIn('status', ['Present', 'CheckedOut'])->count() }}</div>
                            <div class="text-xs font-semibold text-blue-600 uppercase">Working</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-black text-green-600">{{ $dailyControl->workerAttendances->where('status', 'CheckedOut')->count() }}</div>
                            <div class="text-xs font-semibold text-green-600 uppercase">Checked Out</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-black text-red-600">{{ $dailyControl->workerAttendances->where('status', 'Absent')->count() }}</div>
                            <div class="text-xs font-semibold text-red-600 uppercase">Absent</div>
                        </div>
                    </div>

                    <div class="overflow-x-auto border rounded-xl">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="p-3 font-semibold border-b">Worker</th>
                                    <th class="p-3 font-semibold border-b text-center">Status</th>
                                    <th class="p-3 font-semibold border-b text-center">Check In</th>
                                    <th class="p-3 font-semibold border-b text-center">Check Out</th>
                                    <th class="p-3 font-semibold border-b text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($dailyControl->workerAttendances as $attendance)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-3">
                                            <div class="font-medium text-gray-900">{{ $attendance->cleaningWorker->first_name }} {{ $attendance->cleaningWorker->last_name }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $attendance->cleaningWorker->id_number }}</div>
                                        </td>
                                        <td class="p-3 text-center">
                                            @if($attendance->status === 'Present')
                                                <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Present</span>
                                            @elseif($attendance->status === 'CheckedOut')
                                                <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Checked Out</span>
                                            @elseif($attendance->status === 'Absent')
                                                <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Absent</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">{{ $attendance->status }}</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-center font-mono text-gray-600">
                                            {{ $attendance->check_in_at ? $attendance->check_in_at->format('H:i') : '--:--' }}
                                        </td>
                                        <td class="p-3 text-center font-mono text-gray-600">
                                            {{ $attendance->check_out_at ? $attendance->check_out_at->format('H:i') : '--:--' }}
                                        </td>
                                        <td class="p-3 text-right">
                                            @if($attendance->status === 'Present' && !in_array($dailyControl->status, ['Submitted', 'Approved']))
                                                <form method="POST" action="{{ route('cleaning.daily-control.workforce.checkout', [$dailyControl, $attendance]) }}" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-1 px-3 rounded shadow-sm text-xs transition-colors">
                                                        Check Out
                                                    </button>
                                                </form>
                                            @elseif($attendance->status === 'CheckedOut')
                                                <span class="text-xs text-green-600 font-semibold">Completed</span>
                                            @elseif($attendance->status === 'Absent')
                                                <span class="text-xs text-gray-400 font-semibold">No action</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @if($dailyControl->workforce_check_status === 'Completed')
        <!-- Work Activities Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">Work Activities</h2>
                    <p class="text-sm text-gray-500 mt-1">Record actual cleaning activities and assign participating workers.</p>
                </div>
            </div>
            
            @if($dailyControl->status !== 'Submitted')
            <div class="p-6 border-b border-gray-100">
                <form method="POST" action="{{ route('cleaning.daily-control.activities.store', $dailyControl) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Activity Name</label>
                            <input type="text" name="activity_name" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" placeholder="e.g. Washroom Deep Clean">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <select name="location_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                                <option value="">General / Multiple</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Participating Workers</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($dailyControl->workerAttendances->where('is_present', true) as $attendance)
                                <label class="flex items-center gap-2 text-sm text-gray-700 bg-gray-50 px-3 py-2 border rounded-md cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" name="worker_attendance_ids[]" value="{{ $attendance->id }}" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                                    {{ $attendance->cleaningWorker->first_name }} {{ $attendance->cleaningWorker->last_name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition-all text-sm">Add Activity</button>
                    </div>
                </form>
            </div>
            @endif
            
            @if($dailyControl->workActivities->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider">
                                <th class="p-4 font-semibold border-b">Activity</th>
                                <th class="p-4 font-semibold border-b">Workers</th>
                                <th class="p-4 font-semibold border-b text-center">Status</th>
                                <th class="p-4 font-semibold border-b text-center">Verification</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($dailyControl->workActivities as $activity)
                                <tr>
                                    <td class="p-4 align-top">
                                        <div class="font-bold text-gray-900">{{ $activity->activity_name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $activity->location ? $activity->location->name : 'General' }}</div>
                                    </td>
                                    <td class="p-4 align-top">
                                        <div class="text-xs text-gray-600">
                                            @foreach($activity->workerAttendances as $wa)
                                                <div>• {{ $wa->cleaningWorker->first_name }} {{ $wa->cleaningWorker->last_name }}</div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="p-4 align-top text-center">
                                        @if($dailyControl->status === 'Submitted')
                                            <span class="font-bold">{{ $activity->status }}</span>
                                        @else
                                            <form method="POST" action="{{ route('cleaning.daily-control.activities.status', $activity) }}" class="inline-block" id="status-form-{{ $activity->id }}">
                                                @csrf @method('PATCH')
                                                <select name="status" onchange="if(this.value === 'Incomplete') { let n = prompt('Reason for incomplete:'); if(!n) { this.value = '{{ $activity->status }}'; return; } else { let i = document.createElement('input'); i.type='hidden'; i.name='notes'; i.value=n; this.form.appendChild(i); } } this.form.submit();" class="text-xs rounded border-gray-300 p-1 mb-1 shadow-sm">
                                                    <option value="Pending" {{ $activity->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="Started" {{ $activity->status == 'Started' ? 'selected' : '' }}>Started</option>
                                                    <option value="Completed" {{ $activity->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="Incomplete" {{ $activity->status == 'Incomplete' ? 'selected' : '' }}>Incomplete</option>
                                                </select>
                                            </form>
                                        @endif
                                        @if($activity->started_at)
                                            <div class="text-[10px] text-gray-400">Started: {{ $activity->started_at->format('H:i') }}</div>
                                        @endif
                                        @if($activity->completed_at)
                                            <div class="text-[10px] text-gray-400">Ended: {{ $activity->completed_at->format('H:i') }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 align-top text-center">
                                        @if($dailyControl->status === 'Submitted')
                                            <span class="font-bold {{ $activity->verification_status == 'Verified' ? 'text-green-700' : '' }}">{{ $activity->verification_status }}</span>
                                        @else
                                            <form method="POST" action="{{ route('cleaning.daily-control.activities.verification', $activity) }}" class="inline-block">
                                                @csrf @method('PATCH')
                                                <select name="verification_status" onchange="this.form.submit()" class="text-xs rounded border-gray-300 p-1 shadow-sm {{ $activity->verification_status == 'Verified' ? 'bg-green-50 text-green-700' : ($activity->verification_status == 'Rejected' ? 'bg-red-50 text-red-700' : '') }}">
                                                    <option value="Pending" {{ $activity->verification_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="Verified" {{ $activity->verification_status == 'Verified' ? 'selected' : '' }}>Verified</option>
                                                    <option value="Rejected" {{ $activity->verification_status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                </select>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500 text-sm">No work activities recorded yet.</div>
            @endif
        </div>
        
        <!-- Operational Issues Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">Operational Issues</h2>
            </div>
            
            @if($dailyControl->status !== 'Submitted')
            <div class="p-6 border-b border-gray-100">
                <form method="POST" action="{{ route('cleaning.daily-control.issues.store', $dailyControl) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Issue Type</label>
                            <select name="issue_type" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                                <option value="">Select Type</option>
                                <option value="Equipment Failure">Equipment Failure</option>
                                <option value="Supply Shortage">Supply Shortage</option>
                                <option value="Safety Hazard">Safety Hazard</option>
                                <option value="Staffing">Staffing</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" name="description" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition-all text-sm">Report Issue</button>
                    </div>
                </form>
            </div>
            @endif
            
            @if($dailyControl->operationalIssues->count() > 0)
                <div class="overflow-x-auto p-6 bg-gray-50">
                    <div class="space-y-4">
                        @foreach($dailyControl->operationalIssues as $issue)
                            <div class="bg-white border rounded-lg p-4 shadow-sm {{ $issue->status == 'Resolved' ? 'border-green-200' : 'border-amber-200 border-l-4 border-l-amber-500' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-sm text-gray-900">{{ $issue->issue_type }}</div>
                                    @if($issue->status == 'Resolved')
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded font-medium">Resolved at {{ $issue->resolved_at->format('H:i') }}</span>
                                    @else
                                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded font-medium">Open</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-700 mb-3">{{ $issue->description }}</div>
                                
                                @if($issue->status == 'Open')
                                    @if($dailyControl->status !== 'Submitted')
                                        <form method="POST" action="{{ route('cleaning.daily-control.issues.resolve', $issue) }}" class="mt-2 flex gap-2">
                                            @csrf @method('PATCH')
                                            <input type="text" name="resolution_notes" placeholder="Resolution notes..." required class="flex-1 rounded-md border-gray-300 text-xs shadow-sm">
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded">Mark Resolved</button>
                                        </form>
                                    @endif
                                @else
                                    <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded">
                                        <strong>Resolution:</strong> {{ $issue->resolution_notes }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Submission Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900">Finalize Today's Operations</h3>
                <p class="text-sm text-gray-500">Review all information before submitting to the coordinator. This action cannot be undone.</p>
            </div>
            @if($dailyControl->status === 'Submitted')
                <div class="bg-green-100 text-green-800 font-bold px-4 py-2 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Submitted
                </div>
            @else
                <form method="POST" action="{{ route('cleaning.daily-control.submit', $dailyControl) }}">
                    @csrf
                    <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-all">Submit Daily Control</button>
                </form>
            @endif
        </div>
    @endif
    @endif
</div>
@endsection

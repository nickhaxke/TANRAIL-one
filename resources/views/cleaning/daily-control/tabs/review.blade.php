@php
    $canSubmit = true;
    $blockingReasons = [];

    if ($dailyControl->workforce_check_status !== 'Completed') {
        $canSubmit = false;
        $blockingReasons[] = "Morning workforce check-in is not complete.";
    }

    $stillPresent = $dailyControl->workerAttendances->where('status', 'Present')->count();
    if ($stillPresent > 0) {
        $canSubmit = false;
        $blockingReasons[] = "There are still $stillPresent workers checked in. All workers must be checked out.";
    }

    $incompleteOps = $dailyControl->workActivities->whereIn('status', ['Pending', 'Started'])->count();
    if ($incompleteOps > 0) {
        $canSubmit = false;
        $blockingReasons[] = "There are $incompleteOps operations still In Progress or Planned. They must be Completed, Incomplete, or Cancelled.";
    }
    
    // For specific handlers, we could add more blocking rules here later.
@endphp

<div class="mb-6 flex flex-col md:flex-row gap-6">
    <!-- Review Content -->
    <div class="flex-1 space-y-6">
        
        <!-- Summary Report -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gray-50 flex items-center gap-3">
                <div class="p-2 bg-cyan-100 text-cyan-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h2 class="text-lg font-black text-gray-900">Shift End Report</h2>
                    <p class="text-sm text-gray-500">Auto-generated summary for today's operations.</p>
                </div>
            </div>

            <!-- Workforce Summary -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-xs font-bold tracking-widest text-gray-400 uppercase mb-4">Workforce</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div class="text-2xl font-black text-gray-900">{{ $dailyControl->workerAttendances->count() }}</div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Assigned</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="text-2xl font-black text-green-600">{{ $dailyControl->workerAttendances->where('status', 'CheckedOut')->count() }}</div>
                        <div class="text-[10px] font-bold text-green-600 uppercase tracking-wider">Checked Out</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                        <div class="text-2xl font-black text-red-600">{{ $dailyControl->workerAttendances->where('status', 'Absent')->count() }}</div>
                        <div class="text-[10px] font-bold text-red-600 uppercase tracking-wider">Absent</div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 {{ $stillPresent > 0 ? 'ring-2 ring-red-400' : '' }}">
                        <div class="text-2xl font-black text-blue-600">{{ $stillPresent }}</div>
                        <div class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Still Present</div>
                    </div>
                </div>
            </div>

            <!-- Operations Summary -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-xs font-bold tracking-widest text-gray-400 uppercase mb-4">Operations</h3>
                
                @if($dailyControl->workActivities->isEmpty())
                    <p class="text-sm text-gray-500 italic">No operations recorded.</p>
                @else
                    <div class="space-y-4">
                        @foreach($dailyControl->workActivities->groupBy('cleaning_service_type_id') as $typeId => $activities)
                            @php
                                $type = $activities->first()->serviceType;
                            @endphp
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                    <span class="text-sm font-bold text-gray-900">{{ $type ? $type->name : 'Unknown Service' }}</span>
                                </div>
                                <div class="p-0">
                                    <table class="w-full text-sm text-left">
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($activities as $activity)
                                                <tr>
                                                    <td class="p-3 pl-4 font-medium text-gray-800">{{ $activity->activity_name }}</td>
                                                    <td class="p-3 text-right pr-4">
                                                        @if($activity->status === 'Completed')
                                                            <span class="text-xs font-bold text-green-600 uppercase tracking-wider">Completed</span>
                                                        @elseif($activity->status === 'Incomplete')
                                                            <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Incomplete</span>
                                                        @else
                                                            <span class="text-xs font-bold text-red-600 uppercase tracking-wider">{{ $activity->status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Issues Summary -->
            <div class="p-6">
                <h3 class="text-xs font-bold tracking-widest text-gray-400 uppercase mb-4">Issues</h3>
                @if($dailyControl->operationalIssues->isEmpty())
                    <p class="text-sm text-gray-500 italic">No issues reported.</p>
                @else
                    <div class="flex gap-4">
                        <div class="bg-amber-50 px-4 py-2 rounded-lg border border-amber-100">
                            <span class="text-lg font-black text-amber-600 mr-2">{{ $dailyControl->operationalIssues->where('status', 'Open')->count() }}</span>
                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Open</span>
                        </div>
                        <div class="bg-green-50 px-4 py-2 rounded-lg border border-green-100">
                            <span class="text-lg font-black text-green-600 mr-2">{{ $dailyControl->operationalIssues->where('status', 'Resolved')->count() }}</span>
                            <span class="text-[10px] font-bold text-green-600 uppercase tracking-wider">Resolved</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Submission Panel -->
    <div class="md:w-80 flex-shrink-0">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-900">Submission</h3>
                <p class="text-xs text-gray-500 mt-1">Finalize and lock the shift record.</p>
            </div>
            
            @if($dailyControl->status === 'Submitted')
                <div class="p-6 bg-green-50 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h4 class="font-bold text-green-900 mb-1">Shift Submitted</h4>
                    <p class="text-sm text-green-700">This record is locked and pending coordinator review.</p>
                </div>
            @else
                <div class="p-6">
                    @if(!$canSubmit)
                        <div class="mb-6 bg-red-50 border border-red-100 rounded-lg p-4">
                            <h4 class="text-sm font-bold text-red-800 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Cannot Submit Yet
                            </h4>
                            <ul class="text-xs text-red-700 space-y-1 list-disc list-inside">
                                @foreach($blockingReasons as $reason)
                                    <li>{{ $reason }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="mb-6 bg-green-50 border border-green-100 rounded-lg p-4">
                            <h4 class="text-sm font-bold text-green-800 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Ready to Submit
                            </h4>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('cleaning.daily-control.submit', $dailyControl) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Supervisor Final Remarks <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <textarea name="supervisor_remarks" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" placeholder="Any final notes about this shift..."></textarea>
                        </div>
                        
                        <button type="submit" class="w-full font-bold py-3 px-4 rounded-lg shadow-md transition-all text-sm flex justify-center items-center gap-2 
                            {{ $canSubmit ? 'bg-cyan-600 hover:bg-cyan-700 text-white shadow-cyan-600/20' : 'bg-gray-200 text-gray-400 cursor-not-allowed border border-gray-300 shadow-none' }}" 
                            {{ !$canSubmit ? 'disabled' : '' }}
                            onclick="return confirm('Are you sure? Submitting will lock all records for this shift.');">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Submit Shift Report
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

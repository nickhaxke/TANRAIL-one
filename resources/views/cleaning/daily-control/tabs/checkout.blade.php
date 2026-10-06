<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                Shift Check-out
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage worker check-outs as they finish their shift.</p>
        </div>
        
        @if($dailyControl->workforce_check_status === 'Completed' && $dailyControl->status !== 'Submitted')
            @php
                $eligibleForBulk = $dailyControl->workerAttendances->where('status', 'Present')->count();
            @endphp
            @if($eligibleForBulk > 0)
                <form method="POST" action="{{ route('cleaning.daily-control.workforce.checkout-all', $dailyControl) }}">
                    @csrf
                    <button type="submit" class="bg-gray-900 hover:bg-black text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all text-sm flex items-center gap-2" onclick="return confirm('Check out all {{ $eligibleForBulk }} remaining workers now?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Check Out All ({{ $eligibleForBulk }})
                    </button>
                </form>
            @endif
        @endif
    </div>

    @if($dailyControl->workforce_check_status !== 'Completed')
        <div class="p-12 text-center bg-white">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-50 text-amber-600 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Check-in Required First</h3>
            <p class="text-gray-500 max-w-md mx-auto">You cannot check workers out until the initial morning check-in is complete.</p>
        </div>
    @else
        @php
            $presentCount = $dailyControl->workerAttendances->where('status', 'Present')->count();
        @endphp

        @if($presentCount > 0)
            <div class="p-6 bg-blue-50 border-b border-blue-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-black text-xl">
                        {{ $presentCount }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Workers Still Working</h3>
                        <p class="text-sm text-gray-600">These workers are currently checked in and need to be checked out before shift end.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="p-6 bg-green-50 border-b border-green-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">All Workers Checked Out</h3>
                        <p class="text-sm text-gray-600">There are no workers remaining on site for this shift.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto p-0">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold border-b">Worker</th>
                        <th class="p-4 font-semibold border-b text-center w-32">Status</th>
                        <th class="p-4 font-semibold border-b text-center w-32">Check-in</th>
                        <th class="p-4 font-semibold border-b text-center w-32">Check-out</th>
                        <th class="p-4 font-semibold border-b text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($dailyControl->workerAttendances as $attendance)
                        @if($attendance->status === 'Absent') @continue @endif
                        
                        <tr class="hover:bg-gray-50 transition-colors {{ $attendance->status === 'CheckedOut' ? 'opacity-70' : '' }}">
                            <td class="p-4">
                                <div class="font-bold text-gray-900">{{ $attendance->cleaningWorker->first_name }} {{ $attendance->cleaningWorker->last_name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">ID: {{ $attendance->cleaningWorker->id_number }}</div>
                            </td>
                            <td class="p-4 text-center">
                                @if($attendance->status === 'Present')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider shadow-sm">Present</span>
                                @elseif($attendance->status === 'CheckedOut')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-wider">Checked Out</span>
                                @endif
                            </td>
                            <td class="p-4 text-center font-mono text-xs font-semibold text-gray-900">
                                {{ $attendance->check_in_at ? $attendance->check_in_at->format('H:i') : '--:--' }}
                            </td>
                            <td class="p-4 text-center font-mono text-xs font-semibold {{ $attendance->check_out_at ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $attendance->check_out_at ? $attendance->check_out_at->format('H:i') : '--:--' }}
                            </td>
                            <td class="p-4 text-right">
                                @if($attendance->status === 'Present' && !in_array($dailyControl->status, ['Submitted', 'Approved']))
                                    <form method="POST" action="{{ route('cleaning.daily-control.workforce.checkout', [$dailyControl, $attendance]) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="bg-white border-2 border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white font-bold py-1.5 px-4 rounded-lg shadow-sm text-xs transition-colors">
                                            Check Out
                                        </button>
                                    </form>
                                @elseif($attendance->status === 'CheckedOut')
                                    <span class="text-xs text-green-600 font-bold flex items-center justify-end gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Done
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

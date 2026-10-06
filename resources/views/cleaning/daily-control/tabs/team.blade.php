<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                Team Attendance
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage today's shift workforce.</p>
        </div>
        
        @if($dailyControl->workforce_check_status === 'Completed')
            <div class="flex items-center gap-3">
                <div class="bg-white border border-gray-200 rounded-lg px-3 py-1.5 flex gap-4 text-sm shadow-sm">
                    <div class="text-center">
                        <span class="block text-[10px] uppercase font-bold text-gray-400">Assigned</span>
                        <span class="block font-black text-gray-900">{{ $dailyControl->workerAttendances->count() }}</span>
                    </div>
                    <div class="text-center border-l border-gray-100 pl-4">
                        <span class="block text-[10px] uppercase font-bold text-blue-500">Working</span>
                        <span class="block font-black text-blue-600">{{ $dailyControl->workerAttendances->whereIn('status', ['Present'])->count() }}</span>
                    </div>
                    <div class="text-center border-l border-gray-100 pl-4">
                        <span class="block text-[10px] uppercase font-bold text-green-500">Done</span>
                        <span class="block font-black text-green-600">{{ $dailyControl->workerAttendances->where('status', 'CheckedOut')->count() }}</span>
                    </div>
                    <div class="text-center border-l border-gray-100 pl-4">
                        <span class="block text-[10px] uppercase font-bold text-red-400">Absent</span>
                        <span class="block font-black text-red-500">{{ $dailyControl->workerAttendances->where('status', 'Absent')->count() }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="p-6">
        @if($dailyControl->workforce_check_status !== 'Completed')
            <div class="mb-4 bg-blue-50 text-blue-800 p-4 rounded-xl text-sm border border-blue-100 flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <strong class="font-bold block mb-1">Morning Check-in Required</strong>
                    You must complete the initial attendance check before managing operations.
                </div>
            </div>

            <form method="POST" action="{{ route('cleaning.daily-control.workforce', $dailyControl) }}">
                @csrf
                <div class="overflow-x-auto border border-gray-200 rounded-xl mb-6 shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                <th class="p-4 font-semibold border-b">Worker</th>
                                <th class="p-4 font-semibold border-b w-32 text-center">Present</th>
                                <th class="p-4 font-semibold border-b w-48">Arrival Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($workers as $index => $worker)
                                @php
                                    $attendance = $dailyControl->workerAttendances->where('cleaning_worker_id', $worker->id)->first();
                                    $isPresent = $attendance ? true : false;
                                    $arrivalTime = $attendance && $attendance->arrival_time ? \Carbon\Carbon::parse($attendance->arrival_time)->format('H:i') : '';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900">{{ $worker->first_name }} {{ $worker->last_name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">ID: {{ $worker->id_number }}</div>
                                        <input type="hidden" name="attendances[{{ $index }}][cleaning_worker_id]" value="{{ $worker->id }}">
                                    </td>
                                    <td class="p-4 text-center">
                                        <input type="checkbox" 
                                            name="attendances[{{ $index }}][is_present]" 
                                            value="1"
                                            class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500 w-5 h-5 cursor-pointer shadow-sm"
                                            {{ $isPresent ? 'checked' : '' }}>
                                    </td>
                                    <td class="p-4">
                                        <input type="time" 
                                            name="attendances[{{ $index }}][arrival_time]" 
                                            value="{{ $arrivalTime }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-8 text-center text-sm text-gray-500 italic">No workers assigned to this branch.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Zero Worker Reason</label>
                    <p class="text-xs text-gray-500 mb-2">If you are completing the check with 0 workers present, you must provide a reason.</p>
                    <textarea name="zero_worker_reason" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">{{ $dailyControl->zero_worker_reason }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="submit" name="action" value="save" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                        Save Draft
                    </button>
                    <button type="submit" name="action" value="complete" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md shadow-cyan-600/20 transition-all">
                        Complete Check-in
                    </button>
                </div>
            </form>
        @else
            <!-- Post-Check Attendance List -->
            <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold border-b">Worker</th>
                            <th class="p-4 font-semibold border-b text-center">Status</th>
                            <th class="p-4 font-semibold border-b text-center">Check-in</th>
                            <th class="p-4 font-semibold border-b text-center">Check-out</th>
                            <th class="p-4 font-semibold border-b text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($dailyControl->workerAttendances as $attendance)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900">{{ $attendance->cleaningWorker->first_name }} {{ $attendance->cleaningWorker->last_name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">ID: {{ $attendance->cleaningWorker->id_number }}</div>
                                </td>
                                <td class="p-4 text-center">
                                    @if($attendance->status === 'Expected')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-800 uppercase tracking-wider">Expected</span>
                                    @elseif($attendance->status === 'Present')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider shadow-sm">Present</span>
                                    @elseif($attendance->status === 'CheckedOut')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-wider">Checked Out</span>
                                    @elseif($attendance->status === 'Absent')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800 uppercase tracking-wider">Absent</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center font-mono text-xs font-semibold {{ $attendance->check_in_at ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $attendance->check_in_at ? $attendance->check_in_at->format('H:i') : '--:--' }}
                                </td>
                                <td class="p-4 text-center font-mono text-xs font-semibold {{ $attendance->check_out_at ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $attendance->check_out_at ? $attendance->check_out_at->format('H:i') : '--:--' }}
                                </td>
                                <td class="p-4 text-right">
                                    @if($attendance->status === 'Present' && !in_array($dailyControl->status, ['Submitted', 'Approved']))
                                        <form method="POST" action="{{ route('cleaning.daily-control.workforce.checkout', [$dailyControl, $attendance]) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="bg-white border border-gray-300 text-gray-700 hover:text-cyan-700 hover:border-cyan-300 hover:bg-cyan-50 font-bold py-1.5 px-3 rounded-lg shadow-sm text-xs transition-colors">
                                                Check Out
                                            </button>
                                        </form>
                                    @elseif($attendance->status === 'CheckedOut')
                                        <span class="text-xs text-green-600 font-bold flex items-center justify-end gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            Done
                                        </span>
                                    @elseif($attendance->status === 'Absent')
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">No Action</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-sm text-gray-500 italic">No workers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

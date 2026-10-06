@php
    $handlerType = $workActivity->serviceType ? $workActivity->serviceType->code : null;
    $hasHandler = false;
    $handler = null;
    
    if ($handlerType) {
        try {
            $handler = app(\App\Domains\Modules\Cleaning\Handlers\CleaningServiceHandlerFactory::class)->make($handlerType);
            $hasHandler = true;
        } catch (\Throwable $e) {
            $hasHandler = false;
        }
    }
@endphp

<div class="mb-6">
    <a href="{{ route('cleaning.daily-control.index', ['tab' => 'operations']) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-500 hover:text-gray-900 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Operations
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $workActivity->serviceType ? $workActivity->serviceType->name : 'Operation' }}</span>
                <span class="text-xs text-gray-300">|</span>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $workActivity->serviceTemplate ? $workActivity->serviceTemplate->name : 'Manual' }}</span>
                <span class="text-xs text-gray-300">|</span>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $workActivity->location ? $workActivity->location->name : 'General' }}</span>
            </div>
            <h2 class="text-2xl font-black text-gray-900">{{ $workActivity->activity_name }}</h2>
        </div>
        
        <div class="text-right">
            @if($workActivity->status === 'Pending')
                <form method="POST" action="{{ route('cleaning.daily-control.activities.status', $workActivity) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="Started">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-all">Start Operation</button>
                </form>
            @elseif($workActivity->status === 'Started')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 uppercase tracking-wider border border-blue-200 shadow-sm animate-pulse">In Progress</span>
            @elseif($workActivity->status === 'Completed')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-green-100 text-green-800 uppercase tracking-wider border border-green-200 shadow-sm">Completed</span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 uppercase tracking-wider border border-amber-200 shadow-sm">{{ $workActivity->status }}</span>
            @endif
        </div>
    </div>
    
    <div class="p-4 border-b border-gray-100 bg-white">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mr-2">Assigned Team:</span>
            @forelse($workActivity->workerAttendances as $wa)
                <span class="inline-flex items-center px-2.5 py-1 rounded-md border border-gray-200 bg-gray-50 text-xs font-bold text-gray-700 shadow-sm">
                    {{ $wa->cleaningWorker->first_name }} {{ $wa->cleaningWorker->last_name }}
                </span>
            @empty
                <span class="text-xs italic text-gray-400">No workers assigned</span>
            @endforelse
        </div>
    </div>

    <div class="p-0">
        @if(!$hasHandler)
            <div class="p-12 text-center bg-gray-50">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 text-amber-600 mb-4 shadow-sm border border-amber-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Service Configuration Pending</h3>
                <p class="text-gray-500 max-w-md mx-auto">This service type ({{ $workActivity->serviceType ? $workActivity->serviceType->name : 'Unknown' }}) has not been fully configured for execution yet. Please contact management.</p>
            </div>
        @elseif($workActivity->items->isEmpty())
            <div class="p-12 text-center bg-gray-50">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Service Items</h3>
                <p class="text-gray-500 max-w-md mx-auto">This operation template has no specific work items to execute.</p>
            </div>
        @else
            <form method="POST" action="{{ route('cleaning.daily-control.activities.items.update', $workActivity) }}">
                @csrf @method('PUT')
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                <th class="p-4 font-bold border-b">Task Item</th>
                                <th class="p-4 font-bold border-b w-40 text-center">Status</th>
                                <th class="p-4 font-bold border-b w-32 text-center">Target</th>
                                <th class="p-4 font-bold border-b w-32 text-center">Done</th>
                                <th class="p-4 font-bold border-b w-64">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($workActivity->items as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors {{ $item->status === 'Completed' ? 'bg-green-50/30' : '' }}">
                                    <td class="p-4 font-medium text-gray-900 align-top">
                                        {{ $item->label }}
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="p-4 align-top">
                                        <select name="items[{{ $index }}][status]" class="block w-full text-xs font-bold rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500
                                            {{ $item->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $item->status === 'Incomplete' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $item->status === 'Pending' ? 'bg-gray-100 text-gray-600' : '' }}
                                        " {{ $dailyControl->status === 'Submitted' ? 'disabled' : '' }}>
                                            <option value="Pending" {{ $item->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Completed" {{ $item->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Incomplete" {{ $item->status === 'Incomplete' ? 'selected' : '' }}>Incomplete</option>
                                        </select>
                                    </td>
                                    <td class="p-4 text-center align-top font-bold text-gray-500">
                                        {{ $item->target_qty !== null ? $item->target_qty : '-' }}
                                    </td>
                                    <td class="p-4 align-top">
                                        <input type="number" step="0.01" name="items[{{ $index }}][done_qty]" value="{{ $item->done_qty }}" class="block w-full text-center font-bold text-sm rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500" placeholder="-" {{ $dailyControl->status === 'Submitted' ? 'disabled' : '' }}>
                                    </td>
                                    <td class="p-4 align-top">
                                        <input type="text" name="items[{{ $index }}][remarks]" value="{{ $item->remarks }}" class="block w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500" placeholder="Optional notes..." {{ $dailyControl->status === 'Submitted' ? 'disabled' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($dailyControl->status !== 'Submitted')
                    <div class="p-6 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                        <div class="text-sm text-gray-500 italic">Don't forget to save your progress regularly.</div>
                        <button type="submit" class="bg-gray-900 hover:bg-black text-white font-bold py-2.5 px-8 rounded-lg shadow-md transition-all text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Save Task Results
                        </button>
                    </div>
                @endif
            </form>
        @endif
    </div>
</div>

@if($workActivity->status === 'Started' && $dailyControl->status !== 'Submitted')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 bg-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Finish Operation</h3>
                <p class="text-sm text-gray-500 mt-1">Mark the entire operation as complete once all item results are saved.</p>
            </div>
            
            <form method="POST" action="{{ route('cleaning.daily-control.activities.status', $workActivity) }}" class="flex items-center gap-3">
                @csrf @method('PATCH')
                
                <button type="submit" name="status" value="Incomplete" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-6 rounded-lg transition-colors text-sm" onclick="let reason = prompt('Reason for marking incomplete?'); if(reason) { let i = document.createElement('input'); i.type='hidden'; i.name='notes'; i.value=reason; this.form.appendChild(i); return true; } return false;">
                    Mark Incomplete
                </button>
                
                <button type="submit" name="status" value="Completed" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-8 rounded-lg shadow-md shadow-green-600/20 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Mark Completed
                </button>
            </form>
        </div>
    </div>
@endif

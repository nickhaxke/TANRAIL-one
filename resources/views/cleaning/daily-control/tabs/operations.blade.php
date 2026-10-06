<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                Today's Operations
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage and track cleaning operations for this shift.</p>
        </div>
        
        <div class="flex items-center gap-3 text-sm">
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span> Planned</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> In Progress</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Completed</span>
        </div>
    </div>

    @if($dailyControl->workforce_check_status !== 'Completed')
        <div class="p-8 text-center border-b border-gray-100">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-50 text-amber-600 mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Check-in Required</h3>
            <p class="text-gray-500 text-sm max-w-md mx-auto mb-4">You cannot start operations until you complete the morning workforce check-in on the Team tab.</p>
            <a href="{{ route('cleaning.daily-control.index', ['tab' => 'team']) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                Go to Team Check-in
            </a>
        </div>
    @else
        <!-- Create Operation Form -->
        @if($dailyControl->status !== 'Submitted')
            <div class="p-6 border-b border-gray-200 bg-white">
                <h3 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wider">Start New Operation</h3>
                
                @if($activeServiceTypes->isEmpty())
                    <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm border border-red-100">
                        You do not have any active service assignments for this station today. Contact your coordinator.
                    </div>
                @else
                    <form method="POST" action="{{ route('cleaning.daily-control.activities.store', $dailyControl) }}">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-4">
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Service Type</label>
                                <select name="cleaning_service_type_id" id="service_type_select" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                                    <option value="">Select Service...</option>
                                    @foreach($activeServiceTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Template</label>
                                <select name="cleaning_service_template_id" id="template_select" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                                    <option value="">Select Service First...</option>
                                    <!-- Options will be populated via JS or we can dump all active templates and show/hide based on service type -->
                                    @foreach(\App\Domains\Modules\Cleaning\Models\CleaningServiceTemplate::where('is_active', true)->whereIn('cleaning_service_type_id', $activeServiceTypes->pluck('id'))->get() as $template)
                                        <option value="{{ $template->id }}" data-type-id="{{ $template->cleaning_service_type_id }}" class="hidden">{{ $template->name }} (v{{ $template->version }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Activity Name</label>
                                <input type="text" name="activity_name" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" placeholder="e.g. Morning Clean">
                            </div>
                            
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Location</label>
                                <select name="location_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                                    <option value="">General</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Assign Present Workers</label>
                            <div class="flex flex-wrap gap-2">
                                @forelse($dailyControl->workerAttendances->where('status', 'Present') as $attendance)
                                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 shadow-sm px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="checkbox" name="worker_attendance_ids[]" value="{{ $attendance->id }}" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                                        {{ $attendance->cleaningWorker->first_name }} {{ $attendance->cleaningWorker->last_name }}
                                    </label>
                                @empty
                                    <div class="text-sm text-amber-600 italic">No workers are currently present.</div>
                                @endforelse
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-gray-900 hover:bg-black text-white font-bold py-2 px-6 rounded-lg shadow-md transition-all text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Start Operation
                            </button>
                        </div>
                    </form>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const serviceSelect = document.getElementById('service_type_select');
                            const templateSelect = document.getElementById('template_select');
                            const options = Array.from(templateSelect.options);
                            
                            serviceSelect.addEventListener('change', function() {
                                const selectedType = this.value;
                                templateSelect.value = '';
                                
                                options.forEach(opt => {
                                    if (opt.value === '') return;
                                    
                                    if (opt.getAttribute('data-type-id') === selectedType) {
                                        opt.classList.remove('hidden');
                                        opt.style.display = '';
                                    } else {
                                        opt.classList.add('hidden');
                                        opt.style.display = 'none';
                                    }
                                });
                            });
                        });
                    </script>
                @endif
            </div>
        @endif

        <!-- Operations List -->
        <div class="bg-gray-50">
            @if($dailyControl->workActivities->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($dailyControl->workActivities as $activity)
                        @php
                            // Calculate simple progress if possible
                            $totalItems = $activity->items->count();
                            $completedItems = $activity->items->where('status', 'Completed')->count();
                            $progress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
                        @endphp
                        <div class="p-6 bg-white hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col lg:flex-row justify-between gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-1">
                                        <h3 class="text-lg font-black text-gray-900">{{ $activity->activity_name }}</h3>
                                        @if($activity->status === 'Pending')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-wider">Planned</span>
                                        @elseif($activity->status === 'Started')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider shadow-sm">In Progress</span>
                                        @elseif($activity->status === 'Completed')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-wider">Completed</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">{{ $activity->status }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500 mb-3">
                                        <div class="flex items-center gap-1 font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            {{ $activity->serviceType ? $activity->serviceType->name : 'Unknown Service' }}
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            {{ $activity->location ? $activity->location->name : 'General Location' }}
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $activity->started_at ? $activity->started_at->format('H:i') : 'Not started' }}
                                        </div>
                                    </div>

                                    <div class="text-xs font-semibold text-gray-500 mb-1">ASSIGNED TEAM</div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($activity->workerAttendances as $wa)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded border border-gray-200 bg-white text-xs font-medium text-gray-700">
                                                {{ $wa->cleaningWorker->first_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="lg:w-64 flex flex-col justify-center">
                                    <div class="mb-2 flex justify-between items-end">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Progress</span>
                                        <span class="text-sm font-black text-gray-900">{{ $progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                                        <div class="bg-cyan-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                    
                                    @if($dailyControl->status !== 'Submitted')
                                        <a href="{{ route('cleaning.daily-control.index', ['tab' => 'execute', 'activity_id' => $activity->id]) }}" class="block text-center w-full bg-white border-2 border-gray-900 hover:bg-gray-900 hover:text-white text-gray-900 font-bold py-2 px-4 rounded-lg transition-colors text-sm">
                                            {{ $activity->status === 'Pending' ? 'Start Execution' : ($activity->status === 'Completed' ? 'View Details' : 'Continue Work') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">No Operations Scheduled</h3>
                    <p class="text-gray-500 text-sm max-w-sm mx-auto">Use the form above to start a new cleaning operation for today's shift.</p>
                </div>
            @endif
        </div>
    @endif
</div>

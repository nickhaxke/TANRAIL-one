<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                Operational Issues
            </h2>
            <p class="text-sm text-gray-500 mt-1">Report and track problems blocking operations.</p>
        </div>
        
        <div class="flex items-center gap-3 text-sm font-bold">
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Open ({{ $dailyControl->operationalIssues->where('status', 'Open')->count() }})</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Resolved ({{ $dailyControl->operationalIssues->where('status', 'Resolved')->count() }})</span>
        </div>
    </div>
    
    @if($dailyControl->status !== 'Submitted')
        <div class="p-6 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wider">Report New Issue</h3>
            
            <form method="POST" action="{{ route('cleaning.daily-control.issues.store', $dailyControl) }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                    <div class="md:col-span-3">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Issue Type</label>
                        <select name="issue_type" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                            <option value="">Select Type...</option>
                            <option value="Equipment Failure">Equipment Failure</option>
                            <option value="Supply Shortage">Supply Shortage</option>
                            <option value="Safety Hazard">Safety Hazard</option>
                            <option value="Staffing">Staffing</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="md:col-span-7">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                        <input type="text" name="description" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" placeholder="Provide clear details of the problem...">
                    </div>
                    <div class="md:col-span-2 flex items-end">
                        <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md shadow-amber-600/20 transition-all text-sm">
                            Report Issue
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif
    
    <div class="bg-gray-50 p-6">
        @if($dailyControl->operationalIssues->count() > 0)
            <div class="space-y-4">
                @foreach($dailyControl->operationalIssues as $issue)
                    <div class="bg-white border rounded-xl p-5 shadow-sm {{ $issue->status == 'Resolved' ? 'border-green-200' : 'border-amber-300 border-l-4 border-l-amber-500' }}">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-bold text-gray-900">{{ $issue->issue_type }}</h4>
                                    @if($issue->status == 'Resolved')
                                        <span class="text-[10px] uppercase tracking-wider bg-green-100 text-green-800 px-2 py-0.5 rounded-full font-bold">Resolved</span>
                                    @else
                                        <span class="text-[10px] uppercase tracking-wider bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-bold">Open</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 font-mono">
                                    Reported: {{ $issue->created_at->format('H:i') }}
                                    @if($issue->status == 'Resolved')
                                        &middot; Resolved: {{ $issue->resolved_at->format('H:i') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-gray-700 text-sm mb-4">{{ $issue->description }}</p>
                        
                        @if($issue->status == 'Open')
                            @if($dailyControl->status !== 'Submitted')
                                <form method="POST" action="{{ route('cleaning.daily-control.issues.resolve', $issue) }}" class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row gap-3">
                                    @csrf @method('PATCH')
                                    <input type="text" name="resolution_notes" placeholder="How was this resolved?" required class="flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold text-sm px-6 py-2 rounded-lg shadow-md shadow-green-600/20 whitespace-nowrap transition-colors">
                                        Mark as Resolved
                                    </button>
                                </form>
                            @endif
                        @else
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Resolution</span>
                                <p class="text-sm text-gray-800 bg-green-50 p-3 rounded-lg border border-green-100">{{ $issue->resolution_notes }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center bg-white rounded-lg border border-gray-100">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-50 text-green-500 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">No Issues Reported</h3>
                <p class="text-sm text-gray-500">There are currently no operational issues recorded for this shift.</p>
            </div>
        @endif
    </div>
</div>

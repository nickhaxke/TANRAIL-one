@extends('layouts.cleaning')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Reports & Analysis</h1>
        <p class="text-slate-500 text-sm mt-1">Advanced reporting will be available in Phase 2</p>
    </div>

    <div class="bg-white p-12 rounded-2xl border border-slate-200 shadow-sm text-center">
        <div class="inline-flex h-16 w-16 rounded-full bg-cyan-50 text-cyan-500 items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Reports Coming Soon</h3>
        <p class="text-slate-500 max-w-md mx-auto">This section is deferred for the MVP. Check back after Phase 1 deployment for project-wide analytics and cost reports.</p>
    </div>
</div>
@endsection

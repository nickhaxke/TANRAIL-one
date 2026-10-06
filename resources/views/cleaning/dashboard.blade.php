@extends('layouts.cleaning')

@section('content')
<div class="max-w-5xl mx-auto w-full">
    <!-- Header -->
    <div class="mb-8 border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">TANRAIL ONE</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">Cleaning Operations</span>
                @if($dailyControl && $dailyControl->branch)
                    <span class="text-xs font-bold text-gray-400">|</span>
                    <span class="text-xs font-bold text-gray-500 uppercase">{{ $dailyControl->branch->name }}</span>
                @endif
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Supervisor Dashboard</h1>
        </div>
        <div class="text-right">
            <div class="text-sm font-medium text-gray-500">{{ today()->format('l, F j, Y') }}</div>
        </div>
    </div>

    @if(!$dailyControl)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center max-w-2xl mx-auto mt-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-cyan-50 text-cyan-600 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Ready for Today's Shift?</h2>
            <p class="text-gray-500 mb-8">Start the daily control to open your workspace, manage your team, and track operations.</p>
            
            <form method="POST" action="{{ route('cleaning.daily-control.store') }}">
                @csrf
                <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 px-8 rounded-lg shadow-md shadow-cyan-600/20 transition-all text-lg">
                    Start Today's Shift
                </button>
            </form>
        </div>
    @else
        <!-- Active Context Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex gap-8">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Supervisor</p>
                    <p class="font-bold text-gray-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Station</p>
                    <p class="font-bold text-gray-900">{{ $dailyControl->branch->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Shift</p>
                    <p class="font-bold text-gray-900">{{ $dailyControl->shift }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                    @if($dailyControl->status === 'Submitted')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">Submitted</span>
                    @elseif($dailyControl->workforce_check_status === 'Completed')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">In Progress</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Not Started (Pending Check-in)</span>
                    @endif
                </div>
            </div>
            
            <div>
                @if($dailyControl->status === 'Submitted')
                    <a href="{{ route('cleaning.daily-control.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-6 rounded-lg transition-all shadow-sm border border-gray-200">
                        View Submitted Report
                    </a>
                @elseif($dailyControl->workforce_check_status === 'Completed')
                    <a href="{{ route('cleaning.daily-control.index') }}" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2 px-6 rounded-lg shadow-md shadow-cyan-600/20 transition-all">
                        Continue Today's Shift
                    </a>
                @else
                    <a href="{{ route('cleaning.daily-control.index') }}" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-6 rounded-lg shadow-md shadow-amber-600/20 transition-all">
                        Complete Check-in
                    </a>
                @endif
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Team Metrics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Team
                    </h3>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-black text-gray-900">{{ $stats['assigned'] }}</p>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Assigned</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-blue-600">{{ $stats['present'] }}</p>
                        <p class="text-xs font-semibold text-blue-600 uppercase">Present</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-green-600">{{ $stats['checked_out'] }}</p>
                        <p class="text-xs font-semibold text-green-600 uppercase">Checked Out</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-red-600">{{ $stats['absent'] }}</p>
                        <p class="text-xs font-semibold text-red-600 uppercase">Absent</p>
                    </div>
                </div>
            </div>

            <!-- Operations Metrics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Operations
                    </h3>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-black text-gray-900">{{ $stats['ops_planned'] }}</p>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Planned</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-blue-600">{{ $stats['ops_in_progress'] }}</p>
                        <p class="text-xs font-semibold text-blue-600 uppercase">In Progress</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-green-600">{{ $stats['ops_completed'] }}</p>
                        <p class="text-xs font-semibold text-green-600 uppercase">Completed</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-amber-600">{{ $stats['ops_incomplete'] }}</p>
                        <p class="text-xs font-semibold text-amber-600 uppercase">Inc/Cancelled</p>
                    </div>
                </div>
            </div>

            <!-- Issues Metrics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Issues
                    </h3>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-black text-amber-600">{{ $stats['issues_open'] }}</p>
                        <p class="text-xs font-semibold text-amber-600 uppercase">Open</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-green-600">{{ $stats['issues_resolved'] }}</p>
                        <p class="text-xs font-semibold text-green-600 uppercase">Resolved</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

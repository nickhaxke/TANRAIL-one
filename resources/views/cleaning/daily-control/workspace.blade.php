@extends('layouts.cleaning')

@section('content')
<div class="max-w-6xl mx-auto w-full">
    <!-- Header -->
    <div class="mb-6 border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">TANRAIL ONE</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">Cleaning Operations</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-500 uppercase">{{ $branch->name }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Today's Workspace</h1>
        </div>
        <div class="text-right">
            <div class="text-sm font-medium text-gray-500">{{ today()->format('l, F j, Y') }} &mdash; Shift: {{ $dailyControl->shift }}</div>
            <div class="mt-1">
                @if($dailyControl->status === 'Submitted')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">Submitted</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">In Progress</span>
                @endif
            </div>
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

    <!-- Workspace Navigation -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 p-1 flex flex-wrap sm:flex-nowrap gap-1">
        <a href="{{ route('cleaning.daily-control.index', ['tab' => 'team']) }}" 
           class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold transition-all {{ $tab === 'team' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            Team
        </a>
        <a href="{{ route('cleaning.daily-control.index', ['tab' => 'operations']) }}" 
           class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold transition-all {{ $tab === 'operations' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            Operations
        </a>
        <a href="{{ route('cleaning.daily-control.index', ['tab' => 'issues']) }}" 
           class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold transition-all {{ $tab === 'issues' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            Issues
        </a>
        <a href="{{ route('cleaning.daily-control.index', ['tab' => 'checkout']) }}" 
           class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold transition-all {{ $tab === 'checkout' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
            Check-out
        </a>
        <a href="{{ route('cleaning.daily-control.index', ['tab' => 'review']) }}" 
           class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold transition-all {{ $tab === 'review' ? 'bg-cyan-600 text-white shadow-md' : 'text-cyan-700 hover:bg-cyan-50' }}">
            Review & Submit
        </a>
    </div>

    <!-- Tab Content -->
    <div>
        @include('cleaning.daily-control.tabs.' . $tab)
    </div>
</div>
@endsection

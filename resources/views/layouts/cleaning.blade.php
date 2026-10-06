<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TANRAIL') }} - Cleaning Operations</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex overflow-hidden">

    <!-- Left Sidebar Navigation -->
    <aside class="w-64 bg-[#0E1A38] border-r border-slate-800/80 flex flex-col shrink-0 h-full z-40 transition-all duration-300">
        
        <!-- Brand Area -->
        <div class="h-16 flex items-center px-4 border-b border-slate-800/80 shrink-0">
            <a href="{{ route('cleaning.dashboard') }}" class="flex items-center gap-2.5">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-cyan-500 to-cyan-400 text-slate-950 font-black text-sm flex items-center justify-center shadow-md shadow-cyan-500/20">
                    T
                </div>
                <div>
                    <div class="flex items-center gap-2 text-white font-black text-sm tracking-wide leading-tight">
                        <span>TANRAIL CLEANING</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-cyan-400/20 text-cyan-300 border border-cyan-400/30">OPS</span>
                    </div>
                    <div class="text-slate-400 text-[10px] truncate max-w-[160px] font-medium uppercase tracking-wider">
                        Facility Services
                    </div>
                </div>
            </a>
        </div>

        <!-- Scrollable Navigation Menu -->
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-6 custom-scrollbar">
            
            @php
                $user = auth()->user();
                $canMonitor = $user?->can('cleaning.operations.monitor') ?? false;
                $canManageOps = $user?->can('cleaning.operations.manage') ?? false;
                $canManageWorkers = $user?->can('cleaning.workers.manage') ?? false;
                $canViewWorkers = $user?->can('cleaning.workers.view') ?? false;
                $canViewStore = $user?->can('cleaning.store.view') ?? false;
                $canReceiveStore = $user?->can('cleaning.store.receive') ?? false;
            @endphp

            <!-- 1. DASHBOARD -->
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Overview</div>
                <div class="space-y-1">
                    <a href="{{ route('cleaning.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('cleaning.dashboard*') ? 'bg-cyan-600/10 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('cleaning.dashboard*') ? 'text-cyan-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- 2. OPERATIONS -->
            @if($canManageOps || $canMonitor)
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Operations</div>
                <div class="space-y-1">
                    @if($canManageOps)
                    <a href="{{ route('cleaning.daily-control.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('cleaning.daily-control*') ? 'bg-cyan-600/10 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('cleaning.daily-control*') ? 'text-cyan-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Today's Operations
                    </a>
                    @endif

                    @if($canMonitor)
                    <a href="{{ route('cleaning.coordinator.operations') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('cleaning.coordinator.operations*') ? 'bg-cyan-600/10 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('cleaning.coordinator.operations*') ? 'text-cyan-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Operations Monitor
                    </a>
                    @endif

                    <a href="{{ route('cleaning.timesheets.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('cleaning.timesheets*') ? 'bg-cyan-600/10 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('cleaning.timesheets*') ? 'text-cyan-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Timesheet Summary
                    </a>
                </div>
            </div>
            @endif

            <!-- 3. PERSONNEL -->
            @if($canViewWorkers || $canManageWorkers || $canMonitor)
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Personnel</div>
                <div class="space-y-1">
                    @if($canMonitor)
                    <a href="{{ route('cleaning.coordinator.supervisors') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('cleaning.coordinator.supervisors*') ? 'bg-cyan-600/10 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('cleaning.coordinator.supervisors*') ? 'text-cyan-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Supervisors
                    </a>
                    @endif

                    <a href="{{ route('cleaning.workers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('cleaning.workers*') ? 'bg-cyan-600/10 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('cleaning.workers*') ? 'text-cyan-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Cleaning Workers
                    </a>
                </div>
            </div>
            @endif

            <!-- 4. CENTRAL STORE -->
            @if($canViewStore)
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Central Cleaning Store</div>
                <div class="space-y-1">
                    <a href="{{ route('cleaning.store.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('cleaning.store.index') ? 'bg-cyan-600/10 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('cleaning.store.index') ? 'text-cyan-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        Central Stock
                    </a>
                    @if($canReceiveStore)
                    <a href="{{ route('cleaning.store.receive') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('cleaning.store.receive') ? 'bg-cyan-600/10 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('cleaning.store.receive') ? 'text-cyan-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        Receive Stock
                    </a>
                    @endif
                    <a href="{{ route('cleaning.store.movements') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('cleaning.store.movements') ? 'bg-cyan-600/10 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('cleaning.store.movements') ? 'text-cyan-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        Stock Movements
                    </a>
                </div>
            </div>
            @endif

            <!-- 5. REPORTS -->
            @if($canMonitor)
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Analysis</div>
                <div class="space-y-1">
                    <a href="{{ route('cleaning.coordinator.reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('cleaning.coordinator.reports*') ? 'bg-cyan-600/10 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('cleaning.coordinator.reports*') ? 'text-cyan-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Reports & Audit
                    </a>
                </div>
            </div>
            @endif

        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
        <!-- Top Header Bar -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between shrink-0 shadow-sm z-30">
            <!-- Breadcrumbs / Left side -->
            <div class="flex items-center gap-2 text-sm">
                <button class="md:hidden p-2 -ml-2 rounded-xl text-slate-500 hover:text-slate-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="hidden sm:flex items-center gap-2 text-slate-500">
                    <a href="{{ route('cleaning.dashboard') }}" class="font-bold hover:text-cyan-600 transition-colors uppercase tracking-wider text-[11px]">Cleaning</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800 font-bold uppercase tracking-wider text-[11px]">{{ str_replace('_', ' ', request()->segment(2) ?? 'dashboard') }}</span>
                </div>
            </div>

            <!-- Right side / User Actions -->
            <div class="flex items-center gap-3">
                @php
                    $currentUser = auth()->user();
                    $userRoles = $currentUser?->roles?->pluck('name')->all() ?? [];
                    $canAccessManagement = in_array('Super Admin', $userRoles) || in_array('Business Unit Director', $userRoles) || in_array('Cleaning Supervisor', $userRoles);
                @endphp

                @if($canAccessManagement)
                <a href="{{ route('management.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-sm text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-300 hover:bg-slate-200 transition-all uppercase tracking-wider">
                    Management Portal &rarr;
                </a>
                @endif

                <div class="h-6 border-l border-slate-300 mx-1 hidden sm:block"></div>

                <!-- Cashier / User Indicator -->
                <div class="flex items-center gap-2 pl-1">
                    <div class="h-8 w-8 rounded bg-slate-800 text-white font-bold text-xs flex items-center justify-center">
                        {{ substr($currentUser->name ?? 'C', 0, 1) }}
                    </div>
                    <div class="hidden xl:block text-left">
                        <div class="text-[11px] font-bold text-slate-900 uppercase tracking-wide leading-tight">{{ $currentUser->name ?? 'Station Staff' }}</div>
                        <div class="text-[10px] text-slate-500 font-medium uppercase tracking-wider leading-tight">{{ $userRoles[0] ?? 'Cashier' }}</div>
                    </div>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="ml-1">
                    @csrf
                    <button type="submit" title="Sign Out" class="p-2 rounded text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Content scrollable area -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-6 flex flex-col custom-scrollbar">
            @yield('content')
        </main>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.2); 
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.4); 
        }
    </style>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TANRAIL') }} - Restaurant Operations</title>

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
            <a href="{{ route('restaurant.dashboard') }}" class="flex items-center gap-2.5">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 font-black text-sm flex items-center justify-center shadow-md shadow-amber-500/20">
                    T
                </div>
                <div>
                    <div class="text-white font-black text-sm tracking-wide leading-tight flex items-center gap-1.5">
                        <span>TANRAIL DINING</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-amber-400/20 text-amber-300 border border-amber-400/30">POS</span>
                    </div>
                    <div class="text-slate-400 text-[10px] truncate max-w-[160px] font-medium uppercase tracking-wider">
                        {{ $branch->name ?? 'Station Dining Concourse' }}
                    </div>
                </div>
            </a>
        </div>

        <!-- Scrollable Navigation Menu -->
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-6 custom-scrollbar">
            
            <!-- 1. DASHBOARD -->
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Overview</div>
                <div class="space-y-1">
                    <a href="{{ route('restaurant.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.dashboard*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.dashboard*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- 2. SALES / POS -->
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Front of House</div>
                <div class="space-y-1">
                    <a href="{{ route('restaurant.pos') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.pos') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.pos') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        New Sale (POS)
                    </a>
                    <a href="{{ route('restaurant.orders') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.orders') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.orders') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Orders
                    </a>
                    <a href="{{ route('restaurant.sales.history') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.sales.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.sales.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Sales History
                    </a>
                </div>
            </div>

            <!-- 3. PRODUCTS & MENU -->
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Catalog</div>
                <div class="space-y-1">
                    <a href="{{ route('restaurant.menu') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.menu') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.menu') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        Products & Menu
                    </a>
                    <a href="{{ route('restaurant.menu.categories') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.menu.categories') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.menu.categories') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        Categories
                    </a>
                </div>
            </div>

            <!-- 4. INVENTORY / STORE & 5. PURCHASING -->
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Back of House</div>
                <div class="space-y-1">
                    <a href="{{ route('restaurant.store') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.store') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.store') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        Inventory / Store
                    </a>
                    <a href="{{ route('restaurant.locations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.locations.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.locations.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        Store Locations
                    </a>
                    <a href="{{ route('restaurant.purchasing.requests') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.purchasing.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.purchasing.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Purchasing
                    </a>
                </div>
            </div>

            <!-- 6. CASH & SHIFTS -->
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Operations</div>
                <div class="space-y-1">
                    <a href="{{ route('restaurant.shifts') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.shifts*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.shifts*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Cash & Shifts
                    </a>
                    <a href="{{ route('restaurant.expenses') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.expenses*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.expenses*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Expenses
                    </a>
                </div>
            </div>

            <!-- 7-11. MANAGEMENT & REPORTS -->
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase">Management</div>
                <div class="space-y-1">
                    <a href="{{ route('restaurant.customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.customers.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.customers.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Customers
                    </a>
                    <a href="{{ route('restaurant.reports.sales') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.reports.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.reports.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Reports
                    </a>
                    <a href="{{ route('restaurant.approvals.discounts') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.approvals.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.approvals.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Approvals
                    </a>
                    <a href="{{ route('restaurant.audit.activity') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.audit.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.audit.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                        Audit
                    </a>
                    <a href="{{ route('restaurant.settings.restaurant') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('restaurant.settings.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('restaurant.settings.*') ? 'text-blue-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Settings
                    </a>
                </div>
            </div>

            <!-- Temporary Kitchen link for reference -->
            @if(request()->routeIs('restaurant.kitchen'))
            <div class="pt-4 mt-4 border-t border-slate-800/50">
                <a href="{{ route('restaurant.kitchen') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors bg-amber-600/10 text-amber-500">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" /></svg>
                    Kitchen (KDS)
                </a>
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
                    <a href="{{ route('restaurant.dashboard') }}" class="font-bold hover:text-blue-600 transition-colors uppercase tracking-wider text-[11px]">Restaurant</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800 font-bold uppercase tracking-wider text-[11px]">{{ str_replace('_', ' ', request()->segment(2) ?? 'dashboard') }}</span>
                </div>
            </div>

            <!-- Right side / User Actions -->
            <div class="flex items-center gap-3">
                @php
                    $currentUser = auth()->user();
                    $userRoles = $currentUser?->roles?->pluck('name')->all() ?? [];
                    $canAccessManagement = in_array('Super Admin', $userRoles) || in_array('Business Unit Director', $userRoles) || in_array('Station Supervisor', $userRoles);
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

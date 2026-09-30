<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TANRAIL ONE') }} - Management</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-gray-50" x-data="{ sidebarOpen: false }">

    @php
        $sidebarLinks = [
            [
                'name' => 'Dashboard',
                'route' => 'management.dashboard',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />'
            ],
            [
                'name' => 'Business Units & Branches',
                'route' => 'management.organization.business-units',
                'active_pattern' => ['management.organization.*', 'management.business-units.*', 'management.branches.*'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />',
                'submenus' => [
                    ['name' => 'Business Units', 'route' => 'management.organization.business-units', 'active_pattern' => ['management.organization.business-units', 'management.business-units.*']],
                    ['name' => 'Branches & Stations', 'route' => 'management.organization.branches', 'active_pattern' => ['management.organization.branches', 'management.branches.*']],
                    ['name' => 'Hierarchy Topology', 'route' => 'management.organization.structure'],
                    ['name' => 'Corporate Profile', 'route' => 'management.organization.index'],
                ]
            ],
            [
                'name' => 'Procurement & Approvals',
                'route' => 'management.procurement',
                'active_pattern' => ['management.procurement*', 'management.approvals*'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />',
                'submenus' => [
                    ['name' => 'Purchase Orders', 'route' => 'management.procurement', 'active_pattern' => 'management.procurement*'],
                    ['name' => 'Approval Center', 'route' => 'management.approvals', 'active_pattern' => 'management.approvals*'],
                ]
            ],
            [
                'name' => 'Inventory',
                'route' => 'management.inventory',
                'active_pattern' => 'management.inventory*',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />'
            ],
            [
                'name' => 'Finance & Billing',
                'route' => 'management.finance',
                'active_pattern' => 'management.finance*',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
            ],
            [
                'name' => 'Users & Staff',
                'route' => 'management.users.index',
                'active_pattern' => ['management.users.*', 'management.roles.*'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />',
                'submenus' => [
                    ['name' => 'Staff Directory', 'route' => 'management.users.index', 'active_pattern' => 'management.users.*'],
                    ['name' => 'Roles & Permissions', 'route' => 'management.roles.index', 'active_pattern' => 'management.roles.*'],
                ]
            ],
            [
                'name' => 'Reports & Audit',
                'route' => 'management.reports',
                'active_pattern' => ['management.reports*', 'management.audit*'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />',
                'submenus' => [
                    ['name' => 'Administrative Reports', 'route' => 'management.reports'],
                    ['name' => 'Audit & Control Center', 'route' => 'management.audit'],
                ]
            ],
            [
                'name' => 'Settings',
                'route' => 'management.settings.system',
                'active_pattern' => 'management.settings.*',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                'submenus' => [
                    ['name' => 'System Preferences', 'route' => 'management.settings.system'],
                    ['name' => 'Corporate Profile', 'route' => 'management.settings.organization'],
                    ['name' => 'Financial Settings', 'route' => 'management.settings.financial'],
                    ['name' => 'Tax Configuration', 'route' => 'management.settings.tax'],
                    ['name' => 'Numbering & Codes', 'route' => 'management.settings.numbering'],
                    ['name' => 'Notifications & Alerts', 'route' => 'management.settings.notifications'],
                ]
            ],
        ];
    @endphp

    <!-- Mobile sidebar -->
    <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true" x-cloak>
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/80"></div>
        <div class="fixed inset-0 flex">
            <div x-show="sidebarOpen" x-transition.translate.x class="relative mr-16 flex w-full max-w-xs flex-1">
                <div class="absolute top-0 right-0 -mr-16 flex pt-4 pr-2">
                    <button type="button" @click="sidebarOpen = false" class="relative -m-2.5 p-2.5 text-gray-200 hover:text-white transition-colors">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-slate-900 px-6 pb-4">
                    <div class="flex h-16 shrink-0 items-center border-b border-slate-800">
                        <div class="text-white font-bold text-lg uppercase tracking-wide">TANRAIL ONE</div>
                    </div>
                    <nav class="flex flex-1 flex-col">
                        <ul role="list" class="flex flex-1 flex-col gap-y-7">
                            <li>
                                <ul role="list" class="-mx-2 space-y-1">
                                    @foreach($sidebarLinks as $link)
                                    <li x-data="{ expanded: {{ request()->routeIs($link['active_pattern'] ?? $link['route']) ? 'true' : 'false' }} }">
                                        @if(isset($link['submenus']))
                                            <button type="button" @click="expanded = !expanded" class="group flex w-full items-center justify-between gap-x-3 rounded-md {{ request()->routeIs($link['active_pattern'] ?? $link['route']) ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-2.5 text-sm leading-6 font-medium transition-colors" aria-controls="sub-menu-{{ $loop->index }}" :aria-expanded="expanded">
                                                <div class="flex items-center gap-x-3">
                                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        {!! $link['icon'] !!}
                                                    </svg>
                                                    {{ $link['name'] }}
                                                </div>
                                                <svg class="h-4 w-4 shrink-0 transition-transform duration-300" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <ul class="mt-1 px-2 space-y-1" id="sub-menu-{{ $loop->index }}" x-show="expanded" x-collapse x-cloak>
                                                @foreach($link['submenus'] as $submenu)
                                                    <li>
                                                        <a href="{{ route($submenu['route']) }}" class="{{ request()->routeIs($submenu['active_pattern'] ?? $submenu['route']) ? 'text-white font-medium bg-slate-800/50' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }} block rounded-md py-2 pl-9 pr-2 text-sm leading-6 transition-colors">
                                                            {{ $submenu['name'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <a href="{{ route($link['route']) }}" class="group flex gap-x-3 rounded-md {{ request()->routeIs($link['active_pattern'] ?? $link['route']) ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-2.5 text-sm leading-6 font-medium transition-colors">
                                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    {!! $link['icon'] !!}
                                                </svg>
                                                {{ $link['name'] }}
                                            </a>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 border-r border-gray-800">
            <div class="flex h-16 shrink-0 items-center px-6 bg-gray-950 border-b border-gray-800">
                <div class="text-white font-bold text-lg tracking-wide flex items-center gap-2">
                    <div class="w-7 h-7 bg-white text-gray-950 flex items-center justify-center font-black">
                        T
                    </div>
                    TANRAIL <span class="text-gray-500 font-normal text-xs ml-1 mt-1">ERP</span>
                </div>
            </div>
            <nav class="flex flex-1 flex-col px-3 pb-4">
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="space-y-1">
                            @foreach($sidebarLinks as $link)
                            <li x-data="{ expanded: {{ request()->routeIs($link['active_pattern'] ?? $link['route']) ? 'true' : 'false' }} }">
                                @if(isset($link['submenus']))
                                    <button type="button" @click="expanded = !expanded" class="group flex w-full items-center justify-between gap-x-3 rounded {{ request()->routeIs($link['active_pattern'] ?? $link['route']) ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} px-3 py-2 text-sm leading-6 font-medium transition-colors" aria-controls="sub-menu-desktop-{{ $loop->index }}" :aria-expanded="expanded">
                                        <div class="flex items-center gap-x-3 flex-1 text-left">
                                            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs($link['active_pattern'] ?? $link['route']) ? 'text-white' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                {!! $link['icon'] !!}
                                            </svg>
                                            <span class="leading-tight">{{ $link['name'] }}</span>
                                        </div>
                                        <svg class="h-4 w-4 shrink-0 transition-transform duration-300 ml-auto" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <!-- Expandable link section -->
                                    <ul class="mt-1 space-y-1 pl-9 pr-2 border-l border-gray-800 ml-5" id="sub-menu-desktop-{{ $loop->index }}" x-show="expanded" x-collapse x-cloak>
                                        @foreach($link['submenus'] as $submenu)
                                            <li>
                                                <a href="{{ route($submenu['route']) }}" class="{{ request()->routeIs($submenu['active_pattern'] ?? $submenu['route']) ? 'text-white font-medium' : 'text-gray-400 hover:text-white' }} block py-1.5 text-sm transition-colors relative">
                                                    @if(request()->routeIs($submenu['active_pattern'] ?? $submenu['route']))
                                                        <span class="absolute -left-[1.6rem] top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                                    @endif
                                                    <span class="block text-left leading-tight">{{ $submenu['name'] }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <a href="{{ route($link['route']) }}" class="group flex items-center gap-x-3 rounded {{ request()->routeIs($link['active_pattern'] ?? $link['route']) ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} px-3 py-2 text-sm leading-6 font-medium transition-colors text-left">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs($link['active_pattern'] ?? $link['route']) ? 'text-white' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            {!! $link['icon'] !!}
                                        </svg>
                                        <span class="leading-tight">{{ $link['name'] }}</span>
                                    </a>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:pl-64 h-full flex flex-col">
        <!-- Top Navigation -->
        <!-- Top Navigation -->
        <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 sm:gap-x-6 sm:px-6 lg:px-8">
            <button type="button" @click="sidebarOpen = true" class="-m-2.5 p-2.5 text-gray-700 lg:hidden hover:text-blue-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <!-- Separator -->
            <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

            <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 items-center">
                <!-- Breadcrumbs -->
                <div class="hidden sm:flex items-center text-sm font-semibold text-gray-800">
                    <span class="capitalize">{{ explode('.', request()->route()->getName())[1] ?? 'Dashboard' }}</span>
                </div>

                <!-- Search -->
                <form class="relative flex flex-1 lg:max-w-md ml-auto" action="#" method="GET">
                    <label for="search-field" class="sr-only">Search</label>
                    <div class="relative w-full">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="search-field" class="block w-full rounded border-gray-300 py-1.5 pl-9 pr-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-gray-50 hover:bg-white transition-colors" placeholder="Search system..." type="search" name="search">
                    </div>
                </form>

                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <!-- Notifications -->
                    <button type="button" class="-m-2.5 p-2.5 text-gray-500 hover:text-gray-700 relative">
                        <span class="sr-only">View notifications</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </button>

                    <!-- Separator -->
                    <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

                    <!-- Profile dropdown -->
                    @php
                        $currentUser = auth()->user();
                        $userName = $currentUser->name ?? 'Administrator';
                    @endphp
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open" class="-m-1.5 flex items-center p-1.5 hover:bg-gray-50 rounded transition-colors" id="user-menu-button" :aria-expanded="open" aria-haspopup="true">
                            <span class="sr-only">Open user menu</span>
                            <div class="h-8 w-8 rounded border border-gray-300 bg-white flex items-center justify-center text-gray-700 font-bold text-sm shadow-sm">
                                {{ substr($userName, 0, 1) }}
                            </div>
                            <span class="hidden lg:flex lg:flex-col lg:items-start lg:ml-3">
                                <span class="text-sm font-semibold text-gray-900" aria-hidden="true">{{ $userName }}</span>
                            </span>
                            <svg class="ml-2 h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown menu -->
                        <div x-show="open" 
                             x-cloak 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
                             role="menu">
                            <div class="py-1">
                                <a href="{{ route('management.settings.system') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100" role="menuitem">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="flex-1 overflow-y-auto bg-gray-100">
            <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>

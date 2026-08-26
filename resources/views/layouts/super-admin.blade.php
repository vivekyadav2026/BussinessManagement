<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin - {{ config('app.name', 'Business Management') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Theme Switcher Script -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme-color') || 'navy';
            document.documentElement.className = 'h-full bg-gray-50 theme-' + theme;
        })();
    </script>
    <style>
        :root {
            /* Theme bases */
            --bg-main: #f3f4f6;
            --bg-card: #ffffff;
            --border-color: #f3f4f6;
            --border-hard: #e5e7eb;
            --text-main: #1f2937;
            --text-muted: #6b7280;

            /* Sidebar custom */
            --theme-bg: #0f172a; /* Default to Midnight Slate for Super Admin */
            --theme-text: #e2e8f0;
            --theme-active: #3b82f6;
            --theme-active-text: #ffffff;
            --theme-hover: rgba(255, 255, 255, 0.1);
            --theme-hover-text: #ffffff;
        }

        .theme-orange {
            --theme-bg: #ea580c;
            --theme-text: #ffedd5;
            --theme-active: #ffffff;
            --theme-active-text: #ea580c;
            --theme-hover: rgba(255, 255, 255, 0.15);
            --theme-hover-text: #ffffff;
        }
        
        .theme-navy {
            --theme-bg: #1c2331;
            --theme-text: #d1d5db;
            --theme-active: #f59e0b;
            --theme-active-text: #111827;
            --theme-hover: rgba(255, 255, 255, 0.1);
            --theme-hover-text: #ffffff;
        }

        .theme-purple { /* GoTRI Purple */
            --theme-bg: #7c3aed;
            --theme-text: #ede9fe;
            --theme-active: #ffffff;
            --theme-active-text: #7c3aed;
            --theme-hover: rgba(255, 255, 255, 0.15);
            --theme-hover-text: #ffffff;
        }

        .theme-emerald {
            --theme-bg: #064e3b;
            --theme-text: #d1fae5;
            --theme-active: #10b981;
            --theme-active-text: #ffffff;
            --theme-hover: rgba(255, 255, 255, 0.1);
            --theme-hover-text: #ffffff;
        }

        .theme-indigo {
            --theme-bg: #1e1b4b;
            --theme-text: #e0e7ff;
            --theme-active: #6366f1;
            --theme-active-text: #ffffff;
            --theme-hover: rgba(255, 255, 255, 0.1);
            --theme-hover-text: #ffffff;
        }

        .theme-slate {
            --theme-bg: #0f172a;
            --theme-text: #e2e8f0;
            --theme-active: #3b82f6;
            --theme-active-text: #ffffff;
            --theme-hover: rgba(255, 255, 255, 0.1);
            --theme-hover-text: #ffffff;
        }

        /* True Dark Theme Override */
        .theme-dark {
            --bg-main: #0b0f19;
            --bg-card: #111827;
            --border-color: #1f2937;
            --border-hard: #374151;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            
            --theme-bg: #111827;
            --theme-text: #9ca3af;
            --theme-active: #f97316; /* Orange accent in dark theme */
            --theme-active-text: #ffffff;
            --theme-hover: rgba(255, 255, 255, 0.05);
            --theme-hover-text: #ffffff;
        }

        /* Dynamic overrides for the main layout to apply dark theme without refactoring every view */
        .theme-dark body, .theme-dark .bg-gray-50, .theme-dark .bg-gray-100 {
            background-color: var(--bg-main) !important;
            color: var(--text-main) !important;
        }
        .theme-dark .bg-white, .theme-dark .panel, .theme-dark .stat-card, .theme-dark .bg-white\/10 {
            background-color: var(--bg-card) !important;
            color: var(--text-main) !important;
            border-color: var(--border-color) !important;
        }
        .theme-dark h1, .theme-dark h2, .theme-dark h3, .theme-dark h4, .theme-dark th, .theme-dark td, .theme-dark .text-gray-900, .theme-dark .text-gray-800, .theme-dark .text-slate-900 {
            color: var(--text-main) !important;
        }
        .theme-dark p, .theme-dark .text-gray-500, .theme-dark .text-gray-400, .theme-dark .text-slate-500, .theme-dark .text-slate-400 {
            color: var(--text-muted) !important;
        }
        .theme-dark .border-gray-100, .theme-dark .border-gray-200, .theme-dark .border-slate-100, .theme-dark .border-b, .theme-dark .border-t, .theme-dark .divide-gray-50 > * {
            border-color: var(--border-color) !important;
        }
        .theme-dark .sticky {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }
        .theme-dark input, .theme-dark select, .theme-dark textarea {
            background-color: var(--bg-main) !important;
            border-color: var(--border-hard) !important;
            color: var(--text-main) !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar -->
    <div x-show="sidebarOpen" class="relative z-40 md:hidden" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        <div class="fixed inset-0 z-40 flex">
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" class="relative flex w-full max-w-xs flex-1 flex-col pt-5 pb-4" style="background-color: var(--theme-bg);">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button type="button" @click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="flex flex-shrink-0 items-center px-4">
                    <div class="font-bold text-xl text-white">Super Admin</div>
                </div>
                <div class="mt-5 h-0 flex-1 overflow-y-auto">
                    <nav class="space-y-1 px-2">
                        <x-sidebar-item route="{{ route('super-admin.dashboard') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>' :active="request()->routeIs('super-admin.dashboard')" class="text-gray-300 hover:bg-slate-800 hover:text-white" />
                        <x-sidebar-item route="{{ route('super-admin.organizations.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>' :active="request()->routeIs('super-admin.organizations.*')" class="text-gray-300 hover:bg-slate-800 hover:text-white">Organizations</x-sidebar-item>
                        <x-sidebar-item route="{{ route('super-admin.plans.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>' :active="request()->routeIs('super-admin.plans.*')" class="text-gray-300 hover:bg-slate-800 hover:text-white">Plans</x-sidebar-item>
                        <x-sidebar-item route="{{ route('super-admin.subscriptions.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' :active="request()->routeIs('super-admin.subscriptions.*')" class="text-gray-300 hover:bg-slate-800 hover:text-white">Subscriptions</x-sidebar-item>
                    </nav>
                </div>
            </div>
            <div class="w-14 flex-shrink-0" aria-hidden="true"></div>
        </div>
    </div>

    <!-- Static sidebar -->
    <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col">
        <div class="flex min-h-0 flex-1 flex-col" style="background-color: var(--theme-bg);">
            <div class="flex flex-1 flex-col overflow-y-auto pt-5 pb-4">
                <div class="flex flex-shrink-0 items-center px-4 mb-3">
                    <div class="font-bold text-xl text-white truncate w-full">Super Admin</div>
                </div>


                <nav class="mt-2 flex-1 space-y-1 px-2">
                    <x-sidebar-item :dark="true" route="{{ route('super-admin.dashboard') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 0 001 1m-6 0h6"/></svg>' :active="request()->routeIs('super-admin.dashboard')">Dashboard</x-sidebar-item>
                    <x-sidebar-item :dark="true" route="{{ route('super-admin.organizations.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>' :active="request()->routeIs('super-admin.organizations.*')">Organizations</x-sidebar-item>
                    <x-sidebar-item :dark="true" route="{{ route('super-admin.plans.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>' :active="request()->routeIs('super-admin.plans.*')">Plans</x-sidebar-item>
                    <x-sidebar-item :dark="true" route="{{ route('super-admin.subscriptions.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' :active="request()->routeIs('super-admin.subscriptions.*')">Subscriptions</x-sidebar-item>
                </nav>
            </div>
        </div>
    </div>

    <div class="flex flex-1 flex-col md:pl-64">
        <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow">
            <button type="button" @click="sidebarOpen = true" class="border-r border-gray-200 px-4 text-gray-500 md:hidden">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
            </button>
            <div class="flex flex-1 justify-between px-4 items-center">
                <div class="flex flex-1"></div>
                <div class="ml-4 flex items-center md:ml-6 gap-3">
                    <!-- Theme Switcher Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-1.5 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition-colors">
                            <span class="sr-only">Choose theme</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-30" style="display: none;">
                            <button @click="localStorage.setItem('theme-color', 'orange'); window.location.reload()" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <span class="w-3.5 h-3.5 rounded-full bg-[#ea580c] inline-block border border-gray-300"></span> Modern Orange
                            </button>
                            <button @click="localStorage.setItem('theme-color', 'purple'); window.location.reload()" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <span class="w-3.5 h-3.5 rounded-full bg-[#7c3aed] inline-block border border-gray-300"></span> GoTRI Purple
                            </button>
                            <button @click="localStorage.setItem('theme-color', 'dark'); window.location.reload()" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <span class="w-3.5 h-3.5 rounded-full bg-[#0b0f19] inline-block border border-gray-300"></span> Midnight Dark
                            </button>
                            <button @click="localStorage.setItem('theme-color', 'navy'); window.location.reload()" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <span class="w-3.5 h-3.5 rounded-full bg-[#1c2331] inline-block border border-gray-300"></span> Dark Navy
                            </button>
                            <button @click="localStorage.setItem('theme-color', 'slate'); window.location.reload()" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <span class="w-3.5 h-3.5 rounded-full bg-[#0f172a] inline-block border border-gray-300"></span> Midnight Slate
                            </button>
                        </div>
                    </div>

                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-1.5 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none relative transition-colors">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.07 6.07 0 00-1-3.5M9 17v1a3 3 0 006 0v-1m-6 0H9m0 0a3 3 0 01-3-3v-3.5M9 17h6" /></svg>
                            <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-80 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-30" style="display: none;">
                            <div class="px-4 py-2 text-sm font-semibold border-b border-gray-100 text-gray-800">Notifications</div>
                            <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
                                <div class="px-4 py-3 hover:bg-gray-50 text-xs">
                                    <p class="font-medium text-gray-900">New Registration</p>
                                    <p class="text-gray-500 mt-0.5">TechCorp Ltd signed up</p>
                                    <p class="text-gray-400 text-[10px] mt-1">5 mins ago</p>
                                </div>
                                <div class="px-4 py-3 hover:bg-gray-50 text-xs">
                                    <p class="font-medium text-gray-900">Subscription Updated</p>
                                    <p class="text-gray-500 mt-0.5">Sharma Traders upgraded to Pro Plan</p>
                                    <p class="text-gray-400 text-[10px] mt-1">1 hour ago</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative ml-2" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--theme-active)] focus:ring-offset-2" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <div class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[var(--theme-active)] text-[var(--theme-active-text)] font-bold text-sm">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </button>
                        </div>
                        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-30" style="display: none;">
                            <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-100">
                                <div class="font-semibold text-gray-800">{{ auth()->user()->name }}</div>
                                <div class="truncate text-[10px]">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 font-medium">Profile Settings</a>
                            <a href="/" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 font-medium">Visit Website</a>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-red-50 border-t border-gray-100 font-semibold">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="flex-1 pb-8">
            <div class="mt-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 dash-content">
                    @if(session('success'))
                        <div class="rounded-md bg-green-50 p-4 mb-6">
                            <div class="flex">
                                <div class="ml-3"><p class="text-sm font-medium text-green-800">{{ session('success') }}</p></div>
                            </div>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="rounded-md bg-red-50 p-4 mb-6">
                            <div class="flex">
                                <div class="ml-3"><p class="text-sm font-medium text-red-800">{{ session('error') }}</p></div>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin - {{ config('app.name', 'Vyapaargo') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Theme Switcher Script -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme-color') || 'navy';
            document.documentElement.className = 'h-full bg-gray-50 theme-' + theme;
        })();
    </script>
    <style>
        :root {
            /* Theme bases from Vyapaargo style guide */
            --bg-main: #F3F5F3;
            --bg-card: #FFFFFF;
            --border-color: #EBECE6;
            --border-hard: #DFE1DA;
            --text-main: #17233F;
            --text-muted: #4B5670;
            --text-faint: #8991A5;

            --gold: #D99A2B;
            --gold-deep: #B87F1B;
            --teal: #146356;
            --teal-soft: #E4F0EC;
            --rose: #AE3B34;
            --rose-soft: #F5E6E4;
            --radius: 14px;
            --shadow: 0 1px 2px rgba(23,35,63,.04), 0 8px 24px rgba(23,35,63,.06);

            /* Sidebar custom variables */
            --theme-bg: #17233F; /* Deep Navy Ink */
            --theme-text: #8991A5;
            --theme-active: #D99A2B; /* Khatabook Gold */
            --theme-active-text: #17233F;
            --theme-hover: rgba(217, 154, 43, 0.12);
            --theme-hover-text: #ffffff;
        }

        /* Sidebar Item & Text Overrides */
        nav a {
            font-family: 'Space Grotesk', sans-serif !important;
            font-weight: 600 !important;
        }

        body {
            background-color: var(--bg-main) !important;
            color: var(--text-main) !important;
            font-family: 'Inter', sans-serif !important;
        }

        h1, h2, h3, h4, .dash-head h1, .display {
            font-family: 'Space Grotesk', sans-serif !important;
            letter-spacing: -.01em !important;
            color: var(--text-main) !important;
            font-weight: 700 !important;
        }

        .mono, .font-mono, [class*="mono"] {
            font-family: 'IBM Plex Mono', monospace !important;
        }

        /* Logo Branding styling */
        .logo {
            display: flex;
            align-items: center;
            gap: 9px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 18px;
        }
        .logo .mark {
            width: 24px;
            height: 24px;
            background: var(--gold);
            border-radius: 6px;
            position: relative;
        }
        .logo .mark::before {
            content: '';
            position: absolute;
            left: 5px;
            right: 5px;
            top: 6px;
            height: 2px;
            background: var(--theme-bg);
            box-shadow: 0 4px 0 var(--theme-bg), 0 8px 0 var(--theme-bg);
        }

        /* Forms Layout & Labels styling */
        form label:not(.inline-flex):not(.flex-row),
        .field label:not(.inline-flex):not(.flex-row) {
            display: flex !important;
            justify-content: space-between !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            color: var(--text-main) !important;
            margin-bottom: 7px !important;
            font-family: 'Inter', sans-serif !important;
        }

        form label .opt,
        .field label .opt {
            font-weight: 400 !important;
            color: var(--text-faint) !important;
            font-family: 'IBM Plex Mono', monospace !important;
            font-size: 10.5px !important;
        }

        /* Global inputs & form elements styling overrides for premium look */
        input[type="text"], input[type="number"], input[type="email"], input[type="password"], input[type="date"], input[type="time"], input[type="search"], select, textarea {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-hard) !important;
            border-radius: 10px !important;
            padding: 9px 13px !important;
            font-size: 13px !important;
            color: var(--text-main) !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
        }

        input[type="text"]:focus, input[type="number"]:focus, input[type="email"]:focus, input[type="password"]:focus, input[type="date"]:focus, input[type="time"]:focus, input[type="search"]:focus, select:focus, textarea:focus {
            border-color: var(--gold) !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(217, 154, 43, 0.15) !important;
            background-color: #FFFFFF !important;
        }

        /* Buttons styling */
        .btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 7px !important;
            font-family: 'Space Grotesk', sans-serif !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            padding: 9px 18px !important;
            font-size: 13px !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }

        .btn-gold, .btn-primary {
            background-color: var(--gold) !important;
            color: #17233F !important;
            border: none !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        }
        .btn-gold:hover, .btn-primary:hover {
            background-color: var(--gold-deep) !important;
            color: #ffffff !important;
        }

        .btn-secondary {
            background-color: #FFFFFF !important;
            color: var(--text-main) !important;
            border: 1px solid var(--border-hard) !important;
        }
        .btn-secondary:hover {
            background-color: var(--bg-main) !important;
            border-color: var(--text-muted) !important;
        }

        .btn-sm {
            padding: 6px 12px !important;
            font-size: 12px !important;
            border-radius: 8px !important;
        }

        /* Table & Panels styling */
        .panel, .card, div[class*="bg-white rounded"] {
            background: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--radius) !important;
            box-shadow: var(--shadow) !important;
        }

        .inv-table th {
            font-family: 'IBM Plex Mono', monospace !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            color: var(--text-muted) !important;
            letter-spacing: .04em !important;
            padding-bottom: 8px !important;
        }
        
        .inv-table td {
            border-top: 1px solid var(--border-color) !important;
            padding: 12px 8px !important;
        }

        /* Eyebrow badge styling */
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: var(--gold-deep);
            background: #FBF1DD;
            border: 1px solid #EFDDAE;
            padding: 5px 11px;
            border-radius: 100px;
        }
        .eyebrow::before {
            content: '●';
            font-size: 7px;
        }

        /* Restore left padding for inputs that have search icons */
        input[class*="pl-8"], input.pl-8 { padding-left: 2rem !important; }
        input[class*="pl-9"], input.pl-9 { padding-left: 2.25rem !important; }
        input[class*="pl-10"], input.pl-10 { padding-left: 2.5rem !important; }
        input[class*="pl-12"], input.pl-12 { padding-left: 3rem !important; }

        /* Explicit Sidebar Collapse Rules */
        .sidebar-expanded { width: 16rem !important; }
        .sidebar-collapsed { width: 4.5rem !important; }

        .main-expanded { padding-left: 16rem !important; }
        .main-collapsed { padding-left: 4.5rem !important; }

        /* Pre-render instant CSS sync to prevent page layout shift on navigation */
        html.sidebar-is-collapsed .sidebar-expanded { width: 4.5rem !important; }
        html.sidebar-is-collapsed .main-expanded { padding-left: 4.5rem !important; }

        @media (max-width: 768px) {
            .main-expanded, .main-collapsed, html.sidebar-is-collapsed .main-expanded { padding-left: 0 !important; }
            .sidebar-expanded, .sidebar-collapsed, html.sidebar-is-collapsed .sidebar-expanded { width: 16rem !important; }
        }
    </style>

    <script>
        localStorage.removeItem('sidebar_collapsed');
        document.documentElement.classList.remove('sidebar-is-collapsed');
    </script>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-gray-900" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">


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
                    <div class="logo text-white">
                        <div class="mark bg-[#D99A2B]"></div>
                        <span>Vyapaargo</span>
                    </div>
                </div>
                <div class="mt-5 h-0 flex-1 overflow-y-auto">
                    <nav class="space-y-1 px-2">
                        <x-sidebar-item route="{{ route('super-admin.dashboard') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 0 001 1m-6 0h6"/></svg>' :active="request()->routeIs('super-admin.dashboard')" class="text-gray-300 hover:bg-slate-800 hover:text-white" />
                        <x-sidebar-item route="{{ route('super-admin.organizations.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>' :active="request()->routeIs('super-admin.organizations.*')" class="text-gray-300 hover:bg-slate-800 hover:text-white">Organizations</x-sidebar-item>
                        <x-sidebar-item route="{{ route('super-admin.plans.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>' :active="request()->routeIs('super-admin.plans.*')" class="text-gray-300 hover:bg-slate-800 hover:text-white">Plans</x-sidebar-item>
                        <x-sidebar-item route="{{ route('super-admin.subscriptions.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' :active="request()->routeIs('super-admin.subscriptions.*')" class="text-gray-300 hover:bg-slate-800 hover:text-white">Subscriptions</x-sidebar-item>
                        <x-sidebar-item route="{{ route('super-admin.settings.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' :active="request()->routeIs('super-admin.settings.*')" class="text-gray-300 hover:bg-slate-800 hover:text-white">Platform Settings</x-sidebar-item>
                    </nav>
                </div>
            </div>
            <div class="w-14 flex-shrink-0" aria-hidden="true"></div>
        </div>
    </div>

    <!-- Static sidebar -->
    <div :class="sidebarCollapsed ? 'sidebar-collapsed' : 'sidebar-expanded'" class="hidden md:fixed md:inset-y-0 md:flex md:flex-col transition-all duration-300 z-30">
        <div class="flex min-h-0 flex-1 flex-col" style="background-color: var(--theme-bg);">
            <div class="flex flex-1 flex-col overflow-y-auto pt-5 pb-4">
                <div class="flex flex-shrink-0 items-center justify-between px-4 mb-3">
                    <div class="logo text-white flex items-center gap-2">
                        <div class="mark bg-[#D99A2B]"></div>
                        <span x-show="!sidebarCollapsed" class="truncate font-bold text-base">Vyapaargo</span>
                    </div>
                    {{-- <button type="button" @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebar_collapsed', sidebarCollapsed); document.documentElement.classList.toggle('sidebar-is-collapsed', sidebarCollapsed)" class="text-gray-300 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition" title="Toggle Sidebar">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16" /></svg>
                    </button> --}}
                </div>

                <nav class="mt-2 flex-1 space-y-1 px-2">
                    <x-sidebar-item :dark="true" route="{{ route('super-admin.dashboard') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 0 001 1m-6 0h6"/></svg>' :active="request()->routeIs('super-admin.dashboard')">Dashboard</x-sidebar-item>
                    <x-sidebar-item :dark="true" route="{{ route('super-admin.organizations.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>' :active="request()->routeIs('super-admin.organizations.*')">Organizations</x-sidebar-item>
                    <x-sidebar-item :dark="true" route="{{ route('super-admin.plans.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>' :active="request()->routeIs('super-admin.plans.*')">Plans</x-sidebar-item>
                    <x-sidebar-item :dark="true" route="{{ route('super-admin.subscriptions.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' :active="request()->routeIs('super-admin.subscriptions.*')">Subscriptions</x-sidebar-item>
                    <x-sidebar-item :dark="true" route="{{ route('super-admin.settings.index') }}" icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' :active="request()->routeIs('super-admin.settings.*')">Platform Settings</x-sidebar-item>
                </nav>

            </div>
            <div class="flex flex-shrink-0 border-t border-white/10 p-4">
                <div class="group block w-full flex-shrink-0">
                    <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[var(--theme-active)] text-[var(--theme-active-text)] font-bold shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div x-show="!sidebarCollapsed" class="ml-3">
                            <p class="text-sm font-medium text-white truncate max-w-[130px]">{{ auth()->user()->name }}</p>
                            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                @csrf
                                <button type="submit" class="text-xs font-medium text-[var(--theme-text)] hover:text-white transition-colors">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div :class="sidebarCollapsed ? 'main-collapsed' : 'main-expanded'" class="flex flex-1 flex-col transition-all duration-300">
        <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow">
            <button type="button" @click="sidebarOpen = true" class="border-r border-gray-200 px-4 text-gray-500 md:hidden">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
            </button>

            {{-- <button type="button" @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebar_collapsed', sidebarCollapsed)" class="hidden md:flex items-center justify-center border-r border-gray-200 px-4 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition" title="Toggle Sidebar">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16" /></svg>
            </button> --}}

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

                    <!-- Profile dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" type="button" class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <div class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[var(--theme-active)] text-[var(--theme-active-text)] font-bold text-xs">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </button>
                        </div>
                        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" style="display: none;">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500 font-medium">Signed in as</p>
                                <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="flex-1">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</body>
</html>

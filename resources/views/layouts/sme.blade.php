<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Vyapaargo') }}</title>

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
            width: 100% !important;
            border-width: 1px !important;
            border-style: solid !important;
            border-color: var(--border-hard) !important;
            border-radius: 9px !important;
            padding-left: 13px !important;
            padding-right: 13px !important;
            padding-top: 10px !important;
            padding-bottom: 10px !important;
            font-size: 13.5px !important;
            font-family: 'Inter', sans-serif !important;
            outline: none !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            background-color: var(--bg-card) !important;
            color: var(--text-main) !important;
            box-shadow: none !important;
        }
        
        .theme-dark input[type="text"], .theme-dark input[type="number"], .theme-dark input[type="email"], .theme-dark input[type="password"], .theme-dark input[type="date"], .theme-dark input[type="time"], .theme-dark input[type="search"], .theme-dark select, .theme-dark textarea {
            background-color: var(--bg-main) !important;
        }

        input[type="text"]:focus, input[type="number"]:focus, input[type="email"]:focus, input[type="password"]:focus, input[type="date"]:focus, input[type="time"]:focus, input[type="search"]:focus, select:focus, textarea:focus {
            border-color: var(--gold) !important;
            background-color: var(--bg-card) !important;
            box-shadow: 0 0 0 3px rgba(217, 154, 43, 0.15) !important;
            outline: none !important;
        }

        input::placeholder, textarea::placeholder {
            color: var(--text-faint) !important;
        }

        /* Checkbox overrides */
        input[type="checkbox"], input[type="radio"] {
            width: 1.125rem !important;
            height: 1.125rem !important;
            border-radius: 0.375rem !important;
            border-color: var(--border-hard) !important;
            color: var(--gold) !important;
            accent-color: var(--gold) !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            background-color: var(--bg-card) !important;
        }
        input[type="checkbox"]:checked, input[type="radio"]:checked {
            background-color: var(--theme-active) !important;
            border-color: var(--theme-active) !important;
        }
        .theme-dark input[type="checkbox"], .theme-dark input[type="radio"] {
            background-color: var(--bg-main) !important;
        }
        .theme-dark input[type="checkbox"]:checked, .theme-dark input[type="radio"]:checked {
            background-color: var(--theme-active) !important;
        }
        input[type="checkbox"]:focus, input[type="radio"]:focus {
            box-shadow: 0 0 0 3px rgba(217, 154, 43, 0.15) !important;
        }

        /* Buttons Overrides */
        .btn, button.btn {
            font-family: 'Space Grotesk', sans-serif !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            border-radius: 9px !important;
            padding: 11px 18px !important;
            transition: all 0.2s !important;
        }
        .btn-gold, button.btn-gold, [class*="btn-gold"] {
            background-color: var(--gold) !important;
            color: var(--text-main) !important;
            border: 1px solid transparent !important;
        }
        .btn-gold:hover, button.btn-gold:hover {
            background-color: var(--gold-deep) !important;
            color: var(--text-main) !important;
        }
        .btn-ghost, button.btn-ghost {
            border: 1px solid var(--border-hard) !important;
            background-color: var(--bg-card) !important;
            color: var(--text-main) !important;
        }
        .btn-ghost:hover, button.btn-ghost:hover {
            background-color: var(--bg-main) !important;
        }

        /* Card and Panels rounded overrides */
        .panel, .card {
            border-radius: var(--radius) !important; /* 14px */
            border: 1px solid var(--border-color) !important;
            box-shadow: var(--shadow) !important;
            background-color: var(--bg-card) !important;
            padding: 1.5rem !important; /* Added global 24px padding to panels */
        }
        .theme-dark .panel, .theme-dark .card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3) !important;
        }

        /* Table styles override to match theme */
        .inv-table th {
            font-family: 'IBM Plex Mono', monospace !important;
            font-size: 10.5px !important;
            text-transform: uppercase !important;
            color: var(--text-muted) !important;
            letter-spacing: .04em !important;
            padding-bottom: 8px !important;
        }
        
        .inv-table td {
            border-top: 1px solid var(--border-color) !important;
            padding: 12px 8px !important;
        }

        /* Roles selection cards in forms */
        .role-opt {
            border: 1px solid var(--border-hard) !important;
            border-radius: 10px !important;
            padding: 12px 14px !important;
            cursor: pointer !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            gap: 9px !important;
            transition: all 0.2s !important;
            background-color: var(--bg-card) !important;
            color: var(--text-main) !important;
        }
        .role-opt input {
            accent-color: var(--gold) !important;
        }
        .role-opt:has(input:checked), .role-opt.checked {
            border-color: var(--gold) !important;
            background-color: #FBF1DD !important;
            color: var(--text-main) !important;
        }

        /* Client chip visual preview */
        .client-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: var(--teal-soft);
            border: 1px solid #C7E1D8;
            border-radius: 9px;
            padding: 9px 12px;
            margin-top: 10px;
            font-size: 12.5px;
        }
        .client-chip .who {
            font-weight: 600;
            color: var(--teal);
        }
        .client-chip .meta {
            color: var(--text-muted);
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
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
    </style>

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-gray-900" x-data="{ sidebarOpen: false }">

    <!-- Off-canvas menu for mobile -->
    <div x-show="sidebarOpen" class="relative z-40 md:hidden" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        <div class="fixed inset-0 z-40 flex">
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex w-full max-w-xs flex-1 flex-col pt-5 pb-4" style="background-color: var(--theme-bg);">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button type="button" @click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                    <div class="logo text-white">
                        <div class="mark bg-[#D99A2B]"></div>
                        <span>Vyapaargo</span>
                    </div>
                </div>
                <nav class="mt-5 flex-1 space-y-1 px-2">
                    @include('layouts.partials.sidebar-links')
                </nav>
            </div>
        </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col">
        <div class="flex min-h-0 flex-1 flex-col" style="background-color: var(--theme-bg);">
            <div class="flex flex-1 flex-col overflow-y-auto pt-5 pb-4">
                <div class="flex flex-shrink-0 items-center px-4 mb-3">
                    <div class="logo text-white">
                        <div class="mark bg-[#D99A2B]"></div>
                        <span>Vyapaargo</span>
                    </div>
                </div>


                <nav class="mt-2 flex-1 space-y-1 px-3">
                    @include('layouts.partials.sidebar-links')
                </nav>
            </div>
            <div class="flex flex-shrink-0 border-t border-white/10 p-4">
                <div class="group block w-full flex-shrink-0">
                    <div class="flex items-center">
                        <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[var(--theme-active)] text-[var(--theme-active-text)] font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
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

    <div class="flex flex-1 flex-col md:pl-64">
        <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow-sm border-b border-gray-200">
            <button type="button" @click="sidebarOpen = true" class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
            </button>
            <div class="flex flex-1 justify-between px-4 items-center">
                <div class="flex items-center">
                    <!-- Location Switcher -->
                    <div class="flex items-center">
                        @php
                            $activeLocationId = \App\Services\LocationManager::getActiveLocationId();
                            if(auth()->user()->hasRole('Organization Admin') || auth()->user()->hasRole('Super Admin')) {
                                $availableLocations = \App\Models\Location::where('organization_id', auth()->user()->organization_id)->where('is_active', true)->get();
                            } else {
                                $availableLocations = auth()->user()->locations()->where('is_active', true)->get();
                            }
                        @endphp
                        @if($availableLocations->count() > 0)
                            <form action="{{ route('organization.set-location') }}" method="POST" class="m-0">
                                @csrf
                                <select name="location_id" onchange="this.form.submit()" class="block w-full border-0 bg-transparent py-2 pl-3 pr-8 text-sm font-semibold text-gray-900 focus:ring-0">
                                    @if(!$activeLocationId)
                                        <option value="" disabled selected>Select Location</option>
                                    @endif
                                    @foreach($availableLocations as $loc)
                                        <option value="{{ $loc->id }}" {{ $activeLocationId == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->organization->name ?? 'Business' }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex-1 flex justify-center px-4">
                    <div class="w-full max-w-lg">
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative text-gray-400 focus-within:text-gray-600">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                            </div>
                            <input id="search" class="block w-full rounded-md border-0 bg-gray-100 py-1.5 pl-10 pr-3 text-gray-900 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 placeholder:text-gray-500" placeholder="Search invoices, products, clients..." type="search" name="search">
                        </div>
                    </div>
                </div>

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
                                    <p class="font-medium text-gray-900">Payment Received</p>
                                    <p class="text-gray-500 mt-0.5">Sharma Traders paid ₹10,132</p>
                                    <p class="text-gray-400 text-[10px] mt-1">2 mins ago</p>
                                </div>
                                <div class="px-4 py-3 hover:bg-gray-50 text-xs">
                                    <p class="font-medium text-gray-900">New KOT Order</p>
                                    <p class="text-gray-500 mt-0.5">Table 07 ordered Butter Chicken</p>
                                    <p class="text-gray-400 text-[10px] mt-1">10 mins ago</p>
                                </div>
                                <div class="px-4 py-3 hover:bg-gray-50 text-xs">
                                    <p class="font-medium text-gray-900">Low Stock Warning</p>
                                    <p class="text-gray-500 mt-0.5">Basmati Rice (25kg) is below 5 units</p>
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
            <!-- Header if exists -->
            @isset($header)
            <div class="bg-white shadow">
                <div class="px-4 sm:px-6 lg:mx-auto lg:max-w-7xl lg:px-8">
                    <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                        <div class="min-w-0 flex-1">
                            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:leading-9">{{ $header }}</h1>
                        </div>
                    </div>
                </div>
            </div>
            @endisset

            <div class="mt-8">
                <!-- Keep dash-content wrapper for backward compatibility with old views, but give it a max-w container -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 dash-content">
                    @if(session('success'))
                        <div class="rounded-md bg-green-50 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="rounded-md bg-red-50 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
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

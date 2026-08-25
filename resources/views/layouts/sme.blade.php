<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Business Management') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900" x-data="{ sidebarOpen: false }">

    <!-- Off-canvas menu for mobile -->
    <div x-show="sidebarOpen" class="relative z-40 md:hidden" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        <div class="fixed inset-0 z-40 flex">
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex w-full max-w-xs flex-1 flex-col bg-[#1c2331] pt-5 pb-4">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button type="button" @click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="flex flex-shrink-0 items-center px-4">
                    <div class="flex items-center gap-2">
                        <div class="bg-yellow-500 rounded p-1">
                            <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </div>
                        <div class="font-bold text-xl text-white">Business Pro</div>
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
        <div class="flex min-h-0 flex-1 flex-col bg-[#1c2331]">
            <div class="flex flex-1 flex-col overflow-y-auto pt-5 pb-4">
                <div class="flex flex-shrink-0 items-center px-4 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="bg-yellow-500 rounded p-1">
                            <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </div>
                        <div class="font-bold text-xl text-white tracking-wide">Business Pro</div>
                    </div>
                </div>
                <nav class="mt-2 flex-1 space-y-1 px-3">
                    @include('layouts.partials.sidebar-links')
                </nav>
            </div>
            <div class="flex flex-shrink-0 border-t border-gray-700/50 p-4">
                <div class="group block w-full flex-shrink-0">
                    <div class="flex items-center">
                        <div>
                            <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-yellow-500 text-gray-900 font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white group-hover:text-gray-200">{{ auth()->user()->organization->name ?? 'Organization' }}</p>
                            <p class="text-xs font-medium text-gray-400 group-hover:text-gray-300">Organization Admin</p>
                            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                @csrf
                                <button type="submit" class="text-xs font-medium text-gray-500 hover:text-white transition-colors">Sign out</button>
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
                    <a href="#" class="px-4 py-1.5 text-sm font-medium rounded-full bg-[#1c2331] text-white hover:bg-slate-800 transition-colors">Website</a>
                    <a href="{{ route('dashboard') }}" class="px-4 py-1.5 text-sm font-semibold rounded-full bg-yellow-500 text-gray-900 hover:bg-yellow-400 transition-colors">Dashboard</a>
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

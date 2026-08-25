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
<body class="h-full font-sans antialiased text-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50">
    @php
        $maxWidthClass = $maxWidth ?? 'sm:max-w-md';
    @endphp

    <div class="sm:mx-auto sm:w-full {{ $maxWidthClass }}">
        <div class="flex justify-center">
            <!-- Modern Logo Icon -->
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-600">
                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                </svg>
            </div>
        </div>
        
        @if(isset($header))
            {{ $header }}
        @endif
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full {{ $maxWidthClass }}">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-xl sm:px-10 border border-gray-100">
            {{ $slot }}
        </div>
    </div>
</body>
</html>

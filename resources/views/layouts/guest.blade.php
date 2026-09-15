<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Vyapaargo') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <style>
        /* Gold & Ink Branding Style Overrides */
        :root {
            --gold: #D99A2B;
            --gold-deep: #B87F1B;
            --ink: #17233F;
            --ink-soft: #4B5670;
            --bg: #F3F5F3;
        }
        body {
            background-color: var(--bg) !important;
            font-family: 'Inter', sans-serif !important;
        }
        h2 {
            font-family: 'Space Grotesk', sans-serif !important;
        }
        /* Override default Tailwind indigo classes to match brand color */
        .text-indigo-600 {
            color: var(--gold-deep) !important;
        }
        .text-indigo-600:hover {
            color: var(--gold) !important;
        }
        .bg-indigo-600 {
            background-color: var(--ink) !important;
            color: #ffffff !important;
            font-family: 'Space Grotesk', sans-serif !important;
            font-weight: 600 !important;
            transition: background-color 0.15s ease !important;
        }
        .bg-indigo-600:hover {
            background-color: var(--gold-deep) !important;
            color: var(--ink) !important;
        }
        .focus\:ring-indigo-500:focus {
            --tw-ring-color: var(--gold) !important;
        }
        .focus\:border-indigo-500:focus {
            border-color: var(--gold) !important;
        }
    </style>
</head>
<body class="min-h-screen bg-[var(--bg)] font-sans antialiased text-gray-900 flex flex-col justify-start sm:justify-center py-6 sm:py-10 px-4 sm:px-6 lg:px-8">
    @php
        $maxWidthClass = $maxWidth ?? 'max-w-md';
    @endphp

    <div class="w-full {{ $maxWidthClass }} mx-auto">
        <div class="flex justify-center">
            <!-- Vyapaargo Logo Icon -->
            <a href="{{ route('welcome') }}" style="display:flex; align-items:center; gap:8px; font-family:'Space Grotesk'; font-weight:700; font-size:22px; color:#17233F; text-decoration:none;">
                <div style="width:28px; height:28px; background:#17233F; border-radius:7px; position:relative; flex-shrink: 0; box-shadow: 0 2px 6px rgba(23,35,63,0.12);">
                    <div style="position:absolute; left:6px; right:6px; top:7px; height:2px; background:#D99A2B; box-shadow:0 5px 0 #D99A2B, 0 10px 0 #D99A2B;"></div>
                </div>
                <span>{{ config('app.name', 'Vyapaargo') }}</span>
            </a>
        </div>
        
        @if(isset($header))
            <div class="mt-2.5">
                {{ $header }}
            </div>
        @endif
    </div>

    <div class="mt-4 sm:mt-5 w-full {{ $maxWidthClass }} mx-auto mb-10">
        <div class="bg-white py-6 px-5 sm:py-7 sm:px-8 rounded-2xl shadow-lg border border-gray-200/80">
            {{ $slot }}
        </div>
        
        <div class="mt-6 text-center text-xs text-gray-500 font-sans">
            &copy; {{ date('Y') }} {{ config('app.name', 'Vyapaargo') }}. All rights reserved.<br>
            <a href="{{ route('public.terms') }}" class="hover:text-gray-800 transition">Terms</a> &middot; 
            <a href="{{ route('public.privacy') }}" class="hover:text-gray-800 transition">Privacy Policy</a>
        </div>
    </div>
</body>
</html>

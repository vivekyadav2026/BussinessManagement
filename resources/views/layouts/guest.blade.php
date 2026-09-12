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
<body class="min-h-screen bg-[var(--bg)] font-sans antialiased text-gray-900 flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8">
    @php
        $maxWidthClass = $maxWidth ?? 'max-w-md';
    @endphp

    <div class="w-full {{ $maxWidthClass }} mx-auto">
        <div class="flex justify-center">
            <!-- Vyapaargo Logo Icon -->
            <a href="{{ route('welcome') }}" style="display:flex; align-items:center; gap:9px; font-family:'Space Grotesk'; font-weight:700; font-size:24px; color:#17233F; text-decoration:none;">
                <div style="width:30px; height:30px; background:#17233F; border-radius:8px; position:relative; flex-shrink: 0;">
                    <div style="position:absolute; left:7px; right:7px; top:8px; height:2.5px; background:#D99A2B; box-shadow:0 6px 0 #D99A2B, 0 12px 0 #D99A2B;"></div>
                </div>
                <span>{{ config('app.name', 'Vyapaargo') }}</span>
            </a>
        </div>
        
        @if(isset($header))
            {{ $header }}
        @endif
    </div>

    <div class="mt-6 w-full {{ $maxWidthClass }} mx-auto">
        <div class="bg-white py-8 px-5 sm:px-10 rounded-2xl shadow-xl border border-gray-200/80">
            {{ $slot }}
        </div>
    </div>
</body>
</html>

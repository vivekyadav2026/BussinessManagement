<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Vyapaargo') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-180.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind & App JS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <!-- Public Layout CSS for Header/Footer -->
    @include('layouts.partials.public_css')

    <style>
        /* Gold & Ink Branding Style Overrides for Tailwind components */
        body {
            background-color: var(--bg) !important;
            font-family: 'Inter', sans-serif !important;
        }
        /* Make sure the main content is isolated so Tailwind doesn't ruin the public header */
        .text-indigo-600 { color: var(--gold-deep) !important; }
        .text-indigo-600:hover { color: var(--gold) !important; }
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
        .focus\:ring-indigo-500:focus { --tw-ring-color: var(--gold) !important; }
        .focus\:border-indigo-500:focus { border-color: var(--gold) !important; }
        
        /* Clean spacing for guest layout content */
        .guest-content-wrapper {
            padding-top: 24px;
            padding-bottom: 48px;
        }
    </style>
</head>
<body class="min-h-screen bg-[var(--bg)] font-sans antialiased text-gray-900 flex flex-col">

    <!-- Public Header Navbar -->
    @include('layouts.partials.public_header')

    @php
        $maxWidthClass = $maxWidth ?? 'max-w-md';
        $isFullCard = $fullCard ?? false;
    @endphp

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col justify-start sm:justify-center py-6 sm:py-8 px-4 sm:px-6 lg:px-8 guest-content-wrapper">
        @if($isFullCard)
            <div class="w-full mx-auto my-2" style="max-width: 1140px;">
                {{ $slot }}
            </div>
        @else
            <div class="w-full {{ $maxWidthClass }} mx-auto">
                @if(isset($header))
                    <div class="mt-2.5 mb-6">
                        {{ $header }}
                    </div>
                @endif
            </div>

            <div class="w-full {{ $maxWidthClass }} mx-auto mb-10">
                <div class="bg-white py-6 px-5 sm:py-7 sm:px-8 rounded-2xl shadow-lg border border-gray-200/80">
                    {{ $slot }}
                </div>
            </div>
        @endif
    </main>

    <!-- Public Footer -->
    @include('layouts.partials.public_footer')

    <!-- Public JS -->
    @include('layouts.partials.public_js')

</body>
</html>
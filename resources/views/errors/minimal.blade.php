<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name', 'Vyapaargo') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .display { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-6 antialiased">
    <div class="max-w-xl w-full text-center space-y-8 bg-slate-800/80 border border-slate-700/80 backdrop-blur-xl p-8 sm:p-12 rounded-3xl shadow-2xl">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-3xl font-bold font-mono shadow-inner mx-auto">
            @yield('code')
        </div>
        
        <div class="space-y-3">
            <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">@yield('heading')</h1>
            <p class="text-slate-400 text-sm sm:text-base leading-relaxed max-w-md mx-auto">
                @yield('message')
            </p>
        </div>

        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
            @yield('actions')
        </div>
        
        <div class="pt-6 border-t border-slate-700/50 flex items-center justify-center gap-2 text-xs text-slate-500 font-mono">
            <div class="w-2 h-2 rounded-full bg-amber-400"></div>
            <span>{{ config('app.name', 'Vyapaargo') }} ERP System</span>
        </div>
    </div>
</body>
</html>

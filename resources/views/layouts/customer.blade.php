<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $organization->name ?? 'Restaurant Menu' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 pb-20">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50 border-b">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                @if(isset($organization) && $organization->logo)
                    <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" class="h-10 w-10 object-contain rounded-full">
                @else
                    <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold">
                        {{ substr($organization->name ?? 'R', 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-lg font-bold text-gray-900 leading-tight">{{ $organization->name ?? 'Restaurant' }}</h1>
                    <p class="text-xs text-gray-500">{{ $location->name ?? '' }}</p>
                </div>
            </div>
            
            @if(session('restaurant_table_id'))
                <div class="text-right">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                        Table {{ \App\Models\RestaurantTable::find(session('restaurant_table_id'))->name }}
                    </span>
                </div>
            @endif
        </div>
        
        @yield('header_extensions')
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto">
        @yield('content')
    </main>

    <!-- Floating Cart Button -->
    @if(!request()->routeIs('public.order.track'))
        <div class="fixed bottom-4 left-0 right-0 px-4 z-40 max-w-4xl mx-auto">
            <a href="{{ route('public.order.cart', [$organization->id, $location->id]) }}" class="w-full bg-gray-900 text-white rounded-xl shadow-lg p-4 flex justify-between items-center hover:bg-black transition-colors transform active:scale-95">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="font-bold">View Cart</span>
                </div>
                <div class="font-bold bg-white text-gray-900 px-3 py-1 rounded-full text-sm">
                    @php
                        $cartKey = 'cart_' . $location->id;
                        $cart = session()->get($cartKey, []);
                        $count = array_sum(array_column($cart, 'quantity'));
                    @endphp
                    {{ $count }} item{{ $count !== 1 ? 's' : '' }}
                </div>
            </a>
        </div>
    @endif

</body>
</html>

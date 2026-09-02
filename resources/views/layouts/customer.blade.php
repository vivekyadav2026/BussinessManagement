<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $organization->name ?? 'Digital QR Menu' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8F8F6; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-[#F8F8F6] text-[#0F172A] pb-28 antialiased selection:bg-[#0F172A] selection:text-white">

    <!-- Sticky Glassmorphic Premium Header -->
    <header class="bg-white/90 backdrop-blur-md shadow-xs sticky top-0 z-50 border-b border-stone-200/60">
        <div class="max-w-4xl mx-auto px-4 py-3.5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                @if(isset($organization) && $organization->logo)
                    <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" class="h-11 w-11 object-cover rounded-2xl border border-stone-200 shadow-xs">
                @else
                    <div class="h-11 w-11 bg-[#0F172A] rounded-2xl flex items-center justify-center text-amber-400 font-black text-lg shadow-sm">
                        {{ substr($organization->name ?? 'R', 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-base font-black text-[#0F172A] leading-tight tracking-tight">{{ $organization->name ?? 'Digital Menu' }}</h1>
                    <p class="text-xs font-bold text-[#475569] flex items-center gap-1 mt-0.5">
                        <span class="text-amber-600">📍</span> {{ $location->name ?? 'Main Outlet' }}
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                @if(session('restaurant_table_id'))
                    <div class="px-3 py-1 bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A] rounded-full text-xs font-black shadow-2xs font-mono uppercase tracking-wider">
                        🪑 Table {{ \App\Models\RestaurantTable::find(session('restaurant_table_id'))->name ?? '' }}
                    </div>
                @endif
            </div>
        </div>
        
        @yield('header_extensions')
    </header>

    <!-- Main Content Area -->
    <main class="max-w-4xl mx-auto">
        @yield('content')
    </main>

    <!-- Floating Sticky Bottom Cart Bar -->
    @if(!request()->routeIs('public.order.track'))
        @php
            $cartKey = 'cart_' . $location->id;
            $cart = session()->get($cartKey, []);
            $count = array_sum(array_column($cart, 'quantity'));
            $cartTotal = 0;
            foreach($cart as $cItem) {
                $cartTotal += $cItem['price'] * $cItem['quantity'];
            }
        @endphp

        @if($count > 0)
        <div class="fixed bottom-4 left-0 right-0 px-4 z-40 max-w-4xl mx-auto">
            <a href="{{ route('public.order.cart', [$organization->id, $location->id]) }}" class="w-full bg-[#0F172A] hover:bg-black text-white rounded-2xl shadow-xl p-4 flex justify-between items-center transition transform active:scale-95 border border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-400/20 border border-amber-400/30 flex items-center justify-center font-black text-amber-400 text-base">
                        🛒
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-slate-300 tracking-widest uppercase">{{ $count }} {{ $count === 1 ? 'ITEM' : 'ITEMS' }} ADDED</div>
                        <div class="text-lg font-black text-amber-300 font-mono">₹{{ number_format($cartTotal, 2) }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#0F172A] bg-amber-400 hover:bg-amber-300 px-5 py-3 rounded-xl shadow-md transition">
                    <span>View Cart & Checkout</span>
                    <span>&rarr;</span>
                </div>
            </a>
        </div>
        @endif
    @endif

</body>
</html>

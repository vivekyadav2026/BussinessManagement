<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $organization->name ?? 'Dine-In Menu' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; 
            background-color: #ffffff; 
            color: #1f2937;
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        html { scroll-behavior: smooth; }
        
        /* Petpooja Orange theme colors */
        :root {
            --petpooja-orange: #f97316;
            --petpooja-orange-deep: #ea580c;
            --petpooja-cream: #fff7ed;
        }
        
        /* Veg Dot Icon */
        .veg-icon {
            width: 15px;
            height: 15px;
            border: 1.5px solid #16a34a;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            flex-shrink: 0;
        }
        .veg-icon-dot {
            width: 7px;
            height: 7px;
            background-color: #16a34a;
            border-radius: 50%;
        }

        /* Non-Veg Icon */
        .non-veg-icon {
            width: 15px;
            height: 15px;
            border: 1.5px solid #dc2626;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            flex-shrink: 0;
        }
        .non-veg-icon-dot {
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 7px solid #dc2626;
        }
    </style>
</head>
<body class="bg-white text-gray-900 pb-24 antialiased selection:bg-orange-500 selection:text-white">

    <!-- Top Petpooja Header -->
    <header class="bg-white sticky top-0 z-40 border-b border-gray-100 shadow-2xs">
        <div class="max-w-2xl mx-auto px-4 py-2.5 flex justify-between items-center gap-2">
            <!-- Left: Logo & Restaurant Name -->
            <div class="flex items-center gap-2.5 min-w-0">
                @if(isset($organization) && $organization->logo)
                    <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" class="h-10 w-10 object-cover rounded-full border border-gray-100 shadow-2xs shrink-0">
                @else
                    <div class="h-10 w-10 bg-gradient-to-br from-rose-600 to-red-700 rounded-full flex items-center justify-center text-white font-black text-sm shadow-2xs shrink-0 border border-red-600">
                        🍽️
                    </div>
                @endif
                <div class="min-w-0">
                    <h1 class="text-sm font-extrabold text-gray-900 uppercase tracking-tight truncate leading-tight">
                        {{ $organization->name ?? 'RESTAURANT DINE-IN' }}
                    </h1>
                    <p class="text-[11px] font-semibold text-gray-400 truncate flex items-center gap-1">
                        <span>📍</span> {{ $location->name ?? 'Main Outlet' }}
                    </p>
                </div>
            </div>
            
            <!-- Right: Table Badge -->
            <div class="flex items-center gap-1.5 shrink-0">
                @php
                    $tableId = session('restaurant_table_id');
                    $tableObj = $tableId ? \App\Models\RestaurantTable::find($tableId) : null;
                    $tableName = $tableObj ? $tableObj->name : ($table->name ?? null);
                    $tableCode = $tableName ? (preg_match('/^table\s*/i', $tableName) ? $tableName : 'Table ' . $tableName) : null;
                @endphp
                @if($tableCode)
                    <div class="px-2.5 py-1 bg-gray-50 text-gray-800 border border-gray-200 rounded-lg text-xs font-bold flex items-center gap-1 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 10h18M7 15h1m8 0h1m-9 5h8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>{{ $tableCode }}</span>
                    </div>
                @else
                    <span class="px-2.5 py-1 bg-orange-50 text-orange-700 border border-orange-200 rounded-lg text-[11px] font-bold">
                        🛍️ Takeaway
                    </span>
                @endif
            </div>
        </div>
        
        @yield('header_extensions')
    </header>

    <!-- Main Content Area -->
    <main class="max-w-2xl mx-auto">
        @yield('content')
    </main>

    <!-- Floating Bottom Cart Bar (Appears above the nav bar when items in cart) -->
    @if(!request()->routeIs('public.order.cart') && !request()->routeIs('public.order.checkout') && !request()->routeIs('public.order.track'))
        @php
            $cartKey = 'cart_' . ($location->id ?? 0);
            $cart = session()->get($cartKey, []);
            $count = array_sum(array_column($cart, 'quantity'));
            $cartTotal = 0;
            foreach($cart as $cItem) {
                $cartTotal += $cItem['price'] * $cItem['quantity'];
            }
        @endphp

        @if($count > 0)
        <div class="fixed bottom-16 left-0 right-0 px-4 z-40 max-w-2xl mx-auto pointer-events-none">
            <a href="{{ route('public.order.cart', [$organization->id, $location->id]) }}" class="pointer-events-auto w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-2xl shadow-xl p-3.5 flex justify-between items-center transition transform active:scale-98">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center font-black text-white text-base">
                        🛒
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-orange-100 uppercase tracking-wider">{{ $count }} {{ $count === 1 ? 'ITEM' : 'ITEMS' }} ADDED</div>
                        <div class="text-base font-black text-white">₹{{ number_format($cartTotal, 2) }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-orange-600 bg-white px-4 py-2.5 rounded-xl shadow-xs">
                    <span>View Cart</span>
                    <span>&rarr;</span>
                </div>
            </a>
        </div>
        @endif
    @endif

</body>
</html>

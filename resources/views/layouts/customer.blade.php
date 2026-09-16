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
            color: #020617;
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        html { scroll-behavior: smooth; }
        
        /* Unified Slate & Amber brand accents */
        :root {
            --brand-primary: #D99A2B;
            --brand-dark: #020617;
            --brand-surface: #f8fafc;
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
<body class="bg-white text-slate-900 pb-24 antialiased selection:bg-amber-500 selection:text-slate-950">

    <!-- Top Restaurant Header -->
    <header class="bg-white sticky top-0 z-40 border-b border-slate-200/90 shadow-2xs">
        <div class="max-w-2xl mx-auto px-4 py-2.5 flex justify-between items-center gap-2">
            <!-- Left: Logo & Restaurant Name -->
            <div class="flex items-center gap-2.5 min-w-0">
                @if(isset($organization) && $organization->logo)
                    <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" class="h-10 w-10 object-cover rounded-xl border border-slate-200 shadow-2xs shrink-0">
                @else
                    <div class="h-10 w-10 bg-slate-950 text-amber-400 rounded-xl flex items-center justify-center font-black text-base shadow-xs shrink-0 border border-slate-800">
                        🍽️
                    </div>
                @endif
                <div class="min-w-0">
                    <h1 class="text-sm font-black text-slate-950 uppercase tracking-tight truncate leading-tight">
                        {{ $organization->name ?? 'RESTAURANT DINE-IN' }}
                    </h1>
                    <p class="text-[11px] font-semibold text-slate-500 truncate flex items-center gap-1">
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
                    <div class="px-3 py-1.5 bg-slate-950 text-amber-400 border border-slate-800 rounded-xl text-xs font-black font-mono flex items-center gap-1.5 shadow-xs whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 10h18M7 15h1m8 0h1m-9 5h8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>{{ $tableCode }}</span>
                    </div>
                @else
                    <span class="px-3 py-1.5 bg-slate-100 text-slate-800 border border-slate-200 rounded-xl text-xs font-bold whitespace-nowrap">
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
            <a href="{{ route('public.order.cart', [$organization->id, $location->id]) }}" class="pointer-events-auto w-full bg-slate-950 text-white rounded-2xl shadow-2xl border border-slate-800 hover:border-slate-700 p-3.5 flex justify-between items-center transition transform active:scale-98">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center font-black text-lg shrink-0">
                        🛒
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-amber-400 uppercase tracking-widest">{{ $count }} {{ $count === 1 ? 'ITEM' : 'ITEMS' }} ADDED</div>
                        <div class="text-base font-black text-white font-mono">₹{{ number_format($cartTotal, 2) }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-slate-950 bg-amber-500 hover:bg-amber-400 px-4 py-2.5 rounded-xl shadow-md transition whitespace-nowrap">
                    <span>View Cart</span>
                    <span>&rarr;</span>
                </div>
            </a>
        </div>
        @endif
    @endif

</body>
</html>

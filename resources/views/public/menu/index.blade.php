@extends('layouts.customer')

@section('content')
<div x-data="{ 
    activeTab: 'menu', 
    selectedCat: 'all', 
    orderSubTab: 'orders',
    searchQuery: '',
    dishModalOpen: false,
    modalDish: {},
    modalCategory: ''
}" class="min-h-screen bg-white pb-10">

    <!-- TAB 1: HOME OVERVIEW -->
    <div x-show="activeTab === 'home'" x-cloak class="px-4 py-5 space-y-5">
        <!-- Restaurant Welcome Hero -->
        <div class="bg-gradient-to-br from-orange-50 via-amber-50/50 to-white p-6 rounded-3xl border border-orange-100/80 shadow-2xs space-y-4">
            <div class="flex items-center gap-3.5">
                @if($organization->logo)
                    <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" class="w-14 h-14 object-cover rounded-2xl border border-white shadow-xs">
                @else
                    <div class="w-14 h-14 bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-xs">
                        🍽️
                    </div>
                @endif
                <div>
                    <h2 class="text-lg font-black text-gray-900 tracking-tight leading-tight">{{ $organization->name }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1 font-medium">
                        <span>📍</span> {{ $location->name }} &bull; Dine-in & Express
                    </p>
                </div>
            </div>

            @if(session('restaurant_table_id'))
                @php
                    $tableObj = \App\Models\RestaurantTable::find(session('restaurant_table_id'));
                    $tableName = $tableObj ? $tableObj->name : ($table->name ?? '1');
                    $displayName = preg_match('/^table\s*/i', $tableName) ? $tableName : 'Table ' . $tableName;
                @endphp
                <div class="flex items-center justify-between bg-white px-4 py-3 rounded-2xl border border-orange-200/70 shadow-2xs">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">🪑</span>
                        <div>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-orange-600 block">Current Table</span>
                            <span class="text-sm font-black text-gray-900">{{ $displayName }}</span>
                        </div>
                    </div>
                    @if($activeOrders->count() > 0)
                        <span class="px-2.5 py-1 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-lg border border-rose-200 uppercase tracking-wider">
                            ● Occupied
                        </span>
                    @else
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-lg border border-emerald-200 uppercase tracking-wider">
                            ● Available
                        </span>
                    @endif
                </div>
            @endif

            <p class="text-xs text-gray-600 leading-relaxed font-medium">
                Welcome to our digital contactless dining experience! Browse our freshly cooked delicacies, customize your order, and track kitchen updates right from your phone.
            </p>

            <button type="button" @click="activeTab = 'menu'" 
                    class="w-full py-3.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-2xl font-extrabold text-sm shadow-md transition flex items-center justify-center gap-2 active:scale-98">
                <span>🍽️ Explore Full Menu</span>
                <span>&rarr;</span>
            </button>
        </div>

        <!-- Quick Categories Grid -->
        <div class="space-y-3">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-gray-400">Browse Menu Categories</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach($categories as $category)
                    <div @click="activeTab = 'menu'; selectedCat = {{ $category->id }}"
                         class="bg-gray-50/80 hover:bg-orange-50/60 p-3.5 rounded-2xl border border-gray-100 hover:border-orange-200 text-center cursor-pointer transition flex flex-col items-center gap-1.5 shadow-2xs group">
                        <span class="text-2xl group-hover:scale-110 transition-transform">🍲</span>
                        <span class="text-xs font-bold text-gray-800 group-hover:text-orange-600">{{ $category->name }}</span>
                        <span class="text-[10px] text-gray-400 font-semibold">{{ $category->items->count() }} items</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- TAB 2: MENU VIEW (DEFAULT ACTIVE) -->
    <div x-show="activeTab === 'menu'" class="space-y-4">
        
        <!-- Search Input Bar -->
        <div class="px-4 pt-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Search item name or category..." 
                       class="w-full pl-10 pr-9 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:bg-white transition placeholder:text-gray-400 shadow-2xs">
                <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
        </div>

        <!-- Category Carousel -->
        <div class="px-4 pt-1">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <!-- All Items Card -->
                <div @click="selectedCat = 'all'" 
                     :class="selectedCat === 'all' ? 'border-slate-950 bg-slate-950 text-amber-400 font-black shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'"
                     class="relative shrink-0 flex flex-col items-center justify-center p-2.5 rounded-2xl border min-w-[80px] cursor-pointer transition">
                    <!-- Checkmark Badge on Active -->
                    <span x-show="selectedCat === 'all'" class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center text-[9px] font-black shadow-2xs">✓</span>
                    <span class="text-xl mb-1">🍽️</span>
                    <span class="text-[11px] font-black whitespace-nowrap">All Items</span>
                    <!-- Amber Bottom Line Indicator -->
                    <span x-show="selectedCat === 'all'" class="absolute bottom-0 left-3 right-3 h-0.5 bg-amber-400 rounded-full"></span>
                </div>

                @foreach($categories as $cat)
                    @if($cat->items->isNotEmpty())
                        <div @click="selectedCat = {{ $cat->id }}" 
                             :class="selectedCat === {{ $cat->id }} ? 'border-slate-950 bg-slate-950 text-amber-400 font-black shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'"
                             class="relative shrink-0 flex flex-col items-center justify-center p-2.5 rounded-2xl border min-w-[80px] cursor-pointer transition">
                            <!-- Checkmark Badge on Active -->
                            <span x-show="selectedCat === {{ $cat->id }}" class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center text-[9px] font-black shadow-2xs">✓</span>
                            <span class="text-xl mb-1">🍲</span>
                            <span class="text-[11px] font-black whitespace-nowrap">{{ $cat->name }}</span>
                            <!-- Amber Bottom Line Indicator -->
                            <span x-show="selectedCat === {{ $cat->id }}" class="absolute bottom-0 left-3 right-3 h-0.5 bg-amber-400 rounded-full"></span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        @php
            $cartKey = 'cart_' . ($location->id ?? 0);
            $cart = session()->get($cartKey, []);
        @endphp

        <!-- Menu Categories & Dish Listing -->
        <div class="px-4 space-y-6">
            @foreach($categories as $category)
                @if($category->items->isNotEmpty())
                    <div x-show="selectedCat === 'all' || selectedCat === {{ $category->id }}" class="space-y-2">
                        
                        <!-- Category Header Bar (e.g. Breakfast (28)) -->
                        <div class="flex items-center justify-between pt-2 pb-1 border-b border-gray-100">
                            <h3 class="text-base font-black text-gray-900 tracking-tight flex items-center gap-2">
                                <span>{{ $category->name }}</span>
                                <span class="text-xs font-bold text-gray-400 font-mono">({{ $category->items->count() }})</span>
                            </h3>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>

                        <!-- Dishes List Rows (Petpooja Style) -->
                        <div class="divide-y divide-gray-100 divide-dashed">
                            @foreach($category->items as $item)
                                @php
                                    $inCartQty = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
                                    $isNonVeg = preg_match('/chicken|mutton|egg|fish|meat|prawn/i', $item->name . ' ' . ($item->description ?? ''));
                                @endphp
                                <div x-show="!searchQuery || '{{ strtolower(addslashes($item->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($category->name)) }}'.includes(searchQuery.toLowerCase())"
                                     class="py-4 flex justify-between items-start gap-4 {{ !$item->is_available ? 'opacity-50' : '' }}">
                                    
                                    <!-- Left Side: Veg Icon, Name, Price, Description -->
                                    <div class="flex-1 min-w-0 pr-1 space-y-1">
                                        <!-- Veg / Non-veg Indicator -->
                                        @if($isNonVeg)
                                            <div class="non-veg-icon" title="Non-Vegetarian">
                                                <div class="non-veg-icon-dot"></div>
                                            </div>
                                        @else
                                            <div class="veg-icon" title="Pure Vegetarian">
                                                <div class="veg-icon-dot"></div>
                                            </div>
                                        @endif

                                        <!-- Dish Title -->
                                        <h4 @click="modalDish = {{ json_encode($item) }}; modalCategory = '{{ addslashes($category->name) }}'; dishModalOpen = true" 
                                            class="text-sm sm:text-base font-bold text-gray-900 cursor-pointer hover:text-orange-600 transition leading-snug">
                                            {{ $item->name }}
                                        </h4>

                                        <!-- Dish Price -->
                                        <div class="text-sm font-extrabold text-gray-900 font-mono">
                                            ₹{{ number_format($item->price, 2) }}
                                        </div>

                                        <!-- Dish Description -->
                                        @if($item->description)
                                            <p @click="modalDish = {{ json_encode($item) }}; modalCategory = '{{ addslashes($category->name) }}'; dishModalOpen = true" 
                                               class="text-xs text-gray-500 font-medium line-clamp-2 leading-relaxed cursor-pointer pt-0.5">
                                                {{ $item->description }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Right Side: Image Thumbnail & + Add / Stepper Button -->
                                    <div class="relative shrink-0 flex flex-col items-center">
                                        <!-- Image Container -->
                                        <div @click="modalDish = {{ json_encode($item) }}; modalCategory = '{{ addslashes($category->name) }}'; dishModalOpen = true" 
                                             class="w-24 h-20 sm:w-28 sm:h-22 rounded-2xl overflow-hidden cursor-pointer shadow-2xs border border-gray-100 flex items-center justify-center bg-[#fff7ed]">
                                            @if($item->photo)
                                                <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                            @else
                                                <!-- Warm Petpooja Food Plate Illustration -->
                                                <div class="text-orange-400 flex flex-col items-center justify-center">
                                                    <svg class="w-10 h-10 stroke-current" fill="none" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- + Add Button / Stepper Controller -->
                                        <div class="mt-2 w-max">
                                            @if($item->is_available)
                                                @if($inCartQty > 0)
                                                    <!-- Quantity Stepper -->
                                                    <div class="flex items-center bg-slate-950 text-white rounded-xl shadow-xs font-bold text-xs overflow-hidden border border-slate-800">
                                                        <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $item->id]) }}" method="POST" class="m-0 p-0">
                                                            @csrf
                                                            <input type="hidden" name="action" value="decrease">
                                                            <button type="submit" class="px-2.5 py-1 text-slate-300 hover:text-white hover:bg-slate-900 transition text-sm font-black">&minus;</button>
                                                        </form>
                                                        <span class="px-2 py-1 font-mono font-black text-xs text-amber-400">{{ $inCartQty }}</span>
                                                        <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $item->id]) }}" method="POST" class="m-0 p-0">
                                                            @csrf
                                                            <input type="hidden" name="action" value="increase">
                                                            <button type="submit" class="px-2.5 py-1 text-slate-300 hover:text-white hover:bg-slate-900 transition text-sm font-black">+</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <!-- Add Button -->
                                                    <form action="{{ route('public.order.add', [$organization->id, $location->id]) }}" method="POST" class="m-0 p-0">
                                                        @csrf
                                                        <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                                        <button type="submit" class="bg-white border-2 border-slate-900 hover:bg-slate-950 hover:text-amber-400 text-slate-950 font-black px-4 py-1 rounded-xl text-xs uppercase tracking-wider shadow-2xs transition active:scale-95 flex items-center gap-1 cursor-pointer whitespace-nowrap">
                                                            <span>+ Add</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider bg-slate-100 px-2 py-0.5 rounded-md">
                                                    Sold Out
                                                </span>
                                            @endif
                                        </div>

                                    </div>

                                </div>
                            @endforeach
                        </div>

                    </div>
                @endif
            @endforeach
        </div>

    </div>

    <!-- TAB 3: ORDERS VIEW (Matches Petpooja screenshot 3) -->
    <div x-show="activeTab === 'orders'" x-cloak class="px-4 py-4 space-y-6">
        
        <!-- Orders Sub-tab Bar -->
        <div class="flex items-center gap-6 border-b border-gray-100 text-sm font-bold pb-2">
            <button type="button" @click="orderSubTab = 'orders'"
                    :class="orderSubTab === 'orders' ? 'text-orange-600 border-b-2 border-orange-500 pb-2 -mb-2 font-extrabold' : 'text-gray-400 hover:text-gray-600'" 
                    class="flex items-center gap-1.5 transition">
                <span>🍴 Orders</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold" 
                      :class="orderSubTab === 'orders' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-500'">
                    {{ $activeOrders->count() }}
                </span>
            </button>
            <button type="button" @click="orderSubTab = 'items'"
                    :class="orderSubTab === 'items' ? 'text-orange-600 border-b-2 border-orange-500 pb-2 -mb-2 font-extrabold' : 'text-gray-400 hover:text-gray-600'" 
                    class="flex items-center gap-1.5 transition">
                <span>📋 Item List</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold"
                      :class="orderSubTab === 'items' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-500'">
                    {{ $activeOrders->pluck('items')->flatten()->sum('quantity') }}
                </span>
            </button>
        </div>

        <!-- Orders Subtab Content -->
        <div x-show="orderSubTab === 'orders'">
            @if($activeOrders->isEmpty())
                <!-- Petpooja Empty State: Cloche/Platter Illustration -->
                <div class="text-center py-16 space-y-4 max-w-sm mx-auto">
                    <div class="w-36 h-28 mx-auto flex items-center justify-center">
                        <svg viewBox="0 0 200 150" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Tray / Base -->
                            <rect x="20" y="115" width="160" height="8" rx="4" fill="#1f2937" />
                            <rect x="30" y="110" width="140" height="5" fill="#f97316" />
                            
                            <!-- Cloche Dome -->
                            <path d="M40 105 C40 50, 160 50, 160 105 Z" fill="#ffffff" stroke="#1f2937" stroke-width="4" stroke-linejoin="round"/>
                            <path d="M48 100 C52 65, 120 58, 125 65" stroke="#f97316" stroke-width="3" stroke-linecap="round"/>
                            
                            <!-- Cloche Handle -->
                            <circle cx="100" cy="45" r="7" fill="#f97316" stroke="#1f2937" stroke-width="3.5" />
                            
                            <!-- Tea Cup alongside -->
                            <path d="M125 85 h25 a12 12 0 0 1 12 12 v8 a0 0 0 0 1 0 0 h-37 a0 0 0 0 1 0 0 v-8 a12 12 0 0 1 0 -12 z" fill="#ffffff" stroke="#1f2937" stroke-width="3" />
                            <path d="M162 90 a6 6 0 0 1 0 10 h-5" stroke="#1f2937" stroke-width="3" fill="none"/>
                            <rect x="120" y="105" width="45" height="4" rx="2" fill="#f97316" stroke="#1f2937" stroke-width="2"/>
                        </svg>
                    </div>

                    <div class="space-y-1">
                        <h4 class="text-lg font-black text-gray-900">No Orders Yet</h4>
                        <p class="text-xs text-gray-400 font-medium leading-relaxed">
                            You haven't ordered anything yet. Place your first order.
                        </p>
                    </div>

                    <button type="button" @click="activeTab = 'menu'" 
                            class="px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs rounded-xl shadow-md transition active:scale-95">
                        Start Ordering
                    </button>
                </div>
            @else
                <!-- Active Placed Orders List -->
                <div class="space-y-4">
                    @foreach($activeOrders as $ord)
                        <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                <div>
                                    <span class="text-xs font-mono font-black text-gray-900">{{ $ord->order_number }}</span>
                                    <span class="text-[10px] text-gray-400 font-medium block">{{ $ord->created_at->format('h:i A') }}</span>
                                </div>
                                <div class="flex gap-1">
                                    @if($ord->payment_status === 'Paid')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Paid ✅
                                        </span>
                                    @endif
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                        @if($ord->status === 'Received') bg-amber-50 text-amber-800 border border-amber-200
                                        @elseif($ord->status === 'Preparing') bg-blue-50 text-blue-800 border border-blue-200
                                        @elseif($ord->status === 'Ready') bg-emerald-50 text-emerald-800 border border-emerald-200
                                        @else bg-gray-50 text-gray-800 border border-gray-200 @endif">
                                        {{ $ord->status }}
                                    </span>
                                </div>
                            </div>

                            <!-- Ordered Items List -->
                            <div class="space-y-1.5 text-xs">
                                @foreach($ord->items as $oItem)
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-800">
                                            <b class="text-orange-600 font-black font-mono mr-1">{{ $oItem->quantity }}x</b> 
                                            {{ $oItem->name_snapshot }}
                                        </span>
                                        <span class="font-mono font-bold text-gray-900">₹{{ number_format($oItem->total, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-between items-center pt-2 border-t border-gray-100 text-xs">
                                <span class="font-bold text-gray-500">Total Bill</span>
                                <span class="text-base font-black text-orange-600 font-mono">₹{{ number_format($ord->total, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Item List Subtab Content -->
        <div x-show="orderSubTab === 'items'" x-cloak>
            @php
                $allItems = $activeOrders->pluck('items')->flatten();
            @endphp
            @if($allItems->isEmpty())
                <div class="text-center py-16 space-y-2 text-gray-400 text-xs">
                    <span class="text-4xl block">📋</span>
                    <p class="font-bold text-gray-600">No items ordered yet</p>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs divide-y divide-gray-100">
                    @foreach($allItems as $item)
                        <div class="py-2.5 flex justify-between items-center text-xs">
                            <span class="font-bold text-gray-900">{{ $item->name_snapshot }}</span>
                            <span class="px-2 py-0.5 bg-gray-100 font-mono font-bold text-gray-700 rounded-md">
                                x{{ $item->quantity }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- TAB 4: PAY BILL VIEW (Matches Petpooja screenshot 1) -->
    <div x-show="activeTab === 'bill'" x-cloak class="px-4 py-5 space-y-6">
        @if($activeOrders->isEmpty())
            <!-- Petpooja Empty State: Thermal Printer / Hand Illustration -->
            <div class="text-center py-16 space-y-4 max-w-sm mx-auto">
                <div class="w-36 h-28 mx-auto flex items-center justify-center">
                    <svg viewBox="0 0 200 150" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Hand receiving receipt -->
                        <path d="M40 80 L70 80 C85 80, 95 85, 105 100 L95 108 L75 95 L50 95 Z" fill="#ffffff" stroke="#1f2937" stroke-width="3"/>
                        <path d="M40 75 L70 75 C85 75, 105 80, 115 95 L108 100 L85 88 L50 88 Z" fill="#ffedd5" stroke="#1f2937" stroke-width="3"/>
                        <rect x="35" y="70" width="20" height="35" rx="3" fill="#f97316" stroke="#1f2937" stroke-width="3"/>
                        
                        <!-- Printer Device -->
                        <rect x="95" y="65" width="65" height="48" rx="6" fill="#ffffff" stroke="#1f2937" stroke-width="3.5" />
                        <rect x="108" y="74" width="25" height="15" rx="3" fill="#f97316" />
                        <circle cx="145" cy="78" r="2.5" fill="#1f2937" />
                        <circle cx="145" cy="85" r="2.5" fill="#1f2937" />
                        
                        <!-- Wavy Receipt paper rolling out -->
                        <path d="M125 65 L125 35 C130 25, 150 40, 155 30 L165 42 L135 65 Z" fill="#ffffff" stroke="#1f2937" stroke-width="3" stroke-linejoin="round"/>
                        <path d="M135 42 h15" stroke="#f97316" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M138 48 h12" stroke="#f97316" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </div>

                <div class="space-y-1">
                    <h4 class="text-lg font-black text-gray-900">No Bill Generated Yet</h4>
                    <p class="text-xs text-gray-400 font-medium leading-relaxed">
                        Your bill will appear here once you place your order.
                    </p>
                </div>

                <button type="button" @click="activeTab = 'menu'" 
                        class="px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs rounded-xl shadow-md transition active:scale-95">
                    Start Ordering
                </button>
            </div>
        @else
            <!-- Live Itemized Bill -->
            @php
                $subtotalSum = $activeOrders->sum('subtotal');
                $taxSum = $activeOrders->sum('tax');
                $totalSum = $activeOrders->sum('total');
                $paidSum = $activeOrders->where('payment_status', 'Paid')->sum('total');
                $amountDue = max(0, $totalSum - $paidSum);
            @endphp
            <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-xs space-y-4">
                <div class="text-center pb-3 border-b border-gray-100">
                    <h3 class="font-black text-base text-gray-900 uppercase tracking-tight">{{ $organization->name }}</h3>
                    <p class="text-[11px] text-gray-400 font-semibold">{{ $location->name }}</p>
                    <div class="inline-block mt-2 px-3 py-0.5 bg-orange-50 text-orange-700 rounded-full text-xs font-mono font-bold">
                        Table Receipt Summary
                    </div>
                </div>

                <!-- Combined Orders Items -->
                <div class="space-y-2 text-xs divide-y divide-gray-100">
                    @foreach($activeOrders as $ord)
                        @foreach($ord->items as $item)
                            <div class="pt-2 flex justify-between items-center">
                                <span class="font-bold text-gray-800">
                                    <span class="text-orange-600 mr-1 font-mono font-black">{{ $item->quantity }}x</span> 
                                    {{ $item->name_snapshot }}
                                </span>
                                <span class="font-mono font-bold text-gray-900">₹{{ number_format($item->total, 2) }}</span>
                            </div>
                        @endforeach
                    @endforeach
                </div>

                <!-- Tax & Grand Total Breakdown -->
                <div class="border-t border-dashed border-gray-200 pt-3 space-y-1.5 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Items Subtotal</span>
                        <span class="font-mono font-semibold text-gray-900">₹{{ number_format($subtotalSum, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500 text-[11px]">
                        <span>Taxes & GST</span>
                        <span class="font-mono">₹{{ number_format($taxSum, 2) }}</span>
                    </div>
                    @if($paidSum > 0)
                        <div class="flex justify-between text-emerald-600 text-[11px] font-bold">
                            <span>Already Paid Online</span>
                            <span class="font-mono">-₹{{ number_format($paidSum, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-baseline pt-2 border-t border-gray-200">
                        <span class="font-black text-sm uppercase text-gray-900">Amount Due</span>
                        <span class="text-xl font-black text-orange-600 font-mono">₹{{ number_format($amountDue, 2) }}</span>
                    </div>
                </div>

                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200/80 text-[11px] text-amber-800 font-semibold text-center">
                    💡 Please pay at the counter or request staff to collect payment.
                </div>
            </div>
        @endif
    </div>

    <!-- Dish Quick View Modal -->
    <div x-show="dishModalOpen" x-cloak class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200 space-y-0 transform transition-all" @click.away="dishModalOpen = false">
            <div class="relative w-full h-56 bg-slate-900 flex items-center justify-center overflow-hidden">
                <template x-if="modalDish.photo">
                    <img :src="'/storage/' + modalDish.photo" class="w-full h-full object-cover">
                </template>
                <template x-if="!modalDish.photo">
                    <div class="w-full h-full bg-gradient-to-br from-slate-900 to-slate-950 flex items-center justify-center">
                        <span class="text-6xl filter drop-shadow-md">🍱</span>
                    </div>
                </template>

                <!-- Top badges & close button -->
                <div class="absolute top-3.5 inset-x-3.5 flex items-center justify-between pointer-events-none">
                    <span class="pointer-events-auto px-3 py-1 rounded-full text-[10px] font-mono font-black uppercase tracking-wider bg-slate-950/80 backdrop-blur-md text-amber-400 border border-slate-700/60 shadow-xs" x-text="modalCategory"></span>
                    
                    <button type="button" @click="dishModalOpen = false" 
                            class="pointer-events-auto w-9 h-9 rounded-full bg-slate-950/70 hover:bg-slate-950 text-white flex items-center justify-center font-bold text-sm backdrop-blur-md transition shadow-xs">
                        ✕
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <template x-if="modalDish.name && (modalDish.name.toLowerCase().includes('chicken') || modalDish.name.toLowerCase().includes('mutton') || modalDish.name.toLowerCase().includes('egg') || modalDish.name.toLowerCase().includes('fish') || modalDish.name.toLowerCase().includes('meat'))">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black uppercase bg-red-50 text-red-700 border border-red-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span> Non-Veg
                            </span>
                        </template>
                        <template x-if="modalDish.name && !(modalDish.name.toLowerCase().includes('chicken') || modalDish.name.toLowerCase().includes('mutton') || modalDish.name.toLowerCase().includes('egg') || modalDish.name.toLowerCase().includes('fish') || modalDish.name.toLowerCase().includes('meat'))">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Pure Veg
                            </span>
                        </template>
                    </div>

                    <h3 class="text-xl font-black text-slate-950 tracking-tight" x-text="modalDish.name"></h3>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed" x-text="modalDish.description || 'Authentic gourmet recipe freshly prepared to order with handpicked ingredients.'"></p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Price</span>
                        <span class="text-2xl font-black text-slate-950 font-mono">₹<span x-text="parseFloat(modalDish.price || 0).toFixed(2)"></span></span>
                    </div>
                    
                    <form action="{{ route('public.order.add', [$organization->id, $location->id]) }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="menu_item_id" :value="modalDish.id">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 font-black px-6 py-3 rounded-xl text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition active:scale-95 flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Add to Order</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- FIXED 4-TAB BOTTOM NAVIGATION BAR -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-[0_-4px_25px_rgba(0,0,0,0.06)]">
        <div class="max-w-2xl mx-auto px-6 py-2 flex justify-between items-center text-center">
            
            <!-- 1. Home Tab -->
            <button type="button" @click="activeTab = 'home'" 
                    class="flex flex-col items-center justify-center flex-1 py-1 transition group">
                <svg class="w-5 h-5 mb-0.5 transition" 
                     :class="activeTab === 'home' ? 'text-slate-950 stroke-[2.5]' : 'text-slate-400 stroke-[1.8] group-hover:text-slate-600'" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] tracking-tight transition" 
                      :class="activeTab === 'home' ? 'text-slate-950 font-black' : 'text-slate-400 font-medium group-hover:text-slate-600'">
                    Home
                </span>
                <span x-show="activeTab === 'home'" class="w-1 h-1 rounded-full bg-amber-500 mt-0.5"></span>
            </button>

            <!-- 2. Menu Tab -->
            <button type="button" @click="activeTab = 'menu'" 
                    class="flex flex-col items-center justify-center flex-1 py-1 transition group">
                <svg class="w-5 h-5 mb-0.5 transition" 
                     :class="activeTab === 'menu' ? 'text-slate-950 stroke-[2.5]' : 'text-slate-400 stroke-[1.8] group-hover:text-slate-600'" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-[10px] tracking-tight transition" 
                      :class="activeTab === 'menu' ? 'text-slate-950 font-black' : 'text-slate-400 font-medium group-hover:text-slate-600'">
                    Menu
                </span>
                <span x-show="activeTab === 'menu'" class="w-1 h-1 rounded-full bg-amber-500 mt-0.5"></span>
            </button>

            <!-- 3. Orders Tab -->
            <button type="button" @click="activeTab = 'orders'" 
                    class="flex flex-col items-center justify-center flex-1 py-1 transition group relative">
                @if($activeOrders->count() > 0)
                    <span class="absolute top-0 right-1/4 w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                @endif
                <svg class="w-5 h-5 mb-0.5 transition" 
                     :class="activeTab === 'orders' ? 'text-slate-950 stroke-[2.5]' : 'text-slate-400 stroke-[1.8] group-hover:text-slate-600'" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="text-[10px] tracking-tight transition" 
                      :class="activeTab === 'orders' ? 'text-slate-950 font-black' : 'text-slate-400 font-medium group-hover:text-slate-600'">
                    Orders
                </span>
                <span x-show="activeTab === 'orders'" class="w-1 h-1 rounded-full bg-amber-500 mt-0.5"></span>
            </button>

            <!-- 4. Pay Bill Tab -->
            <button type="button" @click="activeTab = 'bill'" 
                    class="flex flex-col items-center justify-center flex-1 py-1 transition group">
                <svg class="w-5 h-5 mb-0.5 transition" 
                     :class="activeTab === 'bill' ? 'text-slate-950 stroke-[2.5]' : 'text-slate-400 stroke-[1.8] group-hover:text-slate-600'" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-[10px] tracking-tight transition" 
                      :class="activeTab === 'bill' ? 'text-slate-950 font-black' : 'text-slate-400 font-medium group-hover:text-slate-600'">
                    Pay Bill
                </span>
                <span x-show="activeTab === 'bill'" class="w-1 h-1 rounded-full bg-amber-500 mt-0.5"></span>
            </button>

        </div>
    </nav>

</div>
@endsection

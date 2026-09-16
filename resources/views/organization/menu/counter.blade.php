@extends('layouts.sme')

@section('title', 'Counter Billing & QSR Express POS')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20" x-data="counterBilling()" x-init="init()" @keydown.window="handleGlobalKeydown($event)">
    
    <!-- 1. Breadcrumb & Page Header (Exact Organization / Employees Style) -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Restaurant</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Operations</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Counter Billing</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Counter Billing & Express POS</h1>
            <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed max-w-3xl">
                High-speed token ordering, live counter queue, kitchen dispatch, and instant register checkout.
            </p>
        </div>

        <!-- Right Header Status & Action Controls -->
        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-100/80 text-emerald-950 border border-emerald-300 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                <span>Terminal Online</span>
                <span class="text-slate-300">|</span>
                <span class="font-mono text-slate-700" x-text="liveClock">--:--:--</span>
            </div>

            <a href="{{ route('organization.menu.kitchen.index') }}" target="_blank" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-2xs transition">
                <span>👨‍🍳 Kitchen KOT &rarr;</span>
            </a>

            <a href="{{ route('organization.menu.pos.index') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <span>🍽️ Waiter POS</span>
            </a>
        </div>
    </div>

    <!-- 2. KPI Metrics Strip (Exact 4 Cards from Employees Page) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Pending Hold Tokens -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs cursor-pointer hover:border-amber-400 transition" @click="activeTab = 'current'">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Active Tokens</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-bold">⏳</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950 font-mono" x-text="activeOrders.length">0</div>
            <div class="text-[11px] text-amber-800 font-semibold mt-1">Pending at kitchen / counter</div>
        </div>

        <!-- Metric 2: Settled Bills Today -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs cursor-pointer hover:border-emerald-400 transition" @click="activeTab = 'completed'">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Settled Bills</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">✓</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950 font-mono" x-text="completedOrders.length">0</div>
            <div class="text-[11px] text-emerald-800 font-semibold mt-1">Settled today</div>
        </div>

        <!-- Metric 3: Today's Revenue -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Counter Sales</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">💰</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950 font-mono">₹<span x-text="totalSettledSales.toFixed(2)">0.00</span></div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Total revenue collected</div>
        </div>

        <!-- Metric 4: Average Order Value -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Average Ticket</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">📊</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950 font-mono">₹<span x-text="averageOrderValue.toFixed(2)">0.00</span></div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Average per bill</div>
        </div>
    </div>

    <!-- 3. Segmented Tab Switcher Bar -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-3 shadow-2xs flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-none">
            <button type="button" @click="activeTab = 'new'" 
                    :class="activeTab === 'new' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300 font-bold'" 
                    class="px-4 py-2 rounded-lg text-xs transition flex items-center gap-2 cursor-pointer whitespace-nowrap">
                <span>➕ New Express Order</span>
                <span x-show="cart.length > 0" class="bg-slate-950 text-amber-400 text-[10px] px-1.5 py-0.2 rounded font-mono font-black" x-text="cartTotalQty"></span>
            </button>
            <button type="button" @click="activeTab = 'current'" 
                    :class="activeTab === 'current' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300 font-bold'" 
                    class="px-4 py-2 rounded-lg text-xs transition flex items-center gap-2 cursor-pointer whitespace-nowrap">
                <span>⏳ Active Hold Tokens</span>
                <span class="bg-slate-950 text-white text-[10px] px-1.5 py-0.2 rounded font-mono font-black" x-text="activeOrders.length">0</span>
            </button>
            <button type="button" @click="activeTab = 'completed'" 
                    :class="activeTab === 'completed' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300 font-bold'" 
                    class="px-4 py-2 rounded-lg text-xs transition flex items-center gap-2 cursor-pointer whitespace-nowrap">
                <span>📜 Settled Bills</span>
                <span class="bg-slate-200 text-slate-800 text-[10px] px-1.5 py-0.2 rounded font-mono font-bold" x-text="completedOrders.length">0</span>
            </button>
        </div>

        <div class="flex items-center gap-2 self-end sm:self-auto">
            <button type="button" @click="shortcutsModalOpen = true" 
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                <span>⌨️ Hotkeys</span>
            </button>
            <button type="button" @click="toggleFullScreen()" 
                    class="p-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 rounded-lg text-xs font-bold transition shadow-2xs cursor-pointer" title="Fullscreen">
                ⛶
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: NEW EXPRESS ORDER & FAST BILLING    -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'new'" class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Left Panel: Menu Catalog (7 Cols on LG, 8 on XL) -->
        <div class="lg:col-span-7 xl:col-span-8 bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs space-y-4">
            
            <!-- Category Pills & Diet Filter Row -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
                
                <!-- Category Pills (Horizontal Scroll) -->
                <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 scrollbar-none">
                    <button type="button" @click="selectedCategory = 'all'" 
                            :class="selectedCategory === 'all' ? 'bg-slate-900 text-white font-extrabold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300 font-bold'" 
                            class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition cursor-pointer flex items-center gap-1.5">
                        <span>All Items</span>
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" @click="selectedCategory = {{ $cat->id }}" 
                                :class="selectedCategory === {{ $cat->id }} ? 'bg-slate-900 text-white font-extrabold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300 font-bold'" 
                                class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition cursor-pointer flex items-center gap-1.5">
                            <span>{{ $cat->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded font-mono"
                                  :class="selectedCategory === {{ $cat->id }} ? 'bg-amber-400 text-slate-950 font-black' : 'bg-slate-200 text-slate-700 font-bold'">
                                {{ $cat->items->count() }}
                            </span>
                        </button>
                    @endforeach
                </div>

                <!-- Diet Filter: All / Veg / Non-Veg -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg border border-slate-300 shrink-0">
                    <button type="button" @click="dietFilter = 'all'" 
                            :class="dietFilter === 'all' ? 'bg-slate-900 text-white font-extrabold shadow-2xs' : 'text-slate-700 font-bold hover:text-slate-950'" 
                            class="px-2.5 py-1 rounded-md text-xs transition cursor-pointer">
                        All
                    </button>
                    <button type="button" @click="dietFilter = 'veg'" 
                            :class="dietFilter === 'veg' ? 'bg-emerald-600 text-white font-extrabold shadow-2xs' : 'text-slate-700 font-bold hover:text-slate-950'" 
                            class="px-2.5 py-1 rounded-md text-xs transition cursor-pointer flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span> Veg
                    </button>
                    <button type="button" @click="dietFilter = 'non_veg'" 
                            :class="dietFilter === 'non_veg' ? 'bg-rose-600 text-white font-extrabold shadow-2xs' : 'text-slate-700 font-bold hover:text-slate-950'" 
                            class="px-2.5 py-1 rounded-md text-xs transition cursor-pointer flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-300"></span> Non-Veg
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" x-model="searchQuery" x-ref="searchInput" placeholder="Search dish name, code or category... (Press / to search)" 
                       class="w-full pl-10 bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition placeholder:text-slate-400 shadow-2xs">
                <button type="button" x-show="searchQuery" @click="searchQuery = ''; $refs.searchInput.focus()" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs font-bold cursor-pointer">✕</button>
            </div>

            <!-- Items Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-[580px] overflow-y-auto p-1 pr-2">
                @foreach($categories as $cat)
                    @foreach($cat->items as $item)
                        @php
                            $isVeg = (bool)($item->is_veg ?? true);
                        @endphp
                        <div x-show="isItemVisible({{ $cat->id }}, '{{ strtolower(addslashes($item->name)) }}', '{{ strtolower(addslashes($cat->name)) }}', {{ $isVeg ? 'true' : 'false' }})"
                            @click="addToCart({{ json_encode($item) }})"
                            class="bg-white border border-slate-200/90 hover:border-amber-500 rounded-xl p-3.5 cursor-pointer transition hover:shadow-xs active:scale-98 flex flex-col justify-between group relative overflow-hidden shadow-2xs">
                            
                            <!-- In-Cart Live Pill -->
                            <div x-show="getItemCartQty({{ $item->id }}) > 0" 
                                 class="absolute top-2 right-2 bg-amber-500 text-slate-950 px-2 py-0.5 rounded-md text-[10px] font-mono font-black shadow-2xs flex items-center gap-0.5">
                                <span x-text="getItemCartQty({{ $item->id }})"></span> in ticket
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex items-center gap-1.5">
                                    <!-- Veg / Non-Veg Indicator -->
                                    <span class="w-3.5 h-3.5 rounded-xs border flex items-center justify-center shrink-0 {{ $isVeg ? 'border-emerald-600' : 'border-rose-600' }}" title="{{ $isVeg ? 'Vegetarian' : 'Non-Vegetarian' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isVeg ? 'bg-emerald-600' : 'bg-rose-600' }}"></span>
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider truncate max-w-[90px]">{{ $cat->name }}</span>
                                </div>
                                <h3 class="text-xs sm:text-sm font-extrabold text-slate-950 group-hover:text-amber-700 transition line-clamp-2 leading-snug">
                                    {{ $item->name }}
                                </h3>
                            </div>

                            <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-100">
                                <span class="text-xs sm:text-sm font-mono font-black text-slate-950">₹{{ number_format($item->price, 2) }}</span>
                                <span class="w-7 h-7 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 flex items-center justify-center font-black text-sm transition shadow-2xs">
                                    +
                                </span>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        <!-- Right Panel: Express Billing Ticket (5 Cols on LG, 4 on XL) -->
        <div class="lg:col-span-5 xl:col-span-4 bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs sticky top-4 space-y-4 flex flex-col justify-between">
            <div>
                <!-- Ticket Header -->
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-700 flex items-center justify-center text-sm font-bold border border-amber-300 shadow-2xs">
                            🧾
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-950 flex items-center gap-1.5">
                                <span x-text="editingOrderId ? '✏️ Editing #' + editingOrderNumber : 'Billing Ticket'"></span>
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium">Quick-Service Register</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="cancelEditOrder()" x-show="editingOrderId" 
                                class="text-xs text-slate-600 hover:text-slate-900 font-bold hover:underline cursor-pointer">
                            Cancel Edit
                        </button>
                        <button type="button" @click="clearCart()" class="text-xs text-rose-700 hover:text-rose-900 font-bold hover:underline cursor-pointer" x-show="cart.length > 0">
                            Clear All
                        </button>
                    </div>
                </div>

                <!-- Order Details & Inputs -->
                <div class="pt-3 space-y-3">
                    
                    <!-- Order Type Selector (Exact SME Style) -->
                    <div>
                        <div class="grid grid-cols-3 gap-1 bg-slate-100 p-1 rounded-lg border border-slate-300">
                            <button type="button" @click="orderType = 'Takeaway'" 
                                    :class="orderType === 'Takeaway' ? 'bg-slate-900 text-white font-extrabold shadow-2xs' : 'text-slate-700 font-bold hover:text-slate-950'" 
                                    class="py-1.5 rounded-md text-xs transition cursor-pointer text-center">
                                🛍️ Takeaway
                            </button>
                            <button type="button" @click="orderType = 'Dine-in'" 
                                    :class="orderType === 'Dine-in' ? 'bg-slate-900 text-white font-extrabold shadow-2xs' : 'text-slate-700 font-bold hover:text-slate-950'" 
                                    class="py-1.5 rounded-md text-xs transition cursor-pointer text-center">
                                🍽️ Dine-in
                            </button>
                            <button type="button" @click="orderType = 'Delivery'" 
                                    :class="orderType === 'Delivery' ? 'bg-slate-900 text-white font-extrabold shadow-2xs' : 'text-slate-700 font-bold hover:text-slate-950'" 
                                    class="py-1.5 rounded-md text-xs transition cursor-pointer text-center">
                                🛵 Delivery
                            </button>
                        </div>
                    </div>

                    <!-- Customer / Token # with Inline Auto Generator -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700">Customer / Token #</label>
                            <button type="button" @click="generateQuickToken()" class="text-xs text-amber-700 hover:text-amber-900 font-bold hover:underline cursor-pointer flex items-center gap-0.5">
                                <span>⚡ Auto Token</span>
                            </button>
                        </div>
                        <input type="text" x-model="customerName" placeholder="e.g. Token #101 or Customer Name" 
                               class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg text-xs font-bold text-slate-950 py-2 px-3 outline-none transition placeholder:text-slate-400">
                    </div>

                    <!-- Customer Phone Number (Optional) -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Mobile (Optional)</label>
                        <input type="tel" x-model="customerPhone" placeholder="10-digit mobile number" 
                               class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg text-xs font-mono font-medium text-slate-950 py-2 px-3 outline-none transition placeholder:text-slate-400">
                    </div>

                    <!-- Cooking Notes & Presets -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Cooking Instructions</label>
                        <div class="flex flex-wrap gap-1.5 text-[10px]">
                            <button type="button" @click="appendPresetNote('📦 Takeaway')" class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 font-bold transition cursor-pointer">📦 Takeaway</button>
                            <button type="button" @click="appendPresetNote('🌶️ Spicy')" class="px-2.5 py-1 rounded-md bg-rose-50 hover:bg-rose-100 border border-rose-300 text-rose-800 font-bold transition cursor-pointer">🌶️ Spicy</button>
                            <button type="button" @click="appendPresetNote('🚫 Less Sugar')" class="px-2.5 py-1 rounded-md bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-900 font-bold transition cursor-pointer">🚫 Less Sugar</button>
                            <button type="button" @click="appendPresetNote('🧊 Extra Ice')" class="px-2.5 py-1 rounded-md bg-sky-50 hover:bg-sky-100 border border-sky-300 text-sky-800 font-bold transition cursor-pointer">🧊 Extra Ice</button>
                            <button type="button" @click="appendPresetNote('🧄 Jain')" class="px-2.5 py-1 rounded-md bg-purple-50 hover:bg-purple-100 border border-purple-300 text-purple-800 font-bold transition cursor-pointer">🧄 Jain</button>
                        </div>
                        <input type="text" x-model="cookingNotes" placeholder="e.g. Less spicy, extra sauce..." 
                               class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg text-xs font-medium text-slate-950 py-2 px-3 outline-none transition placeholder:text-slate-400">
                    </div>
                </div>

                <!-- Cart Items Scroll List -->
                <div class="space-y-2 max-h-[220px] overflow-y-auto min-h-[120px] my-3 pr-1 divide-y divide-slate-100">
                    
                    <!-- Clean Compact Empty State -->
                    <template x-if="cart.length === 0">
                        <div class="p-6 border-2 border-dashed border-slate-300 rounded-xl text-center space-y-1 bg-slate-50/60">
                            <div class="text-2xl">🛒</div>
                            <p class="font-extrabold text-xs text-slate-800">Ticket is empty</p>
                            <p class="text-[11px] text-slate-500">Click any dish on the left menu to add items.</p>
                        </div>
                    </template>

                    <!-- Cart Item Rows -->
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="pt-2.5 flex items-center justify-between gap-2 text-xs">
                            <div class="flex-1 pr-1 min-w-0">
                                <div class="font-extrabold text-slate-950 truncate" x-text="item.name"></div>
                                <div class="text-[11px] text-slate-600 font-medium mt-0.5">
                                    ₹<span x-text="item.price.toFixed(2)" class="font-mono"></span> × <span x-text="item.qty" class="font-bold"></span>
                                    <span class="text-amber-700 font-mono font-black ml-1">= ₹<span x-text="(item.price * item.qty).toFixed(2)"></span></span>
                                </div>
                            </div>
                            
                            <!-- Stepper Controls & Delete -->
                            <div class="flex items-center gap-1.5 shrink-0">
                                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg border border-slate-300">
                                    <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded bg-white hover:bg-slate-200 font-black text-slate-800 flex items-center justify-center text-xs shadow-2xs cursor-pointer">-</button>
                                    <span class="w-6 text-center font-mono font-black text-xs text-slate-950" x-text="item.qty"></span>
                                    <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded bg-white hover:bg-slate-200 font-black text-slate-800 flex items-center justify-center text-xs shadow-2xs cursor-pointer">+</button>
                                </div>
                                <button type="button" @click="removeItem(index)" class="w-6 h-6 text-slate-400 hover:text-rose-700 font-bold flex items-center justify-center text-xs cursor-pointer" title="Remove item">✕</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Financial Breakdown & Total / Actions Dock -->
            <div class="space-y-3 pt-2">
                
                <!-- Financial Subtotals -->
                <div class="space-y-1 text-xs text-slate-600 px-1">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-slate-600">Subtotal</span>
                        <span class="font-mono font-extrabold text-slate-950">₹<span x-text="cartSubtotal.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between items-center text-[11px] text-slate-500" x-show="cgstPercent > 0">
                        <span>CGST (<span x-text="cgstPercent"></span>%)</span>
                        <span class="font-mono font-bold">₹<span x-text="cartCgst.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between items-center text-[11px] text-slate-500" x-show="sgstPercent > 0">
                        <span>SGST (<span x-text="sgstPercent"></span>%)</span>
                        <span class="font-mono font-bold">₹<span x-text="cartSgst.toFixed(2)">0.00</span></span>
                    </div>
                </div>

                <!-- Total Payable Box (High Contrast Slate-900 Card) -->
                <div class="bg-slate-900 text-white rounded-xl p-4 shadow-sm border border-slate-800 flex items-center justify-between">
                    <div class="space-y-0.5">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Payable</span>
                        <p class="text-xs text-slate-400 font-medium" x-text="cart.length > 0 ? (cartTotalQty + ' items in ticket') : 'No items added'"></p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl sm:text-3xl font-black font-mono text-amber-400 tracking-tight">
                            ₹<span x-text="cartTotal.toFixed(2)">0.00</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium" x-show="cgstPercent > 0 || sgstPercent > 0">Taxes included</span>
                    </div>
                </div>

                <!-- Action Button Suite (Exact Style of Organization & Employee Pages) -->
                <div class="space-y-2">
                    <!-- Primary Full-Width Action: Pay & Print -->
                    <button type="button" @click="openSettleModal()" :disabled="cart.length === 0 || loading" 
                            :class="cart.length === 0 
                                ? 'bg-slate-100 text-slate-400 border border-slate-300 cursor-not-allowed shadow-none' 
                                : 'bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold shadow-xs cursor-pointer'"
                            class="w-full py-3 px-4 rounded-lg text-xs sm:text-sm transition flex items-center justify-between">
                        
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span x-show="!loading" x-text="cart.length === 0 ? 'Pay & Print (Add Items)' : 'Pay & Print Receipt'"></span>
                            <span x-show="loading">Processing...</span>
                        </div>

                        <template x-if="cart.length > 0">
                            <span class="bg-slate-950/15 text-slate-950 px-2.5 py-0.5 rounded font-mono font-extrabold text-xs">
                                ₹<span x-text="cartTotal.toFixed(2)"></span> &rarr;
                            </span>
                        </template>
                    </button>

                    <!-- Secondary Row: Hold Token & Exact Cash -->
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="saveHoldOrder()" :disabled="cart.length === 0 || loading" 
                                :class="cart.length === 0 
                                    ? 'bg-slate-50 text-slate-300 border-slate-200 cursor-not-allowed shadow-none' 
                                    : 'bg-slate-100 hover:bg-slate-200 text-slate-800 border-slate-300 shadow-2xs cursor-pointer'"
                                class="py-2.5 px-3 rounded-lg text-xs font-bold transition border flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Hold Token</span>
                            <span class="text-[10px] text-slate-500 font-mono font-bold">(F4)</span>
                        </button>

                        <button type="button" @click="quickExactCashSettle()" :disabled="cart.length === 0 || loading" 
                                :class="cart.length === 0 
                                    ? 'bg-slate-50 text-slate-300 border-slate-200 cursor-not-allowed shadow-none' 
                                    : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-950 border-emerald-300 shadow-2xs cursor-pointer'"
                                class="py-2.5 px-3 rounded-lg text-xs font-bold transition border flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Exact Cash</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: LIVE ACTIVE TOKENS QUEUE            -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'current'" class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 space-y-5" style="display: none;">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-950 tracking-tight flex items-center gap-2">
                    <span>Live Active Tokens Queue</span>
                    <span class="text-xs font-mono font-bold bg-amber-100 text-amber-950 px-2.5 py-0.5 rounded-md border border-amber-300" x-text="activeOrders.length + ' Pending'"></span>
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">Hold orders waiting for billing, kitchen delivery, or item additions</p>
            </div>
            
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="text" x-model="tokenSearchQuery" placeholder="Search token # or customer..." 
                       class="text-xs bg-white border border-slate-300 rounded-lg py-2 px-3 font-medium text-slate-950 focus:border-amber-500 outline-none w-full sm:w-56 shadow-2xs">
                <button type="button" @click="fetchActiveOrders()" class="text-xs font-extrabold text-slate-900 hover:text-slate-950 flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-lg border border-slate-300 transition cursor-pointer shrink-0 shadow-2xs">
                    <span>🔄 Refresh</span>
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            <!-- Quick Add Card -->
            <div @click="resetCartForm(); activeTab = 'new'" 
                 class="border-2 border-dashed border-slate-300 hover:border-amber-500 bg-slate-50/60 hover:bg-amber-50/30 rounded-xl p-5 flex flex-col items-center justify-center text-slate-600 hover:text-amber-800 cursor-pointer transition min-h-[190px] group shadow-2xs">
                <div class="w-12 h-12 rounded-lg bg-white border border-slate-300 group-hover:border-amber-400 group-hover:bg-amber-100 flex items-center justify-center mb-3 shadow-2xs transition">
                    <svg class="w-6 h-6 text-slate-400 group-hover:text-amber-700 transition" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="font-extrabold text-sm text-slate-950 tracking-wide">Create New Token</span>
                <span class="text-[11px] text-slate-500 mt-1 font-medium">Start fresh counter order</span>
            </div>

            <!-- Active Token Cards -->
            <template x-for="order in filteredActiveOrders" :key="order.id">
                <div class="border border-slate-200/90 rounded-xl p-4.5 flex flex-col justify-between hover:border-amber-400 hover:shadow-xs transition bg-white space-y-3.5 shadow-2xs">
                    <div>
                        <div class="flex justify-between items-start mb-2.5">
                            <div>
                                <span class="font-extrabold text-sm text-slate-950 truncate block" x-text="order.customer_name"></span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] text-slate-500 font-mono" x-text="formatTime(order.created_at)"></span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-slate-100 text-slate-700 border border-slate-200 uppercase" x-text="order.order_type || 'Takeaway'"></span>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-black bg-amber-500 text-slate-950 px-2.5 py-1 rounded-md shadow-2xs" x-text="order.order_number"></span>
                        </div>
                        
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 space-y-1.5 text-xs">
                            <div class="flex justify-between font-bold text-slate-700">
                                <span>Items Summary</span>
                                <span class="text-slate-500 font-mono" x-text="order.items.length + ' items'"></span>
                            </div>
                            <div class="text-[11px] text-slate-600 truncate" x-text="order.items.map(i => i.quantity + 'x ' + i.name_snapshot).join(', ')"></div>
                            
                            <div class="flex justify-between items-center pt-2 border-t border-slate-200 font-black">
                                <span class="text-slate-600 text-xs">Total</span>
                                <span class="text-slate-950 font-mono text-sm">₹<span x-text="parseFloat(order.total).toFixed(2)"></span></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5 pt-1">
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="editOrder(order)" class="py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer shadow-2xs">
                                ✏️ Edit
                            </button>
                            <button type="button" @click="openSettleModalForOrder(order)" class="py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-lg text-xs font-extrabold transition flex items-center justify-center gap-1 shadow-xs cursor-pointer">
                                💳 Settle Bill
                            </button>
                        </div>
                        <div class="flex items-center justify-between px-1 pt-1 text-[11px]">
                            <a :href="'/organization/menu/pos/orders/' + order.id + '/print-kot'" target="_blank" 
                               class="text-slate-600 hover:text-slate-950 font-bold hover:underline flex items-center gap-1">
                                🖨️ KOT Slip
                            </a>
                            <button type="button" @click="cancelOrder(order)" 
                                    class="text-rose-700 hover:text-rose-900 font-bold hover:underline cursor-pointer">
                                ✕ Void Token
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 3: COMPLETED ORDERS / SETTLED BILLS    -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'completed'" class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 space-y-5" style="display: none;">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-950 tracking-tight flex items-center gap-2">
                    <span>Today's Settled Counter Bills</span>
                    <span class="text-xs font-mono font-bold bg-emerald-100 text-emerald-950 px-2.5 py-0.5 rounded-md border border-emerald-300" x-text="completedOrders.length + ' Settled'"></span>
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">Historical log of settled counter orders and customer receipts</p>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="text" x-model="completedSearchQuery" placeholder="Search order # or customer..." 
                       class="text-xs bg-white border border-slate-300 rounded-lg py-2 px-3 font-medium text-slate-950 focus:border-amber-500 outline-none w-full sm:w-56 shadow-2xs">
                <button type="button" @click="fetchCompletedOrders()" class="text-xs font-extrabold text-slate-900 hover:text-slate-950 flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-lg border border-slate-300 transition cursor-pointer shrink-0 shadow-2xs">
                    <span>🔄 Refresh</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-600 font-extrabold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-lg">Order #</th>
                        <th class="px-4 py-3">Customer / Token</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Items</th>
                        <th class="px-4 py-3">Grand Total</th>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3 rounded-tr-lg text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    <template x-if="filteredCompletedOrders.length === 0">
                        <tr><td colspan="7" class="text-center py-12 text-slate-500 font-medium">No completed orders found today.</td></tr>
                    </template>
                    <template x-for="order in filteredCompletedOrders" :key="order.id">
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-extrabold text-slate-950" x-text="order.order_number"></td>
                            <td class="px-4 py-3 font-bold text-slate-950">
                                <span x-text="order.customer_name"></span>
                                <span x-show="order.customer_phone" class="block text-[10px] text-slate-500 font-mono font-normal" x-text="order.customer_phone"></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-slate-100 text-slate-800 border border-slate-300 px-2 py-0.5 rounded text-[10px] font-bold uppercase" x-text="order.order_type || 'Takeaway'"></span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="bg-slate-100 text-slate-800 border border-slate-200 px-2 py-0.5 rounded font-mono text-[11px]" x-text="order.items.length + ' items'"></span>
                            </td>
                            <td class="px-4 py-3 font-mono font-extrabold text-slate-950 text-sm">₹<span x-text="parseFloat(order.total).toFixed(2)"></span></td>
                            <td class="px-4 py-3 text-slate-500 font-mono text-[11px]" x-text="formatTime(order.created_at)"></td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <template x-if="order.invoice_id">
                                        <a :href="'/organization/invoices/' + order.invoice_id" target="_blank" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 rounded-lg text-xs font-bold transition shadow-2xs">
                                            👁️ Invoice
                                        </a>
                                    </template>
                                    <a :href="'/organization/menu/pos/orders/' + order.id + '/print-receipt'" target="_blank" 
                                       class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-extrabold transition shadow-2xs">
                                        🖨️ Print Bill
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SETTLEMENT & CHECKOUT MODAL                -->
    <!-- ========================================== -->
    <div x-show="settleModalOpen" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl space-y-5 border border-slate-200" @click.away="settleModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="font-extrabold text-base text-slate-950">Checkout & Settle Bill</h3>
                    <p class="text-xs text-slate-500 font-medium">Select payment method & calculate change</p>
                </div>
                <button type="button" @click="settleModalOpen = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-sm transition cursor-pointer">✕</button>
            </div>

            <!-- Grand Total Box -->
            <div class="bg-slate-900 text-white rounded-xl p-4 text-center border border-slate-800 shadow-inner">
                <div class="text-[11px] text-amber-400 uppercase tracking-wider font-extrabold">Final Amount Payable</div>
                <div class="text-3xl font-black font-mono text-white mt-1">₹<span x-text="modalGrandTotal.toFixed(2)"></span></div>
            </div>

            <div class="space-y-4">
                <!-- Payment Method Cards -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Payment Method</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="paymentMethod = 'Cash'" 
                                :class="paymentMethod === 'Cash' ? 'border-2 border-amber-500 bg-amber-50 text-slate-950 font-extrabold shadow-2xs' : 'border border-slate-300 text-slate-800 hover:bg-slate-50 font-bold'" 
                                class="py-2.5 px-2 rounded-lg text-xs text-center transition flex flex-col items-center gap-1 cursor-pointer">
                            <span class="text-lg">💵</span>
                            <span>Cash</span>
                        </button>
                        <button type="button" @click="paymentMethod = 'UPI'" 
                                :class="paymentMethod === 'UPI' ? 'border-2 border-amber-500 bg-amber-50 text-slate-950 font-extrabold shadow-2xs' : 'border border-slate-300 text-slate-800 hover:bg-slate-50 font-bold'" 
                                class="py-2.5 px-2 rounded-lg text-xs text-center transition flex flex-col items-center gap-1 cursor-pointer">
                            <span class="text-lg">📱</span>
                            <span>UPI / QR</span>
                        </button>
                        <button type="button" @click="paymentMethod = 'Card'" 
                                :class="paymentMethod === 'Card' ? 'border-2 border-amber-500 bg-amber-50 text-slate-950 font-extrabold shadow-2xs' : 'border border-slate-300 text-slate-800 hover:bg-slate-50 font-bold'" 
                                class="py-2.5 px-2 rounded-lg text-xs text-center transition flex flex-col items-center gap-1 cursor-pointer">
                            <span class="text-lg">💳</span>
                            <span>Card POS</span>
                        </button>
                    </div>
                </div>

                <!-- Cash Tender & Change Calculator -->
                <div x-show="paymentMethod === 'Cash'" class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2.5">
                    <label class="block text-xs font-bold text-slate-700">Cash Tendered (Customer Paid)</label>
                    
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-black text-slate-500">₹</span>
                        <input type="number" step="1" x-model.number="cashTendered" placeholder="e.g. 500" 
                               class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg text-sm font-mono font-bold py-1.5 px-3 outline-none">
                    </div>

                    <!-- Preset Cash Buttons -->
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <button type="button" @click="cashTendered = modalGrandTotal" class="px-2.5 py-1 rounded-md bg-white border border-slate-300 text-[11px] font-bold text-slate-800 hover:bg-slate-100 cursor-pointer shadow-2xs">Exact</button>
                        <button type="button" @click="cashTendered = 100" class="px-2.5 py-1 rounded-md bg-white border border-slate-300 text-[11px] font-bold text-slate-800 hover:bg-slate-100 cursor-pointer shadow-2xs">₹100</button>
                        <button type="button" @click="cashTendered = 200" class="px-2.5 py-1 rounded-md bg-white border border-slate-300 text-[11px] font-bold text-slate-800 hover:bg-slate-100 cursor-pointer shadow-2xs">₹200</button>
                        <button type="button" @click="cashTendered = 500" class="px-2.5 py-1 rounded-md bg-white border border-slate-300 text-[11px] font-bold text-slate-800 hover:bg-slate-100 cursor-pointer shadow-2xs">₹500</button>
                        <button type="button" @click="cashTendered = 2000" class="px-2.5 py-1 rounded-md bg-white border border-slate-300 text-[11px] font-bold text-slate-800 hover:bg-slate-100 cursor-pointer shadow-2xs">₹2000</button>
                    </div>

                    <div class="flex justify-between items-center pt-2.5 border-t border-slate-200 text-xs font-bold">
                        <span class="text-slate-600">Change Return:</span>
                        <span :class="cashChange >= 0 ? 'text-emerald-700 text-sm font-mono font-black' : 'text-rose-600 font-mono'" 
                              x-text="'₹' + cashChange.toFixed(2)">₹0.00</span>
                    </div>
                </div>

                <!-- Discount Amount with Quick Presets -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700">Discount Amount (₹)</label>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="applyPercentDiscount(5)" class="text-[10px] px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 font-bold cursor-pointer">5%</button>
                            <button type="button" @click="applyPercentDiscount(10)" class="text-[10px] px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 font-bold cursor-pointer">10%</button>
                            <button type="button" @click="discount = 0; calculateModalTotal()" class="text-[10px] px-2 py-0.5 rounded bg-rose-50 border border-rose-200 text-rose-800 font-bold cursor-pointer">Clear</button>
                        </div>
                    </div>
                    <input type="number" min="0" step="1" x-model.number="discount" @input="calculateModalTotal()" 
                           placeholder="0.00" class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg text-xs font-mono font-bold py-2 px-3 outline-none">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" @click="confirmSettle()" :disabled="loading" 
                        class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold rounded-lg text-xs sm:text-sm transition shadow-xs flex justify-center items-center gap-2 cursor-pointer">
                    <span x-show="!loading">✓ Print & Settle Bill (Enter)</span>
                    <span x-show="loading">Processing...</span>
                </button>
                <button type="button" @click="settleModalOpen = false" class="py-3 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 rounded-lg text-xs font-bold cursor-pointer">Cancel</button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- KEYBOARD SHORTCUTS MODAL                   -->
    <!-- ========================================== -->
    <div x-show="shortcutsModalOpen" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-2xl space-y-4 border border-slate-200" @click.away="shortcutsModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h3 class="font-extrabold text-sm text-slate-950 flex items-center gap-2">
                    <span>⌨️ POS Hotkeys & Shortcuts</span>
                </h3>
                <button type="button" @click="shortcutsModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer">✕</button>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-600 font-medium">New Order Tab</span>
                    <kbd class="px-2 py-0.5 bg-slate-100 rounded border border-slate-300 font-mono font-bold text-slate-800">F1</kbd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-600 font-medium">Active Tokens Tab</span>
                    <kbd class="px-2 py-0.5 bg-slate-100 rounded border border-slate-300 font-mono font-bold text-slate-800">F2</kbd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-600 font-medium">Settled Bills Tab</span>
                    <kbd class="px-2 py-0.5 bg-slate-100 rounded border border-slate-300 font-mono font-bold text-slate-800">F3</kbd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-600 font-medium">Hold Active Token</span>
                    <kbd class="px-2 py-0.5 bg-slate-100 rounded border border-slate-300 font-mono font-bold text-slate-800">F4</kbd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-600 font-medium">Pay & Print Bill</span>
                    <kbd class="px-2 py-0.5 bg-slate-100 rounded border border-slate-300 font-mono font-bold text-slate-800">Ctrl + Enter</kbd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-600 font-medium">Focus Menu Search</span>
                    <kbd class="px-2 py-0.5 bg-slate-100 rounded border border-slate-300 font-mono font-bold text-slate-800">/</kbd>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-slate-600 font-medium">Close / Cancel</span>
                    <kbd class="px-2 py-0.5 bg-slate-100 rounded border border-slate-300 font-mono font-bold text-slate-800">Esc</kbd>
                </div>
            </div>

            <button type="button" @click="shortcutsModalOpen = false" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-extrabold cursor-pointer">
                Got it
            </button>
        </div>
    </div>

</div>

<script>
function counterBilling() {
    return {
        activeTab: 'new',
        selectedCategory: 'all',
        dietFilter: 'all',
        searchQuery: '',
        tokenSearchQuery: '',
        completedSearchQuery: '',
        
        // Clock
        liveClock: '',

        // Tax Rates from Org
        cgstPercent: {{ (float)($org->cgst_percent ?? 0) }},
        sgstPercent: {{ (float)($org->sgst_percent ?? 0) }},

        // Cart / Order State
        editingOrderId: null,
        editingOrderNumber: '',
        orderType: 'Takeaway',
        customerName: '',
        customerPhone: '',
        cookingNotes: '',
        cart: [],
        
        // Modal State
        settleModalOpen: false,
        shortcutsModalOpen: false,
        paymentMethod: 'Cash',
        discount: 0,
        cashTendered: 0,
        orderToSettle: null,
        modalGrandTotal: 0,
        
        // Data
        activeOrders: [],
        completedOrders: [],
        loading: false,

        init() {
            this.updateLiveClock();
            setInterval(() => this.updateLiveClock(), 1000);

            this.fetchActiveOrders();
            this.fetchCompletedOrders();
            
            // Auto refresh active queue every 20s
            setInterval(() => this.fetchActiveOrders(), 20000);
        },

        updateLiveClock() {
            const now = new Date();
            this.liveClock = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        },

        handleGlobalKeydown(e) {
            const isTyping = ['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName);

            if (e.key === 'Escape') {
                if (this.settleModalOpen) { this.settleModalOpen = false; return; }
                if (this.shortcutsModalOpen) { this.shortcutsModalOpen = false; return; }
                if (this.searchQuery) { this.searchQuery = ''; return; }
            }

            if (!isTyping) {
                if (e.key === 'F1') { e.preventDefault(); this.activeTab = 'new'; }
                if (e.key === 'F2') { e.preventDefault(); this.activeTab = 'current'; }
                if (e.key === 'F3') { e.preventDefault(); this.activeTab = 'completed'; }
                if (e.key === 'F4') { e.preventDefault(); this.saveHoldOrder(); }
                if (e.key === '/') { 
                    e.preventDefault(); 
                    if (this.$refs.searchInput) this.$refs.searchInput.focus(); 
                }
            }

            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                if (this.settleModalOpen) {
                    this.confirmSettle();
                } else if (this.cart.length > 0) {
                    this.openSettleModal();
                }
            }
        },

        toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(err => {});
                }
            }
        },

        formatTime(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        isItemVisible(categoryId, itemName, catName, isVeg) {
            const matchCategory = this.selectedCategory === 'all' || this.selectedCategory === categoryId;
            const q = this.searchQuery.toLowerCase();
            const matchSearch = !q || itemName.includes(q) || catName.includes(q);
            const matchDiet = this.dietFilter === 'all' || 
                             (this.dietFilter === 'veg' && isVeg) || 
                             (this.dietFilter === 'non_veg' && !isVeg);
            return matchCategory && matchSearch && matchDiet;
        },

        getItemCartQty(itemId) {
            const item = this.cart.find(i => i.id === itemId);
            return item ? item.qty : 0;
        },

        get cartTotalQty() {
            return this.cart.reduce((sum, item) => sum + item.qty, 0);
        },

        get cartSubtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        get cartCgst() {
            return (this.cartSubtotal * this.cgstPercent) / 100;
        },

        get cartSgst() {
            return (this.cartSubtotal * this.sgstPercent) / 100;
        },

        get cartTotal() {
            return this.cartSubtotal + this.cartCgst + this.cartSgst;
        },

        get cashChange() {
            if (!this.cashTendered || this.cashTendered < this.modalGrandTotal) return 0;
            return this.cashTendered - this.modalGrandTotal;
        },

        get totalSettledSales() {
            return this.completedOrders.reduce((sum, order) => sum + parseFloat(order.total || 0), 0);
        },

        get averageOrderValue() {
            if (this.completedOrders.length === 0) return 0;
            return this.totalSettledSales / this.completedOrders.length;
        },

        get filteredActiveOrders() {
            if (!this.tokenSearchQuery) return this.activeOrders;
            const q = this.tokenSearchQuery.toLowerCase();
            return this.activeOrders.filter(o => 
                (o.order_number && o.order_number.toLowerCase().includes(q)) ||
                (o.customer_name && o.customer_name.toLowerCase().includes(q))
            );
        },

        get filteredCompletedOrders() {
            if (!this.completedSearchQuery) return this.completedOrders;
            const q = this.completedSearchQuery.toLowerCase();
            return this.completedOrders.filter(o => 
                (o.order_number && o.order_number.toLowerCase().includes(q)) ||
                (o.customer_name && o.customer_name.toLowerCase().includes(q))
            );
        },

        addToCart(item) {
            let existing = this.cart.find(i => i.id === item.id);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({
                    id: item.id,
                    name: item.name,
                    price: parseFloat(item.price),
                    qty: 1
                });
            }
        },

        updateQty(index, change) {
            this.cart[index].qty += change;
            if (this.cart[index].qty <= 0) {
                this.cart.splice(index, 1);
            }
        },

        removeItem(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            if (confirm('Clear current items in cart?')) {
                this.resetCartForm();
            }
        },

        cancelEditOrder() {
            this.resetCartForm();
        },
        
        resetCartForm() {
            this.cart = [];
            this.customerName = '';
            this.customerPhone = '';
            this.cookingNotes = '';
            this.editingOrderId = null;
            this.editingOrderNumber = '';
            this.orderType = 'Takeaway';
        },

        generateQuickToken() {
            const tokenNum = Math.floor(100 + Math.random() * 900);
            this.customerName = 'Token #' + tokenNum;
        },

        appendPresetNote(tag) {
            if (!this.cookingNotes) {
                this.cookingNotes = tag;
            } else if (!this.cookingNotes.includes(tag)) {
                this.cookingNotes += ' | ' + tag;
            }
        },

        fetchActiveOrders() {
            fetch('{{ route("organization.menu.counter.orders.active") }}')
                .then(res => res.json())
                .then(data => { this.activeOrders = data; });
        },

        fetchCompletedOrders() {
            fetch('{{ route("organization.menu.counter.orders.completed") }}')
                .then(res => res.json())
                .then(data => { this.completedOrders = data; });
        },

        saveHoldOrder() {
            if (this.cart.length === 0) return;
            this.loading = true;

            const payload = {
                order_id: this.editingOrderId,
                customer_name: this.customerName || ('Token ' + Math.floor(100 + Math.random() * 900)),
                customer_phone: this.customerPhone || null,
                order_type: this.orderType,
                notes: this.cookingNotes,
                items: this.cart.map(i => ({ menu_item_id: i.id, quantity: i.qty }))
            };

            fetch('{{ route("organization.menu.counter.orders.save") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    this.resetCartForm();
                    this.fetchActiveOrders();
                    this.activeTab = 'current';
                } else {
                    alert(data.message || 'Error saving token order.');
                }
            }).catch(err => { this.loading = false; });
        },

        editOrder(order) {
            this.editingOrderId = order.id;
            this.editingOrderNumber = order.order_number;
            this.customerName = order.customer_name;
            this.customerPhone = order.customer_phone || '';
            this.orderType = order.order_type || 'Takeaway';
            this.cookingNotes = order.special_notes || '';
            this.cart = order.items.map(i => ({
                id: i.menu_item_id,
                name: i.name_snapshot,
                price: parseFloat(i.price_snapshot),
                qty: i.quantity
            }));
            this.activeTab = 'new';
        },

        cancelOrder(order) {
            if (!confirm(`Are you sure you want to void and cancel order ${order.order_number}?`)) return;
            this.loading = true;

            fetch(`/organization/menu/counter/orders/${order.id}/cancel`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    this.fetchActiveOrders();
                } else {
                    alert(data.message);
                }
            }).catch(err => { this.loading = false; });
        },

        openSettleModal() {
            if (this.cart.length === 0) return;
            this.orderToSettle = 'current_cart';
            this.discount = 0;
            this.modalGrandTotal = this.cartTotal;
            this.cashTendered = Math.ceil(this.modalGrandTotal);
            this.settleModalOpen = true;
        },

        openSettleModalForOrder(order) {
            this.orderToSettle = order;
            this.discount = 0;
            this.modalGrandTotal = parseFloat(order.total);
            this.cashTendered = Math.ceil(this.modalGrandTotal);
            this.settleModalOpen = true;
        },

        calculateModalTotal() {
            let base = this.orderToSettle === 'current_cart' ? this.cartTotal : parseFloat(this.orderToSettle.total);
            this.modalGrandTotal = Math.max(0, base - (this.discount || 0));
        },

        applyPercentDiscount(percent) {
            let base = this.orderToSettle === 'current_cart' ? this.cartTotal : parseFloat(this.orderToSettle.total);
            this.discount = Math.round((base * percent) / 100);
            this.calculateModalTotal();
        },

        quickExactCashSettle() {
            if (this.cart.length === 0) return;
            this.orderToSettle = 'current_cart';
            this.paymentMethod = 'Cash';
            this.discount = 0;
            this.modalGrandTotal = this.cartTotal;
            this.cashTendered = this.modalGrandTotal;
            this.confirmSettle();
        },

        confirmSettle() {
            if (this.orderToSettle === 'current_cart') {
                this.loading = true;
                const payload = {
                    order_id: this.editingOrderId,
                    customer_name: this.customerName || ('Token ' + Math.floor(100 + Math.random() * 900)),
                    customer_phone: this.customerPhone || null,
                    order_type: this.orderType,
                    notes: this.cookingNotes,
                    items: this.cart.map(i => ({ menu_item_id: i.id, quantity: i.qty }))
                };

                fetch('{{ route("organization.menu.counter.orders.save") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.executeSettle(data.order.id);
                    } else {
                        this.loading = false; 
                        alert(data.message);
                    }
                }).catch(err => { this.loading = false; });
            } else {
                this.executeSettle(this.orderToSettle.id);
            }
        },

        executeSettle(orderId) {
            this.loading = true;
            fetch(`/organization/menu/counter/orders/${orderId}/settle`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ payment_method: this.paymentMethod, discount: this.discount })
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    this.settleModalOpen = false;
                    this.resetCartForm();
                    this.fetchActiveOrders();
                    this.fetchCompletedOrders();
                    window.open(data.print_receipt_url, '_blank');
                    this.activeTab = 'completed';
                } else {
                    alert(data.message);
                }
            }).catch(err => { this.loading = false; });
        }
    }
}
</script>
@endsection

@extends('layouts.sme')

@section('title', 'Restro Waiter POS & Floor Ordering')

@section('content')
<div class="space-y-5" x-data="waiterPos()" x-init="init()">
    
    <!-- Top Header & Floor Status -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white px-5 py-4 rounded-3xl border border-slate-200/90 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 font-black flex items-center justify-center text-xl shrink-0 border border-amber-500/20">
                🍽️
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base font-black text-slate-950 tracking-tight">Waiter POS & Floor Billing</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-950 border border-emerald-300">
                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Floor Live
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Take table orders, dispatch KOT to kitchen & settle customer bills.</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Table Occupancy Pills -->
            <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700">
                <span class="flex items-center gap-1.5 text-emerald-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span x-text="vacantTablesCount" class="font-mono font-black"></span> Vacant
                </span>
                <span class="text-slate-300">|</span>
                <span class="flex items-center gap-1.5 text-amber-700">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span x-text="occupiedTablesCount" class="font-mono font-black"></span> Occupied
                </span>
            </div>

            <!-- Quick Links -->
            <a href="{{ route('organization.menu.kitchen.index') }}" target="_blank"
               class="px-4 py-2 bg-slate-950 hover:bg-slate-900 text-white font-black rounded-xl text-xs transition flex items-center gap-1.5 shadow-2xs whitespace-nowrap">
                <span>👨‍🍳 Kitchen KOT</span>
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
            <a href="{{ route('organization.menu.tables.index') }}" 
               class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-800 rounded-xl text-xs font-bold transition border border-slate-200/90 whitespace-nowrap shadow-2xs">
                🪑 Floor Tables
            </a>
        </div>
    </div>

    <!-- Main POS Workspace Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        <!-- Left Column: Floor Tables & Menu Grid (8 Cols) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-5">
            
            <!-- Table Selection Card -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-2xs space-y-3.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xs font-black text-slate-950 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🪑 Select Table</span>
                            <span class="text-slate-400 font-mono font-bold">({{ count($tables) }})</span>
                        </h2>
                        
                        <!-- Table Status Filters -->
                        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/60">
                            <button type="button" @click="tableFilter = 'all'" 
                                    :class="tableFilter === 'all' ? 'bg-slate-950 text-white font-black shadow-2xs' : 'text-slate-600 font-bold hover:text-slate-900'" 
                                    class="px-3 py-1 rounded-lg text-xs transition cursor-pointer">
                                All
                            </button>
                            <button type="button" @click="tableFilter = 'vacant'" 
                                    :class="tableFilter === 'vacant' ? 'bg-emerald-600 text-white font-black shadow-2xs' : 'text-slate-600 font-bold hover:text-slate-900'" 
                                    class="px-3 py-1 rounded-lg text-xs transition cursor-pointer">
                                Vacant
                            </button>
                            <button type="button" @click="tableFilter = 'occupied'" 
                                    :class="tableFilter === 'occupied' ? 'bg-amber-500 text-slate-950 font-black shadow-2xs' : 'text-slate-600 font-bold hover:text-slate-900'" 
                                    class="px-3 py-1 rounded-lg text-xs transition cursor-pointer">
                                Occupied
                            </button>
                        </div>
                    </div>

                    <!-- Order Type Selector Chips -->
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-2xl border border-slate-200 overflow-x-auto shrink-0">
                        <button type="button" @click="setOrderType('Dine-in')" 
                                :class="orderType === 'Dine-in' ? 'bg-slate-950 text-white font-black shadow-xs' : 'text-slate-600 font-bold hover:text-slate-950'" 
                                class="px-3 py-1.5 rounded-xl text-xs transition uppercase tracking-wider whitespace-nowrap cursor-pointer">
                            🪑 Dine-in Table
                        </button>
                        <button type="button" @click="setOrderType('Takeaway')" 
                                :class="orderType === 'Takeaway' ? 'bg-slate-950 text-white font-black shadow-xs' : 'text-slate-600 font-bold hover:text-slate-950'" 
                                class="px-3 py-1.5 rounded-xl text-xs transition uppercase tracking-wider whitespace-nowrap cursor-pointer">
                            🛍️ Parcel / Takeaway
                        </button>
                    </div>
                </div>

                <!-- Tables Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-3 max-h-60 overflow-y-auto p-1 pr-2">
                    <template x-for="t in tables" :key="t.id">
                        <button type="button" 
                            x-show="tableFilter === 'all' || (tableFilter === 'vacant' && !t.is_occupied) || (tableFilter === 'occupied' && t.is_occupied)"
                            @click="selectTable(t)" 
                            :class="[
                                selectedTableId === t.id ? 'ring-2 ring-slate-950 border-slate-950 shadow-md scale-[1.02] bg-white' : '',
                                t.is_occupied ? 'bg-amber-50/70 border-amber-300 text-amber-950 hover:border-amber-400 shadow-2xs' : 'bg-emerald-50/50 border-emerald-300 text-emerald-950 hover:border-emerald-400 shadow-2xs'
                            ]"
                            class="p-3 rounded-2xl border text-left transition-all duration-150 flex flex-col justify-between min-h-[105px] relative group overflow-hidden cursor-pointer">
                            
                            <div class="flex flex-col gap-1 w-full relative">
                                <div class="flex items-center justify-between w-full gap-1.5">
                                    <span class="font-black text-xs sm:text-sm tracking-tight leading-tight truncate min-w-0 text-slate-950" 
                                          x-text="t.name" :title="t.name"></span>
                                    <span class="text-[9px] px-2 py-0.5 rounded-md font-black uppercase tracking-wider shrink-0 shadow-2xs" 
                                          :class="t.is_occupied ? 'bg-amber-500 text-slate-950 border border-amber-600' : 'bg-emerald-100 text-emerald-950 border border-emerald-300'"
                                          x-text="t.is_occupied ? 'Occupied' : 'Free'">
                                    </span>
                                </div>

                                <template x-if="t.is_occupied && t.active_order">
                                    <div class="mt-2 pt-1.5 border-t border-amber-200/90 w-full space-y-0.5">
                                        <div class="text-[11px] text-amber-950 font-mono font-black tracking-tight truncate" x-text="'#' + t.active_order.order_number"></div>
                                        <div class="text-xs font-black text-slate-950 font-mono" x-text="'₹' + Number(t.active_order.total).toFixed(2)"></div>
                                    </div>
                                </template>
                                <template x-if="!(t.is_occupied && t.active_order)">
                                    <div class="mt-2 pt-1.5 border-t border-emerald-200/90 text-[10px] text-emerald-700 font-bold flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Ready</span>
                                    </div>
                                </template>
                            </div>
                        </button>
                    </template>
                    <div x-show="tables.length === 0" class="col-span-full text-center py-6 text-xs text-slate-400 font-bold">
                        No dining tables configured. Click "Floor Tables" to add tables.
                    </div>
                </div>
            </div>

            <!-- Menu Categories & Food Items Section -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-2xs space-y-4">
                
                <!-- Category Tabs & Real-Time Search -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 scrollbar-none">
                        <button type="button" @click="selectedCategory = 'all'" 
                                :class="selectedCategory === 'all' ? 'bg-slate-950 text-amber-400 font-black shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold'" 
                                class="px-3.5 py-1.5 rounded-xl text-xs whitespace-nowrap transition cursor-pointer">
                            All Dishes
                        </button>
                        @foreach($categories as $cat)
                            <button type="button" @click="selectedCategory = {{ $cat->id }}" 
                                    :class="selectedCategory === {{ $cat->id }} ? 'bg-slate-950 text-amber-400 font-black shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold'" 
                                    class="px-3.5 py-1.5 rounded-xl text-xs whitespace-nowrap transition cursor-pointer">
                                {{ $cat->name }} ({{ count($cat->items) }})
                            </button>
                        @endforeach
                    </div>

                    <!-- Search Input -->
                    <div class="w-full sm:w-60 shrink-0 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="searchQuery" placeholder="Search menu dish..." 
                               class="w-full text-xs font-bold border border-slate-300 rounded-xl py-2 pl-9 pr-3.5 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none bg-white text-slate-950 placeholder:text-slate-400">
                    </div>
                </div>

                <!-- Food Items Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-[420px] overflow-y-auto p-1 pr-2">
                    @foreach($categories as $cat)
                        @foreach($cat->items as $item)
                            @php
                                $isNonVeg = preg_match('/chicken|mutton|egg|fish|meat|prawn/i', $item->name . ' ' . ($item->description ?? ''));
                            @endphp
                            <div x-show="(selectedCategory === 'all' || selectedCategory === {{ $cat->id }}) && ('{{ strtolower(addslashes($item->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($cat->name)) }}'.includes(searchQuery.toLowerCase()))"
                                @click="addToTicket({{ json_encode($item) }})"
                                class="bg-white border border-slate-200/90 hover:border-slate-950 rounded-2xl p-3.5 cursor-pointer transition transform active:scale-97 shadow-2xs hover:shadow-md flex flex-col justify-between group relative overflow-hidden">
                                
                                <div>
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider truncate">{{ $cat->name }}</span>
                                        @if($isNonVeg)
                                            <span class="w-2 h-2 rounded-full bg-red-600 shrink-0" title="Non-Veg"></span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0" title="Veg"></span>
                                        @endif
                                    </div>
                                    <div class="text-xs font-black text-slate-950 group-hover:text-amber-600 transition line-clamp-2 leading-snug tracking-tight">
                                        {{ $item->name }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-100">
                                    <span class="text-sm font-black text-slate-950 font-mono">₹{{ number_format($item->price, 2) }}</span>
                                    <span class="w-7 h-7 rounded-xl bg-slate-100 text-slate-900 group-hover:bg-slate-950 group-hover:text-amber-400 flex items-center justify-center font-black text-sm transition shadow-2xs">
                                        +
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Column: Order Ticket / Billing Cart (4 Cols) -->
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-md sticky top-4 space-y-4">
                
                <!-- Ticket Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                            <h3 class="font-black text-base text-slate-950 tracking-tight truncate" 
                                x-text="orderType === 'Dine-in' ? (selectedTableName ? 'Table: ' + selectedTableName : 'Select Floor Table') : 'Takeaway / Parcel Order'"></h3>
                        </div>
                        <div class="text-[11px] text-slate-500 font-mono font-bold mt-0.5 flex items-center gap-1.5 flex-wrap" x-show="activeOrderIds.length > 0">
                            <span>Token:</span>
                            <span x-text="activeOrderNumber" class="font-black text-slate-950 bg-amber-100/80 text-amber-950 px-2 py-0.5 rounded-md border border-amber-200"></span>
                            <span x-show="activeOrderPaymentStatus === 'Paid'" class="font-black text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-md border border-emerald-300 uppercase tracking-wider text-[10px]">
                                ✅ PAID ONLINE
                            </span>
                            <span x-show="activeOrderPaymentStatus !== 'Paid'" class="font-black text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 uppercase tracking-wider text-[10px]">
                                ⏳ PENDING
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" @click="cancelOrder()" 
                                class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded-xl text-xs font-black border border-rose-700 transition flex items-center gap-1.5 shadow-sm cursor-pointer" 
                                x-show="activeOrderIds.length > 0"
                                title="Void or Cancel this active order">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Cancel Order</span>
                        </button>
                        <button type="button" @click="clearTicket()" 
                                class="px-2.5 py-1 text-xs text-slate-500 hover:text-rose-600 font-bold cursor-pointer" 
                                x-show="cart.length > 0 && !activeOrderIds.length">
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Customer Details Input -->
                <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-3 rounded-2xl border border-slate-200/80">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Guest Name</label>
                        <input type="text" x-model="customerName" placeholder="e.g. Rahul" 
                               class="w-full border border-slate-300 rounded-xl text-xs font-bold py-1.5 px-2.5 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white text-slate-950 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Mobile Phone</label>
                        <input type="text" x-model="customerPhone" placeholder="e.g. 9876543210" 
                               class="w-full border border-slate-300 rounded-xl text-xs font-bold py-1.5 px-2.5 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white text-slate-950 outline-none font-mono">
                    </div>
                </div>

                <!-- Order Ticket Items Scroll List -->
                <div class="space-y-2 max-h-56 overflow-y-auto min-h-[130px] pr-1">
                    <template x-if="combinedItems.length === 0">
                        <div class="text-center py-10 text-slate-400 text-xs font-extrabold space-y-1">
                            <div class="text-3xl">📋</div>
                            <p class="text-slate-700">Ticket is empty</p>
                            <p class="text-[10px] text-slate-400 font-medium">Click dishes from the menu to build the order.</p>
                        </div>
                    </template>

                    <!-- Sent to Kitchen Items -->
                    <template x-for="(item, index) in sentItems" :key="'sent_'+index">
                        <div class="flex items-center justify-between p-2.5 rounded-2xl bg-slate-100/90 border border-slate-200 text-xs">
                            <div class="flex-1 pr-2 min-w-0">
                                <div class="font-black text-slate-900 truncate" x-text="item.name"></div>
                                <div class="text-[10px] text-slate-500 font-bold">
                                    ₹<span x-text="item.price.toFixed(2)"></span> &times; <span x-text="item.qty"></span>
                                    <span class="ml-1 text-[9px] bg-slate-200 text-slate-800 font-black px-1.5 py-0.5 rounded-full uppercase tracking-wider">In Kitchen</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 font-mono font-black text-slate-900">
                                <span class="w-6 text-center text-sm" x-text="item.qty"></span>
                            </div>
                        </div>
                    </template>

                    <!-- New Unsent Cart Items -->
                    <template x-for="(item, index) in cart" :key="'cart_'+index">
                        <div class="flex items-center justify-between p-2.5 rounded-2xl bg-amber-50/70 border-2 border-amber-300 text-xs shadow-2xs">
                            <div class="flex-1 pr-2 min-w-0">
                                <div class="font-black text-slate-950 truncate flex items-center gap-1.5">
                                    <span class="w-2 h-2 bg-amber-500 rounded-full shrink-0"></span>
                                    <span x-text="item.name"></span>
                                </div>
                                <div class="text-[10px] text-amber-950 font-extrabold pl-3">
                                    ₹<span x-text="item.price.toFixed(2)"></span> &times; <span x-text="item.qty"></span>
                                    <span class="ml-1 text-[9px] bg-amber-200 text-amber-950 font-black px-1.5 py-0.5 rounded-full uppercase tracking-wider">New</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded-lg bg-white border border-slate-300 hover:border-slate-950 font-black text-slate-900 flex items-center justify-center text-sm shadow-2xs transition cursor-pointer">&minus;</button>
                                <span class="w-6 text-center font-black text-sm font-mono text-slate-950" x-text="item.qty"></span>
                                <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded-lg bg-white border border-slate-300 hover:border-slate-950 font-black text-slate-900 flex items-center justify-center text-sm shadow-2xs transition cursor-pointer">+</button>
                                <button type="button" @click="cart.splice(index, 1); calculateTotals();" class="ml-1 text-rose-500 hover:text-rose-700 font-black p-0.5 cursor-pointer">&times;</button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Cooking / Kitchen Special Notes -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Cooking Instructions</label>
                        <div class="flex gap-1">
                            <button type="button" @click="appendPresetNote('Extra Spicy')" class="text-[9px] bg-slate-100 text-slate-700 font-bold px-1.5 py-0.5 rounded-md hover:bg-slate-200 cursor-pointer">🌶️ Spicy</button>
                            <button type="button" @click="appendPresetNote('No Garlic')" class="text-[9px] bg-slate-100 text-slate-700 font-bold px-1.5 py-0.5 rounded-md hover:bg-slate-200 cursor-pointer">🧄 No Garlic</button>
                            <button type="button" @click="appendPresetNote('Make Parcel')" class="text-[9px] bg-slate-100 text-slate-700 font-bold px-1.5 py-0.5 rounded-md hover:bg-slate-200 cursor-pointer">🛍️ Parcel</button>
                            <button type="button" @click="appendPresetNote('Jain Prep')" class="text-[9px] bg-slate-100 text-slate-700 font-bold px-1.5 py-0.5 rounded-md hover:bg-slate-200 cursor-pointer">🌿 Jain</button>
                        </div>
                    </div>
                    <input type="text" x-model="cookingNotes" placeholder="e.g. Less oil, extra spicy, sauce on side..." 
                           class="w-full text-xs font-bold border border-slate-300 rounded-xl py-2 px-3 bg-slate-50/70 text-slate-950 outline-none focus:border-amber-500 focus:bg-white transition">
                </div>

                <!-- Bill Totals Breakdown with CGST & SGST -->
                <div class="border-t border-slate-200 pt-3 space-y-1.5 text-xs font-bold text-slate-600">
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">Items Subtotal</span>
                        <span class="font-mono text-slate-950 font-black">₹<span x-text="subtotal.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between text-slate-500 text-[11px]" x-show="cgst > 0">
                        <span>Central GST (<span x-text="cgstPercent"></span>%)</span>
                        <span class="font-mono text-slate-900 font-semibold">₹<span x-text="cgst.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between text-slate-500 text-[11px]" x-show="sgst > 0">
                        <span>State GST (<span x-text="sgstPercent"></span>%)</span>
                        <span class="font-mono text-slate-900 font-semibold">₹<span x-text="sgst.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between text-rose-600 text-[11px]" x-show="discount > 0">
                        <span>Discount</span>
                        <span class="font-mono font-bold">-₹<span x-text="discount.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between text-emerald-600 text-[11px]" x-show="alreadyPaidAmount > 0">
                        <span>Paid Online</span>
                        <span class="font-mono font-bold">-₹<span x-text="alreadyPaidAmount.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between items-baseline pt-2.5 border-t border-dashed border-slate-300 text-base font-black text-slate-950">
                        <span>Amount Due</span>
                        <span class="text-2xl text-slate-950 font-mono font-black">₹<span x-text="grandTotal.toFixed(2)">0.00</span></span>
                    </div>
                </div>

                <!-- Action Buttons: Send KOT & Settle Bill -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="saveOrder('kot')" :disabled="loading || combinedItems.length === 0" 
                            class="w-full py-3.5 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 rounded-xl text-xs font-black transition flex items-center justify-center gap-1.5 shadow-sm disabled:opacity-40 uppercase tracking-wider cursor-pointer">
                            <span>👨‍🍳 Send KOT</span>
                        </button>

                        <button type="button" @click="openSettleModal()" :disabled="loading || combinedItems.length === 0" 
                            :class="activeOrderPaymentStatus === 'Paid' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-950 hover:bg-slate-900 text-white'"
                            class="w-full py-3.5 rounded-xl text-xs font-black transition flex items-center justify-center gap-1.5 shadow-sm disabled:opacity-40 uppercase tracking-wider cursor-pointer">
                            <span x-text="activeOrderPaymentStatus === 'Paid' ? '✓ Complete & Release' : '💳 Settle Bill'"></span>
                        </button>
                    </div>

                    <!-- Print Slips when order exists -->
                    <div class="grid grid-cols-2 gap-2" x-show="activeOrderIds.length > 0">
                        <a :href="activeKotUrl" target="_blank" 
                           class="w-full py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 text-center rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs">
                            🖨️ KOT Slip
                        </a>
                        <a :href="activeReceiptUrl" target="_blank" 
                           class="w-full py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 text-center rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs">
                            🧾 Customer Bill
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Settlement / Payment Modal -->
    <div x-show="settleModalOpen" x-cloak class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col justify-between my-auto overflow-hidden" @click.away="settleModalOpen = false">
            
            <!-- Sticky Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 shrink-0">
                <div>
                    <h3 class="font-black text-base sm:text-lg text-slate-950 tracking-tight" x-text="activeOrderPaymentStatus === 'Paid' ? 'Release Table & Complete' : 'Payment & Settlement'"></h3>
                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium" x-text="activeOrderPaymentStatus === 'Paid' ? 'Order is already paid online. Confirm to release table.' : 'Select payment mode and confirm bill settlement.'"></p>
                </div>
                <button type="button" @click="settleModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition shrink-0">&times;</button>
            </div>

            <!-- Scrollable Content Body -->
            <div class="overflow-y-auto py-3 space-y-3.5 pr-1 max-h-[calc(90vh-130px)]">
                <!-- Amount Payable Banner -->
                <div class="bg-emerald-700 text-white rounded-2xl p-3.5 text-center shadow-inner space-y-1 shrink-0" x-show="activeOrderPaymentStatus === 'Paid'">
                    <span class="text-[10px] uppercase tracking-widest font-black text-emerald-200">✅ PAID ONLINE VIA DIRECT UPI</span>
                    <div class="text-2xl font-black text-white font-mono">₹0.00 DUE</div>
                    <p class="text-[11px] text-emerald-100 font-bold">Guest has already paid ₹<span x-text="grandTotal.toFixed(2)"></span> online. No payment required!</p>
                </div>
                <div class="bg-slate-950 text-white rounded-2xl p-3.5 text-center shadow-inner space-y-1 shrink-0" x-show="activeOrderPaymentStatus !== 'Paid'">
                    <span class="text-[10px] uppercase tracking-widest font-black text-amber-400">Total Amount Payable</span>
                    <div class="text-3xl font-black text-white font-mono">₹<span x-text="grandTotal.toFixed(2)"></span></div>
                </div>

                <!-- Payment Status Selector (Paid vs Unpaid Pre-Bill) -->
                <div>
                    <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Payment Action & Status</label>
                    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl border border-slate-200">
                        <button type="button" @click="settlePaymentStatus = 'Paid'" 
                                :class="settlePaymentStatus === 'Paid' ? 'bg-emerald-600 text-white font-black shadow-xs' : 'text-slate-700 font-bold hover:bg-slate-200'"
                                class="py-2.5 rounded-lg text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <span>✅ Mark as Paid</span>
                        </button>
                        <button type="button" @click="settlePaymentStatus = 'Pending'" 
                                :class="settlePaymentStatus === 'Pending' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-700 font-bold hover:bg-slate-200'"
                                class="py-2.5 rounded-lg text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <span>⏳ Unpaid (Pre-Bill)</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-emerald-800 font-bold mt-1.5 flex items-center gap-1" x-show="settlePaymentStatus === 'Paid'">
                        <span>💡</span> <span>Receipt prints as <b>PAID ✅</b> (No "Scan to Pay" QR code printed).</span>
                    </p>
                    <p class="text-[11px] text-amber-900 font-bold mt-1.5 flex items-center gap-1" x-show="settlePaymentStatus === 'Pending'">
                        <span>💡</span> <span>Receipt prints as <b>PENDING ⏳</b> with <b>"SCAN TO PAY" QR Code 📲</b> for customer.</span>
                    </p>
                </div>

                <!-- Payment Mode Options (Hidden if already paid or marked pending) -->
                <div x-show="settlePaymentStatus === 'Paid' && activeOrderPaymentStatus !== 'Paid'">
                    <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Payment Method</label>
                    <select x-model="paymentMethod" @change="if(paymentMethod === 'UPI') renderUpiQrCode()" class="w-full border border-slate-300 rounded-xl text-xs font-black py-2.5 px-3.5 bg-white text-slate-950 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                        <option value="Cash">💵 Cash Settlement</option>
                        <option value="UPI">📱 Instant UPI QR / PhonePe / GPay</option>
                        <option value="Card">💳 Credit / Debit Card (Swipe)</option>
                    </select>
                </div>

                <!-- Live Organization UPI QR Code Display (POS) -->
                <div x-show="paymentMethod === 'UPI' && settlePaymentStatus === 'Paid' && activeOrderPaymentStatus !== 'Paid'" class="bg-amber-50/90 p-3.5 rounded-2xl border border-amber-200 text-center space-y-2">
                    <div class="text-[11px] font-black text-amber-900 uppercase tracking-wider flex items-center justify-center gap-1.5">
                        <span>📱 SCAN QR CODE TO PAY BILL</span>
                    </div>
                    
                    <div class="flex justify-center py-1">
                        <div id="posUpiQrCode" class="p-2 bg-white rounded-xl border-2 border-amber-300 shadow-xs inline-block min-w-[130px] min-h-[130px]"></div>
                    </div>

                    <div class="text-sm font-mono font-black text-slate-950">
                        Amount: ₹<span x-text="grandTotal.toFixed(2)"></span>
                    </div>

                    <template x-if="orgUpiId">
                        <div class="text-[11px] font-mono text-slate-800 bg-white/90 py-1 px-3 rounded-lg border border-slate-200 inline-block font-bold">
                            UPI ID: <span class="text-amber-800 font-extrabold" x-text="orgUpiId"></span>
                        </div>
                    </template>
                    <template x-if="!orgUpiId">
                        <div class="text-[11px] text-rose-700 font-bold bg-rose-50 p-2 rounded-lg border border-rose-200">
                            ⚠️ Organization UPI ID not configured. Please add UPI ID in <a href="{{ route('organization.profile') }}" target="_blank" class="underline text-rose-900 font-black">Organization Profile</a>.
                        </div>
                    </template>

                    <div class="text-[10px] text-slate-500 font-bold pt-0.5">
                        Accepts GPay • PhonePe • Paytm • BHIM • All UPI Apps
                    </div>
                </div>

                <!-- Discount Amount -->
                <div>
                    <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Discount Amount (₹)</label>
                    <input type="number" min="0" step="0.01" x-model.number="discount" @input="calculateTotals()" 
                           placeholder="0.00"
                           class="w-full border border-slate-300 rounded-xl text-xs font-bold py-2 px-3.5 bg-white text-slate-950 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-mono">
                </div>

                <!-- Cash Tendered & Change Return -->
                <div x-show="paymentMethod === 'Cash' && settlePaymentStatus === 'Paid' && activeOrderPaymentStatus !== 'Paid'" class="bg-slate-50 p-3 rounded-2xl border border-slate-200 space-y-2">
                    <div class="flex justify-between items-center text-xs font-bold text-slate-700">
                        <span>Cash Received (₹):</span>
                        <input type="number" min="0" step="1" x-model.number="tenderAmount" 
                               class="w-32 text-right border border-slate-300 rounded-xl px-3 py-1.5 font-mono font-black text-slate-950 bg-white">
                    </div>
                    <div class="flex justify-between items-center text-xs font-black text-emerald-800 pt-2 border-t border-slate-200">
                        <span>Change to Return:</span>
                        <span class="font-mono text-sm font-black">₹<span x-text="Math.max(0, tenderAmount - grandTotal).toFixed(2)"></span></span>
                    </div>
                </div>
            </div>

            <!-- Sticky Confirm & Print Buttons -->
            <div class="flex gap-2 pt-3 border-t border-slate-100 shrink-0">
                <button type="button" @click="confirmSettle()" :disabled="loading" 
                        :class="settlePaymentStatus === 'Paid' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-amber-500 hover:bg-amber-400 text-slate-950'"
                        class="flex-1 py-3 rounded-xl text-xs font-black transition shadow-sm uppercase tracking-wider cursor-pointer">
                    <span x-show="!loading" x-text="settlePaymentStatus === 'Paid' ? '✓ Complete & Print Receipt' : '🖨️ Print Pre-Bill (Unpaid)'"></span>
                    <span x-show="loading">Processing...</span>
                </button>
                <button type="button" @click="settleModalOpen = false" class="py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
function waiterPos() {
    return {
        tables: @json($tables),
        tableFilter: 'all',
        selectedTableId: null,
        selectedTableName: '',
        orderType: 'Dine-in',
        selectedCategory: 'all',
        searchQuery: '',
        customerName: '',
        customerPhone: '',
        cookingNotes: '',
        cart: [],
        discount: 0,
        subtotal: 0,
        cgst: 0,
        sgst: 0,
        totalTax: 0,
        grandTotal: 0,
        cgstPercent: {{ (float)($org->cgst_percent ?? 0) }},
        sgstPercent: {{ (float)($org->sgst_percent ?? 0) }},
        orgUpiId: '{{ $org->upi_id ?? "" }}',
        orgName: '{{ addslashes($org->name ?? "POS") }}',
        activeOrderIds: [],
        activeOrderNumber: '',
        activeOrderPaymentStatus: 'Pending',
        settlePaymentStatus: 'Paid',
        activeKotUrl: '#',
        activeReceiptUrl: '#',
        settleModalOpen: false,
        paymentMethod: 'Cash',
        tenderAmount: 0,
        sentItems: [],
        alreadyPaidAmount: 0,
        loading: false,

        init() {
            const firstTable = @json($tables->first());
            if (firstTable) {
                this.selectTable(firstTable);
            }

            // Auto-poll tables status every 5 seconds for 100% real-time updates!
            setInterval(() => {
                this.pollTablesStatus();
            }, 5000);
        },

        pollTablesStatus() {
            fetch('/organization/menu/pos/api/tables-status')
                .then(res => res.json())
                .then(data => {
                    if (Array.isArray(data)) {
                        let prevOccupied = this.occupiedTablesCount;
                        this.tables = data;

                        // Auto refresh currently selected table if active
                        if (this.selectedTableId) {
                            let updatedTable = data.find(t => t.id === this.selectedTableId);
                            if (updatedTable && updatedTable.is_occupied) {
                                this.getTableOrder(this.selectedTableId, true);
                            }
                        }

                        // Play audio chime alert when new table order arrives!
                        let newOccupied = data.filter(t => t.is_occupied).length;
                        if (newOccupied > prevOccupied) {
                            this.playChimeSound();
                        }
                    }
                })
                .catch(err => console.log('Polling error:', err));
        },

        playChimeSound() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.15);
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.4);
            } catch(e) {}
        },

        get vacantTablesCount() {
            return this.tables.filter(t => !t.is_occupied).length;
        },

        get occupiedTablesCount() {
            return this.tables.filter(t => t.is_occupied).length;
        },

        selectTable(table) {
            this.selectedTableId = table.id;
            this.selectedTableName = table.name;
            this.orderType = 'Dine-in';
            this.getTableOrder(table.id);
        },

        setOrderType(type) {
            this.orderType = type;
            if (type === 'Takeaway') {
                this.selectedTableId = null;
                this.selectedTableName = '';
                this.resetForm();
                this.orderType = 'Takeaway';
            }
        },

        appendPresetNote(note) {
            if (this.cookingNotes.includes(note)) return;
            this.cookingNotes = this.cookingNotes ? (this.cookingNotes + ', ' + note) : note;
        },

        getTableOrder(tableId, isBackground = false) {
            if (!tableId) return;
            if (!isBackground) {
                this.loading = true;
                this.resetForm();
            }
            this.selectedTableId = tableId;
            this.activeTable = this.tables.find(t => t.id === tableId);
            if(this.activeTable) this.selectedTableName = this.activeTable.name;
            
            fetch(`/organization/menu/pos/table/${tableId}`)
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    const tableIndex = this.tables.findIndex(t => t.id === tableId);
                    
                    if (data.active_orders && data.active_orders.length > 0) {
                        if(tableIndex > -1) {
                            this.tables[tableIndex].is_occupied = true;
                            this.tables[tableIndex].active_order = data.active_orders[0];
                        }
                        
                        this.activeOrderIds = data.active_orders.map(o => o.id);
                        this.activeOrderNumber = data.active_orders[0].order_number;
                        this.activeOrderPaymentStatus = data.active_orders[0].payment_status || 'Pending';
                        if (!isBackground || !this.customerName) {
                            this.customerName = data.active_orders[0].customer_name || '';
                        }
                        if (!isBackground || !this.customerPhone) {
                            this.customerPhone = data.active_orders[0].customer_phone || '';
                        }
                        this.orderType = data.active_orders[0].order_type || 'Dine-in';
                        this.cookingNotes = data.active_orders.map(o => o.special_notes).filter(Boolean).join(' | ');
                        
                        this.activeKotUrl = `/organization/menu/pos/orders/${data.active_orders[0].id}/print-kot`;
                        this.activeReceiptUrl = `/organization/menu/pos/orders/${data.active_orders[0].id}/print-receipt`;
                        
                        let allSent = [];
                        let paidAmount = 0;
                        data.active_orders.forEach(order => {
                            if (order.payment_status === 'Paid') {
                                paidAmount += parseFloat(order.total) || 0;
                            }
                            order.items.forEach(i => {
                                allSent.push({
                                    id: i.menu_item_id,
                                    name: i.name_snapshot,
                                    price: parseFloat(i.price_snapshot),
                                    qty: i.quantity
                                });
                            });
                        });
                        this.alreadyPaidAmount = paidAmount;
                        this.sentItems = allSent;
                        this.calculateTotals();
                    } else {
                        if(tableIndex > -1) {
                            this.tables[tableIndex].is_occupied = false;
                            this.tables[tableIndex].active_order = null;
                        }
                    }
                });
        },

        get combinedItems() {
            let items = [...this.sentItems];
            this.cart.forEach(c => {
                let existing = items.find(i => i.id === c.id);
                if (existing) {
                    existing.qty += c.qty;
                } else {
                    items.push({...c});
                }
            });
            return items;
        },

        addToTicket(item) {
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
            this.calculateTotals();
        },

        updateQty(index, change) {
            this.cart[index].qty += change;
            if (this.cart[index].qty <= 0) {
                this.cart.splice(index, 1);
            }
            this.calculateTotals();
        },

        clearTicket() {
            if(confirm('Clear unsent items from ticket?')) {
                this.cart = [];
                this.calculateTotals();
            }
        },
        
        resetForm() {
            this.selectedTableId = null;
            this.selectedTableName = '';
            this.activeOrderIds = [];
            this.activeOrderNumber = '';
            this.activeOrderPaymentStatus = 'Pending';
            this.customerName = '';
            this.customerPhone = '';
            this.cookingNotes = '';
            this.cart = [];
            this.sentItems = [];
            this.activeKotUrl = '';
            this.activeReceiptUrl = '';
            this.discount = 0;
            this.alreadyPaidAmount = 0;
            this.calculateTotals();
        },

        calculateTotals() {
            this.subtotal = this.combinedItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
            this.cgst = (this.subtotal * this.cgstPercent) / 100;
            this.sgst = (this.subtotal * this.sgstPercent) / 100;
            this.totalTax = this.cgst + this.sgst;
            this.grandTotal = Math.max(0, this.subtotal + this.totalTax - this.discount - this.alreadyPaidAmount);
        },

        saveOrder(mode) {
            if (this.cart.length === 0) return;

            this.loading = true;

            const payload = {
                restaurant_table_id: this.selectedTableId,
                order_type: this.orderType,
                customer_name: this.customerName,
                customer_phone: this.customerPhone,
                notes: this.cookingNotes,
                items: this.cart.map(i => ({ menu_item_id: i.id, quantity: i.qty }))
            };

            fetch('{{ route("organization.menu.pos.orders.save") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    if (!this.activeOrderIds.includes(data.order.id)) {
                        this.activeOrderIds.push(data.order.id);
                    }
                    this.activeOrderNumber = data.order.order_number;
                    this.activeKotUrl = data.print_kot_url;
                    this.activeReceiptUrl = data.print_receipt_url;

                    this.getTableOrder(this.selectedTableId);
                    
                    if (!this.selectedTableId) {
                        this.cart.forEach(c => this.sentItems.push({...c}));
                        this.cart = [];
                        this.calculateTotals();
                    }
                } else {
                    alert(data.message || 'Error saving order.');
                }
            })
            .catch(err => {
                this.loading = false;
                alert('Backend Error:\n' + (err.message ? err.message.substring(0, 500) : 'Unknown error'));
                console.error(err);
            });
        },

        renderUpiQrCode() {
            this.$nextTick(() => {
                const qrContainer = document.getElementById("posUpiQrCode");
                if (!qrContainer) return;
                qrContainer.innerHTML = "";
                const upiId = this.orgUpiId || '';
                if (!upiId) return;
                const amount = (this.grandTotal || 0).toFixed(2);
                const upiString = `upi://pay?pa=${encodeURIComponent(upiId)}&pn=${encodeURIComponent(this.orgName)}&am=${amount}&cu=INR&tn=${encodeURIComponent('POS Order Payment')}`;
                if (typeof QRCode !== 'undefined') {
                    new QRCode(qrContainer, {
                        text: upiString,
                        width: 130,
                        height: 130,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                }
            });
        },

        openSettleModal() {
            if (this.combinedItems.length === 0) return;
            this.settlePaymentStatus = (this.activeOrderPaymentStatus === 'Paid') ? 'Paid' : 'Paid';
            this.tenderAmount = this.grandTotal;
            this.settleModalOpen = true;
            if (this.paymentMethod === 'UPI') this.renderUpiQrCode();
        },

        confirmSettle() {
            if (this.cart.length > 0) {
                this.saveOrderAndSettle();
            } else if (this.activeOrderIds.length > 0) {
                this.executeSettle();
            } else {
                alert("Nothing to settle.");
            }
        },
        
        saveOrderAndSettle() {
            this.loading = true;
            const payload = {
                restaurant_table_id: this.selectedTableId,
                order_type: this.orderType,
                customer_name: this.customerName,
                customer_phone: this.customerPhone,
                notes: this.cookingNotes,
                items: this.cart.map(i => ({ menu_item_id: i.id, quantity: i.qty }))
            };

            fetch('{{ route("organization.menu.pos.orders.save") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if(!this.activeOrderIds.includes(data.order.id)) {
                        this.activeOrderIds.push(data.order.id);
                    }
                    this.executeSettle();
                } else {
                    this.loading = false;
                    alert(data.message || 'Error saving order.');
                }
            });
        },

        executeSettle() {
            this.loading = true;
            fetch(`/organization/menu/pos/orders/${this.activeOrderIds[0]}/settle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_status: this.settlePaymentStatus,
                    payment_method: this.paymentMethod,
                    discount: this.discount,
                    extra_order_ids: this.activeOrderIds.slice(1)
                })
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => { throw new Error(text); });
                }
                return res.json();
            })
            .then(data => {
                this.loading = false;
                if (data.success) {
                    this.settleModalOpen = false;
                    const tableId = this.selectedTableId;
                    this.resetForm();
                    window.open(data.print_receipt_url, '_blank');
                    if (tableId) {
                        this.getTableOrder(tableId);
                    }
                } else {
                    alert(data.message || 'Error settling bill.');
                }
            })
            .catch(err => {
                this.loading = false;
                alert('An error occurred settling payment.');
            });
        },

        cancelOrder() {
            if (!this.activeOrderIds[0]) return;

            if (confirm(`Are you sure you want to cancel Order #${this.activeOrderNumber}? This will vacate the table.`)) {
                this.loading = true;
                fetch(`/organization/menu/pos/orders/${this.activeOrderIds[0]}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if (data.success) {
                        alert(data.message || 'Order cancelled successfully.');
                        const tableId = this.selectedTableId;
                        this.resetForm();
                        if (tableId) {
                            this.getTableOrder(tableId);
                        }
                    } else {
                        alert(data.message || 'Error cancelling order.');
                    }
                })
                .catch(err => {
                    this.loading = false;
                    alert('An error occurred cancelling order.');
                });
            }
        }
    }
}
</script>
@endsection

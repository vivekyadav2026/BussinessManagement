@extends('layouts.sme')

@section('title', 'Restro Waiter POS & Order Billing')

@section('content')
<div class="space-y-5" x-data="waiterPos()">
    
    <!-- Minimalist Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white px-5 py-4 rounded-2xl border border-gray-200/80 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 font-bold flex items-center justify-center text-lg shrink-0">
                🍽️
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base font-bold text-gray-900 tracking-tight" style="color: #0f172a !important;">Restro POS & Billing</h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <span class="w-1.5 h-1.5 mr-1 rounded-full bg-emerald-500 animate-pulse"></span> Live POS
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-0.5" style="color: #64748b !important;">Take table orders, KOT dispatch & quick receipts</p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <div class="flex items-center gap-3 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-200/60 text-xs font-medium text-gray-600">
                <span class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span x-text="vacantTablesCount"></span> Vacant
                </span>
                <span class="text-gray-300">|</span>
                <span class="flex items-center gap-1.5 text-amber-700 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span x-text="occupiedTablesCount"></span> Occupied
                </span>
            </div>

            <a href="{{ route('organization.menu.kitchen.index') }}" target="_blank" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-2xs">
                <span>👨‍🍳 Kitchen KOT</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
            <a href="{{ route('organization.menu.tables.index') }}" class="px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-semibold transition border border-gray-200">
                ⚙️ Tables
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        <!-- Left Column: Tables & Menu Items (7 Cols) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-5">
            
            <!-- Table Selection Section -->
            <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-xs space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-stone-100">
                    <div class="flex items-center gap-2">
                        <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🪑 Select Table</span>
                            <span class="text-slate-400 font-mono font-bold">({{ count($tables) }})</span>
                        </h2>
                        
                        <!-- Table Filters -->
                        <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-xl">
                            <button type="button" @click="tableFilter = 'all'" :class="tableFilter === 'all' ? 'bg-white text-slate-900 font-black shadow-2xs' : 'text-slate-500 font-bold hover:text-slate-800'" class="px-2.5 py-0.5 rounded-lg text-[10px] transition">All</button>
                            <button type="button" @click="tableFilter = 'vacant'" :class="tableFilter === 'vacant' ? 'bg-white text-emerald-800 font-black shadow-2xs' : 'text-slate-500 font-bold hover:text-slate-800'" class="px-2.5 py-0.5 rounded-lg text-[10px] transition">Vacant</button>
                            <button type="button" @click="tableFilter = 'occupied'" :class="tableFilter === 'occupied' ? 'bg-white text-amber-800 font-black shadow-2xs' : 'text-slate-500 font-bold hover:text-slate-800'" class="px-2.5 py-0.5 rounded-lg text-[10px] transition">Occupied</button>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 bg-stone-100 p-1 rounded-2xl border border-stone-200 overflow-x-auto">
                        <button type="button" @click="setOrderType('Dine-in')" :class="orderType === 'Dine-in' ? 'bg-slate-900 text-white font-black shadow-2xs' : 'text-slate-600 font-bold hover:bg-stone-200'" class="px-3 py-1.5 rounded-xl text-[10px] sm:text-xs transition uppercase tracking-wider whitespace-nowrap flex-shrink-0">🪑 Dine-in</button>
                        <button type="button" @click="setOrderType('Takeaway')" :class="orderType === 'Takeaway' ? 'bg-slate-900 text-white font-black shadow-2xs' : 'text-slate-600 font-bold hover:bg-stone-200'" class="px-3 py-1.5 rounded-xl text-[10px] sm:text-xs transition uppercase tracking-wider whitespace-nowrap flex-shrink-0">🛍️ Parcel / Takeaway</button>
                    </div>
                </div>

                <!-- Tables Cards Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3 max-h-56 overflow-y-auto p-1 pr-2">
                    <template x-for="t in tables" :key="t.id">
                        <button type="button" 
                            x-show="tableFilter === 'all' || (tableFilter === 'vacant' && !t.is_occupied) || (tableFilter === 'occupied' && t.is_occupied)"
                            @click="selectTable(t)" 
                            :class="[
                                selectedTableId === t.id ? 'ring-2 ring-slate-900 border-slate-900 shadow-md bg-white scale-[1.02]' : '',
                                t.is_occupied ? 'bg-gradient-to-b from-amber-50/90 to-amber-100/50 border-amber-300 text-amber-950 hover:border-amber-400 shadow-2xs' : 'bg-gradient-to-b from-emerald-50/90 to-emerald-100/40 border-emerald-300 text-emerald-950 hover:border-emerald-400 shadow-2xs'
                            ]"
                            class="p-3.5 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between min-h-[108px] relative group overflow-hidden">
                            
                            <div class="flex flex-col gap-1 w-full relative">
                                <div class="flex justify-between items-start w-full gap-1">
                                    <span class="font-black text-[13px] tracking-tight leading-tight line-clamp-2 pr-1" 
                                          :class="t.is_occupied ? 'text-slate-900' : 'text-slate-900'" 
                                          x-text="t.name" :title="t.name"></span>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded-sm font-black uppercase tracking-wider shrink-0 shadow-2xs mt-0.5" 
                                          :class="t.is_occupied ? 'bg-amber-200 text-amber-900 border border-amber-300' : 'bg-emerald-200 text-emerald-900 border border-emerald-300'"
                                          x-text="t.is_occupied ? 'Busy' : 'Free'">
                                    </span>
                                </div>

                            <template x-if="t.is_occupied && t.active_order">
                                <div class="mt-2 pt-2 border-t border-amber-200/90 w-full space-y-0.5">
                                    <div class="text-[10px] text-amber-800 font-mono font-bold tracking-tight" x-text="'#' + t.active_order.order_number"></div>
                                    <div class="text-xs font-black text-amber-950 font-mono" x-text="'₹' + Number(t.active_order.total).toFixed(2)"></div>
                                </div>
                            </template>
                            <template x-if="!(t.is_occupied && t.active_order)">
                                <div class="mt-2 pt-2 border-t border-emerald-200/90 text-[10px] text-emerald-700 font-bold flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Available</span>
                                </div>
                            </template>
                        </button>
                    </template>
                    <div x-show="tables.length === 0" class="col-span-full text-center py-6 text-xs text-slate-400 font-bold">No tables configured. Click "Tables" to add tables.</div>
                </div>
            </div>

            <!-- Menu Categories & Food Items Section -->
            <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-xs space-y-4">
                
                <!-- Category Tabs & Search -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-b border-stone-100 pb-3">
                    <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 scrollbar-none">
                        <button type="button" @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-slate-900 text-white font-black shadow-2xs' : 'bg-stone-100 text-slate-700 hover:bg-stone-200 font-bold'" class="px-3.5 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                            All Dishes
                        </button>
                        @foreach($categories as $cat)
                            <button type="button" @click="selectedCategory = {{ $cat->id }}" :class="selectedCategory === {{ $cat->id }} ? 'bg-slate-900 text-white font-black shadow-2xs' : 'bg-stone-100 text-slate-700 hover:bg-stone-200 font-bold'" class="px-3.5 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                                {{ $cat->name }} ({{ count($cat->items) }})
                            </button>
                        @endforeach
                    </div>

                    <div class="w-full sm:w-56 shrink-0 relative">
                        <input type="text" x-model="searchQuery" placeholder="🔍 Search food item..." class="w-full text-xs font-bold border-2 border-stone-200 rounded-2xl py-2 px-3.5 focus:border-slate-900 outline-none bg-stone-50/50">
                    </div>
                </div>

                <!-- Food Items Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-[390px] overflow-y-auto p-1">
                    @foreach($categories as $cat)
                        @foreach($cat->items as $item)
                            <div x-show="(selectedCategory === 'all' || selectedCategory === {{ $cat->id }}) && ('{{ strtolower($item->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($cat->name) }}'.includes(searchQuery.toLowerCase()))"
                                @click="addToTicket({{ json_encode($item) }})"
                                class="bg-white border-2 border-stone-200 hover:border-slate-900 rounded-2xl p-3.5 cursor-pointer transition transform active:scale-95 shadow-2xs flex flex-col justify-between group hover:shadow-md">
                                
                                <div>
                                    <div class="flex items-start justify-between gap-1 mb-1">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider line-clamp-1">{{ $cat->name }}</span>
                                    </div>
                                    <div class="text-xs font-black text-slate-900 group-hover:text-amber-600 transition line-clamp-2 leading-tight tracking-tight">{{ $item->name }}</div>
                                </div>

                                <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-stone-100">
                                    <span class="text-sm font-black text-slate-900 font-mono">₹{{ number_format($item->price, 2) }}</span>
                                    <span class="w-7 h-7 rounded-xl bg-stone-100 text-slate-900 group-hover:bg-slate-900 group-hover:text-amber-400 flex items-center justify-center font-black text-sm transition shadow-2xs">+</span>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Column: Order Ticket / Billing Cart (5 Cols) -->
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="bg-white p-5 rounded-3xl border border-stone-200 shadow-md sticky top-4 space-y-4">
                
                <!-- Ticket Header -->
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                            <h3 class="font-black text-base text-slate-900 tracking-tight" x-text="orderType === 'Dine-in' ? (selectedTableName ? 'Table: ' + selectedTableName : 'Select Table') : 'Takeaway / Parcel Order'"></h3>
                        </div>
                        <div class="text-[11px] text-slate-400 font-mono font-bold mt-0.5" x-show="activeOrderIds.length > 0">Active Order #: <span x-text="activeOrderNumber" class="font-black text-slate-950"></span></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="cancelOrder()" class="px-3 py-1 bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white rounded-xl text-xs font-extrabold border border-rose-200 transition flex items-center gap-1 shadow-2xs" x-show="activeOrderIds.length > 0">
                            <span>🚫 Cancel Order</span>
                        </button>
                        <button type="button" @click="clearTicket()" class="text-xs text-slate-400 hover:text-rose-600 font-bold" x-show="cart.length > 0 && !activeOrderIds.length">Clear</button>
                    </div>
                </div>

                <!-- Customer Details Input -->
                <div class="grid grid-cols-2 gap-2 text-xs bg-stone-50 p-3 rounded-2xl border border-stone-200/80">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">👤 Guest Name</label>
                        <input type="text" x-model="customerName" placeholder="e.g. Rahul" class="w-full border-2 border-stone-200 rounded-xl text-xs font-bold py-1.5 px-2.5 focus:border-slate-900 bg-white text-slate-900 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">📞 Mobile Phone</label>
                        <input type="text" x-model="customerPhone" placeholder="e.g. 9876543210" class="w-full border-2 border-stone-200 rounded-xl text-xs font-bold py-1.5 px-2.5 focus:border-slate-900 bg-white text-slate-900 outline-none font-mono">
                    </div>
                </div>

                <!-- Order Ticket Items List -->
                <div class="space-y-2 max-h-56 overflow-y-auto min-h-[130px] pr-1">
                    <template x-if="combinedItems.length === 0">
                        <div class="text-center py-10 text-slate-400 text-xs font-extrabold space-y-1">
                            <div class="text-2xl">📋</div>
                            <p>Ticket is empty</p>
                            <p class="text-[10px] text-slate-400 font-medium">Click any dish from the menu grid to add to order.</p>
                        </div>
                    </template>

                    <!-- Sent Items -->
                    <template x-for="(item, index) in sentItems" :key="'sent_'+index">
                        <div class="flex items-center justify-between p-2.5 rounded-2xl bg-stone-100 border border-stone-200 text-xs opacity-90">
                            <div class="flex-1 pr-2 min-w-0">
                                <div class="font-black text-slate-800 truncate" x-text="item.name"></div>
                                <div class="text-[10px] text-slate-500 font-bold">₹<span x-text="item.price.toFixed(2)"></span> x <span x-text="item.qty"></span> <span class="ml-1 text-[9px] bg-slate-200 text-slate-800 font-black px-1.5 py-0.5 rounded-full uppercase">Sent to Kitchen</span></div>
                            </div>
                            <div class="flex items-center gap-1 font-mono font-black text-slate-700">
                                <span class="w-6 text-center text-sm" x-text="item.qty"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Unsent Cart Items -->
                    <template x-for="(item, index) in cart" :key="'cart_'+index">
                        <div class="flex items-center justify-between p-2.5 rounded-2xl bg-amber-50/60 border-2 border-amber-200/90 text-xs shadow-2xs">
                            <div class="flex-1 pr-2 min-w-0">
                                <div class="font-black text-slate-900 truncate flex items-center gap-1.5">
                                    <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                                    <span x-text="item.name"></span>
                                </div>
                                <div class="text-[10px] text-amber-900 font-extrabold pl-3">₹<span x-text="item.price.toFixed(2)"></span> x <span x-text="item.qty"></span> <span class="ml-1 text-[9px] bg-amber-200 text-amber-950 font-black px-1.5 py-0.5 rounded-full uppercase">New Dish</span></div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded-lg bg-white border border-stone-300 hover:border-slate-900 font-black text-slate-900 flex items-center justify-center text-sm shadow-2xs transition">&minus;</button>
                                <span class="w-6 text-center font-black text-sm font-mono" x-text="item.qty"></span>
                                <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded-lg bg-white border border-stone-300 hover:border-slate-900 font-black text-slate-900 flex items-center justify-center text-sm shadow-2xs transition">+</button>
                                <button type="button" @click="cart.splice(index, 1); calculateTotals();" class="ml-1 text-rose-500 hover:text-rose-700 font-black p-0.5">&times;</button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Cooking / Kitchen Special Notes -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Cooking Instructions</label>
                        <div class="flex gap-1">
                            <button type="button" @click="appendPresetNote('Extra Spicy')" class="text-[9px] bg-stone-100 text-slate-600 font-bold px-1.5 py-0.5 rounded hover:bg-stone-200">🌶️ Spicy</button>
                            <button type="button" @click="appendPresetNote('No Garlic')" class="text-[9px] bg-stone-100 text-slate-600 font-bold px-1.5 py-0.5 rounded hover:bg-stone-200">🧄 No Garlic</button>
                            <button type="button" @click="appendPresetNote('Make Parcel')" class="text-[9px] bg-stone-100 text-slate-600 font-bold px-1.5 py-0.5 rounded hover:bg-stone-200">🛍️ Parcel</button>
                        </div>
                    </div>
                    <input type="text" x-model="cookingNotes" placeholder="e.g. Less oil, extra spicy..." class="w-full text-xs font-bold border-2 border-stone-200 rounded-xl py-1.5 px-3 bg-stone-50/50 text-slate-900 outline-none focus:border-slate-900">
                </div>

                <!-- Bill Totals Breakdown with CGST & SGST -->
                <div class="border-t-2 border-stone-200 pt-3 space-y-1.5 text-xs font-extrabold text-slate-600">
                    <div class="flex justify-between">
                        <span>Items Subtotal</span>
                        <span class="font-mono text-slate-900 font-black">₹<span x-text="subtotal.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between" x-show="cgst > 0">
                        <span>CGST (<span x-text="cgstPercent"></span>%)</span>
                        <span class="font-mono text-slate-900">₹<span x-text="cgst.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between" x-show="sgst > 0">
                        <span>SGST (<span x-text="sgstPercent"></span>%)</span>
                        <span class="font-mono text-slate-900">₹<span x-text="sgst.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between" x-show="discount > 0">
                        <span>Discount</span>
                        <span class="font-mono text-rose-600">-₹<span x-text="discount.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between items-baseline pt-2.5 border-t border-stone-200 text-base font-black text-slate-900">
                        <span>Grand Total Bill</span>
                        <span class="text-xl text-slate-950 font-mono">₹<span x-text="grandTotal.toFixed(2)">0.00</span></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2 pt-3 border-t border-stone-200 mt-auto">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="saveOrder('kot')" :disabled="loading || combinedItems.length === 0" 
                            class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-amber-950 rounded-xl text-[11px] font-black transition flex items-center justify-center gap-1.5 shadow-sm disabled:opacity-50 uppercase">
                            <span>👨‍🍳 Send KOT</span>
                        </button>

                        <button type="button" @click="openSettleModal()" :disabled="loading || combinedItems.length === 0" 
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-black transition flex items-center justify-center gap-1.5 shadow-sm disabled:opacity-50 uppercase">
                            <span>💳 Settle Bill</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-2" x-show="activeOrderIds.length > 0">
                        <a :href="activeKotUrl" target="_blank" 
                           class="w-full py-2 bg-white border-2 border-amber-200 hover:border-amber-400 hover:bg-amber-50 text-amber-800 text-center rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                            🖨️ KOT Slip
                        </a>
                        <a :href="activeReceiptUrl" target="_blank" 
                           class="w-full py-2 bg-white border-2 border-emerald-200 hover:border-emerald-400 hover:bg-emerald-50 text-emerald-800 text-center rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                            🧾 Customer Receipt
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Settlement / Payment Modal -->
    <div x-show="settleModalOpen" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-stone-200">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div>
                    <h3 class="font-black text-lg text-slate-900 tracking-tight">Payment & Bill Settlement</h3>
                    <p class="text-xs text-slate-500 font-semibold">Confirm payment mode and print final receipt.</p>
                </div>
                <button type="button" @click="settleModalOpen = false" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-slate-600 font-black flex items-center justify-center text-sm">&times;</button>
            </div>

            <div class="bg-slate-900 text-white rounded-2xl p-4 text-center shadow-inner">
                <div class="text-xs uppercase tracking-wider font-extrabold text-amber-400 mb-1">Final Amount Payable</div>
                <div class="text-3xl font-black text-white font-mono">₹<span x-text="grandTotal.toFixed(2)"></span></div>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-1.5">Select Payment Mode</label>
                    <select x-model="paymentMethod" class="w-full border-2 border-stone-200 rounded-2xl text-xs font-black py-3 px-3.5 bg-white text-slate-900 outline-none focus:border-slate-900">
                        <option value="Cash">💵 Cash Payment</option>
                        <option value="UPI">📱 Dynamic UPI QR Code / Scanner</option>
                        <option value="Card">💳 Credit / Debit Card (POS Machine)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-1.5">Discount Amount (₹)</label>
                    <input type="number" min="0" step="0.01" x-model.number="discount" @input="calculateTotals()" class="w-full border-2 border-stone-200 rounded-2xl text-xs font-black py-2.5 px-3.5 bg-white text-slate-900 outline-none focus:border-slate-900 font-mono">
                </div>

                <div x-show="paymentMethod === 'Cash'" class="bg-stone-50 p-3 rounded-2xl border border-stone-200 space-y-2">
                    <div class="flex justify-between items-center text-xs font-bold text-slate-700">
                        <span>Cash Tendered (₹):</span>
                        <input type="number" min="0" step="1" x-model.number="tenderAmount" class="w-28 text-right border-2 border-stone-300 rounded-xl px-2.5 py-1 font-mono font-black text-slate-900">
                    </div>
                    <div class="flex justify-between items-center text-xs font-black text-emerald-800 pt-1 border-t border-stone-200">
                        <span>Change to Return:</span>
                        <span class="font-mono text-sm">₹<span x-text="Math.max(0, tenderAmount - grandTotal).toFixed(2)"></span></span>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 pt-2 border-t border-stone-100">
                <button type="button" @click="confirmSettle()" :disabled="loading" class="flex-1 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black transition shadow-lg uppercase tracking-wider">
                    ✓ Confirm Payment & Print Bill
                </button>
                <button type="button" @click="settleModalOpen = false" class="py-3.5 px-5 bg-stone-100 hover:bg-stone-200 text-slate-700 font-extrabold rounded-2xl text-xs">Cancel</button>
            </div>
        </div>
    </div>

</div>

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
        cgstPercent: {{ (float)auth()->user()->organization->cgst_percent }},
        sgstPercent: {{ (float)auth()->user()->organization->sgst_percent }},
        activeOrderIds: [],
        activeOrderNumber: '',
        activeKotUrl: '#',
        activeReceiptUrl: '#',
        settleModalOpen: false,
        paymentMethod: 'Cash',
        tenderAmount: 0,
        sentItems: [],
        loading: false,

        init() {
            const firstTable = @json($tables->first());
            if (firstTable) {
                this.selectTable(firstTable);
            }
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

        getTableOrder(tableId) {
            if (!tableId) return;
            this.loading = true;
            this.resetForm();
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
                        this.customerName = data.active_orders[0].customer_name || '';
                        this.customerPhone = data.active_orders[0].customer_phone || '';
                        this.orderType = data.active_orders[0].order_type || 'Dine-in';
                        this.cookingNotes = data.active_orders.map(o => o.special_notes).filter(Boolean).join(' | ');
                        
                        this.activeKotUrl = `/organization/menu/pos/orders/${data.active_orders[0].id}/print-kot`;
                        this.activeReceiptUrl = `/organization/menu/pos/orders/${data.active_orders[0].id}/print-receipt`;
                        
                        let allSent = [];
                        data.active_orders.forEach(order => {
                            order.items.forEach(i => {
                                allSent.push({
                                    id: i.menu_item_id,
                                    name: i.name_snapshot,
                                    price: parseFloat(i.price_snapshot),
                                    qty: i.quantity
                                });
                            });
                        });
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
            this.customerName = '';
            this.customerPhone = '';
            this.cookingNotes = '';
            this.cart = [];
            this.sentItems = [];
            this.activeKotUrl = '';
            this.activeReceiptUrl = '';
            this.discount = 0;
            this.calculateTotals();
        },

        calculateTotals() {
            this.subtotal = this.combinedItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
            this.cgst = (this.subtotal * this.cgstPercent) / 100;
            this.sgst = (this.subtotal * this.sgstPercent) / 100;
            this.totalTax = this.cgst + this.sgst;
            this.grandTotal = Math.max(0, this.subtotal + this.totalTax - this.discount);
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

        openSettleModal() {
            if (this.combinedItems.length === 0) return;
            this.tenderAmount = this.grandTotal;
            this.settleModalOpen = true;
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

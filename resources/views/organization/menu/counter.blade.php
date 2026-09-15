@extends('layouts.sme')

@section('title', 'Counter Billing & QSR Express')

@section('content')
<div class="space-y-6" x-data="counterBilling()" x-init="init()">
    
    <!-- Minimalist Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white px-5 py-4 rounded-2xl border border-gray-200/80 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 font-bold flex items-center justify-center text-lg shrink-0">
                🛒
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base font-bold text-gray-900 tracking-tight" style="color: #0f172a !important;">Counter Billing</h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <span class="w-1.5 h-1.5 mr-1 rounded-full bg-emerald-500 animate-pulse"></span> QSR Mode
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-0.5" style="color: #64748b !important;">Quick-service token ordering & express checkout</p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <div class="hidden sm:flex items-center gap-3 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-200/60 text-xs">
                <span class="text-gray-500 font-medium">Tokens: <strong class="text-amber-600 font-bold" x-text="activeOrders.length">0</strong></span>
                <span class="text-gray-300">|</span>
                <span class="text-gray-500 font-medium">Completed: <strong class="text-emerald-600 font-bold" x-text="completedOrders.length">0</strong></span>
            </div>

            <!-- Tab Switcher -->
            <div class="flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200/60">
                <button type="button" @click="activeTab = 'new'" 
                        :class="activeTab === 'new' ? 'bg-white text-indigo-600 font-bold shadow-2xs' : 'text-gray-600 hover:text-gray-900'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition">
                    ➕ New Order
                </button>
                <button type="button" @click="activeTab = 'current'" 
                        :class="activeTab === 'current' ? 'bg-white text-indigo-600 font-bold shadow-2xs' : 'text-gray-600 hover:text-gray-900'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                    <span>⏳ Tokens</span>
                    <span class="bg-amber-100 text-amber-800 text-[10px] px-1.5 py-0.2 rounded-full font-bold" x-text="activeOrders.length">0</span>
                </button>
                <button type="button" @click="activeTab = 'completed'" 
                        :class="activeTab === 'completed' ? 'bg-white text-indigo-600 font-bold shadow-2xs' : 'text-gray-600 hover:text-gray-900'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition">
                    📜 Completed
                </button>
            </div>
        </div>
    </div>

    <!-- 1. NEW ORDER TAB -->
    <div x-show="activeTab === 'new'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Menu Items Grid (8 Cols) -->
        <div class="lg:col-span-7 xl:col-span-8 bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm space-y-4">
            
            <!-- Filter & Search Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-b border-gray-100 pb-3">
                
                <!-- Category Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 scrollbar-none">
                    <button type="button" @click="selectedCategory = 'all'" 
                            :class="selectedCategory === 'all' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" 
                            class="px-3.5 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                        All Items
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" @click="selectedCategory = {{ $cat->id }}" 
                                :class="selectedCategory === {{ $cat->id }} ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" 
                                class="px-3.5 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                            {{ $cat->name }} ({{ $cat->items->count() }})
                        </button>
                    @endforeach
                </div>

                <!-- Item Search -->
                <div class="w-full sm:w-56 shrink-0 relative">
                    <input type="text" x-model="searchQuery" x-ref="searchInput" placeholder="🔍 Search item name..." 
                           class="w-full text-xs border border-gray-200 rounded-xl py-2 pl-3 pr-8 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-2xs">
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600 text-xs">✕</button>
                </div>
            </div>

            <!-- Items Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-[540px] overflow-y-auto pr-1">
                @foreach($categories as $cat)
                    @foreach($cat->items as $item)
                        <div x-show="(selectedCategory === 'all' || selectedCategory === {{ $cat->id }}) && ('{{ strtolower(addslashes($item->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($cat->name)) }}'.includes(searchQuery.toLowerCase()))"
                            @click="addToCart({{ json_encode($item) }})"
                            class="bg-white border border-gray-200 hover:border-indigo-500 rounded-xl p-3 cursor-pointer transition-all hover:shadow-md flex flex-col justify-between group relative overflow-hidden">
                            
                            <div class="space-y-1">
                                <div class="flex items-start justify-between gap-1">
                                    <span class="text-xs font-bold text-gray-900 group-hover:text-indigo-600 transition line-clamp-2 leading-tight">
                                        {{ $item->name }}
                                    </span>
                                </div>
                                <div class="text-[10px] font-semibold text-gray-400">{{ $cat->name }}</div>
                            </div>

                            <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100">
                                <span class="text-xs font-black text-slate-900 group-hover:text-indigo-600 transition">₹{{ number_format($item->price, 2) }}</span>
                                <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center font-bold text-xs transition shadow-2xs">
                                    +
                                </span>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        <!-- Right Column: Cart & Billing Ticket (4 Cols) -->
        <div class="lg:col-span-5 xl:col-span-4 bg-white p-5 rounded-2xl border border-gray-200/80 shadow-md sticky top-4 space-y-4 flex flex-col justify-between">
            <div>
                <!-- Ticket Header -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="font-black text-base text-slate-900 flex items-center gap-1.5">
                            <span x-text="editingOrderId ? '✏️ Edit Token #' + editingOrderNumber : '🛒 New Counter Ticket'"></span>
                        </h3>
                        <p class="text-[10px] text-gray-400 font-semibold mt-0.5">Counter express checkout</p>
                    </div>
                    <button type="button" @click="clearCart()" class="text-xs text-rose-600 hover:text-rose-700 font-bold hover:underline" x-show="cart.length > 0">
                        Clear All
                    </button>
                </div>

                <!-- Customer Name / Token Input -->
                <div class="pt-3 space-y-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-wider">Customer / Token #</label>
                            <button type="button" @click="generateQuickToken()" class="text-[10px] text-indigo-600 font-bold hover:underline">
                                ⚡ Auto Token
                            </button>
                        </div>
                        <input type="text" x-model="customerName" placeholder="e.g. Token 105 or Customer Name" 
                               class="w-full border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-gray-50/50 font-bold">
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-wider">Cooking Instructions</label>
                        </div>
                        <div class="flex items-center gap-1.5 overflow-x-auto text-[10px] pb-1">
                            <button type="button" @click="appendPresetNote('📦 Takeaway')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition border border-slate-200/60 whitespace-nowrap">📦 Takeaway</button>
                            <button type="button" @click="appendPresetNote('🌶️ Spicy')" class="px-2 py-0.5 rounded bg-red-50 hover:bg-red-100 text-red-700 font-semibold transition border border-red-200/60 whitespace-nowrap">🌶️ Spicy</button>
                            <button type="button" @click="appendPresetNote('🚫 Less Sugar')" class="px-2 py-0.5 rounded bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold transition border border-red-200/60 whitespace-nowrap">🚫 Less Sugar</button>
                        </div>
                        <input type="text" x-model="cookingNotes" placeholder="e.g. Less oil, extra spicy..." 
                               class="w-full border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-gray-50/50 font-bold">
                    </div>
                </div>

                <!-- Cart Items Scroll List -->
                <div class="space-y-2 max-h-[240px] overflow-y-auto min-h-[160px] my-3 pr-1 divide-y divide-gray-100">
                    <template x-if="cart.length === 0">
                        <div class="text-center py-12 text-gray-400 text-xs font-medium space-y-1">
                            <div class="text-3xl opacity-40">🛒</div>
                            <p class="font-bold text-gray-500">Cart is empty</p>
                            <p class="text-[10px] text-gray-400">Click items on the left to add to bill.</p>
                        </div>
                    </template>

                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="pt-2 flex items-center justify-between gap-2 text-xs">
                            <div class="flex-1 pr-1 min-w-0">
                                <div class="font-bold text-slate-800 truncate" x-text="item.name"></div>
                                <div class="text-[10px] text-gray-500 font-semibold">
                                    ₹<span x-text="item.price.toFixed(2)"></span> × <span x-text="item.qty"></span>
                                    <span class="text-indigo-600 font-bold ml-1">= ₹<span x-text="(item.price * item.qty).toFixed(2)"></span></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 bg-gray-100 p-0.5 rounded-lg border border-gray-200/60">
                                <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded bg-white hover:bg-gray-200 font-bold text-gray-700 flex items-center justify-center text-xs shadow-2xs">-</button>
                                <span class="w-5 text-center font-black text-xs" x-text="item.qty"></span>
                                <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded bg-white hover:bg-gray-200 font-bold text-gray-700 flex items-center justify-center text-xs shadow-2xs">+</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Total Breakdown & Action Buttons -->
            <div class="border-t border-gray-200 pt-3 space-y-2">
                <div class="space-y-1 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-semibold text-slate-800">₹<span x-text="cartSubtotal.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between text-[11px] text-gray-500">
                        <span>CGST (<span x-text="cgstPercent"></span>%)</span>
                        <span>₹<span x-text="cartCgst.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between text-[11px] text-gray-500">
                        <span>SGST (<span x-text="sgstPercent"></span>%)</span>
                        <span>₹<span x-text="cartSgst.toFixed(2)">0.00</span></span>
                    </div>
                </div>

                <div class="flex justify-between items-baseline pt-2 border-t border-dashed border-gray-200">
                    <span class="text-xs font-bold text-slate-900 uppercase">Grand Total</span>
                    <span class="text-2xl font-black text-indigo-700">₹<span x-text="cartTotal.toFixed(2)">0.00</span></span>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2">
                    <button type="button" @click="saveHoldOrder()" :disabled="cart.length === 0 || loading" 
                            class="py-3 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition shadow-xs flex justify-center items-center gap-1.5">
                        <span x-show="!loading">⏳ Hold Token</span>
                        <span x-show="loading" class="animate-spin text-xs">🌀</span>
                    </button>
                    <button type="button" @click="openSettleModal()" :disabled="cart.length === 0 || loading" 
                            class="py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition shadow-sm flex justify-center items-center gap-1.5">
                        <span x-show="!loading">💳 Pay & Print</span>
                        <span x-show="loading" class="animate-spin text-xs">🌀</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. ACTIVE HOLD TOKENS QUEUE TAB -->
    <div x-show="activeTab === 'current'" class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-4" style="display: none;">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b pb-4">
            <div>
                <h2 class="text-lg font-black text-slate-900">Live Active Tokens Queue</h2>
                <p class="text-xs text-gray-500">Hold orders waiting for billing or order modification</p>
            </div>
            <button type="button" @click="fetchActiveOrders()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100">
                🔄 Refresh Queue
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            <!-- Quick Add Card -->
            <div @click="resetCartForm(); activeTab = 'new'" 
                 class="border-2 border-dashed border-gray-300 bg-gray-50/60 rounded-2xl p-5 flex flex-col items-center justify-center text-gray-500 hover:border-indigo-500 hover:text-indigo-600 hover:bg-indigo-50/40 cursor-pointer transition min-h-[160px] group shadow-xs">
                <div class="w-12 h-12 rounded-full bg-white border border-gray-200 group-hover:border-indigo-400 group-hover:bg-indigo-100 flex items-center justify-center mb-3 shadow-2xs transition">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="font-extrabold text-sm tracking-wide">Create New Token</span>
                <span class="text-[10px] text-gray-400 mt-1">Start fresh counter order</span>
            </div>

            <!-- Active Token Cards -->
            <template x-for="order in activeOrders" :key="order.id">
                <div class="border border-gray-200 rounded-2xl p-4 flex flex-col justify-between hover:border-indigo-400 hover:shadow-md transition bg-white space-y-3">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-black text-base text-slate-900 truncate block" x-text="order.customer_name"></span>
                                <span class="text-[10px] text-gray-400 font-mono" x-text="new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                            </div>
                            <span class="text-xs font-mono font-bold bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg border border-indigo-100" x-text="order.order_number"></span>
                        </div>
                        
                        <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100 space-y-1 text-xs">
                            <div class="flex justify-between font-semibold text-gray-700">
                                <span>Items Summary</span>
                                <span class="text-gray-500" x-text="order.items.length + ' items'"></span>
                            </div>
                            <div class="text-[11px] text-gray-500 truncate" x-text="order.items.map(i => i.quantity + 'x ' + i.name_snapshot).join(', ')"></div>
                            <div class="flex justify-between items-center pt-1 border-t border-gray-200/60 font-black">
                                <span class="text-slate-700">Total</span>
                                <span class="text-indigo-700 text-sm">₹<span x-text="parseFloat(order.total).toFixed(2)"></span></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 pt-1">
                        <button type="button" @click="editOrder(order)" class="flex-1 py-2 bg-white border border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1">
                            ✏️ Edit Items
                        </button>
                        <button type="button" @click="openSettleModalForOrder(order)" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs rounded-xl text-xs font-bold transition flex items-center justify-center gap-1">
                            💳 Bill Now
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- 3. COMPLETED ORDERS TAB -->
    <div x-show="activeTab === 'completed'" class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-4" style="display: none;">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b pb-4">
            <div>
                <h2 class="text-lg font-black text-slate-900">Today's Completed Counter Bills</h2>
                <p class="text-xs text-gray-500">Historical view of settled counter orders and receipts</p>
            </div>
            <button type="button" @click="fetchCompletedOrders()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100">
                🔄 Refresh Receipts
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-xl">Order #</th>
                        <th class="px-4 py-3">Customer / Token</th>
                        <th class="px-4 py-3">Items</th>
                        <th class="px-4 py-3">Grand Total</th>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3 rounded-tr-xl text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <template x-if="completedOrders.length === 0">
                        <tr><td colspan="6" class="text-center py-10 text-gray-400 font-medium">No completed orders today.</td></tr>
                    </template>
                    <template x-for="order in completedOrders" :key="order.id">
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900" x-text="order.order_number"></td>
                            <td class="px-4 py-3 font-bold text-slate-800" x-text="order.customer_name"></td>
                            <td class="px-4 py-3 text-gray-500" x-text="order.items.length + ' items'"></td>
                            <td class="px-4 py-3 font-black text-emerald-600 text-sm">₹<span x-text="parseFloat(order.total).toFixed(2)"></span></td>
                            <td class="px-4 py-3 text-gray-500" x-text="new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></td>
                            <td class="px-4 py-3 text-right flex items-center justify-end gap-2">
                                <template x-if="order.invoice_id">
                                    <a :href="'/organization/invoices/' + order.invoice_id" target="_blank" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-bold transition">
                                        👁️ View
                                    </a>
                                </template>
                                <a :href="'/organization/menu/pos/orders/' + order.id + '/print-receipt'" target="_blank" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition shadow-2xs">
                                    🖨️ Print Bill
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Settlement / Quick Payment Modal -->
    <div x-show="settleModalOpen" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 border border-gray-100" @click.away="settleModalOpen = false">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-black text-lg text-slate-900">Checkout & Settle Bill</h3>
                    <p class="text-xs text-gray-400">Select payment method & calculate change</p>
                </div>
                <button type="button" @click="settleModalOpen = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 font-bold flex items-center justify-center text-sm transition">✕</button>
            </div>

            <!-- Grand Total Box -->
            <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-2xl p-4 text-center shadow-md">
                <div class="text-xs text-indigo-200 uppercase tracking-wider font-bold">Final Amount Payable</div>
                <div class="text-4xl font-black text-white mt-1">₹<span x-text="modalGrandTotal.toFixed(2)"></span></div>
            </div>

            <div class="space-y-4">
                <!-- Payment Method Cards -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Payment Method</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="paymentMethod = 'Cash'" 
                                :class="paymentMethod === 'Cash' ? 'border-2 border-emerald-600 bg-emerald-50 text-emerald-900 font-black shadow-2xs' : 'border border-gray-200 text-gray-700 hover:bg-gray-50'" 
                                class="py-2.5 px-2 rounded-xl text-xs text-center transition flex flex-col items-center gap-1">
                            <span class="text-lg">💵</span>
                            <span>Cash</span>
                        </button>
                        <button type="button" @click="paymentMethod = 'UPI'" 
                                :class="paymentMethod === 'UPI' ? 'border-2 border-indigo-600 bg-indigo-50 text-indigo-900 font-black shadow-2xs' : 'border border-gray-200 text-gray-700 hover:bg-gray-50'" 
                                class="py-2.5 px-2 rounded-xl text-xs text-center transition flex flex-col items-center gap-1">
                            <span class="text-lg">📱</span>
                            <span>UPI / QR</span>
                        </button>
                        <button type="button" @click="paymentMethod = 'Card'" 
                                :class="paymentMethod === 'Card' ? 'border-2 border-purple-600 bg-purple-50 text-purple-900 font-black shadow-2xs' : 'border border-gray-200 text-gray-700 hover:bg-gray-50'" 
                                class="py-2.5 px-2 rounded-xl text-xs text-center transition flex flex-col items-center gap-1">
                            <span class="text-lg">💳</span>
                            <span>Card POS</span>
                        </button>
                    </div>
                </div>

                <!-- Cash Tender & Change Calculator -->
                <div x-show="paymentMethod === 'Cash'" class="bg-gray-50 p-3 rounded-2xl border border-gray-200/80 space-y-2">
                    <label class="block text-xs font-bold text-gray-700">Cash Tendered (Customer Paid)</label>
                    
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-500">₹</span>
                        <input type="number" step="1" x-model.number="cashTendered" placeholder="e.g. 500" 
                               class="w-full border-gray-300 rounded-xl text-sm font-bold py-1.5 px-3 focus:border-emerald-500">
                    </div>

                    <!-- Preset Cash Buttons -->
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <button type="button" @click="cashTendered = modalGrandTotal" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-[11px] font-bold hover:bg-gray-100">Exact</button>
                        <button type="button" @click="cashTendered = 100" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-[11px] font-bold hover:bg-gray-100">₹100</button>
                        <button type="button" @click="cashTendered = 200" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-[11px] font-bold hover:bg-gray-100">₹200</button>
                        <button type="button" @click="cashTendered = 500" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-[11px] font-bold hover:bg-gray-100">₹500</button>
                        <button type="button" @click="cashTendered = 2000" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-[11px] font-bold hover:bg-gray-100">₹2000</button>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-gray-200 text-xs font-bold">
                        <span class="text-gray-600">Change Return:</span>
                        <span :class="cashChange >= 0 ? 'text-emerald-700 text-sm font-black' : 'text-rose-600'" 
                              x-text="'₹' + cashChange.toFixed(2)">₹0.00</span>
                    </div>
                </div>

                <!-- Discount Amount -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Discount Amount (₹)</label>
                    <input type="number" min="0" step="1" x-model.number="discount" @input="calculateModalTotal()" 
                           placeholder="0.00" class="w-full border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" @click="confirmSettle()" :disabled="loading" 
                        class="flex-1 py-3.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-xl text-sm font-black transition shadow-md flex justify-center items-center gap-2">
                    <span x-show="!loading">✓ Print & Complete Bill</span>
                    <span x-show="loading" class="animate-spin text-xs">🌀</span>
                </button>
                <button type="button" @click="settleModalOpen = false" class="py-3.5 px-4 bg-gray-100 text-gray-700 rounded-xl text-xs font-bold hover:bg-gray-200">Cancel</button>
            </div>
        </div>
    </div>

</div>

<script>
function counterBilling() {
    return {
        activeTab: 'new',
        selectedCategory: 'all',
        searchQuery: '',
        
        // Tax Rates from Org
        cgstPercent: {{ $org->cgst_percent ?? 2.5 }},
        sgstPercent: {{ $org->sgst_percent ?? 2.5 }},

        // Cart / Order State
        editingOrderId: null,
        editingOrderNumber: '',
        customerName: '',
        cookingNotes: '',
        cart: [],
        
        // Modal State
        settleModalOpen: false,
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
            this.fetchActiveOrders();
            this.fetchCompletedOrders();
            
            // Auto refresh active queue every 20s
            setInterval(() => this.fetchActiveOrders(), 20000);
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

        clearCart() {
            if(confirm('Clear current items in cart?')) {
                this.resetCartForm();
            }
        },
        
        resetCartForm() {
            this.cart = [];
            this.customerName = '';
            this.cookingNotes = '';
            this.editingOrderId = null;
            this.editingOrderNumber = '';
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
            this.cookingNotes = order.special_notes || '';
            this.cart = order.items.map(i => ({
                id: i.menu_item_id,
                name: i.name_snapshot,
                price: parseFloat(i.price_snapshot),
                qty: i.quantity
            }));
            this.activeTab = 'new';
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

        confirmSettle() {
            if (this.orderToSettle === 'current_cart') {
                this.loading = true;
                const payload = {
                    order_id: this.editingOrderId,
                    customer_name: this.customerName || ('Token ' + Math.floor(100 + Math.random() * 900)),
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

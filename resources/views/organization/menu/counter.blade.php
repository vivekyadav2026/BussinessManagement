@extends('layouts.sme')

@section('title', 'Counter Billing Mode')

@section('content')
<div class="space-y-6" x-data="counterBilling()">
    
    <!-- Top Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-gray-100 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <span>🛒 Counter Billing Mode</span>
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Quick-service ordering & billing for non-dining outlets</p>
        </div>
        <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-xl">
            <button @click="activeTab = 'new'" :class="activeTab === 'new' ? 'bg-white shadow-sm font-bold text-indigo-600' : 'text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg text-sm transition">
                ➕ New Order
            </button>
            <button @click="activeTab = 'current'" :class="activeTab === 'current' ? 'bg-white shadow-sm font-bold text-indigo-600' : 'text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                ⏳ Current Orders <span class="bg-indigo-100 text-indigo-800 text-[10px] px-2 py-0.5 rounded-full" x-text="activeOrders.length">0</span>
            </button>
            <button @click="activeTab = 'completed'" :class="activeTab === 'completed' ? 'bg-white shadow-sm font-bold text-indigo-600' : 'text-gray-600 hover:bg-gray-200'" class="px-4 py-2 rounded-lg text-sm transition">
                ✅ Completed
            </button>
        </div>
    </div>

    <!-- NEW ORDER TAB -->
    <div x-show="activeTab === 'new'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Menu Grid -->
        <div class="lg:col-span-7 xl:col-span-8 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-b pb-3">
                <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1">
                    <button type="button" @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                        All Items
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" @click="selectedCategory = {{ $cat->id }}" :class="selectedCategory === {{ $cat->id }} ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
                <div class="w-full sm:w-48 shrink-0">
                    <input type="text" x-model="searchQuery" placeholder="🔍 Search food item..." class="w-full text-xs border-gray-300 rounded-xl py-1.5 px-3 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-[500px] overflow-y-auto p-1">
                @foreach($categories as $cat)
                    @foreach($cat->items as $item)
                        <div x-show="(selectedCategory === 'all' || selectedCategory === {{ $cat->id }}) && ('{{ strtolower($item->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($cat->name) }}'.includes(searchQuery.toLowerCase()))"
                            @click="addToCart({{ json_encode($item) }})"
                            class="bg-white border border-gray-200 hover:border-indigo-500 rounded-xl p-3 cursor-pointer transition transform hover:-translate-y-0.5 shadow-xs flex flex-col justify-between group">
                            <div>
                                <div class="text-xs font-bold text-gray-900 group-hover:text-indigo-600 transition line-clamp-2">{{ $item->name }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $cat->name }}</div>
                            </div>
                            <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100">
                                <span class="text-xs font-black text-indigo-700">₹{{ number_format($item->price, 2) }}</span>
                                <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center font-bold text-xs transition">+</span>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        <!-- Right: Cart & Billing -->
        <div class="lg:col-span-5 xl:col-span-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm sticky top-4 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-base text-gray-900 flex items-center gap-1.5">
                    🛒 <span x-text="editingOrderId ? 'Edit Order #' + editingOrderNumber : 'New Counter Order'"></span>
                </h3>
                <button type="button" @click="clearCart()" class="text-xs text-rose-500 hover:text-rose-700 font-semibold" x-show="cart.length > 0">Clear</button>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Customer Name / Token #</label>
                <input type="text" x-model="customerName" placeholder="e.g. Token 12 or John" class="w-full border-gray-300 rounded-lg text-sm py-2 px-3 focus:border-indigo-500 bg-white">
            </div>

            <div class="space-y-2 max-h-60 overflow-y-auto min-h-[200px] pr-1">
                <template x-if="cart.length === 0">
                    <div class="text-center py-10 text-gray-400 text-xs font-medium">
                        <p>No items in cart.</p>
                        <p class="text-[10px] text-gray-300 mt-1">Tap items from the left to add.</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50/80 border border-gray-100 text-xs">
                        <div class="flex-1 pr-2 min-w-0">
                            <div class="font-bold text-gray-800 truncate" x-text="item.name"></div>
                            <div class="text-[10px] text-gray-500 font-semibold">₹<span x-text="item.price.toFixed(2)"></span> x <span x-text="item.qty"></span></div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded bg-gray-200 hover:bg-gray-300 font-bold text-gray-700 flex items-center justify-center text-sm">-</button>
                            <span class="w-6 text-center font-bold text-sm" x-text="item.qty"></span>
                            <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded bg-gray-200 hover:bg-gray-300 font-bold text-gray-700 flex items-center justify-center text-sm">+</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="border-t border-gray-100 pt-3 space-y-1.5 text-xs">
                <div class="flex justify-between items-baseline pt-2 text-sm font-black text-gray-900">
                    <span>Total Amount</span>
                    <span class="text-xl text-indigo-700">₹<span x-text="cartTotal.toFixed(2)">0.00</span></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button type="button" @click="saveHoldOrder()" :disabled="cart.length === 0 || loading" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition flex justify-center items-center gap-1">
                    ⏳ Save / Hold
                </button>
                <button type="button" @click="openSettleModal()" :disabled="cart.length === 0 || loading" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex justify-center items-center gap-1">
                    💳 Bill & Complete
                </button>
            </div>
        </div>
    </div>

    <!-- CURRENT ORDERS TAB -->
    <div x-show="activeTab === 'current'" class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6" style="display: none;">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Live / Hold Orders</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            <!-- Quick Add New Order Card -->
            <div @click="resetCartForm(); activeTab = 'new'" class="border-2 border-dashed border-gray-300 bg-gray-50/50 rounded-xl p-4 flex flex-col items-center justify-center text-gray-500 hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50/50 cursor-pointer transition min-h-[140px] group shadow-sm">
                <div class="w-12 h-12 rounded-full bg-white border border-gray-200 group-hover:border-indigo-300 group-hover:bg-indigo-100 flex items-center justify-center mb-3 shadow-xs transition">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="font-bold text-sm tracking-wide">Start New Order</span>
            </div>

            <template x-for="order in activeOrders" :key="order.id">
                <div class="border border-gray-200 rounded-xl p-4 flex flex-col justify-between hover:border-indigo-300 hover:shadow-md transition">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-lg text-gray-900 truncate pr-2" x-text="order.customer_name"></span>
                            <span class="text-[10px] text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded" x-text="order.order_number"></span>
                        </div>
                        <div class="text-xs text-gray-500 mb-3" x-text="'Items: ' + order.items.length + ' | Total: ₹' + parseFloat(order.total).toFixed(2)"></div>
                    </div>
                    
                    <div class="flex gap-2 mt-auto pt-3 border-t border-gray-100">
                        <button type="button" @click="editOrder(order)" class="flex-1 py-2 bg-white border border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Add More
                        </button>
                        <button type="button" @click="openSettleModalForOrder(order)" class="flex-1 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white shadow-sm rounded-lg text-xs font-bold transition flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Bill Now
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- COMPLETED ORDERS TAB -->
    <div x-show="activeTab === 'completed'" class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6" style="display: none;">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-900">Today's Completed Counter Orders</h2>
            <button @click="fetchCompletedOrders()" class="text-xs text-indigo-600 font-bold hover:underline">Refresh</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-bold">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-xl">Order #</th>
                        <th class="px-4 py-3">Customer / Token</th>
                        <th class="px-4 py-3">Items</th>
                        <th class="px-4 py-3">Total Amount</th>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3 rounded-tr-xl">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-if="completedOrders.length === 0">
                        <tr><td colspan="6" class="text-center py-6 text-gray-400">No completed orders today.</td></tr>
                    </template>
                    <template x-for="order in completedOrders" :key="order.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs font-bold" x-text="order.order_number"></td>
                            <td class="px-4 py-3 font-bold text-gray-800" x-text="order.customer_name"></td>
                            <td class="px-4 py-3 text-xs" x-text="order.items.length + ' items'"></td>
                            <td class="px-4 py-3 font-bold text-emerald-600">₹<span x-text="parseFloat(order.total).toFixed(2)"></span></td>
                            <td class="px-4 py-3 text-xs" x-text="new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></td>
                            <td class="px-4 py-3">
                                <a :href="'/organization/menu/pos/orders/' + order.id + '/print-receipt'" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-bold text-xs">Print Bill</a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Settlement / Payment Modal -->
    <div x-show="settleModalOpen" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-lg text-gray-900">Checkout & Settle</h3>
                <button type="button" @click="settleModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
            </div>

            <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-3 text-center">
                <div class="text-xs text-indigo-700 font-medium">Final Amount Payable</div>
                <div class="text-3xl font-black text-indigo-900 mt-0.5">₹<span x-text="modalGrandTotal.toFixed(2)"></span></div>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Select Payment Method</label>
                    <select x-model="paymentMethod" class="w-full border-gray-300 rounded-xl text-sm font-bold py-2">
                        <option value="Cash">💵 Cash Payment</option>
                        <option value="UPI">📱 UPI / QR Code Direct</option>
                        <option value="Card">💳 Card Payment (POS Machine)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Discount Amount (₹)</label>
                    <input type="number" min="0" step="0.01" x-model.number="discount" @input="calculateModalTotal()" class="w-full border-gray-300 rounded-xl text-sm py-2 px-3">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" @click="confirmSettle()" :disabled="loading" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition shadow-xs">
                    ✓ Print Bill & Complete
                </button>
                <button type="button" @click="settleModalOpen = false" class="py-3 px-4 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold">Cancel</button>
            </div>
        </div>
    </div>

</div>

<script>
function counterBilling() {
    return {
        activeTab: 'new', // new, current, completed
        selectedCategory: 'all',
        searchQuery: '',
        
        // Cart / Order State
        editingOrderId: null,
        editingOrderNumber: '',
        customerName: '',
        cart: [],
        
        // Modal State
        settleModalOpen: false,
        paymentMethod: 'Cash',
        discount: 0,
        orderToSettle: null,
        modalGrandTotal: 0,
        
        // Data
        activeOrders: [],
        completedOrders: [],
        loading: false,

        init() {
            this.fetchActiveOrders();
            this.fetchCompletedOrders();
            
            // Auto refresh active orders every 30s
            setInterval(() => this.fetchActiveOrders(), 30000);
        },

        get cartTotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
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
            if(confirm('Clear current items?')) {
                this.resetCartForm();
            }
        },
        
        resetCartForm() {
            this.cart = [];
            this.customerName = '';
            this.editingOrderId = null;
            this.editingOrderNumber = '';
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
                customer_name: this.customerName,
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
                    alert(data.message || 'Error saving order.');
                }
            }).catch(err => { this.loading = false; });
        },

        editOrder(order) {
            this.editingOrderId = order.id;
            this.editingOrderNumber = order.order_number;
            this.customerName = order.customer_name;
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
            this.settleModalOpen = true;
        },

        openSettleModalForOrder(order) {
            this.orderToSettle = order;
            this.discount = 0;
            this.modalGrandTotal = parseFloat(order.total);
            this.settleModalOpen = true;
        },

        calculateModalTotal() {
            let base = this.orderToSettle === 'current_cart' ? this.cartTotal : parseFloat(this.orderToSettle.total);
            this.modalGrandTotal = Math.max(0, base - this.discount);
        },

        confirmSettle() {
            if (this.orderToSettle === 'current_cart') {
                // Save first, then settle
                this.loading = true;
                const payload = {
                    order_id: this.editingOrderId,
                    customer_name: this.customerName,
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
                        this.loading = false; alert(data.message);
                    }
                });
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
            });
        }
    }
}
</script>
@endsection

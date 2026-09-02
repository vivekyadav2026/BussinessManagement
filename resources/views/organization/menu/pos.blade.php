@extends('layouts.sme')

@section('title', 'Waiter POS & Table Billing')

@section('content')
<div class="space-y-6" x-data="waiterPos()">
    
    <!-- Top Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-gray-100 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <span>🍽️ Restro Waiter POS & Order Billing</span>
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Take table orders, send KOT to kitchen, and print instant customer receipts</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.menu.kitchen.index') }}" target="_blank" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                <span>👨‍🍳 Open Kitchen Screen (KOT)</span>
            </a>
            <a href="{{ route('organization.menu.tables.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">
                ⚙️ Manage Tables
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Tables & Menu Items (8 Cols) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-6">
            
            <!-- Table Selection Section -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <span>🪑 Select Table</span>
                        <span class="text-xs font-normal text-gray-400">({{ count($tables) }} Tables Total)</span>
                    </h2>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="setOrderType('Dine-in')" :class="orderType === 'Dine-in' ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-lg text-xs transition">Dine-in (Table)</button>
                        <button type="button" @click="setOrderType('Takeaway')" :class="orderType === 'Takeaway' ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-lg text-xs transition">Takeaway / Parcel</button>
                    </div>
                </div>

                <!-- Tables Cards Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3 max-h-60 overflow-y-auto p-1">
                    @forelse($tables as $t)
                        <button type="button" @click="selectTable({{ json_encode($t) }})" 
                            :class="selectedTableId === {{ $t->id }} ? 'ring-2 ring-indigo-600 border-indigo-600 shadow-md bg-white' : ''"
                            class="p-3 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between min-h-[105px] relative group overflow-hidden
                            {{ $t->is_occupied ? 'bg-gradient-to-b from-amber-50 to-amber-100/60 border-amber-300 text-amber-950 hover:border-amber-400 shadow-xs' : 'bg-gradient-to-b from-emerald-50 to-emerald-100/50 border-emerald-300 text-emerald-950 hover:border-emerald-400 shadow-xs' }}">
                            
                            <div class="flex items-start justify-between gap-1 w-full">
                                <span class="font-black text-sm tracking-tight text-gray-900 leading-snug truncate">{{ $t->name }}</span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full font-black uppercase tracking-wider shrink-0 shadow-2xs {{ $t->is_occupied ? 'bg-amber-200 text-amber-900 border border-amber-300' : 'bg-emerald-200 text-emerald-900 border border-emerald-300' }}">
                                    {{ $t->is_occupied ? 'Occupied' : 'Vacant' }}
                                </span>
                            </div>

                            @if($t->is_occupied && $t->active_order)
                                <div class="mt-2 pt-1.5 border-t border-amber-200/80 w-full space-y-0.5">
                                    <div class="text-[10px] text-amber-800 font-mono font-bold tracking-tight">#{{ $t->active_order->order_number }}</div>
                                    <div class="text-xs font-black text-amber-950 font-mono">₹{{ number_format($t->active_order->total, 2) }}</div>
                                </div>
                            @else
                                <div class="mt-2 pt-1.5 border-t border-emerald-200/80 text-[10px] text-emerald-700 font-bold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Available</span>
                                </div>
                            @endif
                        </button>
                    @empty
                        <div class="col-span-full text-center py-6 text-xs text-gray-400">No tables configured. Click "Manage Tables" to add tables.</div>
                    @endforelse
                </div>
            </div>

            <!-- Menu Categories & Food Items Section -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-4">
                
                <!-- Category Tabs & Search -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-b pb-3">
                    <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1">
                        <button type="button" @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                            All Items
                        </button>
                        @foreach($categories as $cat)
                            <button type="button" @click="selectedCategory = {{ $cat->id }}" :class="selectedCategory === {{ $cat->id }} ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                                {{ $cat->name }} ({{ count($cat->items) }})
                            </button>
                        @endforeach
                    </div>

                    <div class="w-full sm:w-48 shrink-0">
                        <input type="text" x-model="searchQuery" placeholder="🔍 Search food item..." class="w-full text-xs border-gray-300 rounded-xl py-1.5 px-3 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Food Items Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-[380px] overflow-y-auto p-1">
                    @foreach($categories as $cat)
                        @foreach($cat->items as $item)
                            <div x-show="(selectedCategory === 'all' || selectedCategory === {{ $cat->id }}) && ('{{ strtolower($item->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($cat->name) }}'.includes(searchQuery.toLowerCase()))"
                                @click="addToTicket({{ json_encode($item) }})"
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

        </div>

        <!-- Right Column: Order Ticket / Billing Cart (5 Cols) -->
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm sticky top-4 space-y-4">
                
                <!-- Ticket Header -->
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="font-bold text-base text-gray-900 flex items-center gap-1.5">
                            <span x-text="orderType === 'Dine-in' ? (selectedTableName ? 'Table: ' + selectedTableName : 'Select Table') : 'Takeaway Order'"></span>
                        </h3>
                        <div class="text-[11px] text-gray-400 font-mono mt-0.5" x-show="activeOrderId">Order #: <span x-text="activeOrderNumber" class="font-bold text-gray-700"></span></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="cancelOrder()" class="px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white rounded-lg text-xs font-bold border border-rose-200 transition flex items-center gap-1 shadow-2xs" x-show="activeOrderId">
                            <span>🚫 Cancel Order</span>
                        </button>
                        <button type="button" @click="clearTicket()" class="text-xs text-gray-500 hover:text-gray-700 font-semibold" x-show="cart.length > 0 && !activeOrderId">Clear</button>
                    </div>
                </div>

                <!-- Customer Details Input -->
                <div class="grid grid-cols-2 gap-2 text-xs bg-gray-50/80 p-2.5 rounded-xl border border-gray-200/60">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">👤 Guest Name</label>
                        <input type="text" x-model="customerName" placeholder="e.g. Abhishek" class="w-full border-gray-300 rounded-lg text-xs py-1.5 px-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">📞 Phone Number</label>
                        <input type="text" x-model="customerPhone" placeholder="e.g. 9876543210" class="w-full border-gray-300 rounded-lg text-xs py-1.5 px-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white font-mono">
                    </div>
                </div>

                <!-- Order Ticket Items List -->
                <div class="space-y-2 max-h-60 overflow-y-auto min-h-[140px] pr-1">
                    <template x-if="cart.length === 0">
                        <div class="text-center py-10 text-gray-400 text-xs font-medium">
                            <p>No items added to ticket.</p>
                            <p class="text-[10px] text-gray-300 mt-1">Tap any food item from the menu grid to add.</p>
                        </div>
                    </template>

                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50/80 border border-gray-100 text-xs">
                            <div class="flex-1 pr-2 min-w-0">
                                <div class="font-bold text-gray-800 truncate" x-text="item.name"></div>
                                <div class="text-[10px] text-gray-500 font-semibold">₹<span x-text="item.price.toFixed(2)"></span> x <span x-text="item.qty"></span></div>
                            </div>

                            <div class="flex items-center gap-1">
                                <button type="button" @click="updateQty(index, -1)" class="w-5 h-5 rounded bg-gray-200 hover:bg-gray-300 font-bold text-gray-700 flex items-center justify-center text-xs">-</button>
                                <span class="w-6 text-center font-bold text-xs" x-text="item.qty"></span>
                                <button type="button" @click="updateQty(index, 1)" class="w-5 h-5 rounded bg-gray-200 hover:bg-gray-300 font-bold text-gray-700 flex items-center justify-center text-xs">+</button>
                                <button type="button" @click="removeItem(index)" class="ml-1 text-rose-500 hover:text-rose-700 font-bold p-0.5">✕</button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Cooking / Kitchen Special Notes -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Cooking / Kitchen Instructions</label>
                    <input type="text" x-model="cookingNotes" placeholder="e.g. Extra spicy, Less oil, Make parcel..." class="w-full text-xs border-gray-300 rounded-lg py-1 px-2.5 bg-gray-50/50">
                </div>

                <!-- Bill Totals -->
                <div class="border-t border-gray-100 pt-3 space-y-1.5 text-xs">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-bold text-gray-800">₹<span x-text="subtotal.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between text-gray-600" x-show="discount > 0">
                        <span>Discount</span>
                        <span class="font-bold text-rose-600">-₹<span x-text="discount.toFixed(2)">0.00</span></span>
                    </div>
                    <div class="flex justify-between items-baseline pt-2 border-t text-sm font-black text-gray-900">
                        <span>Grand Total</span>
                        <span class="text-lg text-indigo-700">₹<span x-text="grandTotal.toFixed(2)">0.00</span></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2 pt-2">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="saveOrder('kot')" :disabled="loading || cart.length === 0" class="w-full py-2.5 px-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 shadow-xs disabled:opacity-50">
                            <span>👨‍🍳 Send KOT</span>
                        </button>

                        <button type="button" @click="openSettleModal()" :disabled="loading || cart.length === 0" class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 shadow-xs disabled:opacity-50">
                            <span>💳 Settle & Bill</span>
                        </button>
                    </div>

                    <div class="flex gap-2" x-show="activeOrderId">
                        <a :href="activeKotUrl" target="_blank" class="flex-1 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-center rounded-lg text-xs font-bold transition">
                            🖨️ Print KOT Slip
                        </a>
                        <a :href="activeReceiptUrl" target="_blank" class="flex-1 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-center rounded-lg text-xs font-bold transition">
                            🧾 Customer Receipt
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Settlement / Payment Modal -->
    <div x-show="settleModalOpen" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-lg text-gray-900">Payment & Bill Settlement</h3>
                <button type="button" @click="settleModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
            </div>

            <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-3 text-center">
                <div class="text-xs text-indigo-700 font-medium">Final Amount Payable</div>
                <div class="text-3xl font-black text-indigo-900 mt-0.5">₹<span x-text="grandTotal.toFixed(2)"></span></div>
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
                    <input type="number" min="0" step="0.01" x-model.number="discount" @input="calculateTotals()" class="w-full border-gray-300 rounded-xl text-sm py-2 px-3">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" @click="confirmSettle()" :disabled="loading" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition shadow-xs">
                    ✓ Confirm Payment & Print Bill
                </button>
                <button type="button" @click="settleModalOpen = false" class="py-3 px-4 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold">Cancel</button>
            </div>
        </div>
    </div>

</div>

<script>
function waiterPos() {
    return {
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
        grandTotal: 0,
        activeOrderId: null,
        activeOrderNumber: '',
        activeKotUrl: '#',
        activeReceiptUrl: '#',
        settleModalOpen: false,
        paymentMethod: 'Cash',
        loading: false,

        init() {
            // Select first available table by default
            const firstTable = @json($tables->first());
            if (firstTable) {
                this.selectTable(firstTable);
            }
        },

        selectTable(table) {
            this.selectedTableId = table.id;
            this.selectedTableName = table.name;
            this.orderType = 'Dine-in';

            if (table.active_order) {
                this.activeOrderId = table.active_order.id;
                this.activeOrderNumber = table.active_order.order_number;
                this.customerName = table.active_order.customer_name || '';
                this.customerPhone = table.active_order.customer_phone || '';
                this.cookingNotes = table.active_order.notes || '';
                this.activeKotUrl = `/organization/menu/pos/orders/${table.active_order.id}/print-kot`;
                this.activeReceiptUrl = `/organization/menu/pos/orders/${table.active_order.id}/print-receipt`;

                this.cart = table.active_order.items.map(i => ({
                    id: i.menu_item_id,
                    name: i.name_snapshot,
                    price: parseFloat(i.price_snapshot),
                    qty: i.quantity
                }));
            } else {
                this.clearTicketData();
            }

            this.calculateTotals();
        },

        setOrderType(type) {
            this.orderType = type;
            if (type === 'Takeaway') {
                this.selectedTableId = null;
                this.selectedTableName = '';
                this.clearTicketData();
            }
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

        removeItem(index) {
            this.cart.splice(index, 1);
            this.calculateTotals();
        },

        clearTicket() {
            if (confirm('Clear current ticket items?')) {
                this.clearTicketData();
                this.calculateTotals();
            }
        },

        clearTicketData() {
            this.cart = [];
            this.customerName = '';
            this.customerPhone = '';
            this.cookingNotes = '';
            this.activeOrderId = null;
            this.activeOrderNumber = '';
            this.activeKotUrl = '';
            this.activeReceiptUrl = '';
            this.discount = 0;
        },

        calculateTotals() {
            this.subtotal = this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            this.grandTotal = Math.max(0, this.subtotal - this.discount);
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
                        this.activeOrderId = data.order.id;
                        this.activeOrderNumber = data.order.order_number;
                        this.activeKotUrl = data.print_kot_url;

                        // Reload page to reflect updated table status silently without opening print pop-up
                        window.location.reload();
                    } else {
                    alert(data.message || 'Error saving order.');
                }
            })
            .catch(err => {
                this.loading = false;
                alert('An error occurred.');
                console.error(err);
            });
        },

        openSettleModal() {
            if (this.cart.length === 0) return;
            this.settleModalOpen = true;
        },

        confirmSettle() {
            if (!this.activeOrderId) {
                // First save order, then settle
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
                        this.activeOrderId = data.order.id;
                        this.executeSettle();
                    } else {
                        this.loading = false;
                        alert(data.message || 'Error saving order.');
                    }
                });
            } else {
                this.executeSettle();
            }
        },

        executeSettle() {
            this.loading = true;
            fetch(`/organization/menu/pos/orders/${this.activeOrderId}/settle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: this.paymentMethod,
                    discount: this.discount
                })
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                if (data.success) {
                    this.settleModalOpen = false;
                    this.clearTicketData();
                    window.open(data.print_receipt_url, '_blank');
                    window.location.reload();
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
            if (!this.activeOrderId) return;

            if (confirm(`Are you sure you want to cancel Order #${this.activeOrderNumber}? This will vacate the table.`)) {
                this.loading = true;
                fetch(`/organization/menu/pos/orders/${this.activeOrderId}/cancel`, {
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
                        this.clearTicketData();
                        window.location.reload();
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

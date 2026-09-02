@extends('layouts.sme')

@push('styles')
<style>
    .cart-grid { display: grid; grid-template-columns: 2fr 1.2fr 1.5fr 1.2fr 40px; gap: 10px; align-items: center; }
</style>
@endpush

@section('content')
<div class="dash-head mb-6">
  <a href="{{ route('organization.invoices.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Invoices</a>
  <h1 class="text-2xl font-bold text-gray-900">Create Invoice</h1>
</div>

<!-- Alert Banner Container -->
<div id="invoiceErrorBanner" class="hidden mb-6 bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl text-sm font-semibold shadow-sm flex items-start justify-between">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span id="errorMessageText">An error occurred while generating invoice.</span>
    </div>
    <button type="button" onclick="document.getElementById('invoiceErrorBanner').classList.add('hidden')" class="text-rose-400 hover:text-rose-600 font-bold">&times;</button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <!-- Billing Details -->
        <div class="panel mb-6 shadow-sm p-6 bg-white rounded-xl border border-gray-100">
            <h3 class="font-bold border-b pb-2 mb-4 text-gray-800">Billing Details</h3>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div id="clientSearchGroup" class="relative">
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Select Client</label>
                        <button type="button" onclick="openQuickClientModal()" class="text-xs text-indigo-600 hover:text-indigo-900 font-semibold">+ Quick Add Client</button>
                    </div>
                    <input type="text" id="clientSearch" placeholder="Search client by name or phone (or leave blank for Walk-in)..." class="w-full border-gray-300 rounded-lg text-sm" value="{{ request('client_id') ? \App\Models\Client::find(request('client_id'))->name ?? '' : '' }}" autocomplete="off">
                    <input type="hidden" id="clientId" value="{{ request('client_id', '') }}">
                    <div id="clientDropdown" class="absolute z-20 w-full bg-white border border-gray-200 mt-1 rounded-lg shadow-lg hidden max-h-48 overflow-y-auto"></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Invoice Date</label>
                    <input type="date" id="invoiceDate" value="{{ now()->toDateString() }}" class="w-full border-gray-300 rounded-lg text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Internal Notes</label>
                <input type="text" id="invoiceNotes" placeholder="Optional internal reference notes..." class="w-full border-gray-300 rounded-lg text-sm">
            </div>
        </div>

        <!-- Items -->
        <div class="panel p-6 bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-end border-b pb-2 mb-4">
                <h3 class="font-bold">Invoice Items</h3>
                <div id="productSearchGroup" class="w-72 relative">
                    <input type="text" id="productSearch" placeholder="Search product name, SKU or scan barcode..." class="w-full border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-indigo-500" autocomplete="off">
                    <div id="productDropdown" class="absolute z-20 w-full bg-white border border-gray-200 mt-1 rounded-lg shadow-xl hidden max-h-64 overflow-y-auto"></div>
                </div>
            </div>

            <div class="cart-grid border-b pb-2 mb-3 text-xs font-bold text-gray-500 uppercase tracking-wide">
                <div>Product</div>
                <div>Price (₹)</div>
                <div>Qty</div>
                <div class="text-right">Total (₹)</div>
                <div></div>
            </div>

            <div id="cartItems" class="space-y-2 mb-4 min-h-[100px]">
                <div id="emptyCart" class="text-center text-gray-400 py-8 text-sm">No items added yet. Type in search bar or scan barcode above to add items.</div>
            </div>
        </div>
    </div>

    <!-- Summary Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <div class="panel bg-white p-6 shadow-sm border border-gray-100 rounded-xl">
            <h3 class="font-bold border-b pb-3 mb-4 text-gray-800 text-base">Invoice Summary</h3>
            
            <div class="space-y-3 mb-4 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="font-bold text-gray-900">₹<span id="sumSubtotal">0.00</span></span>
                </div>
                
                <div class="flex justify-between">
                    <span>Total Tax</span>
                    <span class="font-bold text-gray-900">₹<span id="sumTax">0.00</span></span>
                </div>

                <div class="pt-2 pb-3 border-b border-gray-100 space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Discount Type</label>
                        <select id="discountType" onchange="calculateTotals()" class="w-36 border-gray-300 rounded-lg text-xs font-bold py-1.5 px-2.5 bg-gray-50 focus:bg-white text-gray-800">
                            <option value="fixed">₹ Flat (Rupees)</option>
                            <option value="percent">% Percentage (%)</option>
                        </select>
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Discount Value</label>
                        <input type="number" id="sumDiscount" value="0" min="0" step="0.01" placeholder="0" class="w-36 text-right border-gray-300 rounded-lg text-sm py-1.5 px-2.5 bg-gray-50 focus:bg-white font-bold text-gray-800" oninput="calculateTotals()">
                    </div>
                    <div id="discountConvertedRow" class="hidden text-right text-xs text-indigo-600 font-bold bg-indigo-50/70 py-1 px-2.5 rounded-lg border border-indigo-100">
                        Discount Amount: -₹<span id="sumDiscountCalculated">0.00</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-baseline mb-6">
                <span class="text-sm font-semibold text-gray-700">Grand Total</span>
                <span class="text-2xl font-black text-gray-900">₹<span id="sumGrandTotal">0.00</span></span>
            </div>

            <div class="space-y-4 border-t border-gray-100 pt-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Payment Received (₹)</label>
                    <input type="number" id="sumPaid" value="0" min="0" step="0.01" class="w-full border-gray-300 rounded-lg text-lg font-bold bg-gray-50 focus:bg-white text-gray-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Save As Status</label>
                    <select id="invoiceStatus" class="w-full border-gray-300 rounded-lg font-medium text-sm">
                        <option value="Paid">✓ Paid (Full Payment Received)</option>
                        <option value="Due">⏳ Due (Unpaid / Credit Sale)</option>
                        <option value="Partially Paid">🌗 Partially Paid</option>
                        <option value="Draft">📝 Draft (Save without deducting stock)</option>
                    </select>
                </div>
            </div>

            <button onclick="submitInvoice()" id="btnSubmit" class="btn btn-gold w-full justify-center py-3 text-base shadow-sm">Complete & Save Invoice</button>
        </div>
    </div>
</div>

<!-- Quick Add Client Modal -->
<div id="quickClientModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeQuickClientModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Quick Add Client</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Client Name <span class="text-red-500">*</span></label>
                        <input type="text" id="modalClientName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Enter name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" id="modalClientPhone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Enter phone number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="modalClientEmail" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Enter email (optional)">
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                <button type="button" onclick="submitQuickClient()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Save Client</button>
                <button type="button" onclick="closeQuickClientModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];

const clientSearch = document.getElementById('clientSearch');
const clientDropdown = document.getElementById('clientDropdown');
const clientId = document.getElementById('clientId');
const productSearch = document.getElementById('productSearch');
const productDropdown = document.getElementById('productDropdown');

// Client Search
clientSearch.addEventListener('input', function() {
    let q = this.value.trim();
    if(q.length < 1) { fetchClients(''); return; }
    fetchClients(q);
});

clientSearch.addEventListener('focus', function() {
    fetchClients(this.value.trim());
});

clientSearch.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        let q = this.value.trim();
        
        // If dropdown has real matching clients
        let firstClientItem = clientDropdown.querySelector('[data-client-id]');
        if (firstClientItem && q.length > 0) {
            firstClientItem.dispatchEvent(new Event('mousedown'));
            return;
        }
        
        // Otherwise, automatically trigger Quick Add Modal with pre-filled value
        openQuickClientModalWithPrefill(q);
    }
});

function fetchClients(q) {
    fetch(`/organization/clients/search?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
            clientDropdown.innerHTML = '';
            if(data.length === 0) {
                if(q.length > 0) {
                    let safeQ = q.replace(/'/g, "\\'");
                    clientDropdown.innerHTML = `
                        <div class="p-3 bg-indigo-50/80 border-t border-indigo-100 text-center space-y-2 rounded-b-lg">
                            <div class="text-xs text-gray-500 font-medium">No client matching "<b class="text-gray-900">${q}</b>"</div>
                            <button type="button" onmousedown="openQuickClientModalWithPrefill('${safeQ}')" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-2 px-3 rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-xs">
                                <span>➕ Add "${q}" as New Client</span>
                                <kbd class="bg-indigo-800/60 text-[10px] px-1.5 py-0.5 rounded font-mono">ENTER ↵</kbd>
                            </button>
                        </div>
                    `;
                } else {
                    clientDropdown.innerHTML = `
                        <div class="p-3 text-xs text-gray-500 text-center space-y-2">
                            <div>No client selected.</div>
                            <button type="button" onmousedown="openQuickClientModalWithPrefill('')" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-2 px-3 rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-xs">
                                <span>➕ Add New Client</span>
                            </button>
                        </div>
                    `;
                }
            } else {
                data.forEach(c => {
                    let div = document.createElement('div');
                    div.setAttribute('data-client-id', c.id);
                    div.className = 'p-2.5 hover:bg-indigo-50 cursor-pointer text-sm border-b border-gray-100 last:border-0';
                    div.innerHTML = `<div class="font-bold text-gray-800">${c.name}</div><div class="text-xs text-gray-500">${c.phone || 'No phone'}</div>`;
                    div.onmousedown = (e) => {
                        e.preventDefault();
                        clientSearch.value = c.name;
                        clientId.value = c.id;
                        clientDropdown.classList.add('hidden');
                    };
                    clientDropdown.appendChild(div);
                });

                if (q.length > 0) {
                    let safeQ = q.replace(/'/g, "\\'");
                    let addOption = document.createElement('div');
                    addOption.className = 'p-2.5 bg-indigo-50/90 hover:bg-indigo-100 cursor-pointer text-xs font-bold text-indigo-900 border-t border-indigo-100 flex items-center justify-between gap-2 transition rounded-b-lg';
                    addOption.innerHTML = `
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="text-indigo-600 font-black text-sm shrink-0">➕</span>
                            <span class="truncate">Add "<span class="text-indigo-700 font-extrabold">${q}</span>"</span>
                        </div>
                        <span class="bg-indigo-600 text-white px-2 py-1 rounded-md text-[10px] font-bold shrink-0 shadow-xs flex items-center gap-1">
                            <span>+ Add</span>
                            <kbd class="bg-indigo-800/60 text-[9px] px-1 rounded">↵</kbd>
                        </span>
                    `;
                    addOption.onmousedown = (e) => {
                        e.preventDefault();
                        openQuickClientModalWithPrefill(safeQ);
                    };
                    clientDropdown.appendChild(addOption);
                }
            }
            clientDropdown.classList.remove('hidden');
        });
}




// Product Search
productSearch.addEventListener('input', function() {
    let q = this.value.trim();
    fetchProducts(q);
});

productSearch.addEventListener('focus', function() {
    fetchProducts(this.value.trim());
});

function fetchProducts(q) {
    fetch(`/organization/invoices/products/search?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
            productDropdown.innerHTML = '';
            if(data.length === 0) {
                productDropdown.innerHTML = '<div class="p-3 text-xs text-gray-400 text-center">No products found</div>';
            } else {
                data.forEach(p => {
                    let div = document.createElement('div');
                    div.className = 'p-2.5 hover:bg-indigo-50 cursor-pointer text-sm border-b border-gray-100 last:border-0 flex justify-between items-center';
                    div.innerHTML = `
                        <div>
                            <div class="font-bold text-gray-900">${p.name}</div>
                            <div class="text-xs text-gray-400 font-mono">SKU: ${p.sku}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-indigo-700">₹${parseFloat(p.selling_price).toFixed(2)}</div>
                            <div class="text-[11px] ${p.current_stock > 0 ? 'text-emerald-600 font-semibold' : 'text-rose-500 font-bold'}">Stock: ${p.current_stock}</div>
                        </div>
                    `;
                    div.onmousedown = (e) => {
                        e.preventDefault();
                        addToCart(p);
                        productSearch.value = '';
                        productDropdown.classList.add('hidden');
                        productSearch.focus();
                    };
                    productDropdown.appendChild(div);
                });
            }
            productDropdown.classList.remove('hidden');
        });
}

// Global click outside listener
document.addEventListener('click', function(e) {
    if (!document.getElementById('clientSearchGroup').contains(e.target)) {
        clientDropdown.classList.add('hidden');
    }
    if (!document.getElementById('productSearchGroup').contains(e.target)) {
        productDropdown.classList.add('hidden');
    }
});

// Barcode scanner enter key & Instant Add
productSearch.addEventListener('keydown', function(e) {
    if(e.key === 'Enter') {
        e.preventDefault();
        let q = this.value.trim();
        if(!q) return;

        // Prevent double trigger if dropdown was already rendering
        productDropdown.innerHTML = '<div class="p-3 text-xs text-indigo-500 font-bold text-center animate-pulse">Scanning...</div>';
        productDropdown.classList.remove('hidden');

        fetch(`/organization/invoices/products/search?q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(data => {
                if(data.length === 1 || (data.length > 0 && (data[0].barcode === q || data[0].sku === q))) {
                    addToCart(data[0]);
                    productSearch.value = '';
                    productDropdown.classList.add('hidden');
                    productSearch.focus();
                } else if(data.length > 0) {
                    addToCart(data[0]);
                    productSearch.value = '';
                    productDropdown.classList.add('hidden');
                    productSearch.focus();
                } else {
                    productDropdown.innerHTML = '<div class="p-3 text-xs text-rose-500 text-center font-bold">No product found for "'+q+'"</div>';
                    productSearch.select(); // Select text so they can scan again immediately
                }
            })
            .catch(err => {
                productDropdown.innerHTML = '<div class="p-3 text-xs text-red-500 text-center">Scan failed</div>';
            });
    }
});

function addToCart(product) {
    let existing = cart.find(i => i.id === product.id);
    if(existing) {
        existing.qty++;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: parseFloat(product.selling_price),
            taxRate: parseFloat(product.tax_rate || 0),
            qty: 1,
            maxStock: product.current_stock
        });
    }
    renderCart();
}

function updateQty(id, qty) {
    let item = cart.find(i => i.id === id);
    if(item) {
        item.qty = parseInt(qty) || 1;
        renderCart();
    }
}

function adjustQty(id, change) {
    let item = cart.find(i => i.id === id);
    if(item) {
        item.qty = Math.max(1, item.qty + change);
        renderCart();
    }
}

function removeCart(id) {
    cart = cart.filter(i => i.id !== id);
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    
    if(cart.length === 0) {
        container.innerHTML = '<div id="emptyCart" class="text-center text-gray-400 py-8 text-sm">No items added yet. Type in search bar or scan barcode above to add items.</div>';
        calculateTotals();
        return;
    }
    
    container.innerHTML = '';
    
    cart.forEach(item => {
        let taxAmt = (item.price * (item.taxRate / 100));
        let total = (item.price + taxAmt) * item.qty;
        
        let row = document.createElement('div');
        row.className = 'cart-grid border border-gray-100 bg-white rounded-lg p-2.5 shadow-xs text-sm items-center';
        row.innerHTML = `
            <div class="font-medium text-gray-900">${item.name} ${item.qty > item.maxStock ? '<span class="text-[10px] bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded font-bold ml-1">Low Stock (' + item.maxStock + ')</span>' : ''}</div>
            <div class="font-semibold text-gray-700">₹${item.price.toFixed(2)}</div>
            <div class="flex items-center gap-1">
                <button type="button" onclick="adjustQty(${item.id}, -1)" class="w-6 h-6 flex items-center justify-center border border-gray-300 rounded bg-gray-50 hover:bg-gray-100 font-bold text-gray-600">-</button>
                <input type="number" min="1" value="${item.qty}" class="w-12 border-gray-300 rounded py-0.5 px-1 text-center text-sm font-bold" onchange="updateQty(${item.id}, this.value)">
                <button type="button" onclick="adjustQty(${item.id}, 1)" class="w-6 h-6 flex items-center justify-center border border-gray-300 rounded bg-gray-50 hover:bg-gray-100 font-bold text-gray-600">+</button>
            </div>
            <div class="text-right font-bold text-indigo-900">₹${total.toFixed(2)}</div>
            <div class="text-right">
                <button class="text-gray-400 hover:text-rose-600 p-1" onclick="removeCart(${item.id})" title="Remove item">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        `;
        container.appendChild(row);
    });
    
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    let tax = 0;
    
    cart.forEach(item => {
        let base = item.price * item.qty;
        let t = base * (item.taxRate / 100);
        subtotal += base;
        tax += t;
    });
    
    let discountType = document.getElementById('discountType').value;
    let discountInput = parseFloat(document.getElementById('sumDiscount').value) || 0;
    let calculatedDiscount = 0;

    if (discountType === 'percent') {
        calculatedDiscount = (subtotal + tax) * (discountInput / 100);
        document.getElementById('discountConvertedRow').classList.remove('hidden');
        document.getElementById('sumDiscountCalculated').textContent = calculatedDiscount.toFixed(2);
    } else {
        calculatedDiscount = discountInput;
        document.getElementById('discountConvertedRow').classList.add('hidden');
    }

    let grandTotal = subtotal + tax - calculatedDiscount;
    if(grandTotal < 0) grandTotal = 0;
    
    document.getElementById('sumSubtotal').textContent = subtotal.toFixed(2);
    document.getElementById('sumTax').textContent = tax.toFixed(2);
    document.getElementById('sumGrandTotal').textContent = grandTotal.toFixed(2);
    
    let status = document.getElementById('invoiceStatus').value;
    if(status === 'Paid') {
        document.getElementById('sumPaid').value = grandTotal.toFixed(2);
    }
}

document.getElementById('invoiceStatus').addEventListener('change', calculateTotals);

// Quick Add Client Functions
function openQuickClientModal() {
    openQuickClientModalWithPrefill(clientSearch.value.trim());
}

function openQuickClientModalWithPrefill(prefill) {
    clientDropdown.classList.add('hidden');
    document.getElementById('modalClientName').value = '';
    document.getElementById('modalClientPhone').value = '';
    document.getElementById('modalClientEmail').value = '';

    if (prefill) {
        let isPhone = /^[0-9+\s\-]{5,15}$/.test(prefill);
        if (isPhone) {
            document.getElementById('modalClientPhone').value = prefill;
        } else {
            document.getElementById('modalClientName').value = prefill;
        }
    }
    document.getElementById('quickClientModal').classList.remove('hidden');
    setTimeout(() => {
        if (document.getElementById('modalClientName').value === '') {
            document.getElementById('modalClientName').focus();
        } else {
            document.getElementById('modalClientPhone').focus();
        }
    }, 100);
}

function closeQuickClientModal() {
    document.getElementById('quickClientModal').classList.add('hidden');
}

['modalClientName', 'modalClientPhone', 'modalClientEmail'].forEach(id => {
    const el = document.getElementById(id);
    if(el) {
        el.addEventListener('keydown', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                submitQuickClient();
            }
        });
    }
});



function submitQuickClient() {
    let name = document.getElementById('modalClientName').value.trim();
    let phone = document.getElementById('modalClientPhone').value.trim();
    let email = document.getElementById('modalClientEmail').value.trim();

    if(!name && phone) {
        name = "Client " + phone;
        document.getElementById('modalClientName').value = name;
    }

    if(!name) {
        alert("Please enter Client Name or Phone Number.");
        document.getElementById('modalClientName').focus();
        return;
    }

    fetch('{{ route("organization.clients.quick-store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ name, phone, email })
    })
    .then(async res => {
        const data = await res.json();
        if(res.ok && data.success) {
            clientId.value = data.client.id;
            clientSearch.value = data.client.name;
            closeQuickClientModal();
        } else {
            let errorMsg = data.message || "Error saving client.";
            if(data.errors) {
                errorMsg = Object.values(data.errors).flat().join(" ");
            }
            alert(errorMsg);
        }
    })
    .catch(err => {
        alert("An error occurred saving the client.");
        console.error(err);
    });
}


function showErrorBanner(msg) {
    const banner = document.getElementById('invoiceErrorBanner');
    document.getElementById('errorMessageText').textContent = msg;
    banner.classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function submitInvoice() {
    document.getElementById('invoiceErrorBanner').classList.add('hidden');

    if(cart.length === 0) {
        showErrorBanner("Please add at least one product to the invoice.");
        return;
    }
    
    const clientVal = clientId.value;
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.textContent = 'Processing & Generating Invoice...';
    
    let discountType = document.getElementById('discountType').value;
    let discountInput = parseFloat(document.getElementById('sumDiscount').value) || 0;
    let subtotalVal = parseFloat(document.getElementById('sumSubtotal').textContent) || 0;
    let taxVal = parseFloat(document.getElementById('sumTax').textContent) || 0;
    let finalDiscount = discountType === 'percent' ? ((subtotalVal + taxVal) * (discountInput / 100)) : discountInput;

    const payload = {
        client_id: clientVal || null,
        invoice_date: document.getElementById('invoiceDate').value,
        notes: document.getElementById('invoiceNotes').value,
        discount: finalDiscount,
        discount_type: discountType,
        discount_value: discountInput,
        amount_paid: parseFloat(document.getElementById('sumPaid').value) || 0,
        status: document.getElementById('invoiceStatus').value,
        items: cart.map(i => ({ product_id: i.id, quantity: i.qty }))
    };
    
    fetch('{{ route("organization.invoices.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const data = await res.json();
        if(res.ok && data.success) {
            window.location.href = data.redirect;
        } else {
            let errorMsg = data.message || "Failed to create invoice.";
            if(data.errors) {
                errorMsg = Object.values(data.errors).flat().join(" ");
            }
            showErrorBanner(errorMsg);
            btn.disabled = false;
            btn.textContent = 'Complete & Save Invoice';
        }
    })
    .catch(err => {
        showErrorBanner("A server error occurred. Please try again.");
        console.error(err);
        btn.disabled = false;
        btn.textContent = 'Complete & Save Invoice';
    });
}
</script>
@endsection

@extends('layouts.sme')

@push('styles')
<style>
    .cart-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 40px; gap: 10px; align-items: center; }
</style>
@endpush

@section('content')
<div class="dash-head mb-6">
  <a href="{{ route('organization.invoices.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Invoices</a>
  <h1 class="text-2xl font-bold text-gray-900">Create Invoice</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <!-- Billing Details -->
        <div class="panel mb-6">
            <h3 class="font-bold border-b pb-2 mb-4">Billing Details</h3>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Client</label>
                    <div class="relative">
                        <input type="text" id="clientSearch" placeholder="Search client by name or phone..." class="w-full border-gray-300 rounded-lg text-sm" value="{{ request('client_id') ? \App\Models\Client::find(request('client_id'))->name ?? '' : '' }}" autocomplete="off">
                        <input type="hidden" id="clientId" value="{{ request('client_id', '') }}">
                        <div id="clientDropdown" class="absolute z-10 w-full bg-white border border-gray-200 mt-1 rounded-lg shadow-lg hidden max-h-48 overflow-y-auto"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Invoice Date</label>
                    <input type="date" id="invoiceDate" value="{{ now()->toDateString() }}" class="w-full border-gray-300 rounded-lg text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Internal Notes</label>
                <input type="text" id="invoiceNotes" placeholder="Optional notes..." class="w-full border-gray-300 rounded-lg text-sm">
            </div>
        </div>

        <!-- Items -->
        <div class="panel">
            <div class="flex justify-between items-end border-b pb-2 mb-4">
                <h3 class="font-bold">Invoice Items</h3>
                <div class="w-64 relative">
                    <input type="text" id="productSearch" placeholder="Scan barcode or search..." class="w-full border-gray-300 rounded-lg text-sm" autocomplete="off">
                    <div id="productDropdown" class="absolute z-10 w-full bg-white border border-gray-200 mt-1 rounded-lg shadow-lg hidden max-h-64 overflow-y-auto"></div>
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
                <div id="emptyCart" class="text-center text-gray-400 py-6 text-sm">No items added yet. Search or scan to add products.</div>
            </div>
        </div>
    </div>

    <!-- Summary Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <div class="panel bg-gray-50 border-2 border-indigo-50">
            <h3 class="font-bold border-b pb-2 mb-4 text-indigo-900">Summary</h3>
            
            <div class="flex justify-between mb-2 text-sm text-gray-600">
                <span>Subtotal</span>
                <span class="font-bold text-gray-900">₹<span id="sumSubtotal">0.00</span></span>
            </div>
            
            <div class="flex justify-between mb-2 text-sm text-gray-600">
                <span>Total Tax</span>
                <span class="font-bold text-gray-900">₹<span id="sumTax">0.00</span></span>
            </div>

            <div class="flex justify-between items-center mb-4 text-sm text-gray-600 border-b pb-4">
                <span>Discount (₹)</span>
                <input type="number" id="sumDiscount" value="0" min="0" step="0.01" class="w-24 text-right border-gray-300 rounded-md text-sm py-1" oninput="calculateTotals()">
            </div>

            <div class="flex justify-between mb-6 text-lg font-black text-indigo-900">
                <span>Grand Total</span>
                <span>₹<span id="sumGrandTotal">0.00</span></span>
            </div>

            <hr class="mb-4">

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Payment Received (₹)</label>
                <input type="number" id="sumPaid" value="0" min="0" step="0.01" class="w-full border-gray-300 rounded-lg text-lg font-bold">
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Save As</label>
                <select id="invoiceStatus" class="w-full border-gray-300 rounded-lg font-medium">
                    <option value="Paid">Paid</option>
                    <option value="Due">Due</option>
                    <option value="Partially Paid">Partially Paid</option>
                    <option value="Draft">Draft (Do not deduct stock)</option>
                </select>
            </div>

            <button onclick="submitInvoice()" id="btnSubmit" class="btn btn-gold w-full justify-center py-3 text-base shadow-sm">Complete Invoice</button>
        </div>
    </div>
</div>

<script>
let cart = [];

// Client Search
const clientSearch = document.getElementById('clientSearch');
const clientDropdown = document.getElementById('clientDropdown');
const clientId = document.getElementById('clientId');

clientSearch.addEventListener('input', function() {
    let q = this.value;
    if(q.length < 2) { clientDropdown.classList.add('hidden'); return; }
    
    fetch(`/organization/clients/search?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
            clientDropdown.innerHTML = '';
            if(data.length === 0) {
                clientDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No clients found</div>';
            } else {
                data.forEach(c => {
                    let div = document.createElement('div');
                    div.className = 'p-2 hover:bg-indigo-50 cursor-pointer text-sm border-b last:border-0';
                    div.innerHTML = `<strong>${c.name}</strong> <span class="text-xs text-gray-500">${c.phone || ''}</span>`;
                    div.onclick = () => {
                        clientSearch.value = c.name;
                        clientId.value = c.id;
                        clientDropdown.classList.add('hidden');
                    };
                    clientDropdown.appendChild(div);
                });
            }
            clientDropdown.classList.remove('hidden');
        });
});

// Hide dropdowns on click outside
document.addEventListener('click', function(e) {
    if(e.target !== clientSearch) clientDropdown.classList.add('hidden');
    if(e.target !== productSearch) productDropdown.classList.add('hidden');
});

// Product Search
const productSearch = document.getElementById('productSearch');
const productDropdown = document.getElementById('productDropdown');

productSearch.addEventListener('input', function() {
    let q = this.value;
    if(q.length < 2) { productDropdown.classList.add('hidden'); return; }
    
    fetch(`/organization/invoices/products/search?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
            productDropdown.innerHTML = '';
            if(data.length === 0) {
                productDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No products found</div>';
            } else {
                data.forEach(p => {
                    let div = document.createElement('div');
                    div.className = 'p-2 hover:bg-indigo-50 cursor-pointer text-sm border-b last:border-0 flex justify-between';
                    div.innerHTML = `
                        <div>
                            <strong>${p.name}</strong> 
                            <div class="text-xs text-gray-500">SKU: ${p.sku}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold">₹${parseFloat(p.selling_price).toFixed(2)}</div>
                            <div class="text-xs text-gray-500">Stock: ${p.current_stock}</div>
                        </div>
                    `;
                    div.onclick = () => {
                        addToCart(p);
                        productSearch.value = '';
                        productDropdown.classList.add('hidden');
                        productSearch.focus(); // keep focus for next scan
                    };
                    productDropdown.appendChild(div);
                });
            }
            productDropdown.classList.remove('hidden');
        });
});

// Handle Barcode Scanner Enter Key
productSearch.addEventListener('keypress', function(e) {
    if(e.key === 'Enter') {
        e.preventDefault(); // don't submit form
        // If there's an exact barcode match, we should ideally hit a specific endpoint. 
        // For simplicity, if dropdown has exactly 1 item, we click it.
        if(!productDropdown.classList.contains('hidden') && productDropdown.children.length === 1 && !productDropdown.children[0].classList.contains('text-gray-500')) {
            productDropdown.children[0].click();
        }
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

function removeCart(id) {
    cart = cart.filter(i => i.id !== id);
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const empty = document.getElementById('emptyCart');
    
    if(cart.length === 0) {
        container.innerHTML = '';
        container.appendChild(empty);
        empty.classList.remove('hidden');
        calculateTotals();
        return;
    }
    
    empty.classList.add('hidden');
    container.innerHTML = '';
    
    cart.forEach(item => {
        let taxAmt = (item.price * (item.taxRate / 100));
        let total = (item.price + taxAmt) * item.qty;
        
        let row = document.createElement('div');
        row.className = 'cart-grid border border-gray-100 bg-white rounded-lg p-2 shadow-sm text-sm';
        row.innerHTML = `
            <div class="font-medium">${item.name} <span class="text-xs text-red-500 ml-1 ${item.qty > item.maxStock ? '' : 'hidden'}">Low Stock!</span></div>
            <div>₹${item.price.toFixed(2)}</div>
            <div>
                <input type="number" min="1" value="${item.qty}" class="w-16 border-gray-300 rounded py-1 px-2 text-center text-sm" onchange="updateQty(${item.id}, this.value)">
            </div>
            <div class="text-right font-bold text-indigo-900">₹${total.toFixed(2)}</div>
            <div class="text-right">
                <button class="text-red-500 hover:text-red-700" onclick="removeCart(${item.id})">
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
    
    let discount = parseFloat(document.getElementById('sumDiscount').value) || 0;
    let grandTotal = subtotal + tax - discount;
    if(grandTotal < 0) grandTotal = 0;
    
    document.getElementById('sumSubtotal').textContent = subtotal.toFixed(2);
    document.getElementById('sumTax').textContent = tax.toFixed(2);
    document.getElementById('sumGrandTotal').textContent = grandTotal.toFixed(2);
    
    // Auto-fill paid amount if fully paid
    let status = document.getElementById('invoiceStatus').value;
    if(status === 'Paid') {
        document.getElementById('sumPaid').value = grandTotal.toFixed(2);
    }
}

document.getElementById('invoiceStatus').addEventListener('change', calculateTotals);

function submitInvoice() {
    if(!clientId.value) { alert("Please select a client."); return; }
    if(cart.length === 0) { alert("Please add at least one item."); return; }
    
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.textContent = 'Processing...';
    
    const payload = {
        client_id: clientId.value,
        invoice_date: document.getElementById('invoiceDate').value,
        notes: document.getElementById('invoiceNotes').value,
        discount: parseFloat(document.getElementById('sumDiscount').value) || 0,
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
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            window.location.href = data.redirect;
        } else {
            alert("Error: " + data.message);
            btn.disabled = false;
            btn.textContent = 'Complete Invoice';
        }
    })
    .catch(err => {
        alert("A server error occurred.");
        console.error(err);
        btn.disabled = false;
        btn.textContent = 'Complete Invoice';
    });
}
</script>
@endsection

@extends('layouts.sme')

@section('title', 'Create Invoice')

@push('styles')
<style>
    .cart-grid { display: grid; grid-template-columns: 2fr 1.2fr 1.5fr 1.2fr 40px; gap: 10px; align-items: center; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.invoices.index') }}" class="hover:text-slate-900 transition-colors">Operations</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.invoices.index') }}" class="hover:text-slate-900 transition-colors">Invoices</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">New Invoice</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    🧾
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Create Invoice</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Issue a GST tax invoice, deduct warehouse inventory, and record customer payments.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('organization.invoices.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs rounded-lg transition border border-slate-300">
                &larr; Back to Invoices
            </a>
        </div>
    </div>

    <!-- Alert Banner Container -->
    <div id="invoiceErrorBanner" class="hidden bg-rose-50 border border-rose-300 text-rose-950 p-4 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs flex items-start justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span id="errorMessageText">An error occurred while generating invoice.</span>
        </div>
        <button type="button" onclick="document.getElementById('invoiceErrorBanner').classList.add('hidden')" class="text-rose-500 hover:text-rose-700 font-black text-base">&times;</button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Billing Details Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 sm:p-6">
                <h3 class="font-extrabold text-xs uppercase tracking-wider text-slate-950 border-b border-slate-200 pb-3 mb-4">
                    1. Billing & Customer Details
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div id="clientSearchGroup" class="relative">
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Select Client</label>
                            <button type="button" onclick="openQuickClientModal()" class="text-xs text-amber-700 hover:text-amber-800 font-extrabold">+ Quick Add Client</button>
                        </div>
                        <input type="text" id="clientSearch" placeholder="Search client by name or phone (or leave blank for Walk-in)..." class="w-full border border-slate-300 rounded-lg text-xs sm:text-sm px-3 py-2 text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" value="{{ request('client_id') ? \App\Models\Client::find(request('client_id'))->name ?? '' : '' }}" autocomplete="off">
                        <input type="hidden" id="clientId" value="{{ request('client_id', '') }}">
                        <div id="clientDropdown" class="absolute z-20 w-full bg-white border border-slate-200 mt-1 rounded-lg shadow-xl hidden max-h-48 overflow-y-auto"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Invoice Date</label>
                        <input type="date" id="invoiceDate" value="{{ now()->toDateString() }}" class="w-full border border-slate-300 rounded-lg text-xs sm:text-sm px-3 py-2 text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Internal Notes</label>
                    <input type="text" id="invoiceNotes" placeholder="Optional notes for customer or internal reference..." class="w-full border border-slate-300 rounded-lg text-xs sm:text-sm px-3 py-2 text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <!-- Items Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 border-b border-slate-200 pb-3 mb-4">
                    <h3 class="font-extrabold text-xs uppercase tracking-wider text-slate-950">
                        2. Invoice Line Items
                    </h3>
                    <div id="productSearchGroup" class="w-full sm:w-80 relative">
                        <input type="text" id="productSearch" placeholder="Search product, SKU or scan barcode..." class="w-full border border-slate-300 rounded-lg text-xs sm:text-sm px-3 py-2 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-slate-950 placeholder-slate-400" autocomplete="off">
                        <div id="productDropdown" class="absolute z-20 w-full bg-white border border-slate-200 mt-1 rounded-lg shadow-xl hidden max-h-64 overflow-y-auto"></div>
                    </div>
                </div>

                <div class="cart-grid border-b border-slate-200 pb-2 mb-3 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                    <div>Product</div>
                    <div>Price (₹)</div>
                    <div>Qty</div>
                    <div class="text-right">Total (₹)</div>
                    <div></div>
                </div>

                <div id="cartItems" class="space-y-2 mb-4 min-h-[100px]">
                    <div id="emptyCart" class="text-center text-slate-400 py-10 text-xs sm:text-sm">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 text-base mx-auto mb-2">
                            📦
                        </div>
                        No items added yet. Type in the search box above or scan a barcode to add products.
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 sm:p-6">
                <h3 class="font-extrabold border-b border-slate-200 pb-3 mb-4 text-slate-950 text-xs uppercase tracking-wider">
                    3. Invoice Summary
                </h3>
                
                <div class="space-y-3 mb-4 text-xs sm:text-sm text-slate-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-extrabold text-slate-950">₹<span id="sumSubtotal">0.00</span></span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span>CGST ({{ (float)(auth()->user()->organization->cgst_percent ?? 0) }}%)</span>
                        <span class="font-extrabold text-slate-950">₹<span id="sumCgst">0.00</span></span>
                    </div>

                    <div class="flex justify-between">
                        <span>SGST ({{ (float)(auth()->user()->organization->sgst_percent ?? 0) }}%)</span>
                        <span class="font-extrabold text-slate-950">₹<span id="sumSgst">0.00</span></span>
                    </div>

                    <div class="pt-2 pb-3 border-y border-slate-200 space-y-2">
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-extrabold text-slate-600 uppercase tracking-wider">Discount Type</label>
                            <select id="discountType" onchange="calculateTotals()" class="w-36 border border-slate-300 rounded-lg text-xs font-bold py-1.5 px-2.5 bg-slate-50 focus:bg-white text-slate-800">
                                <option value="fixed">₹ Flat (Rupees)</option>
                                <option value="percent">% Percentage (%)</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-extrabold text-slate-600 uppercase tracking-wider">Discount Value</label>
                            <input type="number" id="sumDiscount" value="0" min="0" step="0.01" placeholder="0" class="w-36 text-right border border-slate-300 rounded-lg text-xs py-1.5 px-2.5 bg-slate-50 focus:bg-white font-black text-slate-900" oninput="calculateTotals()">
                        </div>
                        <div id="discountConvertedRow" class="hidden text-right text-xs text-amber-700 font-extrabold bg-amber-50 py-1 px-2.5 rounded-lg border border-amber-200">
                            Discount Amount: -₹<span id="sumDiscountCalculated">0.00</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-baseline mb-4 pt-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-600">Grand Total</span>
                    <span class="text-2xl font-black text-slate-950">₹<span id="sumGrandTotal">0.00</span></span>
                </div>

                <div class="space-y-4 border-t border-slate-200 pt-4 mb-6">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-1">Save As Status</label>
                        <select id="invoiceStatus" class="w-full border border-slate-300 rounded-lg font-bold text-xs text-slate-900 px-3 py-2 bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="Paid">✓ Paid (Full Payment Received Now)</option>
                            <option value="Due">⏳ Due (Credit Sale - Pay Later)</option>
                            <option value="Partially Paid">🌗 Partially Paid (Partial Advance Received)</option>
                            <option value="Draft">📝 Draft (Estimate / Quotation Only)</option>
                        </select>
                    </div>

                    <!-- Payment Settlement Block (Shown when Paid or Partially Paid) -->
                    <div id="paymentDetailsBlock" class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <div id="partialAmountGroup" class="hidden">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Advance Amount Paid Now (₹) *</label>
                                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-900">Partial</span>
                            </div>
                            <input type="number" id="sumPaid" value="0" min="0" step="0.01" class="w-full border border-slate-300 rounded-lg text-sm font-black bg-white focus:border-amber-500 text-slate-950 px-3 py-2 shadow-2xs">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Payment Method *</label>
                            <select id="paymentMethod" class="w-full border border-slate-300 rounded-lg font-bold text-xs text-slate-900 px-3 py-2 bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 shadow-2xs">
                                <option value="Cash">💵 Cash</option>
                                <option value="UPI">📱 UPI / QR Code</option>
                                <option value="Card">💳 Debit / Credit Card</option>
                                <option value="Bank Transfer">🏦 Bank Transfer / NEFT</option>
                            </select>
                        </div>

                        <div id="paymentRefGroup" class="hidden">
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Txn Ref / UTR / Cheque #</label>
                            <input type="text" id="paymentRef" placeholder="e.g. UPI UTR 42918401928" class="w-full border border-slate-300 rounded-lg text-xs font-medium px-3 py-2 text-slate-950 bg-white focus:border-amber-500 shadow-2xs">
                        </div>

                        <!-- Inline Quick UPI Setup (Shown when UPI is selected & upi_id is empty) -->
                        <div id="quickUpiSetupBlock" class="hidden p-3 bg-amber-50 border border-amber-200 rounded-xl space-y-2 text-xs text-amber-900">
                            <div class="font-black text-amber-950 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Merchant UPI ID Missing</span>
                            </div>
                            <p class="text-[11px] text-amber-800 leading-snug">Enter your UPI ID / VPA to display your payment QR Code on this invoice:</p>
                            <div class="flex gap-2">
                                <input type="text" id="quickUpiInput" placeholder="e.g. yourname@upi or 9876543210@paytm" class="w-full border border-amber-300 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 bg-white font-mono focus:outline-none focus:border-amber-500">
                                <button type="button" onclick="saveQuickUpiId()" id="btnSaveQuickUpi" class="bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-bold px-3 py-1.5 rounded-lg text-xs shrink-0 shadow-2xs transition flex items-center gap-1">
                                    <span>Save & Show QR</span>
                                </button>
                            </div>
                        </div>

                        <!-- Live Dynamic UPI QR Code Box -->
                        <div id="upiQrCodeContainer" class="hidden p-3 bg-white border border-slate-200 rounded-xl flex flex-col items-center justify-center text-center space-y-2 shadow-2xs">
                            <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Scan to Pay via UPI</div>
                            <div id="createUpiQrBox" class="p-2 bg-white rounded-lg border border-slate-200 shadow-2xs min-h-[140px] flex items-center justify-center"></div>
                            <div class="text-[11px] font-black text-slate-900 font-mono" id="upiVpaDisplay"></div>
                            <div class="text-[11px] font-extrabold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200" id="upiAmountDisplay">₹0.00</div>
                            <div class="text-[10px] text-slate-400">Scan using GPay, PhonePe, Paytm, BHIM</div>
                        </div>
                    </div>

                    <p id="statusNoticeText" class="text-[11px] text-slate-500 font-medium leading-relaxed mt-1">Full payment will be marked as received and recorded immediately.</p>
                </div>

                <button onclick="submitInvoice()" id="btnSubmit" class="w-full py-3 px-4 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-black text-xs sm:text-sm rounded-lg shadow-xs transition flex items-center justify-center gap-2">
                    <span>Complete & Save Invoice</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Client Modal -->
<div id="quickClientModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" onclick="closeQuickClientModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
            <div class="bg-slate-900 px-5 py-4 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-lg">👥</span>
                    <h3 class="text-base font-extrabold text-white" id="modal-title">Quick Add Client</h3>
                </div>
                <button type="button" onclick="closeQuickClientModal()" class="text-slate-400 hover:text-white font-bold text-lg leading-none">&times;</button>
            </div>
            <div class="bg-white p-6 space-y-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Client Name <span class="text-rose-600">*</span></label>
                    <input type="text" id="modalClientName" class="w-full border border-slate-300 rounded-lg text-xs sm:text-sm px-3 py-2 text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="Enter company or individual name">
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Phone Number</label>
                    <input type="text" id="modalClientPhone" class="w-full border border-slate-300 rounded-lg text-xs sm:text-sm px-3 py-2 text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="e.g. 9876543210">
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                    <input type="email" id="modalClientEmail" class="w-full border border-slate-300 rounded-lg text-xs sm:text-sm px-3 py-2 text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="billing@clientcompany.com (optional)">
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex justify-end gap-2">
                <button type="button" onclick="closeQuickClientModal()" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-lg transition border border-slate-300">Cancel</button>
                <button type="button" onclick="submitQuickClient()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">Save & Select Client</button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let orgUpiId = @json(auth()->user()?->organization?->upi_id ?? '');
let orgName = @json(auth()->user()?->organization?->name ?? 'Merchant');

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
    
    cart.forEach(item => {
        let base = item.price * item.qty;
        subtotal += base;
    });

    let cgstPercent = {{ (float)(auth()->user()->organization->cgst_percent ?? 0) }};
    let sgstPercent = {{ (float)(auth()->user()->organization->sgst_percent ?? 0) }};

    let cgst = (subtotal * cgstPercent) / 100;
    let sgst = (subtotal * sgstPercent) / 100;
    let tax = cgst + sgst;
    
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
    document.getElementById('sumCgst').textContent = cgst.toFixed(2);
    document.getElementById('sumSgst').textContent = sgst.toFixed(2);
    document.getElementById('sumGrandTotal').textContent = grandTotal.toFixed(2);

    let status = document.getElementById('invoiceStatus').value;
    let sumPaidInput = document.getElementById('sumPaid');
    let notice = document.getElementById('statusNoticeText');
    let detailsBlock = document.getElementById('paymentDetailsBlock');
    let partialGroup = document.getElementById('partialAmountGroup');
    let refGroup = document.getElementById('paymentRefGroup');
    let pMethod = document.getElementById('paymentMethod')?.value || 'Cash';

    if (status === 'Paid') {
        if (sumPaidInput) sumPaidInput.value = grandTotal.toFixed(2);
        if (detailsBlock) detailsBlock.classList.remove('hidden');
        if (partialGroup) partialGroup.classList.add('hidden');
        if (notice) notice.innerHTML = `✓ Full payment of <strong>₹${grandTotal.toFixed(2)}</strong> will be recorded immediately via <strong>${pMethod}</strong>. Stock deducted.`;
    } else if (status === 'Partially Paid') {
        if (sumPaidInput && (parseFloat(sumPaidInput.value) === 0 || parseFloat(sumPaidInput.value) >= grandTotal)) {
            sumPaidInput.value = (grandTotal / 2).toFixed(2);
        }
        if (detailsBlock) detailsBlock.classList.remove('hidden');
        if (partialGroup) partialGroup.classList.remove('hidden');
        if (notice) notice.innerHTML = `🌗 Advance payment recorded via <strong>${pMethod}</strong>. Remaining balance added to Receivables. Stock deducted.`;
    } else if (status === 'Due') {
        if (sumPaidInput) sumPaidInput.value = '0.00';
        if (detailsBlock) detailsBlock.classList.add('hidden');
        if (notice) notice.innerHTML = `⏳ Credit sale. Invoice added to Receivables as Unpaid. Stock deducted immediately.`;
    } else if (status === 'Draft') {
        if (sumPaidInput) sumPaidInput.value = '0.00';
        if (detailsBlock) detailsBlock.classList.add('hidden');
        if (notice) notice.innerHTML = `📝 Estimate / Quotation draft. Stock NOT deducted and payment deferred.`;
    }

    if (refGroup) {
        if (status === 'Paid' || status === 'Partially Paid') {
            if (pMethod === 'Cash') {
                refGroup.classList.add('hidden');
            } else {
                refGroup.classList.remove('hidden');
            }
        }
    }

    updateUpiQrCode(grandTotal);
}

function updateUpiQrCode(grandTotal) {
    const pMethod = document.getElementById('paymentMethod')?.value || 'Cash';
    const status = document.getElementById('invoiceStatus')?.value || 'Paid';
    const quickSetupBlock = document.getElementById('quickUpiSetupBlock');
    const qrContainer = document.getElementById('upiQrCodeContainer');
    const qrBox = document.getElementById('createUpiQrBox');
    const vpaDisplay = document.getElementById('upiVpaDisplay');
    const amountDisplay = document.getElementById('upiAmountDisplay');

    if (pMethod !== 'UPI' || (status !== 'Paid' && status !== 'Partially Paid')) {
        if (quickSetupBlock) quickSetupBlock.classList.add('hidden');
        if (qrContainer) qrContainer.classList.add('hidden');
        return;
    }

    if (!orgUpiId) {
        if (quickSetupBlock) quickSetupBlock.classList.remove('hidden');
        if (qrContainer) qrContainer.classList.add('hidden');
        return;
    }

    if (quickSetupBlock) quickSetupBlock.classList.add('hidden');
    if (qrContainer) qrContainer.classList.remove('hidden');

    let collectAmount = grandTotal;
    if (status === 'Partially Paid') {
        collectAmount = parseFloat(document.getElementById('sumPaid')?.value) || 0;
    }

    if (vpaDisplay) vpaDisplay.textContent = orgUpiId;
    if (amountDisplay) amountDisplay.textContent = '₹' + collectAmount.toFixed(2);

    if (qrBox && typeof QRCode !== 'undefined') {
        qrBox.innerHTML = '';
        if (collectAmount <= 0) {
            qrBox.innerHTML = '<div class="text-slate-400 text-[10px] font-bold text-center">Enter amount</div>';
            return;
        }

        const upiString = `upi://pay?pa=${encodeURIComponent(orgUpiId)}&pn=${encodeURIComponent(orgName)}&am=${collectAmount.toFixed(2)}&cu=INR&tn=${encodeURIComponent('Invoice Payment')}`;

        new QRCode(qrBox, {
            text: upiString,
            width: 120,
            height: 120,
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    }
}

function saveQuickUpiId() {
    const input = document.getElementById('quickUpiInput');
    const upiVal = input.value.trim();
    if(!upiVal) {
        alert("Please enter a valid UPI ID (e.g. name@upi)");
        input.focus();
        return;
    }
    const btn = document.getElementById('btnSaveQuickUpi');
    btn.disabled = true;
    btn.innerHTML = '<span>Saving...</span>';

    fetch('{{ route("organization.quick-upi") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ upi_id: upiVal })
    })
    .then(async res => {
        const data = await res.json();
        if(res.ok && data.success) {
            orgUpiId = data.upi_id;
            calculateTotals();
        } else {
            alert(data.message || "Failed to save UPI ID.");
            btn.disabled = false;
            btn.innerHTML = '<span>Save & Show QR</span>';
        }
    })
    .catch(err => {
        alert("An error occurred while saving UPI ID.");
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = '<span>Save & Show QR</span>';
    });
}

document.getElementById('invoiceStatus').addEventListener('change', calculateTotals);
if (document.getElementById('paymentMethod')) {
    document.getElementById('paymentMethod').addEventListener('change', calculateTotals);
}
if (document.getElementById('sumPaid')) {
    document.getElementById('sumPaid').addEventListener('input', calculateTotals);
}

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
    let cgstVal = parseFloat(document.getElementById('sumCgst').textContent) || 0;
    let sgstVal = parseFloat(document.getElementById('sumSgst').textContent) || 0;
    let taxVal = cgstVal + sgstVal;
    let finalDiscount = discountType === 'percent' ? ((subtotalVal + taxVal) * (discountInput / 100)) : discountInput;

    const payload = {
        client_id: clientVal || null,
        invoice_date: document.getElementById('invoiceDate').value,
        notes: document.getElementById('invoiceNotes').value,
        discount: finalDiscount,
        discount_type: discountType,
        discount_value: discountInput,
        amount_paid: parseFloat(document.getElementById('sumPaid').value) || 0,
        payment_method: document.getElementById('paymentMethod')?.value || 'Cash',
        reference_number: document.getElementById('paymentRef')?.value || null,
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endsection

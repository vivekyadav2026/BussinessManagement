@extends('layouts.sme')

@section('title', 'Inventory & Stock Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Inventory</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Warehouse & Branches</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Stock Levels</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    📦
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Stock Management</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ $activeLocation->name ?? 'Active Branch' }}</span>
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Real-time quantity on hand, min-stock safety limits, valuation, and immediate inventory adjustments.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Actions (Scanner, Ledger, Product Add) -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.inventory.scanner') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span>Scanner Gun / Cam</span>
            </a>

            <a href="{{ route('organization.inventory.history') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Stock Ledger</span>
            </a>

            <a href="{{ route('organization.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Product</span>
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-300 text-emerald-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-emerald-700 text-base">✓</span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-rose-700 text-base">⚠️</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 2. KPI Metric Cards Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Tracked SKUs -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Tracked SKUs</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 border border-slate-200 flex items-center justify-center text-xs font-bold">📋</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ number_format($totalTracked ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Active items in catalog</p>
            </div>
        </div>

        <!-- Total Stock Units on Hand -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Units on Hand</span>
                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center text-xs font-bold">📦</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-indigo-900 font-mono tracking-tight">{{ number_format($totalUnits ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Physical quantity across warehouse</p>
            </div>
        </div>

        <!-- Low / Out of Stock Alert Items -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Stock Alerts</span>
                <span class="w-7 h-7 rounded-lg {{ ($lowStockCount ?? 0) > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }} flex items-center justify-center text-xs font-bold">
                    {{ ($lowStockCount ?? 0) > 0 ? '⚠️' : '✅' }}
                </span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black {{ ($lowStockCount ?? 0) > 0 ? 'text-rose-700' : 'text-emerald-700' }} font-mono tracking-tight">
                    {{ number_format($lowStockCount ?? 0) }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                    {{ ($outOfStockCount ?? 0) > 0 ? ($outOfStockCount . ' out of stock') : 'At or below minimum limit' }}
                </p>
            </div>
        </div>

        <!-- Estimated Inventory Value -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Asset Valuation</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center text-xs font-bold">₹</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-amber-800 font-mono tracking-tight">₹{{ number_format($totalValuation ?? 0, 2) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Calculated at purchase cost</p>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar & Search Toolbar -->
    <div class="bg-white border border-slate-200/90 rounded-xl p-4 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <!-- Search Input Form -->
        <form method="GET" action="{{ route('organization.inventory.index') }}" class="flex-1 flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search by product name, SKU, or barcode..."
                    class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-xs sm:text-sm font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-slate-900 placeholder:text-slate-400">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-950 hover:bg-slate-800 text-white font-bold text-xs rounded-lg shadow-2xs transition">
                Search
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('organization.inventory.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Clear
                </a>
            @endif
        </form>

        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg border border-slate-200 shrink-0 self-start md:self-auto overflow-x-auto">
            <button type="button" onclick="filterByStatus('all')" class="stock-filter-btn px-3 py-1 rounded text-xs font-extrabold transition {{ (!request('status') || request('status') == 'all') ? 'bg-white text-slate-950 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}" data-filter="all">
                All ({{ $totalTracked ?? 0 }})
            </button>
            <button type="button" onclick="filterByStatus('low_stock')" class="stock-filter-btn px-3 py-1 rounded text-xs font-extrabold transition {{ request('status') == 'low_stock' ? 'bg-white text-slate-950 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}" data-filter="low_stock">
                Alerts ({{ $lowStockCount ?? 0 }})
            </button>
            <button type="button" onclick="filterByStatus('healthy')" class="stock-filter-btn px-3 py-1 rounded text-xs font-extrabold transition {{ request('status') == 'healthy' ? 'bg-white text-slate-950 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}" data-filter="healthy">
                Healthy ({{ $healthyCount ?? 0 }})
            </button>
        </div>
    </div>

    <!-- 4. High-Contrast Stock Table -->
    <div class="bg-white border border-slate-200/90 rounded-xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-950 text-white font-extrabold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Product Details</th>
                        <th class="py-3.5 px-4">SKU / Barcode</th>
                        <th class="py-3.5 px-4">Safety Limit</th>
                        <th class="py-3.5 px-4 text-center">Current Stock</th>
                        <th class="py-3.5 px-4 text-right">Purchase Price</th>
                        <th class="py-3.5 px-4 text-right">Total Valuation</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium text-slate-800" id="stockTableBody">
                    @forelse($products as $product)
                    @php
                        $stockLevel = $product->inventoryStocks->first()?->quantity ?? $product->stock ?? 0;
                        $isOut = $stockLevel <= 0;
                        $isLow = !$isOut && ($stockLevel <= $product->min_stock_level);
                        $itemValuation = $stockLevel * ($product->purchase_price ?? 0);
                        $statusClass = $isOut ? 'out_of_stock' : ($isLow ? 'low_stock' : 'healthy');
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors stock-row" data-status="{{ $statusClass }}">
                        <!-- Product Details -->
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-9 h-9 rounded-lg object-cover border border-slate-200 shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center font-black text-xs shrink-0">
                                        {{ strtoupper(substr($product->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-950 text-sm truncate max-w-[200px] sm:max-w-[260px]">{{ $product->name }}</div>
                                    <div class="text-[11px] text-slate-500 font-medium mt-0.5">
                                        Category: <span class="text-slate-700 font-semibold">{{ $product->category->name ?? 'Unassigned' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- SKU / Barcode -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="font-mono text-slate-900 font-bold text-xs">{{ $product->sku ?? '—' }}</div>
                            @if($product->barcode)
                                <div class="font-mono text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M3 8h18M3 12h18M3 16h18M3 20h18"/></svg>
                                    <span>{{ $product->barcode }}</span>
                                </div>
                            @endif
                        </td>

                        <!-- Safety Min Limit -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                <span class="text-slate-400">Min:</span>
                                <span>{{ $product->min_stock_level }}</span>
                            </span>
                        </td>

                        <!-- Current Stock Level Badge -->
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($isOut)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-black bg-rose-50 text-rose-800 border border-rose-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                    <span>0 Out of Stock</span>
                                </span>
                            @elseif($isLow)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-black bg-amber-50 text-amber-900 border border-amber-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    <span>{{ number_format($stockLevel) }} Low Stock</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-black bg-emerald-50 text-emerald-800 border border-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    <span>{{ number_format($stockLevel) }} In Stock</span>
                                </span>
                            @endif
                        </td>

                        <!-- Purchase Price -->
                        <td class="py-3 px-4 text-right whitespace-nowrap font-mono text-slate-700 font-semibold">
                            ₹{{ number_format($product->purchase_price ?? 0, 2) }}
                        </td>

                        <!-- Total Valuation -->
                        <td class="py-3 px-4 text-right whitespace-nowrap font-mono text-slate-950 font-bold">
                            ₹{{ number_format($itemValuation, 2) }}
                        </td>

                        <!-- Actions (Adjust Stock modal button) -->
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" 
                                onclick="openAdjustModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $stockLevel }}, '{{ $product->sku }}')" 
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                <span>Adjust</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                📦
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900">No Inventory Records Found</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                No items matched your search query. Add products to the catalog or switch branches to view active stock.
                            </p>
                            <div class="mt-4 flex items-center justify-center gap-2">
                                <a href="{{ route('organization.products.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                                    + Add New Product
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/70">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 5. Modernized Stock Adjustment Modal -->
<div id="adjustModal" class="hidden fixed inset-0 bg-slate-950/75 backdrop-blur-xs flex items-center justify-center z-50 p-4 transition-all duration-300">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-300 overflow-hidden transform transition animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="bg-slate-950 text-white px-6 py-4 flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center font-black text-sm shrink-0">
                    ⚡
                </div>
                <div>
                    <h3 class="text-base font-extrabold tracking-tight text-white" style="color: #ffffff !important;">Stock Adjustment</h3>
                    <p class="text-[11px] text-slate-300" style="color: #cbd5e1 !important;">Record immediate stock movement or audit correction</p>
                </div>
            </div>
            <button type="button" onclick="closeAdjustModal()" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition text-sm font-bold" style="color: #cbd5e1 !important;">
                ✕
            </button>
        </div>
        
        <!-- Modal Form -->
        <form action="{{ route('organization.inventory.adjust') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="product_id" id="adjustProductId">

            <!-- Selected Product Overview Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Selected Product</span>
                    <div class="text-sm font-black text-slate-950 mt-0.5" id="adjustProductName">Loading...</div>
                    <div class="text-[11px] text-slate-600 font-mono mt-0.5" id="adjustProductSku">SKU: —</div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Current Qty</span>
                    <div class="text-xl font-black text-slate-950 font-mono mt-0.5" id="adjustCurrentStock">0</div>
                </div>
            </div>
            
            <!-- Movement Type & Quantity -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">Movement Type <span class="text-rose-600">*</span></label>
                    <select name="type" required class="w-full border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 rounded-lg px-3 py-2 text-xs sm:text-sm font-semibold outline-none bg-white transition">
                        <option value="in">➕ Stock In (Add Stock)</option>
                        <option value="out">➖ Stock Out (Deduct Stock)</option>
                        <option value="adjustment">🔄 Audit / Reconciliation</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">Units Quantity <span class="text-rose-600">*</span></label>
                    <input type="number" name="quantity" required min="1" value="1" 
                        class="w-full border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 rounded-lg px-3 py-2 text-xs sm:text-sm font-bold font-mono outline-none transition text-slate-900">
                </div>
            </div>
            
            <!-- Reference Notes / Reason -->
            <div>
                <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">Reason / Audit Reference Note</label>
                <input type="text" name="notes" placeholder="e.g., Supplier batch receipt, damage write-off, monthly count..." 
                    class="w-full border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 rounded-lg px-3 py-2 text-xs sm:text-sm font-medium outline-none transition text-slate-900 placeholder:text-slate-400">
            </div>
            
            <!-- Modal Actions -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                <button type="button" onclick="closeAdjustModal()" 
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-5 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    Confirm Stock Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAdjustModal(id, name, currentStock, sku) {
    document.getElementById('adjustProductId').value = id;
    document.getElementById('adjustProductName').textContent = name;
    document.getElementById('adjustProductSku').textContent = 'SKU: ' + (sku || 'N/A');
    document.getElementById('adjustCurrentStock').textContent = currentStock;
    document.getElementById('adjustModal').classList.remove('hidden');
}

function closeAdjustModal() {
    document.getElementById('adjustModal').classList.add('hidden');
}

// Client-side quick filter for status tabs without mandatory reload
function filterByStatus(status) {
    // Update active tab buttons visual state
    document.querySelectorAll('.stock-filter-btn').forEach(btn => {
        if (btn.getAttribute('data-filter') === status) {
            btn.className = 'stock-filter-btn px-3 py-1 rounded text-xs font-extrabold transition bg-white text-slate-950 shadow-2xs';
        } else {
            btn.className = 'stock-filter-btn px-3 py-1 rounded text-xs font-extrabold transition text-slate-600 hover:text-slate-900';
        }
    });

    const rows = document.querySelectorAll('.stock-row');
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === 'all') {
            row.style.display = '';
        } else if (status === 'low_stock') {
            if (rowStatus === 'low_stock' || rowStatus === 'out_of_stock') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        } else if (status === 'healthy') {
            if (rowStatus === 'healthy') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}
</script>
@endsection

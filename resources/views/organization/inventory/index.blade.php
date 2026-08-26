@extends('layouts.sme')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Inventory Stock</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage and track product stock levels for <span class="font-semibold text-gray-700">{{ \App\Models\Location::find(\App\Services\LocationManager::getActiveLocationId())->name ?? 'Unknown Location' }}</span></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('organization.inventory.history') }}" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition shadow-sm">View Ledger History</a>
            <a href="{{ route('organization.inventory.scanner') }}" class="px-4 py-2.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl font-semibold text-sm hover:opacity-95 shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Scanner Mode
            </a>
        </div>
    </div>

    <!-- Notices -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm">
        {{ session('error') }}
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Search Filter -->
        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
            <form method="GET" action="{{ route('organization.inventory.index') }}" class="flex gap-3 w-full max-w-lg">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name, SKU, or barcode..." class="border border-gray-200 focus:border-[var(--theme-active)] rounded-xl px-4 py-2 text-sm w-full outline-none transition">
                <button type="submit" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition shadow-sm">Filter</button>
                @if(request('search'))
                    <a href="{{ route('organization.inventory.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-750 text-sm flex items-center font-medium">Clear</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Product Name</th>
                        <th class="px-6 py-4">SKU / Barcode</th>
                        <th class="px-6 py-4">Current Stock</th>
                        <th class="px-6 py-4 text-right">Quick Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                    @php
                        $stockLevel = $product->inventoryStocks->first()->stock ?? 0;
                        $isLowStock = $stockLevel <= $product->min_stock_level;
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $product->name }}</div>
                            <div class="text-[11px] text-gray-450 font-medium mt-0.5">Min Stock Limit: {{ $product->min_stock_level }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-700 font-medium">SKU: <span class="font-mono text-gray-900">{{ $product->sku }}</span></div>
                            @if($product->barcode)
                                <div class="text-[11px] text-gray-400 font-mono mt-0.5">Barcode: {{ $product->barcode }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold {{ $isLowStock ? 'bg-rose-50 text-rose-700 border border-rose-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                                {{ $stockLevel }}
                            </span>
                            @if($isLowStock)
                                <span class="text-[10px] text-rose-500 font-bold ml-2 uppercase tracking-wide">Low Stock</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="openAdjustModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $stockLevel }})" class="px-3.5 py-1.5 bg-white border border-gray-200 text-[var(--theme-active)] hover:bg-[var(--theme-active)] hover:text-white rounded-xl text-xs font-bold transition shadow-sm">Adjust Stock</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-12 text-gray-400 font-medium">No products found. Create items in your catalog before managing stock.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Adjust Stock Modal -->
<div id="adjustModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-all duration-300">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-gray-150 transform transition">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Adjust Stock Level</h3>
                <p class="text-xs text-gray-500 mt-0.5" id="adjustProductName">Product details loading...</p>
            </div>
            <button onclick="document.getElementById('adjustModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-50">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form action="{{ route('organization.inventory.adjust') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="product_id" id="adjustProductId">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Movement Type</label>
                    <select name="type" class="w-full border border-gray-200 focus:border-[var(--theme-active)] rounded-xl px-4 py-2.5 text-sm outline-none bg-white transition">
                        <option value="in">Stock In (Add)</option>
                        <option value="out">Stock Out (Deduct)</option>
                        <option value="adjustment">Manual Count Adjustment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Quantity</label>
                    <input type="number" name="quantity" required min="1" value="1" class="w-full border border-gray-200 focus:border-[var(--theme-active)] rounded-xl px-4 py-2.5 text-sm outline-none transition">
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes / Reference Reason</label>
                <input type="text" name="notes" placeholder="e.g. Supplier delivery, monthly audit count..." class="w-full border border-gray-200 focus:border-[var(--theme-active)] rounded-xl px-4 py-2.5 text-sm outline-none transition">
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-50">
                <button type="button" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-50 transition" onclick="document.getElementById('adjustModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl text-xs font-semibold hover:opacity-95 shadow-sm transition">Confirm Stock Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAdjustModal(id, name, currentStock) {
    document.getElementById('adjustProductId').value = id;
    document.getElementById('adjustProductName').innerHTML = `<span class="font-semibold text-gray-800">${name}</span> <span class="text-xs text-gray-400">(Current Active Stock: ${currentStock})</span>`;
    document.getElementById('adjustModal').classList.remove('hidden');
}
</script>
@endsection

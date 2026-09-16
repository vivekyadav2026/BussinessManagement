@extends('layouts.sme')

@section('title', 'Product Catalog')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Inventory</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Central Catalog</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Products</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    📦
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Product Catalog</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Manage central product definitions, SKU master codes, barcode labels, and selling margins.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Actions (Capacity & Add CTA) -->
        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-900 border border-slate-300 shadow-2xs">
                <span class="text-slate-500 font-medium">Capacity:</span>
                <span>{{ $totalProducts ?? 0 }} / {{ is_numeric($maxProducts) ? $maxProducts : '∞' }}</span>
                @if(($limitReached ?? false) && Route::has('organization.subscription.index'))
                    <span class="ml-1 text-rose-700 font-extrabold">(Limit Reached)</span>
                @endif
            </div>

            @if(!($limitReached ?? false))
                <a href="{{ route('organization.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add New Product</span>
                </a>
            @else
                <a href="{{ route('organization.subscription.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-lg shadow-xs transition" title="Product limit reached. Upgrade subscription.">
                    <span>Upgrade to Add More &rarr;</span>
                </a>
            @endif
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
        <!-- Total Products -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Catalog Size</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 border border-slate-200 flex items-center justify-center text-xs font-bold">📦</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ number_format($totalProducts ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Total master catalog products</p>
            </div>
        </div>

        <!-- Active Listed Items -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Active Listings</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-xs font-bold">🟢</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-emerald-700 font-mono tracking-tight">{{ number_format($activeProducts ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Available for billing & POS</p>
            </div>
        </div>

        <!-- Inactive / Archived Items -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Inactive Items</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center text-xs font-bold">⏸️</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-700 font-mono tracking-tight">{{ number_format($inactiveProducts ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Hidden or paused products</p>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Categories</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center text-xs font-bold">🏷️</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ number_format($categoriesCount ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Assigned product categories</p>
            </div>
        </div>
    </div>

    <!-- 3. Filter & Search Toolbar Strip -->
    <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs">
        <form method="GET" action="{{ route('organization.products.index') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end flex-wrap">
            <!-- Search -->
            <div class="flex-1 min-w-[220px]">
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Search Catalog</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search product name, SKU, or barcode..." 
                        class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2 pl-9 text-xs font-semibold text-slate-950 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="w-full sm:w-52">
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Category</label>
                <select name="category_id" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-semibold text-slate-950 focus:bg-white focus:ring-2 focus:ring-amber-500 transition shadow-2xs">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-40">
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-semibold text-slate-950 focus:bg-white focus:ring-2 focus:ring-amber-500 transition shadow-2xs">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 pt-1 sm:pt-0">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                    Apply Filter
                </button>

                @if(request()->hasAny(['search', 'category_id', 'status']))
                    <a href="{{ route('organization.products.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition" title="Clear all filters">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Products Table Card -->
    <div class="bg-white border border-slate-200/90 rounded-xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-extrabold uppercase tracking-wider text-slate-600">
                        <th class="py-3 px-4 w-16 text-center">Item</th>
                        <th class="py-3 px-4">Product Details</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Financials &amp; Margin</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($products as $product)
                    <tr class="hover:bg-amber-50/20 transition-colors">
                        <!-- Thumbnail -->
                        <td class="py-3 px-4 text-center">
                            @if($product->image_path)
                                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-11 h-11 object-cover rounded-lg border border-slate-200 shadow-2xs mx-auto">
                            @else
                                <div class="w-11 h-11 bg-slate-100 border border-slate-200 rounded-lg flex items-center justify-center text-slate-400 font-black text-xs mx-auto">
                                    📦
                                </div>
                            @endif
                        </td>

                        <!-- Product Title & Codes -->
                        <td class="py-3 px-4">
                            <div class="font-extrabold text-slate-950 text-sm tracking-tight leading-snug">
                                <a href="{{ route('organization.products.show', $product) }}" class="hover:text-amber-700 hover:underline">
                                    {{ $product->name }}
                                </a>
                            </div>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                <span class="text-[10px] font-extrabold font-mono bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded border border-slate-200">
                                    SKU: {{ $product->sku }}
                                </span>
                                @if($product->barcode)
                                    <span class="text-[10px] font-mono text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200">
                                        {{ $product->barcode }}
                                    </span>
                                @endif
                                @if($product->tax_rate > 0)
                                    <span class="text-[10px] font-bold text-slate-600">
                                        GST {{ $product->tax_rate }}%
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="py-3 px-4">
                            @if($product->category)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-slate-100 text-slate-800 border border-slate-200">
                                    🏷️ {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs italic">Uncategorized</span>
                            @endif
                        </td>

                        <!-- Pricing & Margin -->
                        <td class="py-3 px-4">
                            <div class="text-sm font-black text-emerald-700 font-mono">
                                ₹{{ number_format($product->selling_price, 2) }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Cost: <span class="font-mono">₹{{ number_format($product->purchase_price, 2) }}</span>
                                @php
                                    $sell = (float)$product->selling_price;
                                    $cost = (float)$product->purchase_price;
                                    $margin = $sell > 0 ? (($sell - $cost) / $sell) * 100 : 0;
                                @endphp
                                @if($margin > 0)
                                    <span class="text-emerald-600 font-bold ml-1">({{ round($margin) }}% margin)</span>
                                @endif
                            </div>
                        </td>

                        <!-- Status Pill -->
                        <td class="py-3 px-4">
                            @if($product->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-emerald-100/90 text-emerald-950 border border-emerald-300 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4 text-right">
                            <div class="flex justify-end gap-1.5 items-center">
                                <!-- Print Barcode -->
                                <a href="{{ route('organization.products.print-barcode', $product) }}" 
                                    class="p-1.5 text-slate-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition" 
                                    title="Print Barcode / QR Label Sheet">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"/></svg>
                                </a>

                                <!-- View Specification -->
                                <a href="{{ route('organization.products.show', $product) }}" 
                                    class="p-1.5 text-slate-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg border border-transparent hover:border-blue-200 transition" 
                                    title="View Product Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <!-- Edit Product -->
                                <a href="{{ route('organization.products.edit', $product) }}" 
                                    class="p-1.5 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg border border-transparent hover:border-indigo-200 transition" 
                                    title="Edit Product">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                <!-- Delete Product -->
                                <form action="{{ route('organization.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this product?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition" title="Delete Product">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <div class="text-3xl mb-2">📦</div>
                            <p class="text-sm font-extrabold text-slate-800">No products found in catalog</p>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                @if(request()->hasAny(['search', 'category_id', 'status']))
                                    No items match your active search filters. Try clearing your filters or changing search keywords.
                                @else
                                    Start building your product catalog by adding your first retail or inventory item.
                                @endif
                            </p>
                            @if(!($limitReached ?? false))
                                <div class="mt-4">
                                    <a href="{{ route('organization.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                                        <span>+ Add First Product</span>
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-slate-600 font-medium">
                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection


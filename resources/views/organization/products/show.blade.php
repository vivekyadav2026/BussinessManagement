@extends('layouts.sme')

@section('title', 'Product Specification: ' . $product->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.products.index') }}" class="hover:text-slate-900 transition-colors">Inventory</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.products.index') }}" class="hover:text-slate-900 transition-colors">Products</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold truncate max-w-[200px]">{{ $product->name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    📦
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">{{ $product->name }}</h1>
                        @if($product->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-emerald-100/90 text-emerald-950 border border-emerald-300 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                Active Listing
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                Inactive / Draft
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        SKU: <span class="font-mono font-bold text-slate-800">{{ $product->sku }}</span>
                        @if($product->category)
                            &bull; Category: <span class="font-bold text-slate-800">{{ $product->category->name }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Action Buttons -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.products.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs rounded-lg transition shadow-2xs">
                &larr; Back to Catalog
            </a>

            <a href="{{ route('organization.products.print-barcode', $product) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Barcode Labels</span>
            </a>

            <a href="{{ route('organization.products.edit', $product) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Product</span>
            </a>
        </div>
    </div>

    <!-- 2. Financial Metrics Bar -->
    @php
        $sell = (float)$product->selling_price;
        $cost = (float)$product->purchase_price;
        $profit = $sell - $cost;
        $margin = $sell > 0 ? ($profit / $sell) * 100 : 0;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Selling Price -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Retail Selling Price</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-xs font-bold">₹</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-emerald-700 font-mono tracking-tight">₹{{ number_format($sell, 2) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Customer counter billing rate</p>
            </div>
        </div>

        <!-- Purchase Cost -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Purchase / Cost Price</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center text-xs font-bold">🏷️</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900 font-mono tracking-tight">₹{{ number_format($cost, 2) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Supplier acquisition cost</p>
            </div>
        </div>

        <!-- Profit Margin -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Profit Margin</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center text-xs font-bold">%</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-amber-600 font-mono tracking-tight">{{ round($margin, 1) }}%</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Net profit: ₹{{ number_format($profit, 2) }} / unit</p>
            </div>
        </div>

        <!-- Tax & Min Stock -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Tax &amp; Stock Alert</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center text-xs font-bold">🛡️</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900 font-mono tracking-tight">GST {{ $product->tax_rate }}%</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Min alert threshold: {{ $product->min_stock_level }} units</p>
            </div>
        </div>
    </div>

    <!-- 3. Two Column Details Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Specification Panel -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 space-y-5">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider">Product Identification &amp; Master Codes</h3>
                    <span class="text-xs font-mono text-slate-400">ID: #{{ $product->id }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-slate-50/70 rounded-lg border border-slate-200">
                        <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Product Name</span>
                        <span class="font-black text-slate-950 text-sm mt-0.5 block">{{ $product->name }}</span>
                    </div>

                    <div class="p-3 bg-slate-50/70 rounded-lg border border-slate-200">
                        <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Assigned Category</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $product->category->name ?? 'Unassigned' }}</span>
                    </div>

                    <div class="p-3 bg-slate-50/70 rounded-lg border border-slate-200">
                        <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Internal SKU Code</span>
                        <span class="font-mono font-black text-slate-900 text-sm mt-0.5 block">{{ $product->sku }}</span>
                    </div>

                    <div class="p-3 bg-slate-50/70 rounded-lg border border-slate-200">
                        <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Barcode / EAN Code</span>
                        <span class="font-mono font-bold text-slate-900 text-sm mt-0.5 block">{{ $product->barcode ?: 'No barcode generated' }}</span>
                    </div>
                </div>

                <!-- Description -->
                <div class="pt-2">
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Product Description &amp; Technical Notes</label>
                    <div class="p-4 bg-slate-50/60 rounded-xl border border-slate-200 text-xs text-slate-800 leading-relaxed font-medium">
                        {{ $product->description ?: 'No additional product description or specification notes provided.' }}
                    </div>
                </div>
            </div>

            <!-- Barcode Label Preview Card -->
            @if($product->barcode)
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider">Barcode &amp; QR Sticker Preview</h3>
                    <a href="{{ route('organization.products.print-barcode', $product) }}" class="text-xs font-bold text-amber-700 hover:underline">
                        Open Print Sheet &rarr;
                    </a>
                </div>

                <div class="p-6 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-center justify-around gap-6 text-center">
                    <!-- Barcode Box -->
                    <div class="p-4 bg-white rounded-lg border border-slate-200 shadow-2xs space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Barcode (CODE128)</span>
                        <div class="pt-2">
                            <svg id="previewBarcode"></svg>
                        </div>
                        <div class="text-xs font-mono font-bold text-slate-800">{{ $product->barcode }}</div>
                    </div>

                    <!-- Details chip -->
                    <div class="text-left space-y-2 text-xs">
                        <div class="font-bold text-slate-900">Ready for Thermal Sticker Printing</div>
                        <p class="text-slate-500 text-[11px] max-w-xs">
                            Compatible with all standard 50mm x 25mm and 38mm x 25mm barcode label rolls for retail POS scanners.
                        </p>
                        <a href="{{ route('organization.products.print-barcode', $product) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-900 text-white font-bold text-xs rounded-lg hover:bg-slate-800 transition">
                            <span>🖨️ Configure Print Batch</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Col: Gallery & Media -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 space-y-4">
                <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider border-b border-slate-200 pb-3">Product Media Gallery</h3>

                @php
                    $allImages = collect();
                    if($product->image_path) {
                        $allImages->push($product->image_path);
                    }
                    if(isset($product->images)) {
                        foreach($product->images as $gImg) {
                            if($gImg->image_path !== $product->image_path) {
                                $allImages->push($gImg->image_path);
                            }
                        }
                    }
                @endphp

                @if($allImages->count() > 0)
                    <div class="w-full h-64 bg-slate-50 rounded-xl overflow-hidden border border-slate-200 shadow-2xs">
                        <img id="mainGalleryViewer" src="{{ Storage::url($allImages->first()) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition">
                    </div>

                    @if($allImages->count() > 1)
                        <div class="grid grid-cols-4 gap-2 pt-2">
                            @foreach($allImages as $idx => $imgSrc)
                                <img src="{{ Storage::url($imgSrc) }}" onclick="document.getElementById('mainGalleryViewer').src = this.src" class="w-full h-14 object-cover rounded-lg border border-slate-200 shadow-2xs cursor-pointer hover:opacity-80 transition hover:border-amber-400">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="w-full h-56 bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center rounded-xl text-slate-400 text-xs p-4 text-center">
                        <span class="text-3xl mb-2">📷</span>
                        <span class="font-bold text-slate-600">No Product Photos</span>
                        <span class="text-[11px] text-slate-400 mt-1">Upload images while editing this product</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@if($product->barcode)
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            JsBarcode("#previewBarcode", "{{ $product->barcode }}", {
                format: "CODE128",
                width: 1.5,
                height: 45,
                displayValue: false
            });
        } catch(e) {
            console.error("Barcode preview render error:", e);
        }
    });
</script>
@endif
@endsection


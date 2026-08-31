@extends('layouts.sme')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-2 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 mb-4 border-b border-gray-100">
        <a href="{{ route('organization.products.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Add New Product</h1>
            <p class="text-xs text-gray-500">Create a new item configuration inside your central product catalog.</p>
        </div>
    </div>

    <!-- Form Container -->
    <form action="{{ route('organization.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Columns: Product Configurations -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Panel 1: Basic Information -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                        <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Basic Information</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('name') border-red-300 @enderror" placeholder="e.g. Wireless Mouse, Red T-Shirt">
                            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Category</label>
                                <select name="category_id" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">SKU (Auto-generated if empty)</label>
                                <input type="text" name="sku" value="{{ old('sku') }}" placeholder="e.g. PRD-1029" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('sku') border-red-300 @enderror">
                                @error('sku') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Barcode (UPC/EAN/QR)</label>
                            <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Scan or enter barcode..." class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('barcode') border-red-300 @enderror">
                            @error('barcode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                            <textarea name="description" rows="3" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition" placeholder="Provide product technical details or notes...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Pricing & Inventory Config -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                        <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Pricing & Inventory Config</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Selling Price <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">₹</span>
                                    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}" required class="w-full pl-7 border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('selling_price') border-red-300 @enderror" placeholder="0.00">
                                </div>
                                @error('selling_price') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Purchase Price</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">₹</span>
                                    <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" class="w-full pl-7 border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('purchase_price') border-red-300 @enderror" placeholder="0.00">
                                </div>
                                @error('purchase_price') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tax Rate (%)</label>
                                <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', 0) }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('tax_rate') border-red-300 @enderror" placeholder="0">
                                @error('tax_rate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Low Stock Alert Level</label>
                                <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 5) }}" class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('min_stock_level') border-red-300 @enderror" placeholder="e.g. 5">
                                @error('min_stock_level') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Media & Actions -->
            <div class="space-y-6">
                <!-- Product Media Panel -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                        <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Product Image</h2>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="w-full h-40 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 flex flex-col items-center justify-center overflow-hidden relative" id="imagePreviewContainer">
                            <svg class="w-8 h-8 text-gray-350 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[11px] text-gray-400 font-medium">No Image Uploaded</span>
                        </div>
                        <input type="file" name="image" id="imageInput" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border file:border-gray-300 file:text-xs file:font-semibold file:bg-white hover:file:bg-gray-50 file:cursor-pointer cursor-pointer">
                        @error('image') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <!-- Action Controls Panel -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                        <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Publish Product</h2>
                    </div>
                    
                    <div class="flex gap-2.5 pt-1.5">
                        <a href="{{ route('organization.products.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg font-semibold text-xs hover:bg-gray-50 shadow-sm flex-1 text-center transition">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-lg font-semibold text-xs hover:opacity-90 shadow-sm flex-1 transition">Save Product</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('imagePreviewContainer').innerHTML = `<img src="${ev.target.result}" class="w-full h-full object-cover">`;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endsection


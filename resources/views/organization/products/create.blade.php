@extends('layouts.sme')

@section('content')
<div class="dash-head mb-6">
  <a href="{{ route('organization.products.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Catalog</a>
  <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
</div>

<form action="{{ route('organization.products.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="panel">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Basic Information</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                    @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU (Auto-generated if empty)</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" placeholder="e.g. PRD-1029" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                        @error('sku') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barcode (UPC/EAN/QR)</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Scan or enter barcode..." class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                    @error('barcode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="panel">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Pricing & Inventory Config</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label>
                        <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                        @error('selling_price') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Price</label>
                        <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                        @error('purchase_price') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax Rate (%)</label>
                        <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', 0) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                        @error('tax_rate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Alert Level</label>
                        <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 5) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                        @error('min_stock_level') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="panel">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Product Image</h3>
                
                <div class="mb-4">
                    <div class="w-full h-48 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center mb-2 overflow-hidden" id="imagePreviewContainer">
                        <span class="text-gray-400 text-sm">No Image</span>
                    </div>
                    <input type="file" name="image" id="imageInput" accept="image/*" class="w-full text-sm">
                    @error('image') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="panel">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Publish</h3>
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('organization.products.index') }}" class="btn border flex-1 text-center">Cancel</a>
                    <button type="submit" class="btn btn-gold flex-1">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

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

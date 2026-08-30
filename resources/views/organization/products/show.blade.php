@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Product Details</h1>
      <p class="text-gray-500 mt-1">Full specification for {{ $product->name }}</p>
  </div>
  <div class="flex gap-2">
      <a class="btn bg-gray-100 text-gray-700 btn-sm" href="{{ route('organization.products.index') }}">Back to Catalog</a>
      <a class="btn btn-gold btn-sm" href="{{ route('organization.products.edit', $product) }}">Edit Product</a>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="panel mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Product Name</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">{{ $product->name }}</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Category</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">{{ $product->category->name ?? 'Unassigned' }}</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">SKU</label>
                    <div class="mt-1 text-sm font-mono text-gray-900">{{ $product->sku }}</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Barcode</label>
                    <div class="mt-1 text-sm font-mono text-gray-900">{{ $product->barcode ?: 'None' }}</div>
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-xs font-semibold text-gray-400 uppercase">Description</label>
                <div class="mt-1 text-sm text-gray-700 leading-relaxed">{{ $product->description ?: 'No description provided.' }}</div>
            </div>
        </div>
        
        <div class="panel">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Financial & Stock Parameters</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Selling Price</label>
                    <div class="mt-1 text-sm font-bold text-green-600">₹{{ number_format($product->selling_price, 2) }}</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Purchase Price</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">₹{{ number_format($product->purchase_price, 2) }}</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Tax Rate</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">{{ $product->tax_rate }}%</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Min Stock Level Alert</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">{{ $product->min_stock_level }} units</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Status</label>
                    <div class="mt-1">
                        @if($product->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div>
        <div class="panel text-center">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Product Image</h3>
            @if($product->image_path)
                <img src="{{ Storage::url($product->image_path) }}" class="w-full h-auto object-cover rounded shadow-md border border-gray-200">
            @else
                <div class="w-full h-48 bg-gray-50 border border-dashed border-gray-200 flex flex-col items-center justify-center rounded text-gray-400 text-sm">
                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>No image uploaded</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

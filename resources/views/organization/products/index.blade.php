@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Products Catalog</h1>
      <p class="text-gray-500 mt-1">Manage products shared across all branches.</p>
  </div>
  <a class="btn btn-gold btn-sm" href="{{ route('organization.products.create') }}">+ Add Product</a>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<div class="panel mb-6">
    <form method="GET" action="{{ route('organization.products.index') }}" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, SKU, barcode..." class="w-full border-gray-300 rounded-lg text-sm">
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Category</label>
            <select name="category_id" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-gold py-2">Filter</button>
        @if(request()->hasAny(['search', 'category_id']))
            <a href="{{ route('organization.products.index') }}" class="btn bg-gray-100 text-gray-600 py-2">Clear</a>
        @endif
    </form>
</div>

<div class="panel">
  <table class="inv-table w-full">
    <thead>
      <tr>
        <th>Image</th>
        <th>Product Details</th>
        <th>Pricing</th>
        <th>Status</th>
        <th class="text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($products as $product)
      <tr>
        <td class="w-16">
            @if($product->image_path)
                <img src="{{ Storage::url($product->image_path) }}" class="w-12 h-12 object-cover rounded shadow-sm">
            @else
                <div class="w-12 h-12 bg-gray-100 flex items-center justify-center rounded text-gray-400 text-xs">No img</div>
            @endif
        </td>
        <td>
            <div class="font-bold text-gray-900">{{ $product->name }}</div>
            <div class="text-xs text-gray-500 mt-1">
                SKU: <span class="font-mono bg-gray-100 px-1 rounded">{{ $product->sku }}</span> | 
                Cat: {{ $product->category->name ?? 'None' }}
            </div>
            @if($product->barcode)
                <div class="text-xs text-gray-400 mt-1">Barcode: {{ $product->barcode }}</div>
            @endif
        </td>
        <td>
            <div class="text-sm">Sell: <span class="font-medium text-green-600">₹{{ number_format($product->selling_price, 2) }}</span></div>
            <div class="text-xs text-gray-500">Buy: ₹{{ number_format($product->purchase_price, 2) }}</div>
        </td>
        <td>
            @if($product->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
            @endif
        </td>
        <td class="text-right">
            <div class="flex justify-end gap-1 items-center">
                <a href="{{ route('organization.products.print-barcode', $product) }}" class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Print Barcode / QR Label Sheet">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"/></svg>
                </a>
                <a href="{{ route('organization.products.show', $product) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Product">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <a href="{{ route('organization.products.edit', $product) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit Product">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>

                <form action="{{ route('organization.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete Product">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </td>

      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center py-6 text-gray-500">No products found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
  
  <div class="mt-4">
      {{ $products->links() }}
  </div>
</div>
@endsection

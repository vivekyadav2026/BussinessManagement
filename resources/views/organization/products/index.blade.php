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
            <a href="{{ route('organization.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Edit</a>
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

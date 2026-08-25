@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Inventory Stock</h1>
      <p class="text-gray-500 mt-1">Manage stock for your active location ({{ \App\Models\Location::find(\App\Services\LocationManager::getActiveLocationId())->name ?? 'Unknown' }}).</p>
  </div>
  <div class="flex gap-2">
      <a class="btn border border-gray-300 text-gray-700 btn-sm bg-white" href="{{ route('organization.inventory.history') }}">View History</a>
      <a class="btn btn-gold btn-sm flex items-center gap-2" href="{{ route('organization.inventory.scanner') }}">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Scanner Mode
      </a>
  </div>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-6 border border-red-200 text-sm font-medium">
    {{ session('error') }}
</div>
@endif

<div class="panel mb-6">
    <form method="GET" action="{{ route('organization.inventory.index') }}" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Search Products</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, SKU, barcode..." class="w-full border-gray-300 rounded-lg text-sm">
        </div>
        <button type="submit" class="btn btn-gold py-2">Filter</button>
        @if(request('search'))
            <a href="{{ route('organization.inventory.index') }}" class="btn bg-gray-100 text-gray-600 py-2">Clear</a>
        @endif
    </form>
</div>

<div class="panel">
  <table class="inv-table w-full">
    <thead>
      <tr>
        <th>Product</th>
        <th>SKU / Barcode</th>
        <th>Current Stock</th>
        <th class="text-right">Quick Adjust</th>
      </tr>
    </thead>
    <tbody>
      @forelse($products as $product)
      @php
          $stockLevel = $product->stock;
          $isLowStock = $stockLevel <= $product->min_stock_level;
      @endphp
      <tr>
        <td>
            <div class="font-bold text-gray-900">{{ $product->name }}</div>
            <div class="text-xs text-gray-500 mt-1">Min Stock: {{ $product->min_stock_level }}</div>
        </td>
        <td>
            <div class="text-xs text-gray-600">SKU: <span class="font-mono">{{ $product->sku }}</span></div>
            @if($product->barcode)
                <div class="text-xs text-gray-400 mt-1">Bar: {{ $product->barcode }}</div>
            @endif
        </td>
        <td>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $isLowStock ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                {{ $stockLevel }}
            </span>
            @if($isLowStock)
                <span class="text-xs text-red-500 font-medium ml-2 uppercase tracking-wide">Low Stock</span>
            @endif
        </td>
        <td class="text-right">
            <button onclick="openAdjustModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $stockLevel }})" class="btn btn-sm border text-indigo-600 hover:bg-indigo-50">Adjust</button>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4" class="text-center py-6 text-gray-500">No products found. Add products to your catalog first.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
  
  <div class="mt-4">
      {{ $products->links() }}
  </div>
</div>

<!-- Adjust Stock Modal -->
<div id="adjustModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-1">Adjust Stock</h3>
        <p class="text-sm text-gray-500 mb-4" id="adjustProductName"></p>
        
        <form action="{{ route('organization.inventory.adjust') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" id="adjustProductId">
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Movement Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none">
                        <option value="in">Stock In (Add)</option>
                        <option value="out">Stock Out (Deduct)</option>
                        <option value="adjustment">Manual Adjustment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" required min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes / Reference</label>
                <input type="text" name="notes" placeholder="Optional notes..." class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none">
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" class="btn border" onclick="document.getElementById('adjustModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="btn btn-gold">Confirm Movement</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAdjustModal(id, name, currentStock) {
    document.getElementById('adjustProductId').value = id;
    document.getElementById('adjustProductName').textContent = name + ' (Current: ' + currentStock + ')';
    document.getElementById('adjustModal').classList.remove('hidden');
}
</script>
@endsection

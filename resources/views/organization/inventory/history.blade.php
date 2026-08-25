@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <a href="{{ route('organization.inventory.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Inventory</a>
      <h1 class="text-2xl font-bold text-gray-900">Stock Movements Ledger</h1>
      <p class="text-gray-500 mt-1">Audit trail for {{ \App\Models\Location::find(\App\Services\LocationManager::getActiveLocationId())->name ?? 'Unknown' }}.</p>
  </div>
</div>

<div class="panel">
  <table class="inv-table w-full">
    <thead>
      <tr>
        <th>Date / Time</th>
        <th>Product</th>
        <th>Type</th>
        <th class="text-right">Quantity</th>
        <th>User</th>
        <th>Notes / Ref</th>
      </tr>
    </thead>
    <tbody>
      @forelse($movements as $movement)
      <tr>
        <td class="text-sm text-gray-600 whitespace-nowrap">
            {{ $movement->created_at->format('M d, Y') }}<br>
            <span class="text-xs text-gray-400">{{ $movement->created_at->format('h:i A') }}</span>
        </td>
        <td>
            <div class="font-bold text-gray-900">{{ $movement->product->name ?? 'Deleted Product' }}</div>
            <div class="text-xs text-gray-500 mt-1">SKU: <span class="font-mono">{{ $movement->product->sku ?? 'N/A' }}</span></div>
        </td>
        <td>
            @if($movement->type === 'in')
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 uppercase tracking-wide">Stock In</span>
            @elseif($movement->type === 'out')
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 uppercase tracking-wide">Stock Out</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 uppercase tracking-wide">Adjustment</span>
            @endif
        </td>
        <td class="text-right">
            <span class="text-lg font-bold {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
            </span>
        </td>
        <td class="text-sm text-gray-600">
            {{ $movement->user->name ?? 'System' }}
        </td>
        <td class="text-sm text-gray-500">
            @if($movement->reference)
                <span class="font-mono bg-gray-100 px-1 rounded block mb-1">{{ $movement->reference }}</span>
            @endif
            {{ $movement->notes ?? '-' }}
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center py-6 text-gray-500">No stock movements recorded for this location yet.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
  
  <div class="mt-4">
      {{ $movements->links() }}
  </div>
</div>
@endsection

@extends('layouts.sme')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('organization.inventory.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Stock Movements Ledger</h1>
            <p class="text-sm text-gray-500 mt-0.5">Audit trail and ledger entries for <span class="font-semibold text-gray-750">{{ \App\Models\Location::find(\App\Services\LocationManager::getActiveLocationId())->name ?? 'Unknown Location' }}</span></p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Date / Time</th>
                        <th class="px-6 py-4">Product details</th>
                        <th class="px-6 py-4">Movement Type</th>
                        <th class="px-6 py-4 text-right">Quantity Change</th>
                        <th class="px-6 py-4">Logged By</th>
                        <th class="px-6 py-4">Notes / Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($movements as $movement)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-900">{{ $movement->created_at->format('M d, Y') }}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">{{ $movement->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $movement->product->name ?? 'Deleted Product' }}</div>
                            <div class="text-[11px] text-gray-450 mt-0.5">SKU: <span class="font-mono">{{ $movement->product->sku ?? 'N/A' }}</span></div>
                        </td>
                        <td class="px-6 py-4">
                            @if($movement->type === 'in')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wide">Stock In</span>
                            @elseif($movement->type === 'out')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100 uppercase tracking-wide">Stock Out</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-200 uppercase tracking-wide">Adjustment</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-bold {{ $movement->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-700">
                            {{ $movement->user->name ?? 'System Process' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($movement->reference)
                                <span class="font-mono bg-gray-100 border border-gray-150 text-[10px] text-gray-600 px-1.5 py-0.5 rounded block w-max mb-1">{{ $movement->reference }}</span>
                            @endif
                            <div class="text-xs text-gray-500 font-medium">{{ $movement->notes ?? '—' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400 font-medium">No stock movements recorded for this branch location yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $movements->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

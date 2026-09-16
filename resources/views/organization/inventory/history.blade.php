@extends('layouts.sme')

@section('title', 'Stock Movements Ledger')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.inventory.index') }}" class="hover:text-slate-900 transition-colors">Inventory</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Audit Trail</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Stock Ledger</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    📋
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Stock Movements Ledger</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ \App\Models\Location::find(\App\Services\LocationManager::getActiveLocationId())->name ?? 'Active Branch' }}</span>
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Immutable audit log of inbound shipments, sales dispatches, inventory deductions, and audit counts.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Actions -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.inventory.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Back to Stock Levels</span>
            </a>

            <a href="{{ route('organization.inventory.scanner') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span>Scanner Mode</span>
            </a>
        </div>
    </div>

    <!-- 2. High-Contrast Movements Ledger Table -->
    <div class="bg-white border border-slate-200/90 rounded-xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-950 text-white font-extrabold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Date / Time</th>
                        <th class="py-3.5 px-4">Product Details</th>
                        <th class="py-3.5 px-4">Movement Type</th>
                        <th class="py-3.5 px-4 text-right">Quantity Delta</th>
                        <th class="py-3.5 px-4">Logged By</th>
                        <th class="py-3.5 px-4">Audit Notes / Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium text-slate-800">
                    @forelse($movements as $movement)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- Date / Time -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="font-bold text-slate-950">{{ $movement->created_at->format('d M Y') }}</div>
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $movement->created_at->format('h:i A') }}</div>
                        </td>

                        <!-- Product Details -->
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-950 text-sm">{{ $movement->product->name ?? 'Deleted Product' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">SKU: {{ $movement->product->sku ?? 'N/A' }}</div>
                        </td>

                        <!-- Movement Type Badge -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($movement->type === 'in')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-black bg-emerald-50 text-emerald-800 border border-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    <span>Stock In</span>
                                </span>
                            @elseif($movement->type === 'out')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-black bg-rose-50 text-rose-800 border border-rose-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                    <span>Stock Out</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-black bg-slate-100 text-slate-800 border border-slate-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                                    <span>Reconciliation</span>
                                </span>
                            @endif
                        </td>

                        <!-- Quantity Delta -->
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <span class="text-sm font-black font-mono {{ $movement->quantity > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}
                            </span>
                        </td>

                        <!-- Logged By -->
                        <td class="py-3 px-4 whitespace-nowrap text-slate-700 font-bold text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-slate-100 border border-slate-300 flex items-center justify-center text-[10px] text-slate-600">
                                    👤
                                </span>
                                <span>{{ $movement->user->name ?? 'System Process' }}</span>
                            </div>
                        </td>

                        <!-- Notes / Reference -->
                        <td class="py-3 px-4">
                            @if($movement->reference)
                                <span class="font-mono bg-slate-100 border border-slate-300 text-[10px] font-bold text-slate-700 px-2 py-0.5 rounded inline-block mb-1">
                                    {{ $movement->reference }}
                                </span>
                            @endif
                            <div class="text-xs text-slate-600 font-medium">{{ $movement->notes ?? '—' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                📋
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900">No Movement Records</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                No inventory movements or adjustments have been recorded for this branch yet.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/70">
            {{ $movements->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

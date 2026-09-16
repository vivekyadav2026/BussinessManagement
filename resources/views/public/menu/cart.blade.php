@extends('layouts.customer')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" 
               class="p-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:text-slate-950 hover:bg-slate-50 shadow-2xs transition flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-950 tracking-tight">Your Order Cart</h1>
                <p class="text-[11px] text-slate-500 font-semibold">Review your selected items before sending to the kitchen</p>
            </div>
        </div>

        @if(!empty($cart))
            <span class="px-2.5 py-1 bg-amber-500/15 text-amber-900 border border-amber-300 text-[11px] font-black rounded-lg font-mono">
                {{ array_sum(array_column($cart, 'quantity')) }} Items
            </span>
        @endif
    </div>

    @if(empty($cart))
        <div class="text-center py-16 bg-white rounded-3xl shadow-2xs border border-slate-200/90 p-8 space-y-4">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-amber-500/10 text-amber-700 text-3xl shadow-2xs mx-auto border border-amber-200">
                🛒
            </div>
            <div class="space-y-1">
                <h2 class="text-lg font-black text-slate-950">Your order cart is empty</h2>
                <p class="text-xs font-semibold text-slate-500 max-w-xs mx-auto">Explore our menu to add appetizing dishes, chef specials, and drinks.</p>
            </div>
            <div class="pt-2">
                <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" 
                   class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold px-6 py-3 rounded-xl shadow-xs transition text-xs whitespace-nowrap">
                    <span>Browse Menu & Add Dishes</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>
    @else
        <!-- Cart Items List Card -->
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/90 overflow-hidden divide-y divide-slate-100">
            @php $subtotal = 0; @endphp
            @foreach($cart as $id => $item)
                @php $subtotal += $item['price'] * $item['quantity']; @endphp
                <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                    <!-- Item Name & Unit Price -->
                    <div class="flex-grow min-w-0 pr-1 space-y-0.5">
                        <h3 class="font-black text-slate-950 text-sm line-clamp-1 leading-snug">{{ $item['name'] }}</h3>
                        <p class="text-xs font-bold text-slate-500 font-mono">₹{{ number_format($item['price'], 2) }} <span class="text-[10px] font-medium text-slate-400">each</span></p>
                    </div>
                    
                    <div class="flex items-center gap-3 shrink-0">
                        <!-- Quantity Stepper Controls -->
                        <div class="flex items-center bg-slate-950 text-white rounded-xl overflow-hidden shadow-2xs border border-slate-900">
                            <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $id]) }}" method="POST" class="inline m-0 p-0">
                                @csrf
                                <input type="hidden" name="action" value="decrease">
                                <button type="submit" class="px-3 py-1.5 text-white hover:bg-slate-800 font-black transition text-xs leading-none">&minus;</button>
                            </form>
                            <span class="px-2.5 font-black text-amber-400 text-xs font-mono py-1.5 select-none">{{ $item['quantity'] }}</span>
                            <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $id]) }}" method="POST" class="inline m-0 p-0">
                                @csrf
                                <input type="hidden" name="action" value="increase">
                                <button type="submit" class="px-3 py-1.5 text-white hover:bg-slate-800 font-black transition text-xs leading-none">+</button>
                            </form>
                        </div>

                        <!-- Item Total Price -->
                        <div class="font-black text-slate-950 text-sm font-mono w-20 text-right">
                            ₹{{ number_format($item['price'] * $item['quantity'], 2) }}
                        </div>
                        
                        <!-- Delete Item -->
                        <form action="{{ route('public.order.remove', [$organization->id, $location->id, $id]) }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="p-1.5 border border-slate-200 bg-white hover:border-rose-300 hover:bg-rose-50 text-slate-400 hover:text-rose-700 rounded-xl transition cursor-pointer shadow-2xs" title="Remove Item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Bill Summary Card -->
        @php
            $cgstRate = (float)($organization->cgst_percent ?? 0);
            $sgstRate = (float)($organization->sgst_percent ?? 0);
            $cgstAmount = round(($subtotal * $cgstRate) / 100, 2);
            $sgstAmount = round(($subtotal * $sgstRate) / 100, 2);
            $totalTax = round($cgstAmount + $sgstAmount, 2);
            $grandTotal = round($subtotal + $totalTax, 2);
        @endphp
        <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-2xs space-y-3">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                <h3 class="text-xs font-black text-slate-950 uppercase tracking-wider">Bill Summary Breakdown</h3>
            </div>
            
            <div class="space-y-2 text-xs font-semibold text-slate-600">
                <div class="flex justify-between items-center">
                    <span>Items Subtotal</span>
                    <span class="font-mono font-bold text-slate-950">₹{{ number_format($subtotal, 2) }}</span>
                </div>
                @if($cgstRate > 0 && $cgstAmount > 0)
                <div class="flex justify-between items-center">
                    <span>Central GST ({{ $cgstRate }}%)</span>
                    <span class="font-mono font-bold text-slate-950">₹{{ number_format($cgstAmount, 2) }}</span>
                </div>
                @endif
                @if($sgstRate > 0 && $sgstAmount > 0)
                <div class="flex justify-between items-center">
                    <span>State GST ({{ $sgstRate }}%)</span>
                    <span class="font-mono font-bold text-slate-950">₹{{ number_format($sgstAmount, 2) }}</span>
                </div>
                @endif
                @if($totalTax <= 0)
                <div class="flex justify-between items-center text-slate-400 text-[11px]">
                    <span>Taxes & GST</span>
                    <span class="font-mono font-bold text-emerald-600">₹0.00 (Nil)</span>
                </div>
                @endif
            </div>
            
            <div class="flex justify-between items-baseline border-t border-dashed border-slate-300 pt-3 text-base font-black text-slate-950">
                <span>Grand Total</span>
                <span class="font-mono text-slate-950 text-2xl font-black">₹{{ number_format($grandTotal, 2) }}</span>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-600 font-medium flex items-center gap-2">
                <span class="text-base">⚡</span>
                <span>Dishes are prepared fresh upon confirmation. You can pay online or at your table after dining.</span>
            </div>
        </div>

        <!-- Sticky Bottom Checkout Bar -->
        <div class="pt-2">
            <a href="{{ route('public.order.checkout', [$organization->id, $location->id]) }}" 
               class="w-full flex items-center justify-between bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-black p-4 rounded-2xl shadow-md transition transform active:scale-99 text-sm uppercase tracking-wider whitespace-nowrap">
                <div class="flex items-center gap-2">
                    <span class="font-extrabold">Proceed to Checkout</span>
                </div>
                <div class="flex items-center gap-2 font-mono text-base font-black">
                    <span>₹{{ number_format($grandTotal, 2) }}</span>
                    <span>&rarr;</span>
                </div>
            </a>
        </div>
    @endif
</div>
@endsection


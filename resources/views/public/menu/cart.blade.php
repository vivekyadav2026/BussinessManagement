@extends('layouts.customer')

@section('content')
<div class="px-4 py-5 max-w-2xl mx-auto space-y-5">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
        <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" class="p-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-2xs transition font-extrabold text-xs flex items-center gap-1">
            <span>&larr;</span> Back to Menu
        </a>
        <div class="min-w-0">
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Your Order Cart</h1>
            <p class="text-[11px] text-gray-400 font-semibold">Review your items before sending to kitchen</p>
        </div>
    </div>

    @if(empty($cart))
        <div class="text-center py-16 bg-white rounded-3xl shadow-2xs border border-gray-100 p-8 space-y-4">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-orange-50 text-orange-500 text-3xl shadow-2xs mx-auto">
                🛒
            </div>
            <div>
                <h2 class="text-lg font-black text-gray-900 mb-1">Your cart is empty</h2>
                <p class="text-xs font-semibold text-gray-400">Looks like you haven't added any dishes yet.</p>
            </div>
            <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-extrabold px-6 py-3 rounded-xl shadow-md transition text-xs active:scale-95">
                Browse Menu & Add Dishes
            </a>
        </div>
    @else
        <!-- Cart Items List Card -->
        <div class="bg-white rounded-2xl shadow-2xs border border-gray-200/80 overflow-hidden divide-y divide-gray-100">
            @php $subtotal = 0; @endphp
            @foreach($cart as $id => $item)
                @php $subtotal += $item['price'] * $item['quantity']; @endphp
                <div class="p-4 flex items-center justify-between gap-4">
                    <div class="flex-grow min-w-0 pr-1">
                        <h3 class="font-extrabold text-gray-900 text-sm line-clamp-1 leading-snug">{{ $item['name'] }}</h3>
                        <p class="text-xs font-bold text-gray-500 font-mono mt-0.5">₹{{ number_format($item['price'], 2) }} each</p>
                    </div>
                    
                    <div class="flex items-center gap-3 shrink-0">
                        <!-- Quantity Stepper Controls -->
                        <div class="flex items-center bg-orange-500 text-white rounded-lg overflow-hidden shadow-xs">
                            <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $id]) }}" method="POST" class="inline m-0 p-0">
                                @csrf
                                <input type="hidden" name="action" value="decrease">
                                <button type="submit" class="px-2.5 py-1 text-white hover:bg-orange-600 font-black transition text-xs">&minus;</button>
                            </form>
                            <span class="px-2 font-black text-white text-xs font-mono py-1">{{ $item['quantity'] }}</span>
                            <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $id]) }}" method="POST" class="inline m-0 p-0">
                                @csrf
                                <input type="hidden" name="action" value="increase">
                                <button type="submit" class="px-2.5 py-1 text-white hover:bg-orange-600 font-black transition text-xs">+</button>
                            </form>
                        </div>

                        <!-- Item Total Price -->
                        <div class="font-black text-gray-900 text-sm font-mono w-16 text-right">
                            ₹{{ number_format($item['price'] * $item['quantity'], 2) }}
                        </div>
                        
                        <!-- Delete Item -->
                        <form action="{{ route('public.order.remove', [$organization->id, $location->id, $id]) }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Remove Item">
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
            if ($cgstRate <= 0 && $sgstRate <= 0) {
                $cgstRate = 2.5;
                $sgstRate = 2.5;
            }
            $cgstAmount = ($subtotal * $cgstRate) / 100;
            $sgstAmount = ($subtotal * $sgstRate) / 100;
            $totalTax = $cgstAmount + $sgstAmount;
        @endphp
        <div class="bg-white border border-gray-200/80 rounded-2xl p-5 shadow-2xs space-y-3">
            <h3 class="text-xs font-black text-gray-500 uppercase tracking-wider border-b border-gray-100 pb-2.5">Bill Summary Breakdown</h3>
            
            <div class="flex justify-between text-xs font-semibold text-gray-600">
                <span>Items Subtotal</span>
                <span class="font-mono font-bold text-gray-900">₹{{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-xs font-semibold text-gray-600">
                <span>CGST ({{ $cgstRate }}%)</span>
                <span class="font-mono font-bold text-gray-900">₹{{ number_format($cgstAmount, 2) }}</span>
            </div>
            <div class="flex justify-between text-xs font-semibold text-gray-600">
                <span>SGST ({{ $sgstRate }}%)</span>
                <span class="font-mono font-bold text-gray-900">₹{{ number_format($sgstAmount, 2) }}</span>
            </div>
            
            <div class="flex justify-between border-t border-dashed border-gray-200 pt-3 text-base font-black text-gray-900">
                <span>Grand Total</span>
                <span class="font-mono text-orange-600 text-xl font-black">₹{{ number_format($subtotal + $totalTax, 2) }}</span>
            </div>
        </div>

        <!-- Checkout Action Button -->
        <a href="{{ route('public.order.checkout', [$organization->id, $location->id]) }}" class="block w-full text-center bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold py-3.5 rounded-2xl shadow-md transition transform active:scale-98 text-sm uppercase tracking-wider">
            Proceed to Checkout & Place Order &rarr;
        </a>
    @endif
</div>
@endsection

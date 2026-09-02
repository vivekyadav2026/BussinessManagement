@extends('layouts.customer')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 border-b border-stone-200">
        <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" class="p-2.5 rounded-2xl border border-stone-300 bg-white text-[#0F172A] hover:bg-stone-100 shadow-2xs transition font-black text-sm" title="Back to Menu">
            &larr; Back
        </a>
        <div>
            <span class="inline-block bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A] text-[10px] font-mono font-black px-2.5 py-0.5 rounded-full uppercase tracking-widest mb-0.5">
                Review Order
            </span>
            <h1 class="text-2xl font-black text-[#0F172A] tracking-tight">Your Shopping Cart</h1>
        </div>
    </div>

    @if(empty($cart))
        <div class="text-center py-16 bg-white rounded-3xl shadow-xs border border-stone-200 p-8 space-y-4">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-[#0F172A] text-amber-400 text-3xl shadow-xs mx-auto">
                🛒
            </div>
            <div>
                <h2 class="text-xl font-black text-[#0F172A] mb-1">Your cart is empty</h2>
                <p class="text-xs font-semibold text-[#475569]">Looks like you haven't added any dishes yet.</p>
            </div>
            <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" class="inline-block bg-[#0F172A] hover:bg-black text-white font-black px-6 py-3 rounded-2xl shadow-md transition text-xs uppercase tracking-wider">
                Browse Menu & Add Dishes
            </a>
        </div>
    @else
        <!-- Cart Items List Card -->
        <div class="bg-white rounded-3xl shadow-xs border border-stone-200 overflow-hidden divide-y divide-stone-100">
            @php $subtotal = 0; @endphp
            @foreach($cart as $id => $item)
                @php $subtotal += $item['price'] * $item['quantity']; @endphp
                <div class="p-5 flex items-center justify-between gap-4">
                    <div class="flex-grow min-w-0">
                        <h3 class="font-black text-[#0F172A] text-base line-clamp-1 tracking-tight">{{ $item['name'] }}</h3>
                        <p class="text-xs font-bold text-[#475569] font-mono mt-0.5">₹{{ number_format($item['price'], 2) }} each</p>
                    </div>
                    
                    <div class="flex items-center gap-3 shrink-0">
                        <!-- Quantity Stepper Controls -->
                        <div class="flex items-center border-2 border-[#0F172A] rounded-xl overflow-hidden bg-white shadow-2xs">
                            <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $id]) }}" method="POST" class="inline m-0 p-0">
                                @csrf
                                <input type="hidden" name="action" value="decrease">
                                <button type="submit" class="px-3 py-1 text-[#0F172A] hover:bg-stone-100 font-black transition text-sm">&minus;</button>
                            </form>
                            <span class="px-2.5 font-black text-[#0F172A] text-xs font-mono bg-stone-100 border-x border-stone-200 py-1">{{ $item['quantity'] }}</span>
                            <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $id]) }}" method="POST" class="inline m-0 p-0">
                                @csrf
                                <input type="hidden" name="action" value="increase">
                                <button type="submit" class="px-3 py-1 text-[#0F172A] hover:bg-stone-100 font-black transition text-sm">+</button>
                            </form>
                        </div>

                        <!-- Item Total Price -->
                        <div class="font-black text-[#0F172A] text-base font-mono w-20 text-right">
                            ₹{{ number_format($item['price'] * $item['quantity'], 2) }}
                        </div>
                        
                        <!-- Delete Item -->
                        <form action="{{ route('public.order.remove', [$organization->id, $location->id, $id]) }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Remove Item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Bill Summary Card -->
        <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-xs space-y-3">
            <h3 class="text-xs font-black text-[#475569] uppercase tracking-wider border-b border-stone-100 pb-3">Bill Summary Breakdown</h3>
            
            <div class="flex justify-between text-xs font-extrabold text-[#475569]">
                <span>Items Subtotal</span>
                <span class="font-mono text-[#0F172A]">₹{{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-xs font-extrabold text-[#475569]">
                <span>GST Tax (Estimated 5%)</span>
                <span class="font-mono text-[#0F172A]">₹{{ number_format($subtotal * 0.05, 2) }}</span>
            </div>
            
            <div class="flex justify-between border-t-2 border-stone-200 pt-3 text-base font-black text-[#0F172A]">
                <span>Grand Total Bill</span>
                <span class="font-mono text-[#0F172A] text-lg">₹{{ number_format($subtotal * 1.05, 2) }}</span>
            </div>
        </div>

        <!-- Checkout Action Button -->
        <a href="{{ route('public.order.checkout', [$organization->id, $location->id]) }}" class="block w-full text-center bg-[#0F172A] hover:bg-black text-white font-black py-4 rounded-2xl shadow-xl transition transform active:scale-95 text-base uppercase tracking-wider">
            Proceed to Checkout & Place Order &rarr;
        </a>
    @endif
</div>
@endsection

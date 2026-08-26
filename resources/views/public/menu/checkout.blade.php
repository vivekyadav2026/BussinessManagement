@extends('layouts.customer')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('public.order.cart', [$organization->id, $location->id]) }}" class="text-gray-500 hover:text-gray-800 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('public.order.place', [$organization->id, $location->id]) }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Your Details</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Full Name *</label>
                    <input type="text" name="customer_name" class="w-full border-gray-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('customer_name') border-red-300 @enderror" required placeholder="e.g. John Doe">
                    @error('customer_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Phone Number *</label>
                    <input type="tel" name="customer_phone" class="w-full border-gray-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('customer_phone') border-red-300 @enderror" required placeholder="e.g. +91 9876543210">
                    @error('customer_phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Dining Option</h2>
            
            @if(session('restaurant_table_id'))
                @php $table = \App\Models\RestaurantTable::find(session('restaurant_table_id')); @endphp
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-6 h-6 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <div>
                        <h4 class="font-bold text-indigo-900">Dine-in at {{ $table->name }}</h4>
                        <p class="text-xs text-indigo-700 mt-1 leading-relaxed">You scanned the QR code for this table. We'll bring your order straight to you.</p>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <label class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="order_type" value="Takeaway" class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500 animate-none" checked required>
                        <span class="ml-3 font-medium text-gray-900">Takeaway (Pick up at counter)</span>
                    </label>
                    <label class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="order_type" value="Online" class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500 animate-none">
                        <span class="ml-3 font-medium text-gray-900">Delivery / Online</span>
                    </label>
                </div>
            @endif
            
            <div class="mt-4">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Special Notes (Optional)</label>
                <textarea name="special_notes" rows="2" class="w-full border-gray-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 rounded-xl px-4 py-2.5 text-sm outline-none transition" placeholder="e.g. No onions, extra spicy"></textarea>
            </div>
        </div>

        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
            @php 
                $subtotal = 0;
                foreach($cart as $item) { $subtotal += $item['price'] * $item['quantity']; }
                $tax = $subtotal * 0.05;
                $total = $subtotal + $tax;
            @endphp
            <div class="flex justify-between font-bold text-lg text-gray-900">
                <span>Total to Pay</span>
                <span class="font-mono">₹{{ number_format($total, 2) }}</span>
            </div>
            <p class="text-xs text-gray-400 mt-2 text-center">Payment will be collected by the restaurant staff.</p>
        </div>

        <button type="submit" class="block w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 text-lg">
            Place Order
        </button>
    </form>
</div>
@endsection

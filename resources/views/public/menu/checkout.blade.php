@extends('layouts.customer')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('public.order.cart', [$organization->id, $location->id]) }}" class="text-gray-500 hover:text-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('public.order.place', [$organization->id, $location->id]) }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Your Details</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="customer_name" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('customer_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                    <input type="tel" name="customer_phone" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('customer_phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Dining Option</h2>
            
            @if(session('restaurant_table_id'))
                @php $table = \App\Models\RestaurantTable::find(session('restaurant_table_id')); @endphp
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <div>
                        <h4 class="font-bold text-blue-900">Dine-in at {{ $table->name }}</h4>
                        <p class="text-sm text-blue-700 mt-1">You scanned the QR code for this table. We'll bring your order straight to you.</p>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="order_type" value="Takeaway" class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500" checked required>
                        <span class="ml-3 font-medium text-gray-900">Takeaway (Pick up at counter)</span>
                    </label>
                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="order_type" value="Online" class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-3 font-medium text-gray-900">Delivery / Online</span>
                    </label>
                </div>
            @endif
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Special Notes (Optional)</label>
                <textarea name="special_notes" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. No onions, extra spicy"></textarea>
            </div>
        </div>

        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
            @php 
                $subtotal = 0;
                foreach($cart as $item) { $subtotal += $item['price'] * $item['quantity']; }
                $tax = $subtotal * 0.05;
                $total = $subtotal + $tax;
            @endphp
            <div class="flex justify-between font-bold text-lg text-gray-900">
                <span>Total to Pay</span>
                <span>${{ number_format($total, 2) }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">Payment will be collected by the restaurant staff.</p>
        </div>

        <button type="submit" class="block w-full text-center bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 transition transform active:scale-95 text-lg">
            Place Order
        </button>
    </form>
</div>
@endsection

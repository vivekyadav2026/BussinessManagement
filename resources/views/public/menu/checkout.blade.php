@extends('layouts.customer')

@section('content')
<div class="px-4 py-5 max-w-2xl mx-auto space-y-5">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
        <a href="{{ route('public.order.cart', [$organization->id, $location->id]) }}" class="p-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-2xs transition font-extrabold text-xs flex items-center gap-1">
            <span>&larr;</span> Back to Cart
        </a>
        <div class="min-w-0">
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Checkout & Place Order</h1>
            <p class="text-[11px] text-gray-400 font-semibold">Enter your details to confirm your order</p>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-bold shadow-2xs">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('public.order.place', [$organization->id, $location->id]) }}" method="POST" class="space-y-4">
        @csrf
        
        <!-- Customer Contact Details Card -->
        <div class="bg-white rounded-2xl shadow-2xs border border-gray-200/80 p-5 space-y-3.5">
            <h2 class="text-xs font-black text-gray-700 uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center gap-1.5">
                <span>👤</span> Contact Information
            </h2>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Your Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_name" class="w-full border border-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:bg-white bg-gray-50/50 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-900 outline-none transition placeholder-gray-400 @error('customer_name') border-rose-400 @enderror" required placeholder="e.g. Rahul Sharma">
                    @error('customer_name') <span class="text-xs font-semibold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Mobile Phone Number <span class="text-rose-500">*</span></label>
                    <input type="tel" name="customer_phone" class="w-full border border-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:bg-white bg-gray-50/50 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-900 outline-none transition placeholder-gray-400 @error('customer_phone') border-rose-400 @enderror" required placeholder="e.g. 9876543210">
                    @error('customer_phone') <span class="text-xs font-semibold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Dining & Order Type Card -->
        <div class="bg-white rounded-2xl shadow-2xs border border-gray-200/80 p-5 space-y-3.5">
            <h2 class="text-xs font-black text-gray-700 uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center gap-1.5">
                <span>🍽️</span> Dining & Instructions
            </h2>
            
            @if(session('restaurant_table_id'))
                @php $table = \App\Models\RestaurantTable::find(session('restaurant_table_id')); @endphp
                <div class="bg-orange-50/60 border border-orange-200/80 rounded-xl p-3.5 flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center font-black text-base shrink-0 shadow-2xs">
                        🪑
                    </div>
                    <div>
                        <h4 class="font-extrabold text-orange-900 text-sm">Dine-in at Table {{ $table->name ?? '1' }}</h4>
                        <p class="text-[11px] text-orange-800 font-medium mt-0.5 leading-relaxed">Our staff will serve your freshly cooked dishes directly to your table.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-orange-500 hover:bg-orange-50/30 transition">
                        <input type="radio" name="order_type" value="Takeaway" class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500" checked required>
                        <span class="ml-2.5 font-bold text-gray-800 text-xs">🛍️ Takeaway / Self Pick-up</span>
                    </label>
                    <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-orange-500 hover:bg-orange-50/30 transition">
                        <input type="radio" name="order_type" value="Online" class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                        <span class="ml-2.5 font-bold text-gray-800 text-xs">🛵 Delivery / Online Order</span>
                    </label>
                </div>
            @endif
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Cooking Instructions / Notes (Optional)</label>
                <textarea name="special_notes" rows="2" class="w-full border border-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:bg-white bg-gray-50/50 rounded-xl px-3.5 py-2 text-xs font-medium text-gray-900 outline-none transition placeholder-gray-400" placeholder="e.g. Extra spicy, less oil, no garlic"></textarea>
            </div>
        </div>

        <!-- Ordered Items Summary Preview -->
        <div class="bg-white rounded-2xl shadow-2xs border border-gray-200/80 p-5 space-y-2.5">
            <h3 class="text-xs font-black text-gray-500 uppercase tracking-wider border-b border-gray-100 pb-2">Order Summary</h3>
            <div class="space-y-1.5 divide-y divide-gray-50">
                @php $subtotal = 0; @endphp
                @foreach($cart as $item)
                    @php $subtotal += $item['price'] * $item['quantity']; @endphp
                    <div class="pt-1.5 flex justify-between items-center text-xs font-medium text-gray-800">
                        <span><b class="text-orange-600 font-mono font-black mr-1">{{ $item['quantity'] }}x</b> {{ $item['name'] }}</span>
                        <span class="font-mono font-bold text-gray-900">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bill Breakdown & Final Payment Card -->
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
            $total = $subtotal + $totalTax;
        @endphp
        <div class="bg-white text-gray-900 rounded-2xl p-5 shadow-2xs space-y-2.5 border border-gray-200/80">
            <div class="flex justify-between text-xs text-gray-600 font-medium">
                <span>Items Subtotal</span>
                <span class="font-mono font-bold text-gray-900">₹{{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-xs text-gray-600 font-medium">
                <span>CGST ({{ $cgstRate }}%)</span>
                <span class="font-mono font-bold text-gray-900">₹{{ number_format($cgstAmount, 2) }}</span>
            </div>
            <div class="flex justify-between text-xs text-gray-600 font-medium">
                <span>SGST ({{ $sgstRate }}%)</span>
                <span class="font-mono font-bold text-gray-900">₹{{ number_format($sgstAmount, 2) }}</span>
            </div>
            <div class="flex justify-between border-t border-dashed border-gray-200 pt-2.5 text-base font-black text-gray-900">
                <span>Total Amount to Pay</span>
                <span class="font-mono text-orange-600 text-xl">₹{{ number_format($total, 2) }}</span>
            </div>
            <p class="text-[11px] text-gray-400 text-center font-medium pt-1">Payment collected at table/counter after serving.</p>
        </div>

        <button type="submit" class="block w-full text-center bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold py-3.5 rounded-2xl shadow-md transition transform active:scale-98 text-sm uppercase tracking-wider">
            🔥 Confirm & Send Order to Kitchen &rarr;
        </button>
    </form>
</div>
@endsection

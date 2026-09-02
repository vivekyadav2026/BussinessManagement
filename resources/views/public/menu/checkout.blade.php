@extends('layouts.customer')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 border-b border-stone-200">
        <a href="{{ route('public.order.cart', [$organization->id, $location->id]) }}" class="p-2.5 rounded-2xl border border-stone-300 bg-white text-[#0F172A] hover:bg-stone-100 shadow-2xs transition font-black text-sm" title="Back to Cart">
            &larr; Back
        </a>
        <div>
            <span class="inline-block bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A] text-[10px] font-mono font-black px-2.5 py-0.5 rounded-full uppercase tracking-widest mb-0.5">
                Final Step
            </span>
            <h1 class="text-2xl font-black text-[#0F172A] tracking-tight">Checkout & Place Order</h1>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3 rounded-2xl text-xs font-black shadow-xs">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('public.order.place', [$organization->id, $location->id]) }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Customer Contact Details Card -->
        <div class="bg-white rounded-3xl shadow-xs border border-stone-200 p-6 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-stone-100">
                <span class="w-2 h-5 bg-[#0F172A] rounded-full"></span>
                <h2 class="text-xs font-black text-[#0F172A] uppercase tracking-wider">Contact Information</h2>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-extrabold text-[#0F172A] uppercase tracking-wider mb-1.5">Full Name <span class="text-rose-600">*</span></label>
                    <input type="text" name="customer_name" class="w-full border-2 border-stone-200 focus:border-[#0F172A] focus:bg-white bg-stone-50/50 rounded-2xl px-4 py-3 text-xs font-black text-[#0F172A] outline-none transition placeholder-stone-400 @error('customer_name') border-rose-400 @enderror" required placeholder="e.g. Rahul Sharma">
                    @error('customer_name') <span class="text-xs font-bold text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-extrabold text-[#0F172A] uppercase tracking-wider mb-1.5">Mobile Phone Number <span class="text-rose-600">*</span></label>
                    <input type="tel" name="customer_phone" class="w-full border-2 border-stone-200 focus:border-[#0F172A] focus:bg-white bg-stone-50/50 rounded-2xl px-4 py-3 text-xs font-black text-[#0F172A] outline-none transition placeholder-stone-400 @error('customer_phone') border-rose-400 @enderror" required placeholder="e.g. 9876543210">
                    @error('customer_phone') <span class="text-xs font-bold text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Dining & Order Type Card -->
        <div class="bg-white rounded-3xl shadow-xs border border-stone-200 p-6 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-stone-100">
                <span class="w-2 h-5 bg-[#0F172A] rounded-full"></span>
                <h2 class="text-xs font-black text-[#0F172A] uppercase tracking-wider">Dining & Preparation Option</h2>
            </div>
            
            @if(session('restaurant_table_id'))
                @php $table = \App\Models\RestaurantTable::find(session('restaurant_table_id')); @endphp
                <div class="bg-[#FEF3C7]/60 border-2 border-[#FDE68A] rounded-2xl p-4 flex items-start gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#0F172A] text-amber-400 flex items-center justify-center font-black text-lg shrink-0 shadow-xs">
                        🪑
                    </div>
                    <div>
                        <h4 class="font-black text-[#92400E] text-base">Dine-in at Table {{ $table->name ?? '1' }}</h4>
                        <p class="text-xs text-[#92400E] font-bold mt-0.5 leading-relaxed">You scanned Table {{ $table->name ?? '1' }} QR code. Our staff will bring your freshly cooked dishes directly to your table.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="flex items-center p-4 border-2 border-stone-200 rounded-2xl cursor-pointer hover:border-[#0F172A] hover:bg-stone-50 transition">
                        <input type="radio" name="order_type" value="Takeaway" class="w-4 h-4 text-[#0F172A] border-stone-300 focus:ring-[#0F172A]" checked required>
                        <span class="ml-3 font-black text-[#0F172A] text-xs">🛍️ Takeaway / Self Pick-up</span>
                    </label>
                    <label class="flex items-center p-4 border-2 border-stone-200 rounded-2xl cursor-pointer hover:border-[#0F172A] hover:bg-stone-50 transition">
                        <input type="radio" name="order_type" value="Online" class="w-4 h-4 text-[#0F172A] border-stone-300 focus:ring-[#0F172A]">
                        <span class="ml-3 font-black text-[#0F172A] text-xs">🛵 Delivery / Online Order</span>
                    </label>
                </div>
            @endif
            
            <div>
                <label class="block text-xs font-extrabold text-[#0F172A] uppercase tracking-wider mb-1.5">Special Cooking Instructions (Optional)</label>
                <textarea name="special_notes" rows="2" class="w-full border-2 border-stone-200 focus:border-[#0F172A] focus:bg-white bg-stone-50/50 rounded-2xl px-4 py-2.5 text-xs font-bold text-[#0F172A] outline-none transition placeholder-stone-400" placeholder="e.g. Extra spicy, less oil, no garlic"></textarea>
            </div>
        </div>

        <!-- Ordered Items Summary Preview -->
        <div class="bg-white rounded-3xl shadow-xs border border-stone-200 p-6 space-y-3">
            <h3 class="text-xs font-black text-[#475569] uppercase tracking-wider border-b border-stone-100 pb-3">Order Items Summary</h3>
            <div class="space-y-2">
                @php $subtotal = 0; @endphp
                @foreach($cart as $item)
                    @php $subtotal += $item['price'] * $item['quantity']; @endphp
                    <div class="flex justify-between items-center text-xs font-semibold text-[#0F172A]">
                        <span><b class="text-[#0F172A] font-mono font-black mr-1.5">{{ $item['quantity'] }}x</b> {{ $item['name'] }}</span>
                        <span class="font-mono font-black text-[#0F172A]">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bill Breakdown & Final Payment Card -->
        <div class="bg-[#0F172A] text-white rounded-3xl p-6 shadow-xl space-y-3 border border-slate-800">
            @php 
                $tax = $subtotal * 0.05;
                $total = $subtotal + $tax;
            @endphp
            <div class="flex justify-between text-xs text-slate-300 font-semibold">
                <span>Items Subtotal</span>
                <span class="font-mono font-bold">₹{{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-xs text-slate-300 font-semibold">
                <span>GST Tax (5%)</span>
                <span class="font-mono font-bold">₹{{ number_format($tax, 2) }}</span>
            </div>
            <div class="flex justify-between border-t border-slate-800 pt-3 text-lg font-black text-white">
                <span>Total Amount to Pay</span>
                <span class="font-mono text-amber-300">₹{{ number_format($total, 2) }}</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2 text-center font-bold">Payment collected at table/counter after serving.</p>
        </div>

        <button type="submit" class="block w-full text-center bg-[#0F172A] hover:bg-black text-white font-black py-4 rounded-2xl shadow-xl transition transform active:scale-95 text-base uppercase tracking-wider">
            🔥 Confirm & Send Order to Kitchen &rarr;
        </button>
    </form>
</div>
@endsection

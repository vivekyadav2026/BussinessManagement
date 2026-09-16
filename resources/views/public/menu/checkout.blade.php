@extends('layouts.customer')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
        <a href="{{ route('public.order.cart', [$organization->id, $location->id]) }}" 
           class="p-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:text-slate-950 hover:bg-slate-50 shadow-2xs transition flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-black text-slate-950 tracking-tight">Checkout & Place Order</h1>
            <p class="text-[11px] text-slate-500 font-semibold">Enter your contact details and dining preferences</p>
        </div>
    </div>

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-950 text-xs font-bold shadow-2xs flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950 font-bold">&times;</button>
        </div>
    @endif

    <form action="{{ route('public.order.place', [$organization->id, $location->id]) }}" method="POST" class="space-y-5">
        @csrf
        
        <!-- Customer Contact Details Card -->
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/90 p-5 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                <h2 class="text-xs font-black text-slate-950 uppercase tracking-wider">Contact Information</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">
                        Your Full Name <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" 
                           name="customer_name" 
                           value="{{ old('customer_name') }}"
                           class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-950 outline-none transition bg-white placeholder:text-slate-400 @error('customer_name') border-rose-400 bg-rose-50/20 @enderror" 
                           required 
                           placeholder="e.g. Rahul Sharma">
                    @error('customer_name') <span class="text-[11px] font-bold text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">
                        Mobile Phone Number <span class="text-rose-600">*</span>
                    </label>
                    <input type="tel" 
                           name="customer_phone" 
                           value="{{ old('customer_phone') }}"
                           class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-950 outline-none transition bg-white placeholder:text-slate-400 font-mono @error('customer_phone') border-rose-400 bg-rose-50/20 @enderror" 
                           required 
                           placeholder="e.g. 9876543210">
                    @error('customer_phone') <span class="text-[11px] font-bold text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Dining & Order Type Card -->
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/90 p-5 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                <h2 class="text-xs font-black text-slate-950 uppercase tracking-wider">Dining & Kitchen Notes</h2>
            </div>
            
            @if(session('restaurant_table_id'))
                @php $table = \App\Models\RestaurantTable::find(session('restaurant_table_id')); @endphp
                <div class="bg-amber-50/60 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-lg shrink-0 shadow-2xs">
                        🪑
                    </div>
                    <div>
                        <h4 class="font-black text-slate-950 text-sm">Dine-in at Table {{ $table->name ?? '1' }}</h4>
                        <p class="text-xs text-amber-900 font-semibold mt-0.5 leading-relaxed">Your order will be linked to Table {{ $table->name ?? '1' }} and freshly served directly by floor staff.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="flex items-center p-3.5 border border-slate-200 rounded-xl cursor-pointer hover:border-amber-500 hover:bg-amber-50/20 transition group">
                        <input type="radio" name="order_type" value="Takeaway" class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500 cursor-pointer" checked required>
                        <div class="ml-3">
                            <span class="block font-black text-slate-950 text-xs">🛍️ Takeaway / Pickup</span>
                            <span class="text-[10px] font-medium text-slate-500">Collect order at counter</span>
                        </div>
                    </label>
                    <label class="flex items-center p-3.5 border border-slate-200 rounded-xl cursor-pointer hover:border-amber-500 hover:bg-amber-50/20 transition group">
                        <input type="radio" name="order_type" value="Online" class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500 cursor-pointer">
                        <div class="ml-3">
                            <span class="block font-black text-slate-950 text-xs">🛵 Online Delivery</span>
                            <span class="text-[10px] font-medium text-slate-500">Delivered to location</span>
                        </div>
                    </label>
                </div>
            @endif
            
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Special Cooking Instructions (Optional)</label>
                <textarea name="special_notes" rows="2" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-950 outline-none transition placeholder:text-slate-400 bg-white" placeholder="e.g. Mild spicy, less oil, allergy alert, extra chutney">{{ old('special_notes') }}</textarea>
            </div>
        </div>

        <!-- Ordered Items Summary Preview -->
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/90 p-5 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <span class="text-xs font-black text-slate-950 uppercase tracking-wider">Ordered Items</span>
                <span class="text-[11px] font-bold text-slate-500 font-mono">{{ count($cart) }} Dish(es)</span>
            </div>
            
            <div class="space-y-2 divide-y divide-slate-100">
                @php $subtotal = 0; @endphp
                @foreach($cart as $item)
                    @php $subtotal += $item['price'] * $item['quantity']; @endphp
                    <div class="pt-2 first:pt-0 flex justify-between items-center text-xs font-semibold text-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="bg-slate-100 text-slate-900 font-mono font-black px-2 py-0.5 rounded text-[11px] border border-slate-200">{{ $item['quantity'] }}x</span>
                            <span class="font-bold text-slate-950">{{ $item['name'] }}</span>
                        </div>
                        <span class="font-mono font-black text-slate-950">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bill Breakdown & Final Payment Card -->
        @php
            $cgstRate = (float)($organization->cgst_percent ?? 0);
            $sgstRate = (float)($organization->sgst_percent ?? 0);
            $cgstAmount = round(($subtotal * $cgstRate) / 100, 2);
            $sgstAmount = round(($subtotal * $sgstRate) / 100, 2);
            $totalTax = round($cgstAmount + $sgstAmount, 2);
            $total = round($subtotal + $totalTax, 2);
        @endphp
        <div class="bg-white rounded-2xl p-5 shadow-2xs space-y-3 border border-slate-200/90">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                <h3 class="text-xs font-black text-slate-950 uppercase tracking-wider">Payment Breakdown</h3>
            </div>

            <div class="space-y-2 text-xs text-slate-600 font-semibold">
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
                <span>Total Amount to Pay</span>
                <span class="font-mono text-slate-950 text-2xl font-black">₹{{ number_format($total, 2) }}</span>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-600 font-medium text-center">
                💳 Online UPI, Cards, Netbanking & Table Cash supported after placing order.
            </div>
        </div>

        <button type="submit" 
                class="w-full flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-black py-4 px-6 rounded-2xl shadow-md transition transform active:scale-99 text-sm uppercase tracking-wider whitespace-nowrap cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>Confirm & Send Order to Kitchen</span>
            <span>&rarr;</span>
        </button>
    </form>
</div>
@endsection


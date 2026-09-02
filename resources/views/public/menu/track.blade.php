@extends('layouts.customer')

@section('content')
<div class="px-4 py-8 max-w-2xl mx-auto space-y-6">

    <!-- Celebration Header & Success Card -->
    <div class="bg-white rounded-3xl shadow-xs border border-stone-200 p-6 sm:p-8 text-center space-y-5">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-[#0F172A] text-amber-400 text-4xl shadow-sm mx-auto">
            🎉
        </div>
        
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-black uppercase tracking-wider bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A] mb-2">
                Order #{{ $order->order_number }} Confirmed
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-[#0F172A] tracking-tight">Thank You, {{ $order->customer_name }}!</h1>
            <p class="text-xs font-semibold text-[#475569] mt-1">Your order has been sent directly to the kitchen.</p>
        </div>

        <!-- Live Visual Order Progress Step Tracker -->
        <div class="pt-4 border-t border-stone-100">
            <span class="text-[11px] font-black uppercase tracking-wider text-[#475569] block mb-4">Live Order Progress Tracker</span>
            
            <div class="grid grid-cols-4 gap-2 text-center relative">
                <!-- Step 1: Received -->
                <div class="space-y-1">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-xs
                        {{ in_array($order->status, ['Received', 'Preparing', 'Ready', 'Served']) ? 'bg-[#0F172A] text-amber-400' : 'bg-stone-200 text-stone-500' }}">
                        1
                    </div>
                    <span class="text-[10px] font-black block {{ $order->status === 'Received' ? 'text-[#0F172A]' : 'text-[#475569]' }}">Received</span>
                </div>

                <!-- Step 2: Preparing -->
                <div class="space-y-1">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-xs
                        {{ in_array($order->status, ['Preparing', 'Ready', 'Served']) ? 'bg-[#0F172A] text-amber-400' : ($order->status === 'Received' ? 'bg-amber-500 text-white blink' : 'bg-stone-200 text-stone-500') }}">
                        2
                    </div>
                    <span class="text-[10px] font-black block {{ $order->status === 'Preparing' ? 'text-amber-800' : 'text-[#475569]' }}">Cooking</span>
                </div>

                <!-- Step 3: Ready -->
                <div class="space-y-1">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-xs
                        {{ in_array($order->status, ['Ready', 'Served']) ? 'bg-[#0F172A] text-amber-400' : 'bg-stone-200 text-stone-500' }}">
                        3
                    </div>
                    <span class="text-[10px] font-black block {{ $order->status === 'Ready' ? 'text-[#0F172A]' : 'text-[#475569]' }}">Ready</span>
                </div>

                <!-- Step 4: Served -->
                <div class="space-y-1">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-xs
                        {{ $order->status === 'Served' ? 'bg-[#0F172A] text-amber-400' : 'bg-stone-200 text-stone-500' }}">
                        4
                    </div>
                    <span class="text-[10px] font-black block {{ $order->status === 'Served' ? 'text-[#0F172A]' : 'text-[#475569]' }}">Served</span>
                </div>
            </div>
        </div>

        @if($order->status !== 'Served' && $order->status !== 'Cancelled')
            <p class="text-xs text-[#0F172A] font-extrabold animate-pulse pt-2">
                🔄 Status updates live automatically every few seconds...
            </p>
        @endif

        <!-- Quick Action Buttons: Back to Menu / Add More Items -->
        <div class="pt-4 border-t border-stone-100 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" class="px-6 py-3.5 bg-[#0F172A] hover:bg-black text-white font-black text-xs rounded-2xl shadow-md transition flex items-center justify-center gap-2 uppercase tracking-wider">
                <span>📖 Back to Menu / Order More Items</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>

    <!-- Order Items Summary Card -->
    <div class="bg-white rounded-3xl shadow-xs border border-stone-200 p-6 space-y-4">
        <h2 class="font-black text-xs text-[#0F172A] border-b border-stone-100 pb-3 uppercase tracking-wider">Ordered Items Summary</h2>
        <div class="space-y-3">
            @foreach($order->items as $item)
                <div class="flex justify-between items-center text-xs font-semibold">
                    <span class="text-[#0F172A]"><b class="text-[#0F172A] font-mono font-black mr-2">{{ $item->quantity }}x</b> {{ $item->name_snapshot }}</span>
                    <span class="text-[#0F172A] font-black font-mono">₹{{ number_format($item->total, 2) }}</span>
                </div>
            @endforeach
        </div>
        
        <div class="border-t border-stone-100 pt-3 space-y-2 text-xs font-semibold">
            <div class="flex justify-between text-[#475569]">
                <span>Subtotal</span>
                <span class="font-mono text-[#0F172A] font-bold">₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-[#475569]">
                <span>GST Tax</span>
                <span class="font-mono text-[#0F172A] font-bold">₹{{ number_format($order->tax, 2) }}</span>
            </div>
            <div class="flex justify-between font-black text-base text-[#0F172A] pt-3 border-t border-stone-200">
                <span>Total Bill</span>
                <span class="font-mono text-[#0F172A] text-lg">₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- Payment Status Card -->
        <div class="pt-4 border-t border-stone-100 flex flex-col items-center">
            @if($order->payment_status === 'Paid')
                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-100 text-emerald-950 rounded-full text-xs font-black border border-emerald-300">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Payment Status: PAID</span>
                </div>
            @elseif($order->status !== 'Cancelled')
                <div class="w-full bg-[#0F172A] text-white rounded-2xl p-5 text-center space-y-3 shadow-md border border-slate-800">
                    <p class="text-xs text-slate-200 font-bold">Pay instantly via <b>UPI (GPay, PhonePe, Paytm, BHIM)</b> or Card / Netbanking</p>
                    <button id="rzp-order-pay-btn" class="w-full bg-amber-400 hover:bg-amber-300 text-[#0F172A] font-black py-3.5 px-6 rounded-xl shadow-lg transition flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        Pay ₹{{ number_format($order->total, 2) }} Online Now
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

@if($order->payment_status !== 'Paid' && $order->status !== 'Cancelled')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var key = "{{ $key ?? '' }}";
    var orderId = "{{ $payment->razorpay_order_id ?? '' }}";
    var btn = document.getElementById('rzp-order-pay-btn');
    if (!btn) return;

    btn.onclick = function(e) {
        e.preventDefault();
        if (!key || key === 'rzp_test_xxxxxxxxx' || !orderId) {
            window.location.href = "{{ route('payment.order', $order->id) }}";
            return;
        }

        var options = {
            "key": key,
            "amount": "{{ round($order->total * 100) }}",
            "currency": "INR",
            "name": "{{ $organization->name }}",
            "description": "Payment for Order #{{ $order->order_number }}",
            "order_id": orderId,
            "prefill": {
                "name": "{{ $order->customer_name }}",
                "contact": "{{ $order->customer_phone }}"
            },
            "theme": { "color": "#0F172A" },
            "handler": function (response) {
                location.reload();
            }
        };
        var rzp = new Razorpay(options);
        rzp.on('payment.failed', function(res){ alert("Payment failed: " + res.error.description); });
        rzp.open();
    };
});
</script>
@endif

@if($order->status !== 'Served' && $order->status !== 'Cancelled')
<script>
    setTimeout(function() {
        window.location.reload();
    }, 15000);
</script>
@endif
@endsection

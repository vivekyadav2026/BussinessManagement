@extends('layouts.customer')

@section('content')
<div class="px-4 py-8 max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-center p-8 mb-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        
        <h1 class="text-2xl font-black text-gray-900 mb-2">Order {{ $order->order_number }}</h1>
        <p class="text-gray-500 mb-8">Hi {{ $order->customer_name }}, your order has been received.</p>
        
        <div class="bg-gray-50 rounded-xl p-4 inline-block mx-auto border mb-6">
            <div class="text-xs uppercase tracking-wider font-bold text-gray-500 mb-1">Current Status</div>
            <div class="text-xl font-black text-blue-600">{{ strtoupper($order->status) }}</div>
        </div>
        
        @if($order->status !== 'Served' && $order->status !== 'Cancelled')
            <p class="text-sm text-gray-400 mb-4 animate-pulse">This page will automatically update.</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-900 mb-4 border-b pb-2">Order Summary</h2>
        <div class="space-y-3 mb-6">
            @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-700"><span class="font-bold mr-2">{{ $item->quantity }}x</span> {{ $item->name_snapshot }}</span>
                    <span class="text-gray-900 font-medium font-mono">₹{{ number_format($item->total, 2) }}</span>
                </div>
            @endforeach
        </div>
        
        <div class="border-t border-gray-100 pt-4 space-y-2">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <span class="font-mono">₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>Tax</span>
                <span class="font-mono">₹{{ number_format($order->tax, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg text-gray-900 pt-2 border-t mt-2">
                <span>Total</span>
                <span class="font-mono">₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col items-center">
            @if($order->payment_status === 'Paid')
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Payment Status: PAID
                </div>
            @elseif($order->status !== 'Cancelled')
                <div class="w-full bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
                    <p class="text-xs text-blue-700 font-semibold mb-3">Scan with <strong>GPay, PhonePe, Paytm, BHIM</strong> or pay via Card/Netbanking</p>
                    <button id="rzp-order-pay-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        Pay ₹{{ number_format($order->total, 2) }} Online
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
            "theme": { "color": "#2563eb" },
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


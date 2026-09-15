@extends('layouts.customer')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto space-y-5">

    <!-- Celebration Header & Success Card -->
    <div class="bg-white rounded-3xl shadow-2xs border border-gray-100 p-6 text-center space-y-4">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-50 text-orange-500 text-3xl shadow-2xs mx-auto">
            🎉
        </div>
        
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-orange-50 text-orange-800 border border-orange-200 mb-1.5">
                Token #{{ $order->order_number }}
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Order Placed, {{ $order->customer_name }}!</h1>
            <p class="text-xs font-medium text-gray-500 mt-0.5">Your order has been dispatched to the kitchen.</p>
        </div>

        <!-- Live Visual Order Progress Step Tracker -->
        <div class="pt-4 border-t border-gray-100">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400 block mb-3">Kitchen Progress Tracker</span>
            
            <div class="grid grid-cols-4 gap-2 text-center relative">
                <!-- Step 1: Received -->
                <div class="space-y-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-2xs
                        {{ in_array($order->status, ['Received', 'Preparing', 'Ready', 'Served']) ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-400' }}">
                        1
                    </div>
                    <span class="text-[10px] font-bold block {{ $order->status === 'Received' ? 'text-orange-600' : 'text-gray-500' }}">Received</span>
                </div>

                <!-- Step 2: Preparing -->
                <div class="space-y-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-2xs
                        {{ in_array($order->status, ['Preparing', 'Ready', 'Served']) ? 'bg-orange-500 text-white' : ($order->status === 'Received' ? 'bg-orange-100 text-orange-600 animate-pulse' : 'bg-gray-100 text-gray-400') }}">
                        2
                    </div>
                    <span class="text-[10px] font-bold block {{ $order->status === 'Preparing' ? 'text-orange-600' : 'text-gray-500' }}">Cooking</span>
                </div>

                <!-- Step 3: Ready -->
                <div class="space-y-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-2xs
                        {{ in_array($order->status, ['Ready', 'Served']) ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-400' }}">
                        3
                    </div>
                    <span class="text-[10px] font-bold block {{ $order->status === 'Ready' ? 'text-orange-600' : 'text-gray-500' }}">Ready</span>
                </div>

                <!-- Step 4: Served -->
                <div class="space-y-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-2xs
                        {{ $order->status === 'Served' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                        4
                    </div>
                    <span class="text-[10px] font-bold block {{ $order->status === 'Served' ? 'text-emerald-700' : 'text-gray-500' }}">Served</span>
                </div>
            </div>
        </div>

        @if($order->status !== 'Served' && $order->status !== 'Cancelled')
            <p class="text-xs text-orange-600 font-bold animate-pulse pt-2">
                🔄 Status refreshes live automatically...
            </p>
        @endif

        <!-- Quick Action Buttons: Back to Menu / Add More Items -->
        <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row gap-2.5 justify-center">
            <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" class="px-5 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 font-extrabold text-xs rounded-xl shadow-2xs transition flex items-center justify-center gap-2">
                <span>📖 Back to Menu / Order More</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>

    <!-- Order Items Summary Card -->
    <div class="bg-white rounded-2xl shadow-2xs border border-gray-200/80 p-5 space-y-3">
        <h2 class="font-extrabold text-xs text-gray-500 border-b border-gray-100 pb-2 uppercase tracking-wider">Ordered Items Summary</h2>
        <div class="space-y-2">
            @foreach($order->items as $item)
                <div class="flex justify-between items-center text-xs font-medium text-gray-800">
                    <span><b class="text-orange-600 font-mono font-black mr-1">{{ $item->quantity }}x</b> {{ $item->name_snapshot }}</span>
                    <span class="font-bold font-mono text-gray-900">₹{{ number_format($item->total, 2) }}</span>
                </div>
            @endforeach
        </div>
        
        <div class="border-t border-dashed border-gray-200 pt-3 space-y-1.5 text-xs text-gray-600">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span class="font-mono font-bold text-gray-900">₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @php
                $cgstRate = (float)($organization->cgst_percent ?? 0);
                $sgstRate = (float)($organization->sgst_percent ?? 0);
                if ($cgstRate <= 0 && $sgstRate <= 0) {
                    $cgstRate = 2.5;
                    $sgstRate = 2.5;
                }
                $cgstAmount = round(($order->subtotal * $cgstRate) / 100, 2);
                $sgstAmount = round(($order->subtotal * $sgstRate) / 100, 2);
                if ($order->tax > 0 && ($cgstAmount + $sgstAmount) <= 0) {
                    $cgstAmount = round($order->tax / 2, 2);
                    $sgstAmount = round($order->tax / 2, 2);
                }
            @endphp
            @if($cgstAmount > 0)
            <div class="flex justify-between text-gray-500 text-[11px]">
                <span>CGST ({{ $cgstRate }}%)</span>
                <span class="font-mono font-bold text-gray-900">₹{{ number_format($cgstAmount, 2) }}</span>
            </div>
            @endif
            @if($sgstAmount > 0)
            <div class="flex justify-between text-gray-500 text-[11px]">
                <span>SGST ({{ $sgstRate }}%)</span>
                <span class="font-mono font-bold text-gray-900">₹{{ number_format($sgstAmount, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between font-black text-base text-gray-900 pt-2.5 border-t border-gray-200">
                <span>Total Bill</span>
                <span class="font-mono text-orange-600 text-lg">₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- Payment Status Card -->
        <div class="pt-3 border-t border-gray-100 flex flex-col items-center">
            @if($order->payment_status === 'Paid')
                <div class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-50 text-emerald-800 rounded-full text-xs font-extrabold border border-emerald-200">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Payment Status: PAID</span>
                </div>
            @elseif($order->status !== 'Cancelled')
                <div class="w-full bg-orange-50 border border-orange-200 text-orange-950 rounded-2xl p-4 text-center space-y-2.5">
                    <p class="text-xs text-orange-800 font-semibold">Pay online via <b>UPI (GPay, PhonePe, Paytm, QR)</b> or Netbanking</p>
                    <button id="rzp-order-pay-btn" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-3 px-5 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-xs uppercase tracking-wider active:scale-98">
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

    var isMock = !key || key.includes('xxxx') || !orderId || orderId.startsWith('order_mock_') || orderId.startsWith('order_sandbox_');

    function verifyAndComplete(payId, signature) {
        fetch('{{ route("payments.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                razorpay_order_id: orderId,
                razorpay_payment_id: payId,
                razorpay_signature: signature
            })
        })
        .then(res => res.json())
        .then(data => {
            window.location.reload();
        })
        .catch(err => {
            console.error(err);
            window.location.reload();
        });
    }

    btn.onclick = function(e) {
        e.preventDefault();

        if (isMock) {
            if (confirm("Test Mode: Simulate online payment completion for Order #{{ $order->order_number }}?")) {
                verifyAndComplete('pay_mock_' + Date.now(), null);
            }
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
            "theme": { "color": "#f97316" },
            "handler": function (response) {
                verifyAndComplete(response.razorpay_payment_id, response.razorpay_signature);
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

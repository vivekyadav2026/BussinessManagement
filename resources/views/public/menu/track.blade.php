@extends('layouts.customer')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto space-y-5">

    <!-- Celebration Header & Success Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-6 text-center space-y-5">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-500 border border-amber-500/20 text-3xl shadow-xs mx-auto">
            🍳
        </div>
        
        <div class="space-y-1.5">
            <div class="flex items-center justify-center gap-2">
                <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-mono font-black uppercase tracking-wider bg-slate-950 text-amber-400 border border-slate-800 shadow-xs">
                    Token #{{ $order->order_number }}
                </span>
                @if($order->table)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                        {{ $order->table->name }}
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-black text-slate-950 tracking-tight">Order Placed, {{ $order->customer_name }}!</h1>
            <p class="text-xs font-medium text-slate-500">Your order has been received and dispatched to the kitchen staff.</p>
        </div>

        <!-- Live Visual Order Progress Step Tracker -->
        <div class="pt-5 border-t border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Live Kitchen Timeline</span>
                <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-md
                    @if($order->status === 'Served') bg-emerald-50 text-emerald-800 border border-emerald-200
                    @elseif($order->status === 'Ready') bg-blue-50 text-blue-800 border border-blue-200
                    @elseif($order->status === 'Preparing') bg-amber-50 text-amber-800 border border-amber-200
                    @else bg-slate-100 text-slate-800 border border-slate-200 @endif">
                    Current: {{ $order->status }}
                </span>
            </div>
            
            <div class="relative">
                <!-- Connecting Line Behind Steps -->
                <div class="absolute top-4 left-6 right-6 h-0.5 bg-slate-200 -z-0"></div>
                <div class="absolute top-4 left-6 h-0.5 bg-amber-500 transition-all duration-500 -z-0"
                    style="width: {{ $order->status === 'Served' ? '100%' : ($order->status === 'Ready' ? '66%' : ($order->status === 'Preparing' ? '33%' : '0%')) }}; max-width: calc(100% - 3rem);"></div>

                <div class="grid grid-cols-4 gap-2 text-center relative z-10">
                    <!-- Step 1: Received -->
                    <div class="space-y-1.5">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-xs
                            {{ in_array($order->status, ['Received', 'Preparing', 'Ready', 'Served']) ? 'bg-slate-950 text-amber-400 border-2 border-amber-400' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                            @if(in_array($order->status, ['Preparing', 'Ready', 'Served']))
                                ✓
                            @else
                                1
                            @endif
                        </div>
                        <span class="text-[11px] font-black block {{ $order->status === 'Received' ? 'text-slate-950' : 'text-slate-500' }}">Received</span>
                    </div>

                    <!-- Step 2: Preparing -->
                    <div class="space-y-1.5">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-xs
                            {{ in_array($order->status, ['Preparing', 'Ready', 'Served']) ? 'bg-slate-950 text-amber-400 border-2 border-amber-400' : ($order->status === 'Received' ? 'bg-amber-100 text-amber-800 border-2 border-amber-400 animate-pulse' : 'bg-slate-100 text-slate-400 border border-slate-200') }}">
                            @if(in_array($order->status, ['Ready', 'Served']))
                                ✓
                            @else
                                2
                            @endif
                        </div>
                        <span class="text-[11px] font-black block {{ $order->status === 'Preparing' ? 'text-amber-600' : 'text-slate-500' }}">Cooking</span>
                    </div>

                    <!-- Step 3: Ready -->
                    <div class="space-y-1.5">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-xs
                            {{ in_array($order->status, ['Ready', 'Served']) ? 'bg-slate-950 text-amber-400 border-2 border-amber-400' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                            @if($order->status === 'Served')
                                ✓
                            @else
                                3
                            @endif
                        </div>
                        <span class="text-[11px] font-black block {{ $order->status === 'Ready' ? 'text-amber-600' : 'text-slate-500' }}">Ready</span>
                    </div>

                    <!-- Step 4: Served -->
                    <div class="space-y-1.5">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-xs font-black transition shadow-xs
                            {{ $order->status === 'Served' ? 'bg-emerald-600 text-white border-2 border-emerald-400' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                            @if($order->status === 'Served')
                                ✓
                            @else
                                4
                            @endif
                        </div>
                        <span class="text-[11px] font-black block {{ $order->status === 'Served' ? 'text-emerald-700' : 'text-slate-500' }}">Served</span>
                    </div>
                </div>
            </div>
        </div>

        @if($order->status !== 'Served' && $order->status !== 'Cancelled')
            <div class="flex items-center justify-center gap-2 pt-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                <p class="text-xs text-slate-600 font-bold">
                    Kitchen status refreshes live automatically
                </p>
            </div>
        @endif

        <!-- Quick Action Buttons: Back to Menu / Add More Items -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row gap-2.5 justify-center">
            <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" class="px-5 py-3 bg-slate-950 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-2 active:scale-98">
                <span>📖 Back to Menu / Order More Items</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>

    <!-- Order Items Summary Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="font-black text-xs text-slate-950 uppercase tracking-wider flex items-center gap-1.5">
                <span>Ordered Items Summary</span>
                <span class="text-[11px] font-mono text-slate-500">({{ $order->items->count() }})</span>
            </h2>
            <span class="text-[11px] font-mono text-slate-400">{{ $order->created_at->format('h:i A, d M') }}</span>
        </div>

        <div class="divide-y divide-slate-100 divide-dashed">
            @foreach($order->items as $item)
                <div class="py-2.5 flex justify-between items-center text-xs">
                    <div class="flex items-center gap-2 min-w-0 pr-2">
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-900 font-mono font-black text-xs shrink-0">
                            {{ $item->quantity }}x
                        </span>
                        <span class="font-bold text-slate-900 truncate">{{ $item->name_snapshot }}</span>
                    </div>
                    <span class="font-black font-mono text-slate-950 shrink-0">₹{{ number_format($item->total, 2) }}</span>
                </div>
            @endforeach
        </div>
        
        <div class="border-t border-slate-200 pt-3.5 space-y-2 text-xs text-slate-600">
            <div class="flex justify-between">
                <span class="font-medium text-slate-500">Subtotal</span>
                <span class="font-mono font-bold text-slate-900">₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @php
                $cgstRate = (float)($organization->cgst_percent ?? 0);
                $sgstRate = (float)($organization->sgst_percent ?? 0);
                $cgstAmount = isset($order->cgst) && (float)$order->cgst > 0 
                    ? (float)$order->cgst 
                    : round(($order->subtotal * $cgstRate) / 100, 2);
                $sgstAmount = isset($order->sgst) && (float)$order->sgst > 0 
                    ? (float)$order->sgst 
                    : round(($order->subtotal * $sgstRate) / 100, 2);

                if ((float)$order->tax <= 0) {
                    $cgstAmount = 0;
                    $sgstAmount = 0;
                } elseif (($cgstAmount + $sgstAmount) <= 0 && (float)$order->tax > 0) {
                    $cgstAmount = round($order->tax / 2, 2);
                    $sgstAmount = round($order->tax / 2, 2);
                }
            @endphp
            @if($cgstAmount > 0)
            <div class="flex justify-between text-slate-500 text-[11px]">
                <span>Central GST ({{ $cgstRate > 0 ? $cgstRate : round(($cgstAmount / ($order->subtotal ?: 1)) * 100, 1) }}%)</span>
                <span class="font-mono font-semibold text-slate-900">₹{{ number_format($cgstAmount, 2) }}</span>
            </div>
            @endif
            @if($sgstAmount > 0)
            <div class="flex justify-between text-slate-500 text-[11px]">
                <span>State GST ({{ $sgstRate > 0 ? $sgstRate : round(($sgstAmount / ($order->subtotal ?: 1)) * 100, 1) }}%)</span>
                <span class="font-mono font-semibold text-slate-900">₹{{ number_format($sgstAmount, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between items-baseline pt-3 border-t border-slate-200">
                <span class="font-black text-sm uppercase tracking-tight text-slate-950">Grand Total</span>
                <span class="font-mono font-black text-amber-600 text-xl">₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- Payment Status Card -->
        <div class="pt-4 border-t border-slate-100">
            @if($order->payment_status === 'Paid')
                <div class="w-full bg-emerald-50 text-emerald-900 border border-emerald-200 rounded-2xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">
                            ✓
                        </div>
                        <div>
                            <span class="text-xs font-black block uppercase tracking-wider text-emerald-950">Payment Settled</span>
                            <span class="text-[11px] font-medium text-emerald-800">Your bill has been paid in full. Thank you!</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md bg-emerald-200/80 text-emerald-950 text-[10px] font-black uppercase font-mono tracking-wider">
                        PAID
                    </span>
                </div>
            @elseif($order->status !== 'Cancelled')
                <div class="w-full bg-slate-950 text-white border border-slate-800 rounded-2xl p-5 space-y-3.5">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black text-amber-400 uppercase tracking-widest block">Instant Digital Payment</span>
                            <h4 class="text-sm font-black text-white">Pay via UPI QR, Cards, or Netbanking</h4>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            Payment Pending
                        </span>
                    </div>

                    <button id="rzp-order-pay-btn" class="w-full bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 font-black py-3.5 px-5 rounded-xl shadow-lg shadow-amber-500/10 transition flex items-center justify-center gap-2 text-xs uppercase tracking-wider active:scale-98 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span>Pay ₹{{ number_format($order->total, 2) }} Online Now</span>
                    </button>
                    <p class="text-[11px] text-slate-400 text-center font-medium">
                        Instant confirmation with Razorpay secure checkout. Cash / Counter payment also accepted.
                    </p>
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
            "theme": { "color": "#020617" },
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

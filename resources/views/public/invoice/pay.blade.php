<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-4 md:p-8">

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-8 border-b border-gray-100 bg-gray-900 text-white flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">{{ $invoice->organization->name }}</h1>
                <p class="text-gray-400 text-sm">{{ $invoice->location->name ?? 'HQ' }}</p>
            </div>
            <div class="text-right">
                <div class="text-xl font-black">INVOICE</div>
                <div class="text-gray-400 text-sm">#{{ $invoice->invoice_number }}</div>
            </div>
        </div>

        <div class="p-8 grid grid-cols-2 gap-8 border-b border-gray-100">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Billed To</h3>
                <p class="font-bold text-gray-900 text-lg">{{ $invoice->client->name ?? 'Walk-in Customer' }}</p>
                @if(optional($invoice->client)->email)<p class="text-gray-600 text-sm">{{ $invoice->client->email }}</p>@endif
                @if(optional($invoice->client)->phone)<p class="text-gray-600 text-sm">{{ $invoice->client->phone }}</p>@endif
            </div>
            <div class="text-right">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Payment Details</h3>
                <p class="text-sm text-gray-600">Date: <span class="font-medium text-gray-900">{{ $invoice->invoice_date->format('M d, Y') }}</span></p>
                <p class="text-sm text-gray-600">Due: <span class="font-medium text-gray-900">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '-' }}</span></p>
                <div class="mt-2 inline-block px-3 py-1 rounded-full text-xs font-bold 
                    {{ $invoice->status == 'Paid' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $invoice->status == 'Partially Paid' ? 'bg-orange-100 text-orange-800' : '' }}
                    {{ $invoice->status == 'Due' || $invoice->status == 'Draft' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $invoice->status == 'Overdue' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $invoice->status == 'Cancelled' ? 'bg-gray-100 text-gray-600' : '' }}
                ">
                    {{ $invoice->status }}
                </div>
            </div>
        </div>

        <div class="p-8">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-3 text-xs font-bold text-gray-400 uppercase">Item</th>
                        <th class="py-3 text-xs font-bold text-gray-400 uppercase text-right">Qty</th>
                        <th class="py-3 text-xs font-bold text-gray-400 uppercase text-right">Price</th>
                        <th class="py-3 text-xs font-bold text-gray-400 uppercase text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($invoice->items as $item)
                    <tr class="border-b border-gray-100">
                        <td class="py-4 font-medium text-gray-900">{{ $item->product_name_snapshot }}</td>
                        <td class="py-4 text-right text-gray-600">{{ $item->quantity }}</td>
                        <td class="py-4 text-right text-gray-600">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-4 text-right font-medium text-gray-900">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-8 bg-gray-50 flex justify-end">
            <div class="w-64 space-y-3 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Tax</span>
                    <span>₹{{ number_format($invoice->tax, 2) }}</span>
                </div>
                @if($invoice->discount > 0)
                <div class="flex justify-between text-green-600 border-b border-gray-200 pb-3">
                    <span>Discount</span>
                    <span>-₹{{ number_format($invoice->discount, 2) }}</span>
                </div>
                @else
                <div class="border-b border-gray-200"></div>
                @endif
                <div class="flex justify-between font-black text-gray-900 text-lg pt-1">
                    <span>Total</span>
                    <span>₹{{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Amount Paid</span>
                    <span>₹{{ number_format($invoice->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between font-black text-xl pt-2 border-t border-gray-300 {{ $invoice->amount_due > 0 ? 'text-indigo-600' : 'text-green-600' }}">
                    <span>Balance Due</span>
                    <span>₹{{ number_format($invoice->amount_due, 2) }}</span>
                </div>
            </div>
        </div>
        
        @if($invoice->amount_due > 0 && $invoice->status !== 'Cancelled')
        <div class="p-8 bg-indigo-50/70 border-t border-indigo-100 text-center space-y-6">
            <div>
                <h2 class="text-xl font-extrabold text-indigo-950 mb-1">Pay Outstanding Invoice</h2>
                <p class="text-indigo-700 text-xs font-medium">Scan QR code using any UPI App or click to pay online.</p>
            </div>

            @if($invoice->organization?->upi_id)
            <div class="max-w-xs mx-auto bg-white p-5 rounded-2xl border border-indigo-200/80 shadow-xs flex flex-col items-center justify-center space-y-3">
                <span class="text-xs font-black uppercase text-indigo-950 tracking-wider">📱 Scan &amp; Pay via UPI</span>
                <div id="publicUpiQrCode" class="p-2 bg-white border border-slate-200 rounded-xl shadow-2xs"></div>
                <div class="text-xs font-mono font-bold text-slate-800 bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100">
                    {{ $invoice->organization->upi_id }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium">Scan via GPay, PhonePe, Paytm, BHIM or any UPI App</p>
            </div>
            @endif

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-4">
                <button id="rzp-pay-button" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-md transition-colors flex items-center justify-center gap-2 text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Pay ₹{{ number_format($invoice->amount_due, 2) }} via Gateway
                </button>
            </div>
            
            <p class="text-xs text-indigo-400">Protected by 256-bit Encryption</p>
        </div>
        @endif
    </div>

    <!-- Public Support Complaint Link -->
    <div class="mt-6 text-center">
        <a href="{{ route('public.complaint.create', ['org_id' => $invoice->organization_id, 'invoice_id' => $invoice->id]) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-amber-600 transition bg-white px-4 py-2.5 rounded-xl border border-slate-200 shadow-2xs">
            <span>📝 Have an issue with this invoice? Submit a Support Complaint &amp; Ticket</span>
            <span>&rarr;</span>
        </a>
    </div>
</div>

@if($invoice->amount_due > 0 && $invoice->status !== 'Cancelled')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var key = "{{ $key ?? '' }}";
    var orderId = "{{ $payment->razorpay_order_id ?? '' }}";
    var invoiceId = "{{ $invoice->id }}";
    var amountInPaise = "{{ round($invoice->amount_due * 100) }}";
    var isMock = !key || key.includes('xxxx') || !orderId || orderId.startsWith('order_mock_') || orderId.startsWith('order_sandbox_');

    var payButton = document.getElementById('rzp-pay-button');
    if (!payButton) return;

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

    payButton.onclick = function(e) {
        e.preventDefault();

        if (isMock) {
            if (confirm("Test Mode: Simulate payment completion for Invoice #{{ $invoice->invoice_number }}?")) {
                verifyAndComplete('pay_mock_' + Date.now(), null);
            }
            return;
        }

        var options = {
            "key": key,
            "amount": amountInPaise,
            "currency": "INR",
            "name": "{{ $invoice->organization->name }}",
            "description": "Payment for Invoice #{{ $invoice->invoice_number }}",
            "order_id": orderId,
            "prefill": {
                "name": "{{ $invoice->client->name ?? '' }}",
                "email": "{{ $invoice->client->email ?? '' }}",
                "contact": "{{ $invoice->client->phone ?? '' }}"
            },
            "theme": {
                "color": "#4f46e5"
            },
            "handler": function (response) {
                verifyAndComplete(response.razorpay_payment_id, response.razorpay_signature);
            }
        };

        var rzp = new Razorpay(options);
        rzp.on('payment.failed', function (response) {
            alert("Payment failed: " + response.error.description);
        });
        rzp.open();
    };
});
</script>
@endif

@if($invoice->organization?->upi_id && $invoice->amount_due > 0)
@php
    $dueAmountStr = number_format($invoice->amount_due, 2, '.', '');
@endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const qrEl = document.getElementById("publicUpiQrCode");
        if (qrEl) {
            const upiVpa = "{{ $invoice->organization->upi_id }}";
            const orgName = "{{ addslashes($invoice->organization->name) }}";
            const amount = "{{ $dueAmountStr }}";
            const invNum = "{{ $invoice->invoice_number }}";
            const upiString = `upi://pay?pa=${encodeURIComponent(upiVpa)}&pn=${encodeURIComponent(orgName)}&am=${amount}&cu=INR&tn=${encodeURIComponent('Invoice ' + invNum)}`;

            new QRCode(qrEl, {
                text: upiString,
                width: 140,
                height: 140,
                colorDark: "#0f172a",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    });
</script>
@endif

</body>
</html>


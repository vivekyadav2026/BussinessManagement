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
                <p class="font-bold text-gray-900 text-lg">{{ $invoice->client->name }}</p>
                <p class="text-gray-600 text-sm">{{ $invoice->client->email }}</p>
                <p class="text-gray-600 text-sm">{{ $invoice->client->phone }}</p>
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
        <div class="p-8 bg-indigo-50 border-t border-indigo-100 text-center">
            <h2 class="text-xl font-bold text-indigo-900 mb-2">Ready to Pay?</h2>
            <p class="text-indigo-700 mb-4 text-sm">Scan with <strong>any UPI app</strong> (Google Pay, PhonePe, Paytm, BHIM, Camera) or pay using Card/Netbanking.</p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-4">
                <button id="rzp-pay-button" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-md transition-colors flex items-center justify-center gap-2 text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Pay ₹{{ number_format($invoice->amount_due, 2) }} via Razorpay / UPI QR
                </button>
            </div>
            
            <p class="text-xs text-indigo-400 mt-4">Protected by Razorpay 256-bit Encryption</p>
        </div>
        @endif
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

    var payButton = document.getElementById('rzp-pay-button');
    if (!payButton) return;

    payButton.onclick = function(e) {
        e.preventDefault();

        if (!key || key === 'rzp_test_xxxxxxxxx' || !orderId) {
            // Fallback to direct checkout route if order couldn't be auto-created
            window.location.href = "{{ route('payment.invoice', $invoice->id) }}";
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
                document.body.innerHTML = `
                    <div class="bg-gray-50 h-screen flex flex-col justify-center items-center p-4 text-center">
                        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h1 class="text-3xl font-black text-gray-900 mb-2">Payment Completed!</h1>
                        <p class="text-gray-500 mb-6">Thank you for your payment. Your invoice will update shortly.</p>
                        <a href="${window.location.href}" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow">View Updated Invoice</a>
                    </div>
                `;
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

</body>
</html>


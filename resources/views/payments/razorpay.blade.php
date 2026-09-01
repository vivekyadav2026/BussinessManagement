<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment - {{ $name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col justify-center items-center p-4">

<div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 max-w-md w-full text-center">
    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
    </div>
    
    <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $name }}</h1>
    <p class="text-gray-500 text-sm mb-6">{{ $description }}</p>
    
    <div class="bg-blue-50/70 border border-blue-100 rounded-2xl p-4 mb-6">
        <div class="text-xs uppercase tracking-wider font-semibold text-blue-600 mb-1">Total Amount Payable</div>
        <div class="text-4xl font-black text-gray-900">₹{{ number_format($amount, 2) }}</div>
    </div>

    <div class="text-left bg-gray-50 rounded-xl p-4 mb-6 border border-gray-100 text-xs text-gray-600 space-y-2">
        <div class="font-bold text-gray-800">Supported Payment Methods:</div>
        <div class="flex items-center gap-2 text-gray-600">
            <span class="w-2 h-2 rounded-full bg-green-500"></span>
            Scan UPI QR with <strong>Google Pay, PhonePe, Paytm, BHIM</strong> or any Scanner App
        </div>
        <div class="flex items-center gap-2 text-gray-600">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            Credit/Debit Cards, Net Banking & Wallets
        </div>
    </div>
    
    <button id="rzp-button1" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition duration-200 text-lg flex items-center justify-center gap-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
        Pay / Scan QR Now
    </button>
    <p class="text-xs text-gray-400 mt-4 text-center">Secured by Razorpay. Do not refresh this page while payment is processing.</p>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "{{ $key }}",
    "amount": "{{ round($amount * 100) }}",
    "currency": "INR",
    "name": "{{ $name }}",
    "description": "{{ $description }}",
    "order_id": "{{ $payment->razorpay_order_id }}",
    "handler": function (response){
        document.body.innerHTML = `
            <div class="bg-gray-50 min-h-screen flex flex-col justify-center items-center p-4 text-center">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h1 class="text-3xl font-black text-gray-900 mb-2">Payment Successful!</h1>
                <p class="text-gray-500 mb-6">Thank you for your payment. You may close this page.</p>
            </div>
        `;
    },
    "prefill": {
        "name": "",
        "email": "",
        "contact": ""
    },
    "theme": {
        "color": "#2563eb"
    }
};
var rzp1 = new Razorpay(options);
rzp1.on('payment.failed', function (response){
    alert("Payment failed: " + response.error.description);
});
document.getElementById('rzp-button1').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}
</script>

</body>
</html>


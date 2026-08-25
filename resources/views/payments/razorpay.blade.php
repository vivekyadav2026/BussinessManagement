<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 h-screen flex flex-col justify-center items-center p-4">

<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 max-w-md w-full text-center">
    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
    </div>
    
    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $name }}</h1>
    <p class="text-gray-500 mb-6">{{ $description }}</p>
    
    <div class="text-4xl font-black text-gray-900 mb-8">₹{{ number_format($amount, 2) }}</div>
    
    <button id="rzp-button1" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow transition text-lg">
        Pay Now
    </button>
    <p class="text-xs text-gray-400 mt-4 text-center">Do not close this window while payment is processing.</p>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "{{ $key }}", // Enter the Key ID generated from the Dashboard
    "amount": "{{ round($amount * 100) }}",
    "currency": "INR",
    "name": "{{ $name }}",
    "description": "{{ $description }}",
    "order_id": "{{ $payment->razorpay_order_id }}",
    "handler": function (response){
        // In a real app, you might ping the server or show a success message
        // But the actual state mutation relies strictly on the Webhook.
        document.body.innerHTML = `
            <div class="bg-gray-50 h-screen flex flex-col justify-center items-center p-4 text-center">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h1 class="text-3xl font-black text-gray-900 mb-2">Payment Successful!</h1>
                <p class="text-gray-500 mb-6">Thank you for your payment. You may close this window.</p>
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
        alert("Payment failed. " + response.error.description);
});
document.getElementById('rzp-button1').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}
// Optionally auto-open
// rzp1.open();
</script>

</body>
</html>

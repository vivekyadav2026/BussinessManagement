@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Subscription & Billing</h1>
        <p class="text-gray-500 mt-1">Manage your organization's active plan, limits, and billing cycles.</p>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200 text-sm font-semibold flex items-center gap-3">
        <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div>
            <p class="font-bold text-base">Account Access Locked</p>
            <p class="text-xs text-red-600 mt-0.5">{{ session('error') }}</p>
        </div>
    </div>
@endif

<div class="panel p-6 shadow-sm mb-8">
    <div class="flex justify-between items-start mb-2">
        <h2 class="text-lg font-bold text-gray-900">Current Plan: {{ $currentSubscription ? $currentSubscription->plan->name : 'No Active Plan' }}</h2>
        @if($currentSubscription)
            @if($currentSubscription->status === 'Trial')
                @php
                    $daysLeft = \App\Services\SubscriptionService::getDaysRemaining(auth()->user()->organization_id);
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    ⏳ Free Trial ({{ $daysLeft }} {{ \Illuminate\Support\Str::plural('day', $daysLeft) }} left)
                </span>
            @elseif(in_array($currentSubscription->status, ['Expired', 'Cancelled']))
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                    ⚠️ {{ $currentSubscription->status }}
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                    ✓ Active Subscription
                </span>
            @endif
        @endif
    </div>

@php
    $featureMap = [
        'module_retail' => 'Retail & Inventory ERP',
        'module_payroll' => 'HR & Payroll Module',
        'module_restaurant' => 'Restaurant POS & KOT',
        'digital_qr_menu' => 'Digital QR Menu',
        'kitchen_display' => 'Kitchen Display System (KDS)',
        'table_management' => 'Table & Order Management',
        'max_employees' => 'Max Employees Limit',
        'max_invoices_per_month' => 'Monthly Invoice Limit',
        'payment_gateway' => 'Razorpay Payment Gateway',
    ];
@endphp

@if($currentSubscription)
    <div class="text-sm text-gray-600 mb-4">
        Valid until: <span class="font-bold text-gray-800 font-mono">{{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('M d, Y') : 'Lifetime' }}</span>
    </div>

    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 mt-6 border-b border-gray-100 pb-2">Your Limits & Features Included</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($currentSubscription->plan->features as $feature)
            @php
                $label = $featureMap[$feature->feature_code] ?? ucwords(str_replace('_', ' ', $feature->feature_code));
                $val = strtolower(trim($feature->feature_value));
            @endphp
            <div class="bg-gray-50/70 border border-gray-100 p-3.5 rounded-xl flex justify-between items-center">
                <span class="text-sm font-bold text-gray-700">{{ $label }}</span>
                @if($val === 'true' || $val === 'yes')
                    <span class="text-xs font-bold bg-green-100 text-green-800 border border-green-200 px-3 py-0.5 rounded-full">✓ Enabled</span>
                @elseif($val === 'false' || $val === 'no')
                    <span class="text-xs font-bold bg-gray-100 text-gray-400 border border-gray-200 px-3 py-0.5 rounded-full">✗ Excluded</span>
                @else
                    <span class="text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-0.5 rounded-full font-mono">{{ $feature->feature_value }}</span>
                @endif
            </div>
        @endforeach
    </div>
@else
    <p class="text-red-500 text-sm">Your organization does not have an active subscription. Please select a plan below to unlock your account.</p>
@endif
</div>


<h2 class="text-xl font-bold text-gray-900 mb-4">Available Plans</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($plans as $plan)
        <div class="panel p-6 shadow-sm flex flex-col {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'ring-2 ring-indigo-600 border-indigo-600' : '' }}">
            @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2">Current Plan</div>
            @endif
            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
            <div class="mt-2 mb-4">
                <span class="text-3xl font-black text-gray-900 font-mono">₹{{ number_format($plan->price_monthly, 2) }}</span>
                <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">/mo</span>
            </div>
            <p class="text-sm text-gray-600 mb-6 flex-grow leading-relaxed">{{ $plan->description }}</p>

            <ul class="space-y-2.5 mb-6">
                @foreach($plan->features as $feature)
                    @php
                        $label = $featureMap[$feature->feature_code] ?? ucwords(str_replace('_', ' ', $feature->feature_code));
                        $val = strtolower(trim($feature->feature_value));
                    @endphp
                    <li class="flex items-center justify-between text-xs text-gray-600">
                        <span class="font-medium text-gray-700">{{ $label }}:</span>
                        @if($val === 'true' || $val === 'yes')
                            <span class="font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded text-[11px]">✓ Yes</span>
                        @elseif($val === 'false' || $val === 'no')
                            <span class="font-bold text-gray-400 bg-gray-50 px-2 py-0.5 rounded text-[11px]">✗ No</span>
                        @else
                            <strong class="text-indigo-700 font-mono bg-indigo-50 px-2.5 py-0.5 rounded text-[11px] font-bold">{{ $feature->feature_value }}</strong>
                        @endif
                    </li>
                @endforeach
            </ul>



            @if(!$currentSubscription || $currentSubscription->plan_id != $plan->id)
                <button type="button" onclick="subscribePlan('{{ $plan->id }}', '{{ $plan->name }}', {{ $plan->price_monthly }})" class="w-full btn btn-gold py-3 justify-center flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Switch to {{ $plan->name }}
                </button>
            @else
                <button disabled class="w-full btn btn-ghost py-2.5 justify-center cursor-not-allowed opacity-50">Active Plan</button>
            @endif
        </div>
    @endforeach
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function subscribePlan(planId, planName, price) {
    if (!confirm('Proceed to subscribe to ' + planName + ' plan?')) return;

    fetch('{{ url("organization/subscription/initiate") }}/' + planId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Error initializing payment.');
            return;
        }

        if (data.is_free) {
            alert(data.message);
            window.location.reload();
            return;
        }

        var options = {
            "key": data.key,
            "amount": data.amount,
            "currency": data.currency,
            "name": data.org_name,
            "description": "Subscription for " + data.plan_name + " Plan",
            "order_id": data.order_id,
            "prefill": {
                "name": data.user_name,
                "email": data.user_email
            },
            "theme": { "color": "#4f46e5" },
            "handler": function (response) {
                // Confirm payment server-side
                fetch('{{ route("organization.subscription.confirm") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_id: data.plan_id,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id
                    })
                })
                .then(res => res.json())
                .then(resData => {
                    alert(resData.message || 'Subscription updated!');
                    window.location.href = "{{ route('organization.dashboard') }}";
                });
            }
        };

        var rzp = new Razorpay(options);
        rzp.on('payment.failed', function(res){ alert("Payment failed: " + res.error.description); });
        rzp.open();
    })
    .catch(err => {
        alert('Failed to process request: ' + err.message);
    });
}
</script>
@endsection


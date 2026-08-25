@extends('layouts.public')

@section('content')
<section class="hero" style="padding-bottom: 20px;">
    <div class="wrap text-center" style="text-align: center; max-width: 700px; margin: 0 auto;">
        <div class="eyebrow">Pricing Plans</div>
        <h1 style="font-size: 42px; margin-bottom: 24px;">Simple pricing, <br><em>no hidden fees</em>.</h1>
        <p class="lead" style="margin: 0 auto;">Start for free and upgrade as your business grows. Cancel anytime.</p>
    </div>
</section>

<section>
    <div class="wrap plans">
        <div class="plan-card">
            <div class="plan-name">Free</div>
            <div class="plan-price">₹0<span> / forever</span></div>
            <ul class="plan-feats">
                <li>1 User</li>
                <li>Unlimited Products</li>
                <li>Up to 50 Invoices/mo</li>
                <li>Basic Reports</li>
            </ul>
            <a class="btn btn-ghost w-100" href="{{ route('register') }}" style="width:100%; justify-content:center;">Get Started</a>
        </div>
        <div class="plan-card feat">
            <div class="plan-tag">MOST POPULAR</div>
            <div class="plan-name">Pro</div>
            <div class="plan-price">₹499<span> / mo</span></div>
            <ul class="plan-feats">
                <li>5 Users</li>
                <li>Unlimited Invoices</li>
                <li>Employee Payroll</li>
                <li>Payment Gateway (Razorpay)</li>
            </ul>
            <a class="btn btn-gold w-100" href="{{ route('register') }}" style="width:100%; justify-content:center;">Start Free Trial</a>
        </div>
        <div class="plan-card">
            <div class="plan-name">Restaurant</div>
            <div class="plan-price">₹999<span> / mo</span></div>
            <ul class="plan-feats">
                <li>Everything in Pro</li>
                <li>Digital QR Menu</li>
                <li>Kitchen Display System</li>
                <li>Table Management</li>
            </ul>
            <a class="btn btn-ghost w-100" href="{{ route('register') }}" style="width:100%; justify-content:center;">Start Free Trial</a>
        </div>
        <div class="plan-card">
            <div class="plan-name">Enterprise</div>
            <div class="plan-price">Custom</div>
            <ul class="plan-feats">
                <li>Unlimited Users</li>
                <li>Multiple Locations</li>
                <li>Priority Support</li>
                <li>Custom Integrations</li>
            </ul>
            <a class="btn btn-ghost w-100" href="{{ route('register') }}" style="width:100%; justify-content:center;">Contact Us</a>
        </div>
    </div>
</section>
@endsection

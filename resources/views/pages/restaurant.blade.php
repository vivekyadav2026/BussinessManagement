@extends('layouts.public')

@section('content')
<section class="hero" style="padding-bottom: 20px;">
    <div class="wrap text-center" style="text-align: center; max-width: 700px; margin: 0 auto;">
        <div class="eyebrow">Restaurant POS</div>
        <h1 class="page-title">Built for modern <br><em>cafes & restaurants</em>.</h1>
        <p class="page-lead" style="max-width: 600px;">Manage tables, send orders directly to the kitchen, and let customers order via QR codes seamlessly.</p>
    </div>
</section>

<section style="padding-top: 10px;">
    <div class="wrap">
        <div class="rest-split">
            <div>
                <img src="{{ asset('images/restaurant_preview.jpg') }}" alt="Restaurant QR Menu Ordering" class="illust-img">
            </div>
            <div>
                <h2 class="section-title">Smart QR Ordering</h2>
                <ul class="rest-list">
                    <li>
                        <div class="rest-num">01</div>
                        <div>
                            <h4>Dynamic QR Codes</h4>
                            <p>Generate secure QR codes for every table. Customers scan and order instantly from their phones.</p>
                        </div>
                    </li>
                    <li>
                        <div class="rest-num">02</div>
                        <div>
                            <h4>Kitchen Display System</h4>
                            <p>Orders flow directly to the kitchen screen. No lost tickets, no delays.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Table POS & Waiter App -->
<section style="background: var(--paper); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); padding: 40px 0;">
    <div class="wrap">
        <div class="sec-head text-center" style="margin: 0 auto 36px; text-align: center; max-width: 600px;">
            <div class="eyebrow">Advanced POS</div>
            <h2 class="section-title">Table Tracking &amp; Waiter App</h2>
            <p>From visual dining layouts to fast billing, manage your restaurant tables and staff actions seamlessly.</p>
        </div>
        <div class="grid-2-cards">
            <div class="feat-card">
                <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7"/></svg></div>
                <h3>Visual Dining Layout</h3>
                <p>Track table occupancy, billing requests, and cooking status. Waiters place orders from tables directly on their phones.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.953 11.953 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                <h3>Instant GST Invoicing</h3>
                <p>Generate KOT tickets and bills instantly. Accept digital payments via integrated Razorpay UPI codes automatically.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Ingredient Stock Control -->
<section style="padding: 40px 0;">
    <div class="wrap">
        <div class="grid-2">
            <div>
                <h2 class="section-title">Menu Recipe &amp; Stock Auto-Deduction</h2>
                <p style="color:var(--ink-soft); font-size:15px; line-height:1.6; margin-bottom:24px;">Link menu dishes to specific raw ingredients. As orders flow to the kitchen, Vyapaargo automatically deducts inventory levels in real-time.</p>
                <div class="feat-card" style="margin-bottom: 16px;">
                    <h4 style="font-weight:600; font-family:'Space Grotesk';">Low stock warnings</h4>
                    <p style="font-size:13.5px; color:var(--ink-soft); margin-top:6px;">Receive mobile alerts when ingredients go below set limits, allowing you to reorder stock before it impacts service.</p>
                </div>
                <div class="feat-card">
                    <h4 style="font-weight:600; font-family:'Space Grotesk';">Kitchen Routing</h4>
                    <p style="font-size:13.5px; color:var(--ink-soft); margin-top:6px;">Print separate tickets automatically to the hot kitchen and the bar, keeping your kitchen workflow organized.</p>
                </div>
            </div>
            <div>
                <img src="{{ asset('images/payments_preview.jpg') }}" alt="Recipe Costing Inventory" class="illust-img">
            </div>
        </div>
    </div>
</section>
@endsection

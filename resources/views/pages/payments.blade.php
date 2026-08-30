@extends('layouts.public')

@section('content')
<section class="hero" style="padding-bottom: 20px;">
    <div class="wrap text-center" style="text-align: center; max-width: 700px; margin: 0 auto;">
        <div class="eyebrow">Razorpay Integration</div>
        <h1 class="page-title">Accept payments <br><em>instantly</em>.</h1>
        <p class="page-lead" style="max-width: 600px;">Send payment links with invoices and let clients pay via UPI, cards, and net banking with automatic reconciliation.</p>
    </div>
</section>

<section style="padding-top: 10px;">
    <div class="wrap payment-grid">
        <div class="flow-card" style="text-align:center; width: 100%;">
            <div class="flow-title" style="margin-bottom:20px;">Seamless Payment Flow</div>
            <div class="flow-row" style="justify-content:center; flex-direction: column; gap: 8px;">
                <div class="flow-step" style="width: 100%;">1. Generate Invoice</div>
                <div class="flow-arrow" style="transform: rotate(90deg); margin: 2px 0; display: inline-block;">➔</div>
                <div class="flow-step" style="width: 100%;">2. Send Payment Link</div>
                <div class="flow-arrow" style="transform: rotate(90deg); margin: 2px 0; display: inline-block;">➔</div>
                <div class="flow-step" style="width: 100%;">3. Client Pays via UPI</div>
                <div class="flow-arrow" style="transform: rotate(90deg); margin: 2px 0; display: inline-block;">➔</div>
                <div class="flow-step" style="width: 100%;">4. Auto-Reconciled</div>
            </div>
        </div>
        <div>
            <img src="{{ asset('images/payments_preview.jpg') }}" alt="UPI Payment Success" class="illust-img">
        </div>
    </div>
</section>

<!-- Section 2: Collection Channels & Smart Reminders -->
<section style="background: var(--paper); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); padding: 40px 0;">
    <div class="wrap">
        <div class="sec-head text-center" style="margin: 0 auto 36px; text-align: center; max-width: 600px;">
            <div class="eyebrow">Smart Collections</div>
            <h2 class="section-title">Automated Reminders &amp; UPI QR</h2>
            <p>Get paid up to 3x faster with automated reminders and multiple digital collection channels built right into your invoices.</p>
        </div>
        <div class="grid-2-cards">
            <div class="feat-card">
                <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></div>
                <h3>WhatsApp &amp; SMS Reminders</h3>
                <p>Send automated payment link alerts directly to customer WhatsApp and SMS on invoice due dates. Say goodbye to manual follow-ups.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-16v4m0 4h.01M4 12h2m10 0h2m-6 0v4m-8-8v12h16V8H4zm4 4h8m-8 4h8"/></svg></div>
                <h3>Unified Payment Gateway</h3>
                <p>Integrate your Razorpay account in seconds. Customers scan and pay directly via GPay, PhonePe, Paytm, credit cards, or net banking.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Reconciliation & Cashflow -->
<section style="padding: 40px 0;">
    <div class="wrap">
        <div class="grid-2">
            <div>
                <h2 class="section-title">Ledger Auto-Reconciliation &amp; Reports</h2>
                <p style="color:var(--ink-soft); font-size:15px; line-height:1.6; margin-bottom:24px;">When a client pays through the invoice link, Vyapaargo automatically marks the invoice as Paid, updates the customer ledger, and logs the cashflow entry in real-time.</p>
                <div class="feat-card" style="margin-bottom: 16px;">
                    <h4 style="font-weight:600; font-family:'Space Grotesk';">Receivables Aging Reports</h4>
                    <p style="font-size:13.5px; color:var(--ink-soft); margin-top:6px;">View outstanding aging groups (1-30 days, 30-60 days, 60+ days) instantly on your dashboard to protect your cashflow.</p>
                </div>
                <div class="feat-card">
                    <h4 style="font-weight:600; font-family:'Space Grotesk';">Direct Settlement Tracking</h4>
                    <p style="font-size:13.5px; color:var(--ink-soft); margin-top:6px;">Track settlement status and transition timelines directly to your registered bank account inside the dashboard.</p>
                </div>
            </div>
            <div>
                <img src="{{ asset('images/restaurant_preview.jpg') }}" alt="Reconciliation Ledger reports" class="illust-img">
            </div>
        </div>
    </div>
</section>
@endsection

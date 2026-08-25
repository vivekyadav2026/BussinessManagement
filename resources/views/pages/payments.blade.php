@extends('layouts.public')

@section('content')
<section class="hero" style="padding-bottom: 20px;">
    <div class="wrap text-center" style="text-align: center; max-width: 700px; margin: 0 auto;">
        <div class="eyebrow">Razorpay Integration</div>
        <h1 style="font-size: 42px; margin-bottom: 24px;">Accept payments <br><em>instantly</em>.</h1>
        <p class="lead" style="margin: 0 auto;">Send payment links with invoices and let clients pay via UPI, cards, and net banking with automatic reconciliation.</p>
    </div>
</section>

<section>
    <div class="wrap">
        <div class="flow-card" style="max-width: 800px; margin: 0 auto; text-align:center;">
            <div class="flow-title" style="margin-bottom:32px;">Seamless Payment Flow</div>
            <div class="flow-row" style="justify-content:center;">
                <div class="flow-step">1. Generate Invoice</div>
                <div class="flow-arrow">➔</div>
                <div class="flow-step">2. Send Payment Link</div>
                <div class="flow-arrow">➔</div>
                <div class="flow-step">3. Client Pays via UPI</div>
                <div class="flow-arrow">➔</div>
                <div class="flow-step">4. Auto-Reconciled</div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.public')

@section('content')
<section class="hero" style="padding-bottom: 20px;">
    <div class="wrap text-center" style="text-align: center; max-width: 700px; margin: 0 auto;">
        <div class="eyebrow">Pricing Plans</div>
        <h1 class="page-title">Simple pricing, <br><em>no hidden fees</em>.</h1>
        <p class="page-lead" style="max-width: 600px;">Start for free and upgrade as your business grows. Cancel anytime.</p>
    </div>
</section>

<section style="padding-top: 10px;">
    <div class="wrap plans-container">
        <div class="plans" id="plans-slider">
            @foreach($plans as $plan)
                @php
                    $isFeat = ($plan->name === 'Pro Plan');
                    $isEnterprise = ($plan->name === 'Enterprise');
                    $displayFeatures = $plan->features->filter(function($f) {
                        return !in_array($f->feature_code, ['max_employees', 'module_payroll', 'module_restaurant']);
                    });
                @endphp
                <div class="plan-card {{ $isFeat ? 'feat' : '' }}">
                    @if($isFeat)
                        <div class="plan-tag">MOST POPULAR</div>
                    @endif
                    <div class="plan-name">{{ $plan->name }}</div>
                    <div class="plan-price">
                        @if($isEnterprise)
                            Custom
                        @else
                            ₹{{ number_format($plan->price_monthly, 0) }}<span> / {{ $plan->name === 'Free' ? 'forever' : 'mo' }}</span>
                        @endif
                    </div>
                    <ul class="plan-feats">
                        @foreach($displayFeatures as $feature)
                            <li>{{ $feature->feature_code }}</li>
                        @endforeach
                    </ul>
                    @if($isEnterprise)
                        <a class="btn btn-ghost w-100" href="{{ route('register') }}" style="width:100%; justify-content:center;">Contact Us</a>
                    @else
                        <a class="btn {{ $isFeat ? 'btn-gold' : 'btn-ghost' }} w-100" href="{{ route('register') }}" style="width:100%; justify-content:center;">
                            {{ $plan->name === 'Free' ? 'Get Started' : 'Start Free Trial' }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="slider-dots" id="plans-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>
</section>
@endsection

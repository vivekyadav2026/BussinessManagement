@extends('layouts.public')

@section('content')
<style>
  .toggle-switch {
    position: relative; display: inline-block; width: 50px; height: 28px;
  }
  .toggle-switch input { opacity: 0; width: 0; height: 0; }
  .slider {
    position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
    background-color: var(--border-soft); transition: .4s; border-radius: 34px;
    border: 1px solid var(--border);
  }
  .slider .knob {
    position: absolute; height: 20px; width: 20px; left: 3px; bottom: 3px;
    background-color: white; transition: .4s; border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  .toggle-switch input:checked + .slider { background-color: var(--gold); border-color: var(--gold-deep); }
  .toggle-switch input:checked + .slider .knob { transform: translateX(22px); }
  
  .cat-tabs { display: flex; justify-content: center; gap: 10px; margin-bottom: 30px; }
  .cat-tabs button {
    padding: 10px 20px; font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600;
    border-radius: 30px; border: 1px solid var(--border); background: #fff; cursor: pointer;
    transition: 0.2s; color: var(--ink-soft);
  }
  .cat-tabs button.active { background: var(--ink); color: #fff; border-color: var(--ink); }
  
  .plans { padding-top: 20px; }
  .plan-card { display: flex; flex-direction: column; }
  .price-yearly { display: none; }
  .yearly-active .price-monthly { display: none; }
  .yearly-active .price-yearly { display: block; }
</style>

<section class="hero" style="padding-bottom: 20px;">
    <div class="wrap text-center" style="text-align: center; max-width: 700px; margin: 0 auto;">
        <div class="eyebrow">Pricing Plans</div>
        <h1 class="page-title">Simple pricing, <br><em>no hidden fees</em>.</h1>
        <p class="page-lead" style="max-width: 600px;">Flexible plans tailored for your business needs. Upgrade anytime.</p>
    </div>
</section>

<section style="padding-top: 10px; padding-bottom: 80px;">
    <div class="wrap">
        
        <!-- Category Tabs -->
        <div class="cat-tabs">
            <button type="button" id="tab-business" class="{{ $type !== 'restaurant' ? 'active' : '' }}" onclick="switchCategory('business')">🏢 Business & Retail</button>
            <button type="button" id="tab-restaurant" class="{{ $type === 'restaurant' ? 'active' : '' }}" onclick="switchCategory('restaurant')">🍽️ Restaurant POS</button>
        </div>

        <!-- Billing Toggle -->
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 40px; gap: 12px;">
            <span id="label-monthly" style="font-weight: 600; color: var(--ink);">Monthly</span>
            <label class="toggle-switch">
                <input type="checkbox" id="billing-toggle" onchange="toggleBilling()">
                <span class="slider"><span class="knob"></span></span>
            </label>
            <span id="label-yearly" style="font-weight: 600; color: var(--ink-faint);">Yearly <span style="color: var(--gold); font-size: 12px; margin-left: 5px;">(Save up to 20%)</span></span>
        </div>

        @php
            $basePlans = $plans->where('type', 'base');
            $addonPlans = $plans->where('type', 'addon');

            $businessPlans = $basePlans->filter(function($p) {
                return $p->category === 'business' || $p->category === 'all';
            });

            $restaurantPlans = $basePlans->filter(function($p) {
                return $p->category === 'restaurant' || $p->category === 'all';
            });
        @endphp

        <!-- Business Plans Container -->
        <div class="plans-container" id="plans-business" style="display: {{ $type !== 'restaurant' ? 'block' : 'none' }};">
            <div class="plans" id="plans-business-slider">
                @foreach($businessPlans as $plan)
                    @include('pages.partials.plan_card', ['plan' => $plan])
                @endforeach
            </div>
            <div class="slider-dots" id="plans-business-dots"></div>
        </div>

        <!-- Restaurant Plans Container -->
        <div class="plans-container" id="plans-restaurant" style="display: {{ $type === 'restaurant' ? 'block' : 'none' }};">
            <div class="plans" id="plans-restaurant-slider">
                @foreach($restaurantPlans as $plan)
                    @include('pages.partials.plan_card', ['plan' => $plan])
                @endforeach
            </div>
            <div class="slider-dots" id="plans-restaurant-dots"></div>
        </div>

    </div>
</section>

@if($addonPlans->count() > 0)
<section style="padding-top: 40px; padding-bottom: 80px; background: var(--bg);">
    <div class="wrap">
        <div class="sec-head" style="margin: 0 auto 30px; text-align: center; max-width: 600px;">
            <div class="eyebrow">Power-ups</div>
            <h2>Add-on Modules</h2>
            <p>Customize your existing plan by adding specific features.</p>
        </div>
        <div class="plans-container" style="display: block;">
            <div class="plans addon-plans" id="plans-addon-slider">
                @foreach($addonPlans as $plan)
                    @include('pages.partials.plan_card', ['plan' => $plan, 'isAddon' => true])
                @endforeach
            </div>
            <div class="slider-dots" id="plans-addon-dots"></div>
        </div>
    </div>
</section>
@endif

<script>
    function switchCategory(cat) {
        // Tabs
        document.getElementById('tab-business').classList.remove('active');
        document.getElementById('tab-restaurant').classList.remove('active');
        document.getElementById('tab-' + cat).classList.add('active');

        // Containers
        document.getElementById('plans-business').style.display = (cat === 'business') ? 'block' : 'none';
        document.getElementById('plans-restaurant').style.display = (cat === 'restaurant') ? 'block' : 'none';
        
        // Update URL quietly
        const url = new URL(window.location);
        url.searchParams.set('type', cat);
        window.history.pushState({}, '', url);

        // Re-sync slider dots on category change
        if (typeof window.reinitSliders === 'function') {
            window.reinitSliders();
        }
    }

    function toggleBilling() {
        const isYearly = document.getElementById('billing-toggle').checked;
        
        if (isYearly) {
            document.body.classList.add('yearly-active');
            document.getElementById('label-yearly').style.color = 'var(--ink)';
            document.getElementById('label-monthly').style.color = 'var(--ink-faint)';
        } else {
            document.body.classList.remove('yearly-active');
            document.getElementById('label-monthly').style.color = 'var(--ink)';
            document.getElementById('label-yearly').style.color = 'var(--ink-faint)';
        }
    }
</script>
@endsection

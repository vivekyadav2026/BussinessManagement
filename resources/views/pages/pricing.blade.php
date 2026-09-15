@extends('layouts.public')

@section('content')
<style>
  .toggle-switch {
    position: relative; display: inline-block; width: 56px; height: 30px;
  }
  .toggle-switch input { opacity: 0; width: 0; height: 0; }
  .slider {
    position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
    background-color: var(--border-soft); transition: .3s; border-radius: 34px;
    border: 1px solid var(--border);
  }
  .slider .knob {
    position: absolute; height: 22px; width: 22px; left: 3px; bottom: 3px;
    background-color: white; transition: .3s; border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
  }
  .toggle-switch input:checked + .slider { background-color: var(--gold); border-color: var(--gold-deep); }
  .toggle-switch input:checked + .slider .knob { transform: translateX(26px); }
  
  .cat-tabs { display: flex; justify-content: center; gap: 12px; margin-bottom: 32px; flex-wrap: wrap; }
  .cat-tabs button {
    padding: 12px 24px; font-family: 'Space Grotesk', sans-serif; font-size: 15px; font-weight: 700;
    border-radius: 100px; border: 1.5px solid var(--border); background: #fff; cursor: pointer;
    transition: 0.2s; color: var(--ink-soft); display: inline-flex; align-items: center; gap: 8px;
    box-shadow: 0 2px 4px rgba(23,35,63,0.03);
  }
  .cat-tabs button:hover { border-color: var(--ink); color: var(--ink); }
  .cat-tabs button.active { background: var(--ink); color: #fff; border-color: var(--ink); box-shadow: 0 4px 14px rgba(23,35,63,0.18); }
  
  .plans { padding-top: 10px; }
  .plan-card { display: flex; flex-direction: column; }
  .price-yearly { display: none; }
  .yearly-active .price-monthly { display: none; }
  .yearly-active .price-yearly { display: block; }

  /* Comparison Table */
  .comparison-table-wrap {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 20px;
    overflow-x: auto;
    box-shadow: 0 10px 30px -10px rgba(23,35,63,0.06);
    margin-top: 40px;
  }
  .comp-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 720px;
    text-align: left;
  }
  .comp-table th, .comp-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-soft);
    font-size: 13.5px;
  }
  .comp-table th {
    background: #FAFBF9;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    color: var(--ink);
    font-size: 14px;
  }
  .comp-table tr:last-child td {
    border-bottom: none;
  }
  .comp-table tr:hover td {
    background: rgba(243,245,243,0.4);
  }
  .comp-check { color: var(--teal); font-weight: 800; font-size: 15px; }
  .comp-dash { color: var(--ink-faint); font-weight: 600; }
</style>

<!-- Pricing Hero Banner -->
<section class="hero" style="padding-bottom: 24px;">
    <div class="wrap" style="text-align: center; max-width: 800px; margin: 0 auto;">
        <div class="eyebrow" style="background: #FDF4E5; color: var(--gold-deep); border-color: #F5DEB3;">
          TRANSPARENT &amp; FAIR PRICING
        </div>
        <h1 class="page-title" style="margin-bottom: 14px;">
          Simple plans that <br><em>scale with your business</em>.
        </h1>
        @php
            $maxDiscount = 0;
            foreach($plans as $p) {
                if ($p->price_monthly > 0 && $p->price_yearly > 0) {
                    $discount = (($p->price_monthly * 12) - $p->price_yearly) / ($p->price_monthly * 12) * 100;
                    if ($discount > $maxDiscount) {
                        $maxDiscount = $discount;
                    }
                }
            }
            $maxDiscount = round($maxDiscount);
        @endphp

        <p class="page-lead" style="max-width: 680px; margin: 0 auto 24px; font-size: 16.5px; line-height: 1.6; color: var(--ink-soft);">
          @if($enableTrial && $trialDays > 0)
            Start with our <strong>{{ $trialDays }}-Day full access free trial</strong>. No credit card required. 
          @else
            No credit card required.
          @endif
          @if($maxDiscount > 0)
            Choose monthly flexibility or <strong>save up to {{ $maxDiscount }}%</strong> on annual billing.
          @else
            Choose monthly or annual billing.
          @endif
        </p>

        <div style="display: flex; justify-content: center; gap: 24px; flex-wrap: wrap; margin-top: 20px; margin-bottom: 10px;">
          @if($enableTrial && $trialDays > 0)
          <span style="font-size: 13.5px; font-family: 'IBM Plex Mono'; color: var(--teal); font-weight: 600;">✓ {{ $trialDays }}-Day Free Trial</span>
          @endif
          <span style="font-size: 13.5px; font-family: 'IBM Plex Mono'; color: var(--teal); font-weight: 600;">✓ Instant Setup (&lt;5 Mins)</span>
          <span style="font-size: 13.5px; font-family: 'IBM Plex Mono'; color: var(--teal); font-weight: 600;">✓ 0% Platform Commission</span>
          <span style="font-size: 13.5px; font-family: 'IBM Plex Mono'; color: var(--teal); font-weight: 600;">✓ Cancel Anytime</span>
        </div>
    </div>
</section>

<!-- Pricing Plans & Billing Selector -->
<section style="padding-top: 10px; padding-bottom: 70px;">
    <div class="wrap">
        
        <!-- Category Selector Tabs -->
        <div class="cat-tabs">
            <button type="button" id="tab-business" class="{{ $type !== 'restaurant' ? 'active' : '' }}" onclick="switchCategory('business')">
              🏢 Business &amp; Retail ERP
            </button>
            <button type="button" id="tab-restaurant" class="{{ $type === 'restaurant' ? 'active' : '' }}" onclick="switchCategory('restaurant')">
              🍽️ Restaurant &amp; Cafe POS
            </button>
        </div>

        <!-- Billing Frequency Toggle -->
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 36px; gap: 14px;">
            <span id="label-monthly" style="font-weight: 700; font-size: 14.5px; color: var(--ink);">Monthly Billing</span>
            <label class="toggle-switch">
                <input type="checkbox" id="billing-toggle" onchange="toggleBilling()">
                <span class="slider"><span class="knob"></span></span>
            </label>
            <span id="label-yearly" style="font-weight: 700; font-size: 14.5px; color: var(--ink-faint); display: inline-flex; align-items: center; gap: 6px;">
                Yearly Billing 
                @if($maxDiscount > 0)
                <span style="background: #FDF4E5; border: 1px solid #F5DEB3; color: var(--gold-deep); font-family: 'IBM Plex Mono'; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 100px;">
                  SAVE {{ $maxDiscount }}%
                </span>
                @endif
            </span>
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

<!-- Add-on Modules (if configured) -->
@if($addonPlans->count() > 0)
<section style="padding-top: 30px; padding-bottom: 70px; background: var(--bg); border-top: 1px solid var(--border-soft);">
    <div class="wrap">
        <div class="sec-head" style="margin: 0 auto 30px; text-align: center; max-width: 600px;">
            <div class="eyebrow" style="background: #E6F4F1; color: var(--teal); border-color: #BEE3D8;">MODULAR EXPANSION</div>
            <h2>Add-On Modules &amp; Quotas</h2>
            <p>Need extra capacity without upgrading your entire tier? Stack modular power-ups on any active base plan.</p>
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

<!-- Plan Comparison Matrix -->
<section style="padding: 60px 0 70px; background: var(--paper); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft);">
  <div class="wrap">
    <div class="sec-head" style="margin: 0 auto 32px; text-align: center; max-width: 680px;">
      <div class="eyebrow">FEATURE BREAKDOWN</div>
      <h2 style="font-size: 30px; font-weight: 700; color: var(--ink);">Detailed Plan Comparison</h2>
      <p style="color: var(--ink-soft); font-size: 14.5px;">See exactly what is included in each tier to find the perfect fit for your operations.</p>
    </div>

    @php
        // Helper to format feature codes
        $formatFeatureName = function($code) {
            return ucwords(str_replace('_', ' ', $code));
        };

        // Extract unique feature codes for each category
        $businessFeatures = collect();
        foreach($businessPlans as $plan) {
            foreach($plan->features as $f) {
                if (!$businessFeatures->contains($f->feature_code)) {
                    $businessFeatures->push($f->feature_code);
                }
            }
        }
        
        $restaurantFeatures = collect();
        foreach($restaurantPlans as $plan) {
            foreach($plan->features as $f) {
                if (!$restaurantFeatures->contains($f->feature_code)) {
                    $restaurantFeatures->push($f->feature_code);
                }
            }
        }
    @endphp

    <div class="comparison-table-wrap" id="comparison-business" style="display: {{ $type !== 'restaurant' ? 'block' : 'none' }};">
      <table class="comp-table">
        <thead>
          <tr>
            <th style="width: 32%;">Core Capability</th>
            @foreach($businessPlans as $plan)
            <th style="width: {{ 68 / max(1, $businessPlans->count()) }}%; text-align: center;">{{ $plan->name }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($businessFeatures as $code)
          <tr>
            <td><strong>{{ $formatFeatureName($code) }}</strong></td>
            @foreach($businessPlans as $plan)
                @php
                    $feature = $plan->features->firstWhere('feature_code', $code);
                    $val = $feature ? $feature->feature_value : null;
                @endphp
                <td style="text-align: center;">
                    @if(in_array(strtolower((string)$val), ['yes', 'true', '1']))
                        <span class="comp-check">✓</span>
                    @elseif(in_array(strtolower((string)$val), ['no', 'false', '0', null, '']))
                        <span class="comp-dash">—</span>
                    @else
                        <span class="comp-check">✓</span> {{ $val }}
                    @endif
                </td>
            @endforeach
          </tr>
          @endforeach
          @if($businessFeatures->isEmpty())
          <tr>
            <td colspan="{{ $businessPlans->count() + 1 }}" style="text-align: center; color: var(--ink-faint);">No features defined for these plans.</td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>

    <div class="comparison-table-wrap" id="comparison-restaurant" style="display: {{ $type === 'restaurant' ? 'block' : 'none' }};">
      <table class="comp-table">
        <thead>
          <tr>
            <th style="width: 32%;">Core Capability</th>
            @foreach($restaurantPlans as $plan)
            <th style="width: {{ 68 / max(1, $restaurantPlans->count()) }}%; text-align: center;">{{ $plan->name }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($restaurantFeatures as $code)
          <tr>
            <td><strong>{{ $formatFeatureName($code) }}</strong></td>
            @foreach($restaurantPlans as $plan)
                @php
                    $feature = $plan->features->firstWhere('feature_code', $code);
                    $val = $feature ? $feature->feature_value : null;
                @endphp
                <td style="text-align: center;">
                    @if(in_array(strtolower((string)$val), ['yes', 'true', '1']))
                        <span class="comp-check">✓</span>
                    @elseif(in_array(strtolower((string)$val), ['no', 'false', '0', null, '']))
                        <span class="comp-dash">—</span>
                    @else
                        <span class="comp-check">✓</span> {{ $val }}
                    @endif
                </td>
            @endforeach
          </tr>
          @endforeach
          @if($restaurantFeatures->isEmpty())
          <tr>
            <td colspan="{{ $restaurantPlans->count() + 1 }}" style="text-align: center; color: var(--ink-faint);">No features defined for these plans.</td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- Dynamic FAQ Section -->
<section id="faq" style="background: var(--bg); padding: 70px 0 80px;">
  <div class="wrap" style="max-width: 840px; margin: 0 auto;">
    <div class="sec-head" style="margin-bottom: 36px; text-align: center; margin-left: auto; margin-right: auto;">
      <div class="eyebrow" style="background: #FDF4E5; color: var(--gold-deep); border-color: #F5DEB3;">
        HAVE QUESTIONS?
      </div>
      <h2 style="font-size: 32px; font-weight: 700; color: var(--ink); margin-bottom: 8px;">Frequently Asked Questions</h2>
      <p style="color: var(--ink-soft); font-size: 15px;">Clear answers about billing, trials, hardware compatibility, and upgrades.</p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 14px;">
      @forelse($siteFaqs as $faq)
        <details class="feat-card" style="padding: 20px 24px; cursor: pointer; border-radius: 14px; background: #ffffff;">
          <summary style="font-weight: 700; font-size: 15.5px; font-family: 'Space Grotesk', sans-serif; list-style: none; display: flex; justify-content: space-between; align-items: center; color: var(--ink);">
            <span>{{ $faq['question'] }}</span>
            <span style="color: var(--gold); font-weight: 800; font-size: 20px; line-height: 1;">+</span>
          </summary>
          <p style="font-size: 14px; color: var(--ink-soft); margin-top: 14px; line-height: 1.65; cursor: default;">
            {{ $faq['answer'] }}
          </p>
        </details>
      @empty
        <div class="feat-card" style="padding: 24px; text-align: center; background: #fff;">
          <p style="color: var(--ink-soft);">No FAQs available currently.</p>
        </div>
      @endforelse
    </div>

    <!-- Extra help prompt -->
    <div style="text-align: center; margin-top: 36px; font-size: 14px; color: var(--ink-soft);">
      Still have questions? Chat with our support team at 
      <a href="mailto:{{ $siteSettings['support_email'] ?? 'support@vyapaargo.com' }}" style="color: var(--teal); font-weight: 700; text-decoration: underline;">
        {{ $siteSettings['support_email'] ?? 'support@vyapaargo.com' }}
      </a>
      or call 
      <a href="tel:{{ $siteSettings['support_phone'] ?? '+919876543210' }}" style="color: var(--ink); font-weight: 700;">
        {{ $siteSettings['support_phone'] ?? '+91 98765 43210' }}
      </a>.
    </div>
  </div>
</section>

<!-- Bottom CTA Banner -->
<section style="padding: 0 0 80px; background: var(--bg);">
  <div class="wrap">
    <div class="cta-banner" style="padding: 44px 48px; border-radius: 24px;">
      <div>
        <div class="eyebrow" style="background: rgba(217,154,43,0.2); color: var(--gold); border-color: rgba(217,154,43,0.3); margin-bottom: 12px;">
          START RISK-FREE
        </div>
        <h2 style="font-size: 32px; font-weight: 700; color: #ffffff; line-height: 1.2; margin-bottom: 8px;">
          @if($enableTrial && $trialDays > 0)
            Start your {{ $trialDays }}-day free trial now.
          @else
            Create your account today.
          @endif
        </h2>
        <p style="color: #94A3B8; font-size: 15px; max-width: 520px;">
          @if($enableTrial && $trialDays > 0)
            Experience all premium ERP, POS, and digital billing capabilities without paying a single rupee today.
          @else
            Get access to all premium ERP, POS, and digital billing capabilities instantly.
          @endif
        </p>
      </div>
      <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;">
        <a class="btn btn-gold" style="font-size: 15.5px; padding: 14px 28px; font-weight: 700; border-radius: 10px;" href="{{ route('register') }}">
          Create Free Account &rarr;
        </a>
      </div>
    </div>
  </div>
</section>

<script>
    function switchCategory(cat) {
        // Tabs
        document.getElementById('tab-business').classList.remove('active');
        document.getElementById('tab-restaurant').classList.remove('active');
        document.getElementById('tab-' + cat).classList.add('active');

        // Containers
        document.getElementById('plans-business').style.display = (cat === 'business') ? 'block' : 'none';
        document.getElementById('plans-restaurant').style.display = (cat === 'restaurant') ? 'block' : 'none';

        // Comparison Tables
        if(document.getElementById('comparison-business')) {
            document.getElementById('comparison-business').style.display = (cat === 'business') ? 'block' : 'none';
        }
        if(document.getElementById('comparison-restaurant')) {
            document.getElementById('comparison-restaurant').style.display = (cat === 'restaurant') ? 'block' : 'none';
        }
        
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

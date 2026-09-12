@php
    $isFeat = ($plan->name === 'Pro Plan' || $plan->name === 'Restaurant POS');
    $isEnterprise = ($plan->name === 'Enterprise');
    $isAddon = $isAddon ?? ($plan->type === 'addon');
    $displayFeatures = $plan->features;
    
    $featureMap = [
        'module_retail' => ['name' => 'Retail & Inventory ERP', 'icon' => '🛒'],
        'module_payroll' => ['name' => 'HR & Payroll Module', 'icon' => '👥'],
        'module_restaurant' => ['name' => 'Restaurant POS & KOT', 'icon' => '🍽️'],
        'digital_qr_menu' => ['name' => 'Digital QR Menu', 'icon' => '📱'],
        'kitchen_display' => ['name' => 'Kitchen Display (KDS)', 'icon' => '🍳'],
        'table_management' => ['name' => 'Table & Order Management', 'icon' => '🪑'],
        'advanced_analytics' => ['name' => 'Advanced Analytics & Reports', 'icon' => '📈'],
        'max_clients' => ['name' => 'Clients Quota', 'icon' => '🤝'],
        'max_locations' => ['name' => 'Multi-Branch Locations', 'icon' => '📍'],
        'max_employees' => ['name' => 'Staff / Employees', 'icon' => '👔'],
        'max_invoices_per_month' => ['name' => 'Monthly Invoices', 'icon' => '📄'],
        'max_products' => ['name' => 'Catalog Products', 'icon' => '📦'],
        'max_tables' => ['name' => 'Dining Tables Quota', 'icon' => '🪑'],
        'payment_gateway' => ['name' => 'Razorpay Gateway', 'icon' => '💳'],
        'barcode_scanning' => ['name' => 'Barcode Scanner', 'icon' => '🔍'],
    ];
@endphp
<div class="plan-card {{ ($isFeat && !$isAddon) ? 'feat' : '' }} {{ $isAddon ? 'addon-card' : '' }}">
    @if($isAddon)
        <div class="plan-tag" style="background: var(--ink); color: #fff; border: 1px solid rgba(255,255,255,0.2);">⚡ ADD-ON</div>
    @elseif($isFeat)
        <div class="plan-tag">⭐ MOST POPULAR</div>
    @endif

    <div>
        <div class="plan-name" style="font-size: 17px; font-weight: 700; color: var(--ink); margin-bottom: 6px; line-height: 1.3;">{{ $plan->name }}</div>
        
        <p style="font-size: 12px; color: var(--ink-soft); margin-bottom: 14px; min-height: 34px; line-height: 1.45;">
            {{ $plan->description ?: 'Everything you need to power up your business.' }}
        </p>

        <div class="plan-price-box" style="margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid var(--border-soft);">
            @if($isEnterprise)
                <div class="price-amount" style="font-family:'IBM Plex Mono', monospace; font-size:26px; font-weight:700; color:var(--ink);">Custom</div>
                <div style="font-size: 11.5px; color: var(--ink-faint); margin-top: 2px;">Tailored for large operations</div>
            @else
                <div class="price-monthly">
                    <span class="price-amount" style="font-family:'IBM Plex Mono', monospace; font-size:26px; font-weight:700; color:var(--ink);">&#8377;{{ number_format($plan->price_monthly, 0) }}</span>
                    <span class="price-period" style="font-size:12px; color:var(--ink-faint); font-weight:500;"> / {{ $plan->name === 'Free' ? 'forever' : 'month' }}</span>
                </div>
                <div class="price-yearly">
                    <span class="price-amount" style="font-family:'IBM Plex Mono', monospace; font-size:26px; font-weight:700; color:var(--ink);">&#8377;{{ number_format($plan->price_yearly, 0) }}</span>
                    <span class="price-period" style="font-size:12px; color:var(--ink-faint); font-weight:500;"> / year</span>
                </div>
            @endif
        </div>
        
        <ul class="plan-feats">
            @foreach($displayFeatures as $feature)
                @php
                    $meta = $featureMap[$feature->feature_code] ?? null;
                    $label = $meta['name'] ?? ucwords(str_replace('_', ' ', $feature->feature_code));
                    $val = strtolower(trim($feature->feature_value));
                    
                    if ($val === 'false' || $val === 'no' || $val === '0') {
                        continue;
                    }
                @endphp
                <li>
                    <span style="display: flex; align-items: center; gap: 7px;">
                        <span style="color: var(--teal); font-weight: bold; font-size: 13px;">✓</span>
                        <span style="font-weight: 500; font-size: 12.5px;">{{ $label }}</span>
                    </span>
                    @if($val !== 'true' && $val !== 'yes' && $val !== '1')
                        <span style="font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 700; background: var(--bg); padding: 2px 7px; border-radius: 6px; border: 1px solid var(--border); color: var(--ink);">
                            {{ $feature->feature_value }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    
    @php
        $btnText = $isAddon ? 'Add to Plan &rarr;' : ($plan->name === 'Free' ? 'Start Free Trial' : 'Get Started &rarr;');
        $targetUrl = route('register') . '?plan=' . $plan->id . ($plan->category ? '&type=' . $plan->category : '');
    @endphp

    <div style="margin-top: auto; padding-top: 8px;">
        @if($isEnterprise)
            <a class="btn btn-ghost w-100" href="{{ route('register') }}" style="width:100%; justify-content:center; text-align:center;">Contact Us</a>
        @else
            <a class="btn {{ ($isFeat && !$isAddon) ? 'btn-gold' : 'btn-ghost' }} w-100" href="{{ $targetUrl }}" style="width:100%; justify-content:center; text-align:center;">
                {!! $btnText !!}
            </a>
        @endif
    </div>
</div>

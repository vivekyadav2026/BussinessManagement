@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Subscription & Billing</h1>
        <p class="text-gray-500 mt-1">Manage your organization's active plan, limits, and add-on power-ups.</p>
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
        <h2 class="text-lg font-bold text-gray-900">
            Current Plan: {{ $currentSubscription ? $currentSubscription->plan->name . ' (' . ucfirst($currentSubscription->billing_cycle) . ')' : 'No Active Plan' }}
        </h2>
        @if($currentSubscription)
            <div class="flex items-center gap-3">
                @if($currentSubscription->status === 'Trial')
                    @php
                        $daysLeft = \App\Services\SubscriptionService::getDaysRemaining(auth()->user()->organization_id);
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        ⏳ Free Trial ({{ $daysLeft }} {{ \Illuminate\Support\Str::plural('day', $daysLeft) }} left)
                    </span>
                @elseif(in_array($currentSubscription->status, ['Expired', 'Cancelled', 'Refunded']))
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                        ⚠️ {{ $currentSubscription->status }}
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                        ✓ Active Subscription ({{ ucfirst($currentSubscription->billing_cycle) }})
                    </span>
                @endif
                
                @if($currentSubscription->status === 'Active' && $currentSubscription->gateway_payment_id)
                    <button type="button" onclick="document.getElementById('refundModal').classList.remove('hidden')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition shadow-sm">
                        Request Refund
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Refund Modal -->
    <div id="refundModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex justify-center items-center">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden shadow-2xl p-6 relative">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Request Subscription Refund</h3>
            <div class="p-4 bg-red-50 rounded-lg border border-red-100 text-red-800 mb-4 text-sm font-medium">
                ⚠️ Warning: Processing a refund will immediately cancel your subscription and <strong>deactivate your organization account</strong>. You and your team will not be able to log in anymore.
            </div>
            <p class="text-gray-600 text-sm mb-6">Are you sure you want to proceed with the refund?</p>
            <form action="{{ route('organization.subscription.refund') }}" method="POST" class="flex gap-3 justify-end">
                @csrf
                <button type="button" onclick="document.getElementById('refundModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition shadow-md">Confirm Refund & Deactivate</button>
            </form>
        </div>
    </div>

@php
    $featureMap = [
        'module_retail' => 'Retail & Inventory ERP',
        'module_payroll' => 'HR & Payroll Module',
        'module_restaurant' => 'Restaurant POS & KOT',
        'digital_qr_menu' => 'Digital QR Menu',
        'kitchen_display' => 'Kitchen Display System (KDS)',
        'table_management' => 'Table & Order Management',
        'advanced_analytics' => 'Advanced Analytics & Reports',
        'max_clients' => 'Max Clients Quota',
        'max_locations' => 'Multi-Location Branch Limit',
        'max_employees' => 'Max Employees Limit',
        'max_invoices_per_month' => 'Monthly Invoice Limit',
        'max_products' => 'Max Products Catalog Limit',
        'max_tables' => 'Max Restaurant Tables Quota',
        'payment_gateway' => 'Razorpay Payment Gateway',
    ];
@endphp

@if($currentSubscription)
    <div class="text-sm text-gray-600 mb-4">
        Valid until: <span class="font-bold text-gray-800 font-mono">{{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('M d, Y') : 'Lifetime' }}</span>
    </div>

    <!-- Active Add-ons Strip (if any) -->
    @php
        $activeAddons = auth()->user()->organization->activeAddons ?? collect();
    @endphp
    @if($activeAddons->count() > 0)
        <div class="mb-5 p-4 bg-indigo-50/60 rounded-xl border border-indigo-100">
            <div class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2">⚡ Active Power-Up Add-Ons Attached:</div>
            <div class="flex flex-wrap gap-2">
                @foreach($activeAddons as $addon)
                    <span class="inline-flex items-center gap-1.5 bg-white border border-indigo-200 px-3 py-1 rounded-lg text-xs font-bold text-indigo-700 shadow-sm">
                        <span class="text-amber-500">⚡</span> {{ $addon->plan->name }} ({{ ucfirst($addon->billing_cycle) }})
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 mt-4 border-b border-gray-100 pb-2">Your Limits &amp; Features Included</h3>
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


@php
    $orgType = auth()->user()->organization->business_type ?? 'business';
    
    $basePlans = $plans->where('type', 'base')->filter(function($p) use ($orgType) {
        if ($orgType === 'restaurant') {
            return $p->category === 'restaurant' || $p->category === 'all';
        }
        return $p->category === 'business' || $p->category === 'all';
    });
    
    $addonPlans = $plans->where('type', 'addon');
    $activeCycle = $currentSubscription ? $currentSubscription->billing_cycle : null;
@endphp

<h2 class="text-xl font-bold text-gray-900 mb-4">Available Plans</h2>

<div class="flex justify-center items-center mb-8 gap-4">
    <span id="lbl-monthly" class="font-bold text-gray-900 text-sm">Monthly</span>
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" id="billing-cycle-toggle" class="sr-only peer" onchange="toggleDashBilling()">
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
    </label>
    <span id="lbl-yearly" class="font-bold text-gray-400 text-sm">Yearly (Save 20%)</span>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="dashboard-plans-grid">
    @foreach($basePlans as $plan)
        @php
            $isSamePlan = $currentSubscription && $currentSubscription->plan_id == $plan->id;
            $isMonthlyActive = $isSamePlan && $activeCycle === 'monthly';
            $isYearlyActive = $isSamePlan && $activeCycle === 'yearly';
        @endphp
        <div class="panel p-6 shadow-sm flex flex-col {{ $isSamePlan ? 'ring-2 ring-indigo-600 border-indigo-600' : '' }}">
            <div class="tag-monthly {{ $isMonthlyActive ? '' : 'hidden' }}">
                <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2">Current Plan (Monthly)</div>
            </div>
            <div class="tag-yearly {{ $isYearlyActive ? '' : 'hidden' }}">
                <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2">Current Plan (Yearly)</div>
            </div>
            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
            
            <div class="mt-2 mb-4 price-display-monthly">
                <span class="text-3xl font-black text-gray-900 font-mono">₹{{ number_format($plan->price_monthly, 0) }}</span>
                <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">/mo</span>
            </div>
            <div class="mt-2 mb-4 price-display-yearly hidden">
                <span class="text-3xl font-black text-gray-900 font-mono">₹{{ number_format($plan->price_yearly, 0) }}</span>
                <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">/yr</span>
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

            {{-- Monthly Action Button --}}
            <div class="btn-action-monthly mt-auto">
                @if($isMonthlyActive)
                    <button disabled class="w-full btn btn-ghost py-2.5 justify-center cursor-not-allowed opacity-50">Active Plan (Monthly)</button>
                @else
                    <button type="button" onclick="openCheckoutModal({{ $plan->id }}, '{{ addslashes($plan->name) }}', {{ $plan->price_monthly }}, {{ $plan->price_yearly }}, 'base')" class="w-full btn btn-gold py-3 justify-center flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        Switch to {{ $plan->name }} (Monthly)
                    </button>
                @endif
            </div>

            {{-- Yearly Action Button --}}
            <div class="btn-action-yearly mt-auto hidden">
                @if($isYearlyActive)
                    <button disabled class="w-full btn btn-ghost py-2.5 justify-center cursor-not-allowed opacity-50">Active Plan (Yearly)</button>
                @else
                    <button type="button" onclick="openCheckoutModal({{ $plan->id }}, '{{ addslashes($plan->name) }}', {{ $plan->price_monthly }}, {{ $plan->price_yearly }}, 'base')" class="w-full btn btn-gold py-3 justify-center flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        {{ $isSamePlan ? 'Upgrade to Yearly' : 'Switch to ' . $plan->name . ' (Yearly)' }}
                    </button>
                @endif
            </div>
        </div>
    @endforeach
</div>

@if($addonPlans->count() > 0)
<div class="mt-12">
    <h2 class="text-xl font-bold text-gray-900 mb-2">Power-ups &amp; Add-ons</h2>
    <p class="text-sm text-gray-500 mb-6">Need more features? Add these modular power-ups to your subscription anytime.</p>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6" id="dashboard-addons-grid">
        @foreach($addonPlans as $plan)
            @php
                $activeAddon = auth()->user()->organization->activeAddons->firstWhere('plan_id', $plan->id);
                $hasAddon = $activeAddon !== null;
                $addonCycle = $hasAddon ? $activeAddon->billing_cycle : null;
                $isAddonMonthlyActive = $hasAddon && $addonCycle === 'monthly';
                $isAddonYearlyActive = $hasAddon && $addonCycle === 'yearly';
            @endphp
            <div class="panel p-5 shadow-sm flex flex-col border border-gray-100 {{ $hasAddon ? 'ring-2 ring-indigo-600' : '' }}">
                <div class="tag-monthly {{ $isAddonMonthlyActive ? '' : 'hidden' }}">
                    <div class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider mb-2">Active Add-on (Monthly)</div>
                </div>
                <div class="tag-yearly {{ $isAddonYearlyActive ? '' : 'hidden' }}">
                    <div class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider mb-2">Active Add-on (Yearly)</div>
                </div>
                @if(!$hasAddon)
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Add-on Module</div>
                @endif
                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $plan->name }}</h3>
                
                <div class="mt-1 mb-3 price-display-monthly">
                    <span class="text-2xl font-black text-gray-900 font-mono">₹{{ number_format($plan->price_monthly, 0) }}</span>
                    <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">/mo</span>
                </div>
                <div class="mt-1 mb-3 price-display-yearly hidden">
                    <span class="text-2xl font-black text-gray-900 font-mono">₹{{ number_format($plan->price_yearly, 0) }}</span>
                    <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">/yr</span>
                </div>
                
                <ul class="space-y-2 mb-4">
                    @foreach($plan->features as $feature)
                        @php
                            $label = $featureMap[$feature->feature_code] ?? ucwords(str_replace('_', ' ', $feature->feature_code));
                        @endphp
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="font-bold text-green-600">✓</span>
                            <span>{{ $label }}</span>
                        </li>
                    @endforeach
                </ul>

                {{-- Addon Monthly Button --}}
                <div class="btn-action-monthly mt-auto">
                    @if($isAddonMonthlyActive)
                        <button disabled class="w-full btn btn-ghost py-2 justify-center cursor-not-allowed opacity-50 text-sm">Added (Monthly)</button>
                    @else
                        <button type="button" onclick="openCheckoutModal({{ $plan->id }}, '{{ addslashes($plan->name) }}', {{ $plan->price_monthly }}, {{ $plan->price_yearly }}, 'addon')" class="w-full btn btn-gold py-2 justify-center flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            Add to Plan (Monthly)
                        </button>
                    @endif
                </div>

                {{-- Addon Yearly Button --}}
                <div class="btn-action-yearly mt-auto hidden">
                    @if($isAddonYearlyActive)
                        <button disabled class="w-full btn btn-ghost py-2 justify-center cursor-not-allowed opacity-50 text-sm">Added (Yearly)</button>
                    @else
                        <button type="button" onclick="openCheckoutModal({{ $plan->id }}, '{{ addslashes($plan->name) }}', {{ $plan->price_monthly }}, {{ $plan->price_yearly }}, 'addon')" class="w-full btn btn-gold py-2 justify-center flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            Add to Plan (Yearly)
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- BUNDLED CHECKOUT & ADDON CUSTOMIZER MODAL -->
<div id="checkoutModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-100 animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-6 flex justify-between items-center">
            <div>
                <span class="text-[10px] font-bold font-mono uppercase tracking-widest text-amber-400">CHECKOUT &amp; BUNDLE</span>
                <h3 class="text-lg font-bold" id="modalPlanTitle">Configure Plan Subscription</h3>
            </div>
            <button type="button" onclick="closeCheckoutModal()" class="text-gray-400 hover:text-white p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
            <!-- Selected Base Plan Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Base Selection</span>
                    <h4 class="text-base font-bold text-gray-900" id="modalBaseName">Pro Plan</h4>
                    <span class="text-xs font-semibold text-indigo-600 font-mono" id="modalCycleBadge">Monthly Cycle</span>
                </div>
                <div class="text-right">
                    <span class="text-xl font-black text-gray-900 font-mono" id="modalBasePrice">₹999</span>
                </div>
            </div>

            <!-- Optional Add-ons Selection List (for Base Plans) -->
            <div id="modalAddonsSection" class="space-y-3">
                <div class="flex justify-between items-center">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">⚡ Stack Power-Up Add-Ons (Optional)</h4>
                    <span class="text-[11px] text-gray-400">Bundle in 1 single payment</span>
                </div>

                <div class="space-y-2.5" id="modalAddonList">
                    @foreach($addonPlans as $addon)
                        <label class="flex items-center justify-between p-3.5 border border-gray-200 rounded-xl cursor-pointer hover:border-amber-400 hover:bg-amber-50/20 transition">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="bundle_addon" value="{{ $addon->id }}" 
                                       data-name="{{ $addon->name }}"
                                       data-price-monthly="{{ $addon->price_monthly }}"
                                       data-price-yearly="{{ $addon->price_yearly }}"
                                       onchange="recalculateModalTotal()"
                                       class="w-4 h-4 text-amber-500 border-gray-300 rounded focus:ring-amber-400">
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $addon->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $addon->description ?: 'Instant feature upgrade.' }}</div>
                                </div>
                            </div>
                            <div class="text-right pl-3">
                                <span class="text-sm font-bold text-gray-900 font-mono addon-price-tag" 
                                      data-monthly="₹{{ number_format($addon->price_monthly, 0) }}" 
                                      data-yearly="₹{{ number_format($addon->price_yearly, 0) }}">
                                    ₹{{ number_format($addon->price_monthly, 0) }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Summary Breakdown -->
            <div class="border-t border-gray-200 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Base Plan:</span>
                    <span class="font-mono font-bold" id="summaryBasePrice">₹0</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Add-ons Total:</span>
                    <span class="font-mono font-bold" id="summaryAddonsPrice">₹0</span>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-900 border-t border-dashed border-gray-200 pt-2">
                    <span>Total Amount Payable:</span>
                    <span class="font-mono text-xl text-indigo-600 font-black" id="summaryTotalPrice">₹0</span>
                </div>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-between items-center">
            <button type="button" onclick="closeCheckoutModal()" class="text-sm font-bold text-gray-500 hover:text-gray-700">Cancel</button>
            <button type="button" id="btnConfirmCheckout" onclick="executeBundledCheckout()" class="btn btn-gold py-2.5 px-6 font-bold flex items-center gap-2 shadow-sm">
                <span>Proceed to Pay</span>
                <span id="btnPayAmount" class="font-mono font-black">₹0</span>
                <span>&rarr;</span>
            </button>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
let currentSelectedPlan = {
    id: null,
    name: '',
    priceMonthly: 0,
    priceYearly: 0,
    type: 'base'
};

function toggleDashBilling() {
    const isYearly = document.getElementById('billing-cycle-toggle').checked;
    const monthlyDisplays = document.querySelectorAll('.price-display-monthly');
    const yearlyDisplays = document.querySelectorAll('.price-display-yearly');
    const monthlyButtons = document.querySelectorAll('.btn-action-monthly');
    const yearlyButtons = document.querySelectorAll('.btn-action-yearly');
    
    if (isYearly) {
        document.getElementById('lbl-yearly').classList.replace('text-gray-400', 'text-gray-900');
        document.getElementById('lbl-monthly').classList.replace('text-gray-900', 'text-gray-400');
        monthlyDisplays.forEach(el => el.classList.add('hidden'));
        yearlyDisplays.forEach(el => el.classList.remove('hidden'));
        monthlyButtons.forEach(el => el.classList.add('hidden'));
        yearlyButtons.forEach(el => el.classList.remove('hidden'));
    } else {
        document.getElementById('lbl-monthly').classList.replace('text-gray-400', 'text-gray-900');
        document.getElementById('lbl-yearly').classList.replace('text-gray-900', 'text-gray-400');
        monthlyDisplays.forEach(el => el.classList.remove('hidden'));
        yearlyDisplays.forEach(el => el.classList.add('hidden'));
        monthlyButtons.forEach(el => el.classList.remove('hidden'));
        yearlyButtons.forEach(el => el.classList.add('hidden'));
    }
}

function openCheckoutModal(planId, planName, priceMonthly, priceYearly, planType) {
    const isYearly = document.getElementById('billing-cycle-toggle').checked;
    const cycle = isYearly ? 'yearly' : 'monthly';
    
    currentSelectedPlan = {
        id: planId,
        name: planName,
        priceMonthly: parseFloat(priceMonthly),
        priceYearly: parseFloat(priceYearly),
        type: planType
    };

    // Set Base Plan Info
    document.getElementById('modalPlanTitle').textContent = 'Subscribe: ' + planName;
    document.getElementById('modalBaseName').textContent = planName;
    document.getElementById('modalCycleBadge').textContent = isYearly ? 'Yearly Billing (Save 20%)' : 'Monthly Billing';

    const basePrice = isYearly ? currentSelectedPlan.priceYearly : currentSelectedPlan.priceMonthly;
    document.getElementById('modalBasePrice').textContent = '₹' + basePrice.toLocaleString('en-IN');

    // Reset addon checkboxes
    document.querySelectorAll('input[name="bundle_addon"]').forEach(cb => {
        cb.checked = false;
    });

    // Update addon price tags in modal for cycle
    document.querySelectorAll('.addon-price-tag').forEach(tag => {
        tag.textContent = isYearly ? tag.getAttribute('data-yearly') : tag.getAttribute('data-monthly');
    });

    // If selected plan is an addon itself, hide the addon selection section
    if (planType === 'addon') {
        document.getElementById('modalAddonsSection').classList.add('hidden');
    } else {
        document.getElementById('modalAddonsSection').classList.remove('hidden');
    }

    recalculateModalTotal();
    document.getElementById('checkoutModal').classList.remove('hidden');
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').classList.add('hidden');
}

function recalculateModalTotal() {
    const isYearly = document.getElementById('billing-cycle-toggle').checked;
    const basePrice = isYearly ? currentSelectedPlan.priceYearly : currentSelectedPlan.priceMonthly;
    
    let addonsTotal = 0;
    if (currentSelectedPlan.type !== 'addon') {
        document.querySelectorAll('input[name="bundle_addon"]:checked').forEach(cb => {
            const addPrice = isYearly ? parseFloat(cb.getAttribute('data-price-yearly')) : parseFloat(cb.getAttribute('data-price-monthly'));
            addonsTotal += addPrice;
        });
    }

    const total = basePrice + addonsTotal;

    document.getElementById('summaryBasePrice').textContent = '₹' + basePrice.toLocaleString('en-IN');
    document.getElementById('summaryAddonsPrice').textContent = '₹' + addonsTotal.toLocaleString('en-IN');
    document.getElementById('summaryTotalPrice').textContent = '₹' + total.toLocaleString('en-IN');
    document.getElementById('btnPayAmount').textContent = '₹' + total.toLocaleString('en-IN');
}

function executeBundledCheckout() {
    const isYearly = document.getElementById('billing-cycle-toggle').checked;
    const cycle = isYearly ? 'yearly' : 'monthly';
    
    const selectedAddonIds = [];
    if (currentSelectedPlan.type !== 'addon') {
        document.querySelectorAll('input[name="bundle_addon"]:checked').forEach(cb => {
            selectedAddonIds.push(cb.value);
        });
    }

    const btn = document.getElementById('btnConfirmCheckout');
    btn.disabled = true;
    btn.innerHTML = `<span>Processing...</span>`;

    fetch('{{ url("organization/subscription/initiate") }}/' + currentSelectedPlan.id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            billing_cycle: cycle,
            addon_ids: selectedAddonIds
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = `<span>Proceed to Pay</span> <span class="font-mono font-black">₹${data.total_formatted || '0'}</span> <span>&rarr;</span>`;

        if (!data.success) {
            alert(data.message || 'Error initializing payment.');
            return;
        }

        if (data.is_free) {
            alert(data.message);
            closeCheckoutModal();
            window.location.reload();
            return;
        }

        closeCheckoutModal();

        var options = {
            "key": data.key,
            "amount": data.amount,
            "currency": data.currency,
            "name": data.org_name,
            "description": "Subscription for " + data.plan_name,
            "order_id": data.order_id,
            "prefill": {
                "name": data.user_name,
                "email": data.user_email
            },
            "theme": { "color": "#17233F" },
            "handler": function (response) {
                // Confirm payment and activate all selected plans
                fetch('{{ route("organization.subscription.confirm") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_id: data.plan_id,
                        addon_ids: data.addon_ids,
                        billing_cycle: data.billing_cycle,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id
                    })
                })
                .then(res => res.json())
                .then(resData => {
                    alert(resData.message || 'Subscription successfully activated!');
                    window.location.href = "{{ route('organization.dashboard') }}";
                });
            }
        };

        var rzp = new Razorpay(options);
        rzp.on('payment.failed', function(res){ alert("Payment failed: " + res.error.description); });
        rzp.open();
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = `<span>Proceed to Pay &rarr;</span>`;
        alert('Failed to process request: ' + err.message);
    });
}
</script>
@endsection

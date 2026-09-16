@extends('layouts.sme')

@section('title', 'Subscription & Billing')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.dashboard') }}" class="hover:text-slate-900 transition-colors">Dashboard</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Settings</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Subscription & Billing</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    💳
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Subscription & Billing</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Manage your active plan, system capacity quotas, modular power-up add-ons, and payment records.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Status Pill -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            @if($currentSubscription)
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-900 border border-slate-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full {{ $currentSubscription->status === 'Active' ? 'bg-emerald-500' : ($currentSubscription->status === 'Trial' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                    <span class="text-slate-500 font-medium">Plan:</span>
                    <span class="font-extrabold">{{ $currentSubscription->plan->name }} ({{ ucfirst($currentSubscription->billing_cycle) }})</span>
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>No Active Subscription</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-300 text-emerald-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-emerald-700 text-base">✓</span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-rose-700 text-base">⚠️</span>
        <div>
            <p class="font-bold">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- 2. Current Active Plan Overview Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 space-y-5">
        <!-- Top Status Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Current Active Plan</span>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h2 class="text-xl sm:text-2xl font-black text-slate-950">
                        {{ $currentSubscription ? $currentSubscription->plan->name : 'No Active Plan' }}
                    </h2>
                    @if($currentSubscription)
                        @if($currentSubscription->status === 'Trial')
                            @php
                                $daysLeft = \App\Services\SubscriptionService::getDaysRemaining(auth()->user()->organization_id);
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-50 text-amber-800 border border-amber-300">
                                ⏳ Free Trial ({{ $daysLeft }} {{ \Illuminate\Support\Str::plural('day', $daysLeft) }} remaining)
                            </span>
                        @elseif(in_array($currentSubscription->status, ['Expired', 'Cancelled', 'Refunded']))
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-rose-50 text-rose-800 border border-rose-300">
                                ⚠️ {{ $currentSubscription->status }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                ✓ Active Plan ({{ ucfirst($currentSubscription->billing_cycle) }})
                            </span>
                        @endif
                    @endif
                </div>
                <p class="text-xs text-slate-500 font-medium mt-1">
                    @if($currentSubscription)
                        Subscription cycle valid until: <span class="font-bold text-slate-800 font-mono">{{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('M d, Y') : 'Lifetime Access' }}</span>
                    @else
                        Unlock multi-location management, inventory, POS, and HR payroll by picking a plan below.
                    @endif
                </p>
            </div>

            <!-- Action: Refund Modal Trigger -->
            @if($currentSubscription && $currentSubscription->status === 'Active' && $currentSubscription->gateway_payment_id)
                <div>
                    <button type="button" onclick="document.getElementById('refundModal').classList.remove('hidden')" class="px-3.5 py-2 rounded-lg text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition shadow-2xs">
                        Request Plan Refund
                    </button>
                </div>
            @endif
        </div>

        <!-- Refund Confirmation Modal -->
        <div id="refundModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex justify-center items-center p-4">
            <div class="bg-white rounded-xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-200 p-6 space-y-4 animate-in fade-in zoom-in duration-150">
                <div class="flex items-center gap-2.5 text-rose-600">
                    <span class="text-2xl">⚠️</span>
                    <h3 class="text-lg font-black text-slate-950">Subscription Refund</h3>
                </div>
                <div class="p-3.5 bg-rose-50 rounded-lg border border-rose-200 text-rose-900 text-xs font-medium leading-relaxed">
                    <strong>Critical Warning:</strong> Processing a refund will immediately cancel your subscription and <strong>deactivate your organization account</strong>. You and your staff will lose system access immediately.
                </div>
                <p class="text-slate-600 text-xs font-medium">Are you certain you want to proceed with the cancellation and refund?</p>
                <form action="{{ route('organization.subscription.refund') }}" method="POST" class="flex gap-2.5 justify-end pt-2">
                    @csrf
                    <button type="button" onclick="document.getElementById('refundModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-lg text-xs transition shadow-xs">
                        Confirm Refund & Deactivate
                    </button>
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
                'max_locations' => 'Branch Locations Limit',
                'max_employees' => 'Max Employees Limit',
                'max_invoices_per_month' => 'Monthly Invoices Limit',
                'max_products' => 'Products Catalog Quota',
                'max_tables' => 'Restaurant Tables Limit',
                'payment_gateway' => 'Razorpay Payment Gateway',
            ];
            $activeAddons = auth()->user()->organization?->activeAddons ?? collect();
        @endphp

        <!-- Active Add-ons Strip (if any attached) -->
        @if($activeAddons->count() > 0)
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/90 space-y-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">⚡ Active Power-Up Add-Ons Attached:</span>
                <div class="flex flex-wrap gap-2">
                    @foreach($activeAddons as $addon)
                        <span class="inline-flex items-center gap-1.5 bg-white border border-slate-300 px-3 py-1 rounded-lg text-xs font-extrabold text-slate-900 shadow-2xs">
                            <span class="text-amber-500">⚡</span> {{ $addon->plan->name }} ({{ ucfirst($addon->billing_cycle) }})
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Features & Quota Limits Grid -->
        @if($currentSubscription)
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-3">Included Quota Limits &amp; Features</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($currentSubscription->plan->features as $feature)
                        @php
                            $val = strtolower(trim($feature->feature_value));
                            if ($val === 'false' || $val === 'no' || $val === '0') continue;
                            $label = $featureMap[$feature->feature_code] ?? ucwords(str_replace('_', ' ', $feature->feature_code));
                        @endphp
                        <div class="bg-slate-50/80 border border-slate-200/80 p-3 rounded-lg flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800">{{ $label }}</span>
                            @if($val === 'true' || $val === 'yes' || $val === '1')
                                <span class="text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-md">✓ Enabled</span>
                            @else
                                <span class="text-xs font-black bg-white text-slate-900 border border-slate-300 px-2.5 py-0.5 rounded-md font-mono shadow-2xs">{{ $feature->feature_value }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @php
        $orgType = auth()->user()->organization?->business_type ?? 'business';
        $basePlans = $plans->where('type', 'base')->filter(function($p) use ($orgType) {
            if ($orgType === 'restaurant') {
                return $p->category === 'restaurant' || $p->category === 'all';
            }
            return $p->category === 'business' || $p->category === 'all';
        });
        $addonPlans = $plans->where('type', 'addon');
        $activeCycle = $currentSubscription ? $currentSubscription->billing_cycle : null;
    @endphp

    <!-- 3. Available Base Plans Section -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
            <div>
                <h2 class="text-lg font-extrabold text-slate-950">Choose Your Plan</h2>
                <p class="text-xs text-slate-500 font-medium">Select a tier tailored for your business volume and feature requirements.</p>
            </div>

            <!-- Billing Cycle Switcher -->
            <div class="flex items-center gap-3 bg-slate-100 p-1 rounded-lg border border-slate-200 self-start sm:self-auto">
                <span id="lbl-monthly" class="font-black text-slate-950 text-xs px-2.5 py-1">Monthly</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="billing-cycle-toggle" class="sr-only peer" onchange="toggleDashBilling()">
                    <div class="w-10 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                </label>
                <span id="lbl-yearly" class="font-bold text-slate-500 text-xs px-2.5 py-1 flex items-center gap-1">
                    <span>Yearly</span>
                    <span class="bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-extrabold px-1.5 py-0.2 rounded">Save 20% 🎉</span>
                </span>
            </div>
        </div>

        <!-- Base Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="dashboard-plans-grid">
            @foreach($basePlans as $plan)
                @php
                    $isSamePlan = $currentSubscription && $currentSubscription->plan_id == $plan->id;
                    $isMonthlyActive = $isSamePlan && $activeCycle === 'monthly';
                    $isYearlyActive = $isSamePlan && $activeCycle === 'yearly';
                @endphp
                <div class="bg-white rounded-xl p-6 flex flex-col border transition-all duration-200 {{ $isSamePlan ? 'border-amber-500 ring-2 ring-amber-500/20 shadow-md relative overflow-hidden' : 'border-slate-200/90 hover:border-slate-400 hover:shadow-sm' }}">
                    
                    @if($isSamePlan)
                        <div class="tag-monthly {{ $isMonthlyActive ? '' : 'hidden' }} mb-3">
                            <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-md bg-amber-500 text-slate-950 tracking-wider">
                                ★ Current Active Plan (Monthly)
                            </span>
                        </div>
                        <div class="tag-yearly {{ $isYearlyActive ? '' : 'hidden' }} mb-3">
                            <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-md bg-amber-500 text-slate-950 tracking-wider">
                                ★ Current Active Plan (Yearly)
                            </span>
                        </div>
                    @endif

                    <h3 class="text-lg font-black text-slate-950 tracking-tight">{{ $plan->name }}</h3>
                    <p class="text-xs text-slate-600 mt-1 mb-4 leading-relaxed">{{ $plan->description }}</p>

                    <!-- Price Display -->
                    <div class="price-display-monthly flex items-baseline gap-1 mb-5">
                        <span class="text-3xl font-black text-slate-950 font-mono tracking-tight">₹{{ number_format($plan->price_monthly, 0) }}</span>
                        <span class="text-xs text-slate-500 font-semibold">/ month</span>
                    </div>
                    <div class="price-display-yearly hidden flex items-baseline gap-1 mb-5">
                        <span class="text-3xl font-black text-slate-950 font-mono tracking-tight">₹{{ number_format($plan->price_yearly, 0) }}</span>
                        <span class="text-xs text-slate-500 font-semibold">/ year</span>
                    </div>

                    <!-- Feature Checkmarks List -->
                    <div class="border-t border-slate-100 pt-4 flex-grow mb-6 space-y-2.5">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2">Features & Limits:</span>
                        @foreach($plan->features as $feature)
                            @php
                                $label = $featureMap[$feature->feature_code] ?? ucwords(str_replace('_', ' ', $feature->feature_code));
                                $val = strtolower(trim($feature->feature_value));
                            @endphp
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-700">{{ $label }}:</span>
                                @if($val === 'true' || $val === 'yes' || $val === '1')
                                    <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px] border border-emerald-200">✓ Yes</span>
                                @elseif($val === 'false' || $val === 'no' || $val === '0')
                                    <span class="font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded text-[11px]">✗ No</span>
                                @else
                                    <strong class="text-slate-900 font-mono bg-slate-100 px-2 py-0.5 rounded text-[11px] font-bold border border-slate-200">{{ $feature->feature_value }}</strong>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Action Buttons -->
                    {{-- Monthly CTA --}}
                    <div class="btn-action-monthly mt-auto">
                        @if($isMonthlyActive)
                            <button disabled class="w-full py-2.5 bg-slate-100 text-slate-400 font-extrabold text-xs rounded-lg border border-slate-200 cursor-not-allowed">
                                Current Plan (Monthly)
                            </button>
                        @else
                            <button type="button" onclick="openCheckoutModal({{ $plan->id }}, '{{ addslashes($plan->name) }}', {{ $plan->price_monthly }}, {{ $plan->price_yearly }}, 'base')" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                                Switch to {{ $plan->name }} (Monthly) &rarr;
                            </button>
                        @endif
                    </div>

                    {{-- Yearly CTA --}}
                    <div class="btn-action-yearly mt-auto hidden">
                        @if($isYearlyActive)
                            <button disabled class="w-full py-2.5 bg-slate-100 text-slate-400 font-extrabold text-xs rounded-lg border border-slate-200 cursor-not-allowed">
                                Current Plan (Yearly)
                            </button>
                        @else
                            <button type="button" onclick="openCheckoutModal({{ $plan->id }}, '{{ addslashes($plan->name) }}', {{ $plan->price_monthly }}, {{ $plan->price_yearly }}, 'base')" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                                {{ $isSamePlan ? 'Upgrade to Yearly' : 'Switch to ' . $plan->name . ' (Yearly)' }} &rarr;
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4. Modular Power-ups & Add-ons Grid -->
    @if($addonPlans->count() > 0)
    <div class="space-y-4 pt-4 border-t border-slate-200">
        <div>
            <h2 class="text-lg font-extrabold text-slate-950 flex items-center gap-2">
                <span>⚡ Modular Power-ups &amp; Add-ons</span>
            </h2>
            <p class="text-xs text-slate-500 font-medium">Extend your business capabilities with modular add-ons that attach directly to your active plan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="dashboard-addons-grid">
            @foreach($addonPlans as $plan)
                @php
                    $activeAddon = auth()->user()->organization?->activeAddons?->firstWhere('plan_id', $plan->id);
                    $hasAddon = $activeAddon !== null;
                    $addonCycle = $hasAddon ? $activeAddon->billing_cycle : null;
                    $isAddonMonthlyActive = $hasAddon && $addonCycle === 'monthly';
                    $isAddonYearlyActive = $hasAddon && $addonCycle === 'yearly';
                @endphp
                <div class="bg-white rounded-xl p-5 flex flex-col border transition-all duration-200 {{ $hasAddon ? 'border-amber-500 ring-2 ring-amber-500/20 shadow-xs' : 'border-slate-200/90 hover:border-slate-400' }}">
                    
                    @if($hasAddon)
                        <div class="tag-monthly {{ $isAddonMonthlyActive ? '' : 'hidden' }} mb-2">
                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-amber-500 text-slate-950">Active Add-on (Monthly)</span>
                        </div>
                        <div class="tag-yearly {{ $isAddonYearlyActive ? '' : 'hidden' }} mb-2">
                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-amber-500 text-slate-950">Active Add-on (Yearly)</span>
                        </div>
                    @else
                        <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2">Add-on Module</span>
                    @endif

                    <h3 class="text-base font-extrabold text-slate-950">{{ $plan->name }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5 mb-3 leading-relaxed">{{ $plan->description ?: 'Specialized modular feature expansion.' }}</p>

                    <!-- Addon Price -->
                    <div class="price-display-monthly flex items-baseline gap-1 mb-4">
                        <span class="text-2xl font-black text-slate-950 font-mono">₹{{ number_format($plan->price_monthly, 0) }}</span>
                        <span class="text-xs text-slate-500 font-semibold">/ mo</span>
                    </div>
                    <div class="price-display-yearly hidden flex items-baseline gap-1 mb-4">
                        <span class="text-2xl font-black text-slate-950 font-mono">₹{{ number_format($plan->price_yearly, 0) }}</span>
                        <span class="text-xs text-slate-500 font-semibold">/ yr</span>
                    </div>

                    <!-- Features -->
                    <ul class="space-y-1.5 mb-5 flex-grow border-t border-slate-100 pt-3">
                        @foreach($plan->features as $feature)
                            @php
                                $label = $featureMap[$feature->feature_code] ?? ucwords(str_replace('_', ' ', $feature->feature_code));
                            @endphp
                            <li class="flex items-center gap-1.5 text-xs text-slate-700">
                                <span class="font-bold text-emerald-600">✓</span>
                                <span class="font-semibold">{{ $label }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Action Button -->
                    <div class="btn-action-monthly mt-auto">
                        @if($isAddonMonthlyActive)
                            <button disabled class="w-full py-2 bg-slate-100 text-slate-400 font-bold text-xs rounded-lg border border-slate-200 cursor-not-allowed">
                                Attached (Monthly)
                            </button>
                        @else
                            <button type="button" onclick="openCheckoutModal({{ $plan->id }}, '{{ addslashes($plan->name) }}', {{ $plan->price_monthly }}, {{ $plan->price_yearly }}, 'addon')" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                                Add to Plan (Monthly)
                            </button>
                        @endif
                    </div>

                    <div class="btn-action-yearly mt-auto hidden">
                        @if($isAddonYearlyActive)
                            <button disabled class="w-full py-2 bg-slate-100 text-slate-400 font-bold text-xs rounded-lg border border-slate-200 cursor-not-allowed">
                                Attached (Yearly)
                            </button>
                        @else
                            <button type="button" onclick="openCheckoutModal({{ $plan->id }}, '{{ addslashes($plan->name) }}', {{ $plan->price_monthly }}, {{ $plan->price_yearly }}, 'addon')" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                                Add to Plan (Yearly)
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 5. Subscription & Payment History Table -->
    @if(isset($subscriptionHistory) && $subscriptionHistory->count() > 0)
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider flex items-center gap-2">
                    <span>📜 Subscription &amp; Billing History</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Audit log of your organization's plan purchases, renewals, and transaction references.</p>
            </div>
            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                {{ $subscriptionHistory->count() }} Records
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-3 w-12">#</th>
                        <th class="p-3">Plan / Module</th>
                        <th class="p-3">Cycle</th>
                        <th class="p-3">Starts At</th>
                        <th class="p-3">Expires At</th>
                        <th class="p-3">Payment / Ref ID</th>
                        <th class="p-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($subscriptionHistory as $index => $history)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3 font-bold text-slate-400 font-mono">{{ $index + 1 }}</td>
                            <td class="p-3">
                                <span class="font-extrabold text-slate-950">{{ $history->plan->name ?? 'Standard Plan' }}</span>
                                <span class="text-[10px] font-bold text-slate-500 block uppercase">{{ $history->plan->type ?? 'Base' }}</span>
                            </td>
                            <td class="p-3">
                                <span class="font-bold text-slate-800">{{ ucfirst($history->billing_cycle) }}</span>
                            </td>
                            <td class="p-3 font-mono text-slate-600">
                                {{ $history->starts_at ? $history->starts_at->format('M d, Y') : '-' }}
                            </td>
                            <td class="p-3 font-mono text-slate-600">
                                {{ $history->ends_at ? $history->ends_at->format('M d, Y') : 'Lifetime' }}
                            </td>
                            <td class="p-3 font-mono text-slate-500 text-[11px]">
                                {{ $history->gatewayPayment ? $history->gatewayPayment->payment_id : ($history->gateway_payment_id ? 'PAY-' . $history->gateway_payment_id : 'Free / System') }}
                            </td>
                            <td class="p-3 text-right">
                                @if($history->status === 'Active')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-300">Active</span>
                                @elseif($history->status === 'Trial')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-300">Trial</span>
                                @elseif($history->status === 'Refunded')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-50 text-rose-800 border border-rose-300">Refunded</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">{{ $history->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<!-- 6. BUNDLED CHECKOUT & ADDON CUSTOMIZER MODAL -->
<div id="checkoutModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in duration-150 flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="bg-slate-950 text-white p-5 flex justify-between items-center border-b border-slate-800 shrink-0">
            <div>
                <span class="text-[10px] font-extrabold font-mono uppercase tracking-widest text-amber-400">PLAN CONFIGURATION &amp; CHECKOUT</span>
                <h3 class="text-base font-extrabold" id="modalPlanTitle">Subscribe: Plan Name</h3>
            </div>
            <button type="button" onclick="closeCheckoutModal()" class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg font-bold transition">
                &times;
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5 space-y-4 overflow-y-auto text-xs">
            <!-- Selected Base Plan Card -->
            <div class="bg-slate-50 border border-slate-200/90 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">Base Plan Selected:</span>
                    <h4 class="text-sm font-extrabold text-slate-950" id="modalBaseName">Pro Plan</h4>
                    <span class="text-[11px] font-bold text-amber-600 font-mono" id="modalCycleBadge">Monthly Cycle</span>
                </div>
                <div class="text-right">
                    <span class="text-xl font-black text-slate-950 font-mono" id="modalBasePrice">₹999</span>
                </div>
            </div>

            <!-- Optional Add-ons Selection List -->
            <div id="modalAddonsSection" class="space-y-2.5">
                <div class="flex justify-between items-center">
                    <h4 class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider">⚡ Stack Power-Up Add-Ons (Optional)</h4>
                    <span class="text-[10px] text-slate-400 font-medium">Single bundled payment</span>
                </div>

                <div class="space-y-2" id="modalAddonList">
                    @foreach($addonPlans as $addon)
                        <label class="flex items-center justify-between p-3 border border-slate-200 rounded-lg cursor-pointer hover:border-amber-400 hover:bg-amber-50/20 transition">
                            <div class="flex items-center gap-2.5">
                                <input type="checkbox" name="bundle_addon" value="{{ $addon->id }}" 
                                       data-name="{{ $addon->name }}"
                                       data-price-monthly="{{ $addon->price_monthly }}"
                                       data-price-yearly="{{ $addon->price_yearly }}"
                                       onchange="recalculateModalTotal()"
                                       class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-400">
                                <div>
                                    <div class="text-xs font-bold text-slate-950">{{ $addon->name }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $addon->description ?: 'Modular upgrade.' }}</div>
                                </div>
                            </div>
                            <div class="text-right pl-2">
                                <span class="text-xs font-black text-slate-950 font-mono addon-price-tag" 
                                      data-monthly="₹{{ number_format($addon->price_monthly, 0) }}" 
                                      data-yearly="₹{{ number_format($addon->price_yearly, 0) }}">
                                    ₹{{ number_format($addon->price_monthly, 0) }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Pricing Breakdown -->
            <div class="border-t border-slate-200 pt-3 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Base Tier:</span>
                    <span class="font-mono font-bold text-slate-900" id="summaryBasePrice">₹0</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Power-Up Add-ons Total:</span>
                    <span class="font-mono font-bold text-slate-900" id="summaryAddonsPrice">₹0</span>
                </div>
                <div class="flex justify-between text-sm font-extrabold text-slate-950 border-t border-slate-200 pt-2">
                    <span>Total Amount Payable:</span>
                    <span class="font-mono text-xl text-emerald-700 font-black" id="summaryTotalPrice">₹0</span>
                </div>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="bg-slate-50 px-5 py-3.5 border-t border-slate-200 flex justify-between items-center shrink-0">
            <button type="button" onclick="closeCheckoutModal()" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                Cancel
            </button>
            <button type="button" id="btnConfirmCheckout" onclick="executeBundledCheckout()" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition flex items-center gap-2">
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
        document.getElementById('lbl-yearly').classList.replace('text-slate-500', 'text-slate-950');
        document.getElementById('lbl-monthly').classList.replace('text-slate-950', 'text-slate-500');
        monthlyDisplays.forEach(el => el.classList.add('hidden'));
        yearlyDisplays.forEach(el => el.classList.remove('hidden'));
        monthlyButtons.forEach(el => el.classList.add('hidden'));
        yearlyButtons.forEach(el => el.classList.remove('hidden'));
    } else {
        document.getElementById('lbl-monthly').classList.replace('text-slate-500', 'text-slate-950');
        document.getElementById('lbl-yearly').classList.replace('text-slate-950', 'text-slate-500');
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
    btn.innerHTML = `<span>Processing payment...</span>`;

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
            "theme": { "color": "#0f172a" },
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

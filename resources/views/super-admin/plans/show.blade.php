@extends('layouts.super-admin')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                <a href="{{ route('super-admin.plans.index') }}" class="hover:text-indigo-600 transition">Plans</a>
                <span>/</span>
                <span class="text-gray-800">Plan Details</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $plan->name }}</h1>
                
                <!-- Status Badge -->
                @if($plan->is_active)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                    </span>
                @endif

                <!-- Plan Type Badge -->
                @if($plan->type === 'addon')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        ⚡ Add-on Module
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                        📦 Base Plan
                    </span>
                @endif

                <!-- Category Badge -->
                @php
                    $cat = $plan->category ?? 'business';
                @endphp
                @if($cat === 'restaurant')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
                        🍽️ Restaurant POS
                    </span>
                @elseif($cat === 'business')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                        🏢 Retail & Business
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                        🌐 All Categories
                    </span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.plans.index') }}" class="btn btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Plans
            </a>
            <a href="{{ route('super-admin.plans.edit', $plan) }}" class="btn btn-gold btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Plan
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Monthly Price Card -->
        <div class="panel p-5 bg-white flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 text-xl font-bold">
                ₹
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Monthly Billing</p>
                <div class="text-2xl font-bold text-gray-900 mt-0.5">
                    ₹{{ number_format($plan->price_monthly, 2) }}
                    <span class="text-xs font-medium text-gray-500">/ mo</span>
                </div>
            </div>
        </div>

        <!-- Yearly Price Card -->
        <div class="panel p-5 bg-white flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 text-xl font-bold">
                📅
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Yearly Billing</p>
                <div class="text-2xl font-bold text-gray-900 mt-0.5">
                    ₹{{ number_format($plan->price_yearly, 2) }}
                    <span class="text-xs font-medium text-gray-500">/ yr</span>
                </div>
            </div>
        </div>

        <!-- Active Subscriptions Card -->
        <div class="panel p-5 bg-white flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 text-xl font-bold">
                👥
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Subscribers</p>
                <div class="text-2xl font-bold text-gray-900 mt-0.5">
                    {{ $plan->subscriptions->count() }}
                    <span class="text-xs font-medium text-gray-500">orgs active</span>
                </div>
            </div>
        </div>

        <!-- Classification Card -->
        <div class="panel p-5 bg-white flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 text-xl font-bold">
                🏷️
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Plan Type</p>
                <div class="text-lg font-bold text-gray-900 mt-0.5 capitalize">
                    {{ $plan->type === 'addon' ? 'Add-on' : 'Base Plan' }}
                    <span class="text-xs font-medium text-gray-500 block capitalize">Target: {{ $plan->category ?? 'Business' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Description & Features Table (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Description Panel -->
            <div class="panel p-6 bg-white">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2 pb-3 border-b border-gray-100">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Plan Summary & Description
                </h3>
                <p class="text-sm text-gray-600 leading-relaxed mt-3">
                    {{ $plan->description ?: 'No detailed description provided for this plan yet.' }}
                </p>
            </div>

            <!-- Features & Module Limits Panel -->
            <div class="panel p-6 bg-white">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Plan Features & Configured Limits
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Permissions, module toggles, and quota limits granted by this plan.</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg">
                        {{ $plan->features->count() }} Configured
                    </span>
                </div>

                @if($plan->features->count() > 0)
                    @php
                        $featureMap = [
                            'module_retail' => ['name' => 'Retail & Inventory ERP', 'icon' => '🛒'],
                            'module_payroll' => ['name' => 'HR & Payroll Module', 'icon' => '👥'],
                            'module_restaurant' => ['name' => 'Restaurant POS & KOT', 'icon' => '🍽️'],
                            'digital_qr_menu' => ['name' => 'Digital QR Menu', 'icon' => '📱'],
                            'kitchen_display' => ['name' => 'Kitchen Display System (KDS)', 'icon' => '🍳'],
                            'table_management' => ['name' => 'Table & Order Management', 'icon' => '🪑'],
                            'advanced_analytics' => ['name' => 'Advanced Analytics & Reports', 'icon' => '📈'],
                            'max_clients' => ['name' => 'Max Clients Quota', 'icon' => '🤝'],
                            'max_locations' => ['name' => 'Multi-Location Limit', 'icon' => '📍'],
                            'max_employees' => ['name' => 'Max Employees Limit', 'icon' => '👔'],
                            'max_invoices_per_month' => ['name' => 'Monthly Invoice Limit', 'icon' => '📄'],
                            'max_products' => ['name' => 'Max Products Catalog Limit', 'icon' => '📦'],
                            'max_tables' => ['name' => 'Max Restaurant Tables Quota', 'icon' => '🪑'],
                            'payment_gateway' => ['name' => 'Razorpay Payment Gateway', 'icon' => '💳'],
                            'barcode_scanning' => ['name' => 'Barcode Scanner Integration', 'icon' => '🔍'],
                        ];
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[11px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100">
                                    <th class="pb-3 pl-2">Feature / Module</th>
                                    <th class="pb-3">Key Code</th>
                                    <th class="pb-3 text-right pr-2">Configured Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($plan->features as $feat)
                                    @php
                                        $meta = $featureMap[$feat->feature_code] ?? null;
                                        $label = $meta['name'] ?? ucwords(str_replace('_', ' ', $feat->feature_code));
                                        $icon = $meta['icon'] ?? '⚙️';
                                        $val = strtolower(trim($feat->feature_value));
                                    @endphp
                                    <tr class="hover:bg-gray-50/80 transition group">
                                        <td class="py-3 pl-2">
                                            <div class="flex items-center gap-2.5">
                                                <span class="text-base">{{ $icon }}</span>
                                                <span class="font-bold text-sm text-gray-900">{{ $label }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 font-mono text-xs text-gray-400">
                                            <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600">{{ $feat->feature_code }}</span>
                                        </td>
                                        <td class="py-3 text-right pr-2">
                                            @if($val === 'true' || $val === 'yes' || $val === '1')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    ✓ Enabled
                                                </span>
                                            @elseif($val === 'false' || $val === 'no' || $val === '0')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-400 border border-gray-200">
                                                    ✕ Disabled
                                                </span>
                                            @elseif($val === 'unlimited')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    ∞ Unlimited
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 font-mono">
                                                    {{ $feat->feature_value }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <p class="text-sm">No specific module limits mapped for this plan.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Subscribed Organizations & Danger Zone (1 col) -->
        <div class="space-y-6">
            <!-- Subscribed Organizations Panel -->
            <div class="panel p-6 bg-white">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Subscribed Organizations
                    </h3>
                    <span class="text-xs font-bold bg-indigo-50 text-indigo-700 px-2.5 py-0.5 rounded-full border border-indigo-100">
                        {{ $plan->subscriptions->count() }} Total
                    </span>
                </div>

                @if($plan->subscriptions->count() > 0)
                    <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto pr-1">
                        @foreach($plan->subscriptions as $sub)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">{{ $sub->organization->name ?? 'Unknown Org' }}</h4>
                                    <p class="text-xs text-gray-400">Subscribed: {{ $sub->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $sub->status === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $sub->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 mx-auto mb-2 text-xl">
                            🏢
                        </div>
                        <p class="text-xs text-gray-500 font-medium">No organizations are currently subscribed to this plan.</p>
                    </div>
                @endif
            </div>

            <!-- Danger Zone / Delete Panel -->
            <div class="panel p-6 bg-white border-red-100">
                <h3 class="text-sm font-bold text-rose-900 uppercase tracking-wider pb-3 border-b border-gray-100 mb-3">
                    Plan Management
                </h3>
                <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                    Need to modify limits or deactivate this plan? You can edit anytime without affecting active user sessions.
                </p>

                <div class="space-y-3">
                    <a href="{{ route('super-admin.plans.edit', $plan) }}" class="btn btn-secondary w-full text-center">
                        Edit Plan Details
                    </a>

                    @if($plan->subscriptions->count() === 0)
                        <form action="{{ route('super-admin.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this pricing plan? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-center py-2.5 px-4 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                Delete Plan Permanently
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full text-center py-2.5 px-4 rounded-xl text-xs font-bold text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed" title="Cannot delete plan with active subscriptions">
                            Delete Disabled (Active Subscribers)
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

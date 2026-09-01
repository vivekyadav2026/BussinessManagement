@extends('layouts.super-admin')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Plan Details</h1>
      <p class="text-gray-500 mt-1">Details for pricing plan: {{ $plan->name }}</p>
  </div>
  <div class="flex gap-2">
      <a class="btn bg-gray-100 text-gray-700 btn-sm" href="{{ route('super-admin.plans.index') }}">Back to Plans</a>
      <a class="btn btn-gold btn-sm" href="{{ route('super-admin.plans.edit', $plan) }}">Edit Plan</a>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="panel mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Pricing & Metadata</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Plan Name</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">{{ $plan->name }}</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Active Status</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">
                        @if($plan->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Monthly Price</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">₹{{ number_format($plan->price_monthly, 2) }}</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Yearly Price</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">₹{{ number_format($plan->price_yearly, 2) }}</div>
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-xs font-semibold text-gray-400 uppercase">Description</label>
                <div class="mt-1 text-sm text-gray-700 leading-relaxed">{{ $plan->description ?: 'No description provided.' }}</div>
            </div>
        </div>
        
        <div class="panel">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Plan Features & Module Limits</h3>
            @if($plan->features->count() > 0)
                @php
                    $featureMap = [
                        'module_retail' => 'Retail & Inventory ERP',
                        'module_payroll' => 'HR & Payroll Module',
                        'module_restaurant' => 'Restaurant POS & KOT',
                        'digital_qr_menu' => 'Digital QR Menu',
                        'kitchen_display' => 'Kitchen Display System (KDS)',
                        'table_management' => 'Table & Order Management',
                        'max_employees' => 'Max Employees Limit',
                        'max_invoices_per_month' => 'Monthly Invoice Limit',
                        'payment_gateway' => 'Razorpay Payment Gateway',
                    ];
                @endphp
                <table class="inv-table w-full">
                    <thead>
                        <tr>
                            <th>Feature Name</th>
                            <th>Key Code</th>
                            <th>Configured Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plan->features as $feat)
                            @php
                                $label = $featureMap[$feat->feature_code] ?? ucwords(str_replace('_', ' ', $feat->feature_code));
                                $val = strtolower(trim($feat->feature_value));
                            @endphp
                            <tr>
                                <td class="font-bold text-sm text-gray-800">{{ $label }}</td>
                                <td class="font-mono text-xs text-gray-400">{{ $feat->feature_code }}</td>
                                <td>
                                    @if($val === 'true' || $val === 'yes')
                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-0.5 rounded-full border border-green-200">✓ Enabled (true)</span>
                                    @elseif($val === 'false' || $val === 'no')
                                        <span class="bg-gray-100 text-gray-400 text-xs font-bold px-3 py-0.5 rounded-full border border-gray-200">✗ Disabled (false)</span>
                                    @else
                                        <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-0.5 rounded-full border border-indigo-100 font-mono">{{ $feat->feature_value }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-sm">No special configuration keys mapped for this plan.</p>
            @endif
        </div>

    </div>
    
    <div>
        <div class="panel">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Usage & Admin</h3>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 uppercase">Active Subscriptions</label>
                <div class="text-sm font-bold text-gray-900 mt-1">{{ $plan->subscriptions()->count() }} organizations</div>
            </div>
            
            @if($plan->subscriptions()->count() === 0)
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <form action="{{ route('super-admin.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this pricing plan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none">
                            Delete Plan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

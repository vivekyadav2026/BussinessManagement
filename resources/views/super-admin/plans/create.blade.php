@extends('layouts.super-admin')

@section('content')
<div class="py-6 max-w-4xl mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add Subscription Plan</h1>
            <p class="text-sm text-gray-500">Define plan pricing, limits, and module access permissions.</p>
        </div>
        <a href="{{ route('super-admin.plans.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">&larr; Back to Plans</a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 text-sm font-semibold">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('super-admin.plans.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <h3 class="font-bold text-base text-gray-900 border-b border-gray-100 pb-3">1. Plan Basic Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Plan Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Pro Plan, Gold Plan" required class="w-full border-gray-300 rounded-xl px-4 py-2 text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status</label>
                    <select name="is_active" class="w-full border-gray-300 rounded-xl px-4 py-2 text-sm font-semibold">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Monthly Price (₹) *</label>
                    <input type="number" step="0.01" name="price_monthly" value="{{ old('price_monthly', 0) }}" required class="w-full border-gray-300 rounded-xl px-4 py-2 text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Yearly Price (₹) *</label>
                    <input type="number" step="0.01" name="price_yearly" value="{{ old('price_yearly', 0) }}" required class="w-full border-gray-300 rounded-xl px-4 py-2 text-sm font-semibold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Description</label>
                <textarea name="description" rows="2" placeholder="Brief summary of plan target audience" class="w-full border-gray-300 rounded-xl px-4 py-2 text-sm">{{ old('description') }}</textarea>
            </div>

            <h3 class="font-bold text-base text-gray-900 border-b border-gray-100 pb-3 pt-4">2. Module Access & Plan Limits</h3>
            
            <div id="features-container" class="space-y-4">
                @php
                    $defaultFeatures = [
                        'module_retail' => [
                            'label' => 'Retail & Inventory ERP',
                            'type' => 'boolean',
                            'default' => 'true',
                            'desc' => 'Products catalog, stock movements, inventory tracking & barcode scanner.'
                        ],
                        'module_payroll' => [
                            'label' => 'HR & Payroll Module',
                            'type' => 'boolean',
                            'default' => 'true',
                            'desc' => 'Employee attendance logging, monthly payroll generation & payslip PDF downloads.'
                        ],
                        'module_restaurant' => [
                            'label' => 'Restaurant POS & KOT',
                            'type' => 'boolean',
                            'default' => 'false',
                            'desc' => 'Restaurant menu builder, dining tables management & kitchen orders.'
                        ],
                        'digital_qr_menu' => [
                            'label' => 'Digital QR Menu',
                            'type' => 'boolean',
                            'default' => 'false',
                            'desc' => 'Contactless smartphone QR code scanning for customers to view digital food menu.'
                        ],
                        'kitchen_display' => [
                            'label' => 'Kitchen Display System (KDS)',
                            'type' => 'boolean',
                            'default' => 'false',
                            'desc' => 'Real-time kitchen order screen for chefs & kitchen staff to manage KOT tickets.'
                        ],
                        'table_management' => [
                            'label' => 'Table Management',
                            'type' => 'boolean',
                            'default' => 'false',
                            'desc' => 'Dine-in seating management, dynamic QR code generation & printable table sheets.'
                        ],
                        'payment_gateway' => [
                            'label' => 'Razorpay Payment Gateway',
                            'type' => 'boolean',
                            'default' => 'true',
                            'desc' => 'Enables online Razorpay checkout modal for instant plan upgrades & renewals.'
                        ],
                        'barcode_scanning' => [
                            'label' => 'Barcode Scanner Integration',
                            'type' => 'boolean',
                            'default' => 'true',
                            'desc' => 'Enables live barcode scanner camera & hardware reader in inventory/invoicing.'
                        ],
                        'max_employees' => [
                            'label' => 'Max Employees Limit',
                            'type' => 'text',
                            'default' => '50',
                            'desc' => 'Maximum active employees an organization can create (e.g. 1, 5, 50, Unlimited).'
                        ],
                        'max_invoices_per_month' => [
                            'label' => 'Monthly Invoice Limit',
                            'type' => 'text',
                            'default' => 'Unlimited',
                            'desc' => 'Maximum sales invoices generated per calendar month (e.g. 50, 500, Unlimited).'
                        ],
                        'max_locations' => [
                            'label' => 'Multi-Location Limit',
                            'type' => 'text',
                            'default' => '1',
                            'desc' => 'Maximum branch locations an organization can create & manage (e.g. 1, 3, Unlimited).'
                        ],
                        'max_products' => [
                            'label' => 'Max Products Catalog Limit',
                            'type' => 'text',
                            'default' => 'Unlimited',
                            'desc' => 'Maximum inventory products allowed in catalog (e.g. 100, 1000, Unlimited).'
                        ],
                    ];
                    $index = 0;
                @endphp

                @foreach($defaultFeatures as $code => $feature)
                <div class="flex items-start justify-between bg-gray-50/80 p-4 rounded-xl border border-gray-100 gap-4">
                    <div class="w-7/12">
                        <label class="text-sm font-bold text-gray-900 block">{{ $feature['label'] }}</label>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $feature['desc'] }}</p>
                        <input type="hidden" name="features[{{ $index }}][code]" value="{{ $code }}">
                        <p class="text-[10px] font-mono text-gray-400 mt-1">code: {{ $code }}</p>
                    </div>
                    <div class="w-5/12 pt-1">
                        @if($feature['type'] === 'boolean')
                            <select name="features[{{ $index }}][value]" class="w-full border-gray-300 rounded-lg px-3 py-2 text-xs font-bold bg-white">
                                <option value="true" {{ $feature['default'] === 'true' ? 'selected' : '' }}>✓ Enabled (true)</option>
                                <option value="false" {{ $feature['default'] === 'false' ? 'selected' : '' }}>✗ Disabled (false)</option>
                            </select>
                        @else
                            <input type="text" name="features[{{ $index }}][value]" value="{{ $feature['default'] }}" class="w-full border-gray-300 rounded-lg px-3 py-2 text-xs font-bold bg-white">
                        @endif
                    </div>
                </div>
                @php $index++; @endphp
                @endforeach
            </div>
            
            <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-4 mt-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-wider">Custom Feature Code Assistant</h4>
                        <p class="text-xs text-indigo-700 mt-0.5">Need an extra custom limit or flag for this plan? Add it below.</p>
                    </div>
                    <button type="button" onclick="addFeatureRow()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-xs shadow-sm transition">
                        + Add Custom Feature
                    </button>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition">
                    Save Subscription Plan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let featureIndex = {{ $index }};
    function addFeatureRow() {
        const container = document.getElementById('features-container');
        const row = document.createElement('div');
        row.className = 'flex gap-4 items-center mt-3 bg-indigo-50/30 p-3 rounded-xl border border-indigo-200';
        row.innerHTML = `
            <div class="w-1/2">
                <input type="text" name="features[${featureIndex}][code]" placeholder="Feature Code (e.g. max_clients, max_tables)" class="w-full border-gray-300 rounded-lg px-3 py-1.5 text-xs font-bold">
                <p class="text-[10px] text-gray-400 mt-0.5">Codes: max_clients, max_tables, custom_flag</p>
            </div>
            <div class="w-1/2 flex items-center gap-2">
                <input type="text" name="features[${featureIndex}][value]" placeholder="Value (e.g. true, false, 50, Unlimited)" class="w-full border-gray-300 rounded-lg px-3 py-1.5 text-xs font-bold">
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2 text-lg">&times;</button>
            </div>
        `;
        container.appendChild(row);
        featureIndex++;
    }
</script>
@endsection

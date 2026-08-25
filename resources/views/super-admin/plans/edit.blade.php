@extends('layouts.super-admin')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Edit Plan: {{ $plan->name }}</h2>
            <a href="{{ route('super-admin.plans.index') }}" class="text-gray-500 hover:underline">&larr; Back to Plans</a>
        </div>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 shadow sm:rounded-lg">
            <form action="{{ route('super-admin.plans.update', $plan) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h3 class="font-bold text-lg border-b pb-2 mb-4">Plan Details</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name *</label>
                        <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="is_active" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="1" {{ $plan->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$plan->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Price ($) *</label>
                        <input type="number" step="0.01" name="price_monthly" value="{{ old('price_monthly', $plan->price_monthly) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Yearly Price ($) *</label>
                        <input type="number" step="0.01" name="price_yearly" value="{{ old('price_yearly', $plan->price_yearly) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $plan->description) }}</textarea>
                </div>

                <h3 class="font-bold text-lg border-b pb-2 mb-4">Plan Features & Limits</h3>
                
                <div id="features-container" class="space-y-3 mb-4">
                    @php
                        $definedFeatures = $plan->features->keyBy('feature_code');
                        
                        $defaultFeatureCodes = [
                            'max_employees' => ['label' => 'Max Employees', 'placeholder' => 'e.g., 5, 10, unlimited'],
                            'max_invoices_per_month' => ['label' => 'Max Invoices/Month', 'placeholder' => 'e.g., 50, 500, unlimited'],
                            'module_payroll' => ['label' => 'Payroll Module', 'placeholder' => 'true / false'],
                            'module_restaurant' => ['label' => 'Restaurant Module', 'placeholder' => 'true / false'],
                            'barcode_scanning' => ['label' => 'Barcode Scanning', 'placeholder' => 'true / false'],
                        ];
                        
                        $index = 0;
                    @endphp

                    @foreach($defaultFeatureCodes as $code => $meta)
                        <div class="flex gap-4 items-center">
                            <input type="text" name="features[{{ $index }}][code]" value="{{ $code }}" class="w-1/2 border-gray-300 rounded-md shadow-sm bg-gray-50" readonly>
                            <input type="text" name="features[{{ $index }}][value]" value="{{ isset($definedFeatures[$code]) ? $definedFeatures[$code]->feature_value : '' }}" placeholder="{{ $meta['placeholder'] }}" class="w-1/2 border-gray-300 rounded-md shadow-sm">
                        </div>
                        @php $index++; @endphp
                    @endforeach
                    
                    @foreach($definedFeatures as $code => $feature)
                        @if(!array_key_exists($code, $defaultFeatureCodes))
                            <div class="flex gap-4 items-center mt-3">
                                <input type="text" name="features[{{ $index }}][code]" value="{{ $code }}" class="w-1/2 border-gray-300 rounded-md shadow-sm">
                                <input type="text" name="features[{{ $index }}][value]" value="{{ $feature->feature_value }}" class="w-1/2 border-gray-300 rounded-md shadow-sm">
                                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 font-bold">&times;</button>
                            </div>
                            @php $index++; @endphp
                        @endif
                    @endforeach
                </div>
                
                <button type="button" onclick="addFeatureRow()" class="text-sm text-blue-600 hover:underline mb-6">+ Add Custom Feature Code</button>

                <div>
                    <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-md hover:bg-gray-700">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let featureIndex = {{ $index }};
    function addFeatureRow() {
        const container = document.getElementById('features-container');
        const row = document.createElement('div');
        row.className = 'flex gap-4 items-center mt-3';
        row.innerHTML = `
            <input type="text" name="features[${featureIndex}][code]" placeholder="Feature Code" class="w-1/2 border-gray-300 rounded-md shadow-sm">
            <input type="text" name="features[${featureIndex}][value]" placeholder="Value" class="w-1/2 border-gray-300 rounded-md shadow-sm">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 font-bold">&times;</button>
        `;
        container.appendChild(row);
        featureIndex++;
    }
</script>
@endsection

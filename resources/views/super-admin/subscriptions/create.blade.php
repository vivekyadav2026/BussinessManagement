@extends('layouts.super-admin')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Add New Subscription</h2>
        <a href="{{ route('super-admin.subscriptions.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('super-admin.subscriptions.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                <select name="organization_id" required class="w-full border border-gray-300 rounded px-3 py-2 outline-none">
                    <option value="">Select Organization</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }} (ID: {{ $org->id }})</option>
                    @endforeach
                </select>
                @error('organization_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Subscription Plan</label>
                <select name="plan_id" required class="w-full border border-gray-300 rounded px-3 py-2 outline-none">
                    <option value="">Select Plan</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }} - ${{ number_format($plan->price_monthly, 2) }}/mo</option>
                    @endforeach
                </select>
                @error('plan_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" required class="w-full border border-gray-300 rounded px-3 py-2 outline-none">
                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Trial" {{ old('status') == 'Trial' ? 'selected' : '' }}>Trial</option>
                    <option value="Expired" {{ old('status') == 'Expired' ? 'selected' : '' }}>Expired</option>
                    <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                    <input type="date" name="starts_at" required value="{{ old('starts_at', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded px-3 py-2 outline-none">
                    @error('starts_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ends At (Optional)</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="w-full border border-gray-300 rounded px-3 py-2 outline-none">
                    @error('ends_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t pt-4">
                <a href="{{ route('super-admin.subscriptions.index') }}" class="btn border px-4 py-2 rounded">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-semibold shadow">Create Subscription</button>
            </div>
        </form>
    </div>
</div>
@endsection

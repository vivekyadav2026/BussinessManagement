@extends('layouts.super-admin')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('super-admin.subscriptions.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Subscription</h1>
        <p class="text-gray-500 mt-1">Manage subscription details for {{ optional($subscription->organization)->name }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl">
    <form action="{{ route('super-admin.subscriptions.update', $subscription) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                <input type="text" value="{{ optional($subscription->organization)->name }} (ID: {{ $subscription->organization_id }})" disabled class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-gray-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                <select name="plan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm @error('plan_id') border-red-300 @enderror">
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ old('plan_id', $subscription->plan_id) == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
                @error('plan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm @error('status') border-red-300 @enderror">
                    <option value="Active" {{ old('status', $subscription->status) === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Trial" {{ old('status', $subscription->status) === 'Trial' ? 'selected' : '' }}>Trial</option>
                    <option value="Expired" {{ old('status', $subscription->status) === 'Expired' ? 'selected' : '' }}>Expired</option>
                    <option value="Cancelled" {{ old('status', $subscription->status) === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="starts_at" value="{{ old('starts_at', $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm @error('starts_at') border-red-300 @enderror">
                @error('starts_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date (Leave blank for lifetime)</label>
                <input type="date" name="ends_at" value="{{ old('ends_at', $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm @error('ends_at') border-red-300 @enderror">
                @error('ends_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        
        <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('super-admin.subscriptions.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-medium text-sm hover:bg-indigo-700 transition shadow-sm">Save Changes</button>
        </div>
    </form>
</div>
@endsection

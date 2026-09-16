@extends('layouts.super-admin')

@section('title', 'Edit Subscription')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center gap-3 border-b border-slate-200 pb-5">
        <a href="{{ route('super-admin.subscriptions.index') }}" class="p-2.5 rounded-lg border border-slate-300 bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-2xs transition" title="Back to Subscriptions">
            &larr;
        </a>
        <div>
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-1" aria-label="Breadcrumb">
                <a href="{{ route('super-admin.subscriptions.index') }}" class="hover:text-slate-900 transition-colors">Subscriptions</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Edit #{{ $subscription->id }}</span>
            </nav>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Edit Tenant Subscription</h1>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6">
        <form action="{{ route('super-admin.subscriptions.update', $subscription) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Organization Tenant</label>
                <input type="text" value="{{ optional($subscription->organization)->name }} (Org ID: #{{ $subscription->organization_id }})" disabled 
                       class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3.5 py-2.5 text-xs font-extrabold text-slate-700 cursor-not-allowed">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Plan Tier</label>
                    <select name="plan_id" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-amber-500 bg-slate-50/50">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $subscription->plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} (₹{{ number_format($plan->price_monthly, 0) }}/mo)
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id') <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-amber-500 bg-slate-50/50">
                        <option value="Active" {{ old('status', $subscription->status) === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Trial" {{ old('status', $subscription->status) === 'Trial' ? 'selected' : '' }}>Trial</option>
                        <option value="Expired" {{ old('status', $subscription->status) === 'Expired' ? 'selected' : '' }}>Expired</option>
                        <option value="Cancelled" {{ old('status', $subscription->status) === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status') <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Start Date</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : '') }}" class="w-full border border-slate-300 rounded-lg px-3.5 py-2 text-xs font-semibold text-slate-900 focus:border-amber-500">
                    @error('starts_at') <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">End Date (Blank for lifetime)</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at', $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : '') }}" class="w-full border border-slate-300 rounded-lg px-3.5 py-2 text-xs font-semibold text-slate-900 focus:border-amber-500">
                    @error('ends_at') <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('super-admin.subscriptions.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

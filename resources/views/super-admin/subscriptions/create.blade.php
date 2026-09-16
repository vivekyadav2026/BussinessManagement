@extends('layouts.super-admin')

@section('title', 'Add Subscription')

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
                <span class="text-slate-950 font-extrabold">New Subscription</span>
            </nav>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Assign Tenant Subscription</h1>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6">
        <form action="{{ route('super-admin.subscriptions.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Organization Tenant</label>
                <select name="organization_id" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-slate-50/50">
                    <option value="">Select Organization</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }} (Org ID: #{{ $org->id }})</option>
                    @endforeach
                </select>
                @error('organization_id') <span class="text-rose-600 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Subscription Plan Tier</label>
                    <select name="plan_id" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-slate-50/50">
                        <option value="">Select Plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} (₹{{ number_format($plan->price_monthly, 0) }}/mo)
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id') <span class="text-rose-600 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Subscription Status</label>
                    <select name="status" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-slate-50/50">
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active (Paid / Production)</option>
                        <option value="Trial" {{ old('status') == 'Trial' ? 'selected' : '' }}>Trial (Evaluation)</option>
                        <option value="Expired" {{ old('status') == 'Expired' ? 'selected' : '' }}>Expired (Locked)</option>
                        <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status') <span class="text-rose-600 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Starts At</label>
                    <input type="date" name="starts_at" required value="{{ old('starts_at', date('Y-m-d')) }}" class="w-full border border-slate-300 rounded-lg px-3.5 py-2 text-xs font-semibold text-slate-900 focus:border-amber-500">
                    @error('starts_at') <span class="text-rose-600 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Ends At (Leave blank for Lifetime)</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="w-full border border-slate-300 rounded-lg px-3.5 py-2 text-xs font-semibold text-slate-900 focus:border-amber-500">
                    @error('ends_at') <span class="text-rose-600 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('super-admin.subscriptions.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    Create &amp; Assign Subscription
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

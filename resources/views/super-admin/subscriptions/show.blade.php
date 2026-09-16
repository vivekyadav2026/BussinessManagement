@extends('layouts.super-admin')

@section('title', 'Subscription Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.subscriptions.index') }}" class="p-2.5 rounded-lg border border-slate-300 bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-2xs transition" title="Back to Subscriptions">
                &larr;
            </a>
            <div>
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-1" aria-label="Breadcrumb">
                    <a href="{{ route('super-admin.subscriptions.index') }}" class="hover:text-slate-900 transition-colors">Subscriptions</a>
                    <span class="text-slate-400 font-bold">/</span>
                    <span class="text-slate-950 font-extrabold">Record #{{ $subscription->id }}</span>
                </nav>
                <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Subscription Details</h1>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('super-admin.subscriptions.edit', $subscription->id) }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                Edit Record
            </a>
        </div>
    </div>

    <!-- Main Information Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tenant & Plan Card -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-2xs space-y-4">
            <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider border-b border-slate-100 pb-2.5">Tenant &amp; Plan Profile</h3>
            
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-500 font-bold uppercase text-[10px] block mb-0.5">Organization Tenant</span>
                    <span class="text-base font-black text-slate-950">{{ optional($subscription->organization)->name ?: 'N/A' }}</span>
                    <span class="text-slate-500 font-mono text-[11px] block mt-0.5">Organization ID: #{{ $subscription->organization_id }}</span>
                </div>

                <div>
                    <span class="text-slate-500 font-bold uppercase text-[10px] block mb-0.5">Assigned Plan</span>
                    <span class="text-sm font-extrabold text-indigo-700">{{ optional($subscription->plan)->name ?: 'N/A' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 font-bold uppercase text-[10px] block mb-1">Status</span>
                    <div>
                        @if($subscription->status === 'Active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-300">Active</span>
                        @elseif($subscription->status === 'Trial')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-50 text-amber-800 border border-amber-300">Trial</span>
                        @elseif($subscription->status === 'Expired')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-rose-50 text-rose-800 border border-rose-300">Expired</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-slate-100 text-slate-800 border border-slate-300">{{ $subscription->status }}</span>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-500 font-bold uppercase text-[10px] block">Plan Pricing Tier</span>
                    <div class="text-xs font-bold text-slate-800 mt-1">
                        Monthly: <b class="text-slate-900 font-mono text-sm">₹{{ number_format(optional($subscription->plan)->price_monthly ?? 0, 2) }}</b> | 
                        Yearly: <b class="text-slate-900 font-mono text-sm">₹{{ number_format(optional($subscription->plan)->price_yearly ?? 0, 2) }}</b>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline & Validity Card -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-2xs space-y-4">
            <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider border-b border-slate-100 pb-2.5">Timeline &amp; Access Dates</h3>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-500 font-bold uppercase text-[10px] block mb-0.5">Subscription Starts At</span>
                    <span class="text-sm font-extrabold text-slate-900 font-mono">{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : '-' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 font-bold uppercase text-[10px] block mb-0.5">Subscription Ends At</span>
                    <span class="text-sm font-extrabold text-slate-900 font-mono">{{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'Lifetime Continuous Access' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 font-bold uppercase text-[10px] block mb-0.5">Created Timestamp</span>
                    <span class="text-xs font-semibold text-slate-600 font-mono">{{ $subscription->created_at->format('M d, Y H:i:s') }}</span>
                </div>

                <div>
                    <span class="text-slate-500 font-bold uppercase text-[10px] block mb-0.5">Last Updated</span>
                    <span class="text-xs font-semibold text-slate-600 font-mono">{{ $subscription->updated_at->format('M d, Y H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone Card -->
    <div class="bg-rose-50/70 rounded-xl border border-rose-200 p-5 space-y-3">
        <h3 class="font-black text-xs text-rose-900 uppercase tracking-wider">Danger Zone</h3>
        <p class="text-xs text-rose-700 font-medium leading-relaxed">Deleting this subscription record will immediately lock this organization tenant out of features associated with this plan.</p>
        
        <form action="{{ route('super-admin.subscriptions.destroy', $subscription->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this subscription record?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                Delete Subscription Record
            </button>
        </form>
    </div>
</div>
@endsection

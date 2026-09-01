@extends('layouts.super-admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="dash-head flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.subscriptions.index') }}" class="p-2.5 rounded-xl border border-gray-200 bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-xs transition" title="Back to Subscriptions">
                &larr;
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Subscription Record #{{ $subscription->id }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Billing & tenant access overview for <b>{{ optional($subscription->organization)->name ?: 'N/A' }}</b>.</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.subscriptions.edit', $subscription->id) }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                Edit Subscription
            </a>
        </div>
    </div>

    <!-- Main Information Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tenant & Plan Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-gray-100 pb-3">Tenant & Plan Overview</h3>
            
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Business Tenant Name</span>
                    <span class="text-base font-extrabold text-slate-900">{{ optional($subscription->organization)->name ?: 'N/A' }}</span>
                    <span class="text-slate-400 font-mono text-[11px] block mt-0.5">Organization ID: #{{ $subscription->organization_id }}</span>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Assigned Subscription Plan</span>
                    <span class="text-sm font-extrabold text-indigo-700">{{ optional($subscription->plan)->name ?: 'N/A' }}</span>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block mb-1">Status</span>
                    <div>
                        @if($subscription->status === 'Active')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">Active</span>
                        @elseif($subscription->status === 'Trial')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-300">Trial</span>
                        @elseif($subscription->status === 'Expired')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300">Expired</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-800 border border-slate-300">{{ $subscription->status }}</span>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Plan Pricing Tier (Rupees)</span>
                    <div class="text-xs font-bold text-slate-800 mt-1">
                        Monthly: <b class="text-slate-900 font-mono text-sm">₹{{ number_format(optional($subscription->plan)->price_monthly ?? 0, 2) }}</b> | 
                        Yearly: <b class="text-slate-900 font-mono text-sm">₹{{ number_format(optional($subscription->plan)->price_yearly ?? 0, 2) }}</b>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline & Validity Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-gray-100 pb-3">Subscription Timeline & Access</h3>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Subscription Start Date</span>
                    <span class="text-sm font-bold text-slate-900 font-mono">{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : '-' }}</span>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Subscription End Date</span>
                    <span class="text-sm font-bold text-slate-900 font-mono">{{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'Continuous / Unlimited Lifetime' }}</span>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Created Date</span>
                    <span class="text-xs font-medium text-slate-600 font-mono">{{ $subscription->created_at->format('M d, Y H:i:s') }}</span>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Last Updated Date</span>
                    <span class="text-xs font-medium text-slate-600 font-mono">{{ $subscription->updated_at->format('M d, Y H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone Card -->
    <div class="bg-rose-50/60 rounded-2xl border border-rose-200 p-6 shadow-xs space-y-3">
        <h3 class="font-extrabold text-sm text-rose-900 uppercase tracking-wider">Danger Zone</h3>
        <p class="text-xs text-rose-700 font-medium">Deleting this subscription record will immediately lock this organization tenant out of features associated with this plan.</p>
        
        <form action="{{ route('super-admin.subscriptions.destroy', $subscription->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this subscription?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                Delete Subscription Record
            </button>
        </form>
    </div>
</div>
@endsection

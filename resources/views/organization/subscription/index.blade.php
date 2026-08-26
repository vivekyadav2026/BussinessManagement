@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Subscription & Billing</h1>
        <p class="text-gray-500 mt-1">Manage your organization's active plan, limits, and billing cycles.</p>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<div class="panel p-6 shadow-sm mb-8">
    <h2 class="text-lg font-bold text-gray-900 mb-2">Current Plan: {{ $currentSubscription ? $currentSubscription->plan->name : 'No Active Plan' }}</h2>
    @if($currentSubscription)
        <div class="text-sm text-gray-600 mb-4">
            Valid until: <span class="font-bold text-gray-800 font-mono">{{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('M d, Y') : 'Lifetime' }}</span>
            <span class="ml-4 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">Active</span>
        </div>

        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 mt-6 border-b border-gray-100 pb-2">Your Limits & Usage</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($currentSubscription->plan->features as $feature)
                <div class="bg-gray-50/70 border border-gray-100 p-3.5 rounded-xl flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-700">{{ ucwords(str_replace('_', ' ', $feature->feature_code)) }}</span>
                    <span class="text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 px-2.5 py-0.5 rounded-full font-mono">{{ $feature->feature_value }}</span>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-red-500 text-sm">Your organization does not have an active subscription. Please select a plan below to avoid service interruption.</p>
    @endif
</div>

<h2 class="text-xl font-bold text-gray-900 mb-4">Available Plans</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($plans as $plan)
        <div class="panel p-6 shadow-sm flex flex-col {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'ring-2 ring-indigo-600 border-indigo-600' : '' }}">
            @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2">Current Plan</div>
            @endif
            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
            <div class="mt-2 mb-4">
                <span class="text-3xl font-black text-gray-900 font-mono">₹{{ number_format($plan->price_monthly, 2) }}</span>
                <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">/mo</span>
            </div>
            <p class="text-sm text-gray-600 mb-6 flex-grow leading-relaxed">{{ $plan->description }}</p>

            <ul class="space-y-2.5 mb-6">
                @foreach($plan->features as $feature)
                    <li class="flex items-center text-xs text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-medium">{{ ucwords(str_replace('_', ' ', $feature->feature_code)) }}:</span> <strong class="ml-1 text-gray-800 font-mono">{{ $feature->feature_value }}</strong>
                    </li>
                @endforeach
            </ul>

            @if(!$currentSubscription || $currentSubscription->plan_id != $plan->id)
                <form action="{{ route('organization.subscription.switch') }}" method="POST" onsubmit="return confirm('Switching to this plan will terminate your current billing cycle. Proceed?');">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <button type="submit" class="w-full btn btn-gold py-2.5 justify-center">Switch to {{ $plan->name }}</button>
                </form>
            @else
                <button disabled class="w-full btn btn-ghost py-2.5 justify-center cursor-not-allowed opacity-50">Active Plan</button>
            @endif
        </div>
    @endforeach
</div>
@endsection

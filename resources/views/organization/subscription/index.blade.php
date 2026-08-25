@extends('layouts.sme')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Subscription & Billing</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow border p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-2">Current Plan: {{ $currentSubscription ? $currentSubscription->plan->name : 'No Active Plan' }}</h2>
        @if($currentSubscription)
            <div class="text-sm text-gray-600 mb-4">
                Valid until: <span class="font-medium text-gray-800">{{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('M d, Y') : 'Lifetime' }}</span>
                <span class="ml-4 px-2 py-1 rounded text-xs bg-green-100 text-green-700">Active</span>
            </div>

            <h3 class="text-md font-semibold text-gray-700 mb-3 mt-6 border-b pb-2">Your Limits & Usage</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($currentSubscription->plan->features as $feature)
                    <div class="bg-gray-50 border p-3 rounded flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $feature->feature_code)) }}</span>
                        <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded font-bold">{{ $feature->feature_value }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-red-500 text-sm">Your organization does not have an active subscription. Please select a plan below to avoid service interruption.</p>
        @endif
    </div>

    <h2 class="text-xl font-bold text-gray-800 mb-4">Available Plans</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="bg-white rounded-lg shadow border p-6 flex flex-col {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'ring-2 ring-blue-500' : '' }}">
                @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                    <div class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Current Plan</div>
                @endif
                <h3 class="text-xl font-bold text-gray-800">{{ $plan->name }}</h3>
                <div class="mt-2 mb-4">
                    <span class="text-3xl font-black">${{ number_format($plan->price_monthly, 2) }}</span>
                    <span class="text-gray-500">/mo</span>
                </div>
                <p class="text-sm text-gray-600 mb-6 flex-grow">{{ $plan->description }}</p>

                <ul class="space-y-2 mb-6">
                    @foreach($plan->features as $feature)
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ ucwords(str_replace('_', ' ', $feature->feature_code)) }}: <strong class="ml-1">{{ $feature->feature_value }}</strong>
                        </li>
                    @endforeach
                </ul>

                @if(!$currentSubscription || $currentSubscription->plan_id != $plan->id)
                    <form action="{{ route('organization.subscription.switch') }}" method="POST" onsubmit="return confirm('Switching to this plan will terminate your current billing cycle. Proceed?');">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-medium">Switch to {{ $plan->name }}</button>
                    </form>
                @else
                    <button disabled class="w-full bg-gray-200 text-gray-500 py-2 rounded font-medium cursor-not-allowed">Active</button>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection

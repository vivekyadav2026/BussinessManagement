@extends('layouts.super-admin')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Platform & Trial Settings</h1>
        <p class="text-sm text-gray-500">Configure global SaaS registration rules, trial periods, and subscription controls.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('super-admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="flex items-center justify-between pb-6 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Enable Free Trial for New Registrations</h3>
                    <p class="text-xs text-gray-500 mt-1">If disabled, new users will be forced to buy a subscription plan immediately upon sign up.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enable_free_trial" value="1" {{ $enableTrial == '1' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <div class="space-y-2 max-w-md">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Free Trial Duration (Days)</label>
                <div class="flex items-center gap-3">
                    <input type="number" name="trial_days" value="{{ old('trial_days', $trialDays) }}" min="0" max="365" required class="w-32 border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <span class="text-sm font-semibold text-gray-500">Days</span>
                </div>
                <p class="text-xs text-gray-400">Common values: <code>0</code> (No trial / Instant Paywall), <code>7</code>, <code>14</code>, <code>30</code> days.</p>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition">
                    Save System Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

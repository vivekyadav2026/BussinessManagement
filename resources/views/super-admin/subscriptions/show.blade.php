@extends('layouts.super-admin')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Subscription Details</h2>
        <div class="flex gap-2">
            <a href="{{ route('super-admin.subscriptions.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 text-sm font-semibold">Back</a>
            <a href="{{ route('super-admin.subscriptions.edit', $subscription->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-semibold shadow">Edit</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Main Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase">Organization</label>
                <div class="mt-1 text-sm font-medium text-gray-900">{{ optional($subscription->organization)->name ?: 'N/A' }} (ID: {{ $subscription->organization_id }})</div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase">Subscription Plan</label>
                <div class="mt-1 text-sm font-medium text-gray-900">{{ optional($subscription->plan)->name ?: 'N/A' }} (ID: {{ $subscription->plan_id }})</div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase">Status</label>
                <div class="mt-1 text-sm font-medium text-gray-900">
                    @if($subscription->status === 'Active')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                    @elseif($subscription->status === 'Trial')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Trial</span>
                    @elseif($subscription->status === 'Expired')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $subscription->status }}</span>
                    @endif
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase">Pricing Tier</label>
                <div class="mt-1 text-sm font-medium text-gray-900">
                    Monthly: ${{ number_format(optional($subscription->plan)->price_monthly ?? 0, 2) }} / 
                    Yearly: ${{ number_format(optional($subscription->plan)->price_yearly ?? 0, 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Timeline</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase">Starts At</label>
                <div class="mt-1 text-sm text-gray-900 font-medium">{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : '-' }}</div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase">Ends At</label>
                <div class="mt-1 text-sm text-gray-900 font-medium">{{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'Lifetime / Continuous' }}</div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase">Created Date</label>
                <div class="mt-1 text-sm text-gray-900 font-medium">{{ $subscription->created_at->format('M d, Y H:i:s') }}</div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase">Last Updated</label>
                <div class="mt-1 text-sm text-gray-900 font-medium">{{ $subscription->updated_at->format('M d, Y H:i:s') }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-red-750 mb-4">Danger Zone</h3>
        <p class="text-gray-500 text-sm mb-4">Deleting this subscription record will immediately lock this organization out of features associated with this plan.</p>
        <form action="{{ route('super-admin.subscriptions.destroy', $subscription->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this subscription?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 font-semibold text-sm shadow">
                Delete Subscription
            </button>
        </form>
    </div>
</div>
@endsection

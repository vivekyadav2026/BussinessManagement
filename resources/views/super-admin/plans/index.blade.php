@extends('layouts.super-admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Manage Subscription Plans</h2>
            <a href="{{ route('super-admin.plans.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Add Plan</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 border-b">Name</th>
                            <th class="p-3 border-b">Monthly Price</th>
                            <th class="p-3 border-b">Yearly Price</th>
                            <th class="p-3 border-b">Features</th>
                            <th class="p-3 border-b">Status</th>
                            <th class="p-3 border-b text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 font-medium">{{ $plan->name }}</td>
                                <td class="p-3">${{ number_format($plan->price_monthly, 2) }}</td>
                                <td class="p-3">${{ number_format($plan->price_yearly, 2) }}</td>
                                <td class="p-3 text-xs text-gray-500">
                                    {{ $plan->features->count() }} features defined
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="p-3 text-right flex justify-end gap-3 items-center">
                                    <a href="{{ route('super-admin.plans.show', $plan) }}" class="text-green-600 hover:underline">View</a>
                                    <a href="{{ route('super-admin.plans.edit', $plan) }}" class="text-blue-600 hover:underline">Edit</a>
                                    @if($plan->subscriptions()->count() === 0)
                                        <form action="{{ route('super-admin.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?');" style="display:inline-block; margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

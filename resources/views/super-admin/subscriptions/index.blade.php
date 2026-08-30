@extends('layouts.super-admin')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Subscriptions</h1>
        <p class="text-gray-500 mt-1">Manage tenant billing plans and active subscriptions.</p>
    </div>
    <a href="{{ route('super-admin.subscriptions.create') }}" class="bg-blue-655 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-semibold text-sm">Add Subscription</a>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-white text-xs uppercase text-gray-400 font-semibold border-b border-gray-100 tracking-wider">
                <tr>
                    <th class="px-6 py-4">Organization</th>
                    <th class="px-6 py-4">Plan</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Starts At</th>
                    <th class="px-6 py-4">Ends At</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($subscriptions as $sub)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ optional($sub->organization)->name }}</div>
                        <div class="text-xs text-gray-500 mono mt-1">Org ID: {{ $sub->organization_id }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ optional($sub->plan)->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($sub->status === 'Active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @elseif($sub->status === 'Trial')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Trial</span>
                        @elseif($sub->status === 'Expired')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $sub->status }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $sub->starts_at ? $sub->starts_at->format('M d, Y') : '-' }}</td>
                    <td class="px-6 py-4">{{ $sub->ends_at ? $sub->ends_at->format('M d, Y') : 'Lifetime' }}</td>
                    <td class="px-6 py-4 text-right flex justify-end gap-3 items-center">
                        <a href="{{ route('super-admin.subscriptions.show', $sub->id) }}" class="text-green-600 hover:text-green-900 font-medium text-xs">View</a>
                        <a href="{{ route('super-admin.subscriptions.edit', $sub->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Edit</a>
                        <form action="{{ route('super-admin.subscriptions.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subscription?');" style="display:inline-block; margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No subscriptions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-100 bg-white">
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection

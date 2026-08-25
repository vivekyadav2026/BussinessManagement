@extends('layouts.super-admin')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <a href="{{ route('super-admin.organizations.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Organizations</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $organization->name }}</h1>
        <div class="flex items-center gap-3 mt-2">
            <span class="text-sm text-gray-500 mono">ID: {{ $organization->id }}</span>
            @if($organization->is_active)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
            @endif
        </div>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('super-admin.organizations.edit', $organization) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-50 transition">Edit Details</a>
        <form action="{{ route('super-admin.organizations.toggle-status', $organization) }}" method="POST" onsubmit="return confirm('Are you sure you want to change this organization\'s status?');">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-4 py-2 rounded-lg font-medium text-sm transition {{ $organization->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100 border border-red-200' : 'bg-green-50 text-green-600 hover:bg-green-100 border border-green-200' }}">
                {{ $organization->is_active ? 'Deactivate Tenant' : 'Activate Tenant' }}
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-bold mb-4 border-b pb-2">Tenant Details</h3>
            <div class="space-y-4">
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Email</div>
                    <div class="text-sm font-medium mt-1">{{ $organization->email ?? 'Not provided' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Phone</div>
                    <div class="text-sm font-medium mt-1">{{ $organization->phone ?? 'Not provided' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">GST Number</div>
                    <div class="text-sm font-medium mt-1">{{ $organization->gst_number ?? 'Not provided' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Joined Platform</div>
                    <div class="text-sm font-medium mt-1">{{ $organization->created_at->format('F d, Y h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold">Associated Users</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-white text-xs uppercase text-gray-400 font-semibold border-b border-gray-100 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Roles</th>
                            <th class="px-6 py-4">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No users found in this organization.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

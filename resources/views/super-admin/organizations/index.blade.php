@extends('layouts.super-admin')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Organizations</h1>
        <p class="text-gray-500 mt-1">Manage all tenant businesses on the platform.</p>
    </div>
    <a href="{{ route('super-admin.organizations.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium text-sm hover:bg-indigo-700 transition">+ New Organization</a>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <form method="GET" action="{{ route('super-admin.organizations.index') }}" class="flex gap-4 w-full max-w-2xl">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full md:w-64 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            
            <button type="submit" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-50 transition">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('super-admin.organizations.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm flex items-center">Clear</a>
            @endif
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-white text-xs uppercase text-gray-400 font-semibold border-b border-gray-100 tracking-wider">
                <tr>
                    <th class="px-6 py-4">Organization</th>
                    <th class="px-6 py-4">Contact</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Joined</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($organizations as $org)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ $org->name }}</div>
                        <div class="text-xs text-gray-500 mono mt-1">ID: {{ $org->id }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div>{{ $org->email ?? '—' }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $org->phone ?? '—' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($org->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $org->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('super-admin.organizations.show', $org) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">View</a>
                            <a href="{{ route('super-admin.organizations.edit', $org) }}" class="text-gray-500 hover:text-gray-900 font-medium text-xs">Edit</a>
                            
                            <form action="{{ route('super-admin.organizations.toggle-status', $org) }}" method="POST" onsubmit="return confirm('Are you sure you want to change this organization\'s status?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs font-medium {{ $org->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                                    {{ $org->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        No organizations found matching your criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-100 bg-white">
        {{ $organizations->links() }}
    </div>
</div>
@endsection

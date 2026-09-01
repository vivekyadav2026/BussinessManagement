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
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('super-admin.organizations.show', $org) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Organization Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('super-admin.organizations.edit', $org) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit Organization">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('super-admin.organizations.toggle-status', $org) }}" method="POST" onsubmit="return confirm('Toggle organization status?');" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="p-1.5 {{ $org->is_active ? 'text-gray-500 hover:text-amber-600 hover:bg-amber-50' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition" title="{{ $org->is_active ? 'Deactivate Organization' : 'Activate Organization' }}">
                                    @if($org->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
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

@extends('layouts.sme')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-2 space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-3 border-b border-gray-150">
        <div>
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Roles & Permissions</h1>
            <p class="text-xs text-gray-500">Control access levels and modules permission configuration for your business staff.</p>
        </div>
        <a href="{{ route('organization.roles.create') }}" class="px-3.5 py-1.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-lg font-semibold text-xs hover:opacity-90 shadow-sm transition">+ New Role</a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2.5 rounded-lg text-xs font-semibold shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-200 tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Role Name</th>
                        <th class="px-5 py-3">Assigned Users</th>
                        <th class="px-5 py-3">Date Created</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($roles as $role)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3.5">
                            <div class="font-bold text-gray-900 text-xs">{{ $role->name }}</div>
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-gray-700">
                            {{ $role->users_count }} {{ $role->users_count == 1 ? 'user' : 'users' }}
                        </td>
                        <td class="px-5 py-3.5 text-gray-400 font-medium">
                            {{ $role->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex justify-end gap-1 items-center">
                                <a href="{{ route('organization.roles.show', $role) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Role Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($role->name !== 'Organization Admin')
                                    <a href="{{ route('organization.roles.edit', $role) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit Role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if($role->users_count === 0)
                                        <form action="{{ route('organization.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete Role">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-55 text-gray-500 border border-gray-200">
                                        System Admin (Full Access)
                                    </span>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400 font-medium">No custom roles created yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

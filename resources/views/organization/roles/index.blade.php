@extends('layouts.sme')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Roles & Permissions</h1>
            <p class="text-sm text-gray-500 mt-0.5">Control access levels and modules permission configuration for your business staff.</p>
        </div>
        <a href="{{ route('organization.roles.create') }}" class="px-4 py-2.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl font-semibold text-sm hover:opacity-95 shadow-sm transition">+ New Role</a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Role Name</th>
                        <th class="px-6 py-4">Assigned Users</th>
                        <th class="px-6 py-4">Date Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($roles as $role)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $role->name }}</div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-700">
                            {{ $role->users_count }} {{ $role->users_count == 1 ? 'user' : 'users' }}
                        </td>
                        <td class="px-6 py-4 text-gray-400 font-medium">
                            {{ $role->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($role->name !== 'Organization Admin')
                                <a href="{{ route('organization.roles.edit', $role) }}" class="text-indigo-650 hover:text-indigo-900 font-bold text-xs">Edit Permissions</a>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-50 text-gray-500 border border-gray-150">
                                    System Admin (Full Access)
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-10 text-gray-400 font-medium">No custom roles created yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

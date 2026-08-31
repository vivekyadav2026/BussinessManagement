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
                            <div class="flex justify-end gap-3.5 items-center">
                                <a href="{{ route('organization.roles.show', $role) }}" class="text-green-600 hover:text-green-800 font-bold">View</a>
                                @if($role->name !== 'Organization Admin')
                                    <a href="{{ route('organization.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-800 font-bold">Edit</a>
                                    @if($role->users_count === 0)
                                        <form action="{{ route('organization.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');" class="inline-block m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-bold">Delete</button>
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

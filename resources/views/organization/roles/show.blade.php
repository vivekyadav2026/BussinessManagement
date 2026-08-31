@extends('layouts.sme')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-2 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-150">
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.roles.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-lg font-bold text-gray-900 tracking-tight">Role Details</h1>
                <p class="text-xs text-gray-500">Role name: <span class="font-semibold text-gray-800">{{ $role->name }}</span></p>
            </div>
        </div>
        <div class="flex gap-2">
            <a class="px-3.5 py-1.5 border border-gray-300 text-gray-700 bg-white rounded-lg font-semibold text-xs hover:bg-gray-50 shadow-sm transition" href="{{ route('organization.roles.index') }}">Back to Roles</a>
            <a class="px-3.5 py-1.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-lg font-semibold text-xs hover:opacity-90 shadow-sm transition" href="{{ route('organization.roles.edit', $role) }}">Edit Role</a>
        </div>
    </div>

    <!-- Grid Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left panel: Permissions Assigned -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                    <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                    <h2 class="text-sm font-semibold text-gray-800">Permissions Assigned</h2>
                </div>
                
                @php
                    $groupedPermissions = $role->permissions->groupBy('module');
                @endphp
                
                <div class="space-y-4">
                    @forelse($groupedPermissions as $module => $permissions)
                        <div class="space-y-2">
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $module }}</h4>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($permissions as $permission)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-medium bg-indigo-50 text-indigo-750 border border-indigo-100/80">
                                        {{ str_replace('_', ' ', $permission->name) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-xs py-2">No permissions assigned to this role.</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Right panel: Summary -->
        <div>
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                    <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                    <h2 class="text-sm font-semibold text-gray-800">Summary</h2>
                </div>
                
                <div class="space-y-3.5">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Role Name</label>
                        <div class="text-sm font-semibold text-gray-900 mt-0.5">{{ $role->name }}</div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Users Assigned</label>
                        <div class="text-sm font-semibold text-gray-900 mt-0.5">{{ $role->users()->count() }} users</div>
                    </div>
                </div>
                
                @if($role->users()->count() === 0)
                    <div class="pt-4 border-t border-gray-100">
                        <form action="{{ route('organization.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-center px-4 py-2 border border-red-300 text-xs font-semibold rounded-lg text-red-650 bg-white hover:bg-red-50 focus:outline-none transition shadow-sm">
                                Delete Role
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

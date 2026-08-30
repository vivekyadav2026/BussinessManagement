@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Role Details</h1>
      <p class="text-gray-500 mt-1">Role name: {{ $role->name }}</p>
  </div>
  <div class="flex gap-2">
      <a class="btn bg-gray-100 text-gray-700 btn-sm" href="{{ route('organization.roles.index') }}">Back to Roles</a>
      <a class="btn btn-gold btn-sm" href="{{ route('organization.roles.edit', $role) }}">Edit Role</a>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="panel mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Permissions Assigned</h3>
            @php
                $groupedPermissions = $role->permissions->groupBy('module');
            @endphp
            
            @forelse($groupedPermissions as $module => $permissions)
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">{{ $module }}</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($permissions as $permission)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-800">
                                {{ str_replace('_', ' ', $permission->name) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No permissions assigned to this role.</p>
            @endforelse
        </div>
    </div>
    
    <div>
        <div class="panel">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Summary</h3>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 uppercase">Role Name</label>
                <div class="text-sm font-bold text-gray-900 mt-1">{{ $role->name }}</div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 uppercase">Users Assigned</label>
                <div class="text-sm font-bold text-gray-900 mt-1">{{ $role->users()->count() }} users</div>
            </div>
            
            @if($role->users()->count() === 0)
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <form action="{{ route('organization.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none">
                            Delete Role
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

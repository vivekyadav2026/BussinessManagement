@extends('layouts.sme')

@section('content')
<div class="dash-head mb-6">
  <a href="{{ route('organization.roles.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Roles</a>
  <h1 class="text-2xl font-bold text-gray-900">Create New Role</h1>
</div>

<form action="{{ route('organization.roles.store') }}" method="POST">
    @csrf
    
    <div class="panel mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Role Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Store Manager, Cashier" class="w-full max-w-md border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
        @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
    </div>

    <div class="panel">
        <h3 class="text-lg font-bold mb-4 pb-2 border-b">Assign Permissions</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($groupedPermissions as $module => $permissions)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <h4 class="font-bold text-gray-900 mb-3">{{ $module }}</h4>
                <div class="space-y-2">
                    @foreach($permissions as $permission)
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                               {{ is_array(old('permissions')) && in_array($permission->id, old('permissions')) ? 'checked' : '' }}
                               class="mt-1 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <span class="text-sm text-gray-700">{{ $permission->label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-8 pt-4 border-t flex justify-end gap-3">
            <a href="{{ route('organization.roles.index') }}" class="btn bg-white border border-gray-300 text-gray-700">Cancel</a>
            <button type="submit" class="btn btn-gold">Save Role</button>
        </div>
    </div>
</form>
@endsection

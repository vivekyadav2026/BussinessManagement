@extends('layouts.sme')

@section('content')
<div class="p-6 max-w-5xl space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('organization.roles.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Role: {{ $role->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Modify the name or adjust permission rules for this custom role.</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('organization.roles.update', $role) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Role Name Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Role Name *</label>
            <input type="text" name="name" value="{{ old('name', $role->name) }}" required class="w-full max-w-md border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2.5 text-sm outline-none transition @error('name') border-red-300 @enderror">
            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Permissions Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-50 pb-3">Assign Permissions</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php $oldPermissions = old('permissions', $rolePermissions); @endphp
                @foreach($groupedPermissions as $module => $permissions)
                <div class="bg-gray-50/50 rounded-xl p-4 border border-gray-100 space-y-3">
                    <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider border-b border-gray-100 pb-2 mb-2">{{ $module }}</h4>
                    <div class="space-y-3">
                        @foreach($permissions as $permission)
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                   {{ is_array($oldPermissions) && in_array($permission->id, $oldPermissions) ? 'checked' : '' }}
                                   class="mt-1 rounded text-[var(--theme-active)] focus:ring-[var(--theme-active)] border-gray-300">
                            <span class="text-sm text-gray-700 font-medium leading-none">{{ $permission->label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Submit Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('organization.roles.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl font-semibold text-sm hover:opacity-95 shadow-sm transition">Update Role</button>
            </div>
        </div>
    </form>
</div>
@endsection

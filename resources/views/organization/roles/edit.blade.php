@extends('layouts.sme')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-2">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 mb-4 border-b border-gray-100">
        <a href="{{ route('organization.roles.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Edit Role: {{ $role->name }}</h1>
            <p class="text-xs text-gray-500">Modify the name or adjust permission rules for this custom role.</p>
        </div>
    </div>

    <!-- Form Container -->
    <form action="{{ route('organization.roles.update', $role) }}" method="POST" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')
        
        <!-- Role Details -->
        <div class="p-5 space-y-4">
            <div class="flex items-center gap-2 pb-1.5 border-b border-gray-50">
                <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Role Details</h2>
            </div>
            
            <div class="max-w-md">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Role Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" required class="w-full border border-gray-300 focus:border-[var(--theme-active)] focus:ring-1 focus:ring-[var(--theme-active)] rounded-lg px-3 py-2 text-sm outline-none transition @error('name') border-red-300 @enderror">
                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Permissions Section -->
        <div class="border-t border-gray-200 p-5 space-y-4 bg-gray-50/30">
            <div class="flex items-center gap-2 pb-1.5 border-b border-gray-100">
                <span class="w-1 h-3.5 bg-[var(--theme-active)] rounded-full"></span>
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Assign Permissions</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php $oldPermissions = old('permissions', $rolePermissions); @endphp
                @foreach($groupedPermissions as $module => $permissions)
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-xs space-y-3">
                    <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider border-b border-gray-150 pb-1.5 mb-2">{{ $module }}</h4>
                    <div class="space-y-2.5">
                        @foreach($permissions as $permission)
                        <!-- Added inline-flex class to bypass the global display-flex / justify-between CSS override and make it checkable -->
                        <label class="inline-flex items-start gap-2.5 cursor-pointer w-full">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                   {{ is_array($oldPermissions) && in_array($permission->id, $oldPermissions) ? 'checked' : '' }}
                                   class="mt-0.5 rounded text-[var(--theme-active)] focus:ring-[var(--theme-active)] border-gray-300">
                            <span class="text-xs text-gray-700 font-medium leading-tight">{{ $permission->label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Footer Actions -->
        <div class="bg-gray-50 border-t border-gray-200 px-5 py-3.5 flex justify-end gap-2.5">
            <a href="{{ route('organization.roles.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg font-semibold text-xs hover:bg-gray-50 shadow-sm transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-lg font-semibold text-xs hover:opacity-90 shadow-sm transition">Update Role</button>
        </div>
    </form>
</div>
@endsection

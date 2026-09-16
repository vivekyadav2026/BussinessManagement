@extends('layouts.sme')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-24">

    <!-- 1. Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.roles.index') }}" class="hover:text-slate-900 transition-colors">Roles &amp; Permissions</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Edit {{ $role->name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('organization.roles.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center transition shadow-2xs" title="Back to Roles">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Edit Role: {{ $role->name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">Modify role name and adjust granular module authorization rules.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('organization.roles.show', $role) }}" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-xs font-bold text-slate-800 transition shadow-2xs">
                View Role Details &rarr;
            </a>
        </div>
    </div>

    <!-- Feedback Errors -->
    @if(isset($errors) && $errors->any())
    <div class="bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs shadow-2xs">
        <div class="font-extrabold mb-1 text-rose-900">Please correct the following errors:</div>
        <ul class="list-disc list-inside space-y-0.5 text-rose-800 font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('organization.roles.update', $role) }}" method="POST" id="editRoleForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT / MAIN FORM COLUMN (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200/90 shadow-2xs divide-y divide-slate-100 overflow-hidden">

                <!-- Section 1: Role Identity -->
                <div class="p-5 sm:p-6 space-y-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-950">Role Identity</h3>
                        <p class="text-xs text-slate-600 font-medium mt-0.5">Descriptive name for this authorization tier.</p>
                    </div>

                    <div class="max-w-md">
                        <label class="block text-xs font-bold text-slate-900 mb-1.5">
                            Role Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $role->name) }}" 
                               required 
                               class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('name') border-rose-300 @enderror">
                        @error('name') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Section 2: Permission Matrix -->
                <div class="p-5 sm:p-6 space-y-5 bg-slate-50/40">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                        <div>
                            <h3 class="text-base font-bold text-slate-950">Module Permissions Matrix</h3>
                            <p class="text-xs text-slate-600 font-medium mt-0.5">Toggle authorized features for staff members holding this role.</p>
                        </div>

                        <!-- Master Select All -->
                        <label class="inline-flex items-center gap-2 cursor-pointer bg-white px-3 py-1.5 rounded-lg border border-slate-300 shadow-2xs hover:bg-slate-50 transition select-none self-start sm:self-auto">
                            <input type="checkbox" id="select-all-global" onclick="toggleAllPermissions(this)" class="rounded text-amber-600 focus:ring-amber-500 border-slate-300 w-4 h-4">
                            <span class="text-xs font-bold text-slate-900">Select All Permissions</span>
                        </label>
                    </div>

                    <!-- Module Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php $oldPermissions = old('permissions', $rolePermissions); @endphp
                        @foreach($groupedPermissions as $module => $permissions)
                        @php 
                            $slug = \Illuminate\Support\Str::slug($module); 
                            $icon = match(strtolower($module)) {
                                'products' => '📦',
                                'inventory' => '📊',
                                'clients' => '🤝',
                                'invoices' => '🧾',
                                'attendance' => '⏱️',
                                'payroll' => '💰',
                                'restaurant' => '🍽️',
                                'complaints' => '⚠️',
                                default => '🛡️'
                            };
                        @endphp
                        <div id="module-{{ $slug }}" class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <h4 class="font-extrabold text-slate-950 text-xs uppercase tracking-wider flex items-center gap-1.5">
                                    <span>{{ $icon }}</span>
                                    <span>{{ $module }}</span>
                                </h4>
                                <label class="inline-flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" onchange="toggleModulePermissions(this, '{{ $slug }}')" class="module-select-all rounded text-amber-600 focus:ring-amber-500 border-slate-300 w-3.5 h-3.5">
                                    <span class="text-[11px] font-bold text-amber-800 hover:underline">Select All</span>
                                </label>
                            </div>

                            <div class="space-y-2">
                                @foreach($permissions as $permission)
                                <label class="flex items-start gap-2.5 p-1.5 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors select-none">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}" 
                                           onchange="updateGroupState('{{ $slug }}')"
                                           {{ is_array($oldPermissions) && in_array($permission->id, $oldPermissions) ? 'checked' : '' }}
                                           class="mt-0.5 rounded text-amber-600 focus:ring-amber-500 border-slate-300 w-4 h-4">
                                    <span class="text-xs text-slate-800 font-semibold leading-tight">{{ $permission->label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- RIGHT / SIDEBAR DETAILS (4 cols) -->
            <div class="lg:col-span-4 space-y-5">
                <!-- Role Stats Card -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Role Metadata</h3>

                    <dl class="divide-y divide-slate-100 text-xs">
                        <div class="py-2.5 flex justify-between">
                            <dt class="text-slate-500">Users Assigned</dt>
                            <dd class="font-bold text-slate-900">{{ $role->users()->count() }} Staff Members</dd>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <dt class="text-slate-500">Created Date</dt>
                            <dd class="font-medium text-slate-800">{{ $role->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Danger Zone -->
                @if($role->users()->count() === 0 && $role->name !== 'Organization Admin')
                <div class="bg-rose-50/60 rounded-xl border border-rose-200 p-5 space-y-3">
                    <h3 class="text-xs font-bold text-rose-900 uppercase tracking-wider">Danger Zone</h3>
                    <p class="text-[11px] text-rose-700 leading-relaxed">
                        Deleting this role is permanent and cannot be undone.
                    </p>
                    <button type="button" 
                            onclick="if(confirm('Are you sure you want to delete this role?')) { document.getElementById('deleteRoleForm').submit(); }" 
                            class="w-full text-center px-3 py-2 rounded-lg border border-rose-300 bg-white hover:bg-rose-50 text-xs font-bold text-rose-700 transition shadow-2xs">
                        Delete Role
                    </button>
                </div>
                @endif
            </div>

        </div>

        <!-- Sticky Bottom Action Bar (~64px) -->
        <div class="sticky bottom-4 z-20 mt-8 bg-white/95 backdrop-blur-md border border-slate-300 rounded-xl shadow-lg px-5 py-3 flex items-center justify-between gap-4">
            <div class="text-xs font-medium text-slate-700">
                <span>Saving updates permissions across all active staff holding this role.</span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('organization.roles.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-950 transition">
                    Cancel
                </a>
                <button type="submit" id="updateRoleBtn" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Update Role</span>
                </button>
            </div>
        </div>

    </form>

    @if($role->users()->count() === 0 && $role->name !== 'Organization Admin')
    <form id="deleteRoleForm" action="{{ route('organization.roles.destroy', $role) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif

</div>

<script>
function toggleAllPermissions(master) {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    checkboxes.forEach(cb => cb.checked = master.checked);
    document.querySelectorAll('.module-select-all').forEach(cb => cb.checked = master.checked);
}

function toggleModulePermissions(moduleMaster, slug) {
    const container = document.getElementById('module-' + slug);
    if (container) {
        const checkboxes = container.querySelectorAll('input[name="permissions[]"]');
        checkboxes.forEach(cb => cb.checked = moduleMaster.checked);
    }
    updateGlobalState();
}

function updateGroupState(slug) {
    const container = document.getElementById('module-' + slug);
    if (container) {
        const checkboxes = container.querySelectorAll('input[name="permissions[]"]');
        const checked = container.querySelectorAll('input[name="permissions[]"]:checked');
        const moduleSelect = container.querySelector('.module-select-all');
        if (moduleSelect) {
            moduleSelect.checked = (checkboxes.length > 0 && checkboxes.length === checked.length);
        }
    }
    updateGlobalState();
}

function updateGlobalState() {
    const all = document.querySelectorAll('input[name="permissions[]"]');
    const checked = document.querySelectorAll('input[name="permissions[]"]:checked');
    const master = document.getElementById('select-all-global');
    if (master) {
        master.checked = (all.length > 0 && all.length === checked.length);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    @foreach($groupedPermissions as $module => $permissions)
        updateGroupState('{{ \Illuminate\Support\Str::slug($module) }}');
    @endforeach
});

document.getElementById('editRoleForm').addEventListener('submit', function() {
    const btn = document.getElementById('updateRoleBtn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-slate-950 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Updating Role...
    `;
});
</script>
@endsection

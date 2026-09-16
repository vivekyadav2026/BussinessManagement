@extends('layouts.sme')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumbs & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.roles.index') }}" class="hover:text-slate-900 transition-colors">Settings</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.roles.index') }}" class="hover:text-slate-900 transition-colors">Roles &amp; Permissions</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">{{ $role->name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-400/30 flex items-center justify-center text-lg shrink-0">
                    🛡️
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight flex items-center gap-2">
                        <span>{{ $role->name }}</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-800 border border-slate-300">
                            Custom Role
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">
                        Module capabilities and authorized staff members for this access profile.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.roles.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Roles</span>
            </a>
            <a href="{{ route('organization.roles.edit', $role) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit Role &amp; Permissions</span>
            </a>
        </div>
    </div>

    <!-- 2. Metrics & KPI Banner -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Assigned Permissions -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Capabilities</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-bold">⚡</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $role->permissions->count() }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Authorized actions granted</div>
        </div>

        <!-- Metric 2: Assigned Staff Users -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Assigned Staff</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-bold">👥</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $role->users->count() }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Active users with this role</div>
        </div>

        <!-- Metric 3: Modules Covered -->
        @php
            $groupedPermissions = $role->permissions->groupBy('module');
        @endphp
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Modules Covered</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm font-bold">📦</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $groupedPermissions->count() }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Functional business areas</div>
        </div>

        <!-- Metric 4: Created Date -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Created Date</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">🗓️</span>
            </div>
            <div class="mt-2 text-sm font-extrabold text-slate-950">
                {{ $role->created_at ? $role->created_at->format('M d, Y') : 'System Default' }}
            </div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Role registration record</div>
        </div>
    </div>

    <!-- 3. Main 2-Column Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT COLUMN: Module Capabilities (8 cols) -->
        <div class="lg:col-span-8 space-y-5">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider">Granted Module Capabilities</h2>
                        <p class="text-xs text-slate-600 font-medium mt-0.5">Permissions active for users assigned to this role.</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-200/80 text-slate-800">
                        {{ $role->permissions->count() }} Total
                    </span>
                </div>

                <div class="p-5 space-y-5">
                    @forelse($groupedPermissions as $module => $permissions)
                    @php 
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
                    <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-base">{{ $icon }}</span>
                                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">{{ $module }}</h3>
                            </div>
                            <span class="text-[11px] font-bold text-slate-700 bg-white border border-slate-200 px-2 py-0.5 rounded-md">
                                {{ $permissions->count() }} {{ \Illuminate\Support\Str::plural('action', $permissions->count()) }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach($permissions as $permission)
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-slate-900 border border-slate-200 shadow-2xs">
                                <span class="text-emerald-600 font-extrabold text-xs">✓</span>
                                <span>{{ $permission->label ?? str_replace('_', ' ', $permission->name) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 px-4">
                        <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-xl mb-3">
                            🔒
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">No permissions assigned</h3>
                        <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto">This role has not been granted any module access yet.</p>
                        <a href="{{ route('organization.roles.edit', $role) }}" class="inline-flex items-center gap-1.5 mt-4 px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-extrabold rounded-lg shadow-2xs transition">
                            Assign Permissions
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Assigned Users & Governance (4 cols) -->
        <div class="lg:col-span-4 space-y-5">
            
            <!-- Assigned Users List Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Assigned Staff</h3>
                        <p class="text-[11px] text-slate-600 font-medium mt-0.5">Staff members holding this role</p>
                    </div>
                    <span class="text-xs font-extrabold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $role->users->count() }}
                    </span>
                </div>

                <div class="p-4 divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @forelse($role->users as $user)
                    <div class="py-3 first:pt-0 last:pb-0 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-slate-900 text-amber-400 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-extrabold text-slate-950 truncate">{{ $user->name }}</div>
                            <div class="text-[11px] text-slate-600 truncate font-medium">{{ $user->email }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center">
                        <div class="text-xl mb-1">👥</div>
                        <p class="text-xs font-bold text-slate-800">No users assigned</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Assign this role to an employee via the Employee Management module.</p>
                        <a href="{{ route('organization.employees.index') }}" class="inline-flex items-center gap-1 mt-3 text-xs font-extrabold text-amber-800 hover:text-amber-900">
                            <span>Go to Employees</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Role Best Practices & Governance -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Governance &amp; Scope</h3>
                <ul class="space-y-2.5 text-xs text-slate-700">
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span><strong>Branch Isolation:</strong> Staff access is restricted to their assigned branch locations.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span><strong>Dynamic Updates:</strong> Changes to permissions take effect immediately upon next user activity.</span>
                    </li>
                </ul>
            </div>

            <!-- Danger Zone / Delete Role -->
            @if($role->users->count() === 0)
            <div class="bg-rose-50/60 rounded-xl border border-rose-200/90 p-5 space-y-3">
                <div>
                    <h4 class="text-xs font-extrabold text-rose-950 uppercase tracking-wider">Delete Role</h4>
                    <p class="text-[11px] text-rose-800 font-medium mt-1 leading-relaxed">
                        This role is not assigned to any staff members and can be safely removed.
                    </p>
                </div>
                <form action="{{ route('organization.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this role?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full text-center px-4 py-2 border border-rose-300 text-xs font-bold rounded-lg text-rose-700 bg-white hover:bg-rose-100 hover:border-rose-400 focus:outline-none transition shadow-2xs">
                        Delete Role Permanently
                    </button>
                </form>
            </div>
            @else
            <div class="bg-slate-50 rounded-xl border border-slate-200/80 p-4">
                <div class="flex items-start gap-2 text-xs text-slate-700">
                    <span class="text-slate-500 font-bold">🔒</span>
                    <p class="text-[11px] leading-relaxed">
                        This role is currently assigned to <strong>{{ $role->users->count() }} staff member(s)</strong> and cannot be deleted until all users are reassigned to another role.
                    </p>
                </div>
            </div>
            @endif

        </div>

    </div>

</div>
@endsection

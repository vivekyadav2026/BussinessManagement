@extends('layouts.sme')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Settings</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Administration</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Roles &amp; Permissions</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Roles &amp; Permissions</h1>
            <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed max-w-3xl">
                Control access levels and module permission rulesets for your business staff.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.roles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Create New Role</span>
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-300 text-emerald-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-emerald-700 text-base">✓</span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-rose-700 text-base">⚠️</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 2. Top KPI Metrics Strip -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Roles Defined -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Defined Roles</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">🛡️</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $totalRoles ?? $roles->count() }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Configured permission tiers</div>
        </div>

        <!-- Metric 2: Assigned Staff -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Assigned Users</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-bold">👥</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $totalAssignedUsers ?? $roles->sum('users_count') }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Staff holding active roles</div>
        </div>

        <!-- Metric 3: Protected Modules -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Protected Modules</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm font-bold">📦</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $totalModules ?? 8 }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">ERP, POS, HR &amp; Financials</div>
        </div>

        <!-- Metric 4: Security Status -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Access Model</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-bold">🔒</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">RBAC</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Role-Based Access Control</div>
        </div>
    </div>

    <!-- 3. Roles Data Table -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider font-extrabold text-slate-700">
                    <tr>
                        <th class="px-5 py-3.5">Role Name</th>
                        <th class="px-5 py-3.5">Permissions Coverage</th>
                        <th class="px-5 py-3.5">Assigned Staff</th>
                        <th class="px-5 py-3.5">Created Date</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roles as $role)
                    @php
                        $isSuperAdmin = ($role->name === 'Organization Admin');
                        $permCount = $role->permissions ? $role->permissions->count() : 0;
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- Role Name -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg {{ $isSuperAdmin ? 'bg-slate-900 text-amber-400' : 'bg-slate-100 text-slate-800' }} flex items-center justify-center text-sm font-extrabold shrink-0 shadow-2xs">
                                    🛡️
                                </div>
                                <div>
                                    <a href="{{ route('organization.roles.show', $role) }}" class="font-extrabold text-slate-950 hover:text-amber-600 transition-colors text-sm">
                                        {{ $role->name }}
                                    </a>
                                    @if($isSuperAdmin)
                                        <div class="mt-0.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-950 border border-amber-300">
                                                ★ Workspace Master Role
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Permissions Coverage -->
                        <td class="px-5 py-4">
                            @if($isSuperAdmin)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-900">
                                    <span>⚡</span> Full System Access (All Modules)
                                </span>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-900 border border-slate-300">
                                        {{ $permCount }} {{ Str::plural('Rule', $permCount) }} Granted
                                    </span>
                                </div>
                            @endif
                        </td>

                        <!-- Assigned Staff -->
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-900">
                                <span class="w-2 h-2 rounded-full {{ $role->users_count > 0 ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                <span>{{ $role->users_count }} {{ Str::plural('Staff Member', $role->users_count) }}</span>
                            </span>
                        </td>

                        <!-- Created Date -->
                        <td class="px-5 py-4 text-xs font-medium text-slate-600">
                            {{ $role->created_at ? $role->created_at->format('M d, Y') : '—' }}
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('organization.roles.show', $role) }}" class="p-1.5 text-slate-600 hover:text-slate-950 hover:bg-slate-100 rounded-lg transition" title="View Role Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                @if(!$isSuperAdmin)
                                    <a href="{{ route('organization.roles.edit', $role) }}" class="p-1.5 text-slate-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition" title="Edit Role Permissions">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    @if($role->users_count === 0)
                                        <form action="{{ route('organization.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the {{ $role->name }} role?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Delete Role">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        System Protected
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-xl mx-auto mb-3">
                                🛡️
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">No custom roles created yet</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Create roles like Cashier, Kitchen Staff, or Floor Manager to control system permissions.</p>
                            <a href="{{ route('organization.roles.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-lg shadow-2xs transition">
                                + Create First Role
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

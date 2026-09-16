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
                <span class="text-slate-950 font-extrabold">Employees</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Employees</h1>
            <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed max-w-3xl">
                Manage your organization's staff profiles, location assignments, and system login access.
            </p>
        </div>

        <!-- Right Header Actions (Quota Badge & Add Employee CTA) -->
        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-900 border border-slate-300 shadow-2xs">
                <span class="text-slate-600 font-medium">Capacity:</span>
                <span>{{ $totalCount }} / {{ is_numeric($maxEmployees) ? $maxEmployees : '∞' }}</span>
                @if($limitReached && Route::has('organization.subscription.index'))
                    <span class="ml-1 text-rose-700 font-extrabold">(Limit Reached)</span>
                @endif
            </div>

            @if(!$limitReached)
                <a href="{{ route('organization.employees.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add Employee</span>
                </a>
            @else
                <a href="{{ route('organization.subscription.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-lg shadow-xs transition" title="Employee limit reached. Upgrade your plan.">
                    <span>Upgrade to Add More &rarr;</span>
                </a>
            @endif
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
        <!-- Metric 1: Total Staff -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Total Staff</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">👥</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $totalCount }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">All registered staff members</div>
        </div>

        <!-- Metric 2: Active Members -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Active Staff</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">🟢</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $activeCount }}</div>
            <div class="text-[11px] text-emerald-800 font-semibold mt-1">Ready for shift scheduling</div>
        </div>

        <!-- Metric 3: System Access -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">System Users</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">🔑</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $systemUsersCount }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Equipped with portal login</div>
        </div>

        <!-- Metric 4: Capacity Limit -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Plan Limit</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-bold">📊</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">
                {{ is_numeric($maxEmployees) ? $maxEmployees : 'Unlimited' }}
            </div>
            <div class="text-[11px] font-medium mt-1 {{ $limitReached ? 'text-rose-700 font-bold' : 'text-slate-600' }}">
                @if($limitReached)
                    <span class="text-rose-700 font-bold">Quota limit reached</span>
                @elseif(is_numeric($maxEmployees))
                    {{ max(0, (int)$maxEmployees - (int)$totalCount) }} available slots
                @else
                    No hard limit
                @endif
            </div>
        </div>
    </div>

    <!-- 3. Filter & Search Toolbar -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
        <form method="GET" action="{{ route('organization.employees.index') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-3 flex-1">
                <!-- Search Input -->
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name, employee code, email..." 
                           class="w-full pl-10 bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition placeholder:text-slate-400">
                </div>

                <!-- Status Select -->
                <div class="w-full sm:w-48">
                    <select name="status" class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3 py-2 text-xs sm:text-sm font-bold text-slate-900 outline-none transition">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Members</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 self-end sm:self-auto">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition shadow-2xs">
                    Apply Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('organization.employees.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 rounded-lg text-xs font-bold transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Directory Data Table (Crisp Dark Text & Modern Structure) -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider font-extrabold text-slate-700">
                    <tr>
                        <th class="px-5 py-3.5">Employee</th>
                        <th class="px-5 py-3.5">Contact Details</th>
                        <th class="px-5 py-3.5">Role &amp; Branch</th>
                        <th class="px-5 py-3.5">Portal Access</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $emp)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- Employee Info -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-900 text-amber-400 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name ?? '', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('organization.employees.show', $emp) }}" class="font-extrabold text-slate-950 hover:text-amber-600 transition-colors truncate block">
                                        {{ $emp->full_name }}
                                    </a>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="font-mono text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $emp->employee_code ?? 'EMP-'.$emp->id }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Contact Coordinates -->
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-950 truncate max-w-[200px]">
                                {{ $emp->email ?? '—' }}
                            </div>
                            <div class="text-xs text-slate-600 font-medium mt-0.5">
                                {{ $emp->phone ?? '—' }}
                            </div>
                        </td>

                        <!-- Designation & Branch -->
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-950">
                                {{ $emp->designation ?? 'Staff Member' }}
                            </div>
                            <div class="text-xs text-slate-600 font-medium mt-0.5 flex items-center gap-1">
                                <span>📍</span>
                                <span>{{ $emp->location ? $emp->location->name : 'All Branches' }}</span>
                            </div>
                        </td>

                        <!-- Portal Access -->
                        <td class="px-5 py-4">
                            @if($emp->user && $emp->user->roles->count() > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-950 border border-indigo-200">
                                    <span>🛡️</span> {{ $emp->user->roles->first()->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium text-slate-500 bg-slate-100 border border-slate-200">
                                    No Login Access
                                </span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-5 py-4">
                            @if($emp->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100/80 text-emerald-950 border border-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active
                                </span>
                            @elseif($emp->status === 'inactive')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-950 border border-amber-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Inactive
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-rose-100 text-rose-950 border border-rose-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Terminated
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('organization.employees.show', $emp) }}" class="p-1.5 text-slate-600 hover:text-slate-950 hover:bg-slate-100 rounded-lg transition" title="View Profile">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                <a href="{{ route('organization.employees.edit', $emp) }}" class="p-1.5 text-slate-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition" title="Edit Employee">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>

                                <form action="{{ route('organization.employees.toggle-status', $emp) }}" method="POST" onsubmit="return confirm('Change status for {{ $emp->full_name }}?');" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 {{ $emp->status === 'active' ? 'text-slate-600 hover:text-amber-700 hover:bg-amber-50' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50' }} rounded-lg transition" title="{{ $emp->status === 'active' ? 'Deactivate Employee' : 'Activate Employee' }}">
                                        @if($emp->status === 'active')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <form action="{{ route('organization.employees.destroy', $emp) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $emp->full_name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Delete Employee">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-xl mx-auto mb-3">
                                👥
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">No employees found</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Try adjusting your search criteria or add a new employee to get started.</p>
                            @if(!$limitReached)
                                <a href="{{ route('organization.employees.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-lg shadow-2xs transition">
                                    + Add New Employee
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
        <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $employees->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

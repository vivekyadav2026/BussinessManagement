@extends('layouts.sme')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumbs & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.locations.index') }}" class="hover:text-slate-900 transition-colors">Settings</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.locations.index') }}" class="hover:text-slate-900 transition-colors">Locations &amp; Outlets</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">{{ $location->name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $location->is_active ? 'bg-amber-500/15 border border-amber-400/30' : 'bg-slate-100 border border-slate-200' }} flex items-center justify-center text-lg shrink-0">
                    🏢
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight flex items-center gap-2 flex-wrap">
                        <span>{{ $location->name }}</span>
                        @php
                            $isActiveSession = ($activeLocationId ?? null) == $location->id;
                        @endphp
                        @if($isActiveSession)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-500/20 text-slate-950 border border-amber-400/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                            Active Working Branch
                        </span>
                        @endif
                        @if($location->is_active)
                        <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-900 border border-emerald-300">
                            Operational
                        </span>
                        @else
                        <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-300">
                            Inactive
                        </span>
                        @endif
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">
                        Branch profile, contact specifications, and deployed personnel roster.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.locations.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Locations</span>
            </a>
            <a href="{{ route('organization.locations.edit', $location) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit Location</span>
            </a>
        </div>
    </div>

    <!-- 2. Metrics & KPI Banner -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Operational Status -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Operational Status</span>
                <span class="w-8 h-8 rounded-lg {{ $location->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center text-sm font-bold">
                    {{ $location->is_active ? '🟢' : '⚪' }}
                </span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">
                {{ $location->is_active ? 'Active' : 'Inactive' }}
            </div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">
                {{ $location->is_active ? 'Accepting daily operations' : 'Suspended operations' }}
            </div>
        </div>

        <!-- Metric 2: Deployed Staff -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Assigned Staff</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-bold">👥</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">
                {{ $location->employees ? $location->employees->count() : 0 }}
            </div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Employees linked to this outlet</div>
        </div>

        <!-- Metric 3: Authorized Users -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Login Accounts</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-bold">🔑</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">
                {{ $location->users ? $location->users->count() : 0 }}
            </div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Users with branch permissions</div>
        </div>

        <!-- Metric 4: Registered Date -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Created Record</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">🗓️</span>
            </div>
            <div class="mt-2 text-sm font-extrabold text-slate-950">
                {{ $location->created_at ? $location->created_at->format('M d, Y') : 'Primary Branch' }}
            </div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Branch ID: #{{ str_pad($location->id, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <!-- 3. Main 2-Column Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT COLUMN: Location Specifications & Staff Roster (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Branch Profile Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Location Specifications</h2>
                        <p class="text-[11px] text-slate-600 font-medium mt-0.5">Physical location coordinates and contact information.</p>
                    </div>
                    <a href="{{ route('organization.locations.edit', $location) }}" class="text-xs font-extrabold text-amber-800 hover:underline">
                        Edit Details
                    </a>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                        <span class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Outlet Name</span>
                        <div class="mt-1 text-sm font-extrabold text-slate-950">{{ $location->name }}</div>
                    </div>

                    <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                        <span class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Contact Phone</span>
                        <div class="mt-1 text-sm font-extrabold text-slate-950 font-mono flex items-center gap-1.5">
                            <span>📞</span>
                            <span>{{ $location->phone ?: 'Not provided' }}</span>
                        </div>
                    </div>

                    <div class="md:col-span-2 bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                        <span class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Physical Address</span>
                        <div class="mt-1 text-xs font-semibold text-slate-900 leading-relaxed">
                            {{ $location->address ?: 'No physical address recorded for this branch.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Assigned to Branch Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Staff Assigned to Branch</h2>
                        <p class="text-[11px] text-slate-600 font-medium mt-0.5">Employees actively stationed at this operational site.</p>
                    </div>
                    <span class="text-xs font-extrabold px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-900 border border-indigo-200">
                        {{ $location->employees ? $location->employees->count() : 0 }} Assigned
                    </span>
                </div>

                @php $employees = $location->employees; @endphp
                @if($employees && $employees->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/60 text-[10px] uppercase font-extrabold text-slate-800 border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4 pl-5">Staff Member</th>
                                <th class="py-3 px-4">Contact</th>
                                <th class="py-3 px-4 text-center">Employment Status</th>
                                <th class="py-3 px-4 text-right pr-5">Profile</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($employees as $emp)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 pl-5 align-middle">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-slate-900 text-amber-400 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($emp->first_name ?? 'E', 0, 1) . substr($emp->last_name ?? 'M', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-slate-950 text-xs truncate">
                                                {{ $emp->first_name }} {{ $emp->last_name }}
                                            </div>
                                            <div class="text-[11px] text-slate-600 font-medium truncate">
                                                ID: #{{ $emp->employee_code ?? $emp->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 align-middle">
                                    <div class="text-xs font-semibold text-slate-800">{{ $emp->email ?? 'No email' }}</div>
                                    <div class="text-[11px] font-medium text-slate-600">{{ $emp->phone ?? '' }}</div>
                                </td>
                                <td class="py-3 px-4 align-middle text-center">
                                    @if($emp->status === 'Active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-900 border border-emerald-300">
                                        Active
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                                        {{ $emp->status ?? 'Inactive' }}
                                    </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 pr-5 align-middle text-right">
                                    <a href="{{ route('organization.employees.show', $emp) }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-amber-800 hover:text-amber-900">
                                        <span>View</span>
                                        <span>&rarr;</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center">
                    <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-lg mb-2">
                        👥
                    </div>
                    <p class="text-xs font-bold text-slate-800">No employees stationed at this branch yet</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Assign staff members by updating their location in the Employee module.</p>
                    <a href="{{ route('organization.employees.index') }}" class="inline-flex items-center gap-1 mt-3 text-xs font-extrabold text-amber-800 hover:text-amber-900">
                        <span>Go to Employee Directory</span>
                        <span>&rarr;</span>
                    </a>
                </div>
                @endif
            </div>

        </div>

        <!-- RIGHT COLUMN: Session & Controls (4 cols) -->
        <div class="lg:col-span-4 space-y-5">

            <!-- Active Session Switcher Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Session Active Branch</h3>

                @if($isActiveSession)
                <div class="p-3.5 bg-amber-500/15 border border-amber-400/40 rounded-xl space-y-1">
                    <div class="flex items-center gap-2 text-xs font-extrabold text-slate-950">
                        <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                        <span>Currently Active Working Branch</span>
                    </div>
                    <p class="text-[11px] text-slate-700 font-medium">
                        All invoices, POS sales, and inventory scans are currently scoped to this branch.
                    </p>
                </div>
                @else
                <p class="text-[11px] text-slate-600 font-medium leading-relaxed">
                    Set this branch as your active workspace to manage its inventory and issue point-of-sale invoices.
                </p>
                <form action="{{ route('organization.set-location') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="location_id" value="{{ $location->id }}">
                    <button type="submit" class="w-full text-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 text-xs font-extrabold rounded-lg shadow-xs transition">
                        ⚡ Switch to this Branch
                    </button>
                </form>
                @endif
            </div>

            <!-- Operational Status Toggle Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Operational Toggle</h3>
                    @if($location->is_active)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-900 border border-emerald-300">
                        Operational
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                        Inactive
                    </span>
                    @endif
                </div>

                <form action="{{ route('organization.locations.toggle-status', $location) }}" method="POST" onsubmit="return confirm('Toggle status for this branch?');" class="pt-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full text-center px-4 py-2 border border-slate-300 text-xs font-bold rounded-lg text-slate-800 bg-white hover:bg-slate-50 transition shadow-2xs">
                        {{ $location->is_active ? 'Deactivate Branch' : 'Activate Branch' }}
                    </button>
                </form>
            </div>

            <!-- Branch Scoping & Isolation Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Branch Isolation Policy</h3>
                <ul class="space-y-2.5 text-xs text-slate-700">
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span><strong>Data Integrity:</strong> Sales, cash registers, and stock adjustments remain compartmentalized.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span><strong>Auditing:</strong> Central Super Admins and Org Admins can review consolidated organization metrics anytime.</span>
                    </li>
                </ul>
            </div>

            <!-- Danger Zone / Delete Branch -->
            @php $empCount = $location->employees ? $location->employees->count() : 0; @endphp
            @if($empCount === 0)
            <div class="bg-rose-50/60 rounded-xl border border-rose-200/90 p-5 space-y-3">
                <div>
                    <h4 class="text-xs font-extrabold text-rose-950 uppercase tracking-wider">Delete Branch</h4>
                    <p class="text-[11px] text-rose-800 font-medium mt-1 leading-relaxed">
                        This location has no employees attached and can be permanently removed from your tenant account.
                    </p>
                </div>
                <form action="{{ route('organization.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Permanently delete {{ $location->name }}? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full text-center px-4 py-2 border border-rose-300 text-xs font-bold rounded-lg text-rose-700 bg-white hover:bg-rose-100 hover:border-rose-400 focus:outline-none transition shadow-2xs">
                        Delete Location Permanently
                    </button>
                </form>
            </div>
            @else
            <div class="bg-slate-50 rounded-xl border border-slate-200/80 p-4">
                <div class="flex items-start gap-2 text-xs text-slate-700">
                    <span class="text-slate-500 font-bold">🔒</span>
                    <p class="text-[11px] leading-relaxed">
                        This location is attached to <strong>{{ $empCount }} active staff member(s)</strong>. To delete this location, reassign these employees to another branch first.
                    </p>
                </div>
            </div>
            @endif

        </div>

    </div>

</div>
@endsection

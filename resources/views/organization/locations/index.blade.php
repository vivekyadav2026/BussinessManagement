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
                <span class="text-slate-950 font-extrabold">Locations &amp; Outlets</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Locations &amp; Outlets</h1>
            <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed max-w-3xl">
                Manage your business branches, warehouse distribution points, and retail outlet registers.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            @php
                $isLimitReached = $limitReached ?? (is_numeric($maxLocations) && ($totalLocations ?? $locations->total()) >= (int)$maxLocations);
            @endphp

            @if($isLimitReached)
                <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-50 border border-amber-300 text-amber-950 rounded-lg text-xs font-bold shadow-2xs">
                    <span>⚠️ Quota Reached</span>
                    <a href="{{ route('organization.subscription.index') }}" class="underline font-extrabold hover:text-amber-900">Upgrade Plan</a>
                </div>
            @else
                <a href="{{ route('organization.locations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add New Location</span>
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
        <!-- Metric 1: Total Outlets -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Total Outlets</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">🏢</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $totalLocations ?? $locations->total() }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Configured business branches</div>
        </div>

        <!-- Metric 2: Operational (Active) -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Operational</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm font-bold">🟢</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $activeLocations ?? $locations->where('is_active', true)->count() }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Active trading locations</div>
        </div>

        <!-- Metric 3: Assigned Staff -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Assigned Staff</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-bold">👥</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $totalAssignedEmployees ?? $locations->sum('employees_count') }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Personnel deployed to branches</div>
        </div>

        <!-- Metric 4: Branch Quota -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Branch Quota</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-bold">📍</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">
                {{ is_numeric($maxLocations) ? $maxLocations : 'Unlimited' }}
            </div>
            <div class="text-[11px] font-medium mt-1 {{ $isLimitReached ? 'text-rose-700 font-bold' : 'text-slate-600' }}">
                @if($isLimitReached)
                    <span>Limit reached ({{ $totalLocations ?? $locations->total() }}/{{ $maxLocations }})</span>
                @elseif(is_numeric($maxLocations))
                    <span>{{ max(0, (int)$maxLocations - (int)($totalLocations ?? $locations->total())) }} available slots</span>
                @else
                    <span>No hard branch limit</span>
                @endif
            </div>
        </div>
    </div>

    <!-- 3. Filter & Search Bar -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-3.5 sm:p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" id="branch-search" placeholder="Search branch, phone or address..." onkeyup="filterBranches()" class="w-full pl-9 pr-3 py-2 text-xs font-medium text-slate-900 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-amber-500 focus:bg-white transition">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end text-xs text-slate-600 font-semibold">
                <span>Showing <strong class="text-slate-950">{{ $locations->count() }}</strong> of <strong class="text-slate-950">{{ $totalLocations ?? $locations->total() }}</strong> branches</span>
            </div>
        </div>
    </div>

    <!-- 4. Locations Registry Table -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse" id="branches-table">
                <thead class="bg-slate-50 text-[11px] uppercase font-extrabold text-slate-900 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4 pl-5">Branch / Outlet</th>
                        <th class="py-3.5 px-4">Contact &amp; Physical Address</th>
                        <th class="py-3.5 px-4 text-center">Staff Count</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right pr-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($locations as $loc)
                    @php
                        $isActiveSession = ($activeLocationId ?? null) == $loc->id;
                    @endphp
                    <tr class="branch-row hover:bg-slate-50/80 transition-colors {{ $isActiveSession ? 'bg-amber-50/30' : '' }}">
                        <!-- Column 1: Name & Session Status -->
                        <td class="py-3.5 px-4 pl-5 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg {{ $loc->is_active ? 'bg-amber-500/15 border border-amber-400/30 text-slate-900' : 'bg-slate-100 border border-slate-200 text-slate-400' }} flex items-center justify-center text-sm font-extrabold shrink-0">
                                    🏢
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('organization.locations.show', $loc) }}" class="font-extrabold text-slate-950 hover:text-amber-700 text-sm truncate">
                                            {{ $loc->name }}
                                        </a>
                                        @if($isActiveSession)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/20 text-slate-950 border border-amber-400/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                            Active Session
                                        </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-600 font-medium mt-0.5">
                                        Branch ID: #{{ str_pad($loc->id, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Column 2: Address & Phone -->
                        <td class="py-3.5 px-4 align-middle">
                            <div class="max-w-xs">
                                <div class="text-xs font-semibold text-slate-900 truncate" title="{{ $loc->address }}">
                                    {{ $loc->address ?: 'No address specified' }}
                                </div>
                                <div class="text-[11px] font-medium text-slate-600 mt-0.5 flex items-center gap-1.5">
                                    <span>📞</span>
                                    <span>{{ $loc->phone ?: 'No phone recorded' }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Column 3: Staff Deployment -->
                        <td class="py-3.5 px-4 align-middle text-center">
                            <a href="{{ route('organization.employees.index', ['location_id' => $loc->id]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-900 border border-indigo-200 hover:bg-indigo-100 transition" title="View employees at this branch">
                                <span>👥</span>
                                <span>{{ $loc->employees_count ?? 0 }} Staff</span>
                            </a>
                        </td>

                        <!-- Column 4: Operational Status -->
                        <td class="py-3.5 px-4 align-middle text-center">
                            @if($loc->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-900 border border-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                Operational
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Inactive
                            </span>
                            @endif
                        </td>

                        <!-- Column 5: Action Controls -->
                        <td class="py-3.5 px-4 pr-5 align-middle text-right">
                            <div class="inline-flex items-center gap-1.5">
                                @if($loc->is_active && !$isActiveSession)
                                <form action="{{ route('organization.set-location') }}" method="POST" class="inline m-0">
                                    @csrf
                                    <input type="hidden" name="location_id" value="{{ $loc->id }}">
                                    <button type="submit" class="px-2.5 py-1.5 bg-slate-100 hover:bg-amber-100 hover:text-amber-900 text-slate-800 text-xs font-bold rounded-lg border border-slate-200 transition" title="Switch current session to this branch">
                                        Set Active
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('organization.locations.show', $loc) }}" class="p-1.5 text-slate-600 hover:text-slate-950 hover:bg-slate-100 rounded-lg transition" title="View Branch Profile">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <a href="{{ route('organization.locations.edit', $loc) }}" class="p-1.5 text-slate-600 hover:text-slate-950 hover:bg-slate-100 rounded-lg transition" title="Edit Location">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                <form action="{{ route('organization.locations.toggle-status', $loc) }}" method="POST" onsubmit="return confirm('Change operational status for {{ $loc->name }}?');" class="inline m-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 {{ $loc->is_active ? 'text-amber-700 hover:bg-amber-50' : 'text-emerald-700 hover:bg-emerald-50' }} rounded-lg transition" title="{{ $loc->is_active ? 'Deactivate Branch' : 'Activate Branch' }}">
                                        @if($loc->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>

                                @if(($loc->employees_count ?? 0) === 0)
                                <form action="{{ route('organization.locations.destroy', $loc) }}" method="POST" onsubmit="return confirm('Permanently delete this location? This action cannot be undone.');" class="inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete Location">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-4 text-center">
                            <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-xl mb-3">
                                🏢
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">No branch locations found</h3>
                            <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto">Get started by registering your primary store or head office outlet.</p>
                            @if(!$isLimitReached)
                            <a href="{{ route('organization.locations.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-extrabold rounded-lg shadow-xs transition">
                                + Add First Location
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            {{ $locations->links() }}
        </div>
        @endif
    </div>

</div>

<script>
function filterBranches() {
    const input = document.getElementById('branch-search');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('.branch-row');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}
</script>
@endsection

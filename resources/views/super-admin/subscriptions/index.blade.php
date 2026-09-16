@extends('layouts.super-admin')

@section('title', 'Subscriptions Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Super Admin</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Subscriptions</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    💳
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Tenant Subscriptions</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Manage organization billing tiers, trial access, feature limits, and subscription lifecycles.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Action Button -->
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('super-admin.subscriptions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Subscription</span>
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

    <!-- 2. KPI Metric Cards Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Subscriptions -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Total Subscriptions</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 border border-slate-200 flex items-center justify-center text-xs font-bold">📋</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ number_format($totalCount ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">All issued tenant plans</p>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Active Paid Plans</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-xs font-bold">✓</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-emerald-700 font-mono tracking-tight">{{ number_format($activeCount ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Currently paying active tenants</p>
            </div>
        </div>

        <!-- Free Trial Tenants -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Free Trial Tenants</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center text-xs font-bold">⏳</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-amber-700 font-mono tracking-tight">{{ number_format($trialCount ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Exploring during evaluation period</p>
            </div>
        </div>

        <!-- Expired / Inactive -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Expired / Inactive</span>
                <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center text-xs font-bold">⚠️</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-rose-700 font-mono tracking-tight">{{ number_format($expiredCount ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Expired, cancelled or refunded</p>
            </div>
        </div>
    </div>

    <!-- 3. Filter & Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('super-admin.subscriptions.index') }}" class="flex flex-wrap items-center justify-between gap-3">
            <!-- Search -->
            <div class="relative flex-1 min-w-[240px] max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by organization name..." 
                       class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-xs font-medium focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
            </div>

            <!-- Status Filter -->
            <div class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold text-slate-800 focus:border-amber-500">
                    <option value="">All Statuses</option>
                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Trial" {{ request('status') === 'Trial' ? 'selected' : '' }}>Trial</option>
                    <option value="Expired" {{ request('status') === 'Expired' ? 'selected' : '' }}>Expired</option>
                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('super-admin.subscriptions.index') }}" class="px-3 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 rounded-lg">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Subscriptions Data Table -->
    <div class="bg-white rounded-xl shadow-2xs border border-slate-200/90 overflow-hidden space-y-3 p-5">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-3">Organization Tenant</th>
                        <th class="p-3">Assigned Plan</th>
                        <th class="p-3">Billing Cycle</th>
                        <th class="p-3">Valid From</th>
                        <th class="p-3">Valid Until</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($subscriptions as $sub)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-3">
                            <div class="font-extrabold text-slate-950 text-xs">{{ optional($sub->organization)->name ?: 'Unnamed Org' }}</div>
                            <div class="text-[10px] text-slate-500 font-mono mt-0.5">Org ID: #{{ $sub->organization_id }}</div>
                        </td>
                        <td class="p-3">
                            <span class="font-extrabold text-slate-900">{{ optional($sub->plan)->name ?: 'N/A' }}</span>
                            <span class="text-[10px] font-bold text-slate-500 block uppercase">{{ optional($sub->plan)->type ?? 'Base' }}</span>
                        </td>
                        <td class="p-3">
                            <span class="font-bold text-slate-800">{{ ucfirst($sub->billing_cycle) }}</span>
                        </td>
                        <td class="p-3 font-mono text-slate-600">
                            {{ $sub->starts_at ? $sub->starts_at->format('M d, Y') : '-' }}
                        </td>
                        <td class="p-3 font-mono text-slate-600">
                            {{ $sub->ends_at ? $sub->ends_at->format('M d, Y') : 'Lifetime' }}
                        </td>
                        <td class="p-3 text-center">
                            @if($sub->status === 'Active')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-300">Active</span>
                            @elseif($sub->status === 'Trial')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-300">Trial</span>
                            @elseif($sub->status === 'Expired')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-800 border border-rose-300">Expired</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">{{ $sub->status }}</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex justify-end items-center gap-2">
                                <a href="{{ route('super-admin.subscriptions.show', $sub->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-md transition">
                                    View
                                </a>
                                <a href="{{ route('super-admin.subscriptions.edit', $sub->id) }}" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-md transition">
                                    Edit
                                </a>
                                <form action="{{ route('super-admin.subscriptions.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subscription?');" class="inline-block m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-md transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400 text-xs font-semibold">
                            No subscription records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pt-3 border-t border-slate-100">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>
@endsection

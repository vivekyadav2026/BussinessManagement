@extends('layouts.sme')

@section('title', 'Receivables & Accounts Aging')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Operations</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Billing & Finance</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Receivables</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    💰
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Accounts Receivable</h1>
                        @php
                            $activeLocation = \App\Models\Location::find(\App\Services\LocationManager::getActiveLocationId());
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $activeLocation->name ?? 'Active Branch' }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Track pending customer credit settlements, aging schedules, and cash-inflow pipelines.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section Navigation Pills -->
        <div class="inline-flex items-center gap-1.5 bg-slate-100 border border-slate-300 rounded-xl p-1.5 shadow-2xs shrink-0">
            <a href="{{ route('organization.receivables.index') }}" class="font-extrabold text-xs px-3.5 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.index') ? 'bg-slate-950 text-white shadow-xs' : 'text-slate-700 hover:text-slate-950' }}">
                Overview Dashboard
            </a>
            <a href="{{ route('organization.receivables.client_report') }}" class="font-extrabold text-xs px-3.5 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.client_report') ? 'bg-slate-950 text-white shadow-xs' : 'text-slate-700 hover:text-slate-950' }}">
                Party-wise Balances
            </a>
            <a href="{{ route('organization.receivables.overdue_report') }}" class="font-extrabold text-xs px-3.5 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.overdue_report') ? 'bg-slate-950 text-white shadow-xs' : 'text-slate-700 hover:text-slate-950' }}">
                Overdue Aging
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
        <span class="font-extrabold text-rose-700 text-base">⚠</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 2. Financial KPI Metric Strip (4 Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- 1. Total Outstanding -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Total Outstanding</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-800 text-sm font-bold">
                    💳
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                    ₹{{ number_format($totalOutstanding, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-slate-600 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>All uncollected credit</span>
                </div>
            </div>
        </div>

        <!-- 2. Total Overdue -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-rose-800">Total Overdue</span>
                <span class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-700 text-sm font-bold">
                    ⚠
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-rose-600 tracking-tight">
                    ₹{{ number_format($totalOverdue, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-rose-800 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Past payment deadline</span>
                </div>
            </div>
        </div>

        <!-- 3. Due Today -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-amber-800">Due Today</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-800 text-sm font-bold">
                    ⏳
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-amber-600 tracking-tight">
                    ₹{{ number_format($dueToday, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-amber-800 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Targeted for today</span>
                </div>
            </div>
        </div>

        <!-- 4. Due This Week -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">Due This Week</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700 text-sm font-bold">
                    ✓
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-emerald-700 tracking-tight">
                    ₹{{ number_format($dueThisWeek, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-emerald-800 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Expected by week end</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Search & Filter Bar -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4">
        <form method="GET" action="{{ route('organization.receivables.index') }}" class="flex flex-col md:flex-row gap-3 items-stretch md:items-end">
            <!-- Search Text -->
            <div class="flex-1">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Search Invoice</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by invoice # or client name..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-xs sm:text-sm font-medium focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-slate-950 placeholder-slate-400">
                </div>
            </div>

            <!-- Client Filter -->
            <div class="w-full md:w-64">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Filter Client</label>
                <select name="client_id" class="w-full border border-slate-300 rounded-lg text-xs font-bold text-slate-800 px-3 py-2 bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="">All Clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-56">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="w-full border border-slate-300 rounded-lg text-xs font-bold text-slate-800 px-3 py-2 bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="">All Unpaid Statuses</option>
                    <option value="Due" {{ request('status') == 'Due' ? 'selected' : '' }}>Due (Unpaid)</option>
                    <option value="Partially Paid" {{ request('status') == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="Overdue" {{ request('status') == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid (Fully Settled)</option>
                    <option value="ALL" {{ request('status') == 'ALL' ? 'selected' : '' }}>All Invoices</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg transition shadow-2xs">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'client_id', 'status']))
                    <a href="{{ route('organization.receivables.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition border border-slate-200">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Master Receivables Table Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-[11px] font-extrabold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Invoice #</th>
                        <th class="py-3.5 px-4">Client Name</th>
                        <th class="py-3.5 px-4">Due Date</th>
                        <th class="py-3.5 px-4 text-right">Grand Total</th>
                        <th class="py-3.5 px-4 text-right">Balance Due</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- Invoice # -->
                        <td class="py-3.5 px-4">
                            <div class="font-extrabold text-slate-950">
                                <a href="{{ route('organization.invoices.show', $inv) }}" class="hover:text-amber-600 transition-colors">
                                    {{ $inv->invoice_number }}
                                </a>
                            </div>
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                Issued: {{ $inv->invoice_date ? $inv->invoice_date->format('d M, Y') : '-' }}
                            </div>
                        </td>

                        <!-- Client Name -->
                        <td class="py-3.5 px-4">
                            @if($inv->client)
                                <div class="font-bold text-slate-900">
                                    <a href="{{ route('organization.clients.show', $inv->client_id) }}" class="text-indigo-600 hover:underline">
                                        {{ $inv->client->name }}
                                    </a>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $inv->client->phone ?? 'No Phone' }}
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">
                                    Walk-in Client
                                </span>
                            @endif
                        </td>

                        <!-- Due Date -->
                        <td class="py-3.5 px-4">
                            @php
                                $isOverdue = $inv->status !== 'Paid' && $inv->due_date && $inv->due_date < now()->startOfDay();
                            @endphp
                            <div class="font-semibold {{ $isOverdue ? 'text-rose-600 font-bold' : 'text-slate-700' }}">
                                {{ $inv->due_date ? $inv->due_date->format('d M, Y') : 'Immediate' }}
                            </div>
                            @if($isOverdue)
                                <div class="text-[11px] font-extrabold text-rose-600 mt-0.5 flex items-center gap-1">
                                    <span>●</span>
                                    <span>{{ now()->diffInDays($inv->due_date) }} days late</span>
                                </div>
                            @endif
                        </td>

                        <!-- Grand Total -->
                        <td class="py-3.5 px-4 text-right font-extrabold text-slate-800">
                            ₹{{ number_format($inv->grand_total, 2) }}
                        </td>

                        <!-- Balance Due -->
                        <td class="py-3.5 px-4 text-right font-black text-rose-600 text-sm">
                            ₹{{ number_format($inv->amount_due, 2) }}
                        </td>

                        <!-- Status Pill -->
                        <td class="py-3.5 px-4 text-center">
                            @if($inv->status === 'Paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-800 border border-emerald-300">
                                    Paid
                                </span>
                            @elseif($inv->due_date && $inv->due_date < now()->startOfDay() && $inv->amount_due > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-rose-50 text-rose-800 border border-rose-300 animate-pulse">
                                    Overdue
                                </span>
                            @elseif($inv->status === 'Partially Paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-amber-50 text-amber-900 border border-amber-300">
                                    Partially Paid
                                </span>
                            @elseif($inv->status === 'Due')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-blue-50 text-blue-800 border border-blue-300">
                                    Due
                                </span>
                            @elseif($inv->status === 'Draft')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                    Draft
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                    {{ $inv->status }}
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('organization.invoices.show', $inv) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-amber-500 hover:text-slate-950 text-slate-800 font-extrabold text-xs rounded-lg transition border border-slate-200 shadow-2xs">
                                <span>Record Payment / View &rarr;</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mx-auto mb-3">
                                💰
                            </div>
                            <div class="font-extrabold text-slate-700 text-sm">No outstanding receivables found</div>
                            <p class="text-xs text-slate-500 mt-0.5">All customer accounts are currently settled or match your filter criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

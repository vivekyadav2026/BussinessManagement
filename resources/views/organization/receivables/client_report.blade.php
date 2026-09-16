@extends('layouts.sme')

@section('title', 'Party-wise Outstanding Balances - Receivables')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.receivables.index') }}" class="hover:text-slate-900 transition-colors">Operations</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.receivables.index') }}" class="hover:text-slate-900 transition-colors">Billing & Finance</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Party-wise Balances</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    👥
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Party-wise Balances</h1>
                        @php
                            $activeLocation = \App\Models\Location::find(\App\Services\LocationManager::getActiveLocationId());
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $activeLocation->name ?? 'Active Branch' }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Client ledger breakdown of all parties with outstanding credit balances and overdue liability.
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

    <!-- 2. Financial KPI Metric Strip (4 Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- 1. Total Party Outstanding -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Total Outstanding</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-800 text-sm font-bold">
                    💳
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                    ₹{{ number_format($totalPartyOutstanding ?? 0, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-slate-600 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>Cumulative client balance</span>
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
                    ₹{{ number_format($totalPartyOverdue ?? 0, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-rose-800 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Past due payment dates</span>
                </div>
            </div>
        </div>

        <!-- 3. Debtor Parties Count -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Debtor Accounts</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700 text-sm font-bold">
                    👥
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                    {{ $partyCount ?? count($clients) }}
                </div>
                <div class="text-[11px] font-semibold text-slate-600 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Parties with unpaid bills</span>
                </div>
            </div>
        </div>

        <!-- 4. Average Balance per Debtor -->
        @php
            $pCount = $partyCount ?? count($clients);
            $avgBalance = $pCount > 0 ? ($totalPartyOutstanding / $pCount) : 0;
        @endphp
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Avg Debt / Party</span>
                <span class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-700 text-sm font-bold">
                    📊
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                    ₹{{ number_format($avgBalance, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-slate-600 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span>Average per party</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Search Bar -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4">
        <form method="GET" action="{{ route('organization.receivables.client_report') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search client by name, company, or phone number..."
                       class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm font-semibold text-slate-900 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white rounded-lg text-xs font-extrabold transition-all shadow-2xs flex items-center justify-center gap-1.5">
                    Filter Ledger
                </button>
                @if(request()->filled('search'))
                <a href="{{ route('organization.receivables.client_report') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-all">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Party-wise Ledger Table Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="text-sm font-extrabold text-slate-950">Party Balance Ledger</h3>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Accounts sorted by highest outstanding balance</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-slate-200/80 text-slate-800 border border-slate-300">
                {{ count($clients) }} {{ Str::plural('Party', count($clients)) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4">Client / Party</th>
                        <th class="py-3 px-4">Contact Info</th>
                        <th class="py-3 px-4 text-center">Open Invoices</th>
                        <th class="py-3 px-4 text-right">Total Outstanding</th>
                        <th class="py-3 px-4 text-right">Overdue Balance</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($clients as $c)
                    <tr class="hover:bg-amber-50/30 transition-colors group">
                        <!-- Client Name & Avatar -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-950 text-amber-400 font-black text-sm flex items-center justify-center shrink-0 shadow-2xs border border-slate-700">
                                    {{ strtoupper(substr($c->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('organization.clients.show', $c) }}" class="font-extrabold text-slate-950 hover:text-amber-600 transition-colors block truncate">
                                        {{ $c->name }}
                                    </a>
                                    @if($c->company_name)
                                    <p class="text-[11px] font-medium text-slate-500 truncate">{{ $c->company_name }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Contact Info -->
                        <td class="py-3.5 px-4 font-medium text-slate-600">
                            <div class="space-y-0.5">
                                @if($c->phone)
                                <div class="flex items-center gap-1.5 font-bold text-slate-800">
                                    <span class="text-slate-400 text-xs">📞</span>
                                    <span>{{ $c->phone }}</span>
                                </div>
                                @endif
                                @if($c->email)
                                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 truncate max-w-xs">
                                    <span class="text-slate-400 text-xs">✉</span>
                                    <span class="truncate">{{ $c->email }}</span>
                                </div>
                                @endif
                                @if(!$c->phone && !$c->email)
                                <span class="text-slate-400 italic">No contact provided</span>
                                @endif
                            </div>
                        </td>

                        <!-- Unpaid Invoices Count -->
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-slate-100 text-slate-800 border border-slate-300">
                                {{ $c->invoice_count }} {{ Str::plural('bill', $c->invoice_count) }}
                            </span>
                        </td>

                        <!-- Total Outstanding -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="text-sm font-black text-slate-950 tracking-tight">
                                ₹{{ number_format($c->total_outstanding, 2) }}
                            </div>
                            <div class="text-[10px] font-semibold text-slate-500">Uncollected balance</div>
                        </td>

                        <!-- Overdue Balance -->
                        <td class="py-3.5 px-4 text-right">
                            @if($c->total_overdue > 0)
                            <div class="inline-flex flex-col items-end">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-rose-50 text-rose-700 border border-rose-200">
                                    <span>⚠</span>
                                    <span>₹{{ number_format($c->total_overdue, 2) }}</span>
                                </span>
                                <span class="text-[10px] font-extrabold text-rose-700 mt-0.5">Overdue</span>
                            </div>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span>✓</span>
                                <span>All on-time</span>
                            </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('organization.receivables.index', ['client_id' => $c->id]) }}"
                                   class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 rounded-lg transition-all text-xs font-extrabold shadow-2xs inline-flex items-center gap-1">
                                    <span>View Bills</span>
                                    <span>&rarr;</span>
                                </a>
                                <a href="{{ route('organization.clients.show', $c) }}"
                                   class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-all text-xs font-bold"
                                   title="View Client Ledger Profile">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-16 px-4">
                            <div class="max-w-sm mx-auto flex flex-col items-center">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xl font-bold mb-3 shadow-2xs">
                                    ✓
                                </div>
                                <h4 class="text-sm font-extrabold text-slate-900">All Client Balances Cleared</h4>
                                <p class="text-xs text-slate-500 mt-1 text-center">
                                    No outstanding client receivables match your current filters. All customer accounts are fully settled.
                                </p>
                                @if(request()->filled('search'))
                                <a href="{{ route('organization.receivables.client_report') }}" class="mt-4 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-all shadow-2xs">
                                    Clear Search Filter
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

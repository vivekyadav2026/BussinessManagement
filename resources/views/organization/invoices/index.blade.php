@extends('layouts.sme')

@section('title', 'Invoices & Billing')

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
                <span class="text-slate-950 font-extrabold">Invoices</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    🧾
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Invoice Management</h1>
                        @php
                            $activeLocation = \App\Models\Location::find(\App\Services\LocationManager::getActiveLocationId());
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $activeLocation->name ?? 'Active Branch' }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Generate tax invoices, manage payments, track receivables, and print thermal POS receipts.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Actions (Capacity & Add CTA) -->
        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-900 border border-slate-300 shadow-2xs">
                <span class="text-slate-500 font-medium">Monthly Quota:</span>
                <span>{{ $monthlyCount ?? 0 }} / {{ is_numeric($maxInvoices) ? $maxInvoices : '∞' }}</span>
                @if(($limitReached ?? false) && Route::has('organization.subscription.index'))
                    <span class="ml-1 text-rose-700 font-extrabold">(Limit Reached)</span>
                @endif
            </div>

            @if(!($limitReached ?? false))
                <a href="{{ route('organization.invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    <span>Create Invoice</span>
                </a>
            @else
                <a href="{{ route('organization.subscription.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-lg shadow-xs transition" title="Invoice limit reached. Upgrade subscription plan.">
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
        <span class="font-extrabold text-rose-700 text-base">⚠</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 2. Financial KPI Metric Strip (4 Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- 1. Total Invoiced Volume -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Total Invoiced</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-700 text-sm font-bold">
                    💳
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                    ₹{{ number_format($stats['total_invoiced'] ?? 0, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-slate-600 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>{{ $stats['total_count'] ?? 0 }} total invoices recorded</span>
                </div>
            </div>
        </div>

        <!-- 2. Collections Cleared -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">Total Collected</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700 text-sm font-bold">
                    ✓
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-emerald-700 tracking-tight">
                    ₹{{ number_format($stats['paid_sum'] ?? 0, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-emerald-800 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Fully settled payments</span>
                </div>
            </div>
        </div>

        <!-- 3. Pending Receivables -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-amber-800">Receivables Due</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-800 text-sm font-bold">
                    ⏳
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-amber-600 tracking-tight">
                    ₹{{ number_format($stats['unpaid_sum'] ?? 0, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-amber-800 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Pending settlement</span>
                </div>
            </div>
        </div>

        <!-- 4. Overdue Invoices -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-rose-800">Overdue Risk</span>
                <span class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-700 text-sm font-bold">
                    ⚠
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-rose-600 tracking-tight">
                    {{ $stats['overdue_count'] ?? 0 }}
                    <span class="text-xs font-bold text-slate-500 uppercase ml-1">Invoices</span>
                </div>
                <div class="text-[11px] font-semibold text-rose-800 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Past payment deadline</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Status Tabs Filter Bar -->
    <div class="flex items-center gap-2 overflow-x-auto border-b border-slate-200 pb-2 scrollbar-thin">
        @php
            $currentStatus = request('status', '');
            $statusOptions = [
                '' => 'All Invoices',
                'Paid' => 'Paid',
                'Partially Paid' => 'Partially Paid',
                'Due' => 'Due',
                'Overdue' => 'Overdue',
                'Draft' => 'Draft',
                'Cancelled' => 'Cancelled'
            ];
        @endphp
        @foreach($statusOptions as $val => $label)
            <a href="{{ route('organization.invoices.index', array_merge(request()->except(['page']), ['status' => $val])) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all whitespace-nowrap {{ $currentStatus === (string)$val ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200/80 hover:text-slate-950 border border-slate-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- 4. Search and Toolbar Filter -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4">
        <form method="GET" action="{{ route('organization.invoices.index') }}" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by invoice # (e.g. INV-2026-...) or client name..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-xs sm:text-sm font-medium focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-slate-950 placeholder-slate-400">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg transition shadow-2xs">
                    Search
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('organization.invoices.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition border border-slate-200">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 5. Master Invoices Table Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-[11px] font-extrabold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Invoice #</th>
                        <th class="py-3.5 px-4">Client</th>
                        <th class="py-3.5 px-4">Dates</th>
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
                                Ref ID: #{{ str_pad($inv->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        </td>

                        <!-- Client -->
                        <td class="py-3.5 px-4">
                            @if($inv->client)
                                <div class="font-bold text-slate-900">
                                    <a href="{{ route('organization.clients.show', $inv->client_id) }}" class="text-indigo-600 hover:underline">
                                        {{ $inv->client->name }}
                                    </a>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                    @if($inv->client->phone)
                                        <span>{{ $inv->client->phone }}</span>
                                    @endif
                                    @if($inv->client->gst_number)
                                        <span class="font-mono bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded border border-slate-200">GST: {{ $inv->client->gst_number }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">
                                    Walk-in Client
                                </span>
                            @endif
                        </td>

                        <!-- Dates -->
                        <td class="py-3.5 px-4 text-slate-600">
                            <div class="font-semibold text-slate-900">{{ $inv->invoice_date->format('d M, Y') }}</div>
                            <div class="text-[11px] {{ $inv->due_date && $inv->due_date < now() && $inv->amount_due > 0 ? 'text-rose-600 font-bold' : 'text-slate-500' }} mt-0.5">
                                Due: {{ $inv->due_date ? $inv->due_date->format('d M, Y') : 'Immediate' }}
                            </div>
                        </td>

                        <!-- Grand Total -->
                        <td class="py-3.5 px-4 text-right font-black text-slate-950">
                            ₹{{ number_format($inv->grand_total, 2) }}
                        </td>

                        <!-- Balance Due -->
                        <td class="py-3.5 px-4 text-right">
                            @if($inv->amount_due > 0 && $inv->status !== 'Draft' && $inv->status !== 'Cancelled')
                                <span class="font-extrabold text-rose-600">
                                    ₹{{ number_format($inv->amount_due, 2) }}
                                </span>
                            @else
                                <span class="text-xs font-bold text-emerald-700">₹0.00</span>
                            @endif
                        </td>

                        <!-- Status Pill -->
                        <td class="py-3.5 px-4 text-center">
                            @if($inv->status === 'Paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-800 border border-emerald-300">
                                    Paid
                                </span>
                            @elseif($inv->status === 'Partially Paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-amber-50 text-amber-900 border border-amber-300">
                                    Partially Paid
                                </span>
                            @elseif($inv->status === 'Due')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-blue-50 text-blue-800 border border-blue-300">
                                    Due
                                </span>
                            @elseif($inv->status === 'Overdue')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-rose-50 text-rose-800 border border-rose-300 animate-pulse">
                                    Overdue
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

                        <!-- Action Icons -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <a href="{{ route('organization.invoices.show', $inv) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition" title="View details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('organization.invoices.print', $inv) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="Print A4 Tax Invoice">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                <a href="{{ route('organization.invoices.receipt', $inv) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition" title="Print Thermal POS Receipt">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mx-auto mb-3">
                                🧾
                            </div>
                            <div class="font-extrabold text-slate-700 text-sm">No invoices found</div>
                            <p class="text-xs text-slate-500 mt-0.5">Try adjusting your search criteria or create your first invoice.</p>
                            @if(!($limitReached ?? false))
                            <div class="mt-4">
                                <a href="{{ route('organization.invoices.create') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-lg transition shadow-2xs">
                                    + Create Invoice
                                </a>
                            </div>
                            @endif
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

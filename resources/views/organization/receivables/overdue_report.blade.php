@extends('layouts.sme')

@section('title', 'Overdue Aging & Debt Collection - Receivables')

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
                <span class="text-slate-950 font-extrabold">Overdue Aging</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-500 to-rose-600 flex items-center justify-center text-white text-xl font-black shadow-sm shrink-0">
                    ⚠
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Overdue Aging & Recovery</h1>
                        @php
                            $activeLocation = \App\Models\Location::find(\App\Services\LocationManager::getActiveLocationId());
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $activeLocation->name ?? 'Active Branch' }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Delinquent receivables past due terms. Send automated payment reminders via WhatsApp or Email.
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

    <!-- 2. Financial KPI Metric Strip (3 Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
        <!-- 1. Total Overdue Volume -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-rose-800">Total Delinquent Volume</span>
                <span class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-700 text-sm font-bold">
                    🚨
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-rose-600 tracking-tight">
                    ₹{{ number_format($totalOverdueAmount ?? 0, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-rose-800 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Action required for collection</span>
                </div>
            </div>
        </div>

        <!-- 2. Overdue Count -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Delinquent Invoices</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700 text-sm font-bold">
                    📋
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                    {{ $totalOverdueCount ?? $invoices->total() }}
                </div>
                <div class="text-[11px] font-semibold text-slate-600 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Past payment grace period</span>
                </div>
            </div>
        </div>

        <!-- 3. Critical Late (>30 Days) -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-purple-800">Critical Aging (>30 Days)</span>
                <span class="w-8 h-8 rounded-lg bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-700 text-sm font-bold">
                    ⏳
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-purple-700 tracking-tight">
                    ₹{{ number_format($criticalOverdueAmount ?? 0, 2) }}
                </div>
                <div class="text-[11px] font-semibold text-purple-800 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    <span>Severely overdue credit risks</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Search Bar -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4">
        <form method="GET" action="{{ route('organization.receivables.overdue_report') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search overdue invoices by invoice number or client name..."
                       class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm font-semibold text-slate-900 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white rounded-lg text-xs font-extrabold transition-all shadow-2xs flex items-center justify-center gap-1.5">
                    Search Overdue
                </button>
                @if(request()->filled('search'))
                <a href="{{ route('organization.receivables.overdue_report') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-all">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Overdue Invoices Aging Table -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="text-sm font-extrabold text-slate-950">Overdue Receivables Aging List</h3>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Sorted oldest due date first for prioritized recovery</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                {{ $invoices->total() }} {{ Str::plural('Invoice', $invoices->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4">Invoice #</th>
                        <th class="py-3 px-4">Debtor Party</th>
                        <th class="py-3 px-4">Due Date & Aging</th>
                        <th class="py-3 px-4 text-right">Balance Due</th>
                        <th class="py-3 px-4 text-right">Instant Reminders</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($invoices as $inv)
                    @php 
                        $daysOverdue = (int) now()->startOfDay()->diffInDays($inv->due_date); 
                        $balanceDue = max(0, $inv->grand_total - $inv->amount_paid);
                    @endphp
                    <tr class="hover:bg-rose-50/30 transition-colors group">
                        <!-- Invoice # -->
                        <td class="py-3.5 px-4">
                            <a href="{{ route('organization.invoices.show', $inv) }}" class="font-mono font-bold text-slate-900 hover:text-amber-600 transition-colors block">
                                {{ $inv->invoice_number }}
                            </a>
                            <div class="text-[11px] font-semibold text-slate-500 mt-0.5">
                                Issued: {{ $inv->invoice_date ? $inv->invoice_date->format('M d, Y') : '-' }}
                            </div>
                        </td>

                        <!-- Client -->
                        <td class="py-3.5 px-4">
                            @if($inv->client)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-slate-900 text-amber-400 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($inv->client->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('organization.clients.show', $inv->client) }}" class="font-extrabold text-slate-950 hover:text-amber-600 transition-colors block">
                                        {{ $inv->client->name }}
                                    </a>
                                    @if($inv->client->phone)
                                    <div class="text-[11px] font-medium text-slate-500">
                                        📞 {{ $inv->client->phone }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @else
                            <span class="font-semibold text-slate-500 italic">Walk-in Client</span>
                            @endif
                        </td>

                        <!-- Due Date & Aging Badge -->
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-rose-700">
                                {{ $inv->due_date ? $inv->due_date->format('M d, Y') : '-' }}
                            </div>
                            <div class="mt-1">
                                @if($daysOverdue >= 30)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-100 text-purple-800 border border-purple-300">
                                    <span>⏳</span>
                                    <span>{{ $daysOverdue }} days late (Critical)</span>
                                </span>
                                @elseif($daysOverdue >= 7)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                    <span>⚠</span>
                                    <span>{{ $daysOverdue }} days late</span>
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                    <span>⏱</span>
                                    <span>{{ $daysOverdue == 0 ? 'Due today' : $daysOverdue . ' days late' }}</span>
                                </span>
                                @endif
                            </div>
                        </td>

                        <!-- Balance Due -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="text-sm font-black text-rose-600 tracking-tight">
                                ₹{{ number_format($balanceDue, 2) }}
                            </div>
                            <div class="text-[10px] font-semibold text-slate-500">
                                Total: ₹{{ number_format($inv->grand_total, 2) }}
                            </div>
                        </td>

                        <!-- Instant Reminders -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Email Reminder Form -->
                                <form action="{{ route('organization.invoices.remind', $inv) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="channel" value="email">
                                    <button type="submit"
                                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-800 rounded-lg text-xs font-extrabold transition-all border border-slate-300 inline-flex items-center gap-1 shadow-2xs"
                                            title="Send Email Reminder Notice">
                                        <span>✉</span>
                                        <span>Email</span>
                                    </button>
                                </form>

                                <!-- WhatsApp Reminder Form -->
                                <form action="{{ route('organization.invoices.remind', $inv) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="channel" value="whatsapp">
                                    <button type="submit"
                                            class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 active:bg-emerald-200 text-emerald-800 rounded-lg text-xs font-extrabold transition-all border border-emerald-300 inline-flex items-center gap-1 shadow-2xs"
                                            title="Send WhatsApp Reminder Message">
                                        <span>📱</span>
                                        <span>WA</span>
                                    </button>
                                </form>

                                <!-- Copy Payment Link -->
                                <button type="button"
                                        onclick="copyPaymentLink('{{ $inv->id }}', this)"
                                        class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 active:bg-indigo-200 text-indigo-800 rounded-lg text-xs font-extrabold transition-all border border-indigo-300 inline-flex items-center gap-1 shadow-2xs"
                                        title="Copy Secure Public Payment Link">
                                    <span>🔗</span>
                                    <span>Link</span>
                                </button>
                            </div>
                        </td>

                        <!-- Action -->
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('organization.invoices.show', $inv) }}"
                               class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 rounded-lg transition-all text-xs font-extrabold shadow-2xs inline-flex items-center gap-1">
                                <span>Manage</span>
                                <span>&rarr;</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-16 px-4">
                            <div class="max-w-sm mx-auto flex flex-col items-center">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xl font-bold mb-3 shadow-2xs">
                                    ✓
                                </div>
                                <h4 class="text-sm font-extrabold text-slate-900">Zero Delinquent Invoices</h4>
                                <p class="text-xs text-slate-500 mt-1 text-center">
                                    Congratulations! There are currently no overdue invoices on file for this organization location.
                                </p>
                                @if(request()->filled('search'))
                                <a href="{{ route('organization.receivables.overdue_report') }}" class="mt-4 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-all shadow-2xs">
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

        @if($invoices->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Payment Link Copied Notification Toast -->
<div id="copyToast" class="fixed bottom-6 right-6 z-50 transform translate-y-10 opacity-0 pointer-events-none transition-all duration-200 bg-slate-950 text-white px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 text-xs font-extrabold border border-slate-700">
    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
    <span id="copyToastMsg">Payment link copied to clipboard!</span>
</div>

<script>
function copyPaymentLink(invoiceId, btnElement) {
    const originalContent = btnElement.innerHTML;
    btnElement.innerHTML = '<span>⏳</span><span>...</span>';
    btnElement.disabled = true;

    fetch(`/organization/invoices/${invoiceId}/payment-link`)
        .then(res => res.json())
        .then(data => {
            if (data && data.url) {
                navigator.clipboard.writeText(data.url).then(() => {
                    showToast('Secure payment link copied to clipboard!');
                }).catch(() => {
                    prompt('Copy this payment link:', data.url);
                });
            } else {
                alert('Could not generate payment link.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error fetching payment link.');
        })
        .finally(() => {
            btnElement.innerHTML = originalContent;
            btnElement.disabled = false;
        });
}

function showToast(message) {
    const toast = document.getElementById('copyToast');
    const toastMsg = document.getElementById('copyToastMsg');
    if (toast && toastMsg) {
        toastMsg.textContent = message;
        toast.classList.remove('translate-y-10', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0', 'pointer-events-none');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }, 3000);
    }
}
</script>
@endsection

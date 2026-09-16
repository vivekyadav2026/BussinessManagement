@extends('layouts.sme')

@section('title', 'Client Profile: ' . $client->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.clients.index') }}" class="hover:text-slate-900 transition-colors">Operations</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.clients.index') }}" class="hover:text-slate-900 transition-colors">Clients</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold truncate max-w-[200px]">{{ $client->name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    👥
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">{{ $client->name }}</h1>
                        @if($client->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Active Client</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                <span>Inactive Client</span>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        @if($client->phone) <span>📞 {{ $client->phone }}</span> @endif
                        @if($client->email) <span class="ml-2">✉️ {{ $client->email }}</span> @endif
                        @if($client->gst_number) <span class="ml-2 font-mono text-slate-800 font-bold">GSTIN: {{ $client->gst_number }}</span> @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Action CTAs -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.clients.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs rounded-lg transition shadow-2xs">
                &larr; Back to Directory
            </a>

            <a href="{{ route('organization.clients.edit', $client) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Profile</span>
            </a>

            <a href="{{ route('organization.invoices.create', ['client_id' => $client->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Create Invoice</span>
            </a>
        </div>
    </div>

    <!-- 2. KPI Financial Health Widgets -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Invoiced Volume -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Total Invoiced</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 border border-slate-200 flex items-center justify-center text-xs font-bold">💳</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">₹{{ number_format($client->total_purchased, 2) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Cumulative billing volume</p>
            </div>
        </div>

        <!-- Total Paid / Collected -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Settled &amp; Paid</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-xs font-bold">✓</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-emerald-700 font-mono tracking-tight">₹{{ number_format($client->total_paid, 2) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Cleared collections to date</p>
            </div>
        </div>

        <!-- Outstanding Due -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Outstanding Balance</span>
                <span class="w-7 h-7 rounded-lg {{ $client->outstanding_amount > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }} flex items-center justify-center text-xs font-bold">
                    {{ $client->outstanding_amount > 0 ? '⚠️' : '0' }}
                </span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black {{ $client->outstanding_amount > 0 ? 'text-rose-700' : 'text-slate-900' }} font-mono tracking-tight">
                    ₹{{ number_format($client->outstanding_amount, 2) }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Pending receivables balance</p>
            </div>
        </div>

        <!-- Overdue Balance -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Overdue Risk</span>
                <span class="w-7 h-7 rounded-lg {{ $client->overdue_amount > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }} flex items-center justify-center text-xs font-bold">
                    ⏳
                </span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black {{ $client->overdue_amount > 0 ? 'text-amber-800' : 'text-slate-900' }} font-mono tracking-tight">
                    ₹{{ number_format($client->overdue_amount, 2) }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Past contractual due date</p>
            </div>
        </div>
    </div>

    <!-- 3. Details Grid & Invoice Ledger -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Client Profile Details Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-2xs border border-slate-200/90 p-5 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-black text-xs">
                        📋
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider">Client Master Record</h3>
                </div>
                
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">GSTIN / Tax ID</span>
                        @if($client->gst_number)
                            <span class="font-mono text-xs font-bold text-slate-950 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 inline-block mt-0.5">
                                {{ $client->gst_number }}
                            </span>
                        @else
                            <span class="text-slate-400 italic">Not registered / Consumer</span>
                        @endif
                    </div>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Billing Address</span>
                        <div class="text-slate-800 font-medium whitespace-pre-wrap mt-0.5 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                            {{ $client->address ?: 'No physical address recorded.' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Internal Notes &amp; Comments</span>
                        <div class="text-slate-700 font-medium whitespace-pre-wrap mt-0.5 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                            {{ $client->notes ?: 'No internal notes on file.' }}
                        </div>
                    </div>
                    
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Account Registration</span>
                        <div class="text-slate-700 font-medium mt-0.5">
                            Member since {{ $client->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <a href="{{ route('organization.clients.edit', $client) }}" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-lg transition text-center block">
                        Edit Client Information
                    </a>
                </div>
            </div>
        </div>

        <!-- Invoices Ledger History Table -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-2xs border border-slate-200/90 overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📜</span>
                        <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider">Invoices &amp; Billing Ledger</h3>
                    </div>
                    <span class="text-xs font-bold text-slate-600 bg-white px-2.5 py-1 rounded-md border border-slate-200">
                        {{ $client->invoices->count() }} Invoices
                    </span>
                </div>
                
                @if($client->invoices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-950 text-white font-extrabold text-[11px] uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Invoice #</th>
                                <th class="py-3 px-4">Issue Date</th>
                                <th class="py-3 px-4">Due Date</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Amount Paid</th>
                                <th class="py-3 px-4 text-right">Grand Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-medium text-slate-800">
                            @foreach($client->invoices()->latest()->get() as $inv)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Invoice # -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <a href="{{ route('organization.invoices.show', $inv) }}" class="font-mono font-bold text-amber-600 hover:text-amber-700 hover:underline">
                                        {{ $inv->invoice_number }}
                                    </a>
                                </td>

                                <!-- Issue Date -->
                                <td class="py-3 px-4 whitespace-nowrap text-slate-700">
                                    {{ $inv->invoice_date ? $inv->invoice_date->format('d M Y') : '—' }}
                                </td>

                                <!-- Due Date -->
                                <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                                    {{ $inv->due_date ? $inv->due_date->format('d M Y') : '—' }}
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($inv->status === 'Paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-300">Paid</span>
                                    @elseif($inv->status === 'Due')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-blue-50 text-blue-800 border border-blue-300">Due</span>
                                    @elseif($inv->status === 'Overdue')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-rose-50 text-rose-800 border border-rose-300">Overdue</span>
                                    @elseif($inv->status === 'Partially Paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-amber-50 text-amber-900 border border-amber-300">Partial</span>
                                    @elseif($inv->status === 'Cancelled')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-300">Cancelled</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200">{{ $inv->status }}</span>
                                    @endif
                                </td>

                                <!-- Amount Paid -->
                                <td class="py-3 px-4 text-right whitespace-nowrap font-mono text-emerald-700 font-semibold">
                                    ₹{{ number_format($inv->amount_paid, 2) }}
                                </td>

                                <!-- Grand Total -->
                                <td class="py-3 px-4 text-right whitespace-nowrap font-mono text-slate-950 font-bold">
                                    ₹{{ number_format($inv->grand_total, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12 text-slate-500">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                        📜
                    </div>
                    <h4 class="text-sm font-extrabold text-slate-900">No Invoices Issued</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        No invoices have been billed to this client account yet.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('organization.invoices.create', ['client_id' => $client->id]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                            + Create First Invoice
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

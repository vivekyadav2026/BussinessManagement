@extends('layouts.sme')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('organization.clients.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $client->name }}</h1>
                <div class="text-sm text-gray-500 mt-1.5 flex items-center gap-4 font-medium">
                    @if($client->phone) <span class="flex items-center gap-1.5">📞 {{ $client->phone }}</span> @endif
                    @if($client->email) <span class="flex items-center gap-1.5">✉️ {{ $client->email }}</span> @endif
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('organization.clients.edit', $client) }}" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition shadow-sm">Edit Profile</a>
            <a href="{{ route('organization.invoices.create', ['client_id' => $client->id]) }}" class="px-4 py-2.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl font-semibold text-sm hover:opacity-95 shadow-sm transition">+ New Invoice</a>
        </div>
    </div>

    <!-- Stat Widgets -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Total Purchased</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">₹{{ number_format($client->total_purchased, 2) }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Total Paid</div>
            <div class="text-2xl font-bold text-emerald-600 mt-1">₹{{ number_format($client->total_paid, 2) }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Outstanding Due</div>
            <div class="text-2xl font-bold text-rose-605 text-rose-600 mt-1">₹{{ number_format($client->outstanding_amount, 2) }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Overdue Limit</div>
            <div class="text-2xl font-bold text-amber-600 mt-1">₹{{ number_format($client->overdue_amount, 2) }}</div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Client Details -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-50 pb-3">Client Details</h3>
                
                <div class="space-y-1">
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">GSTIN Number</div>
                    <div class="text-sm font-mono font-semibold {{ $client->gst_number ? 'text-gray-900' : 'text-gray-305 text-gray-400 italic' }}">{{ $client->gst_number ?? 'Not provided' }}</div>
                </div>

                <div class="space-y-1">
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Address</div>
                    <div class="text-sm text-gray-850 font-medium whitespace-pre-wrap">{{ $client->address ?? 'No address provided' }}</div>
                </div>

                <div class="space-y-1">
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Internal Notes</div>
                    <div class="text-sm text-gray-850 font-medium whitespace-pre-wrap">{{ $client->notes ?? '—' }}</div>
                </div>
                
                <div class="space-y-1.5">
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Status</div>
                    @if($client->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice History -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-50 pb-3">Invoice Ledger History</h3>
                
                @if($client->invoices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100 tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Invoice #</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Grand Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($client->invoices()->latest()->get() as $inv)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-700">{{ $inv->invoice_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3"><a href="{{ route('organization.invoices.show', $inv) }}" class="font-mono text-indigo-650 hover:underline font-bold">{{ $inv->invoice_number }}</a></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                        {{ $inv->status == 'Paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : '' }}
                                        {{ $inv->status == 'Draft' ? 'bg-gray-50 text-gray-600 border border-gray-200' : '' }}
                                        {{ $inv->status == 'Due' ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}
                                        {{ $inv->status == 'Overdue' ? 'bg-rose-50 text-rose-700 border border-rose-100' : '' }}
                                        {{ $inv->status == 'Partially Paid' ? 'bg-amber-50 text-amber-700 border border-amber-100' : '' }}
                                        {{ $inv->status == 'Cancelled' ? 'bg-gray-105 bg-gray-100 text-gray-500 border border-gray-200' : '' }}
                                    ">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">₹{{ number_format($inv->grand_total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="text-gray-400 font-medium">No invoices issued to this client yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

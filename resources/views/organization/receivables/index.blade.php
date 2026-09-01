@extends('layouts.sme')

@section('content')
<div class="dash-head flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
  <div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Receivables Dashboard</h1>
      <p class="text-xs text-slate-500 mt-1">Track outstanding credit sales, aging accounts, and payment collection.</p>
  </div>

  <div class="inline-flex gap-1 bg-slate-100 border border-slate-200 rounded-xl p-1 shadow-xs">
    <a href="{{ route('organization.receivables.index') }}" class="font-bold text-xs px-4 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.index') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Dashboard</a>
    <a href="{{ route('organization.receivables.client_report') }}" class="font-bold text-xs px-4 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.client_report') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Client Report</a>
    <a href="{{ route('organization.receivables.overdue_report') }}" class="font-bold text-xs px-4 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.overdue_report') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Overdue Aging</a>
  </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <div class="bg-white p-5 rounded-2xl border-l-4 border-slate-900 border-gray-100 shadow-xs flex flex-col justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Outstanding</span>
        <div class="text-2xl font-black text-slate-900 mt-1.5">₹{{ number_format($totalOutstanding, 2) }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border-l-4 border-rose-500 border-gray-100 shadow-xs flex flex-col justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Overdue</span>
        <div class="text-2xl font-black text-rose-600 mt-1.5">₹{{ number_format($totalOverdue, 2) }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border-l-4 border-amber-500 border-gray-100 shadow-xs flex flex-col justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Due Today</span>
        <div class="text-2xl font-black text-amber-600 mt-1.5">₹{{ number_format($dueToday, 2) }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border-l-4 border-emerald-500 border-gray-100 shadow-xs flex flex-col justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Due This Week</span>
        <div class="text-2xl font-black text-emerald-600 mt-1.5">₹{{ number_format($dueThisWeek, 2) }}</div>
    </div>
</div>

<!-- Dynamic Filter Panel -->
<div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs mb-6">
    <form method="GET" action="{{ route('organization.receivables.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Filter Client</label>
            <select name="client_id" class="w-full border-gray-300 rounded-xl text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-2.5">
                <option value="">All Clients</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-56">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
            <select name="status" class="w-full border-gray-300 rounded-xl text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-2.5">
                <option value="">All Unpaid Statuses</option>
                <option value="Due" {{ request('status') == 'Due' ? 'selected' : '' }}>Due</option>
                <option value="Partially Paid" {{ request('status') == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="Overdue" {{ request('status') == 'Overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs transition flex-1 md:flex-none justify-center">Filter</button>
            @if(request()->hasAny(['client_id', 'status']))
                <a href="{{ route('organization.receivables.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Clear</a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">Invoice #</th>
                    <th class="py-3.5 px-4">Client Name</th>
                    <th class="py-3.5 px-4">Due Date</th>
                    <th class="py-3.5 px-4 text-right">Balance Due</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-xs font-medium">
                @forelse($invoices as $inv)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="py-3.5 px-4 font-bold font-mono">
                        <a href="{{ route('organization.invoices.show', $inv) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline">{{ $inv->invoice_number }}</a>
                    </td>
                    <td class="py-3.5 px-4 font-bold text-slate-900">
                        {{ $inv->client ? $inv->client->name : 'Walk-in Client' }}
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="{{ $inv->due_date < now()->startOfDay() ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                            {{ $inv->due_date ? $inv->due_date->format('M d, Y') : '-' }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">₹{{ number_format($inv->amount_due, 2) }}</td>
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $inv->due_date < now()->startOfDay() ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($inv->status === 'Partially Paid' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                            {{ $inv->due_date < now()->startOfDay() ? 'Overdue' : $inv->status }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-right">
                        <a href="{{ route('organization.invoices.show', $inv) }}" class="p-1.5 inline-flex items-center gap-1 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition text-xs font-bold" title="Manage Invoice">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Manage</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-slate-400 font-medium">No outstanding receivables found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</div>
@endsection

@extends('layouts.sme')

@section('content')
<div class="dash-head flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
  <div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Receivables Dashboard</h1>
      <p class="text-xs text-slate-500 mt-1">Track outstanding payments and party-wise balance summaries.</p>
  </div>

  <div class="inline-flex gap-1 bg-slate-100 border border-slate-200 rounded-xl p-1 shadow-xs">
    <a href="{{ route('organization.receivables.index') }}" class="font-bold text-xs px-4 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.index') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Dashboard</a>
    <a href="{{ route('organization.receivables.client_report') }}" class="font-bold text-xs px-4 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.client_report') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Client Report</a>
    <a href="{{ route('organization.receivables.overdue_report') }}" class="font-bold text-xs px-4 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.overdue_report') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Overdue Aging</a>
  </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">Client Name</th>
                    <th class="py-3.5 px-4 text-center">Unpaid Invoices</th>
                    <th class="py-3.5 px-4 text-right">Total Outstanding</th>
                    <th class="py-3.5 px-4 text-right">Overdue Amount</th>
                    <th class="py-3.5 px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-xs font-medium">
                @forelse($clients as $c)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="py-3.5 px-4 font-bold">
                        <a href="{{ route('organization.clients.show', $c) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline">{{ $c->name }}</a>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full text-xs font-bold border border-slate-200">{{ $c->invoice_count }}</span>
                    </td>
                    <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">₹{{ number_format($c->total_outstanding, 2) }}</td>
                    <td class="py-3.5 px-4 text-right font-bold">
                        @if($c->total_overdue > 0)
                            <span class="text-rose-600 font-extrabold">₹{{ number_format($c->total_overdue, 2) }}</span>
                        @else
                            <span class="text-slate-300 font-normal">-</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 text-right">
                        <a href="{{ route('organization.receivables.index', ['client_id' => $c->id]) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition text-xs font-bold inline-block">
                            View Invoices &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-slate-400 font-medium">No outstanding client balances found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

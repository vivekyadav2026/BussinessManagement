@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Receivables Dashboard</h1>
      <p class="text-gray-500 mt-1">Track outstanding payments and send reminders.</p>
  </div>
</div>

<div class="mb-6 flex justify-between items-center">
  <div class="flex gap-1 bg-[var(--border-color)] border border-[var(--border-hard)] rounded-full p-1">
    <a href="{{ route('organization.receivables.index') }}" class="font-bold text-xs px-4 py-2 rounded-full transition-all {{ request()->routeIs('organization.receivables.index') ? 'bg-[var(--text-main)] text-white font-semibold' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]' }}">Dashboard</a>
    <a href="{{ route('organization.receivables.client_report') }}" class="font-bold text-xs px-4 py-2 rounded-full transition-all {{ request()->routeIs('organization.receivables.client_report') ? 'bg-[var(--text-main)] text-white font-semibold' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]' }}">Client Report</a>
    <a href="{{ route('organization.receivables.overdue_report') }}" class="font-bold text-xs px-4 py-2 rounded-full transition-all {{ request()->routeIs('organization.receivables.overdue_report') ? 'bg-[var(--text-main)] text-white font-semibold' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]' }}">Overdue Aging</a>
  </div>
</div>

<div class="panel p-6 shadow-sm">
    <div class="overflow-x-auto">
        <table class="inv-table w-full">
            <thead>
                <tr>
                    <th class="text-left">Client Name</th>
                    <th class="text-center">Unpaid Invoices</th>
                    <th class="text-right">Total Outstanding</th>
                    <th class="text-right">Overdue Amount</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $c)
                <tr>
                    <td class="font-bold"><a href="{{ route('organization.clients.show', $c) }}" class="text-indigo-600 hover:underline">{{ $c->name }}</a></td>
                    <td class="text-center"><span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full text-xs font-bold">{{ $c->invoice_count }}</span></td>
                    <td class="text-right font-bold text-gray-900">₹{{ number_format($c->total_outstanding, 2) }}</td>
                    <td class="text-right font-bold">
                        @if($c->total_overdue > 0)
                            <span class="text-[var(--rose)]">₹{{ number_format($c->total_overdue, 2) }}</span>
                        @else
                            <span class="text-gray-400 font-normal">-</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('organization.receivables.index', ['client_id' => $c->id]) }}" class="btn btn-ghost py-1 px-3 text-xs">View Invoices</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-6 text-gray-500">No outstanding client balances.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

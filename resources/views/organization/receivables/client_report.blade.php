@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Receivables Dashboard</h1>
      <p class="text-gray-500 mt-1">Track outstanding payments and send reminders.</p>
  </div>
</div>

<div class="mb-6 border-b border-gray-200">
  <nav class="-mb-px flex space-x-8">
    <a href="{{ route('organization.receivables.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Dashboard</a>
    <a href="{{ route('organization.receivables.client_report') }}" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Client Report</a>
    <a href="{{ route('organization.receivables.overdue_report') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Overdue Aging</a>
  </nav>
</div>

<div class="panel">
    <table class="inv-table w-full">
        <thead>
            <tr>
                <th>Client Name</th>
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
                <td class="text-center"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">{{ $c->invoice_count }}</span></td>
                <td class="text-right font-bold text-gray-900">₹{{ number_format($c->total_outstanding, 2) }}</td>
                <td class="text-right">
                    @if($c->total_overdue > 0)
                        <span class="text-red-600 font-bold">₹{{ number_format($c->total_overdue, 2) }}</span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="text-right">
                    <a href="{{ route('organization.receivables.index', ['client_id' => $c->id]) }}" class="text-indigo-600 text-xs font-medium hover:underline">View Invoices</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-6 text-gray-500">No outstanding client balances.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

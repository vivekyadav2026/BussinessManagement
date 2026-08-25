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
    <a href="{{ route('organization.receivables.index') }}" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Dashboard</a>
    <a href="{{ route('organization.receivables.client_report') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Client Report</a>
    <a href="{{ route('organization.receivables.overdue_report') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Overdue Aging</a>
  </nav>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="panel bg-white border-l-4 border-indigo-500">
        <div class="text-xs text-gray-500 uppercase font-bold mb-1">Total Outstanding</div>
        <div class="text-2xl font-black text-gray-900">₹{{ number_format($totalOutstanding, 2) }}</div>
    </div>
    <div class="panel bg-white border-l-4 border-red-500">
        <div class="text-xs text-gray-500 uppercase font-bold mb-1">Total Overdue</div>
        <div class="text-2xl font-black text-red-600">₹{{ number_format($totalOverdue, 2) }}</div>
    </div>
    <div class="panel bg-white border-l-4 border-orange-500">
        <div class="text-xs text-gray-500 uppercase font-bold mb-1">Due Today</div>
        <div class="text-2xl font-black text-orange-600">₹{{ number_format($dueToday, 2) }}</div>
    </div>
    <div class="panel bg-white border-l-4 border-green-500">
        <div class="text-xs text-gray-500 uppercase font-bold mb-1">Due This Week</div>
        <div class="text-2xl font-black text-green-600">₹{{ number_format($dueThisWeek, 2) }}</div>
    </div>
</div>

<div class="panel mb-6">
    <form method="GET" action="{{ route('organization.receivables.index') }}" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Client</label>
            <select name="client_id" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All Clients</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Status</label>
            <select name="status" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All Unpaid</option>
                <option value="Due" {{ request('status') == 'Due' ? 'selected' : '' }}>Due</option>
                <option value="Partially Paid" {{ request('status') == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="Overdue" {{ request('status') == 'Overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
        </div>
        <button type="submit" class="btn btn-gold py-2">Filter</button>
        @if(request()->hasAny(['client_id', 'status']))
            <a href="{{ route('organization.receivables.index') }}" class="btn bg-gray-100 text-gray-600 py-2">Clear</a>
        @endif
    </form>
</div>

<div class="panel">
    <table class="inv-table w-full">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Client</th>
                <th>Due Date</th>
                <th class="text-right">Balance Due</th>
                <th>Status</th>
                <th class="text-right">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr>
                <td class="font-bold"><a href="{{ route('organization.invoices.show', $inv) }}" class="text-indigo-600 hover:underline">{{ $inv->invoice_number }}</a></td>
                <td>{{ $inv->client->name }}</td>
                <td>
                    <span class="{{ $inv->due_date < now()->startOfDay() ? 'text-red-600 font-bold' : '' }}">
                        {{ $inv->due_date ? $inv->due_date->format('M d, Y') : '-' }}
                    </span>
                </td>
                <td class="text-right font-bold text-gray-900">₹{{ number_format($inv->amount_due, 2) }}</td>
                <td>
                    <span class="px-2 py-0.5 rounded text-xs font-bold {{ $inv->due_date < now()->startOfDay() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $inv->due_date < now()->startOfDay() ? 'Overdue' : $inv->status }}
                    </span>
                </td>
                <td class="text-right">
                    <a href="{{ route('organization.invoices.show', $inv) }}" class="btn bg-white border border-gray-300 btn-sm text-xs text-gray-700">Manage</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-6 text-gray-500">No outstanding invoices found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $invoices->links() }}</div>
</div>
@endsection

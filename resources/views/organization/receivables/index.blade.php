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

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="panel bg-white p-5 border-l-4 border-[var(--gold)] flex flex-col justify-between shadow-sm">
        <span class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Total Outstanding</span>
        <div class="text-2xl font-black text-[var(--text-main)] mt-1">₹{{ number_format($totalOutstanding, 2) }}</div>
    </div>
    <div class="panel bg-white p-5 border-l-4 border-[var(--rose)] flex flex-col justify-between shadow-sm">
        <span class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Total Overdue</span>
        <div class="text-2xl font-black text-[var(--rose)] mt-1">₹{{ number_format($totalOverdue, 2) }}</div>
    </div>
    <div class="panel bg-white p-5 border-l-4 border-[var(--gold-deep)] flex flex-col justify-between shadow-sm">
        <span class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Due Today</span>
        <div class="text-2xl font-black text-[var(--gold-deep)] mt-1">₹{{ number_format($dueToday, 2) }}</div>
    </div>
    <div class="panel bg-white p-5 border-l-4 border-[var(--teal)] flex flex-col justify-between shadow-sm">
        <span class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Due This Week</span>
        <div class="text-2xl font-black text-[var(--teal)] mt-1">₹{{ number_format($dueThisWeek, 2) }}</div>
    </div>
</div>

<div class="panel mb-6 p-6 shadow-sm">
    <form method="GET" action="{{ route('organization.receivables.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Client</label>
            <select name="client_id" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All Clients</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-56">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All Unpaid</option>
                <option value="Due" {{ request('status') == 'Due' ? 'selected' : '' }}>Due</option>
                <option value="Partially Paid" {{ request('status') == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="Overdue" {{ request('status') == 'Overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit" class="btn btn-gold py-2.5 px-5 flex-1 md:flex-none justify-center">Filter</button>
            @if(request()->hasAny(['client_id', 'status']))
                <a href="{{ route('organization.receivables.index') }}" class="btn bg-gray-100 text-gray-600 py-2.5 px-4 justify-center">Clear</a>
            @endif
        </div>
    </form>
</div>

<div class="panel p-6 shadow-sm">
    <div class="overflow-x-auto">
        <table class="inv-table w-full">
            <thead>
                <tr>
                    <th class="text-left">Invoice #</th>
                    <th class="text-left">Client</th>
                    <th class="text-left">Due Date</th>
                    <th class="text-right">Balance Due</th>
                    <th class="text-left">Status</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td class="font-bold"><a href="{{ route('organization.invoices.show', $inv) }}" class="text-indigo-600 hover:underline">{{ $inv->invoice_number }}</a></td>
                    <td>{{ $inv->client ? $inv->client->name : 'Walk-in Client' }}</td>
                    <td>
                        <span class="{{ $inv->due_date < now()->startOfDay() ? 'text-red-600 font-bold' : '' }}">
                            {{ $inv->due_date ? $inv->due_date->format('M d, Y') : '-' }}
                        </span>
                    </td>
                    <td class="text-right font-bold text-gray-900">₹{{ number_format($inv->amount_due, 2) }}</td>
                    <td>
                        <span class="px-2.5 py-1 rounded text-xs font-bold {{ $inv->due_date < now()->startOfDay() ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                            {{ $inv->due_date < now()->startOfDay() ? 'Overdue' : $inv->status }}
                        </span>
                    </td>
                    <td class="text-right">
                        <a href="{{ route('organization.invoices.show', $inv) }}" class="btn btn-ghost py-1 px-3 text-xs">Manage</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-6 text-gray-500">No outstanding invoices found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</div>
@endsection

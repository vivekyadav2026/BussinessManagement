@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Invoices</h1>
      <p class="text-gray-500 mt-1">Manage billing for {{ \App\Models\Location::find(\App\Services\LocationManager::getActiveLocationId())->name ?? 'your active branch' }}.</p>
  </div>
  <a class="btn btn-gold btn-sm" href="{{ route('organization.invoices.create') }}">+ Create Invoice</a>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<div class="panel mb-6">
    <form method="GET" action="{{ route('organization.invoices.index') }}" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice number or client name..." class="w-full border-gray-300 rounded-lg text-sm">
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All Statuses</option>
                @foreach(['Draft', 'Paid', 'Partially Paid', 'Due', 'Overdue', 'Cancelled'] as $st)
                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-gold py-2">Filter</button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('organization.invoices.index') }}" class="btn bg-gray-100 text-gray-600 py-2">Clear</a>
        @endif
    </form>
</div>

<div class="panel">
  <table class="inv-table w-full">
    <thead>
      <tr>
        <th>Invoice Number</th>
        <th>Client</th>
        <th>Date</th>
        <th>Amount</th>
        <th>Status</th>
        <th class="text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($invoices as $inv)
      <tr>
        <td class="font-bold text-gray-900"><a href="{{ route('organization.invoices.show', $inv) }}" class="hover:text-indigo-600">{{ $inv->invoice_number }}</a></td>
        <td>
            <a href="{{ route('organization.clients.show', $inv->client_id) }}" class="text-indigo-600 hover:underline font-medium">{{ $inv->client->name }}</a>
        </td>
        <td class="text-sm text-gray-600">
            <div>{{ $inv->invoice_date->format('M d, Y') }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Due: {{ $inv->due_date ? $inv->due_date->format('M d, Y') : 'N/A' }}</div>
        </td>
        <td>
            <div class="font-bold text-gray-900">₹{{ number_format($inv->grand_total, 2) }}</div>
            @if($inv->amount_due > 0 && $inv->status != 'Draft' && $inv->status != 'Cancelled')
                <div class="text-xs font-medium text-red-500 mt-0.5">Due: ₹{{ number_format($inv->amount_due, 2) }}</div>
            @endif
        </td>
        <td>
            <span class="px-2 py-0.5 rounded text-xs font-medium 
                {{ $inv->status == 'Paid' ? 'bg-green-100 text-green-800' : '' }}
                {{ $inv->status == 'Draft' ? 'bg-gray-100 text-gray-800' : '' }}
                {{ $inv->status == 'Due' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $inv->status == 'Overdue' ? 'bg-red-100 text-red-800' : '' }}
                {{ $inv->status == 'Partially Paid' ? 'bg-orange-100 text-orange-800' : '' }}
                {{ $inv->status == 'Cancelled' ? 'bg-gray-200 text-gray-600' : '' }}
            ">
                {{ $inv->status }}
            </span>
        </td>
        <td class="text-right">
            <a href="{{ route('organization.invoices.show', $inv) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">View</a>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center py-6 text-gray-500">No invoices found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
  
  <div class="mt-4">
      {{ $invoices->links() }}
  </div>
</div>
@endsection

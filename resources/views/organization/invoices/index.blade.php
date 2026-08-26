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

<!-- Stats KPI Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="panel bg-white p-5 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Collected</span>
            <div class="text-2xl font-black text-green-600 mt-1">₹{{ number_format($stats['paid_sum'], 2) }}</div>
        </div>
        <div class="p-3 bg-green-50 text-green-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>
    
    <div class="panel bg-white p-5 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending Receivables</span>
            <div class="text-2xl font-black text-amber-500 mt-1">₹{{ number_format($stats['unpaid_sum'], 2) }}</div>
        </div>
        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>

    <div class="panel bg-white p-5 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Overdue Invoices</span>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['overdue_count'] }} <span class="text-xs font-medium text-gray-400">unresolved</span></div>
        </div>
        <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
    </div>
</div>

<div class="panel mb-6 shadow-sm p-6">
    <form method="GET" action="{{ route('organization.invoices.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Search Invoice</label>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by invoice # or client name..." class="w-full pl-9 border-gray-300 rounded-lg text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="w-full md:w-56">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All Statuses</option>
                @foreach(['Draft', 'Paid', 'Partially Paid', 'Due', 'Overdue', 'Cancelled'] as $st)
                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit" class="btn btn-gold py-2.5 px-5 flex-1 md:flex-none justify-center">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('organization.invoices.index') }}" class="btn bg-gray-100 text-gray-600 py-2.5 px-4 justify-center">Clear</a>
            @endif
        </div>
    </form>
</div>

<div class="panel overflow-hidden shadow-sm">
  <table class="inv-table w-full">
    <thead>
      <tr class="bg-gray-50 border-b border-gray-100 text-gray-600">
        <th class="py-3.5 px-4 text-left font-bold text-xs uppercase tracking-wider">Invoice Details</th>
        <th class="py-3.5 px-4 text-left font-bold text-xs uppercase tracking-wider">Client</th>
        <th class="py-3.5 px-4 text-left font-bold text-xs uppercase tracking-wider">Dates</th>
        <th class="py-3.5 px-4 text-left font-bold text-xs uppercase tracking-wider">Total Amount</th>
        <th class="py-3.5 px-4 text-left font-bold text-xs uppercase tracking-wider">Status</th>
        <th class="py-3.5 px-4 text-right font-bold text-xs uppercase tracking-wider">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 bg-white">
      @forelse($invoices as $inv)
      <tr class="hover:bg-gray-50/50 transition-colors">
        <td class="py-4 px-4">
            <div class="font-bold text-gray-900 text-sm">
                <a href="{{ route('organization.invoices.show', $inv) }}" class="hover:text-indigo-600 transition-colors">{{ $inv->invoice_number }}</a>
            </div>
            <div class="text-[10px] text-gray-400 font-mono mt-0.5">ID: {{ str_pad($inv->id, 6, '0', STR_PAD_LEFT) }}</div>
        </td>
        <td class="py-4 px-4 text-sm">
            @if($inv->client)
                <div class="font-semibold text-gray-800">
                    <a href="{{ route('organization.clients.show', $inv->client_id) }}" class="text-indigo-600 hover:underline">{{ $inv->client->name }}</a>
                </div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $inv->client->phone ?? 'No Phone' }}</div>
            @else
                <span class="text-gray-500 italic text-xs bg-gray-100 px-2 py-0.5 rounded-full font-medium">Walk-in Client</span>
            @endif
        </td>
        <td class="py-4 px-4 text-sm text-gray-600">
            <div class="font-medium">{{ $inv->invoice_date->format('M d, Y') }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Due: {{ $inv->due_date ? $inv->due_date->format('M d, Y') : 'N/A' }}</div>
        </td>
        <td class="py-4 px-4 text-sm">
            <div class="font-bold text-gray-900">₹{{ number_format($inv->grand_total, 2) }}</div>
            @if($inv->amount_due > 0 && $inv->status != 'Draft' && $inv->status != 'Cancelled')
                <div class="text-xs font-bold text-rose-500 mt-0.5">Unpaid: ₹{{ number_format($inv->amount_due, 2) }}</div>
            @endif
        </td>
        <td class="py-4 px-4">
            <span class="px-2.5 py-1 rounded-full text-xs font-bold inline-block
                {{ $inv->status == 'Paid' ? 'bg-green-50 text-green-700 border border-green-200' : '' }}
                {{ $inv->status == 'Draft' ? 'bg-gray-50 text-gray-600 border border-gray-200' : '' }}
                {{ $inv->status == 'Due' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                {{ $inv->status == 'Overdue' ? 'bg-rose-50 text-rose-700 border border-rose-200 animate-pulse' : '' }}
                {{ $inv->status == 'Partially Paid' ? 'bg-orange-50 text-orange-700 border border-orange-200' : '' }}
                {{ $inv->status == 'Cancelled' ? 'bg-gray-100 text-gray-500 border border-gray-300' : '' }}
            ">
                {{ $inv->status }}
            </span>
        </td>
        <td class="py-4 px-4 text-right">
            <div class="inline-flex gap-2">
                <a href="{{ route('organization.invoices.show', $inv) }}" class="p-1 text-gray-400 hover:text-indigo-600 hover:bg-gray-100 rounded-lg transition-colors" title="View details">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </a>
                <a href="{{ route('organization.invoices.print', $inv) }}" target="_blank" class="p-1 text-gray-400 hover:text-green-600 hover:bg-gray-100 rounded-lg transition-colors" title="Print Invoice">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                </a>
            </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center py-10 text-gray-400 text-sm">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            No invoices found. Click **Create Invoice** to get started.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
  
  @if($invoices->hasPages())
  <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
      {{ $invoices->links() }}
  </div>
  @endif
</div>
@endsection

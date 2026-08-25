@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <a href="{{ route('organization.clients.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Clients</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h1>
        <div class="text-sm text-gray-500 mt-1 flex items-center gap-4">
            @if($client->phone) <span>📞 {{ $client->phone }}</span> @endif
            @if($client->email) <span>✉️ {{ $client->email }}</span> @endif
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('organization.clients.edit', $client) }}" class="btn border border-gray-300 text-gray-700 bg-white btn-sm">Edit Profile</a>
        <a href="{{ route('organization.invoices.create', ['client_id' => $client->id]) }}" class="btn btn-gold btn-sm">+ New Invoice</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="panel text-center">
        <div class="text-xs text-gray-500 uppercase tracking-wide font-bold mb-1">Total Purchased</div>
        <div class="text-2xl font-black text-gray-800">₹{{ number_format($client->total_purchased, 2) }}</div>
    </div>
    <div class="panel text-center">
        <div class="text-xs text-gray-500 uppercase tracking-wide font-bold mb-1">Total Paid</div>
        <div class="text-2xl font-black text-green-600">₹{{ number_format($client->total_paid, 2) }}</div>
    </div>
    <div class="panel text-center">
        <div class="text-xs text-gray-500 uppercase tracking-wide font-bold mb-1">Outstanding</div>
        <div class="text-2xl font-black text-red-600">₹{{ number_format($client->outstanding_amount, 2) }}</div>
    </div>
    <div class="panel text-center">
        <div class="text-xs text-gray-500 uppercase tracking-wide font-bold mb-1">Overdue Amount</div>
        <div class="text-2xl font-black text-orange-600">₹{{ number_format($client->overdue_amount, 2) }}</div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="panel col-span-1">
        <h3 class="font-bold text-gray-900 mb-4 border-b pb-2">Client Details</h3>
        
        <div class="mb-4">
            <div class="text-xs text-gray-500">GST Number</div>
            <div class="font-mono text-sm {{ $client->gst_number ? 'text-gray-900' : 'text-gray-400' }}">{{ $client->gst_number ?? 'Not provided' }}</div>
        </div>

        <div class="mb-4">
            <div class="text-xs text-gray-500">Address</div>
            <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $client->address ?? 'No address provided' }}</div>
        </div>

        <div class="mb-4">
            <div class="text-xs text-gray-500">Internal Notes</div>
            <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $client->notes ?? '-' }}</div>
        </div>
        
        <div>
            <div class="text-xs text-gray-500 mb-1">Status</div>
            @if($client->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
            @endif
        </div>
    </div>

    <div class="panel col-span-2">
        <h3 class="font-bold text-gray-900 mb-4 border-b pb-2">Invoice History</h3>
        
        @if($client->invoices->count() > 0)
        <table class="inv-table w-full">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Invoice #</th>
                    <th>Status</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->invoices()->latest()->get() as $inv)
                <tr>
                    <td class="text-sm">{{ $inv->invoice_date->format('M d, Y') }}</td>
                    <td><a href="{{ route('organization.invoices.show', $inv) }}" class="font-mono text-indigo-600 hover:underline">{{ $inv->invoice_number }}</a></td>
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
                    <td class="text-right font-bold text-sm">₹{{ number_format($inv->grand_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center py-12">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p class="text-gray-500 font-medium">No invoices found for this client.</p>
        </div>
        @endif
        
    </div>
</div>
@endsection

@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
      <p class="text-gray-500 mt-1">Manage your customer database globally.</p>
  </div>
  <a class="btn btn-gold btn-sm" href="{{ route('organization.clients.create') }}">+ Add Client</a>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<div class="panel mb-6">
    <form method="GET" action="{{ route('organization.clients.index') }}" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, email, GST..." class="w-full border-gray-300 rounded-lg text-sm">
        </div>
        <button type="submit" class="btn btn-gold py-2">Filter</button>
        @if(request('search'))
            <a href="{{ route('organization.clients.index') }}" class="btn bg-gray-100 text-gray-600 py-2">Clear</a>
        @endif
    </form>
</div>

<div class="panel">
  <table class="inv-table w-full">
    <thead>
      <tr>
        <th>Client Name</th>
        <th>Contact Info</th>
        <th>Financial Status</th>
        <th>Status</th>
        <th class="text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($clients as $client)
      <tr>
        <td>
            <div class="font-bold text-gray-900">{{ $client->name }}</div>
            @if($client->gst_number)
            <div class="text-xs text-gray-500 mt-1">GST: <span class="font-mono">{{ $client->gst_number }}</span></div>
            @endif
        </td>
        <td>
            <div class="text-sm text-gray-700">{{ $client->phone ?? 'N/A' }}</div>
            <div class="text-xs text-gray-500">{{ $client->email ?? 'N/A' }}</div>
        </td>
        <td>
            <div class="text-xs text-gray-500">Purchased: ₹{{ number_format($client->total_purchased, 2) }}</div>
            @if($client->outstanding_amount > 0)
                <div class="text-sm font-bold text-red-600 mt-1">Due: ₹{{ number_format($client->outstanding_amount, 2) }}</div>
            @else
                <div class="text-sm font-bold text-green-600 mt-1">Settled</div>
            @endif
        </td>
        <td>
            @if($client->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
            @endif
        </td>
        <td class="text-right">
            <a href="{{ route('organization.clients.show', $client) }}" class="text-gray-600 hover:text-gray-900 font-medium text-xs mr-3">View</a>
            <a href="{{ route('organization.clients.edit', $client) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Edit</a>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center py-6 text-gray-500">No clients found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
  
  <div class="mt-4">
      {{ $clients->links() }}
  </div>
</div>
@endsection

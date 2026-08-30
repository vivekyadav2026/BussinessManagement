@extends('layouts.sme')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Clients</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage customer database details, financial health statuses, and invoice tracking logs.</p>
        </div>
        <a href="{{ route('organization.clients.create') }}" class="px-4 py-2.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl font-semibold text-sm hover:opacity-95 shadow-sm transition">+ Add Client</a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Search Filter -->
        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
            <form method="GET" action="{{ route('organization.clients.index') }}" class="flex gap-3 w-full max-w-lg">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, email, GST..." class="border border-gray-200 focus:border-[var(--theme-active)] rounded-xl px-4 py-2 text-sm w-full outline-none transition">
                <button type="submit" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition shadow-sm">Filter</button>
                @if(request('search'))
                    <a href="{{ route('organization.clients.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-750 text-sm flex items-center font-medium">Clear</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Client Name</th>
                        <th class="px-6 py-4">Contact Info</th>
                        <th class="px-6 py-4">Financial Status</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($clients as $client)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $client->name }}</div>
                            @if($client->gst_number)
                                <div class="text-[11px] text-gray-400 font-mono mt-0.5">GST: {{ $client->gst_number }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-800 font-medium">{{ $client->phone ?? '—' }}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">{{ $client->email ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-500 font-medium">Purchased: ₹{{ number_format($client->total_purchased, 2) }}</div>
                            @if($client->outstanding_amount > 0)
                                <div class="text-xs font-bold text-rose-600 mt-1">Due: ₹{{ number_format($client->outstanding_amount, 2) }}</div>
                            @else
                                <div class="text-xs font-bold text-emerald-600 mt-1">Settled / Paid</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($client->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('organization.clients.show', $client) }}" class="text-green-600 hover:text-green-900 font-bold text-xs">View</a>
                                <span class="text-gray-250">|</span>
                                <a href="{{ route('organization.clients.edit', $client) }}" class="text-indigo-600 hover:text-indigo-950 font-bold text-xs">Edit</a>
                                @if($client->invoices()->count() === 0)
                                    <span class="text-gray-250">|</span>
                                    <form action="{{ route('organization.clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');" style="display:inline-block; margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-655 hover:text-red-900 font-bold text-xs">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-gray-400 font-medium">No clients found in the database directory.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.sme')

@section('title', 'Client Directory')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Operations</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Customer Relationship</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Clients</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    👥
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Client Directory</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Manage corporate client accounts, GST profiles, purchase history, and outstanding receivables ledger.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Actions (Capacity & Add CTA) -->
        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-900 border border-slate-300 shadow-2xs">
                <span class="text-slate-500 font-medium">Capacity:</span>
                <span>{{ $totalClients ?? 0 }} / {{ is_numeric($maxClients) ? $maxClients : '∞' }}</span>
                @if(($limitReached ?? false) && Route::has('organization.subscription.index'))
                    <span class="ml-1 text-rose-700 font-extrabold">(Limit Reached)</span>
                @endif
            </div>

            @if(!($limitReached ?? false))
                <a href="{{ route('organization.clients.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add New Client</span>
                </a>
            @else
                <a href="{{ route('organization.subscription.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-lg shadow-xs transition" title="Client limit reached. Upgrade subscription plan.">
                    <span>Upgrade to Add More &rarr;</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-300 text-emerald-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-emerald-700 text-base">✓</span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-rose-700 text-base">⚠️</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 2. KPI Metric Cards Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Clients -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Total Directory</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 border border-slate-200 flex items-center justify-center text-xs font-bold">👥</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ number_format($totalClients ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Enrolled clients & customer accounts</p>
            </div>
        </div>

        <!-- Active Clients -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Active Accounts</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-xs font-bold">🟢</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-emerald-700 font-mono tracking-tight">{{ number_format($activeClientsCount ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Eligible for instant billing & orders</p>
            </div>
        </div>

        <!-- Inactive Clients -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Inactive Accounts</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center text-xs font-bold">⏸️</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-700 font-mono tracking-tight">{{ number_format($inactiveClientsCount ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Dormant or paused clients</p>
            </div>
        </div>

        <!-- Total Receivables Balance -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Open Receivables</span>
                <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center text-xs font-bold">₹</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-rose-700 font-mono tracking-tight">₹{{ number_format($totalReceivables ?? 0, 2) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Unpaid & overdue client balances</p>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar & Search Toolbar -->
    <div class="bg-white border border-slate-200/90 rounded-xl p-4 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <!-- Search Input Form -->
        <form method="GET" action="{{ route('organization.clients.index') }}" class="flex-1 flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search by client name, phone number, email, or GSTIN..."
                    class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-xs sm:text-sm font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-slate-900 placeholder:text-slate-400">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <button type="submit" class="px-4 py-2 bg-slate-950 hover:bg-slate-800 text-white font-bold text-xs rounded-lg shadow-2xs transition">
                Search
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('organization.clients.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Clear
                </a>
            @endif
        </form>

        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg border border-slate-200 shrink-0 self-start md:self-auto overflow-x-auto">
            <a href="{{ route('organization.clients.index', array_merge(request()->except('status', 'page'), [])) }}" 
               class="px-3 py-1 rounded text-xs font-extrabold transition {{ !request('status') ? 'bg-white text-slate-950 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                All ({{ $totalClients ?? 0 }})
            </a>
            <a href="{{ route('organization.clients.index', array_merge(request()->except('status', 'page'), ['status' => 'active'])) }}" 
               class="px-3 py-1 rounded text-xs font-extrabold transition {{ request('status') === 'active' ? 'bg-white text-slate-950 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                Active ({{ $activeClientsCount ?? 0 }})
            </a>
            <a href="{{ route('organization.clients.index', array_merge(request()->except('status', 'page'), ['status' => 'inactive'])) }}" 
               class="px-3 py-1 rounded text-xs font-extrabold transition {{ request('status') === 'inactive' ? 'bg-white text-slate-950 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                Inactive ({{ $inactiveClientsCount ?? 0 }})
            </a>
        </div>
    </div>

    <!-- 4. High-Contrast Clients Master Table -->
    <div class="bg-white border border-slate-200/90 rounded-xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-950 text-white font-extrabold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Client Profile</th>
                        <th class="py-3.5 px-4">Contact Coordinates</th>
                        <th class="py-3.5 px-4">Tax / GSTIN</th>
                        <th class="py-3.5 px-4">Invoices &amp; Ledger</th>
                        <th class="py-3.5 px-4 text-right">Receivables Due</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium text-slate-800">
                    @forelse($clients as $client)
                    @php
                        $outstanding = $client->outstanding_amount;
                        $hasDue = $outstanding > 0;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- Client Profile -->
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center font-black text-xs shrink-0">
                                    {{ strtoupper(substr($client->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('organization.clients.show', $client) }}" class="font-extrabold text-slate-950 text-sm hover:text-amber-600 transition truncate max-w-[200px] block">
                                        {{ $client->name }}
                                    </a>
                                    @if($client->address)
                                        <div class="text-[11px] text-slate-500 font-medium mt-0.5 truncate max-w-[200px]">
                                            📍 {{ $client->address }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Contact Coordinates -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($client->phone)
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>📞</span>
                                    <span>{{ $client->phone }}</span>
                                </div>
                            @else
                                <div class="text-xs text-slate-400 italic">No phone</div>
                            @endif

                            @if($client->email)
                                <div class="text-[11px] text-slate-600 font-medium mt-0.5 flex items-center gap-1.5">
                                    <span>✉️</span>
                                    <span>{{ $client->email }}</span>
                                </div>
                            @endif
                        </td>

                        <!-- Tax / GSTIN -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($client->gst_number)
                                <span class="inline-flex items-center gap-1 font-mono text-[11px] font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                    {{ $client->gst_number }}
                                </span>
                            @else
                                <span class="text-slate-400 text-[11px] italic">Consumer / Unregistered</span>
                            @endif
                        </td>

                        <!-- Invoices & Total Volume -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="text-xs text-slate-900 font-bold">
                                {{ $client->invoices_count }} {{ Str::plural('Invoice', $client->invoices_count) }}
                            </div>
                            <div class="text-[11px] text-slate-500 font-medium mt-0.5">
                                Vol: ₹{{ number_format($client->total_purchased, 2) }}
                            </div>
                        </td>

                        <!-- Financial Due Status -->
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            @if($hasDue)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-black bg-rose-50 text-rose-700 border border-rose-200 font-mono">
                                    ₹{{ number_format($outstanding, 2) }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-black bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    ✓ Settled
                                </span>
                            @endif
                        </td>

                        <!-- Status Pill -->
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($client->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    <span>Active</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                    <span>Inactive</span>
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Create Invoice Quick Shortcut -->
                                <a href="{{ route('organization.invoices.create', ['client_id' => $client->id]) }}" 
                                    class="p-1.5 text-slate-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition" 
                                    title="Create Invoice for Client">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </a>

                                <!-- View Profile -->
                                <a href="{{ route('organization.clients.show', $client) }}" 
                                    class="p-1.5 text-slate-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg border border-transparent hover:border-blue-200 transition" 
                                    title="View Client Details &amp; History">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <!-- Edit Profile -->
                                <a href="{{ route('organization.clients.edit', $client) }}" 
                                    class="p-1.5 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg border border-transparent hover:border-indigo-200 transition" 
                                    title="Edit Client Info">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                <!-- Delete Profile (Safe only if 0 invoices) -->
                                @if($client->invoices_count === 0)
                                    <form action="{{ route('organization.clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete client \'{{ addslashes($client->name) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition" title="Delete Client">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="p-1.5 text-slate-300 cursor-not-allowed" title="Cannot delete: associated invoices exist in ledger">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-3.5v-1a7 7 0 00-14 0v1m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0H5"/></svg>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                👥
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900">No Clients Found</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                @if(request('search') || request('status'))
                                    No client records match your search filter criteria.
                                @else
                                    Start building your customer catalog to generate GST tax invoices and track outstanding receivables.
                                @endif
                            </p>
                            @if(!($limitReached ?? false))
                                <div class="mt-4">
                                    <a href="{{ route('organization.clients.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                                        + Add First Client
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/70">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

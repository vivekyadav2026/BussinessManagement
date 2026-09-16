@extends('layouts.sme')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <span>Operations & Support</span>
                <span>/</span>
                <span class="text-slate-900 font-bold">Helpdesk Tickets</span>
            </div>
            <h1 class="text-2xl font-black text-slate-950 tracking-tight">Customer Complaints & Helpdesk</h1>
            <p class="text-xs text-slate-600 mt-0.5">Track resolution SLAs, customer disputes, and service escalation tickets.</p>
        </div>

        @can('complaints.create')
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.complaints.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-xl shadow-xs transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Log New Ticket</span>
            </a>
        </div>
        @endcan
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-950 flex items-center justify-between text-xs font-bold shadow-2xs">
        <div class="flex items-center gap-2.5">
            <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-800 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950">&times;</button>
    </div>
    @endif

    <!-- Executive KPI Strip -->
    @php
        $orgId = auth()->user()->organization_id;
        $totalTickets = $complaints->total();
        $openCount = \App\Models\Complaint::where('organization_id', $orgId)->where('status', 'Open')->count();
        $progressCount = \App\Models\Complaint::where('organization_id', $orgId)->where('status', 'In Progress')->count();
        $resolvedCount = \App\Models\Complaint::where('organization_id', $orgId)->where('status', 'Resolved')->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Total Tickets</span>
                <span class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2">{{ number_format($totalTickets) }}</div>
            <p class="text-[11px] font-semibold text-slate-500 mt-1">Logged across all time</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-700">Open Tickets</span>
                <span class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2">{{ number_format($openCount) }}</div>
            <p class="text-[11px] font-semibold text-amber-700 mt-1">Awaiting staff pickup</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-blue-700">In Progress</span>
                <span class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2">{{ number_format($progressCount) }}</div>
            <p class="text-[11px] font-semibold text-blue-700 mt-1">Active investigations</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-700">Resolved</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2">{{ number_format($resolvedCount) }}</div>
            <p class="text-[11px] font-semibold text-emerald-700 mt-1">Closed successfully</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4">
        <form action="{{ route('organization.complaints.index') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 flex-1">
                <!-- Status Filter -->
                <div>
                    <label class="block text-[10px] font-black text-slate-700 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2 px-3 bg-white">
                        <option value="">All Statuses</option>
                        <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>

                <!-- Priority Filter -->
                <div>
                    <label class="block text-[10px] font-black text-slate-700 uppercase tracking-wider mb-1">Priority</label>
                    <select name="priority" class="w-full border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2 px-3 bg-white">
                        <option value="">All Priorities</option>
                        <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                        <option value="Urgent" {{ request('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <!-- Assignee Filter -->
                <div>
                    <label class="block text-[10px] font-black text-slate-700 uppercase tracking-wider mb-1">Assignee</label>
                    <select name="assigned_to" class="w-full border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2 px-3 bg-white">
                        <option value="">All Staff</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('assigned_to') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Client Filter -->
                <div>
                    <label class="block text-[10px] font-black text-slate-700 uppercase tracking-wider mb-1">Client</label>
                    <select name="client_id" class="w-full border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2 px-3 bg-white">
                        <option value="">All Clients</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 shrink-0 pt-2 lg:pt-4">
                <button type="submit" class="px-4 py-2 bg-slate-950 hover:bg-slate-900 active:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs transition whitespace-nowrap">
                    Apply Filter
                </button>
                @if(request()->hasAny(['status', 'priority', 'assigned_to', 'client_id']))
                    <a href="{{ route('organization.complaints.index') }}" class="px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- High-Contrast Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-black text-slate-700 uppercase tracking-wider">
                        <th class="py-3.5 px-4 whitespace-nowrap">Ticket ID</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Subject & Details</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Client / Account</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Priority</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Status</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Assigned Agent</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Logged Date</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                    @forelse($complaints as $complaint)
                        <tr class="hover:bg-slate-50/60 transition group">
                            <!-- Ticket ID -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-mono font-extrabold text-slate-950 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg">
                                    #CMP-{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <!-- Subject -->
                            <td class="py-3.5 px-4 max-w-xs sm:max-w-md">
                                <a href="{{ route('organization.complaints.show', $complaint) }}" class="font-bold text-slate-950 hover:text-amber-700 block truncate transition">
                                    {{ $complaint->subject }}
                                </a>
                                <span class="text-[11px] font-medium text-slate-500 line-clamp-1 mt-0.5">
                                    {{ Str::limit($complaint->description, 60) }}
                                </span>
                            </td>

                            <!-- Client -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">
                                    {{ $complaint->client->name ?? 'Internal / Store Issue' }}
                                </div>
                                @if($complaint->client && $complaint->client->phone)
                                    <div class="text-[11px] font-medium text-slate-500 font-mono">{{ $complaint->client->phone }}</div>
                                @endif
                            </td>

                            <!-- Priority -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($complaint->priority === 'Urgent')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-950 border border-rose-300 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        Urgent
                                    </span>
                                @elseif($complaint->priority === 'High')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-950 border border-amber-300 whitespace-nowrap">
                                        High
                                    </span>
                                @elseif($complaint->priority === 'Medium')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-950 border border-blue-300 whitespace-nowrap">
                                        Medium
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-medium uppercase tracking-wider bg-slate-100 text-slate-800 border border-slate-200 whitespace-nowrap">
                                        Low
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($complaint->status === 'Resolved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-950 border border-emerald-300 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Resolved
                                    </span>
                                @elseif($complaint->status === 'In Progress')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-blue-50 text-blue-950 border border-blue-300 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                        In Progress
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-950 border border-amber-300 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                        Open
                                    </span>
                                @endif
                            </td>

                            <!-- Assigned Staff -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($complaint->assignee)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center text-[10px] font-black shrink-0">
                                            {{ strtoupper(substr($complaint->assignee->first_name, 0, 1) . substr($complaint->assignee->last_name, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-900">{{ $complaint->assignee->first_name }} {{ $complaint->assignee->last_name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 font-medium italic">Unassigned</span>
                                @endif
                            </td>

                            <!-- Created Date -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 font-mono text-[11px]">
                                {{ $complaint->created_at->format('M d, Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('organization.complaints.show', $complaint) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-amber-400 hover:bg-amber-50/50 text-slate-800 hover:text-amber-900 text-xs font-bold transition whitespace-nowrap">
                                    <span>Manage</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900">No support tickets found</h3>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">There are no complaints matching your selected criteria or logged yet.</p>
                                @can('complaints.create')
                                    <div class="mt-4">
                                        <a href="{{ route('organization.complaints.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-xs transition whitespace-nowrap">
                                            + Log Support Ticket
                                        </a>
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($complaints->hasPages())
        <div class="p-4 border-t border-slate-200/80 bg-slate-50/50">
            {{ $complaints->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

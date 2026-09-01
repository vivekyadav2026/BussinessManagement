@extends('layouts.sme')

@section('content')
<div class="dash-head flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Customer Complaints & Helpdesk</h1>
        <p class="text-xs text-slate-500 mt-1">Manage customer support tickets, disputes, and service escalation.</p>
    </div>
    @can('complaints.create')
    <a href="{{ route('organization.complaints.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>+ Log New Ticket</span>
    </a>
    @endcan
</div>

@if(session('success'))
<div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl mb-6 border border-emerald-200 text-sm font-semibold shadow-xs flex items-center gap-2">
    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Helpdesk Ticket KPI Banner -->
@php
    $totalTickets = $complaints->total();
    $openCount = \App\Models\Complaint::where('organization_id', auth()->user()->organization_id)->where('status', 'Open')->count();
    $progressCount = \App\Models\Complaint::where('organization_id', auth()->user()->organization_id)->where('status', 'In Progress')->count();
    $resolvedCount = \App\Models\Complaint::where('organization_id', auth()->user()->organization_id)->where('status', 'Resolved')->count();
@endphp
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <div class="bg-white p-5 rounded-2xl border-l-4 border-slate-900 border-gray-100 shadow-xs flex flex-col justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Support Tickets</span>
        <div class="text-2xl font-black text-slate-900 mt-1.5">{{ $totalTickets }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border-l-4 border-amber-500 border-gray-100 shadow-xs flex flex-col justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Open Tickets</span>
        <div class="text-2xl font-black text-amber-600 mt-1.5">{{ $openCount }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border-l-4 border-blue-500 border-gray-100 shadow-xs flex flex-col justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">In Progress</span>
        <div class="text-2xl font-black text-blue-600 mt-1.5">{{ $progressCount }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border-l-4 border-emerald-500 border-gray-100 shadow-xs flex flex-col justify-between">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Resolved Tickets</span>
        <div class="text-2xl font-black text-emerald-600 mt-1.5">{{ $resolvedCount }}</div>
    </div>
</div>

<!-- Filter Box -->
<div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs mb-6">
    <form action="{{ route('organization.complaints.index') }}" method="GET" class="flex flex-col md:flex-row flex-wrap gap-4 items-end">
        <div class="w-full md:w-36">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
            <select name="status" class="w-full border-gray-300 rounded-xl text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-2.5">
                <option value="">All Statuses</option>
                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
        </div>
        <div class="w-full md:w-36">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Priority</label>
            <select name="priority" class="w-full border-gray-300 rounded-xl text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-2.5">
                <option value="">All Priorities</option>
                <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                <option value="Urgent" {{ request('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Assignee</label>
            <select name="assigned_to" class="w-full border-gray-300 rounded-xl text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-2.5">
                <option value="">All Assignees</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('assigned_to') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Client</label>
            <select name="client_id" class="w-full border-gray-300 rounded-xl text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-2.5">
                <option value="">All Clients</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs transition flex-1 md:flex-none justify-center">Filter</button>
            @if(request()->hasAny(['status', 'priority', 'assigned_to', 'client_id']))
                <a href="{{ route('organization.complaints.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Clear</a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">Ticket ID</th>
                    <th class="py-3.5 px-4">Subject</th>
                    <th class="py-3.5 px-4">Client Name</th>
                    <th class="py-3.5 px-4">Priority</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4">Assigned Staff</th>
                    <th class="py-3.5 px-4">Created Date</th>
                    <th class="py-3.5 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-xs font-medium">
                @forelse($complaints as $complaint)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-indigo-600">#CMP-{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">
                            <a href="{{ route('organization.complaints.show', $complaint) }}" class="hover:text-indigo-600 hover:underline">{{ $complaint->subject }}</a>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-700">{{ $complaint->client->name ?? 'Internal Store Issue' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                {{ $complaint->priority === 'Urgent' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                {{ $complaint->priority === 'High' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                {{ $complaint->priority === 'Medium' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : '' }}
                                {{ $complaint->priority === 'Low' ? 'bg-slate-100 text-slate-700 border border-slate-200' : '' }}
                            ">{{ $complaint->priority }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                {{ $complaint->status === 'Resolved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                {{ $complaint->status === 'In Progress' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                {{ $complaint->status === 'Open' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                            ">{{ $complaint->status }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                            {{ $complaint->assignee ? $complaint->assignee->first_name . ' ' . $complaint->assignee->last_name : 'Unassigned' }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-400">{{ $complaint->created_at->format('M d, Y') }}</td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('organization.complaints.show', $complaint) }}" class="p-1.5 inline-flex items-center gap-1 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition text-xs font-bold" title="Manage Ticket">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>View</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400 font-medium">No customer complaints or tickets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $complaints->links() }}
    </div>
</div>
@endsection

@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Complaints & Tickets</h1>
        <p class="text-gray-500 mt-1">Track and manage customer disputes, inquiries, and staff feedback.</p>
    </div>
    @can('complaints.create')
    <a href="{{ route('organization.complaints.create') }}" class="btn btn-gold py-2.5 px-6 shadow-sm">Log Complaint</a>
    @endcan
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<!-- Filter -->
<div class="panel mb-6 p-6 shadow-sm">
    <form action="{{ route('organization.complaints.index') }}" method="GET" class="flex flex-col md:flex-row flex-wrap gap-4 items-end">
        <div class="w-full md:w-36">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All</option>
                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
        </div>
        <div class="w-full md:w-36">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Priority</label>
            <select name="priority" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All</option>
                <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                <option value="Urgent" {{ request('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Assignee</label>
            <select name="assigned_to" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('assigned_to') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Client</label>
            <select name="client_id" class="w-full border-gray-300 rounded-lg text-sm">
                <option value="">All</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit" class="btn btn-gold py-2.5 px-5 flex-1 md:flex-none justify-center">Filter</button>
            @if(request()->hasAny(['status', 'priority', 'assigned_to', 'client_id']))
                <a href="{{ route('organization.complaints.index') }}" class="btn bg-gray-100 text-gray-600 py-2.5 px-4 justify-center">Clear</a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="panel p-6 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="inv-table w-full">
            <thead>
                <tr>
                    <th class="text-left">Ticket ID</th>
                    <th class="text-left">Subject</th>
                    <th class="text-left">Client</th>
                    <th class="text-left">Priority</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Assignee</th>
                    <th class="text-left">Created</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($complaints as $complaint)
                    <tr>
                        <td class="font-mono text-xs text-gray-500">#{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="font-bold text-gray-900">{{ $complaint->subject }}</td>
                        <td>{{ $complaint->client->name ?? 'Internal' }}</td>
                        <td>
                            <span class="px-2 py-0.5 rounded text-xs font-bold
                                {{ $complaint->priority === 'Urgent' ? 'bg-red-50 text-red-700 border border-red-100' : '' }}
                                {{ $complaint->priority === 'High' ? 'bg-orange-50 text-orange-700 border border-orange-100' : '' }}
                                {{ $complaint->priority === 'Medium' ? 'bg-yellow-50 text-yellow-700 border border-yellow-100' : '' }}
                                {{ $complaint->priority === 'Low' ? 'bg-gray-50 text-gray-700 border border-gray-100' : '' }}
                            ">{{ $complaint->priority }}</span>
                        </td>
                        <td>
                            <span class="px-2 py-0.5 rounded text-xs font-bold
                                {{ $complaint->status === 'Resolved' ? 'bg-green-50 text-green-700 border border-green-100' : '' }}
                                {{ $complaint->status === 'In Progress' ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}
                                {{ $complaint->status === 'Open' ? 'bg-gray-50 text-gray-700 border border-gray-100' : '' }}
                            ">{{ $complaint->status }}</span>
                        </td>
                        <td>{{ $complaint->assignee ? $complaint->assignee->first_name . ' ' . $complaint->assignee->last_name : 'Unassigned' }}</td>
                        <td class="font-mono text-xs text-gray-500">{{ $complaint->created_at->format('M d, Y') }}</td>
                        <td class="text-right">
                            <a href="{{ route('organization.complaints.show', $complaint) }}" class="btn btn-ghost py-1 px-3 text-xs">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-4 text-center text-gray-500">No complaints found.</td>
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

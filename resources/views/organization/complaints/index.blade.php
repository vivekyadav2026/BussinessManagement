@extends('layouts.sme')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Complaints & Tickets</h1>
        @can('complaints.create')
        <a href="{{ route('organization.complaints.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Log Complaint</a>
        @endcan
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <!-- Filter -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <form action="{{ route('organization.complaints.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm text-gray-600">Status</label>
                <select name="status" class="border rounded px-3 py-2 w-32">
                    <option value="">All</option>
                    <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                    <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600">Priority</label>
                <select name="priority" class="border rounded px-3 py-2 w-32">
                    <option value="">All</option>
                    <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                    <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                    <option value="Urgent" {{ request('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600">Assignee</label>
                <select name="assigned_to" class="border rounded px-3 py-2 w-48">
                    <option value="">All</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('assigned_to') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600">Client</label>
                <select name="client_id" class="border rounded px-3 py-2 w-48">
                    <option value="">All</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>
            <a href="{{ route('organization.complaints.index') }}" class="text-gray-500 underline ml-2">Clear</a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">Ticket ID</th>
                    <th class="p-3">Subject</th>
                    <th class="p-3">Client</th>
                    <th class="p-3">Priority</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Assignee</th>
                    <th class="p-3">Created</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($complaints as $complaint)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-gray-500">#{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="p-3 font-medium">{{ $complaint->subject }}</td>
                        <td class="p-3">{{ $complaint->client->name ?? 'Internal' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs
                                {{ $complaint->priority === 'Urgent' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $complaint->priority === 'High' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $complaint->priority === 'Medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $complaint->priority === 'Low' ? 'bg-gray-100 text-gray-700' : '' }}
                            ">{{ $complaint->priority }}</span>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs
                                {{ $complaint->status === 'Resolved' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $complaint->status === 'In Progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $complaint->status === 'Open' ? 'bg-gray-100 text-gray-700' : '' }}
                            ">{{ $complaint->status }}</span>
                        </td>
                        <td class="p-3">{{ $complaint->assignee ? $complaint->assignee->first_name . ' ' . $complaint->assignee->last_name : 'Unassigned' }}</td>
                        <td class="p-3 text-gray-500">{{ $complaint->created_at->format('M d, Y') }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('organization.complaints.show', $complaint) }}" class="text-blue-600 hover:underline">View</a>
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

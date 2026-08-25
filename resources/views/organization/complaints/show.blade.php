@extends('layouts.sme')

@section('content')
<div class="p-4 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Ticket #{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <a href="{{ route('organization.complaints.index') }}" class="text-blue-600 hover:underline">&larr; Back to Tickets</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2">
            <!-- Ticket Content -->
            <div class="bg-white p-6 rounded shadow border mb-6">
                <h2 class="text-xl font-bold mb-2">{{ $complaint->subject }}</h2>
                <div class="flex gap-4 text-sm text-gray-500 mb-6 border-b pb-4">
                    <span><strong>Reported By:</strong> {{ $complaint->reporter ? $complaint->reporter->first_name . ' ' . $complaint->reporter->last_name : 'System/Admin' }}</span>
                    <span><strong>Client:</strong> {{ $complaint->client->name ?? 'None' }}</span>
                    <span><strong>Date:</strong> {{ $complaint->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="prose max-w-none text-gray-800 whitespace-pre-wrap">{{ $complaint->description }}</div>
            </div>
        </div>

        <div class="col-span-1">
            <!-- Ticket Settings -->
            <div class="bg-gray-50 p-6 rounded shadow border">
                <h3 class="font-bold text-lg mb-4 border-b pb-2">Properties</h3>
                <form action="{{ route('organization.complaints.update', $complaint) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border rounded px-3 py-2">
                            <option value="Open" {{ $complaint->status == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="In Progress" {{ $complaint->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved" {{ $complaint->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Priority</label>
                        <select name="priority" class="w-full border rounded px-3 py-2">
                            <option value="Low" {{ $complaint->priority == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ $complaint->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ $complaint->priority == 'High' ? 'selected' : '' }}>High</option>
                            <option value="Urgent" {{ $complaint->priority == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Assignee</label>
                        <select name="assigned_to" class="w-full border rounded px-3 py-2">
                            <option value="">-- Unassigned --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $complaint->assigned_to == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded shadow w-full hover:bg-gray-900">Update Ticket</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

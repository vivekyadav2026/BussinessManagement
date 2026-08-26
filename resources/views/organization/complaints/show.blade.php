@extends('layouts.sme')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Ticket #{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <a href="{{ route('organization.complaints.index') }}" class="text-indigo-600 hover:underline text-sm">&larr; Back to Tickets</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-6 border border-red-200 text-sm font-medium">
            <ul>
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <!-- Ticket Content -->
            <div class="panel p-6 shadow-sm mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $complaint->subject }}</h2>
                <div class="flex flex-wrap gap-4 text-xs text-gray-500 mb-6 border-b border-gray-100 pb-4">
                    <span><strong>Reported By:</strong> {{ $complaint->reporter ? $complaint->reporter->first_name . ' ' . $complaint->reporter->last_name : 'System/Admin' }}</span>
                    <span><strong>Client:</strong> {{ $complaint->client->name ?? 'None' }}</span>
                    <span class="font-mono"><strong>Date:</strong> {{ $complaint->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="prose max-w-none text-gray-800 whitespace-pre-wrap text-sm leading-relaxed">{{ $complaint->description }}</div>
            </div>
        </div>

        <div class="md:col-span-1">
            <!-- Ticket Settings -->
            <div class="panel p-6 shadow-sm">
                <h3 class="font-bold text-lg text-gray-900 mb-4 border-b border-gray-100 pb-2">Properties</h3>
                <form action="{{ route('organization.complaints.update', $complaint) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="Open" {{ $complaint->status == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="In Progress" {{ $complaint->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved" {{ $complaint->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Priority</label>
                        <select name="priority" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="Low" {{ $complaint->priority == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ $complaint->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ $complaint->priority == 'High' ? 'selected' : '' }}>High</option>
                            <option value="Urgent" {{ $complaint->priority == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Assignee</label>
                        <select name="assigned_to" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="">-- Unassigned --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $complaint->assigned_to == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-gold py-2.5 px-6 w-full justify-center">Update Ticket</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

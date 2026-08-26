@extends('layouts.sme')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Log Complaint</h1>
        <a href="{{ route('organization.complaints.index') }}" class="text-indigo-600 hover:underline text-sm">&larr; Back</a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-6 border border-red-200 text-sm font-medium">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel p-6 shadow-sm">
        <form action="{{ route('organization.complaints.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="w-full border rounded px-3 py-2 text-sm" required placeholder="Brief description of the issue">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Description *</label>
                <textarea name="description" rows="5" class="w-full border rounded px-3 py-2 text-sm" required placeholder="Detailed information about the complaint">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Client (Optional)</label>
                    <select name="client_id" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">-- Internal Issue --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Priority *</label>
                    <select name="priority" class="w-full border rounded px-3 py-2 text-sm" required>
                        <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                        <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Assign To (Optional)</label>
                <select name="assigned_to" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">-- Unassigned --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('assigned_to') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-gold py-2.5 px-6 w-full justify-center text-lg">Create Ticket</button>
        </form>
    </div>
</div>
@endsection

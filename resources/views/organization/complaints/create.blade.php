@extends('layouts.sme')

@section('content')
<div class="p-4 max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Log Complaint</h1>
        <a href="{{ route('organization.complaints.index') }}" class="text-blue-600 hover:underline">&larr; Back</a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded shadow border">
        <form action="{{ route('organization.complaints.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Description *</label>
                <textarea name="description" rows="5" class="w-full border rounded px-3 py-2" required>{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Client (Optional)</label>
                    <select name="client_id" class="w-full border rounded px-3 py-2">
                        <option value="">-- Internal Issue --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Priority *</label>
                    <select name="priority" class="w-full border rounded px-3 py-2" required>
                        <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                        <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Assign To (Optional)</label>
                <select name="assigned_to" class="w-full border rounded px-3 py-2">
                    <option value="">-- Unassigned --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('assigned_to') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700 w-full text-lg">Create Ticket</button>
        </form>
    </div>
</div>
@endsection

@extends('layouts.sme')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="dash-head flex items-center gap-3">
        <a href="{{ route('organization.complaints.index') }}" class="p-2.5 rounded-xl border border-gray-200 bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-xs transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Log New Complaint Ticket</h1>
            <p class="text-xs text-slate-500 mt-0.5">Register a new customer support ticket or escalation inquiry.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 text-rose-800 px-4 py-3 rounded-xl border border-rose-200 text-xs font-semibold shadow-xs">
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <form action="{{ route('organization.complaints.store') }}" method="POST" class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        @csrf
        
        <div class="p-6 space-y-5">
            <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span>
                <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Ticket Information Details</h2>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Subject / Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 outline-none transition @error('subject') border-rose-300 @enderror" required placeholder="e.g. Defective screen on Monitor #INV-00002">
                    @error('subject') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Detailed Description <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="4" class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-900 outline-none transition @error('description') border-rose-300 @enderror" required placeholder="Provide complete details about the customer complaint or issue">{{ old('description') }}</textarea>
                    @error('description') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Customer / Client (Optional)</label>
                        <select name="client_id" class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-3 py-2.5 text-xs font-semibold text-slate-800 outline-none transition">
                            <option value="">-- Internal Store Issue --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('client_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Priority Level <span class="text-rose-500">*</span></label>
                        <select name="priority" class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 outline-none transition" required>
                            <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low Priority</option>
                            <option value="Medium" {{ old('priority', 'Medium') == 'Medium' ? 'selected' : '' }}>Medium Priority</option>
                            <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High Priority</option>
                            <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent Priority</option>
                        </select>
                        @error('priority') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Assign To Staff Member (Optional)</label>
                    <select name="assigned_to" class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-3 py-2.5 text-xs font-semibold text-slate-800 outline-none transition">
                        <option value="">-- Unassigned --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('assigned_to') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->designation ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                    @error('assigned_to') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Footer Action Buttons -->
        <div class="bg-slate-50 border-t border-gray-100 px-6 py-4 flex justify-end gap-3">
            <a href="{{ route('organization.complaints.index') }}" class="px-5 py-2.5 border border-gray-300 text-slate-700 bg-white hover:bg-slate-50 rounded-xl font-bold text-xs shadow-xs transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-md transition">Create Support Ticket</button>
        </div>
    </form>
</div>
@endsection

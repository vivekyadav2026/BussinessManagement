@extends('layouts.sme')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="dash-head flex justify-between items-center">
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.complaints.index') }}" class="p-2.5 rounded-xl border border-gray-200 bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-xs transition" title="Back to Tickets">
                &larr;
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Support Ticket #CMP-{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Logged on {{ $complaint->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
        
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                {{ $complaint->status === 'Resolved' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : '' }}
                {{ $complaint->status === 'In Progress' ? 'bg-blue-100 text-blue-800 border border-blue-300' : '' }}
                {{ $complaint->status === 'Open' ? 'bg-amber-100 text-amber-800 border border-amber-300' : '' }}
            ">Status: {{ $complaint->status }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl border border-emerald-200 text-sm font-semibold shadow-xs flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Ticket Issue Details Card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs space-y-5">
                <div class="border-b border-gray-100 pb-4">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Issue Subject</span>
                    <h2 class="text-xl font-extrabold text-slate-900 mt-1">{{ $complaint->subject }}</h2>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl text-xs font-medium">
                    <div>
                        <span class="text-slate-400 uppercase text-[10px] font-bold block">Client Name</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $complaint->client->name ?? 'Internal Store Issue' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase text-[10px] font-bold block">Reported By</span>
                        <span class="font-bold text-slate-800">{{ $complaint->reporter ? $complaint->reporter->first_name . ' ' . $complaint->reporter->last_name : 'System Administrator' }}</span>
                    </div>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Detailed Issue Description</span>
                    <div class="p-4 bg-slate-50/70 border border-slate-200 rounded-xl text-slate-800 text-xs leading-relaxed font-medium whitespace-pre-wrap">
                        {{ $complaint->description }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Properties & Status Management -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs space-y-4">
                <h3 class="font-bold text-sm text-slate-900 border-b border-gray-100 pb-3 uppercase tracking-wider">Ticket Management</h3>
                
                <form action="{{ route('organization.complaints.update', $complaint) }}" method="POST" class="space-y-4">
                    @csrf 
                    @method('PUT')
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Update Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="Open" {{ $complaint->status == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="In Progress" {{ $complaint->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved" {{ $complaint->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Priority Level</label>
                        <select name="priority" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="Low" {{ $complaint->priority == 'Low' ? 'selected' : '' }}>Low Priority</option>
                            <option value="Medium" {{ $complaint->priority == 'Medium' ? 'selected' : '' }}>Medium Priority</option>
                            <option value="High" {{ $complaint->priority == 'High' ? 'selected' : '' }}>High Priority</option>
                            <option value="Urgent" {{ $complaint->priority == 'Urgent' ? 'selected' : '' }}>Urgent Priority</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Assigned Staff Member</label>
                        <select name="assigned_to" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">-- Unassigned --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $complaint->assigned_to == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition">Update Ticket Properties</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

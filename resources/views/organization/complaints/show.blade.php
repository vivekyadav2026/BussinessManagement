@extends('layouts.sme')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.complaints.index') }}" 
               class="p-2.5 rounded-xl border border-slate-200/90 bg-white text-slate-700 hover:text-slate-950 hover:bg-slate-50 shadow-2xs transition">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-0.5">
                    <span>Helpdesk</span>
                    <span>/</span>
                    <span class="text-slate-900 font-bold">Ticket Details</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-slate-950 tracking-tight">
                        #CMP-{{ str_pad($complaint->id, 5, '0', STR_PAD_LEFT) }}
                    </h1>
                    <!-- Status Badge -->
                    @if($complaint->status === 'Resolved')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-950 border border-emerald-300 whitespace-nowrap">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Resolved
                        </span>
                    @elseif($complaint->status === 'In Progress')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-blue-50 text-blue-950 border border-blue-300 whitespace-nowrap">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            In Progress
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-50 text-amber-950 border border-amber-300 whitespace-nowrap">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                            Open
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('organization.complaints.index') }}" 
               class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-2xs transition whitespace-nowrap">
                Back to Tickets
            </a>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Issue Details (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-6">
                <!-- Subject & Priority Strip -->
                <div class="border-b border-slate-100 pb-5">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Issue Subject</span>
                        <!-- Priority Pill -->
                        @if($complaint->priority === 'Urgent')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-950 border border-rose-300 whitespace-nowrap">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                Urgent Priority
                            </span>
                        @elseif($complaint->priority === 'High')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-950 border border-amber-300 whitespace-nowrap">
                                High Priority
                            </span>
                        @elseif($complaint->priority === 'Medium')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-950 border border-blue-300 whitespace-nowrap">
                                Medium Priority
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-medium uppercase tracking-wider bg-slate-100 text-slate-800 border border-slate-200 whitespace-nowrap">
                                Low Priority
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl font-black text-slate-950 leading-snug">{{ $complaint->subject }}</h2>
                    <div class="text-[11px] font-medium text-slate-500 mt-2 flex items-center gap-2">
                        <span>Logged {{ $complaint->created_at->format('M d, Y h:i A') }}</span>
                        <span>&bull;</span>
                        <span>{{ $complaint->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Client & Reporter Meta Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Client Details Card -->
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-4">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">Customer / Client Account</span>
                        <div class="font-black text-slate-950 text-sm">
                            {{ $complaint->client->name ?? 'Internal Store / Facility Issue' }}
                        </div>
                        @if($complaint->client)
                            <div class="mt-2 space-y-0.5 text-xs text-slate-600 font-medium">
                                @if($complaint->client->phone)
                                    <div class="flex items-center gap-1.5 font-mono">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>{{ $complaint->client->phone }}</span>
                                    </div>
                                @endif
                                @if($complaint->client->email)
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span>{{ $complaint->client->email }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Reporter Info Card -->
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-4">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">Logged By</span>
                        <div class="font-black text-slate-950 text-sm">
                            {{ $complaint->reporter ? $complaint->reporter->first_name . ' ' . $complaint->reporter->last_name : 'System Administrator' }}
                        </div>
                        <div class="mt-2 text-xs text-slate-600 font-medium">
                            @if($complaint->reporter && $complaint->reporter->designation)
                                <span>Role: {{ $complaint->reporter->designation }}</span>
                            @else
                                <span>Direct Internal Entry</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Detailed Description -->
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-2">Detailed Issue Narrative</span>
                    <div class="p-5 bg-slate-50 border border-slate-200/90 rounded-xl text-slate-950 text-xs leading-relaxed font-medium whitespace-pre-wrap selection:bg-amber-100">
{{ $complaint->description }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Quick Resolution & Assignee Control -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-5">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                    <h3 class="text-xs font-black text-slate-950 uppercase tracking-wider">Ticket Management</h3>
                </div>

                <form action="{{ route('organization.complaints.update', $complaint) }}" method="POST" class="space-y-4">
                    @csrf 
                    @method('PUT')

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Ticket Status</label>
                        <select name="status" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white">
                            <option value="Open" {{ $complaint->status == 'Open' ? 'selected' : '' }}>Open (Pending)</option>
                            <option value="In Progress" {{ $complaint->status == 'In Progress' ? 'selected' : '' }}>In Progress (Investigating)</option>
                            <option value="Resolved" {{ $complaint->status == 'Resolved' ? 'selected' : '' }}>Resolved (Closed)</option>
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Priority Level</label>
                        <select name="priority" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white">
                            <option value="Low" {{ $complaint->priority == 'Low' ? 'selected' : '' }}>🟢 Low Priority</option>
                            <option value="Medium" {{ $complaint->priority == 'Medium' ? 'selected' : '' }}>🔵 Medium Priority</option>
                            <option value="High" {{ $complaint->priority == 'High' ? 'selected' : '' }}>🟠 High Priority</option>
                            <option value="Urgent" {{ $complaint->priority == 'Urgent' ? 'selected' : '' }}>🔴 Urgent Priority</option>
                        </select>
                    </div>

                    <!-- Assignee -->
                    <div>
                        <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Assigned Agent</label>
                        <select name="assigned_to" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white">
                            <option value="">-- Unassigned --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $complaint->assigned_to == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->first_name }} {{ $emp->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 bg-slate-950 hover:bg-slate-900 active:bg-slate-800 text-white font-black text-xs rounded-xl shadow-xs transition whitespace-nowrap">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Current Assignee Profile Summary -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block">Assigned Handler Details</span>
                @if($complaint->assignee)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-800 border border-amber-300 flex items-center justify-center font-black text-sm shrink-0">
                            {{ strtoupper(substr($complaint->assignee->first_name, 0, 1) . substr($complaint->assignee->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-xs font-black text-slate-950">{{ $complaint->assignee->first_name }} {{ $complaint->assignee->last_name }}</div>
                            <div class="text-[11px] font-semibold text-slate-500">{{ $complaint->assignee->designation ?? 'Staff Member' }}</div>
                            @if($complaint->assignee->phone)
                                <div class="text-[10px] font-mono text-slate-600 mt-0.5">{{ $complaint->assignee->phone }}</div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-3 bg-amber-50/50 border border-amber-200 rounded-xl text-xs text-amber-900 font-medium">
                        No agent currently assigned. Pick a staff member above to route this complaint.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

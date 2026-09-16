@extends('layouts.sme')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.complaints.index') }}" 
               class="p-2.5 rounded-xl border border-slate-200/90 bg-white text-slate-700 hover:text-slate-950 hover:bg-slate-50 shadow-2xs transition">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-0.5">
                    <span>Helpdesk</span>
                    <span>/</span>
                    <span class="text-slate-900 font-bold">New Ticket</span>
                </div>
                <h1 class="text-2xl font-black text-slate-950 tracking-tight">Log Support Ticket</h1>
            </div>
        </div>

        <a href="{{ route('organization.complaints.index') }}" 
           class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-2xs transition whitespace-nowrap">
            Back to Tickets
        </a>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-950 text-xs font-semibold shadow-2xs">
            <div class="flex items-center gap-2 mb-2 font-bold text-rose-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Please correct the errors below before submitting:</span>
            </div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <form action="{{ route('organization.complaints.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Ticket Form Details -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 space-y-5">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                        <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                        <h2 class="text-xs font-black text-slate-950 uppercase tracking-wider">Ticket Information</h2>
                    </div>

                    <!-- Subject / Title -->
                    <div>
                        <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">
                            Subject / Issue Title <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" 
                               name="subject" 
                               value="{{ old('subject') }}" 
                               class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-950 outline-none transition @error('subject') border-rose-400 bg-rose-50/20 @enderror" 
                               required 
                               placeholder="e.g. Defective Screen on Order #INV-0294 or Late Delivery Dispute">
                        @error('subject') <span class="text-[11px] font-bold text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">
                            Detailed Description <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="description" 
                                  rows="5" 
                                  class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-950 outline-none transition leading-relaxed @error('description') border-rose-400 bg-rose-50/20 @enderror" 
                                  required 
                                  placeholder="Describe the complaint in full detail, including product serial numbers, customer remarks, timeline, or steps taken so far...">{{ old('description') }}</textarea>
                        @error('description') <span class="text-[11px] font-bold text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Client & Priority Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <!-- Client Selector -->
                        <div>
                            <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">
                                Customer / Client Account
                            </label>
                            <select name="client_id" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-900 outline-none transition bg-white">
                                <option value="">-- Internal / Store Issue --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} {{ $c->phone ? "({$c->phone})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] font-medium text-slate-500 mt-1">Leave empty for internal operations or facility issues.</p>
                            @error('client_id') <span class="text-[11px] font-bold text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Priority Level -->
                        <div>
                            <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">
                                Priority Level <span class="text-rose-600">*</span>
                            </label>
                            <select name="priority" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-900 outline-none transition bg-white" required>
                                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>🟢 Low (Standard Service)</option>
                                <option value="Medium" {{ old('priority', 'Medium') == 'Medium' ? 'selected' : '' }}>🔵 Medium (Normal Attention)</option>
                                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>🟠 High (VIP / Urgent Review)</option>
                                <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>🔴 Urgent (Critical SLA Blocker)</option>
                            </select>
                            <p class="text-[10px] font-medium text-slate-500 mt-1">Defines SLA escalation speed and alert urgency.</p>
                            @error('priority') <span class="text-[11px] font-bold text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Assignee Selector -->
                    <div>
                        <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">
                            Assign Staff Handler
                        </label>
                        <select name="assigned_to" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-900 outline-none transition bg-white">
                            <option value="">-- Unassigned (General Queue) --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('assigned_to') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->first_name }} {{ $emp->last_name }} &bull; {{ $emp->designation ?? 'Staff' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] font-medium text-slate-500 mt-1">Assigned agent will be notified to begin resolution investigation.</p>
                        @error('assigned_to') <span class="text-[11px] font-bold text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Guidelines & Best Practices Card -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <h3 class="text-xs font-black text-slate-950 uppercase tracking-wider">Helpdesk Guidelines</h3>
                    </div>

                    <div class="space-y-3.5 text-xs font-medium text-slate-600">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-lg bg-amber-500/10 text-amber-800 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">1</span>
                            <div>
                                <strong class="text-slate-900 block font-bold">Clear Subject Lines</strong>
                                Reference invoice numbers, product SKUs, or customer identifiers for rapid triage.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-lg bg-amber-500/10 text-amber-800 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">2</span>
                            <div>
                                <strong class="text-slate-900 block font-bold">Priority Standards</strong>
                                Reserve <span class="text-rose-700 font-bold">Urgent</span> for store-critical blockers, safety risks, or legal disputes.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-lg bg-amber-500/10 text-amber-800 flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">3</span>
                            <div>
                                <strong class="text-slate-900 block font-bold">Direct Assignment</strong>
                                Assigning directly to an employee notifies them immediately to avoid ticket stall.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporter Context Badge -->
                <div class="bg-slate-50/80 rounded-2xl border border-slate-200/80 p-5 space-y-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Logged By</span>
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-black shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-950">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] font-medium text-slate-500">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Footer Action Bar -->
        <div class="sticky bottom-0 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200/90 shadow-md p-4 flex items-center justify-between z-10">
            <a href="{{ route('organization.complaints.index') }}" 
               class="px-5 py-2.5 border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition whitespace-nowrap">
                Cancel
            </a>

            <button type="submit" 
                    class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-xl shadow-xs transition whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Submit Support Ticket</span>
            </button>
        </div>
    </form>
</div>
@endsection

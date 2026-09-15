@extends('layouts.sme')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-2">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 mb-4 border-b border-gray-100">
        <a href="{{ route('organization.clients.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Add New Client</h1>
            <p class="text-xs text-gray-500">Create a new customer profile records inside client catalog database.</p>
        </div>
    </div>

    <!-- Form Container -->
    <form action="{{ route('organization.clients.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/40 overflow-hidden">
        @csrf
        
        <div class="p-6 md:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center font-bold">▶</div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Client Information</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1.5">Full Name / Company Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 rounded-xl px-4 py-2.5 text-sm font-medium outline-none transition-all shadow-sm @error('name') border-red-300 bg-red-50 @enderror" placeholder="e.g. Acme Corp or John Doe">
                    @error('name') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 rounded-xl px-4 py-2.5 text-sm font-medium outline-none transition-all shadow-sm @error('phone') border-red-300 @enderror" placeholder="e.g. +91 98765 43210">
                    @error('phone') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 rounded-xl px-4 py-2.5 text-sm font-medium outline-none transition-all shadow-sm @error('email') border-red-300 @enderror" placeholder="e.g. client@example.com">
                    @error('email') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block mb-1.5">GSTIN Number</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number') }}" placeholder="e.g. 22AAAAA0000A1Z5" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 rounded-xl px-4 py-2.5 text-sm font-medium outline-none transition-all shadow-sm uppercase font-mono @error('gst_number') border-red-300 @enderror">
                    @error('gst_number') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
                
                <div class="col-span-full">
                    <label class="block mb-1.5">Address</label>
                    <textarea name="address" rows="2" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 rounded-xl px-4 py-2.5 text-sm font-medium outline-none transition-all shadow-sm @error('address') border-red-300 @enderror" placeholder="e.g. Street Name, Building Number, City, Pin Code">{{ old('address') }}</textarea>
                    @error('address') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
                
                <div class="col-span-full">
                    <label class="block mb-1.5">Internal Notes</label>
                    <textarea name="notes" rows="2" placeholder="Write reference comments..." class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 rounded-xl px-4 py-2.5 text-sm font-medium outline-none transition-all shadow-sm @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                    @error('notes') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 border-t border-slate-200 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
            <a href="{{ route('organization.clients.index') }}" class="px-6 py-2.5 border border-slate-300 text-slate-700 bg-white rounded-xl font-bold text-xs hover:bg-slate-50 shadow-sm transition-all text-center">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-slate-800 shadow-sm transition-all flex items-center justify-center gap-2">Save Client</button>
        </div>
    </form>
</div>
@endsection

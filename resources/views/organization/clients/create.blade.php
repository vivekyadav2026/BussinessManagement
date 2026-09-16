@extends('layouts.sme')

@section('title', 'Add New Client')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.clients.index') }}" class="hover:text-slate-900 transition-colors">Operations</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.clients.index') }}" class="hover:text-slate-900 transition-colors">Clients</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Add New Client</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    👥
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Add New Client</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Register corporate customer accounts, GST billing details, and contact coordinates.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('organization.clients.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs rounded-lg transition shadow-2xs">
                &larr; Back to Directory
            </a>
        </div>
    </div>

    <!-- 2. Form Card -->
    <form action="{{ route('organization.clients.store') }}" method="POST" class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        @csrf
        
        <div class="p-6 md:p-8 space-y-6">
            <!-- Section Header -->
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-black text-xs">
                    📝
                </div>
                <h2 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Client Profile &amp; Contact Parameters</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Full Name / Company Name <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold outline-none transition text-slate-950 placeholder:text-slate-400 shadow-2xs @error('name') border-rose-300 bg-rose-50 @enderror" 
                        placeholder="e.g. Acme Technologies Pvt Ltd or John Doe">
                    @error('name') <span class="text-[11px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>
                
                <!-- Phone Number -->
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Contact Phone Number
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" 
                        class="w-full border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold outline-none transition text-slate-950 placeholder:text-slate-400 shadow-2xs @error('phone') border-rose-300 @enderror" 
                        placeholder="e.g. +91 98765 43210">
                    @error('phone') <span class="text-[11px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>
                
                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Billing Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold outline-none transition text-slate-950 placeholder:text-slate-400 shadow-2xs @error('email') border-rose-300 @enderror" 
                        placeholder="e.g. billing@company.com">
                    @error('email') <span class="text-[11px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>
                
                <!-- GSTIN Number -->
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">
                        GSTIN / Tax ID
                    </label>
                    <input type="text" name="gst_number" value="{{ old('gst_number') }}" 
                        placeholder="e.g. 27AAAAA0000A1Z5" 
                        class="w-full border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-bold font-mono outline-none transition text-slate-950 uppercase placeholder:text-slate-400 shadow-2xs @error('gst_number') border-rose-300 @enderror">
                    @error('gst_number') <span class="text-[11px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>
                
                <!-- Billing Address -->
                <div class="col-span-full">
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Billing &amp; Delivery Address
                    </label>
                    <textarea name="address" rows="2" 
                        class="w-full border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-medium outline-none transition text-slate-950 placeholder:text-slate-400 shadow-2xs @error('address') border-rose-300 @enderror" 
                        placeholder="e.g. Plot No. 12, Industrial Area, Sector 4, City, State - PIN">{{ old('address') }}</textarea>
                    @error('address') <span class="text-[11px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>
                
                <!-- Internal Notes -->
                <div class="col-span-full">
                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Internal Account Notes (Visible to staff only)
                    </label>
                    <textarea name="notes" rows="2" 
                        placeholder="Payment terms agreement, delivery preferences, contract notes..." 
                        class="w-full border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-medium outline-none transition text-slate-950 placeholder:text-slate-400 shadow-2xs @error('notes') border-rose-300 @enderror">{{ old('notes') }}</textarea>
                    @error('notes') <span class="text-[11px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 border-t border-slate-200 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
            <a href="{{ route('organization.clients.index') }}" class="px-5 py-2.5 border border-slate-300 text-slate-700 bg-white rounded-lg font-bold text-xs hover:bg-slate-50 shadow-2xs transition text-center">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition flex items-center justify-center gap-2">
                ✓ Save Client Account
            </button>
        </div>
    </form>
</div>
@endsection

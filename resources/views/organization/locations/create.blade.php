@extends('layouts.sme')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.locations.index') }}" class="hover:text-slate-900 transition-colors">Settings</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.locations.index') }}" class="hover:text-slate-900 transition-colors">Locations &amp; Outlets</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">New Location</span>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('organization.locations.index') }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 hover:text-slate-950 hover:bg-slate-50 shadow-2xs transition">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Add New Branch Location</h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">
                        Register an operational store, warehouse depot, or regional trading branch.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('organization.locations.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <span>Cancel</span>
            </a>
        </div>
    </div>

    <!-- Error Alerts -->
    @if(isset($errors) && $errors->any())
    <div class="bg-rose-50 border border-rose-300 rounded-xl p-4 text-xs text-rose-950">
        <div class="font-extrabold flex items-center gap-1.5 mb-1 text-sm">
            <span>⚠️</span> Please correct the following errors:
        </div>
        <ul class="list-disc pl-5 space-y-0.5 font-medium">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-rose-700 text-base">⚠️</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 2. Main 2-Column Form Layout -->
    <form action="{{ route('organization.locations.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT FORM COLUMN (8 cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Card: Branch Specifications -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div>
                            <h2 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Branch Identification</h2>
                            <p class="text-[11px] text-slate-600 font-medium mt-0.5">Primary name and contact details for this outlet.</p>
                        </div>
                        <span class="text-xs font-extrabold text-rose-600">* Required</span>
                    </div>

                    <div class="p-5 space-y-4">
                        <!-- Branch Name -->
                        <div>
                            <label for="name" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                                Branch / Outlet Name <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   placeholder="e.g. Flagship Store, Downtown Cafe, Outlet #2" 
                                   class="w-full border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-950 outline-none transition @error('name') border-rose-400 @enderror">
                            @error('name') 
                                <span class="text-xs font-bold text-rose-600 mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                                Contact Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                    📞
                                </div>
                                <input type="text" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       placeholder="e.g. +91 98765 43210" 
                                       class="w-full pl-9 pr-4 py-2.5 border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-xs font-semibold text-slate-950 outline-none transition @error('phone') border-rose-400 @enderror">
                            </div>
                            @error('phone') 
                                <span class="text-xs font-bold text-rose-600 mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Physical Address -->
                        <div>
                            <label for="address" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                                Full Physical Address
                            </label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3" 
                                      placeholder="e.g. Unit 4, Commercial Plaza, MG Road, Bangalore 560001" 
                                      class="w-full border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-950 outline-none transition @error('address') border-rose-400 @enderror">{{ old('address') }}</textarea>
                            @error('address') 
                                <span class="text-xs font-bold text-rose-600 mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons Container -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 flex items-center justify-between">
                    <a href="{{ route('organization.locations.index') }}" class="px-4 py-2 border border-slate-300 text-slate-800 bg-white hover:bg-slate-50 rounded-lg font-bold text-xs shadow-2xs transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 rounded-lg font-extrabold text-xs shadow-xs transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Save &amp; Register Location</span>
                    </button>
                </div>

            </div>

            <!-- RIGHT SIDEBAR GUIDELINES (4 cols) -->
            <div class="lg:col-span-4 space-y-5">

                <!-- Branch Guidelines Card -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Multi-Branch Architecture</h3>
                    <ul class="space-y-2.5 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Inventory Isolation:</strong> Stock quantities and warehouse transfers are tracked per branch.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Staff Assignment:</strong> Employees can be restricted to specific branches for attendance and billing.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Invoicing &amp; POS:</strong> Receipts will automatically show the branch address and contact phone.</span>
                        </li>
                    </ul>
                </div>

                <!-- Scope Note -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-xs text-slate-700 space-y-2">
                    <div class="font-bold text-slate-950 flex items-center gap-1.5">
                        <span>ℹ️</span> Operational Status
                    </div>
                    <p class="leading-relaxed text-[11px]">
                        Newly registered locations are marked as <strong>Operational</strong> by default. You can temporarily toggle them inactive at any time without losing historical invoices or employee records.
                    </p>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection

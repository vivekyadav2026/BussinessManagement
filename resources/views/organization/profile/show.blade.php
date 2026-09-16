@extends('layouts.sme')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-24">

    <!-- 1. Breadcrumb & Page Header (High Contrast Dark Visibility) -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Settings</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Organization</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Organization Profile</span>
            </nav>
            
            <!-- Main Title & Subtitle -->
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Organization Profile</h1>
            <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed max-w-3xl">
                Manage your business identity, tax information, POS settings and operating preferences.
            </p>
        </div>

        @php
            $activeSub = $organization->activeSubscription;
            $activePlan = $activeSub ? $activeSub->plan : null;
            $isRestaurant = ($organization->business_type === 'restaurant');
        @endphp
        <!-- Right Status & Plan Badges Cluster (Structured & No Overflow) -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap lg:justify-end">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-100/80 text-emerald-950 border border-emerald-300 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                Active
            </span>

            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-900 border border-slate-300 shadow-2xs">
                {{ $isRestaurant ? 'Restaurant POS' : 'Retail ERP' }}
            </span>

            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100/90 text-amber-950 border border-amber-300 shadow-2xs">
                <span class="text-amber-900 font-semibold">Plan:</span>
                <span>{{ $activePlan ? $activePlan->name : ($isRestaurant ? 'Restaurant POS' : 'Retail & Wholesale') }}</span>
                @if(Route::has('organization.subscription.index'))
                    <a href="{{ route('organization.subscription.index') }}" class="ml-1 text-amber-950 hover:underline font-extrabold" title="Manage Subscription">
                        &rarr;
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-300 text-emerald-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-emerald-700 text-base">✓</span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs shadow-2xs">
        <div class="font-extrabold mb-1 text-rose-900">Please review the following errors:</div>
        <ul class="list-disc list-inside space-y-0.5 text-rose-800 font-medium">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 2. Organization Identity (Compact Horizontal Header with Strong Dark Text) -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <!-- Logo Avatar (48-56px) -->
                <div class="relative group shrink-0">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shadow-2xs">
                        <img id="logoPreviewImg" 
                             src="{{ $organization->logo_url ?? '' }}" 
                             alt="{{ $organization->name }}" 
                             class="{{ $organization->logo_url ? '' : 'hidden' }} w-full h-full object-cover">
                        
                        <div id="logoFallback" class="{{ $organization->logo_url ? 'hidden' : 'flex' }} w-full h-full bg-slate-900 items-center justify-center text-amber-400 font-extrabold text-base select-none">
                            {{ strtoupper(substr($organization->name, 0, 2)) }}
                        </div>
                    </div>
                </div>

                <!-- Identity Details -->
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-950 tracking-tight truncate">
                            {{ $organization->name }}
                        </h2>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-900 border border-slate-300">
                            #ORG-{{ str_pad($organization->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3 text-xs text-slate-700 font-semibold mt-1 flex-wrap">
                        <span class="text-slate-900">{{ $isRestaurant ? 'Restaurant / Cafe / Food POS' : 'Retail & Wholesale ERP' }}</span>
                        @if($organization->email)
                            <span class="text-slate-300">•</span>
                            <span class="truncate text-slate-800">{{ $organization->email }}</span>
                        @endif
                        @if($organization->phone)
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-800">{{ $organization->phone }}</span>
                        @endif
                    </div>

                    <!-- Meta details bar -->
                    <div class="flex items-center gap-3 text-xs text-slate-600 font-medium mt-2 flex-wrap">
                        <span class="font-semibold text-slate-800">{{ $locationsCount ?? 1 }} Branch {{ ($locationsCount ?? 1) > 1 ? 'Locations' : 'Location' }}</span>
                        <span class="text-slate-300">•</span>
                        <span class="font-semibold text-slate-800">{{ $employeesCount ?? 1 }} Team {{ ($employeesCount ?? 1) > 1 ? 'Members' : 'Member' }}</span>
                        <span class="text-slate-300">•</span>
                        <span>Member since <strong class="text-slate-900">{{ $organization->created_at ? $organization->created_at->format('M Y') : 'Recent' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Compact Logo Upload Trigger -->
            <div class="shrink-0 self-start sm:self-center">
                <label for="logoInput" class="cursor-pointer inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-xs font-bold text-slate-900 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Upload Logo
                </label>
                <p class="text-[11px] text-slate-600 font-medium mt-1 text-left sm:text-right">PNG, JPG up to 2MB</p>
            </div>
        </div>
    </div>

    <!-- 3. Form & Settings (Two-Column Layout on Desktop) -->
    <form action="{{ route('organization.profile.update') }}" method="POST" enctype="multipart/form-data" id="orgProfileForm">
        @csrf
        @method('PUT')

        <!-- Hidden file input for logo -->
        <input type="file" name="logo" id="logoInput" accept="image/png,image/jpeg,image/webp,image/jpg" class="hidden">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT / MAIN COLUMN (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200/90 shadow-2xs divide-y divide-slate-100 overflow-hidden">

                <!-- Section 1: Business Identity -->
                <div class="p-5 sm:p-6 space-y-5">
                    <div>
                        <h3 class="text-base font-bold text-slate-950">Business Identity</h3>
                        <p class="text-xs text-slate-600 font-medium mt-0.5">Legal entity and operating configuration.</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Business Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Business / Trade Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $organization->name) }}" 
                                   required 
                                   placeholder="e.g. Acme Enterprise Retail Ltd"
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-sm font-semibold text-slate-950 outline-none transition @error('name') border-rose-300 @enderror">
                            @error('name') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Industry Operating Mode -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Industry Operating Mode <span class="text-rose-500">*</span>
                            </label>
                            <input type="hidden" name="business_type" id="businessTypeInput" value="{{ old('business_type', $organization->business_type ?? 'business') }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Retail Card -->
                                <div id="cardRetail" 
                                     onclick="selectBusinessType('business')" 
                                     class="cursor-pointer p-3.5 rounded-lg border transition-all flex items-start gap-3 select-none {{ old('business_type', $organization->business_type) === 'business' || !old('business_type', $organization->business_type) ? 'border-amber-500 bg-amber-500/[0.05] shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                    <div class="w-8 h-8 rounded-md bg-slate-100 flex items-center justify-center text-base shrink-0">
                                        🏪
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-950">Retail &amp; Wholesale ERP</span>
                                            <div class="radio-retail w-4 h-4 rounded-full border flex items-center justify-center {{ old('business_type', $organization->business_type) === 'business' || !old('business_type', $organization->business_type) ? 'border-amber-500 bg-amber-500' : 'border-slate-400' }}">
                                                <div class="w-1.5 h-1.5 rounded-full bg-white {{ old('business_type', $organization->business_type) === 'business' || !old('business_type', $organization->business_type) ? '' : 'hidden' }}"></div>
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-slate-700 font-medium mt-0.5 leading-snug">
                                            Invoicing, inventory, barcode scanning &amp; client ledgers.
                                        </p>
                                    </div>
                                </div>

                                <!-- Restaurant Card -->
                                <div id="cardRestaurant" 
                                     onclick="selectBusinessType('restaurant')" 
                                     class="cursor-pointer p-3.5 rounded-lg border transition-all flex items-start gap-3 select-none {{ old('business_type', $organization->business_type) === 'restaurant' ? 'border-amber-500 bg-amber-500/[0.05] shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                    <div class="w-8 h-8 rounded-md bg-slate-100 flex items-center justify-center text-base shrink-0">
                                        🍽️
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-950">Restaurant / Food POS</span>
                                            <div class="radio-restaurant w-4 h-4 rounded-full border flex items-center justify-center {{ old('business_type', $organization->business_type) === 'restaurant' ? 'border-amber-500 bg-amber-500' : 'border-slate-400' }}">
                                                <div class="w-1.5 h-1.5 rounded-full bg-white {{ old('business_type', $organization->business_type) === 'restaurant' ? '' : 'hidden' }}"></div>
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-slate-700 font-medium mt-0.5 leading-snug">
                                            Dine-in tables, QR digital menu, KOTs &amp; captain app.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @error('business_type') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Registered Physical Address -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Registered Physical Address
                            </label>
                            <textarea name="address" 
                                      rows="2" 
                                      placeholder="Street address, building, city, state, postal code"
                                      class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg p-3 text-xs font-medium text-slate-950 outline-none transition @error('address') border-rose-300 @enderror">{{ old('address', $organization->address) }}</textarea>
                            <p class="text-[11px] text-slate-600 font-medium mt-1">Printed on customer tax invoices and POS sales receipts.</p>
                            @error('address') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Communication & Tax Details -->
                <div class="p-5 sm:p-6 space-y-5">
                    <div>
                        <h3 class="text-base font-bold text-slate-950">Communication &amp; Tax Details</h3>
                        <p class="text-xs text-slate-600 font-medium mt-0.5">Contact channels and statutory tax registration.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Official Email -->
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                    Official Business Email
                                </label>
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email', $organization->email) }}" 
                                       placeholder="billing@yourcompany.com"
                                       class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('email') border-rose-300 @enderror">
                                @error('email') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                    Primary Contact Phone
                                </label>
                                <input type="text" 
                                       name="phone" 
                                       value="{{ old('phone', $organization->phone) }}" 
                                       placeholder="+91 98765 43210"
                                       class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-950 outline-none transition @error('phone') border-rose-300 @enderror">
                                @error('phone') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- GSTIN -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-900">
                                    GSTIN (Tax Identification Number)
                                </label>
                                @if($organization->gst_number)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-950 bg-emerald-100/90 px-2 py-0.5 rounded border border-emerald-300">
                                        <span>✓</span> Verified Format
                                    </span>
                                @endif
                            </div>
                            <input type="text" 
                                   name="gst_number" 
                                   id="gstInput"
                                   maxlength="15"
                                   value="{{ old('gst_number', $organization->gst_number) }}" 
                                   placeholder="e.g. 22AAAAA0000A1Z5"
                                   oninput="this.value = this.value.toUpperCase()"
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-mono font-bold tracking-wider text-slate-950 uppercase outline-none transition @error('gst_number') border-rose-300 @enderror">
                            <p class="text-[11px] text-slate-600 font-medium mt-1">15-digit alphanumeric code (e.g. 22AAAAA0000A1Z5) for Indian GST compliance.</p>
                            @error('gst_number') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: POS & Payments -->
                <div class="p-5 sm:p-6 space-y-5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <h3 class="text-base font-bold text-slate-950">POS &amp; Payments</h3>
                            <p class="text-xs text-slate-600 font-medium mt-0.5">Configure payment collection and tax defaults.</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-900 border border-slate-300">GPay</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-900 border border-slate-300">PhonePe</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-900 border border-slate-300">Paytm</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Merchant UPI ID -->
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">
                                Merchant UPI ID / VPA
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-600 font-bold text-xs">
                                    UPI
                                </div>
                                <input type="text" 
                                       name="upi_id" 
                                       value="{{ old('upi_id', $organization->upi_id) }}" 
                                       placeholder="merchant@okhdfcbank or 9876543210@paytm"
                                       class="w-full pl-11 bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm font-mono font-semibold text-slate-950 outline-none transition @error('upi_id') border-rose-300 @enderror">
                            </div>

                            <!-- Compact Callout -->
                            <div class="mt-2 p-3 rounded-lg bg-amber-50/60 border border-amber-200 text-xs text-slate-800 flex items-start gap-2">
                                <span class="text-amber-700 font-bold text-sm shrink-0">💡</span>
                                <span class="leading-relaxed font-medium">
                                    <strong class="text-slate-950 font-bold">Dynamic QR Generator:</strong> Automatically embeds the exact bill total into a QR code on POS slips for instant customer scan &amp; pay via any UPI app.
                                </span>
                            </div>
                            @error('upi_id') <p class="text-rose-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Tax Slabs (CGST & SGST) -->
                        <div class="pt-2">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-900">
                                    Default Intra-State GST Tax Slabs
                                </label>
                                <span id="totalGstBadge" class="text-xs font-bold text-slate-950 bg-slate-100 px-2.5 py-1 rounded border border-slate-300">
                                    Combined GST: {{ number_format((float)old('cgst_percent', $organization->cgst_percent ?? 0) + (float)old('sgst_percent', $organization->sgst_percent ?? 0), 2) }}%
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- CGST -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Central GST (CGST)
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                               step="0.01" 
                                               min="0"
                                               max="100"
                                               id="cgstInput"
                                               name="cgst_percent" 
                                               value="{{ old('cgst_percent', $organization->cgst_percent ?? '0.00') }}" 
                                               oninput="updateCombinedGst()"
                                               placeholder="9.00"
                                               class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3 py-2 text-xs sm:text-sm font-bold text-slate-950 outline-none transition">
                                        <span class="absolute right-3 inset-y-0 flex items-center text-xs font-bold text-slate-500">%</span>
                                    </div>
                                </div>

                                <!-- SGST -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        State GST (SGST / UTGST)
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                               step="0.01" 
                                               min="0"
                                               max="100"
                                               id="sgstInput"
                                               name="sgst_percent" 
                                               value="{{ old('sgst_percent', $organization->sgst_percent ?? '0.00') }}" 
                                               oninput="updateCombinedGst()"
                                               placeholder="9.00"
                                               class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3 py-2 text-xs sm:text-sm font-bold text-slate-950 outline-none transition">
                                        <span class="absolute right-3 inset-y-0 flex items-center text-xs font-bold text-slate-500">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Shift & Attendance -->
                <div class="p-5 sm:p-6 space-y-5">
                    <div>
                        <h3 class="text-base font-bold text-slate-950">Shift &amp; Attendance</h3>
                        <p class="text-xs text-slate-600 font-medium mt-0.5">Working hours baseline for attendance and shift tracking.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5 flex items-center gap-1">
                                <span>☀️</span> Standard Check-in Time
                            </label>
                            <input type="time" 
                                   name="default_check_in" 
                                   value="{{ old('default_check_in', $organization->default_check_in ?? '09:00') }}" 
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3 py-2 text-xs sm:text-sm font-bold text-slate-950 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5 flex items-center gap-1">
                                <span>🌙</span> Standard Check-out Time
                            </label>
                            <input type="time" 
                                   name="default_check_out" 
                                   value="{{ old('default_check_out', $organization->default_check_out ?? '18:00') }}" 
                                   class="w-full bg-white border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-lg px-3 py-2 text-xs sm:text-sm font-bold text-slate-950 outline-none transition">
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-600 font-medium">Punctuality metrics and shift overtime are computed relative to these standard times.</p>
                </div>

            </div>

            <!-- RIGHT / SIDEBAR COLUMN (4 cols) -->
            <div class="lg:col-span-4 space-y-5">

                <!-- 1. Organization Summary Panel -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider mb-3">Organization Summary</h3>

                    <dl class="divide-y divide-slate-100 text-xs">
                        <div class="py-2.5 flex justify-between items-center">
                            <dt class="text-slate-600 font-medium">Status</dt>
                            <dd class="font-bold text-emerald-900 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active
                            </dd>
                        </div>
                        <div class="py-2.5 flex justify-between items-center">
                            <dt class="text-slate-600 font-medium">Tenant ID</dt>
                            <dd class="font-mono font-bold text-slate-950">#ORG-{{ str_pad($organization->id, 4, '0', STR_PAD_LEFT) }}</dd>
                        </div>
                        <div class="py-2.5 flex justify-between items-center">
                            <dt class="text-slate-600 font-medium">Industry</dt>
                            <dd class="font-bold text-slate-950">{{ $isRestaurant ? 'Restaurant POS' : 'Retail ERP' }}</dd>
                        </div>
                        <div class="py-2.5 flex justify-between items-center">
                            <dt class="text-slate-600 font-medium">Subscription Plan</dt>
                            <dd class="font-bold text-slate-950">{{ $activePlan ? $activePlan->name : ($isRestaurant ? 'Restaurant POS' : 'Standard Edition') }}</dd>
                        </div>
                        <div class="py-2.5 flex justify-between items-center">
                            <dt class="text-slate-600 font-medium">Branches</dt>
                            <dd class="font-bold text-slate-950">{{ $locationsCount ?? 1 }} Active</dd>
                        </div>
                        <div class="py-2.5 flex justify-between items-center">
                            <dt class="text-slate-600 font-medium">Staff Count</dt>
                            <dd class="font-bold text-slate-950">{{ $employeesCount ?? 1 }} Members</dd>
                        </div>
                    </dl>

                    @if(Route::has('organization.subscription.index'))
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <a href="{{ route('organization.subscription.index') }}" class="text-xs font-bold text-amber-800 hover:text-amber-950 hover:underline flex items-center justify-between">
                                <span>Manage subscription &amp; billing</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- 2. Primary Account Owner Panel -->
                @php
                    $user = $adminUser ?? auth()->user();
                @endphp
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider mb-3">Primary Account Owner</h3>

                    <div class="flex items-center gap-3 mb-3.5">
                        <div class="w-10 h-10 rounded-full bg-slate-900 text-amber-400 font-extrabold text-sm flex items-center justify-center shrink-0 shadow-2xs">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-sm text-slate-950 truncate">{{ $user->name }}</div>
                            <div class="text-[11px] font-semibold text-slate-600">Tenant Administrator</div>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs border-t border-slate-100 pt-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-slate-500 font-medium">Email:</span>
                            <span class="font-bold text-slate-950 truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-slate-500 font-medium">Phone:</span>
                            <span class="font-bold text-slate-950">
                                {{ $ownerEmployee ? $ownerEmployee->phone : ($organization->phone ?? 'Not specified') }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="mt-4 w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-bold text-slate-900 transition">
                        <span>Manage Security &amp; Credentials</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <!-- 3. System Guidance Note -->
                <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 text-xs text-slate-700 space-y-1.5">
                    <div class="font-bold text-slate-950 flex items-center gap-1.5">
                        <span>ℹ️</span> Workspace Sync
                    </div>
                    <p class="leading-relaxed text-[11px] font-medium text-slate-700">
                        Changes made to legal name, address, GSTIN, and UPI ID reflect immediately across all POS terminals, counter billing screens, and printed receipts.
                    </p>
                </div>

            </div>

        </div>

        <!-- 4. Compact Sticky Bottom Action Bar (~64px) -->
        <div class="sticky bottom-4 z-20 mt-8 bg-white/95 backdrop-blur-md border border-slate-300 rounded-xl shadow-lg px-5 py-3 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-800 font-medium">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="hidden sm:inline">Unsaved changes will update all locations &amp; POS terminals.</span>
                <span class="sm:hidden">Unsaved changes</span>
            </div>

            <div class="flex items-center gap-3">
                <button type="reset" class="px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-slate-950 transition">
                    Discard
                </button>
                <button type="submit" id="saveProfileBtn" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <svg id="saveIcon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>

    </form>

</div>

<!-- Client-side Interactive Scripts -->
<script>
    // Live Logo File Preview
    document.getElementById('logoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const previewImg = document.getElementById('logoPreviewImg');
                const fallback = document.getElementById('logoFallback');
                
                previewImg.src = evt.target.result;
                previewImg.classList.remove('hidden');
                if (fallback) fallback.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Business Category Interactive Card Toggle
    function selectBusinessType(type) {
        document.getElementById('businessTypeInput').value = type;

        const cardRetail = document.getElementById('cardRetail');
        const cardRestaurant = document.getElementById('cardRestaurant');
        const dotRetail = document.querySelector('.radio-retail div');
        const dotRestaurant = document.querySelector('.radio-restaurant div');
        const radioRetail = document.querySelector('.radio-retail');
        const radioRestaurant = document.querySelector('.radio-restaurant');

        if (type === 'business') {
            cardRetail.className = 'cursor-pointer p-3.5 rounded-lg border transition-all flex items-start gap-3 select-none border-amber-500 bg-amber-500/[0.05] shadow-2xs';
            cardRestaurant.className = 'cursor-pointer p-3.5 rounded-lg border transition-all flex items-start gap-3 select-none border-slate-200 hover:border-slate-300 bg-white';
            
            radioRetail.className = 'radio-retail w-4 h-4 rounded-full border flex items-center justify-center border-amber-500 bg-amber-500';
            dotRetail.classList.remove('hidden');

            radioRestaurant.className = 'radio-restaurant w-4 h-4 rounded-full border flex items-center justify-center border-slate-400';
            dotRestaurant.classList.add('hidden');
        } else {
            cardRestaurant.className = 'cursor-pointer p-3.5 rounded-lg border transition-all flex items-start gap-3 select-none border-amber-500 bg-amber-500/[0.05] shadow-2xs';
            cardRetail.className = 'cursor-pointer p-3.5 rounded-lg border transition-all flex items-start gap-3 select-none border-slate-200 hover:border-slate-300 bg-white';

            radioRestaurant.className = 'radio-restaurant w-4 h-4 rounded-full border flex items-center justify-center border-amber-500 bg-amber-500';
            dotRestaurant.classList.remove('hidden');

            radioRetail.className = 'radio-retail w-4 h-4 rounded-full border flex items-center justify-center border-slate-400';
            dotRetail.classList.add('hidden');
        }
    }

    // Dynamic Combined GST Calculator
    function updateCombinedGst() {
        const cgst = parseFloat(document.getElementById('cgstInput').value) || 0;
        const sgst = parseFloat(document.getElementById('sgstInput').value) || 0;
        const total = (cgst + sgst).toFixed(2);
        
        const badge = document.getElementById('totalGstBadge');
        if (badge) {
            badge.textContent = 'Combined GST: ' + total + '%';
        }
    }

    // Submit state feedback
    document.getElementById('orgProfileForm').addEventListener('submit', function() {
        const btn = document.getElementById('saveProfileBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-slate-950 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...
        `;
    });
</script>
@endsection

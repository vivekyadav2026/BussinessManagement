@extends('layouts.super-admin')

@section('title', 'Platform Settings')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20" x-data="{ activeTab: 'platform' }">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Super Admin</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">System Configuration</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Platform Settings</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    ⚙️
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Platform &amp; System Settings</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Configure global SaaS parameters, free trial windows, support helpline, dynamic FAQs, and legal policies.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Quick Links & Public Website Link -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <a href="{{ route('public.pricing') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-2xs">
                <span>View Public Pricing</span>
                <span class="text-slate-400">&rarr;</span>
            </a>
            @if(Route::has('home'))
            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-2xs">
                <span>Public Landing Page</span>
                <span class="text-slate-400">&rarr;</span>
            </a>
            @endif
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
    <div class="bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <div class="flex items-center gap-2 font-bold mb-1">
            <span class="text-rose-700">⚠️</span>
            <span>Please check form inputs:</span>
        </div>
        <ul class="list-disc pl-5 space-y-0.5 text-xs text-rose-800 font-medium">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 2. System Status & Config Strip -->
    @php
        $trialActive = ($settings['enable_free_trial'] ?? '1') == '1';
        $trialDays = $settings['trial_days'] ?? 14;
        $faqCount = is_countable($faqs) ? count($faqs) : 0;
        $hasSupport = !empty($settings['support_email']) || !empty($settings['support_phone']);
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Trial Policy -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Free Trial Window</span>
                <span class="w-7 h-7 rounded-lg {{ $trialActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }} border flex items-center justify-center text-xs font-bold">⏱️</span>
            </div>
            <div class="mt-2">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ $trialActive ? $trialDays . ' Days' : 'Disabled' }}</span>
                    @if($trialActive)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">Blocked</span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Registration trial period</p>
            </div>
        </div>

        <!-- Support Channels -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Support Desk</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center text-xs font-bold">📞</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ $hasSupport ? 'Connected' : 'Missing' }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5 truncate">{{ $settings['support_email'] ?? 'support@vyapaargo.com' }}</p>
            </div>
        </div>

        <!-- Dynamic FAQs -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Dynamic FAQs</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center text-xs font-bold">❓</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ $faqCount }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Live on landing & pricing pages</p>
            </div>
        </div>

        <!-- Legal Compliance -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Legal Compliance</span>
                <span class="w-7 h-7 rounded-lg bg-violet-50 text-violet-700 border border-violet-200 flex items-center justify-center text-xs font-bold">📄</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-emerald-700 font-mono tracking-tight">Active</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Privacy &amp; Terms Published</p>
            </div>
        </div>
    </div>

    <!-- 3. Modern Segmented Tab Navigation -->
    <div class="bg-slate-100 p-1.5 rounded-xl border border-slate-200 flex items-center gap-1.5 overflow-x-auto shadow-2xs scrollbar-none">
        <button type="button" @click="activeTab = 'platform'" 
            :class="activeTab === 'platform' ? 'bg-white text-slate-950 shadow-xs font-extrabold border-slate-300' : 'text-slate-600 hover:text-slate-950 font-semibold border-transparent'" 
            class="px-4 py-2.5 rounded-lg text-xs border transition whitespace-nowrap flex items-center gap-2">
            <span>⚙️</span>
            <span>Platform &amp; Trial</span>
        </button>

        <button type="button" @click="activeTab = 'contact'" 
            :class="activeTab === 'contact' ? 'bg-white text-slate-950 shadow-xs font-extrabold border-slate-300' : 'text-slate-600 hover:text-slate-950 font-semibold border-transparent'" 
            class="px-4 py-2.5 rounded-lg text-xs border transition whitespace-nowrap flex items-center gap-2">
            <span>📞</span>
            <span>Support &amp; Company</span>
        </button>

        <button type="button" @click="activeTab = 'social'" 
            :class="activeTab === 'social' ? 'bg-white text-slate-950 shadow-xs font-extrabold border-slate-300' : 'text-slate-600 hover:text-slate-950 font-semibold border-transparent'" 
            class="px-4 py-2.5 rounded-lg text-xs border transition whitespace-nowrap flex items-center gap-2">
            <span>🌐</span>
            <span>Social Media Links</span>
        </button>

        <button type="button" @click="activeTab = 'faqs'" 
            :class="activeTab === 'faqs' ? 'bg-white text-slate-950 shadow-xs font-extrabold border-slate-300' : 'text-slate-600 hover:text-slate-950 font-semibold border-transparent'" 
            class="px-4 py-2.5 rounded-lg text-xs border transition whitespace-nowrap flex items-center gap-2">
            <span>❓</span>
            <span>Dynamic FAQs</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">{{ $faqCount }}</span>
        </button>

        <button type="button" @click="activeTab = 'privacy'" 
            :class="activeTab === 'privacy' ? 'bg-white text-slate-950 shadow-xs font-extrabold border-slate-300' : 'text-slate-600 hover:text-slate-950 font-semibold border-transparent'" 
            class="px-4 py-2.5 rounded-lg text-xs border transition whitespace-nowrap flex items-center gap-2">
            <span>🔒</span>
            <span>Privacy Policy</span>
        </button>

        <button type="button" @click="activeTab = 'terms'" 
            :class="activeTab === 'terms' ? 'bg-white text-slate-950 shadow-xs font-extrabold border-slate-300' : 'text-slate-600 hover:text-slate-950 font-semibold border-transparent'" 
            class="px-4 py-2.5 rounded-lg text-xs border transition whitespace-nowrap flex items-center gap-2">
            <span>📄</span>
            <span>Terms &amp; Conditions</span>
        </button>
    </div>

    <!-- 4. Main Configuration Form -->
    <form action="{{ route('super-admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- TAB 1: Platform & Trial Controls -->
        <div x-show="activeTab === 'platform'" x-cloak class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 sm:p-8 space-y-6">
            <div class="border-b border-slate-200 pb-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">⚙️</span>
                    <h3 class="text-base font-extrabold text-slate-950">Tenant Onboarding &amp; Free Trial Controls</h3>
                </div>
                <p class="text-xs text-slate-600 font-medium mt-1">
                    Control automated trial licenses allocated to newly registered business organizations upon signup.
                </p>
            </div>

            <!-- Free Trial Toggle Card -->
            <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h4 class="text-sm font-extrabold text-slate-950">Enable Automatic Free Trial on Registration</h4>
                    <p class="text-xs text-slate-600 font-medium mt-1 max-w-xl">
                        When enabled, new organizations automatically receive a trial license for the duration specified below. When disabled, new tenants cannot access POS/ERP features until a paid subscription is activated.
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="enable_free_trial" value="1" {{ ($settings['enable_free_trial'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-12 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                </label>
            </div>

            <!-- Trial Days Input -->
            <div class="space-y-2 max-w-md pt-2">
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                    Default Free Trial Period <span class="text-rose-500">*</span>
                </label>
                <div class="relative flex items-center">
                    <input type="number" name="trial_days" value="{{ old('trial_days', $settings['trial_days'] ?? 14) }}" min="0" max="365" 
                        class="w-full sm:w-44 border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-black text-slate-950 font-mono focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        required>
                    <span class="ml-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Days</span>
                </div>
                <p class="text-[11px] text-slate-500 font-medium">Standard options: <code>7</code>, <code>14</code> (recommended), or <code>30</code> days.</p>
            </div>

            <div class="bg-amber-50/70 border border-amber-200/90 rounded-xl p-4 text-xs text-amber-950 font-medium flex items-start gap-3">
                <span class="text-amber-700 text-base shrink-0">💡</span>
                <p>
                    <strong>Trial Grace Period Notice:</strong> Changing the trial duration applies to newly registered tenants from today. Existing active tenant trials will retain their original expiry timestamp unless manually updated from <a href="{{ route('super-admin.subscriptions.index') }}" class="underline font-bold text-amber-950 hover:text-amber-800">Subscriptions Management</a>.
                </p>
            </div>
        </div>

        <!-- TAB 2: Support & Company Details -->
        <div x-show="activeTab === 'contact'" x-cloak class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 sm:p-8 space-y-6">
            <div class="border-b border-slate-200 pb-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">📞</span>
                    <h3 class="text-base font-extrabold text-slate-950">Corporate Brand &amp; Customer Helpline</h3>
                </div>
                <p class="text-xs text-slate-600 font-medium mt-1">
                    These contact details are shown on the website footer, platform landing pages, and tenant billing receipts.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                        Company / Brand Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? 'Vyapaargo') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="e.g. Vyapaargo Technologies Pvt Ltd" required>
                </div>

                <!-- Support Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                        Official Support Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="support_email" value="{{ old('support_email', $settings['support_email'] ?? 'support@vyapaargo.com') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="e.g. support@vyapaargo.com" required>
                </div>

                <!-- Phone Hotline -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                        Helpdesk Hotline Number
                    </label>
                    <input type="text" name="support_phone" value="{{ old('support_phone', $settings['support_phone'] ?? '+91 98765 43210') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="e.g. +91 98765 43210">
                </div>

                <!-- WhatsApp Support -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                        WhatsApp Helpdesk Chat Number
                    </label>
                    <input type="text" name="support_whatsapp" value="{{ old('support_whatsapp', $settings['support_whatsapp'] ?? '+91 98765 43210') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="e.g. +91 98765 43210">
                </div>

                <!-- Company Tagline -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                        Platform Bio &amp; Footer Tagline
                    </label>
                    <input type="text" name="company_tagline" value="{{ old('company_tagline', $settings['company_tagline'] ?? '') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="The unified cloud business & restaurant ERP platform designed specifically for Indian SMEs...">
                </div>

                <!-- Physical Corporate Address -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                        Registered Corporate Address
                    </label>
                    <textarea name="company_address" rows="3" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="Plot No. 42, Cyber City, Phase 2, Gurugram, Haryana - 122002, India">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                </div>

                <!-- Business Operating Hours -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                        Support Operating Schedule
                    </label>
                    <input type="text" name="business_hours" value="{{ old('business_hours', $settings['business_hours'] ?? 'Mon - Sat: 9:00 AM - 7:00 PM IST') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="Mon - Sat: 9:00 AM - 7:00 PM IST">
                </div>
            </div>
        </div>

        <!-- TAB 3: Social Media Links -->
        <div x-show="activeTab === 'social'" x-cloak class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 sm:p-8 space-y-6">
            <div class="border-b border-slate-200 pb-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">🌐</span>
                    <h3 class="text-base font-extrabold text-slate-950">Social Media &amp; Public Profiles</h3>
                </div>
                <p class="text-xs text-slate-600 font-medium mt-1">
                    Connect your official social media pages to display clickable icons in the public website footer.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- WhatsApp Link -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[11px] font-bold">💬</span>
                        <span>WhatsApp Direct Link</span>
                    </label>
                    <input type="url" name="social_whatsapp" value="{{ old('social_whatsapp', $settings['social_whatsapp'] ?? '') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="https://wa.me/919876543210">
                </div>

                <!-- LinkedIn -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[11px] font-bold">💼</span>
                        <span>LinkedIn Company Page</span>
                    </label>
                    <input type="url" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="https://linkedin.com/company/vyapaargo">
                </div>

                <!-- Twitter / X -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center text-[11px] font-bold">✖️</span>
                        <span>Twitter / X Handle</span>
                    </label>
                    <input type="url" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="https://twitter.com/vyapaargo">
                </div>

                <!-- Instagram -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-[11px] font-bold">📸</span>
                        <span>Instagram Profile</span>
                    </label>
                    <input type="url" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="https://instagram.com/vyapaargo">
                </div>

                <!-- Facebook -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center text-[11px] font-bold">📘</span>
                        <span>Facebook Page</span>
                    </label>
                    <input type="url" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="https://facebook.com/vyapaargo">
                </div>

                <!-- YouTube -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-red-100 text-red-700 flex items-center justify-center text-[11px] font-bold">▶️</span>
                        <span>YouTube Channel</span>
                    </label>
                    <input type="url" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" 
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs" 
                        placeholder="https://youtube.com/@vyapaargo">
                </div>
            </div>
        </div>

        <!-- TAB 4: Dynamic FAQs -->
        <div x-show="activeTab === 'faqs'" x-cloak class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-base">❓</span>
                        <h3 class="text-base font-extrabold text-slate-950">Frequently Asked Questions (Dynamic FAQs)</h3>
                    </div>
                    <p class="text-xs text-slate-600 font-medium mt-1">
                        These dynamic Q&amp;A cards render on both the public Homepage and the Subscription Pricing page.
                    </p>
                </div>
                <button type="button" onclick="addFaqRow()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-extrabold text-slate-950 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 rounded-lg shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add New Question</span>
                </button>
            </div>

            <div id="faq-container" class="space-y-4">
                @forelse($faqs as $index => $faq)
                    <div class="faq-row border border-slate-200 rounded-xl p-5 bg-slate-50/60 space-y-3 transition hover:border-slate-300">
                        <div class="flex justify-between items-center gap-4">
                            <span class="text-xs font-extrabold text-slate-700 font-mono flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center text-[10px]">#</span>
                                <span>Question <span class="faq-num">{{ $index + 1 }}</span></span>
                            </span>
                            <button type="button" onclick="removeFaqRow(this)" class="px-2 py-1 text-xs font-bold text-rose-700 hover:text-rose-800 hover:bg-rose-50 rounded transition">
                                🗑️ Remove
                            </button>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Question Title</label>
                            <input type="text" name="faq_questions[]" value="{{ $faq['question'] }}" 
                                class="w-full border border-slate-300 rounded-lg px-3.5 py-2 text-sm bg-white text-slate-950 font-bold focus:ring-2 focus:ring-amber-500 shadow-2xs" 
                                placeholder="e.g. Can I use Vyapaargo on multiple devices and counter PCs?" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Answer Content</label>
                            <textarea name="faq_answers[]" rows="2" 
                                class="w-full border border-slate-300 rounded-lg px-3.5 py-2 text-sm bg-white text-slate-900 font-medium focus:ring-2 focus:ring-amber-500 shadow-2xs" 
                                placeholder="Enter detailed answer shown in accordion..." required>{{ $faq['answer'] }}</textarea>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center border-2 border-dashed border-slate-200 rounded-xl">
                        <div class="text-3xl mb-2">❓</div>
                        <p class="text-xs font-bold text-slate-700">No dynamic FAQs configured yet.</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Click "Add New Question" above to start populating questions.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 5: Privacy Policy -->
        <div x-show="activeTab === 'privacy'" x-cloak class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-base">🔒</span>
                        <h3 class="text-base font-extrabold text-slate-950">Dynamic Privacy Policy Content</h3>
                    </div>
                    <p class="text-xs text-slate-600 font-medium mt-1">
                        Rendered on the public web at <code>/privacy</code> with HTML formatting support.
                    </p>
                </div>
                @if(Route::has('public.privacy'))
                <a href="{{ route('public.privacy') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 transition">
                    <span>View Live Privacy Page</span>
                    <span class="text-slate-400">&rarr;</span>
                </a>
                @endif
            </div>

            <div class="space-y-2">
                <textarea name="privacy_policy_content" rows="16" class="rich-editor w-full text-sm text-slate-900 border border-slate-300 rounded-xl p-4 focus:ring-2 focus:ring-amber-500">{{ old('privacy_policy_content', $privacyPolicy) }}</textarea>
                <p class="text-[11px] text-slate-500 font-medium">Use the rich editor toolbar above for headings, lists, bold text, and hyperlinks.</p>
            </div>
        </div>

        <!-- TAB 6: Terms & Conditions -->
        <div x-show="activeTab === 'terms'" x-cloak class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-base">📄</span>
                        <h3 class="text-base font-extrabold text-slate-950">Terms &amp; Conditions Legal Agreement</h3>
                    </div>
                    <p class="text-xs text-slate-600 font-medium mt-1">
                        Rendered on the public web at <code>/terms</code> with HTML formatting support.
                    </p>
                </div>
                @if(Route::has('public.terms'))
                <a href="{{ route('public.terms') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 transition">
                    <span>View Live Terms Page</span>
                    <span class="text-slate-400">&rarr;</span>
                </a>
                @endif
            </div>

            <div class="space-y-2">
                <textarea name="terms_and_conditions_content" rows="16" class="rich-editor w-full text-sm text-slate-900 border border-slate-300 rounded-xl p-4 focus:ring-2 focus:ring-amber-500">{{ old('terms_and_conditions_content', $termsAndConditions) }}</textarea>
                <p class="text-[11px] text-slate-500 font-medium">Use the rich editor toolbar above for headings, lists, bold text, and legal clauses.</p>
            </div>
        </div>

        <!-- Sticky Save Action Bar -->
        <div class="p-4 bg-white border border-slate-200/90 rounded-xl shadow-2xs flex items-center justify-between gap-4 flex-wrap">
            <div class="text-xs text-slate-500 font-medium">
                Remember to save after editing any tab settings.
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    <span>Save All Platform Settings</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Dynamic FAQ Script -->
<script>
    function addFaqRow() {
        const container = document.getElementById('faq-container');
        const count = container.querySelectorAll('.faq-row').length + 1;
        
        const div = document.createElement('div');
        div.className = 'faq-row border border-slate-200 rounded-xl p-5 bg-slate-50/60 space-y-3 transition hover:border-slate-300';
        div.innerHTML = `
            <div class="flex justify-between items-center gap-4">
                <span class="text-xs font-extrabold text-slate-700 font-mono flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center text-[10px]">#</span>
                    <span>Question <span class="faq-num">${count}</span></span>
                </span>
                <button type="button" onclick="removeFaqRow(this)" class="px-2 py-1 text-xs font-bold text-rose-700 hover:text-rose-800 hover:bg-rose-50 rounded transition">
                    🗑️ Remove
                </button>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Question Title</label>
                <input type="text" name="faq_questions[]" value="" class="w-full border border-slate-300 rounded-lg px-3.5 py-2 text-sm bg-white text-slate-950 font-bold focus:ring-2 focus:ring-amber-500 shadow-2xs" placeholder="e.g. Can I upgrade my plan anytime?" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Answer Content</label>
                <textarea name="faq_answers[]" rows="2" class="w-full border border-slate-300 rounded-lg px-3.5 py-2 text-sm bg-white text-slate-900 font-medium focus:ring-2 focus:ring-amber-500 shadow-2xs" placeholder="Enter detailed answer shown in accordion..." required></textarea>
            </div>
        `;
        container.appendChild(div);
        reindexFaqs();
    }

    function removeFaqRow(btn) {
        const row = btn.closest('.faq-row');
        if (row) {
            row.remove();
            reindexFaqs();
        }
    }

    function reindexFaqs() {
        const rows = document.querySelectorAll('.faq-row');
        rows.forEach((row, idx) => {
            const num = row.querySelector('.faq-num');
            if (num) num.textContent = idx + 1;
        });
    }
</script>

<!-- Summernote Rich Text Editor CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('.rich-editor').summernote({
            height: 350,
            tabsize: 2,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onInit: function() {
                    $('.note-editor').addClass('border border-slate-300 rounded-xl overflow-hidden shadow-2xs');
                    $('.note-toolbar').addClass('bg-slate-50 border-b border-slate-200');
                }
            }
        });
    });
</script>
@endsection

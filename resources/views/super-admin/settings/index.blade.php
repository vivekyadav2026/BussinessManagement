@extends('layouts.super-admin')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-4" x-data="{ activeTab: 'platform' }">
    <div class="mb-8 flex justify-between items-center flex-wrap gap-4">
        <div>
            <div class="eyebrow mb-2">SYSTEM CONFIGURATION</div>
            <h1 class="text-2xl font-bold text-gray-900">Platform, Support &amp; Dynamic Content</h1>
            <p class="text-sm text-gray-500">Configure global SaaS rules, footer contact details, social links, dynamic FAQs, and legal policies.</p>
        </div>
        <div>
            <a href="{{ route('public.pricing') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">
                <span>View Public Pricing &rarr;</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6 shadow-sm flex items-center gap-3">
            <span class="text-green-600 font-bold text-lg">✓</span>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 mb-6 overflow-x-auto pb-1">
        <button type="button" @click="activeTab = 'platform'" :class="activeTab === 'platform' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 text-sm border-b-2 whitespace-nowrap transition">
            ⚙️ Platform &amp; Trial
        </button>
        <button type="button" @click="activeTab = 'contact'" :class="activeTab === 'contact' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 text-sm border-b-2 whitespace-nowrap transition">
            📞 Support &amp; Address
        </button>
        <button type="button" @click="activeTab = 'social'" :class="activeTab === 'social' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 text-sm border-b-2 whitespace-nowrap transition">
            🌐 Social Media Links
        </button>
        <button type="button" @click="activeTab = 'faqs'" :class="activeTab === 'faqs' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 text-sm border-b-2 whitespace-nowrap transition">
            ❓ Dynamic FAQs
        </button>
        <button type="button" @click="activeTab = 'privacy'" :class="activeTab === 'privacy' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 text-sm border-b-2 whitespace-nowrap transition">
            🔒 Privacy Policy
        </button>
        <button type="button" @click="activeTab = 'terms'" :class="activeTab === 'terms' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'" class="py-3 px-4 text-sm border-b-2 whitespace-nowrap transition">
            📄 Terms &amp; Conditions
        </button>
    </div>

    <form action="{{ route('super-admin.settings.update') }}" method="POST">
        @csrf

        <!-- TAB 1: Platform & Trial -->
        <div x-show="activeTab === 'platform'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div>
                <h3 class="text-base font-bold text-gray-900">Platform &amp; Free Trial Controls</h3>
                <p class="text-xs text-gray-500 mt-1">Manage trial duration for newly registered organizations.</p>
            </div>

            <div class="flex items-center justify-between py-4 border-t border-b border-gray-100">
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Enable Free Trial on Registration</h4>
                    <p class="text-xs text-gray-500 mt-0.5">If disabled, new tenants are blocked until an active paid subscription is selected.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enable_free_trial" value="1" {{ ($settings['enable_free_trial'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <div class="space-y-2 max-w-md">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Default Trial Duration</label>
                <div class="flex items-center gap-3">
                    <input type="number" name="trial_days" value="{{ old('trial_days', $settings['trial_days'] ?? 14) }}" min="0" max="365" class="w-32 border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <span class="text-sm font-semibold text-gray-500">Days</span>
                </div>
                <p class="text-xs text-gray-400">Common: <code>7</code>, <code>14</code>, <code>30</code> days.</p>
            </div>
        </div>

        <!-- TAB 2: Support & Address -->
        <div x-show="activeTab === 'contact'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div>
                <h3 class="text-base font-bold text-gray-900">Company &amp; Support Details</h3>
                <p class="text-xs text-gray-500 mt-1">These details appear dynamically on the public footer, invoice headers, and legal documentation.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Company / Brand Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? 'Vyapaargo') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Vyapaargo Technologies Pvt Ltd">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Support Email Address</label>
                    <input type="email" name="support_email" value="{{ old('support_email', $settings['support_email'] ?? 'support@vyapaargo.com') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="support@vyapaargo.com">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Support Helpline Phone</label>
                    <input type="text" name="support_phone" value="{{ old('support_phone', $settings['support_phone'] ?? '+91 98765 43210') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="+91 98765 43210">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Support WhatsApp Number / Direct Chat</label>
                    <input type="text" name="support_whatsapp" value="{{ old('support_whatsapp', $settings['support_whatsapp'] ?? '+91 98765 43210') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="+91 98765 43210">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Company Tagline / Bio</label>
                    <input type="text" name="company_tagline" value="{{ old('company_tagline', $settings['company_tagline'] ?? '') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="The unified cloud business & restaurant ERP platform...">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Physical Corporate Address</label>
                    <textarea name="company_address" rows="3" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Plot No. 42, Cyber City, Phase 2, Gurugram, Haryana - 122002, India">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Business Operating Hours</label>
                    <input type="text" name="business_hours" value="{{ old('business_hours', $settings['business_hours'] ?? 'Mon - Sat: 9:00 AM - 7:00 PM IST') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Mon - Sat: 9:00 AM - 7:00 PM IST">
                </div>
            </div>
        </div>

        <!-- TAB 3: Social Media Links -->
        <div x-show="activeTab === 'social'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div>
                <h3 class="text-base font-bold text-gray-900">Social Media URLs</h3>
                <p class="text-xs text-gray-500 mt-1">Configure profile links displayed in the website footer. Leave empty to hide.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">WhatsApp Direct Link</label>
                    <input type="url" name="social_whatsapp" value="{{ old('social_whatsapp', $settings['social_whatsapp'] ?? '') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500" placeholder="https://wa.me/919876543210">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">LinkedIn Company Page</label>
                    <input type="url" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500" placeholder="https://linkedin.com/company/vyapaargo">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Twitter / X Handle</label>
                    <input type="url" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500" placeholder="https://twitter.com/vyapaargo">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Instagram Profile</label>
                    <input type="url" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500" placeholder="https://instagram.com/vyapaargo">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Facebook Page</label>
                    <input type="url" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500" placeholder="https://facebook.com/vyapaargo">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">YouTube Channel</label>
                    <input type="url" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500" placeholder="https://youtube.com/@vyapaargo">
                </div>
            </div>
        </div>

        <!-- TAB 4: Dynamic FAQs -->
        <div x-show="activeTab === 'faqs'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Frequently Asked Questions (FAQs)</h3>
                    <p class="text-xs text-gray-500 mt-1">These FAQs are dynamically rendered on the Homepage and Pricing pages.</p>
                </div>
                <button type="button" onclick="addFaqRow()" class="px-4 py-2 text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition">
                    + Add New Question
                </button>
            </div>

            <div id="faq-container" class="space-y-4">
                @foreach($faqs as $index => $faq)
                    <div class="faq-row border border-gray-200 rounded-xl p-5 bg-gray-50/50 space-y-3">
                        <div class="flex justify-between items-center gap-4">
                            <span class="text-xs font-bold text-gray-400 font-mono">FAQ #<span class="faq-num">{{ $index + 1 }}</span></span>
                            <button type="button" onclick="removeFaqRow(this)" class="text-xs text-red-600 hover:text-red-800 font-bold">Remove</button>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Question</label>
                            <input type="text" name="faq_questions[]" value="{{ $faq['question'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-indigo-500 font-medium" placeholder="Enter question..." required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Answer</label>
                            <textarea name="faq_answers[]" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-indigo-500" placeholder="Enter detailed answer..." required>{{ $faq['answer'] }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- TAB 5: Privacy Policy -->
        <div x-show="activeTab === 'privacy'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Dynamic Privacy Policy Content</h3>
                    <p class="text-xs text-gray-500 mt-1">Rendered at <code>/privacy</code> with HTML formatting support.</p>
                </div>
                <a href="{{ route('public.privacy') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline">
                    View Live Page &rarr;
                </a>
            </div>

            <div>
                <textarea name="privacy_policy_content" rows="16" class="rich-editor w-full text-sm text-gray-800 border border-gray-300 rounded-xl p-4 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('privacy_policy_content', $privacyPolicy) }}</textarea>
                <p class="text-xs text-gray-400 mt-2">Supports standard HTML tags.</p>
            </div>
        </div>

        <!-- TAB 6: Terms & Conditions -->
        <div x-show="activeTab === 'terms'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Dynamic Terms &amp; Conditions Content</h3>
                    <p class="text-xs text-gray-500 mt-1">Rendered at <code>/terms</code> with HTML formatting support.</p>
                </div>
                <a href="{{ route('public.terms') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline">
                    View Live Page &rarr;
                </a>
            </div>

            <div>
                <textarea name="terms_and_conditions_content" rows="16" class="rich-editor w-full text-sm text-gray-800 border border-gray-300 rounded-xl p-4 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('terms_and_conditions_content', $termsAndConditions) }}</textarea>
                <p class="text-xs text-gray-400 mt-2">Supports standard HTML tags.</p>
            </div>
        </div>

        <!-- Save Button Footer -->
        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-sm transition flex items-center gap-2">
                <span>Save All Settings</span>
                <span class="text-indigo-200">&rarr;</span>
            </button>
        </div>
    </form>
</div>

<script>
    function addFaqRow() {
        const container = document.getElementById('faq-container');
        const count = container.children.length + 1;
        
        const div = document.createElement('div');
        div.className = 'faq-row border border-gray-200 rounded-xl p-5 bg-gray-50/50 space-y-3';
        div.innerHTML = `
            <div class="flex justify-between items-center gap-4">
                <span class="text-xs font-bold text-gray-400 font-mono">FAQ #<span class="faq-num">${count}</span></span>
                <button type="button" onclick="removeFaqRow(this)" class="text-xs text-red-600 hover:text-red-800 font-bold">Remove</button>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Question</label>
                <input type="text" name="faq_questions[]" value="" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-indigo-500 font-medium" placeholder="Enter question..." required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Answer</label>
                <textarea name="faq_answers[]" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-indigo-500" placeholder="Enter detailed answer..." required></textarea>
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
                    // Prevent any z-index issues with toolbars
                    $('.note-editor').addClass('border border-gray-300 rounded-xl overflow-hidden');
                    $('.note-toolbar').addClass('bg-gray-50 border-b border-gray-300');
                }
            }
        });
    });
</script>
@endsection

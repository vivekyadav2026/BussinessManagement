<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::getAllSettings();
        $faqs = SystemSetting::getFaqs();
        $privacyPolicy = SystemSetting::getPrivacyPolicy();
        $termsAndConditions = SystemSetting::getTermsAndConditions();

        return view('super-admin.settings.index', compact('settings', 'faqs', 'privacyPolicy', 'termsAndConditions'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'trial_days' => 'nullable|integer|min:0|max:365',
            'enable_free_trial' => 'nullable|boolean',
            'company_name' => 'nullable|string|max:255',
            'company_tagline' => 'nullable|string|max:500',
            'support_email' => 'nullable|email|max:255',
            'support_phone' => 'nullable|string|max:50',
            'support_whatsapp' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:1000',
            'business_hours' => 'nullable|string|max:255',
            'social_whatsapp' => 'nullable|string|max:255',
            'social_linkedin' => 'nullable|string|max:255',
            'social_twitter' => 'nullable|string|max:255',
            'social_facebook' => 'nullable|string|max:255',
            'social_instagram' => 'nullable|string|max:255',
            'social_youtube' => 'nullable|string|max:255',
            'privacy_policy_content' => 'nullable|string',
            'terms_and_conditions_content' => 'nullable|string',
            'faq_questions' => 'nullable|array',
            'faq_answers' => 'nullable|array',
        ]);

        // 1. Trial Settings
        if ($request->has('trial_days')) {
            $trialDays = $request->input('trial_days', 14);
            $enableTrial = $request->has('enable_free_trial') ? '1' : '0';
            if ($enableTrial === '0') {
                $trialDays = 0;
            }
            SystemSetting::set('trial_days', (string) $trialDays);
            SystemSetting::set('enable_free_trial', (string) $enableTrial);
        }

        // 2. Company & Support Details
        $textFields = [
            'company_name',
            'company_tagline',
            'support_email',
            'support_phone',
            'support_whatsapp',
            'company_address',
            'business_hours',
            'social_whatsapp',
            'social_linkedin',
            'social_twitter',
            'social_facebook',
            'social_instagram',
            'social_youtube',
            'privacy_policy_content',
            'terms_and_conditions_content',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                SystemSetting::set($field, (string) $request->input($field, ''));
            }
        }

        // 3. Process FAQs Array into JSON
        if ($request->has('faq_questions') && $request->has('faq_answers')) {
            $questions = $request->input('faq_questions', []);
            $answers = $request->input('faq_answers', []);
            
            $faqs = [];
            foreach ($questions as $index => $q) {
                $q = trim($q);
                $a = trim($answers[$index] ?? '');
                if (!empty($q) && !empty($a)) {
                    $faqs[] = [
                        'question' => $q,
                        'answer' => $a,
                    ];
                }
            }

            SystemSetting::set('faqs_json', json_encode($faqs, JSON_UNESCAPED_UNICODE));
        }

        return back()->with('success', 'Platform settings and dynamic content updated successfully.');
    }
}

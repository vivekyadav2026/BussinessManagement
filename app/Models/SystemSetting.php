<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getAllSettings(): array
    {
        return [
            'company_name' => static::get('company_name', 'Vyapaargo'),
            'company_tagline' => static::get('company_tagline', 'The unified cloud business & restaurant ERP platform designed specifically for Indian SMEs, retail shops, and food joints.'),
            'support_email' => static::get('support_email', 'support@vyapaargo.com'),
            'support_phone' => static::get('support_phone', '+91 98765 43210'),
            'support_whatsapp' => static::get('support_whatsapp', '+91 98765 43210'),
            'company_address' => static::get('company_address', 'Plot No. 42, Cyber City, Phase 2, Gurugram, Haryana - 122002, India'),
            'business_hours' => static::get('business_hours', 'Mon - Sat: 9:00 AM - 7:00 PM IST'),
            'social_whatsapp' => static::get('social_whatsapp', 'https://wa.me/919876543210'),
            'social_linkedin' => static::get('social_linkedin', 'https://linkedin.com/company/vyapaargo'),
            'social_twitter' => static::get('social_twitter', 'https://twitter.com/vyapaargo'),
            'social_facebook' => static::get('social_facebook', 'https://facebook.com/vyapaargo'),
            'social_instagram' => static::get('social_instagram', 'https://instagram.com/vyapaargo'),
            'social_youtube' => static::get('social_youtube', 'https://youtube.com/@vyapaargo'),
            'trial_days' => static::get('trial_days', '14'),
            'enable_free_trial' => static::get('enable_free_trial', '1'),
        ];
    }

    public static function getFaqs(): array
    {
        $raw = static::get('faqs_json');
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        return static::getDefaultFaqs();
    }

    public static function getDefaultFaqs(): array
    {
        return [
            [
                'question' => 'Vyapaargo setup karne me kitna samay lagta hai?',
                'answer' => 'Setup bilkul aasan hai. Aap 5 minutes me register karke products ya menu items add kar sakte hain aur turant billing shuru kar sakte hain. Koi complex hardware setup ki zarurat nahi hoti.'
            ],
            [
                'question' => 'Kya online payments automatic reconcile hoti hain?',
                'answer' => 'Haan, Razorpay verified webhooks ke zariye jab bhi koi customer invoice link ya QR scan karke UPI/Card se pay karta hai, invoice status automatically Paid me update ho jata hai aur customer ledger clear ho jata hai.'
            ],
            [
                'question' => 'Kya main ek hi account me multiple branches manage kar sakta hoon?',
                'answer' => 'Bilkul! Multi-Location functionality ke zariye aap apni alag-alag branches ka inventory stock, sales records, aur staff attendance alag-alag scope me track kar sakte hain, jabki owner ko consolidated multi-store reports milti hain.'
            ],
            [
                'question' => 'Restaurant QR Menu order karne ke liye customer ko koi app download karni padegi?',
                'answer' => 'Nahi, kisi bhi app ki zarurat nahi hai. Customer apne mobile camera ya kisi bhi scanner se table QR scan karega aur seedha browser me beautiful visual menu open ho jayega jahan se wo 1-tap me order place kar sakta hai.'
            ],
            [
                'question' => 'Kya hum thermal printer aur barcode scanner connect kar sakte hain?',
                'answer' => 'Haan! Vyapaargo sabhi standard 2-inch/3-inch thermal POS receipt printers (USB & Bluetooth) aur USB/Bluetooth handheld 1D/2D barcode scanners ke saath 100% seamlessly plug-and-play kaam karta hai.'
            ],
            [
                'question' => 'Kya main apna plan kabhi bhi upgrade ya cancel kar sakta hoon?',
                'answer' => 'Ji haan, aap kisi bhi samay apne Dashboard se plan switch, upgrade ya downgrade kar sakte hain. Koi hidden lock-in contract ya cancellation charges nahi hain.'
            ]
        ];
    }

    public static function getPrivacyPolicy(): string
    {
        $custom = static::get('privacy_policy_content');
        if (!empty($custom)) {
            return $custom;
        }

        return static::getDefaultPrivacyPolicy();
    }

    public static function getDefaultPrivacyPolicy(): string
    {
        return <<<'HTML'
<h2>1. Introduction & Overview</h2>
<p>Welcome to <strong>Vyapaargo</strong> ("we", "our", or "us"). We are committed to protecting the privacy, confidentiality, and security of our users' personal and business information. This Privacy Policy explains how we collect, use, disclose, and safeguard your data when you visit our website, register for an account, and use our cloud ERP, invoicing, restaurant POS, and inventory management services (collectively, the "Platform").</p>

<h2>2. Information We Collect</h2>
<p>To provide our multi-tenant business management and restaurant operations software, we collect the following types of information:</p>
<ul>
    <li><strong>Account & Profile Data:</strong> Name, business name, business address, email address, phone number, GSTIN, and login credentials when you register.</li>
    <li><strong>Business Operational Data:</strong> Product lists, menu catalogs, pricing, inventory stock logs, employee rosters, attendance records, and customer directories uploaded or entered by you.</li>
    <li><strong>Transaction & Invoicing Data:</strong> Sales invoices, payments received, pending receivables, tax calculations (CGST/SGST/IGST), customer phone numbers, and payment tokens.</li>
    <li><strong>Device & Usage Data:</strong> IP addresses, browser types, operating systems, POS hardware identifiers, and interaction logs within the application.</li>
</ul>

<h2>3. How We Use Your Information</h2>
<p>We process your data strictly to facilitate your business operations and maintain platform security:</p>
<ul>
    <li>To generate and deliver GST-compliant invoices, KOT tickets, and payment receipts.</li>
    <li>To process digital payments and UPI collections via RBI-authorized payment gateways like Razorpay.</li>
    <li>To send automated WhatsApp and SMS reminders for payment dues and stock alerts as configured by you.</li>
    <li>To calculate employee payroll and record attendance hours based on your organization’s settings.</li>
    <li>To protect against fraud, unauthorized access, and malicious activities.</li>
</ul>

<h2>4. Payment Security & Third-Party Gateways</h2>
<p>We do not store your full debit card, credit card, or UPI PIN numbers on our servers. All digital payment transactions are encrypted and processed through certified PCI-DSS Level 1 compliant gateways (e.g., Razorpay). Data transmission uses industry-standard 256-Bit SSL/TLS encryption.</p>

<h2>5. Multi-Tenant Data Isolation</h2>
<p>Vyapaargo implements strict database scoping and role-based access control (RBAC). Your business records, financial books, client list, and employee records are strictly isolated to your Organization ID and cannot be accessed or viewed by any other tenant or external business.</p>

<h2>6. Data Retention & Deletion</h2>
<p>We retain your business data for as long as your account remains active or as required by applicable Indian tax and financial record-keeping laws. You have the right to request full export or deletion of your business data upon account termination by contacting our support team.</p>

<h2>7. Cookies & Tracking Technologies</h2>
<p>We use essential session cookies and local storage tokens to keep you securely logged in, remember your branch location context, and preserve your cart or bill builder session.</p>

<h2>8. Compliance with Indian Laws (DPDP Act 2023)</h2>
<p>We adhere to the provisions of the Digital Personal Data Protection (DPDP) Act, 2023 and the Information Technology Act, 2000. You retain full ownership of your proprietary customer and sales data.</p>

<h2>9. Contact Us</h2>
<p>If you have any questions, concerns, or grievances regarding this Privacy Policy, please contact our Data Protection Officer at:</p>
<p><strong>Email:</strong> support@vyapaargo.com<br>
<strong>Address:</strong> Plot No. 42, Cyber City, Phase 2, Gurugram, Haryana - 122002, India</p>
HTML;
    }

    public static function getTermsAndConditions(): string
    {
        $custom = static::get('terms_and_conditions_content');
        if (!empty($custom)) {
            return $custom;
        }

        return static::getDefaultTerms();
    }

    public static function getDefaultTerms(): string
    {
        return <<<'HTML'
<h2>1. Agreement to Terms</h2>
<p>By accessing or using <strong>Vyapaargo</strong> ("Platform", "Service"), provided by Vyapaargo Technologies, you agree to be bound by these Terms and Conditions ("Terms"). If you disagree with any part of these terms, you must discontinue using our services immediately.</p>

<h2>2. Account Registration & Organization Responsibilities</h2>
<ul>
    <li>You must provide accurate, current, and complete information during registration.</li>
    <li>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</li>
    <li>Organization Admins are responsible for managing employee access rights, permissions, and location assignments appropriately.</li>
</ul>

<h2>3. Free Trial, Subscriptions & Billing</h2>
<ul>
    <li><strong>Free Trial:</strong> New registrations may receive a 14-day free trial with full access to standard features. No credit card is required to begin.</li>
    <li><strong>Subscription Plans:</strong> Upon expiration of the free trial, uninterrupted access requires selecting a paid subscription plan (Monthly or Yearly) or utilizing the Free tier quotas.</li>
    <li><strong>Taxes:</strong> All subscription pricing is exclusive of applicable Goods and Services Tax (GST) unless explicitly stated otherwise.</li>
    <li><strong>Add-ons:</strong> Add-on modules (e.g., additional locations, extra tables, staff quotas) can be purchased alongside base plans and will co-terminate with the main billing cycle.</li>
</ul>

<h2>4. Acceptable Use Policy</h2>
<p>You agree not to use the Platform to:</p>
<ul>
    <li>Process fraudulent transactions or counterfeit invoices.</li>
    <li>Violate applicable central, state, or local laws, including GST regulations and food safety standards.</li>
    <li>Attempt to reverse-engineer, decompile, or compromise the security and stability of the platform.</li>
    <li>Spam customers with unsolicited messages or abuse automated WhatsApp/SMS delivery channels.</li>
</ul>

<h2>5. Intellectual Property Rights</h2>
<p>The Platform design, source code, logos, trademarks, and documentation are the exclusive property of Vyapaargo. You retain 100% ownership of all business data, client details, catalogs, and transaction records uploaded by you to the service.</p>

<h2>6. Service Availability & SLA</h2>
<p>We strive to maintain a 99.9% uptime SLA for cloud operations. Scheduled maintenance windows will be communicated in advance. We are not liable for outages caused by upstream internet service disruptions or third-party payment gateway downtime.</p>

<h2>7. Limitation of Liability</h2>
<p>To the maximum extent permitted by law, Vyapaargo and its affiliates shall not be liable for any indirect, incidental, special, or consequential damages resulting from loss of profits, data, or business interruption.</p>

<h2>8. Governing Law & Jurisdiction</h2>
<p>These Terms shall be governed by and construed in accordance with the laws of the Republic of India. Any legal actions or proceedings shall be subject to the exclusive jurisdiction of the competent courts in Gurugram / New Delhi, India.</p>

<h2>9. Changes to Terms</h2>
<p>We reserve the right to modify these terms at any time. Continued use of the platform after changes are posted constitutes acceptance of the revised Terms.</p>
HTML;
    }
}

<x-guest-layout :fullCard="true">
    @php
        $initialStep = 1;
        if ($errors->has('name') || $errors->has('admin_phone') || $errors->has('email') || $errors->has('password') || $errors->has('password_confirmation')) {
            $initialStep = 2;
        } elseif ($errors->has('plan')) {
            $initialStep = 3;
        }
        $trialDays = $trialDays ?? (int)\App\Models\SystemSetting::get('trial_days', 14);
        $enableTrial = isset($enableTrial) ? $enableTrial : (\App\Models\SystemSetting::get('enable_free_trial', '1') === '1' && $trialDays > 0);
        $plans = $plans ?? \App\Models\Plan::where('is_active', true)->where('type', 'base')->with('features')->get();
        $addons = $addons ?? \App\Models\Plan::where('is_active', true)->where('type', 'addon')->with('features')->get();
        $razorpayKey = $razorpayKey ?? config('services.razorpay.key');

        $initialPlanId = request('plan');
        if (!$initialPlanId) {
            $initialPlanId = $enableTrial ? 'trial' : ($plans->first() ? $plans->first()->id : null);
        }
    @endphp

    <style>
        /* Scoped styles for Vyapaargo Multi-Step Registration */
        .reg-wrapper {
            width: 100%;
            margin: 0 auto;
        }
        .reg-card {
            background: #FFFFFF;
            border-radius: 24px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 20px 45px -12px rgba(11, 19, 43, 0.12), 0 2px 10px rgba(11, 19, 43, 0.04);
            display: grid;
            grid-template-columns: 1fr;
            overflow: hidden;
            min-height: 680px;
        }
        @media (min-width: 1024px) {
            .reg-card {
                grid-template-columns: 42% 58%;
            }
        }
        
        /* Left Column (Branding & Showcase) */
        .reg-showcase {
            background: linear-gradient(150deg, #0B132B 0%, #111C38 45%, #17233F 100%);
            color: #FFFFFF;
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .reg-showcase::before {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(217, 154, 43, 0.18) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .reg-showcase::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(20, 99, 86, 0.22) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .reg-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(217, 154, 43, 0.35);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 11px;
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 600;
            color: #F8D38D;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        .reg-badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #D99A2B;
            box-shadow: 0 0 8px #D99A2B;
        }
        .reg-headline {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 28px;
            line-height: 1.25;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }
        @media (min-width: 640px) {
            .reg-headline { font-size: 32px; }
        }
        .reg-subhead {
            font-size: 14px;
            line-height: 1.6;
            color: #94A3B8;
            margin-bottom: 28px;
        }
        .reg-feature-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 32px;
        }
        .reg-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .reg-feature-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(217, 154, 43, 0.15);
            border: 1px solid rgba(217, 154, 43, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #D99A2B;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .reg-feature-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #FFFFFF;
        }
        .reg-feature-desc {
            font-size: 12px;
            color: #94A3B8;
            margin-top: 2px;
            line-height: 1.45;
        }
        .reg-trust-box {
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 2;
        }
        .reg-trust-rating {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .reg-stars {
            color: #F59E0B;
            font-size: 14px;
            letter-spacing: 2px;
        }
        .reg-rating-val {
            font-size: 12px;
            font-weight: 700;
            color: #E2E8F0;
            margin-left: 6px;
        }
        .reg-chips {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 11px;
            color: #94A3B8;
        }
        .reg-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Right Column (Interactive Form) */
        .reg-form-pane {
            padding: 36px 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #FFFFFF;
        }
        @media (min-width: 640px) {
            .reg-form-pane {
                padding: 40px 44px;
            }
        }
        .reg-top-bar {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-bottom: 20px;
            margin-bottom: 24px;
            border-bottom: 1px solid #F1F5F9;
        }
        @media (min-width: 640px) {
            .reg-top-bar {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .reg-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: #0F172A;
        }
        .reg-login-link {
            font-size: 13px;
            color: #64748B;
        }
        .reg-login-link a {
            color: var(--gold-deep);
            font-weight: 700;
            text-decoration: none;
        }
        .reg-login-link a:hover {
            text-decoration: underline;
        }
        
        /* Stepper Component */
        .reg-stepper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 28px;
            position: relative;
        }
        .reg-step-btn {
            display: flex;
            flex-direction: column;
            gap: 6px;
            background: transparent;
            border: none;
            cursor: pointer;
            text-align: left;
            padding: 0;
            width: 100%;
        }
        .reg-step-bar {
            height: 4px;
            border-radius: 999px;
            width: 100%;
            background: #E2E8F0;
            transition: background 0.3s ease;
        }
        .reg-step-bar.active {
            background: var(--gold-deep);
        }
        .reg-step-label-row {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .reg-step-num {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10.5px;
            font-weight: 700;
            background: #E2E8F0;
            color: #64748B;
            transition: 0.2s;
            flex-shrink: 0;
        }
        .reg-step-num.active {
            background: #0B132B;
            color: #FFFFFF;
        }
        .reg-step-num.done {
            background: #10B981;
            color: #FFFFFF;
        }
        .reg-step-text {
            font-size: 12px;
            font-weight: 700;
            color: #94A3B8;
            transition: color 0.2s;
        }
        .reg-step-text.active {
            color: #0F172A;
        }
        
        /* Form Inputs & Elements */
        .reg-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 8px;
            margin-bottom: 16px;
            border-bottom: 1px solid #F1F5F9;
        }
        .reg-section-tag {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #334155;
            font-family: 'IBM Plex Mono', monospace;
        }
        .reg-field-group {
            margin-bottom: 16px;
        }
        .reg-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #334155;
            margin-bottom: 6px;
        }
        .reg-input-box {
            position: relative;
            display: flex;
            align-items: center;
        }
        .reg-input-icon {
            position: absolute;
            left: 12px;
            display: flex;
            align-items: center;
            pointer-events: none;
            color: #94A3B8;
        }
        .reg-input {
            width: 100%;
            height: 42px;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
            color: #0F172A;
            font-size: 13.5px;
            font-family: inherit;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .reg-input.with-icon {
            padding-left: 38px;
        }
        .reg-input:focus {
            border-color: #D99A2B;
            box-shadow: 0 0 0 3px rgba(217, 154, 43, 0.18);
            outline: none;
        }
        .reg-input::placeholder {
            color: #94A3B8;
        }
        .reg-grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }
        @media (min-width: 640px) {
            .reg-grid-2 {
                grid-template-columns: 1fr 1fr;
            }
        }
        .reg-grid-3 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }
        @media (min-width: 640px) {
            .reg-grid-3 {
                grid-template-columns: 1fr 1.2fr 1fr;
            }
        }
        
        /* Action Buttons */
        .reg-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #F1F5F9;
        }
        .reg-btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.15s;
        }
        .reg-btn-back:hover {
            background: #F8FAFC;
            color: #0F172A;
            border-color: #94A3B8;
        }
        .reg-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 24px;
            font-size: 13.5px;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            color: #FFFFFF;
            background: #0B132B;
            border: 1px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(11, 19, 43, 0.15);
            transition: all 0.15s ease;
        }
        .reg-btn-primary:hover {
            background: var(--gold-deep);
            color: #0B132B;
            box-shadow: 0 6px 16px rgba(184, 127, 27, 0.25);
        }
        
        /* Summary Box for Step 3 */
        .reg-summary-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 12.5px;
            margin-bottom: 16px;
        }
        .reg-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px dashed #E2E8F0;
        }
        .reg-summary-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .reg-summary-label {
            color: #64748B;
        }
        .reg-summary-val {
            font-weight: 700;
            color: #0F172A;
            text-align: right;
        }

        /* Plan Selection Styles */
        .plan-cycle-toggle-box {
            display: inline-flex;
            align-items: center;
            background: #F1F5F9;
            padding: 4px;
            border-radius: 999px;
            gap: 4px;
            margin-bottom: 16px;
        }
        .plan-cycle-btn {
            border: none;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            background: transparent;
            color: #64748B;
        }
        .plan-cycle-btn.active {
            background: #0B132B;
            color: #FFFFFF;
            box-shadow: 0 2px 6px rgba(11, 19, 43, 0.2);
        }
        .plan-cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }
        .plan-card-item {
            border: 2px solid #E2E8F0;
            border-radius: 14px;
            padding: 14px 16px;
            cursor: pointer;
            background: #FFFFFF;
            transition: all 0.2s ease;
            position: relative;
        }
        .plan-card-item:hover {
            border-color: #CBD5E1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .plan-card-item.selected {
            border-color: #D99A2B;
            background: #FFFDF8;
            box-shadow: 0 0 0 1px #D99A2B, 0 4px 14px rgba(217, 154, 43, 0.12);
        }
        .plan-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .plan-name-title {
            font-size: 14px;
            font-weight: 800;
            color: #0F172A;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .plan-price-tag {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: #0F172A;
            text-align: right;
        }
        .plan-price-sub {
            font-size: 10.5px;
            color: #64748B;
            font-weight: 500;
        }
        .plan-radio-circle {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #CBD5E1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .plan-card-item.selected .plan-radio-circle {
            border-color: #D99A2B;
            background: #D99A2B;
        }
        .plan-radio-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #FFFFFF;
        }
        .plan-badge-pill {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 2px 7px;
            border-radius: 999px;
            letter-spacing: 0.03em;
        }
        
        /* Add-ons Section Styles */
        .addon-box-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
            margin-top: 14px;
            margin-bottom: 16px;
        }
        .addon-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            background: #FAFAFA;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .addon-item-row:hover {
            border-color: #CBD5E1;
            background: #FFFFFF;
        }
        .addon-item-row.selected {
            border-color: #D99A2B;
            background: #FFFDF8;
        }
        .addon-checkbox-custom {
            width: 17px;
            height: 17px;
            border-radius: 5px;
            border: 2px solid #CBD5E1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.15s ease;
            background: #FFFFFF;
        }
        .addon-item-row.selected .addon-checkbox-custom {
            border-color: #D99A2B;
            background: #D99A2B;
            color: #FFFFFF;
        }
    </style>

    <div class="reg-wrapper" x-data="registrationApp()">
        <div class="reg-card">
            <!-- Left Branding & Trust Showcase -->
            <div class="reg-showcase">
                <div>
                    <!-- Brand Logo -->
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                        <div style="width: 48px; height: 48px; border-radius: 14px; background: #ffffff; display: flex; align-items: center; justify-content: center; padding: 6px; box-shadow: 0 4px 14px rgba(0,0,0,0.25); flex-shrink: 0;">
                            <img src="{{ asset('images/logo.png') }}" alt="Vyapaargo" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div>
                            <span style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #ffffff; letter-spacing: -0.02em; display: block; line-height: 1.1;">Vyapaargo</span>
                            <span style="font-size: 11px; font-family: 'IBM Plex Mono', monospace; color: var(--gold); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600;">Unified Business Cloud</span>
                        </div>
                    </div>

                    <!-- Badge -->
                    <div class="reg-badge">
                        <span class="reg-badge-dot"></span>
                        Unified ERP &amp; POS Platform
                    </div>

                    <!-- Headline -->
                    <h2 class="reg-headline">
                        Scale your shop or restaurant with <span style="color: var(--gold);">Vyapaargo</span>.
                    </h2>
                    <p class="reg-subhead">
                        Join over 10,000+ growing retail outlets, supermarkets, and restaurants across India managing billing, inventory, and staff on one platform.
                    </p>

                    <!-- Feature Highlights -->
                    <div class="reg-feature-list">
                        <div class="reg-feature-item">
                            <div class="reg-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <div class="reg-feature-title">Lightning Fast Billing &amp; Invoicing</div>
                                <div class="reg-feature-desc">GST compliant bills, thermal &amp; A4 printing, UPI QR auto-payments.</div>
                            </div>
                        </div>

                        <div class="reg-feature-item">
                            <div class="reg-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <div class="reg-feature-title">Live Inventory &amp; Stock Tracking</div>
                                <div class="reg-feature-desc">Barcode scanning, low-stock threshold alerts &amp; supplier purchases.</div>
                            </div>
                        </div>

                        <div class="reg-feature-item">
                            <div class="reg-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <div class="reg-feature-title">Restaurant POS &amp; Table QR Orders</div>
                                <div class="reg-feature-desc">Instant KOT printing, digital dining menus &amp; waiter apps.</div>
                            </div>
                        </div>

                        <div class="reg-feature-item">
                            <div class="reg-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <div class="reg-feature-title">Staff Attendance &amp; Payroll</div>
                                <div class="reg-feature-desc">Automated salary calculation, shift management &amp; attendance logs.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Trust Elements -->
                <div class="reg-trust-box">
                    <div class="reg-chips">
                        <span class="reg-chip">
                            <svg width="13" height="13" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            256-Bit SSL
                        </span>
                        <span class="reg-chip">
                            <svg width="13" height="13" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            No Card Required
                        </span>
                        <span class="reg-chip">
                            <svg width="13" height="13" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            14-Day Free Trial
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Interactive Form Column -->
            <div class="reg-form-pane">
                <div>
                    <!-- Top Title Bar -->
                    <div class="reg-top-bar">
                        <div>
                            <h3 class="reg-title">Get Started Free</h3>
                            <p style="font-size: 13px; color: #64748B; margin-top: 3px;">Set up your business in under 2 minutes.</p>
                        </div>
                        <div class="reg-login-link">
                            Have an account?
                            <a href="{{ route('login') }}">Sign in &rarr;</a>
                        </div>
                    </div>

                    <!-- 3-Step Wizard Navigation Bar -->
                    <div class="reg-stepper">
                        <!-- Step 1 Button -->
                        <button type="button" class="reg-step-btn" @click="step = 1">
                            <div class="reg-step-bar" :class="step >= 1 ? 'active' : ''"></div>
                            <div class="reg-step-label-row">
                                <span class="reg-step-num" :class="step === 1 ? 'active' : (step > 1 ? 'done' : '')">
                                    <span x-show="step <= 1">1</span>
                                    <svg x-show="step > 1" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="reg-step-text" :class="step >= 1 ? 'active' : ''">Business</span>
                            </div>
                        </button>

                        <!-- Step 2 Button -->
                        <button type="button" class="reg-step-btn" @click="step >= 2 ? step = 2 : null">
                            <div class="reg-step-bar" :class="step >= 2 ? 'active' : ''"></div>
                            <div class="reg-step-label-row">
                                <span class="reg-step-num" :class="step === 2 ? 'active' : (step > 2 ? 'done' : '')">
                                    <span x-show="step <= 2">2</span>
                                    <svg x-show="step > 2" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="reg-step-text" :class="step >= 2 ? 'active' : ''">Owner</span>
                            </div>
                        </button>

                        <!-- Step 3 Button -->
                        <button type="button" class="reg-step-btn">
                            <div class="reg-step-bar" :class="step >= 3 ? 'active' : ''"></div>
                            <div class="reg-step-label-row">
                                <span class="reg-step-num" :class="step === 3 ? 'active' : ''">
                                    <span>3</span>
                                </span>
                                <span class="reg-step-text" :class="step >= 3 ? 'active' : ''">Plan</span>
                            </div>
                        </button>
                    </div>

                    <!-- Server Error Notice -->
                    @if ($errors->any())
                        <div style="background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px; font-size: 13px; color: #991B1B;">
                            <div style="font-weight: 700; display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Please resolve the following errors:
                            </div>
                            <ul style="padding-left: 20px; margin: 0; line-height: 1.5;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('register') }}" id="registrationForm">
                        @csrf

                        <!-- ================= STEP 1: BUSINESS DETAILS ================= -->
                        <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="reg-section-header">
                                <span class="reg-section-tag">Step 1 · Business Profile</span>
                                <span style="font-size: 11px; color: #94A3B8; font-weight: 600;">* Required</span>
                            </div>

                            <!-- Business / Shop Name -->
                            <div class="reg-field-group">
                                <label for="organization_name" class="reg-label">Business / Shop Name *</label>
                                <div class="reg-input-box">
                                    <div class="reg-input-icon">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12"/></svg>
                                    </div>
                                    <input id="organization_name" name="organization_name" type="text" required
                                           class="reg-input with-icon"
                                           value="{{ old('organization_name') }}" placeholder="e.g. Royal Enterprises / Cafe Delight">
                                </div>
                            </div>

                            <!-- Category & Phone -->
                            <div class="reg-grid-2 reg-field-group">
                                <div>
                                    <label for="business_type" class="reg-label">Business Category *</label>
                                    <select id="business_type" name="business_type" required class="reg-input" style="font-weight: 500;">
                                        <option value="business" {{ old('business_type') === 'business' ? 'selected' : '' }}>Retail &amp; Wholesale ERP</option>
                                        <option value="restaurant" {{ old('business_type') === 'restaurant' ? 'selected' : '' }}>Restaurant / Cafe / Food POS</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="business_phone" class="reg-label">Business Mobile *</label>
                                    <div class="reg-input-box">
                                        <div class="reg-input-icon">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <input id="business_phone" name="business_phone" type="tel" required
                                               class="reg-input with-icon"
                                               value="{{ old('business_phone') }}" placeholder="9876543210">
                                    </div>
                                </div>
                            </div>

                            <!-- GST Number -->
                            <div class="reg-field-group">
                                <label for="gst_number" class="reg-label">GSTIN Number <span style="font-weight: 400; text-transform: lowercase; color: #94A3B8;">(optional)</span></label>
                                <input id="gst_number" name="gst_number" type="text"
                                       class="reg-input"
                                       style="text-transform: uppercase; font-family: 'IBM Plex Mono', monospace;"
                                       value="{{ old('gst_number') }}" placeholder="22AAAAA0000A1Z5">
                            </div>

                            <!-- Country, State, Pincode -->
                            <div class="reg-grid-3 reg-field-group">
                                <div>
                                    <label for="country" class="reg-label">Country *</label>
                                    <input id="country" name="country" type="text" required
                                           class="reg-input"
                                           value="{{ old('country', 'India') }}">
                                </div>
                                <div>
                                    <label for="state" class="reg-label">State *</label>
                                    <input id="state" name="state" type="text" required
                                           class="reg-input"
                                           value="{{ old('state') }}" placeholder="Gujarat / Delhi">
                                </div>
                                <div>
                                    <label for="pincode" class="reg-label">Pincode *</label>
                                    <input id="pincode" name="pincode" type="text" required
                                           class="reg-input"
                                           value="{{ old('pincode') }}" placeholder="380001">
                                </div>
                            </div>

                            <!-- Street Address -->
                            <div class="reg-field-group">
                                <label for="address" class="reg-label">Shop / Office Street Address *</label>
                                <input id="address" name="address" type="text" required
                                       class="reg-input"
                                       value="{{ old('address') }}" placeholder="Shop No 14, Commercial Complex, MG Road">
                            </div>

                            <!-- Step 1 Button -->
                            <div class="reg-actions">
                                <button type="button" @click="validateStep1()" class="reg-btn-primary">
                                    <span>Continue to Owner Details</span>
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- ================= STEP 2: OWNER ACCOUNT ================= -->
                        <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                            <div class="reg-section-header">
                                <span class="reg-section-tag">Step 2 · Owner Credentials</span>
                                <span style="font-size: 11px; color: #94A3B8; font-weight: 600;">Account Login</span>
                            </div>

                            <!-- Full Name & Phone -->
                            <div class="reg-grid-2 reg-field-group">
                                <div>
                                    <label for="name" class="reg-label">Owner Full Name *</label>
                                    <div class="reg-input-box">
                                        <div class="reg-input-icon">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        <input id="name" name="name" type="text" autocomplete="name" required
                                               class="reg-input with-icon"
                                               value="{{ old('name') }}" placeholder="Rahul Sharma">
                                    </div>
                                </div>

                                <div>
                                    <label for="admin_phone" class="reg-label">Personal Mobile *</label>
                                    <div class="reg-input-box">
                                        <div class="reg-input-icon">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                        <input id="admin_phone" name="admin_phone" type="tel" required
                                               class="reg-input with-icon"
                                               value="{{ old('admin_phone') }}" placeholder="9876543210">
                                    </div>
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div class="reg-field-group">
                                <label for="email" class="reg-label">Admin Email Address *</label>
                                <div class="reg-input-box">
                                    <div class="reg-input-icon">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                           class="reg-input with-icon"
                                           value="{{ old('email') }}" placeholder="owner@business.com">
                                </div>
                            </div>

                            <!-- Password & Confirm Password -->
                            <div class="reg-grid-2 reg-field-group">
                                <div x-data="{ show: false }">
                                    <label for="password" class="reg-label">Password *</label>
                                    <div class="reg-input-box">
                                        <input id="password" name="password" :type="show ? 'text' : 'password'" required
                                               class="reg-input" style="padding-right: 36px;"
                                               placeholder="••••••••">
                                        <button type="button" @click="show = !show" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #94A3B8;">
                                            <svg x-show="!show" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="show" style="display:none;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div x-data="{ show: false }">
                                    <label for="password_confirmation" class="reg-label">Confirm Password *</label>
                                    <div class="reg-input-box">
                                        <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required
                                               class="reg-input" style="padding-right: 36px;"
                                               placeholder="••••••••">
                                        <button type="button" @click="show = !show" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #94A3B8;">
                                            <svg x-show="!show" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="show" style="display:none;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2 Navigation Buttons -->
                            <div class="reg-actions" style="justify-content: space-between;">
                                <button type="button" @click="step = 1; window.scrollTo({top: 40, behavior: 'smooth'})" class="reg-btn-back">
                                    &larr; Back
                                </button>
                                <button type="button" @click="validateStep2()" class="reg-btn-primary">
                                    <span>Continue to Plan</span>
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- ================= STEP 3: SUBSCRIPTION & CONFIRMATION ================= -->
                        <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                            <div class="reg-section-header">
                                <span class="reg-section-tag">Step 3 · Choose Plan &amp; Activate</span>
                                <span style="font-size: 11px; color: #10B981; font-weight: 700; background: #ECFDF5; padding: 2px 8px; border-radius: 999px;">Transparent Pricing</span>
                            </div>

                            <!-- Hidden Payment & Plan Inputs -->
                            <input type="hidden" name="plan" :value="selected_plan">
                            <input type="hidden" name="billing_cycle" :value="billing_cycle">
                            <input type="hidden" name="razorpay_order_id" id="reg_razorpay_order_id">
                            <input type="hidden" name="razorpay_payment_id" id="reg_razorpay_payment_id">
                            <input type="hidden" name="razorpay_signature" id="reg_razorpay_signature">

                            <!-- Billing Cycle Switcher -->
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <div style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em;">
                                    Select Billing Frequency:
                                </div>
                                <div class="plan-cycle-toggle-box">
                                    <button type="button" class="plan-cycle-btn" :class="billing_cycle === 'monthly' ? 'active' : ''" @click="billing_cycle = 'monthly'">
                                        Monthly
                                    </button>
                                    <button type="button" class="plan-cycle-btn" :class="billing_cycle === 'yearly' ? 'active' : ''" @click="billing_cycle = 'yearly'">
                                        Yearly <span style="background: #10B981; color: #FFFFFF; font-size: 9.5px; padding: 1px 5px; border-radius: 999px; margin-left: 2px;">SAVE</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Plan Selection Cards -->
                            <div class="plan-cards-grid">
                                {{-- 1. Free Trial Option (Only shown if Super Admin enabled it) --}}
                                @if($enableTrial)
                                    <div class="plan-card-item" :class="selected_plan === 'trial' ? 'selected' : ''" @click="selected_plan = 'trial'">
                                        <div class="plan-card-header">
                                            <div>
                                                <div class="plan-name-title">
                                                    <span class="plan-radio-circle">
                                                        <span class="plan-radio-dot" x-show="selected_plan === 'trial'"></span>
                                                    </span>
                                                    <span>{{ $trialDays }}-Days Free Trial</span>
                                                    <span class="plan-badge-pill" style="background: #ECFDF5; color: #059669;">Zero Risk</span>
                                                </div>
                                                <div style="font-size: 11.5px; color: #64748B; margin-top: 3px; padding-left: 24px;">
                                                    Instant trial access without credit card. Full software access enabled.
                                                </div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div class="plan-price-tag" style="color: #059669;">₹0</div>
                                                <div class="plan-price-sub">Free for {{ $trialDays }} days</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- 2. Database Base Plans (Filtered by selected business category) --}}
                                @foreach($plans as $plan)
                                    @php
                                        $isRest = $plan->category === 'restaurant';
                                        $monthlyPrice = (float)$plan->price_monthly;
                                        $yearlyPrice = (float)$plan->price_yearly;
                                    @endphp
                                    <div class="plan-card-item"
                                         x-show="(business_type === 'restaurant' && '{{ $plan->category }}' === 'restaurant') || (business_type !== 'restaurant' && '{{ $plan->category }}' !== 'restaurant')"
                                         :class="selected_plan == '{{ $plan->id }}' ? 'selected' : ''"
                                         @click="selectBasePlan('{{ $plan->id }}')">
                                        <div class="plan-card-header">
                                            <div>
                                                <div class="plan-name-title">
                                                    <span class="plan-radio-circle">
                                                        <span class="plan-radio-dot" x-show="selected_plan == '{{ $plan->id }}'"></span>
                                                    </span>
                                                    <span>{{ $plan->name }}</span>
                                                    @if($isRest)
                                                        <span class="plan-badge-pill" style="background: #FFF7ED; color: #C2410C;">Restaurant &amp; Food POS</span>
                                                    @else
                                                        <span class="plan-badge-pill" style="background: #EFF6FF; color: #1D4ED8;">Retail &amp; Wholesale ERP</span>
                                                    @endif
                                                </div>
                                                <div style="font-size: 11.5px; color: #64748B; margin-top: 3px; padding-left: 24px;">
                                                    {{ $plan->description ?: 'Complete business management, billing & inventory module.' }}
                                                </div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div class="plan-price-tag" x-show="billing_cycle === 'monthly'">
                                                    ₹{{ number_format($monthlyPrice, 0) }}
                                                    <span class="plan-price-sub">/mo</span>
                                                </div>
                                                <div class="plan-price-tag" x-show="billing_cycle === 'yearly'" style="display:none;">
                                                    ₹{{ number_format($yearlyPrice, 0) }}
                                                    <span class="plan-price-sub">/yr</span>
                                                </div>
                                                <div class="plan-price-sub" x-show="billing_cycle === 'yearly'" style="color: #10B981; font-weight: 700; display:none;">
                                                    + GST
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Detailed Plan Features Listing --}}
                                        <div style="padding-left: 24px; margin-top: 8px; display: grid; grid-template-columns: 1fr; gap: 4px;">
                                            @foreach($plan->features as $feat)
                                                @php
                                                    $fVal = strtolower(trim($feat->feature_value));
                                                    $fCode = ucwords(str_replace('_', ' ', $feat->feature_code));
                                                @endphp
                                                @if($fVal !== 'false' && $fVal !== '0')
                                                    <div style="font-size: 11.5px; color: #334155; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px dashed #F1F5F9; padding: 2px 0;">
                                                        <span>• {{ $fCode }}</span>
                                                        <strong style="color: #0F172A; font-family: monospace;">{{ $fVal === 'true' ? 'Included' : $feat->feature_value }}</strong>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- 3. Optional Add-ons Section (Checkbox selection) --}}
                            @if(isset($addons) && $addons->count() > 0)
                                <div style="margin-top: 18px; margin-bottom: 8px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div style="font-size: 12px; font-weight: 800; color: #1E293B; text-transform: uppercase; letter-spacing: 0.04em; display: flex; align-items: center; gap: 6px;">
                                            <span>Power-up Add-ons (Optional)</span>
                                            <span style="font-size: 10px; background: #FEF3C7; color: #B45309; padding: 1px 6px; border-radius: 999px;">Boost Features</span>
                                        </div>
                                        <span style="font-size: 11px; color: #64748B;">Select any add-ons you need</span>
                                    </div>

                                    <div class="addon-box-grid">
                                        @foreach($addons as $addon)
                                            @php
                                                $addonMonthly = (float)$addon->price_monthly;
                                                $addonYearly = (float)$addon->price_yearly;
                                            @endphp
                                            <div class="addon-item-row"
                                                 :class="selected_addons.includes('{{ $addon->id }}') ? 'selected' : ''"
                                                 @click="toggleAddon('{{ $addon->id }}')">
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <span class="addon-checkbox-custom">
                                                        <svg x-show="selected_addons.includes('{{ $addon->id }}')" width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    </span>
                                                    <div>
                                                        <div style="font-size: 12.5px; font-weight: 700; color: #0F172A;">
                                                            {{ $addon->name }}
                                                        </div>
                                                        <div style="font-size: 11px; color: #64748B;">
                                                            {{ $addon->description ?: 'Extra capability module' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="text-align: right; flex-shrink: 0;">
                                                    <div style="font-size: 13px; font-weight: 800; color: #0F172A; font-family: 'Space Grotesk', sans-serif;">
                                                        <span x-show="billing_cycle === 'monthly'">+₹{{ number_format($addonMonthly, 0) }}/mo</span>
                                                        <span x-show="billing_cycle === 'yearly'" style="display:none;">+₹{{ number_format($addonYearly, 0) }}/yr</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Hidden container for addon inputs -->
                            <template x-for="addonId in selected_addons" :key="addonId">
                                <input type="hidden" name="addon_ids[]" :value="addonId">
                            </template>

                            <!-- Summary Box -->
                            <div class="reg-summary-card">
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569; letter-spacing: 0.05em; margin-bottom: 8px;">
                                    Review Your Selection:
                                </div>
                                <div class="reg-summary-row">
                                    <span class="reg-summary-label">Business Name:</span>
                                    <span class="reg-summary-val" x-text="business_name || 'Not provided'"></span>
                                </div>
                                <div class="reg-summary-row">
                                    <span class="reg-summary-label">Category:</span>
                                    <span class="reg-summary-val" x-text="business_type === 'restaurant' ? 'Restaurant / Food POS' : 'Retail & Wholesale ERP'"></span>
                                </div>
                                <div class="reg-summary-row">
                                    <span class="reg-summary-label">Plan Selected:</span>
                                    <span class="reg-summary-val" x-text="getSelectedPlanName()"></span>
                                </div>
                                <div class="reg-summary-row" x-show="selected_addons.length > 0">
                                    <span class="reg-summary-label">Add-ons Selected:</span>
                                    <span class="reg-summary-val" x-text="selected_addons.length + ' Add-on(s)'"></span>
                                </div>
                                <div class="reg-summary-row">
                                    <span class="reg-summary-label">Payment Due Today:</span>
                                    <span class="reg-summary-val" style="color: #059669; font-size: 14px;" x-text="calculateTotalDisplay()">
                                    </span>
                                </div>
                            </div>

                            <!-- Terms Notice -->
                            <div style="font-size: 11.5px; color: #64748B; text-align: center; margin-bottom: 8px;">
                                By clicking below, you agree to our 
                                <a href="{{ route('public.terms') }}" target="_blank" style="color: var(--gold-deep); font-weight: 700; text-decoration: none;">Terms of Service</a> 
                                and 
                                <a href="{{ route('public.privacy') }}" target="_blank" style="color: var(--gold-deep); font-weight: 700; text-decoration: none;">Privacy Policy</a>.
                            </div>

                            <!-- Step 3 Action Buttons -->
                            <div class="reg-actions" style="justify-content: space-between;">
                                <button type="button" @click="step = 2; window.scrollTo({top: 40, behavior: 'smooth'})" class="reg-btn-back">
                                    &larr; Back
                                </button>
                                
                                <button type="button" id="btnLaunchAccount" @click="handleRegistrationCheckout()" :disabled="is_processing" class="reg-btn-primary" style="padding: 12px 28px; font-size: 14px;">
                                    <span x-show="!is_processing">
                                        <span x-show="selected_plan === 'trial'">Start Free Trial &amp; Launch &rarr;</span>
                                        <span x-show="selected_plan !== 'trial'">Pay &amp; Launch Account &rarr;</span>
                                    </span>
                                    <span x-show="is_processing" style="display:none;" x-text="processing_msg">
                                        Processing...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Razorpay Checkout JS -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
    function registrationApp() {
        return {
            step: {{ $initialStep }},
            business_name: '{{ old('organization_name') }}',
            business_type: '{{ old('business_type', 'business') }}',
            owner_name: '{{ old('name') }}',
            owner_email: '{{ old('email') }}',
            owner_phone: '{{ old('admin_phone') }}',
            selected_plan: '{{ old('plan', $initialPlanId) }}',
            billing_cycle: '{{ old('billing_cycle', 'monthly') }}',
            selected_addons: [],
            is_processing: false,
            processing_msg: 'Processing payment...',

            all_plans: @json($plans),
            all_addons: @json($addons),

            selectBasePlan(planId) {
                this.selected_plan = planId;
            },

            toggleAddon(addonId) {
                const idStr = String(addonId);
                const idx = this.selected_addons.indexOf(idStr);
                if (idx > -1) {
                    this.selected_addons.splice(idx, 1);
                } else {
                    this.selected_addons.push(idStr);
                }
            },

            getSelectedPlanName() {
                if (this.selected_plan === 'trial') {
                    return '{{ $trialDays }}-Days Free Trial';
                }
                const p = this.all_plans.find(item => String(item.id) === String(this.selected_plan));
                return p ? p.name : 'Custom Plan';
            },

            calculateTotalDue() {
                if (this.selected_plan === 'trial') {
                    return 0;
                }

                let total = 0;
                const isYearly = this.billing_cycle === 'yearly';

                const p = this.all_plans.find(item => String(item.id) === String(this.selected_plan));
                if (p) {
                    total += parseFloat(isYearly ? p.price_yearly : p.price_monthly) || 0;
                }

                this.selected_addons.forEach(aId => {
                    const addon = this.all_addons.find(item => String(item.id) === String(aId));
                    if (addon) {
                        total += parseFloat(isYearly ? addon.price_yearly : addon.price_monthly) || 0;
                    }
                });

                return total;
            },

            calculateTotalDisplay() {
                if (this.selected_plan === 'trial') {
                    return '₹0 (Free Trial)';
                }
                const total = this.calculateTotalDue();
                const cycleText = this.billing_cycle === 'yearly' ? ' /year' : ' /month';
                return '₹' + Math.round(total).toLocaleString('en-IN') + cycleText;
            },

            validateStep1() {
                let isValid = true;
                const fields = ['organization_name', 'business_type', 'business_phone', 'country', 'state', 'pincode', 'address'];
                
                fields.forEach(id => {
                    const el = document.getElementById(id);
                    if (!el || !el.value.trim()) {
                        isValid = false;
                        if (el) {
                            el.style.borderColor = '#EF4444';
                            el.style.backgroundColor = '#FEF2F2';
                        }
                    } else {
                        if (el) {
                            el.style.borderColor = '#CBD5E1';
                            el.style.backgroundColor = '#FFFFFF';
                        }
                    }
                });

                if (isValid) {
                    this.business_name = document.getElementById('organization_name').value;
                    this.business_type = document.getElementById('business_type').value;

                    // Automatically select default matching plan if switching category and not in trial
                    if (this.selected_plan !== 'trial') {
                        const matchingPlan = this.all_plans.find(p => 
                            (this.business_type === 'restaurant' && p.category === 'restaurant') ||
                            (this.business_type !== 'restaurant' && p.category !== 'restaurant')
                        );
                        if (matchingPlan) {
                            this.selected_plan = String(matchingPlan.id);
                        }
                    }

                    this.step = 2;
                    window.scrollTo({top: 40, behavior: 'smooth'});
                } else {
                    alert('Please complete all required business profile fields marked with *.');
                    const firstInvalid = fields.find(id => {
                        const el = document.getElementById(id);
                        return !el || !el.value.trim();
                    });
                    if (firstInvalid) document.getElementById(firstInvalid).focus();
                }
            },

            validateStep2() {
                let isValid = true;
                const fields = ['name', 'admin_phone', 'email', 'password', 'password_confirmation'];

                fields.forEach(id => {
                    const el = document.getElementById(id);
                    if (!el || !el.value.trim()) {
                        isValid = false;
                        if (el) {
                            el.style.borderColor = '#EF4444';
                            el.style.backgroundColor = '#FEF2F2';
                        }
                    } else {
                        if (el) {
                            el.style.borderColor = '#CBD5E1';
                            el.style.backgroundColor = '#FFFFFF';
                        }
                    }
                });

                const p1 = document.getElementById('password');
                const p2 = document.getElementById('password_confirmation');
                if (p1 && p2 && p1.value && p2.value && p1.value !== p2.value) {
                    alert('Password and Confirm Password do not match.');
                    p1.style.borderColor = '#EF4444';
                    p2.style.borderColor = '#EF4444';
                    isValid = false;
                    p2.focus();
                    return;
                }

                if (isValid) {
                    const form = document.getElementById('registrationForm');
                    const formData = new FormData(form);
                    formData.append('step', 2);
                    
                    fetch('{{ route("register.validate") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(async res => {
                        if (!res.ok) {
                            if (res.status === 422) {
                                const data = await res.json();
                                const errors = data.errors || {};
                                const firstError = Object.values(errors)[0]?.[0] || 'Validation error.';
                                alert(firstError);
                            } else {
                                alert('Error validating form. Please try again.');
                            }
                            throw new Error('Validation failed');
                        }
                        return res.json();
                    })
                    .then(() => {
                        this.owner_name = document.getElementById('name').value;
                        this.owner_email = document.getElementById('email').value;
                        this.owner_phone = document.getElementById('admin_phone').value;
                        this.step = 3;
                        window.scrollTo({top: 40, behavior: 'smooth'});
                    })
                    .catch(err => {
                        console.error('Validation error:', err);
                    });
                } else {
                    alert('Please fill out all required owner credentials.');
                    const firstInvalid = fields.find(id => {
                        const el = document.getElementById(id);
                        return !el || !el.value.trim();
                    });
                    if (firstInvalid) document.getElementById(firstInvalid).focus();
                }
            },

            handleRegistrationCheckout() {
                if (!this.selected_plan) {
                    alert('Please select a plan or trial to proceed.');
                    return;
                }

                const form = document.getElementById('registrationForm');

                // If customer selected Free Trial, submit directly
                if (this.selected_plan === 'trial') {
                    this.is_processing = true;
                    this.processing_msg = 'Launching your trial...';
                    form.submit();
                    return;
                }

                // If paid plan selected, call Razorpay order endpoint
                this.is_processing = true;
                this.processing_msg = 'Connecting to Payment Gateway...';
                
                const formData = new FormData(form);
                formData.append('plan_id', this.selected_plan);
                formData.append('billing_cycle', this.billing_cycle);
                this.selected_addons.forEach(id => formData.append('addon_ids[]', id));

                fetch('{{ route("register.order") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        this.is_processing = false;
                        if (res.status === 422) {
                            // Validation error
                            const errors = data.errors || {};
                            const firstError = Object.values(errors)[0]?.[0] || 'Please check your inputs and try again.';
                            alert('Validation Error: ' + firstError);
                        } else {
                            alert(data.message || 'Unable to start payment session. Please try again.');
                        }
                        throw new Error('Validation failed');
                    }
                    return data;
                })
                .then(data => {
                    if (!data.success) {
                        this.is_processing = false;
                        alert(data.message || 'Unable to start payment session. Please try again.');
                        return;
                    }

                    if (data.is_free) {
                        this.processing_msg = 'Activating free plan...';
                        form.submit();
                        return;
                    }

                    this.processing_msg = 'Waiting for payment completion...';

                    const options = {
                        "key": data.key,
                        "amount": data.amount,
                        "currency": data.currency || "INR",
                        "name": data.business_name || "{{ config('app.name') }}",
                        "description": "Subscription for " + data.plan_name,
                        "order_id": data.order_id,
                        "prefill": {
                            "name": data.owner_name,
                            "email": data.owner_email,
                            "contact": data.owner_phone
                        },
                        "theme": { "color": "#0B132B" },
                        "handler": (response) => {
                            this.processing_msg = 'Verifying payment and creating account...';
                            document.getElementById('reg_razorpay_order_id').value = response.razorpay_order_id;
                            document.getElementById('reg_razorpay_payment_id').value = response.razorpay_payment_id;
                            document.getElementById('reg_razorpay_signature').value = response.razorpay_signature;
                            form.submit();
                        },
                        "modal": {
                            "ondismiss": () => {
                                this.is_processing = false;
                                this.processing_msg = '';
                            }
                        }
                    };
                    const rzp = new Razorpay(options);
                    rzp.on('payment.failed', function (response){
                        alert('Payment Failed: ' + response.error.description);
                    });
                    rzp.open();
                })
                .catch(err => {
                    this.is_processing = false;
                    console.error('Checkout error:', err);
                });
            }
        }
    }
</script>
</x-guest-layout>

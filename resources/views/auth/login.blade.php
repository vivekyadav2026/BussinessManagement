<x-guest-layout :fullCard="true">
    <style>
        /* Scoped styles for Vyapaargo 2-Column Split Login Card */
        .auth-split-wrapper {
            width: 100%;
            margin: 0 auto;
        }
        .auth-split-card {
            background: #FFFFFF;
            border-radius: 24px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 20px 45px -12px rgba(11, 19, 43, 0.12), 0 2px 10px rgba(11, 19, 43, 0.04);
            display: grid;
            grid-template-columns: 1fr;
            overflow: hidden;
            min-height: 580px;
            max-width: 1040px;
            margin: 10px auto;
        }
        @media (min-width: 1024px) {
            .auth-split-card {
                grid-template-columns: 44% 56%;
            }
        }

        /* Left Showcase Pane */
        .auth-showcase {
            background: linear-gradient(150deg, #0B132B 0%, #111C38 45%, #17233F 100%);
            color: #FFFFFF;
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .auth-showcase::before {
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
        .auth-showcase::after {
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
        .auth-badge {
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
            margin-bottom: 22px;
        }
        .auth-badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #D99A2B;
            box-shadow: 0 0 8px #D99A2B;
        }
        .auth-headline {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 28px;
            line-height: 1.25;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }
        @media (min-width: 640px) {
            .auth-headline { font-size: 32px; }
        }
        .auth-subhead {
            font-size: 14px;
            line-height: 1.6;
            color: #94A3B8;
            margin-bottom: 28px;
        }
        .auth-feature-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 28px;
        }
        @media (max-width: 1023px) {
            .auth-feature-list {
                display: none; /* Keep mobile top banner compact */
            }
            .auth-subhead {
                margin-bottom: 12px;
            }
            .auth-showcase {
                padding: 28px 24px;
            }
        }
        .auth-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .auth-feature-icon {
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
        .auth-feature-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #FFFFFF;
        }
        .auth-feature-desc {
            font-size: 12px;
            color: #94A3B8;
            margin-top: 2px;
            line-height: 1.45;
        }
        .auth-trust-box {
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 2;
        }
        .auth-trust-rating {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .auth-stars {
            color: #F59E0B;
            font-size: 14px;
            letter-spacing: 2px;
        }
        .auth-chips {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 11px;
            color: #94A3B8;
        }

        /* Right Form Pane */
        .auth-form-side {
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #FFFFFF;
        }
        @media (min-width: 640px) {
            .auth-form-side {
                padding: 44px 44px;
            }
        }
        @media (max-width: 640px) {
            .auth-form-side {
                padding: 28px 20px;
            }
        }
        .auth-top-bar {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding-bottom: 20px;
            margin-bottom: 24px;
            border-bottom: 1px solid #F1F5F9;
        }
        @media (min-width: 640px) {
            .auth-top-bar {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .auth-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #0F172A;
        }
        .auth-top-link {
            font-size: 13px;
            color: #64748B;
        }
        .auth-top-link a {
            color: var(--gold-deep);
            font-weight: 700;
            text-decoration: none;
        }
        .auth-top-link a:hover {
            text-decoration: underline;
        }
        .auth-field-group {
            margin-bottom: 18px;
        }
        .auth-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #334155;
            margin-bottom: 6px;
        }
        .auth-input-box {
            position: relative;
            display: flex;
            align-items: center;
        }
        .auth-input-icon {
            position: absolute;
            left: 14px;
            display: flex;
            align-items: center;
            pointer-events: none;
            color: #94A3B8;
        }
        .auth-input {
            width: 100%;
            height: 44px;
            padding: 8px 14px;
            border-radius: 10px;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
            color: #0F172A;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .auth-input.with-icon {
            padding-left: 40px;
        }
        .auth-input:focus {
            border-color: #D99A2B;
            box-shadow: 0 0 0 3px rgba(217, 154, 43, 0.18);
            outline: none;
        }
        .auth-input::placeholder {
            color: #94A3B8;
        }
        .auth-btn-primary {
            width: 100%;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #FFFFFF;
            background: #0B132B;
            border: 1px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(11, 19, 43, 0.15);
            transition: all 0.15s ease;
            margin-top: 6px;
        }
        .auth-btn-primary:hover {
            background: var(--gold-deep);
            color: #0B132B;
            box-shadow: 0 6px 16px rgba(184, 127, 27, 0.25);
        }
        .auth-alert-error {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #991B1B;
        }
        .auth-alert-success {
            background: #ECFDF5;
            border: 1px solid #6EE7B7;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #065F46;
        }
        .auth-bottom-help {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #F1F5F9;
            text-align: center;
            font-size: 12.5px;
            color: #64748B;
        }
    </style>

    <div class="auth-split-wrapper">
        <div class="auth-split-card">
            <!-- Left Branding & Trust Showcase -->
            <div class="auth-showcase">
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
                    <div class="auth-badge">
                        <span class="auth-badge-dot"></span>
                        Secure Merchant &amp; Staff Portal
                    </div>

                    <!-- Headline -->
                    <h2 class="auth-headline">
                        Welcome back to <span style="color: var(--gold);">Vyapaargo</span>.
                    </h2>
                    <p class="auth-subhead">
                        Log in to manage your billing, live inventory, restaurant kitchen orders, and staff attendance.
                    </p>

                    <!-- Feature List -->
                    <div class="auth-feature-list">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <div class="auth-feature-title">Fast Counter &amp; POS Billing</div>
                                <div class="auth-feature-desc">Barcode scanning, GST invoices, and instant WhatsApp receipts.</div>
                            </div>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <div class="auth-feature-title">Real-Time Business Analytics</div>
                                <div class="auth-feature-desc">Live sales tracking, low stock alerts, and customer dues ledger.</div>
                            </div>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <div class="auth-feature-title">Kitchen KDS &amp; Table Orders</div>
                                <div class="auth-feature-desc">Real-time KOT printing, digital QR menus &amp; waiter apps.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trust Footer -->
                <div class="auth-trust-box">
                    <div class="auth-chips">
                        <span style="display: inline-flex; align-items: center; gap: 5px;">
                            <svg width="13" height="13" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            256-Bit SSL
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 5px;">
                            <svg width="13" height="13" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            99.9% Uptime
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 5px;">
                            <svg width="13" height="13" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Role-Based Security
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Interactive Form Side -->
            <div class="auth-form-side">
                <div>
                    <!-- Top Title Bar -->
                    <div class="auth-top-bar">
                        <div>
                            <h3 class="auth-title">Sign In</h3>
                            <p style="font-size: 13px; color: #64748B; margin-top: 3px;">Enter your credentials to access your account.</p>
                        </div>
                        <div class="auth-top-link">
                            New here?
                            <a href="{{ route('register') }}">Create Account &rarr;</a>
                        </div>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="auth-alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Global Validation Errors -->
                    @if ($errors->any())
                        <div class="auth-alert-error">
                            <div style="font-weight: 700; margin-bottom: 4px; display:flex; align-items:center; gap:6px;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Please resolve the following errors:
                            </div>
                            <ul style="padding-left: 20px; margin: 0; line-height: 1.5;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="auth-field-group">
                            <label for="email" class="auth-label">Email Address *</label>
                            <div class="auth-input-box">
                                <div class="auth-input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required autofocus
                                       class="auth-input with-icon"
                                       value="{{ old('email') }}" placeholder="owner@business.com">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="auth-field-group" x-data="{ show: false }">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                <label for="password" class="auth-label" style="margin-bottom: 0;">Password *</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" style="font-size: 12px; font-weight: 600; color: var(--gold-deep); text-decoration: none;">Forgot password?</a>
                                @endif
                            </div>
                            <div class="auth-input-box">
                                <div class="auth-input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input id="password" name="password" :type="show ? 'text' : 'password'" autocomplete="current-password" required
                                       class="auth-input with-icon" style="padding-right: 38px;"
                                       placeholder="••••••••">
                                <button type="button" @click="show = !show" style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #94A3B8;">
                                    <svg x-show="!show" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" style="display:none;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                            <input id="remember_me" name="remember" type="checkbox" style="width: 16px; height: 16px; border-radius: 4px; border: 1px solid #CBD5E1; accent-color: var(--gold-deep); cursor: pointer;">
                            <label for="remember_me" style="font-size: 12.5px; font-weight: 500; color: #475569; cursor: pointer;">Remember me on this device</label>
                        </div>

                        <!-- Action Submit Button -->
                        <button type="submit" class="auth-btn-primary">
                            <span>Sign in to Dashboard</span>
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>

                <!-- Footer Help Links -->
                <div class="auth-bottom-help">
                    Need technical assistance?
                    <a href="tel:{{ $siteSettings['support_phone'] ?? '+919876543210' }}" style="color: #0F172A; font-weight: 700; text-decoration: none;">Contact Support</a>
                    &middot;
                    <a href="{{ route('public.terms') }}" target="_blank" style="color: #64748B; text-decoration: none;">Terms</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

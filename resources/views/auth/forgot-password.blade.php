<x-guest-layout :fullCard="true">
    <style>
        /* Scoped styles for Vyapaargo 2-Column Split Forgot Password Card */
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
            min-height: 540px;
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
                display: none;
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
        .auth-chips {
            display: flex;
            align-items: center;
            gap: 14px;
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
            margin-bottom: 20px;
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
            <!-- Left Showcase -->
            <div class="auth-showcase">
                <div>
                    <!-- Badge -->
                    <div class="auth-badge">
                        <span class="auth-badge-dot"></span>
                        Account Security &amp; Recovery
                    </div>

                    <!-- Headline -->
                    <h2 class="auth-headline">
                        Recover your <span style="color: var(--gold);">Vyapaargo</span> access.
                    </h2>
                    <p class="auth-subhead">
                        Forgot your password? No problem. We'll send you an encrypted one-time reset link to your registered email.
                    </p>

                    <!-- Feature List -->
                    <div class="auth-feature-list">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <div class="auth-feature-title">Secure One-Time Token</div>
                                <div class="auth-feature-desc">Reset links expire automatically to keep your store billing and staff data safe.</div>
                            </div>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <div class="auth-feature-title">Verified Identity Protection</div>
                                <div class="auth-feature-desc">Only verified owner/admin email addresses can authorize password resets.</div>
                            </div>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div>
                                <div class="auth-feature-title">Immediate Helpdesk Access</div>
                                <div class="auth-feature-desc">Need urgent assistance? Our support line is active Mon-Sat: 9am - 7pm IST.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trust Chips -->
                <div class="auth-trust-box">
                    <div class="auth-chips">
                        <span style="display: inline-flex; align-items: center; gap: 5px;">
                            <svg width="13" height="13" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            256-Bit SSL Encrypted
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 5px;">
                            <svg width="13" height="13" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Zero Data Leakage
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Form Side -->
            <div class="auth-form-side">
                <div>
                    <!-- Top Title Bar -->
                    <div class="auth-top-bar">
                        <div>
                            <h3 class="auth-title">Forgot Password</h3>
                            <p style="font-size: 13px; color: #64748B; margin-top: 3px;">Enter your email to receive recovery instructions.</p>
                        </div>
                        <div class="auth-top-link">
                            <a href="{{ route('login') }}">&larr; Back to Sign In</a>
                        </div>
                    </div>

                    <!-- Session Status Message -->
                    @if (session('status'))
                        <div class="auth-alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Error Alert -->
                    @if ($errors->any())
                        <div class="auth-alert-error">
                            <div style="font-weight: 700; margin-bottom: 4px; display:flex; align-items:center; gap:6px;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Password reset failed:
                            </div>
                            <ul style="padding-left: 20px; margin: 0; line-height: 1.5;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="auth-field-group">
                            <label for="email" class="auth-label">Registered Admin Email *</label>
                            <div class="auth-input-box">
                                <div class="auth-input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required autofocus
                                       class="auth-input with-icon"
                                       value="{{ old('email') }}" placeholder="owner@business.com">
                            </div>
                            <div style="font-size: 11.5px; color: #94A3B8; margin-top: 4px;">
                                Make sure to enter the exact email used during business registration.
                            </div>
                        </div>

                        <!-- Action Submit Button -->
                        <button type="submit" class="auth-btn-primary">
                            <span>Email Password Reset Link</span>
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>

                <!-- Footer Help Links -->
                <div class="auth-bottom-help">
                    Remembered your password?
                    <a href="{{ route('login') }}" style="color: var(--gold-deep); font-weight: 700; text-decoration: none;">Sign In Now</a>
                    &middot;
                    <a href="{{ route('register') }}" style="color: #64748B; text-decoration: none;">New Account</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

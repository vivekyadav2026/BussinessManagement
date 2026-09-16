<x-guest-layout :fullCard="true">
    <style>
        /* Scoped styles for Vyapaargo 2-Column Split Reset Password Card */
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
                        Security &amp; Credentials
                    </div>

                    <!-- Headline -->
                    <h2 class="auth-headline">
                        Set a strong new password.
                    </h2>
                    <p class="auth-subhead">
                        Keep your store billing, customer credit ledgers, and staff data safely protected.
                    </p>

                    <!-- Feature List -->
                    <div class="auth-feature-list">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <div class="auth-feature-title">Minimum 8 Characters</div>
                                <div class="auth-feature-desc">Use a combination of upper/lowercase letters, numbers &amp; special characters.</div>
                            </div>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <div class="auth-feature-title">Instant Session Sync</div>
                                <div class="auth-feature-desc">Once updated, you can immediately log into your web and POS counters.</div>
                            </div>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div>
                                <div class="auth-feature-title">Role Segregation</div>
                                <div class="auth-feature-desc">Cashier and Waiter roles have separate pins so admin passwords remain private.</div>
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
                            Encrypted Storage
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
                            <h3 class="auth-title">Set New Password</h3>
                            <p style="font-size: 13px; color: #64748B; margin-top: 3px;">Choose a new password for your admin account.</p>
                        </div>
                        <div class="auth-top-link">
                            <a href="{{ route('login') }}">&larr; Back to Sign In</a>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    @if ($errors->any())
                        <div class="auth-alert-error">
                            <div style="font-weight: 700; margin-bottom: 4px; display:flex; align-items:center; gap:6px;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Password update failed:
                            </div>
                            <ul style="padding-left: 20px; margin: 0; line-height: 1.5;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div class="auth-field-group">
                            <label for="email" class="auth-label">Email Address *</label>
                            <div class="auth-input-box">
                                <div class="auth-input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="username" required autofocus
                                       class="auth-input with-icon"
                                       value="{{ old('email', $request->email) }}" placeholder="owner@business.com">
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="auth-field-group" x-data="{ show: false }">
                            <label for="password" class="auth-label">New Password *</label>
                            <div class="auth-input-box">
                                <div class="auth-input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password"
                                       class="auth-input with-icon" style="padding-right: 38px;"
                                       placeholder="••••••••">
                                <button type="button" @click="show = !show" style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #94A3B8;">
                                    <svg x-show="!show" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" style="display:none;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="auth-field-group" x-data="{ show: false }">
                            <label for="password_confirmation" class="auth-label">Confirm New Password *</label>
                            <div class="auth-input-box">
                                <div class="auth-input-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required autocomplete="new-password"
                                       class="auth-input with-icon" style="padding-right: 38px;"
                                       placeholder="••••••••">
                                <button type="button" @click="show = !show" style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #94A3B8;">
                                    <svg x-show="!show" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" style="display:none;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="auth-btn-primary">
                            <span>Update Password &amp; Sign In</span>
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>

                <!-- Footer Help Links -->
                <div class="auth-bottom-help">
                    <a href="{{ route('login') }}" style="color: var(--gold-deep); font-weight: 700; text-decoration: none;">&larr; Back to Sign In</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

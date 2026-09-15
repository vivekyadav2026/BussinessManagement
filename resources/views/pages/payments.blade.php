@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<section class="hero" style="padding-top: 50px; padding-bottom: 50px;">
    <div class="wrap hero-grid">
        <div>
            <div class="eyebrow" style="background: #E6F4F1; color: var(--teal); border: 1px solid #BEE3D8; font-weight: 600;">
                <span>●</span> RAZORPAY &amp; UPI AUTO-SETTLEMENT ENGINE
            </div>
            <h1 style="font-size: 50px; line-height: 1.1; margin-bottom: 20px; font-weight: 700; color: var(--ink);">
                Collect payments faster,<br><em style="color: var(--teal); font-style: normal;">reconcile automatically.</em>
            </h1>
            <p class="lead" style="font-size: 16.5px; line-height: 1.6; color: var(--ink-soft); max-width: 520px; margin-bottom: 28px;">
                Send 1-click WhatsApp payment links, print dynamic UPI QR codes on GST invoices and restaurant receipts, and let verified webhooks update your customer ledgers instantly.
            </p>
                <div style="margin-top: 36px; display: flex; gap: 14px; flex-wrap: wrap;">
                    @php 
                      $trialDays = (int)\App\Models\SystemSetting::get('trial_days', 14); 
                      $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1') === '1';
                    @endphp
                    @if($enableTrial && $trialDays > 0)
                    <a href="{{ route('public.pricing') }}" class="btn btn-gold" style="padding: 14px 28px; font-size: 15.5px; font-weight: 700;">
                        Start {{ $trialDays }}-Day Free Trial &rarr;
                    </a>
                    @else
                    <a href="{{ route('public.pricing') }}" class="btn btn-gold" style="padding: 14px 28px; font-size: 15.5px; font-weight: 700;">
                        Get Started &rarr;
                    </a>
                    @endif
                <a class="btn btn-ghost" href="#payment-channels" style="padding: 13px 22px; font-size: 15px;">
                    View Payment Channels
                </a>
            </div>
            <div class="hero-note" style="font-size: 13px; color: var(--ink-soft); font-weight: 500; margin-top: 24px;">
                <span>Instant UPI &amp; Cards</span>
                <span>Zero Manual Entry</span>
                <span>Live Webhook Verification</span>
            </div>
        </div>

        <!-- Right Side Mockup Showcase -->
        <div style="padding: 10px 0;">
            <div style="background: #ffffff; border: 1px solid var(--border-soft); border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px -15px rgba(23,35,63,0.18);">
                <div style="background: #FAFBF9; border-bottom: 1px solid var(--border-soft); padding: 12px 18px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; gap: 7px;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #EF4444;"></span>
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #F59E0B;"></span>
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #10B981;"></span>
                    </div>
                    <span style="font-family: 'IBM Plex Mono'; font-size: 11px; color: var(--teal); font-weight: 700; background: var(--teal-soft); padding: 3px 14px; border-radius: 100px;">
                        AUTO-RECONCILED VIA UPI
                    </span>
                </div>
                <img src="{{ asset('images/payments_preview.jpg') }}" alt="UPI Payment Instant Settlement" style="width: 100%; height: auto; display: block;">
            </div>
        </div>
    </div>
</section>

<!-- Fintech Stats Strip -->
<div class="stats-strip" style="background: var(--teal-soft); border-color: #BEE3D8;">
    <div class="wrap stats-grid">
        <div class="stat-item">
            <h4 style="color: var(--teal);">3x</h4>
            <p style="color: var(--ink);">Faster Receivables Collection</p>
        </div>
        <div class="stat-item">
            <h4 style="color: var(--teal);">100%</h4>
            <p style="color: var(--ink);">Automated Ledger Reconciliation</p>
        </div>
        <div class="stat-item">
            <h4 style="color: var(--teal);">&lt; 2 Sec</h4>
            <p style="color: var(--ink);">Webhook Processing Speed</p>
        </div>
        <div class="stat-item">
            <h4 style="color: var(--teal);">0</h4>
            <p style="color: var(--ink);">Manual Data Entry Errors</p>
        </div>
    </div>
</div>

<!-- Section 1: 6 Payment Features Grid -->
<section id="payment-channels" style="padding: 70px 0; background: var(--bg);">
    <div class="wrap">
        <div class="sec-head" style="margin: 0 auto 48px; text-align: center; max-width: 680px;">
            <div class="eyebrow" style="background: #FDF4E5; color: var(--gold-deep); border-color: #F5DEB3;">Universal Digital Collections</div>
            <h2 style="font-size: 34px; font-weight: 700; color: var(--ink); margin-bottom: 12px;">Multiple ways for customers to pay</h2>
            <p style="color: var(--ink-soft); font-size: 15.5px; line-height: 1.6;">From retail shop counters to wholesale credit invoices and restaurant tables, accept digital payments anywhere.</p>
        </div>

        <div class="feat-grid" style="gap: 24px;">
            <!-- Feat 1 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Dynamic UPI QR Codes</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Generate invoices and thermal receipts with exact-amount UPI QR codes. Diners &amp; retail shoppers scan with PhonePe, GPay, Paytm, or BHIM.</p>
            </div>

            <!-- Feat 2 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Automated WhatsApp Pay Links</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Send invoice PDFs along with direct payment links over WhatsApp with 1 tap. Customers click and pay without needing any account registration.</p>
            </div>

            <!-- Feat 3 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.953 11.953 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Instant Webhook Reconciliation</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">When a client completes an online payment, cryptographic Razorpay webhooks mark the invoice 'Paid' and update your ledger in less than 2 seconds.</p>
            </div>

            <!-- Feat 4 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Receivables Aging &amp; Dues Ledger</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Track outstanding dues into 0-30, 30-60, and 60+ days aging buckets. Send polite automated reminder notices before invoices go overdue.</p>
            </div>

            <!-- Feat 5 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Credit Cards, Debit &amp; NetBanking</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Allow B2B corporate buyers to pay using credit cards, corporate debit cards, or net banking across 50+ major Indian commercial banks.</p>
            </div>

            <!-- Feat 6 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Bank Account Settlement Logs</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Full visibility over settlement batches, transaction fees, and bank account transfer timelines directly from your organization dashboard.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: 4-Step Settlement Process Workflow -->
<section style="background: #ffffff; border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); padding: 70px 0;">
    <div class="wrap">
        <div class="sec-head" style="margin: 0 auto 48px; text-align: center; max-width: 680px;">
            <div class="eyebrow" style="background: #E6F4F1; color: var(--teal); border-color: #BEE3D8;">Seamless Automation</div>
            <h2 style="font-size: 34px; font-weight: 700; color: var(--ink); margin-bottom: 12px;">The 4-Step Automated Flow</h2>
            <p style="color: var(--ink-soft); font-size: 15.5px; line-height: 1.6;">How Vyapaargo handles payment collection and reconciliation with zero manual effort.</p>
        </div>

        <div class="payment-grid" style="align-items: center; gap: 40px;">
            <div class="flow-card" style="padding: 32px; background: var(--bg); border-radius: 20px; border: 1px solid var(--border-soft);">
                <div class="flow-title" style="margin-bottom: 20px; font-weight: 700; color: var(--ink); font-size: 13px; letter-spacing: 0.04em;">
                    ⚡ END-TO-END SETTLEMENT TIMELINE
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: #ffffff; border: 1px solid var(--border-soft); border-radius: 12px;">
                        <span style="width: 30px; height: 30px; border-radius: 50%; background: var(--ink); color: var(--gold); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; font-family: 'IBM Plex Mono';">1</span>
                        <div>
                            <div style="font-weight: 700; font-size: 14px; color: var(--ink);">Generate GST Tax Invoice</div>
                            <div style="font-size: 12px; color: var(--ink-soft); margin-top: 2px;">Items, HSN codes, and CGST/SGST taxes calculated instantly.</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: #ffffff; border: 1px solid var(--border-soft); border-radius: 12px;">
                        <span style="width: 30px; height: 30px; border-radius: 50%; background: var(--ink); color: var(--gold); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; font-family: 'IBM Plex Mono';">2</span>
                        <div>
                            <div style="font-weight: 700; font-size: 14px; color: var(--ink);">Send 1-Click WhatsApp &amp; SMS Link</div>
                            <div style="font-size: 12px; color: var(--ink-soft); margin-top: 2px;">Client receives signed payment URL with downloadable PDF receipt.</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: #ffffff; border: 1px solid var(--border-soft); border-radius: 12px;">
                        <span style="width: 30px; height: 30px; border-radius: 50%; background: var(--ink); color: var(--gold); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; font-family: 'IBM Plex Mono';">3</span>
                        <div>
                            <div style="font-weight: 700; font-size: 14px; color: var(--ink);">Client Scans UPI / Pays Online</div>
                            <div style="font-size: 12px; color: var(--ink-soft); margin-top: 2px;">1-tap payment via PhonePe, Google Pay, Paytm, or Credit Cards.</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: var(--teal-soft); border: 1px solid #BEE3D8; border-radius: 12px; color: var(--teal);">
                        <span style="width: 30px; height: 30px; border-radius: 50%; background: var(--teal); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; font-family: 'IBM Plex Mono';">4</span>
                        <div>
                            <div style="font-weight: 700; font-size: 14px; color: var(--teal);">Auto-Reconciled &amp; Paid in Ledger</div>
                            <div style="font-size: 12px; color: var(--ink-soft); margin-top: 2px;">Real-time webhook marks invoice PAID &amp; clears customer dues.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <img src="{{ asset('images/dashboard_preview.jpg') }}" alt="Reconciliation Ledger Reports" class="illust-img" style="border-radius: 20px; box-shadow: 0 15px 40px -10px rgba(23,35,63,0.15);">
            </div>
        </div>
    </div>
</section>

<!-- Trust & Security Strip -->
<section style="background: var(--bg); padding: 50px 0; border-bottom: 1px solid var(--border-soft);">
    <div class="wrap" style="text-align: center;">
        <div class="eyebrow" style="background: #ffffff; color: var(--ink); border-color: var(--border-soft);">Bank-Grade Security</div>
        <h3 style="font-size: 24px; font-weight: 700; color: var(--ink); margin-bottom: 24px;">Built with highest security standards</h3>
        <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 20px;">
            <div style="background: #ffffff; border: 1px solid var(--border-soft); padding: 16px 24px; border-radius: 14px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px;">🔒</span>
                <span style="font-weight: 700; font-size: 13.5px; color: var(--ink);">256-Bit SSL Encrypted</span>
            </div>
            <div style="background: #ffffff; border: 1px solid var(--border-soft); padding: 16px 24px; border-radius: 14px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px;">🛡️</span>
                <span style="font-weight: 700; font-size: 13.5px; color: var(--ink);">PCI-DSS Compliant Gateway</span>
            </div>
            <div style="background: #ffffff; border: 1px solid var(--border-soft); padding: 16px 24px; border-radius: 14px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px;">⚡</span>
                <span style="font-weight: 700; font-size: 13.5px; color: var(--ink);">RBI Compliant Webhooks</span>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section style="padding: 60px 0 80px; background: var(--paper);">
    <div class="wrap">
        <div class="cta-banner" style="padding: 40px 48px; border-radius: 24px;">
            <div>
                <div class="eyebrow" style="background: rgba(217,154,43,0.2); color: var(--gold); border-color: rgba(217,154,43,0.3); margin-bottom: 12px;">
                    FAST DIGITAL PAYMENTS
                </div>
                <h2 style="font-size: 32px; font-weight: 700; color: #ffffff; line-height: 1.2; margin-bottom: 8px;">
                    Start collecting payments 3x faster today.
                </h2>
                <p style="color: #94A3B8; font-size: 15px; max-width: 540px;">
                    Setup takes less than 5 minutes. Connect your Razorpay or UPI details and begin auto-reconciling your business dues.
                </p>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;">
                <a class="btn btn-gold" style="font-size: 15.5px; padding: 14px 28px; font-weight: 700; border-radius: 10px;" href="{{ route('public.pricing') }}">
                    Start Free Trial &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

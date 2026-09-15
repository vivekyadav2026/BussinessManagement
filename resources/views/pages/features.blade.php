@php
  $trialDays = (int)\App\Models\SystemSetting::get('trial_days', 14);
  $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1') === '1';
@endphp
@extends('layouts.public')

@section('content')
<section class="hero" style="padding-top: 50px; padding-bottom: 50px;">
    <div class="wrap text-center" style="max-width: 800px; margin: 0 auto;">
        <div class="eyebrow" style="background: #FDF4E5; color: var(--gold-deep); border-color: #F5DEB3;">PLATFORM FEATURES</div>
        <h1 class="page-title" style="margin-bottom: 16px;">Everything you need to <br><em style="color: var(--teal); font-style: normal;">run your business</em></h1>
        <p class="page-lead" style="margin-bottom: 30px;">
            A unified suite integrating POS, Inventory, HRMS, and Payments in one single platform.
        </p>
        <div class="hero-actions" style="display: flex; gap: 14px; justify-content: center;">
            @if($enableTrial && $trialDays > 0)
            <a href="{{ route('public.pricing') }}?type=business" class="btn btn-gold">Start {{ $trialDays }}-Day Free Trial</a>
            @else
            <a href="{{ route('public.pricing') }}?type=business" class="btn btn-gold">Get Started</a>
            @endif
            <a href="{{ route('public.pricing') }}?type=restaurant" class="btn btn-ghost">Restaurant Suite</a>
        </div>
    </div>
</section>

<!-- Section 1: Barcode POS & Inventory -->
<section class="features" style="padding: 30px 0;">
    <div class="wrap">
        <div class="grid-2">
            <div class="side-cards-list">
                <div class="feat-card">
                    <div class="feat-icon"><svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                    <h3>Smart GST Invoicing</h3>
                    <p>Create professional, GST-compliant invoices in under 10 seconds. Auto-calculate CGST/SGST/IGST and share directly via WhatsApp &amp; PDF.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon"><svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
                    <h3>Barcode Inventory Control</h3>
                    <p>Track real-time stock across multiple warehouses or shops. Instant barcode scanning, variant management, and low-stock alerts.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon"><svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                    <h3>HR, Attendance &amp; Payroll</h3>
                    <p>Biometric and one-tap daily staff attendance. Automated salary calculations with advance deductions and instant payslip generator.</p>
                </div>
            </div>
            <div>
                <img src="{{ asset('images/features_preview.jpg') }}" alt="Vyapaargo Barcode Inventory and Billing" class="illust-img">
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Real-time Analytics Dashboard -->
<section style="background: var(--paper); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); padding: 50px 0;">
    <div class="wrap">
        <div class="grid-2">
            <div>
                <img src="{{ asset('images/dashboard_preview.jpg') }}" alt="Real-time Business Analytics Dashboard" class="illust-img">
            </div>
            <div>
                <div class="eyebrow">Real-Time Intelligence</div>
                <h2 class="section-title">360° Business Health Overview</h2>
                <p style="color:var(--ink-soft); font-size:15px; line-height:1.6; margin-bottom:24px;">Stop guessing where your profits go. Vyapaargo consolidates your sales numbers, margin health, overdue payments, and inventory status into one intuitive command center.</p>
                
                <div class="feat-card" style="margin-bottom: 16px;">
                    <h4 style="font-weight:600; font-family:'Space Grotesk';">Receivables Aging &amp; Cashflow Ledger</h4>
                    <p style="font-size:13.5px; color:var(--ink-soft); margin-top:6px;">Categorize dues into 0-30, 30-60, and 60+ days with automated WhatsApp payment reminder links.</p>
                </div>
                <div class="feat-card">
                    <h4 style="font-weight:600; font-family:'Space Grotesk';">Multi-Branch Scoping</h4>
                    <p style="font-size:13.5px; color:var(--ink-soft); margin-top:6px;">Manage multiple retail stores or branch locations under a single organization account without mixing stock.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section style="padding: 60px 0; background: var(--bg);">
    <div class="wrap">
        <div style="background: #0F172A; border-radius: 20px; padding: 40px; display: flex; align-items: center; justify-content: space-between; gap: 30px; flex-wrap: wrap;">
            <div>
                <h3 style="color: #fff; font-size: 26px; margin-bottom: 8px;">Ready to digitize your operations?</h3>
                @if($enableTrial && $trialDays > 0)
                <p style="color: #AAB3CB; margin-top: 6px; font-size: 14px;">Get started in 5 minutes with our risk-free {{ $trialDays }}-day trial. No credit card required.</p>
                @else
                <p style="color: #AAB3CB; margin-top: 6px; font-size: 14px;">Get started in 5 minutes. No credit card required.</p>
                @endif
            </div>
            <div>
                <a href="{{ route('register') }}" class="btn btn-gold" style="padding: 14px 28px; font-weight: 700; font-size: 15px; border-radius: 8px;">Create Free Account</a>
            </div>
        </div>
    </div>
</section>
@endsection

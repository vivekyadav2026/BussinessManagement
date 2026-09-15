@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<section class="hero" style="padding-top: 50px; padding-bottom: 50px;">
    <div class="wrap hero-grid">
        <div>
            <div class="eyebrow" style="background: var(--teal-soft); color: var(--teal); border: 1px solid #BEE3D8; font-weight: 600;">
                <span>●</span> ALL-IN-ONE RESTAURANT &amp; CAFE OPERATING SYSTEM
            </div>
            <h1 style="font-size: 50px; line-height: 1.1; margin-bottom: 20px; font-weight: 700; color: var(--ink);">
                Tez table turnover,<br><em style="color: var(--teal); font-style: normal;">zero order chaos.</em>
            </h1>
            <p class="lead" style="font-size: 16.5px; line-height: 1.6; color: var(--ink-soft); max-width: 520px; margin-bottom: 28px;">
                Empower your restaurant, cafe, bar, or cloud kitchen with dynamic Table QR digital menus, real-time Kitchen Display (KDS), waiter tablet POS, and instant UPI bill settlement.
            </p>
                <div style="margin-top: 36px; display: flex; gap: 14px; flex-wrap: wrap;">
                    @php 
                      $trialDays = (int)\App\Models\SystemSetting::get('trial_days', 14); 
                      $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1') === '1';
                    @endphp
                    @if($enableTrial && $trialDays > 0)
                    <a href="{{ route('public.pricing') }}?type=restaurant" class="btn" style="background: var(--teal); color: #fff; padding: 14px 28px; font-size: 15.5px; font-weight: 700; border-radius: 10px;">
                        Start {{ $trialDays }}-Day Free Trial &rarr;
                    </a>
                    @else
                    <a href="{{ route('public.pricing') }}?type=restaurant" class="btn" style="background: var(--teal); color: #fff; padding: 14px 28px; font-size: 15.5px; font-weight: 700; border-radius: 10px;">
                        Get Started &rarr;
                    </a>
                    @endif
                    <a href="#pos-features" class="btn btn-ghost" style="padding: 13px 22px; font-size: 15px; border-radius: 10px;">
                        Explore POS Modules
                    </a>
                </div>
            <div class="hero-note" style="font-size: 13px; color: var(--ink-soft); font-weight: 500;">
                <span>Table QR Menus</span>
                <span>Real-Time Kitchen KDS</span>
                <span>Thermal Bill &amp; UPI Print</span>
            </div>
        </div>

        <!-- Right Side Mockup Showcase -->
        <div style="padding: 10px 0;">
            <div style="background: #ffffff; border: 1px solid var(--border-soft); border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px -15px rgba(20,99,86,0.18);">
                <div style="background: #FAFBF9; border-bottom: 1px solid var(--border-soft); padding: 12px 18px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; gap: 7px;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #EF4444;"></span>
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #F59E0B;"></span>
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #10B981;"></span>
                    </div>
                    <span style="font-family: 'IBM Plex Mono'; font-size: 11px; color: var(--teal); font-weight: 700; background: var(--teal-soft); padding: 3px 14px; border-radius: 100px;">
                        LIVE KDS &amp; TABLE POS
                    </span>
                </div>
                <img src="{{ asset('images/restaurant_preview.jpg') }}" alt="Restaurant QR Menu and Kitchen KDS" style="width: 100%; height: auto; display: block;">
            </div>
        </div>
    </div>
</section>

<!-- Stats Strip -->
<div class="stats-strip" style="background: var(--teal-soft); border-color: #BEE3D8;">
    <div class="wrap stats-grid">
        <div class="stat-item">
            <h4 style="color: var(--teal);">2.5x</h4>
            <p style="color: var(--ink);">Faster Table Turns</p>
        </div>
        <div class="stat-item">
            <h4 style="color: var(--teal);">0%</h4>
            <p style="color: var(--ink);">Lost Paper Tickets</p>
        </div>
        <div class="stat-item">
            <h4 style="color: var(--teal);">&lt; 30 Sec</h4>
            <p style="color: var(--ink);">Average KOT Dispatch</p>
        </div>
        <div class="stat-item">
            <h4 style="color: var(--teal);">100%</h4>
            <p style="color: var(--ink);">Contactless UPI Pay</p>
        </div>
    </div>
</div>

<!-- Section 1: Core Restaurant Features -->
<section id="rest-features" style="padding: 70px 0; background: var(--bg);">
    <div class="wrap">
        <div class="sec-head" style="margin: 0 auto 48px; text-align: center; max-width: 680px;">
            <div class="eyebrow" style="background: var(--teal-soft); color: var(--teal); border-color: #BEE3D8;">Complete F&amp;B Operating System</div>
            <h2 style="font-size: 34px; font-weight: 700; color: var(--ink); margin-bottom: 12px;">Everything from Table QR to Kitchen Screen</h2>
            <p style="color: var(--ink-soft); font-size: 15.5px; line-height: 1.6;">Designed specifically for Indian cafes, fine dining, QSRs, bars, and food courts.</p>
        </div>

        <div class="feat-grid" style="gap: 24px;">
            <!-- Feat 1 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon" style="background: var(--teal-soft); color: var(--teal);"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Dynamic Table QR Menus</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Generate unique QR codes for each dining table. Guests scan with any phone camera to browse visual menus and place orders instantly.</p>
            </div>

            <!-- Feat 2 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon" style="background: var(--teal-soft); color: var(--teal);"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Real-Time Kitchen KDS</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Orders flow instantly to kitchen display screens with live color-coded preparation timers (Green/Amber/Red) and customizable sound alerts.</p>
            </div>

            <!-- Feat 3 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon" style="background: var(--teal-soft); color: var(--teal);"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Waiter Tablet &amp; Floor Map</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Captains take orders on phone or tablet with visual table vacancy tracking, live running totals, split bills, and instant KOT dispatch.</p>
            </div>

            <!-- Feat 4 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon" style="background: var(--teal-soft); color: var(--teal);"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Express Counter &amp; QSR Billing</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Superfast takeaway order entry with automatic sequential order tokens, thermal receipt printing, and change-return calculations.</p>
            </div>

            <!-- Feat 5 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon" style="background: var(--teal-soft); color: var(--teal);"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Dish &amp; Revenue Analytics</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Track your top-selling star items, slow-moving dishes, peak dining rush hours, and customer purchase history in real time.</p>
            </div>

            <!-- Feat 6 -->
            <div class="feat-card" style="padding: 28px; border-radius: 16px;">
                <div class="feat-icon" style="background: var(--teal-soft); color: var(--teal);"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">UPI QR Receipts &amp; GST Invoicing</h3>
                <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Print 58mm / 80mm thermal receipts with dynamic UPI payment QR codes. Diners scan and pay on spot with automatic reconciliation.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Restaurant Workflow Showcase -->
<section style="background: #ffffff; border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); padding: 70px 0;">
    <div class="wrap">
        <div class="rest-split">
            <div>
                <img src="{{ asset('images/card_restaurant_cafe.jpg') }}" alt="Restaurant Dining Operations" class="illust-img" style="border-radius: 20px;">
            </div>
            <div>
                <div class="eyebrow" style="background: var(--teal-soft); color: var(--teal); border-color: #BEE3D8;">How It Works</div>
                <h2 style="font-size: 32px; font-weight: 700; color: var(--ink); margin-bottom: 14px;">The 3-Step Restaurant Flow</h2>
                <p style="color: var(--ink-soft); font-size: 15px; line-height: 1.6; margin-bottom: 24px;">Eliminate communication bottlenecks between table, waiter, kitchen, and billing cashier.</p>
                
                <ul class="rest-list" style="margin-bottom: 28px;">
                    <li>
                        <div class="rest-num" style="background: var(--teal); color: #fff;">01</div>
                        <div>
                            <h4>Customer Scans QR or Waiter Takes Order</h4>
                            <p>Orders are placed instantly with item customizations, table number, and dietary preferences.</p>
                        </div>
                    </li>
                    <li>
                        <div class="rest-num" style="background: var(--teal); color: #fff;">02</div>
                        <div>
                            <h4>Kitchen Screen Rings &amp; Cooks</h4>
                            <p>Chefs hear the incoming order alert, track cooking timers, and mark tickets 'Ready' with 1 tap.</p>
                        </div>
                    </li>
                    <li>
                        <div class="rest-num" style="background: var(--teal); color: #fff;">03</div>
                        <div>
                            <h4>Instant Bill Print &amp; UPI Settlement</h4>
                            <p>Cashier generates thermal bill slip with dynamic UPI QR code. Ledger and inventory auto-update.</p>
                        </div>
                    </li>
                </ul>

                <a class="btn" style="background: var(--teal); color: #fff; padding: 13px 26px; font-size: 15px; font-weight: 700; border-radius: 10px;" href="{{ route('public.pricing') }}?type=restaurant">
                    Get Started with Restaurant Suite &rarr;
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section style="padding: 60px 0 80px; background: var(--bg);">
    <div class="wrap">
        <div class="cta-banner" style="background: var(--teal); padding: 40px 48px; border-radius: 20px;">
            <div>
                @php
                  $trialDays = (int)\App\Models\SystemSetting::get('trial_days', 14);
                  $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1') === '1';
                @endphp
                <div class="eyebrow" style="background: rgba(255,255,255,0.15); color: #fff; border:none; margin-bottom: 12px;">GO LIVE TODAY</div>
                <h2 style="font-size: 32px; font-weight: 700; color: #ffffff; line-height: 1.2; margin-bottom: 8px;">
                    Scale your F&amp;B brand with confidence.
                </h2>
                <p style="color: rgba(255,255,255,0.85); font-size: 15px; max-width: 520px;">
                    Join hundreds of high-growth restaurants and food chains across India.
                    @if($enableTrial && $trialDays > 0)
                    Start with {{ $trialDays }} days of full access.
                    @endif
                </p>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;">
                <a class="btn" style="background: var(--teal); color: #fff; font-size: 15.5px; padding: 14px 28px; font-weight: 700; border-radius: 10px;" href="{{ route('public.pricing') }}?type=restaurant">
                    View Restaurant Plans &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

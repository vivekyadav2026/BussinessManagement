@extends('layouts.public')

@section('content')

  <!-- Hero Banner Section -->
  <section class="hero" style="padding-top: 50px; padding-bottom: 50px;">
    <div class="wrap hero-grid">
      <div>
        <div class="eyebrow" style="background: #FDF4E5; color: var(--gold-deep); border: 1px solid #F5DEB3; font-weight: 600;">
          <span>●</span> ALL-IN-ONE BUSINESS &amp; RESTAURANT ERP
        </div>
        <h1 style="font-size: 52px; line-height: 1.1; margin-bottom: 20px; font-weight: 700; color: var(--ink);">
          Saara business,<br><em style="color: var(--teal); font-style: normal;">ek jagah.</em>
        </h1>
        <p class="lead" style="font-size: 16.5px; line-height: 1.6; color: var(--ink-soft); max-width: 520px; margin-bottom: 28px;">
          Manage GST billing, barcode inventory, staff attendance, customer dues, and restaurant QR kitchen orders in one unified platform built for Indian businesses.
        </p>
        <div class="hero-actions" style="margin-bottom: 24px;">
          <a class="btn btn-gold" href="{{ route('public.pricing') }}" style="padding: 13px 26px; font-size: 15px; font-weight: 700;">
            @if($enableTrial && $trialDays > 0) Start {{ $trialDays }}-Day Free Trial &rarr; @else Get Started &rarr; @endif
          </a>
          <a class="btn btn-ghost" href="#industry-solutions" style="padding: 13px 22px; font-size: 15px;">
            Choose Your Industry
          </a>
        </div>
        <div class="hero-note" style="font-size: 13px; color: var(--ink-soft); font-weight: 500;">
          <span>No credit card required</span>
          <span>GST-ready invoices</span>
          <span>Setup in 5 mins</span>
        </div>
      </div>

      <!-- Right Invoice Mockup -->
      <div style="padding: 10px 0;">
        <div class="invoice-mock">
          <div class="inv-top">
            <div style="display: flex; align-items: center; gap: 10px;">
              <div style="width: 38px; height: 38px; border-radius: 10px; background: var(--ink); color: var(--gold); display: flex; align-items: center; justify-content: center; font-weight: 700; font-family: 'Space Grotesk'; font-size: 16px;">
                ST
              </div>
              <div>
                <div class="who">Sharma Traders</div>
                <div class="num">INV-{{ date('Y') }}-0417 · {{ date('d M Y') }}</div>
              </div>
            </div>
            <div class="stamp">PAID</div>
          </div>

          <div class="inv-lines">
            <div class="inv-line"><span style="font-weight: 500;">Basmati Rice 25kg × 4</span><span>₹6,200.00</span></div>
            <div class="inv-line"><span style="font-weight: 500;">Sunflower Oil 15L × 2</span><span>₹3,450.00</span></div>
            <div class="inv-line" style="color: var(--ink-faint); margin-top: 6px;"><span>CGST (2.5%) + SGST (2.5%)</span><span>₹482.00</span></div>
          </div>

          <div class="inv-total">
            <span>Total Amount</span>
            <span>₹10,132.00</span>
          </div>

          <div class="inv-bottom">
            <span class="mono" style="font-size:11px; color:var(--ink-soft);">
              ⚡ Auto-Reconciled via UPI
            </span>
            <span class="badge badge-paid">SETTLED</span>
          </div>
          <div class="perf"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Dual Industry Solutions Section -->
  <section id="industry-solutions" style="padding: 40px 0 50px; background: var(--bg);">
    <div class="wrap">
      <div class="sec-head" style="text-align: center; margin: 0 auto 36px; max-width: 650px;">
        <div class="eyebrow" style="background: #FDF4E5; color: var(--gold-deep); border-color: #F5DEB3;">Tailored Business Workspaces</div>
        <h2 style="font-size: 32px; font-weight: 700; color: var(--ink); margin-bottom: 10px;">Select Your Industry Solution</h2>
        <p style="color: var(--ink-soft); font-size: 15px; line-height: 1.5;">Choose the pre-configured workflow that matches your business model.</p>
      </div>

      <div class="grid-2-cards" style="gap: 32px;">
        
        <!-- Card 1: Retail & General Business -->
        <div class="industry-card" style="padding: 28px;">
          <img src="{{ asset('images/card_business_retail.jpg') }}" alt="Retail & General Business POS" class="industry-card-img" style="height: 220px;">
          <div>
            <div class="industry-tag gold">● Retail, Wholesale &amp; General Stores</div>
            <h3 style="font-size: 22px; color: var(--ink); margin-bottom: 10px; font-family: 'Space Grotesk'; font-weight: 700;">
              Retail Shops, Supermarkets &amp; Traders
            </h3>
            <p style="color: var(--ink-soft); font-size: 14.5px; line-height: 1.6; margin-bottom: 20px;">
              Equipped with high-speed barcode scanning, itemized GST tax invoices, customer dues ledger, automated WhatsApp payment links, and employee payroll.
            </p>
            
            <div class="inner-feat-list" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 24px;">
              <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--ink); font-family: 'IBM Plex Mono';">
                <span style="color: var(--teal); font-weight: bold;">✓</span> Barcode Scanning
              </div>
              <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--ink); font-family: 'IBM Plex Mono';">
                <span style="color: var(--teal); font-weight: bold;">✓</span> GST Billing (PDF/WhatsApp)
              </div>
              <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--ink); font-family: 'IBM Plex Mono';">
                <span style="color: var(--teal); font-weight: bold;">✓</span> Receivables Aging Ledger
              </div>
              <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--ink); font-family: 'IBM Plex Mono';">
                <span style="color: var(--teal); font-weight: bold;">✓</span> Staff Attendance &amp; Pay
              </div>
            </div>
          </div>

          <div style="margin-top: auto; padding-top: 18px; border-top: 1px solid var(--border-soft);">
            <a href="{{ route('public.pricing') }}?type=business" class="btn btn-gold" style="width: 100%; justify-content: center; padding: 14px 20px; font-size: 15px; font-weight: 700; border-radius: 10px;">
              Get Started with Business &rarr;
            </a>
            <div style="text-align: center; margin-top: 8px; font-size: 11.5px; color: var(--ink-faint); font-family: 'IBM Plex Mono';">
              @if($enableTrial && $trialDays > 0) Includes {{ $trialDays }}-Day Full Access Free Trial @else Choose your plan to get started @endif
            </div>
          </div>
        </div>

        <!-- Card 2: Restaurant, Cafe & Bar -->
        <div class="industry-card restaurant-theme" style="padding: 28px;">
          <img src="{{ asset('images/card_restaurant_cafe.jpg') }}" alt="Restaurant & Cafe POS Suite" class="industry-card-img" style="height: 220px;">
          <div>
            <div class="industry-tag teal">● Restaurant, Cafe &amp; Bar POS</div>
            <h3 style="font-size: 22px; color: var(--ink); margin-bottom: 10px; font-family: 'Space Grotesk'; font-weight: 700;">
              Restaurants, Cafes &amp; Quick Service
            </h3>
            <p style="color: var(--ink-soft); font-size: 14.5px; line-height: 1.6; margin-bottom: 20px;">
              Complete F&amp;B suite featuring dynamic QR code table menus, real-time Kitchen Display System (KDS), waiter ordering mode, and fast counter takeaway billing.
            </p>
            
            <div class="inner-feat-list" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 24px;">
              <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--ink); font-family: 'IBM Plex Mono';">
                <span style="color: var(--teal); font-weight: bold;">✓</span> Table QR Digital Menu
              </div>
              <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--teal); font-family: 'IBM Plex Mono';">
                <span style="color: var(--teal); font-weight: bold;">✓</span> Real-Time Kitchen KDS
              </div>
              <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--ink); font-family: 'IBM Plex Mono';">
                <span style="color: var(--teal); font-weight: bold;">✓</span> Waiter Tablet Ordering
              </div>
              <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--ink); font-family: 'IBM Plex Mono';">
                <span style="color: var(--teal); font-weight: bold;">✓</span> Thermal KOT &amp; Bill Print
              </div>
            </div>
          </div>

          <div style="margin-top: auto; padding-top: 18px; border-top: 1px solid var(--border-soft);">
            <a href="{{ route('public.pricing') }}?type=restaurant" class="btn" style="width: 100%; justify-content: center; padding: 14px 20px; font-size: 15px; font-weight: 700; border-radius: 10px; background: var(--teal); color: #fff;">
              Get Started with Restaurant &rarr;
            </a>
            <div style="text-align: center; margin-top: 8px; font-size: 11.5px; color: var(--ink-faint); font-family: 'IBM Plex Mono';">
              @if($enableTrial && $trialDays > 0) Includes {{ $trialDays }}-Day Full Access Free Trial @else Choose your plan to get started @endif
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Live Stats Strip -->
  <div class="stats-strip">
    <div class="wrap stats-grid">
      <div class="stat-item">
        <h4>GST Ready</h4>
        <p>Fully Compliant Billing</p>
      </div>
      <div class="stat-item">
        <h4>100% Secure</h4>
        <p>Daily Cloud Backups</p>
      </div>
      <div class="stat-item">
        <h4>99.9%</h4>
        <p>Platform Uptime</p>
      </div>
      <div class="stat-item">
        <h4>&lt; 5 Mins</h4>
        <p>Setup &amp; Go-Live</p>
      </div>
    </div>
  </div>

  <!-- Dashboard Overview Section -->
  <section style="background: var(--paper); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); padding: 56px 0;">
    <div class="wrap" style="text-align: center;">
      <div class="sec-head" style="margin: 0 auto 32px; text-align: center; max-width: 680px;">
        <div class="eyebrow" style="background: #E6F4F1; color: var(--teal); border-color: #BEE3D8;">Command Center</div>
        <h2 style="font-size: 34px; font-weight: 700; color: var(--ink); margin-bottom: 12px;">One Unified Business Dashboard</h2>
        <p style="color: var(--ink-soft); font-size: 15.5px; line-height: 1.6;">Monitor sales trends, profit margins, active receivables, and inventory stock levels in real-time without juggling multiple apps.</p>
      </div>
      
      <!-- Modern Browser Mockup Wrapper -->
      <div style="max-width: 980px; margin: 0 auto; position: relative;">
        <div style="background: #ffffff; border: 1px solid var(--border-soft); border-radius: 16px; overflow: hidden; box-shadow: 0 20px 50px -15px rgba(23,35,63,0.15);">
          <!-- Mock Browser Header Bar -->
          <div style="background: #FAFBF9; border-bottom: 1px solid var(--border-soft); padding: 12px 18px; display: flex; align-items: center; gap: 8px;">
            <span style="width: 10px; height: 10px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
            <span style="width: 10px; height: 10px; border-radius: 50%; background: #F59E0B; display: inline-block;"></span>
            <span style="width: 10px; height: 10px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
            <span style="margin: 0 auto; font-family: 'IBM Plex Mono'; font-size: 11px; color: var(--ink-faint); background: #ffffff; border: 1px solid var(--border-soft); padding: 3px 20px; border-radius: 100px;">
              app.vyapaargo.com/organization/dashboard
            </span>
          </div>
          <img src="{{ asset('images/dashboard_preview.jpg') }}" alt="Vyapaargo Business Dashboard Overview" style="width: 100%; height: auto; display: block;">
        </div>

        <!-- Floating Feature Badges Below Screenshot -->
        <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 14px; margin-top: 24px;">
          <span style="font-size: 12.5px; font-family: 'IBM Plex Mono'; background: #ffffff; border: 1px solid var(--border-soft); padding: 8px 16px; border-radius: 100px; box-shadow: var(--shadow); color: var(--ink);">
            📈 Real-Time Profit &amp; Sales Analytics
          </span>
          <span style="font-size: 12.5px; font-family: 'IBM Plex Mono'; background: #ffffff; border: 1px solid var(--border-soft); padding: 8px 16px; border-radius: 100px; box-shadow: var(--shadow); color: var(--ink);">
            ⚠️ Automated Low-Stock Warnings
          </span>
          <span style="font-size: 12.5px; font-family: 'IBM Plex Mono'; background: #ffffff; border: 1px solid var(--border-soft); padding: 8px 16px; border-radius: 100px; box-shadow: var(--shadow); color: var(--ink);">
            ⚡ 1-Click WhatsApp Payment Dues
          </span>
        </div>
      </div>
    </div>
  </section>

  <!-- Core Modules Section -->
  <section id="features" style="padding: 70px 0; background: var(--bg);">
    <div class="wrap">
      <div class="sec-head" style="margin: 0 auto 48px; text-align: center; max-width: 680px;">
        <div class="eyebrow" style="background: #FDF4E5; color: var(--gold-deep); border-color: #F5DEB3;">Enterprise-Grade Capabilities</div>
        <h2 style="font-size: 34px; font-weight: 700; color: var(--ink); margin-bottom: 12px;">Everything your shop needs, zero clutter.</h2>
        <p style="color: var(--ink-soft); font-size: 15.5px; line-height: 1.6;">Sixteen purpose-built modules in a single login. Employees only see what their role allows — the owner sees everything.</p>
      </div>

      <div class="feat-grid" style="gap: 24px;">
        <div class="feat-card" style="padding: 28px; border-radius: 16px;">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4zM3 7v10l9 4 9-4V7"/></svg></div>
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Products &amp; Barcode Stock</h3>
          <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Instant barcode scanner integration, batch SKU generation, and automatic low-stock notifications.</p>
        </div>
        <div class="feat-card" style="padding: 28px; border-radius: 16px;">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/></svg></div>
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">GST Invoicing &amp; WhatsApp</h3>
          <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Auto CGST/SGST/IGST calculation, thermal receipt printing, and 1-tap WhatsApp PDF invoice sharing.</p>
        </div>
        <div class="feat-card" style="padding: 28px; border-radius: 16px;">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M2 12h20"/></svg></div>
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Receivables &amp; Ageing Ledger</h3>
          <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Track outstanding customer dues (0-30, 30-60, 60+ days) with automated WhatsApp pay link alerts.</p>
        </div>
        <div class="feat-card" style="padding: 28px; border-radius: 16px;">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M4 21V9M12 21V3M20 21v-7"/></svg></div>
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Attendance &amp; Payroll</h3>
          <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Daily employee check-ins feed directly into automated salary calculations with advance deductions.</p>
        </div>
        <div class="feat-card" style="padding: 28px; border-radius: 16px;">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></div>
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Real-Time Financial Health</h3>
          <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">Sales, profit margins, stock valuation, and receivables consolidated into a clean 5-second score.</p>
        </div>
        <div class="feat-card" style="padding: 28px; border-radius: 16px;">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M3 21l4-4 4 4M3 3l4 4 4-4M21 21l-4-4-4 4M21 3l-4 4-4-4"/></svg></div>
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Multi-Branch Enterprise</h3>
          <p style="color: var(--ink-soft); font-size: 14px; line-height: 1.6;">One owner login manages multiple retail stores and warehouses with automatic location scoping.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Restaurant POS Showcase Section -->
  <section id="restaurant" style="background: #ffffff; border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); padding: 70px 0;">
    <div class="wrap">
      <div class="rest-split">
        <div>
          <img src="{{ asset('images/restaurant_preview.jpg') }}" alt="Restaurant QR Menu Ordering and KDS Display" class="illust-img">
        </div>
        <div>
          <div class="eyebrow" style="background: var(--teal-soft); color: var(--teal); border-color: #BEE3D8;">Restaurant &amp; Cafe Suite</div>
          <h2 style="font-size: 32px; font-weight: 700; color: var(--ink); margin-bottom: 14px;">Smart QR Ordering, KDS &amp; Waiter POS</h2>
          <p style="color: var(--ink-soft); font-size: 15px; line-height: 1.6; margin-bottom: 24px;">Transform your cafe or restaurant floor. Faster table turns, zero lost tickets, and seamless contact-free ordering.</p>
          
          <ul class="rest-list" style="margin-bottom: 28px;">
            <li>
              <div class="rest-num">01</div>
              <div>
                <h4>Dynamic Table QR Menus</h4>
                <p>Print elegant QR codes for every table. Diners scan with phone camera to browse visual menus and place orders.</p>
              </div>
            </li>
            <li>
              <div class="rest-num">02</div>
              <div>
                <h4>Real-Time Kitchen Display (KDS)</h4>
                <p>Kitchen staff view active orders, prepare items with live timers, and mark tickets ready instantly.</p>
              </div>
            </li>
            <li>
              <div class="rest-num">03</div>
              <div>
                <h4>Waiter Tablet &amp; Counter Billing</h4>
                <p>Captains take orders on tablet, split bills, apply discounts, and generate thermal receipts with UPI QR codes.</p>
              </div>
            </li>
          </ul>

          <a class="btn" style="background: var(--teal); color: #fff; padding: 13px 26px; font-size: 15px; font-weight: 700; border-radius: 9px;" href="{{ route('public.pricing') }}?type=restaurant">
            Explore Restaurant Plans &rarr;
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Payments & Reconciliation Section -->
  <section id="payments" style="padding: 70px 0; background: var(--bg);">
    <div class="wrap">
      <div class="sec-head" style="margin: 0 auto 48px; text-align: center; max-width: 680px;">
        <div class="eyebrow" style="background: #E6F4F1; color: var(--teal); border-color: #BEE3D8;">Razorpay &amp; UPI Integration</div>
        <h2 style="font-size: 34px; font-weight: 700; color: var(--ink); margin-bottom: 12px;">Instant Digital Collections &amp; Auto-Reconcile</h2>
        <p style="color: var(--ink-soft); font-size: 15.5px; line-height: 1.6;">Send payment links with invoices and let clients pay via UPI, QR, cards, or net banking. Invoices automatically update to 'Paid' upon settlement.</p>
      </div>
      
      <div class="payment-grid" style="align-items: center; gap: 40px;">
        <div class="flow-card" style="padding: 32px; background: #ffffff; border-radius: 20px;">
          <div class="flow-title" style="margin-bottom: 20px; font-weight: 700; color: var(--ink); font-size: 13px; letter-spacing: 0.04em;">
            ⚡ 4-STEP AUTOMATED SETTLEMENT
          </div>
          <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: var(--bg); border: 1px solid var(--border-soft); border-radius: 10px;">
              <span style="width: 28px; height: 28px; border-radius: 50%; background: var(--ink); color: var(--gold); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; font-family: 'IBM Plex Mono';">1</span>
              <span style="font-weight: 600; font-size: 14px; color: var(--ink);">Generate GST Invoice</span>
            </div>
            <div style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: var(--bg); border: 1px solid var(--border-soft); border-radius: 10px;">
              <span style="width: 28px; height: 28px; border-radius: 50%; background: var(--ink); color: var(--gold); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; font-family: 'IBM Plex Mono';">2</span>
              <span style="font-weight: 600; font-size: 14px; color: var(--ink);">Send Instant WhatsApp &amp; SMS Link</span>
            </div>
            <div style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: var(--bg); border: 1px solid var(--border-soft); border-radius: 10px;">
              <span style="width: 28px; height: 28px; border-radius: 50%; background: var(--ink); color: var(--gold); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; font-family: 'IBM Plex Mono';">3</span>
              <span style="font-weight: 600; font-size: 14px; color: var(--ink);">Client Scans UPI / Pays Online</span>
            </div>
            <div style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: var(--teal-soft); border: 1px solid #BEE3D8; border-radius: 10px; color: var(--teal);">
              <span style="width: 28px; height: 28px; border-radius: 50%; background: var(--teal); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; font-family: 'IBM Plex Mono';">4</span>
              <span style="font-weight: 700; font-size: 14px; color: var(--teal);">Auto-Reconciled &amp; Paid in Ledger</span>
            </div>
          </div>
        </div>
        <div>
          <img src="{{ asset('images/payments_preview.jpg') }}" alt="UPI Payment Success Flow" class="illust-img">
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section id="pricing" style="background: #ffffff; border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); padding: 60px 0;">
    <div class="wrap text-center" style="max-width: 700px; margin: 0 auto 48px;">
      <div class="eyebrow">Transparent Pricing</div>
      <h2 style="font-size: 32px; font-weight: 700; color: var(--ink); margin-bottom: 10px;">Simple plans for every stage of growth</h2>
      <p style="color: var(--ink-soft); font-size: 15.5px; line-height: 1.6;">No hidden fees. Full 14-day free trial on all plans. Switch or upgrade anytime.</p>
    </div>
    <div class="wrap plans-container">
      <div class="plans" id="plans-slider">
        @foreach($plans->where('type', 'base') as $plan)
          @include('pages.partials.plan_card', ['plan' => $plan])
        @endforeach
      </div>
      <div class="slider-dots" id="plans-dots">
        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section style="background: var(--bg); padding: 60px 0;">
    <div class="wrap">
      <div class="sec-head" style="margin: 0 auto 36px; text-align: center; max-width: 620px;">
        <div class="eyebrow">Verified Merchant Reviews</div>
        <h2 style="font-size: 32px; font-weight: 700; color: var(--ink); margin-bottom: 10px;">Loved by 500+ Indian businesses</h2>
        <p style="color: var(--ink-soft); font-size: 15px; line-height: 1.6;">See how local retailers and restaurateurs save hours of daily manual tracking.</p>
      </div>
      
      <div class="wrap plans-container">
        <div class="reviews-slider" id="reviews-slider" style="gap: 24px;">
          
          <div class="feat-card" style="padding: 28px; background: #ffffff; border-radius: 16px;">
            <div style="color: var(--gold); font-size: 18px; margin-bottom: 12px; letter-spacing: 2px;">★★★★★</div>
            <p style="font-style: italic; font-size: 14.5px; color: var(--ink); margin-bottom: 18px; line-height: 1.6;">
              "Pehle invoice WhatsApp par bhejna aur customer ka baaki paisa track karna bahut mushkil tha. Ab 1-click me payment link chala jata hai aur jaise hi client pay karta hai ledger auto update ho jata hai."
            </p>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--gold); color: #fff; font-weight: 700; display: flex; align-items: center; justify-content: center; font-family: 'Space Grotesk';">
                RK
              </div>
              <div>
                <div style="font-weight: 700; font-size: 13.5px; color: var(--ink);">Rajesh Kumar</div>
                <div style="font-size: 11.5px; color: var(--ink-faint); font-family: 'IBM Plex Mono';">Kirana &amp; Supermart, Delhi</div>
              </div>
            </div>
          </div>

          <div class="feat-card" style="padding: 28px; background: #ffffff; border-radius: 16px;">
            <div style="color: var(--gold); font-size: 18px; margin-bottom: 12px; letter-spacing: 2px;">★★★★★</div>
            <p style="font-style: italic; font-size: 14.5px; color: var(--ink); margin-bottom: 18px; line-height: 1.6;">
              "Humare cafe me table QR scan karke order seedha kitchen screen par chala jata hai. Waiter ka time bachta hai aur rush hours me tables 2x tezi se clear hoti hain."
            </p>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--teal); color: #fff; font-weight: 700; display: flex; align-items: center; justify-content: center; font-family: 'Space Grotesk';">
                PS
              </div>
              <div>
                <div style="font-weight: 700; font-size: 13.5px; color: var(--ink);">Priya Sharma</div>
                <div style="font-size: 11.5px; color: var(--ink-faint); font-family: 'IBM Plex Mono';">The Coffee Nook, Pune</div>
              </div>
            </div>
          </div>

          <div class="feat-card" style="padding: 28px; background: #ffffff; border-radius: 16px;">
            <div style="color: var(--gold); font-size: 18px; margin-bottom: 12px; letter-spacing: 2px;">★★★★★</div>
            <p style="font-style: italic; font-size: 14.5px; color: var(--ink); margin-bottom: 18px; line-height: 1.6;">
              "Multi-location feature se main apni Delhi aur Gurugram dono branches ka stock aur staff attendance ek hi admin account se control karta hoon. Absolute game changer!"
            </p>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--ink); color: var(--gold); font-weight: 700; display: flex; align-items: center; justify-content: center; font-family: 'Space Grotesk';">
                AV
              </div>
              <div>
                <div style="font-weight: 700; font-size: 13.5px; color: var(--ink);">Amit Verma</div>
                <div style="font-size: 11.5px; color: var(--ink-faint); font-family: 'IBM Plex Mono';">Verma Electronics, Gurugram</div>
              </div>
            </div>
          </div>

        </div>
        <div class="slider-dots" id="reviews-dots">
          <span class="dot active"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section id="faq" style="background: var(--paper); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); padding: 60px 0;">
    <div class="wrap" style="max-width: 820px;">
      <div class="sec-head" style="margin-bottom: 32px; text-align: center; margin-left: auto; margin-right: auto;">
        <div class="eyebrow">Clear Answers</div>
        <h2 style="font-size: 32px; font-weight: 700; color: var(--ink); margin-bottom: 8px;">Frequently Asked Questions</h2>
        <p style="color: var(--ink-soft); font-size: 15px;">Everything you need to know about getting started with Vyapaargo.</p>
      </div>
      <div style="display: flex; flex-direction: column; gap: 12px;">
        @forelse($siteFaqs as $faq)
          <details class="feat-card" style="padding: 18px 22px; cursor: pointer; border-radius: 12px;">
            <summary style="font-weight: 600; font-size: 15px; font-family: 'Space Grotesk'; list-style: none; display: flex; justify-content: space-between; align-items: center; color: var(--ink);">
              <span>{{ $faq['question'] }}</span>
              <span style="color: var(--gold); font-weight: bold; font-size: 18px;">+</span>
            </summary>
            <p style="font-size: 14px; color: var(--ink-soft); margin-top: 12px; line-height: 1.6; cursor: default;">
              {{ $faq['answer'] }}
            </p>
          </details>
        @empty
          <div class="feat-card" style="padding: 24px; text-align: center;">
            <p style="color: var(--ink-soft);">No FAQs available currently.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- Final High-Converting CTA Banner -->
  <section style="padding: 60px 0 80px; background: var(--bg);">
    <div class="wrap">
      <div class="cta-banner" style="padding: 40px 48px; border-radius: 24px;">
        <div>
          <div class="eyebrow" style="background: rgba(217,154,43,0.2); color: var(--gold); border-color: rgba(217,154,43,0.3); margin-bottom: 12px;">
            START FREE TODAY
          </div>
          <h2 style="font-size: 32px; font-weight: 700; color: #ffffff; line-height: 1.2; margin-bottom: 8px;">
            Ready to modernize your business operations?
          </h2>
          <p style="color: #94A3B8; font-size: 15px; max-width: 540px;">
            Join hundreds of forward-thinking retailers, restaurants, and traders across India. Setup takes less than 5 minutes.
          </p>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;">
          <a class="btn btn-gold" style="font-size: 15.5px; padding: 14px 28px; font-weight: 700; border-radius: 10px;" href="{{ route('register') }}">
            Create Free Account &rarr;
          </a>
        </div>
      </div>
    </div>
  </section>

@endsection


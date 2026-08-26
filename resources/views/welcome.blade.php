@extends('layouts.public')

@section('content')

  <section class="hero">
    <div class="wrap hero-grid">
      <div>
        <div class="eyebrow">Built for Indian SMEs</div>
        <h1>Saara business,<br><em>ek jagah.</em></h1>
        <p class="lead">Inventory, billing, staff, payroll aur restaurant orders — sab ek Laravel-powered platform mein. Har invoice ka paisa track ho, har customer ka order kitchen tak pahunche.</p>
        <div class="hero-actions">
          <a class="btn btn-gold" href="{{ route('register') }}">Start Free Trial</a>
        </div>
        <div class="hero-note">
          <span>No card required</span>
          <span>GST-ready invoices</span>
          <span>Setup in a day</span>
        </div>
      </div>
      <div class="invoice-mock">
        <div class="inv-top">
          <div>
            <div class="who">Sharma Traders</div>
            <div class="num">INV-2026-0417 · 25 Aug</div>
          </div>
          <div class="stamp">PAID</div>
        </div>
        <div class="inv-lines">
          <div class="inv-line"><span>Basmati Rice 25kg × 4</span><span>₹6,200</span></div>
          <div class="inv-line"><span>Sunflower Oil 15L × 2</span><span>₹3,450</span></div>
          <div class="inv-line"><span>GST @ 5%</span><span>₹482</span></div>
        </div>
        <div class="inv-total"><span>Total Due</span><span>₹10,132</span></div>
        <div class="inv-bottom">
          <span class="mono" style="font-size:11px;color:var(--ink-faint)">Paid via Razorpay · UPI</span>
          <span class="badge badge-paid">SETTLED</span>
        </div>
        <div class="perf"></div>
      </div>
    </div>
  </section>

  <div class="trust">
    <div class="wrap trust-row">
      <div>Multi-organization &amp; role based access</div>
      <div>Barcode inventory scanning</div>
      <div>WhatsApp invoice sharing</div>
      <div>Razorpay verified webhooks</div>
      <div>Multi-location ready</div>
    </div>
  </div>

  <section id="features">
    <div class="wrap">
      <div class="sec-head">
        <div class="eyebrow">Core modules</div>
        <h2>Everything a growing shop needs, minus the extra tabs.</h2>
        <p>Sixteen modules, one login. Employees see only what their role allows — the owner sees everything.</p>
      </div>
      <div class="feat-grid">
        <div class="feat-card">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4zM3 7v10l9 4 9-4V7"/></svg></div>
          <h3>Products &amp; Barcode Inventory</h3>
          <p>Scan to add, scan to sell. Real-time stock with low-stock alerts before you run out.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/></svg></div>
          <h3>Invoicing, Pay Now or Later</h3>
          <p>Auto tax and totals, PDF and WhatsApp sharing, and dues that chase themselves.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M2 12h20"/></svg></div>
          <h3>Receivables Dashboard</h3>
          <p>See every rupee owed, by client, with one-tap reminders and a direct pay link.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M4 21V9M12 21V3M20 21v-7"/></svg></div>
          <h3>Attendance &amp; Payroll</h3>
          <p>Check-ins feed straight into salary slips. No spreadsheets at month-end.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></div>
          <h3>Business Health Score</h3>
          <p>Sales, profit, stock and receivables rolled into one number you check in five seconds.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon"><svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M3 21l4-4 4 4M3 3l4 4 4-4M21 21l-4-4-4 4M21 3l-4 4-4-4"/></svg></div>
          <h3>Multi-location Support</h3>
          <p>One owner login, many stores. Stock and staff scoped to each branch automatically.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Restaurant Section -->
  <section id="restaurant" style="background:#fff;">
    <div class="wrap">
      <div class="sec-head">
        <div class="eyebrow">Restaurant POS</div>
        <h2>Built for modern cafes &amp; restaurants.</h2>
        <p>Manage tables, send orders directly to the kitchen, and let customers order via QR codes seamlessly.</p>
      </div>
      <div class="rest-split">
        <div class="phone">
          <div class="phone-screen">
            <div style="font-family:'Space Grotesk'; font-weight:700; text-align:center; margin-bottom:12px;">Spice Kitchen</div>
            <div class="qr-chip" style="justify-content:center;">Table 07 &middot; Dine-in</div>
            <div class="menu-cat">Starters</div>
            <div class="menu-item"><div>Paneer Tikka</div><div style="display:flex; gap:10px; align-items:center;"><span class="price">₹220</span><div class="add">+</div></div></div>
            <div class="menu-item"><div>Spring Rolls</div><div style="display:flex; gap:10px; align-items:center;"><span class="price">₹180</span><div class="add">+</div></div></div>
            <div class="menu-cat" style="margin-top:16px;">Main Course</div>
            <div class="menu-item" style="border:none;"><div>Butter Chicken</div><div style="display:flex; gap:10px; align-items:center;"><span class="price">₹350</span><div class="add">+</div></div></div>
          </div>
        </div>
        <div>
          <h2 style="font-size:32px; font-weight:600; margin-bottom:24px;">Smart QR Ordering</h2>
          <ul class="rest-list">
            <li>
              <div class="rest-num">01</div>
              <div>
                <h4>Dynamic QR Codes</h4>
                <p>Generate secure QR codes for every table. Customers scan and order instantly from their phones.</p>
              </div>
            </li>
            <li>
              <div class="rest-num">02</div>
              <div>
                <h4>Kitchen Display System</h4>
                <p>Orders flow directly to the kitchen screen. No lost tickets, no delays.</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- Payments Section -->
  <section id="payments">
    <div class="wrap text-center" style="max-width:700px; margin:0 auto; margin-bottom:50px;">
      <div class="eyebrow">Razorpay Integration</div>
      <h2>Accept payments instantly.</h2>
      <p style="color:var(--ink-soft); font-size:15.5px; line-height:1.6;">Send payment links with invoices and let clients pay via UPI, cards, and net banking with automatic reconciliation.</p>
    </div>
    <div class="wrap">
      <div class="flow-card" style="max-width:800px; margin:0 auto; text-align:center;">
        <div class="flow-title" style="margin-bottom:32px;">Seamless Payment Flow</div>
        <div class="flow-row" style="justify-content:center;">
          <div class="flow-step">1. Generate Invoice</div>
          <div class="flow-arrow">➔</div>
          <div class="flow-step">2. Send Payment Link</div>
          <div class="flow-arrow">➔</div>
          <div class="flow-step">3. Client Pays via UPI</div>
          <div class="flow-arrow">➔</div>
          <div class="flow-step">4. Auto-Reconciled</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section id="pricing" style="background:#fff;">
    <div class="wrap text-center" style="max-width:700px; margin:0 auto; margin-bottom:60px;">
      <div class="eyebrow">Pricing Plans</div>
      <h2>Simple pricing, no hidden fees.</h2>
      <p style="color:var(--ink-soft); font-size:15.5px; line-height:1.6;">Start for free and upgrade as your business grows. Cancel anytime.</p>
    </div>
    <div class="wrap plans">
      <div class="plan-card">
        <div class="plan-name">Free</div>
        <div class="plan-price">₹0<span> / forever</span></div>
        <ul class="plan-feats">
          <li>1 User</li>
          <li>Unlimited Products</li>
          <li>Up to 50 Invoices/mo</li>
          <li>Basic Reports</li>
        </ul>
        <a class="btn btn-ghost" href="{{ route('register') }}" style="width:100%; justify-content:center;">Get Started</a>
      </div>
      <div class="plan-card feat">
        <div class="plan-tag">MOST POPULAR</div>
        <div class="plan-name">Pro</div>
        <div class="plan-price">₹499<span> / mo</span></div>
        <ul class="plan-feats">
          <li>5 Users</li>
          <li>Unlimited Invoices</li>
          <li>Employee Payroll</li>
          <li>Payment Gateway (Razorpay)</li>
        </ul>
        <a class="btn btn-gold" href="{{ route('register') }}" style="width:100%; justify-content:center;">Start Free Trial</a>
      </div>
      <div class="plan-card">
        <div class="plan-name">Restaurant</div>
        <div class="plan-price">₹999<span> / mo</span></div>
        <ul class="plan-feats">
          <li>Everything in Pro</li>
          <li>Digital QR Menu</li>
          <li>Kitchen Display System</li>
          <li>Table Management</li>
        </ul>
        <a class="btn btn-ghost" href="{{ route('register') }}" style="width:100%; justify-content:center;">Start Free Trial</a>
      </div>
      <div class="plan-card">
        <div class="plan-name">Enterprise</div>
        <div class="plan-price">Custom</div>
        <ul class="plan-feats">
          <li>Unlimited Users</li>
          <li>Multiple Locations</li>
          <li>Priority Support</li>
          <li>Custom Integrations</li>
        </ul>
        <a class="btn btn-ghost" href="{{ route('register') }}" style="width:100%; justify-content:center;">Contact Us</a>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section style="padding-bottom:100px;">
    <div class="wrap">
      <div class="cta-banner">
        <div>
          <h2>Ready to transform how you run your business?</h2>
          <p>Join thousands of growing SMEs in India.</p>
        </div>
        <a class="btn btn-gold" style="font-size:16px; padding:14px 28px;" href="{{ route('register') }}">Create Free Account</a>
      </div>
    </div>
  </section>

@endsection


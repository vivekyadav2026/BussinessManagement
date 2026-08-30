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
            <div class="num">INV-{{ date('Y') }}-0417 · {{ date('d M') }}</div>
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

  <!-- Dashboard Overview Section -->
  <section style="background: var(--paper); border-bottom: 1px solid var(--border-soft); padding: 32px 0;">
    <div class="wrap" style="text-align: center;">
      <div class="sec-head" style="margin: 0 auto 24px; text-align: center;">
        <div class="eyebrow">Real-time Analytics</div>
        <h2>Powerful Business Dashboard</h2>
        <p>Monitor your entire business health from a single dashboard. Sales trends, stock level status, and active invoice tracking - all updated in real-time.</p>
      </div>
      <div style="max-width: 900px; margin: 0 auto;">
        <img src="{{ asset('images/dashboard_preview.jpg') }}" alt="Vyapaargo Dashboard" class="illust-img">
      </div>
    </div>
  </section>

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
        <div>
          <img src="{{ asset('images/restaurant_preview.jpg') }}" alt="Restaurant QR Menu Ordering" class="illust-img">
        </div>
        <div>
          <h2 class="section-title">Smart QR Ordering</h2>
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
    <div class="wrap text-center" style="max-width:700px; margin:0 auto; margin-bottom:32px;">
      <div class="eyebrow">Razorpay Integration</div>
      <h2>Accept payments instantly.</h2>
      <p style="color:var(--ink-soft); font-size:15.5px; line-height:1.6;">Send payment links with invoices and let clients pay via UPI, cards, and net banking with automatic reconciliation.</p>
    </div>
    <div class="wrap payment-grid">
      <div class="flow-card" style="text-align:center; width: 100%;">
        <div class="flow-title" style="margin-bottom:20px;">Seamless Payment Flow</div>
        <div class="flow-row" style="justify-content:center; flex-direction: column; gap: 8px;">
          <div class="flow-step" style="width: 100%;">1. Generate Invoice</div>
          <div class="flow-arrow" style="transform: rotate(90deg); margin: 2px 0; display: inline-block;">➔</div>
          <div class="flow-step" style="width: 100%;">2. Send Payment Link</div>
          <div class="flow-arrow" style="transform: rotate(90deg); margin: 2px 0; display: inline-block;">➔</div>
          <div class="flow-step" style="width: 100%;">3. Client Pays via UPI</div>
          <div class="flow-arrow" style="transform: rotate(90deg); margin: 2px 0; display: inline-block;">➔</div>
          <div class="flow-step" style="width: 100%;">4. Auto-Reconciled</div>
        </div>
      </div>
      <div>
        <img src="{{ asset('images/payments_preview.jpg') }}" alt="UPI Payment Success" class="illust-img">
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
    <div class="wrap plans-container">
      <div class="plans" id="plans-slider">
        @foreach($plans as $plan)
          @php
            $isFeat = ($plan->name === 'Pro Plan');
            $isEnterprise = ($plan->name === 'Enterprise');
            $displayFeatures = $plan->features->filter(function($f) {
                return !in_array($f->feature_code, ['max_employees', 'module_payroll', 'module_restaurant']);
            });
          @endphp
          <div class="plan-card {{ $isFeat ? 'feat' : '' }}">
            @if($isFeat)
              <div class="plan-tag">MOST POPULAR</div>
            @endif
            <div class="plan-name">{{ $plan->name }}</div>
            <div class="plan-price">
              @if($isEnterprise)
                Custom
              @else
                ₹{{ number_format($plan->price_monthly, 0) }}<span> / {{ $plan->name === 'Free' ? 'forever' : 'mo' }}</span>
              @endif
            </div>
            <ul class="plan-feats">
              @foreach($displayFeatures as $feature)
                <li>{{ $feature->feature_code }}</li>
              @endforeach
            </ul>
            @if($isEnterprise)
              <a class="btn btn-ghost" href="{{ route('register') }}" style="width:100%; justify-content:center;">Contact Us</a>
            @else
              <a class="btn {{ $isFeat ? 'btn-gold' : 'btn-ghost' }}" href="{{ route('register') }}" style="width:100%; justify-content:center;">
                {{ $plan->name === 'Free' ? 'Get Started' : 'Start Free Trial' }}
              </a>
            @endif
          </div>
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
  <section style="background: var(--paper); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft);">
    <div class="wrap">
      <div class="sec-head" style="margin: 0 auto 24px; text-align: center; max-width: 600px;">
        <div class="eyebrow">User Stories</div>
        <h2>Loved by Indian business owners.</h2>
        <p>See how local merchants are saving hours of manual billing and inventory tracking every single day.</p>
      </div>
    <div class="wrap plans-container">
      <div class="reviews-slider" id="reviews-slider">
        <div class="feat-card" style="padding: 20px;">
          <div style="color: var(--gold); font-size: 16px; margin-bottom: 8px;">★★★★★</div>
          <p style="font-style: italic; font-size: 13px; color: var(--ink-soft); margin-bottom: 12px; line-height: 1.4;">"Pehle invoice WhatsApp par bhejna aur receivables track karna sir dard tha. Ab ek click me direct UPI payment link chala jata hai."</p>
          <div style="font-weight: 700; font-size: 12.5px;">- Rajesh Kumar</div>
          <div style="font-size: 11px; color: var(--ink-faint); font-family: 'IBM Plex Mono';">Kirana Store, New Delhi</div>
        </div>
        <div class="feat-card" style="padding: 20px;">
          <div style="color: var(--gold); font-size: 16px; margin-bottom: 8px;">★★★★★</div>
          <p style="font-style: italic; font-size: 13px; color: var(--ink-soft); margin-bottom: 12px; line-height: 1.4;">"Humare cafe me QR order seedha kitchen screen par jata hai. Kot print karne ka jhanjhat khatam ho gaya. Billing automatically track hoti hai."</p>
          <div style="font-weight: 700; font-size: 12.5px;">- Priya Sharma</div>
          <div style="font-size: 11px; color: var(--ink-faint); font-family: 'IBM Plex Mono';">The Coffee Nook, Pune</div>
        </div>
        <div class="feat-card" style="padding: 20px;">
          <div style="color: var(--gold); font-size: 16px; margin-bottom: 8px;">★★★★★</div>
          <p style="font-style: italic; font-size: 13px; color: var(--ink-soft); margin-bottom: 12px; line-height: 1.4;">"Multi-location feature se main apni Delhi aur Gurugram dono branches ka stock aur attendance ek hi admin account se control karta hoon."</p>
          <div style="font-weight: 700; font-size: 12.5px;">- Amit Verma</div>
          <div style="font-size: 11px; color: var(--ink-faint); font-family: 'IBM Plex Mono';">Verma Electronics, Gurugram</div>
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
  <section style="background: var(--bg); border-bottom: 1px solid var(--border-soft);">
    <div class="wrap" style="max-width: 800px;">
      <div class="sec-head" style="margin-bottom: 24px; text-align: center; margin-left: auto; margin-right: auto;">
        <div class="eyebrow">FAQs</div>
        <h2>Frequently Asked Questions</h2>
      </div>
      <div style="display: flex; flex-direction: column; gap: 10px;">
        <details class="feat-card" style="padding: 14px 18px; cursor: pointer;">
          <summary style="font-weight: 600; font-size: 14.5px; font-family: 'Space Grotesk'; list-style: none; display: flex; justify-content: space-between; align-items: center;">
            <span>Vyapaargo setup karne me kitna samay lagta hai?</span>
            <span style="color: var(--gold); font-weight: bold;">+</span>
          </summary>
          <p style="font-size: 13px; color: var(--ink-soft); margin-top: 10px; line-height: 1.5; cursor: default;">Setup bilkul aasan hai. Aap 5 minutes me register karke products add kar sakte hain aur billing shuru kar sakte hain.</p>
        </details>
        <details class="feat-card" style="padding: 14px 18px; cursor: pointer;">
          <summary style="font-weight: 600; font-size: 14.5px; font-family: 'Space Grotesk'; list-style: none; display: flex; justify-content: space-between; align-items: center;">
            <span>Kya online payments automatic reconcile hoti hain?</span>
            <span style="color: var(--gold); font-weight: bold;">+</span>
          </summary>
          <p style="font-size: 13px; color: var(--ink-soft); margin-top: 10px; line-height: 1.5; cursor: default;">Haan, Razorpay verified webhooks ke zariye jab bhi koi customer UPI ya card se pay karta hai, invoice automatically status 'Paid' me update ho jata hai.</p>
        </details>
        <details class="feat-card" style="padding: 14px 18px; cursor: pointer;">
          <summary style="font-weight: 600; font-size: 14.5px; font-family: 'Space Grotesk'; list-style: none; display: flex; justify-content: space-between; align-items: center;">
            <span>Kya main ek account me multiple locations manage kar sakta hoon?</span>
            <span style="color: var(--gold); font-weight: bold;">+</span>
          </summary>
          <p style="font-size: 13px; color: var(--ink-soft); margin-top: 10px; line-height: 1.5; cursor: default;">Haan, Enterprise aur Pro plans me multi-location support hai. Har branch ka stock, sales aur staff attendance separate scope hota hai jo owner ek dashboard se dekh sakta hai.</p>
        </details>
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


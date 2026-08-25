<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Khatabook Pro — SME Business Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#F3F5F3;
    --paper:#FFFFFF;
    --ink:#17233F;
    --ink-soft:#4B5670;
    --ink-faint:#8991A5;
    --gold:#D99A2B;
    --gold-deep:#B87F1B;
    --teal:#146356;
    --teal-soft:#E4F0EC;
    --rose:#AE3B34;
    --rose-soft:#F5E6E4;
    --border:#DFE1DA;
    --border-soft:#EBECE6;
    --radius:14px;
    --shadow: 0 1px 2px rgba(23,35,63,.04), 0 8px 24px rgba(23,35,63,.06);
  }
  *{box-sizing:border-box; margin:0; padding:0;}
  html{scroll-behavior:smooth;}
  body{
    background:var(--bg);
    color:var(--ink);
    font-family:'Inter',sans-serif;
    -webkit-font-smoothing:antialiased;
    overflow-x:hidden;
  }
  h1,h2,h3,.display{font-family:'Space Grotesk',sans-serif; letter-spacing:-0.01em;}
  .mono{font-family:'IBM Plex Mono',monospace;}
  a{color:inherit; text-decoration:none;}
  img,svg{display:block;}
  .wrap{max-width:1180px; margin:0 auto; padding:0 32px;}

  /* Header */
  header.site{
    position:sticky; top:0; z-index:50; background:rgba(243,245,243,.86); backdrop-filter:blur(10px);
    border-bottom:1px solid var(--border-soft);
  }
  .nav{display:flex; align-items:center; justify-content:space-between; padding:18px 32px;}
  .logo{display:flex; align-items:center; gap:9px; font-family:'Space Grotesk'; font-weight:700; font-size:18px;}
  .logo .mark{width:26px; height:26px; background:var(--ink); border-radius:6px; position:relative; flex:none;}
  .logo .mark::before{content:''; position:absolute; left:6px; right:6px; top:7px; height:2px; background:var(--gold); box-shadow:0 5px 0 var(--gold), 0 10px 0 var(--gold);}
  .nav-links{display:flex; gap:34px; font-size:14.5px; color:var(--ink-soft); font-weight:500;}
  .nav-links a:hover{color:var(--ink);}
  .nav-cta{display:flex; gap:10px; align-items:center;}

  /* Buttons */
  .btn{
    display:inline-flex; align-items:center; gap:8px; font-family:'Space Grotesk'; font-weight:600;
    font-size:14px; padding:11px 20px; border-radius:9px; cursor:pointer; border:1px solid transparent;
    transition:.15s;
  }
  .btn-gold{background:var(--gold); color:var(--ink);}
  .btn-gold:hover{background:var(--gold-deep);}
  .btn-ghost{border-color:var(--border); color:var(--ink);}
  .btn-ghost:hover{border-color:var(--ink);}
  .btn-sm{padding:8px 14px; font-size:13px;}

  /* Hero */
  .hero{padding:88px 0 60px;}
  .hero-grid{display:grid; grid-template-columns:1.05fr .95fr; gap:56px; align-items:center;}
  .eyebrow{
    display:inline-flex; align-items:center; gap:8px; font-family:'IBM Plex Mono'; font-size:12px;
    color:var(--gold-deep); background:#FBF1DD; border:1px solid #EFDDAE; padding:6px 12px; border-radius:100px;
    margin-bottom:22px; letter-spacing:.03em;
  }
  .eyebrow::before{content:'●'; font-size:8px;}
  .hero h1{font-size:52px; line-height:1.06; font-weight:600; margin-bottom:22px;}
  .hero h1 em{font-style:normal; color:var(--gold-deep); position:relative;}
  .hero p.lead{font-size:17px; color:var(--ink-soft); line-height:1.6; max-width:490px; margin-bottom:32px;}
  .hero-actions{display:flex; gap:14px; margin-bottom:36px;}
  .hero-note{font-size:13px; color:var(--ink-faint); display:flex; gap:18px;}
  .hero-note span{display:flex; align-items:center; gap:6px;}
  .hero-note span::before{content:'✓'; color:var(--teal); font-weight:700;}

  /* invoice mock card */
  .invoice-mock{
    background:var(--paper); border-radius:4px; padding:28px 26px 24px; position:relative; max-width:390px; margin-left:auto;
    box-shadow:var(--shadow); border:1px solid var(--border-soft);
  }
  .invoice-mock::before, .invoice-mock::after{
    content:''; position:absolute; width:22px; height:22px; background:var(--bg); border-radius:50%; top:50%; transform:translateY(-50%);
  }
  .invoice-mock::before{left:-11px;} .invoice-mock::after{right:-11px;}
  .invoice-mock .perf{
    position:absolute; left:0; right:0; top:50%; border-top:2px dashed var(--border); transform:translateY(-1px);
  }
  .inv-top{display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px;}
  .inv-top .who{font-family:'Space Grotesk'; font-weight:700; font-size:15px;}
  .inv-top .num{font-family:'IBM Plex Mono'; font-size:11px; color:var(--ink-faint);}
  .stamp{
    font-family:'Space Grotesk'; font-weight:700; font-size:13px; color:var(--teal); border:2.5px solid var(--teal);
    border-radius:50%; width:64px; height:64px; display:flex; align-items:center; justify-content:center; text-align:center;
    transform:rotate(-14deg); opacity:.9; letter-spacing:.02em;
  }
  .inv-lines{padding:16px 0; border-top:1px solid var(--border-soft); font-size:13px;}
  .inv-line{display:flex; justify-content:space-between; padding:6px 0; color:var(--ink-soft);}
  .inv-line span:last-child{font-family:'IBM Plex Mono'; color:var(--ink);}
  .inv-total{display:flex; justify-content:space-between; padding-top:14px; margin-top:6px; border-top:1.5px solid var(--ink); font-family:'Space Grotesk'; font-weight:700; font-size:16px;}
  .inv-total span:last-child{font-family:'IBM Plex Mono';}
  .inv-bottom{margin-top:20px; padding-top:16px; border-top:2px dashed var(--border); display:flex; justify-content:space-between; align-items:center;}
  .badge{font-family:'IBM Plex Mono'; font-size:10.5px; padding:4px 9px; border-radius:100px; font-weight:600; letter-spacing:.03em;}
  .badge-paid{background:var(--teal-soft); color:var(--teal);}

  /* trust strip */
  .trust{padding:26px 0; border-top:1px solid var(--border-soft); border-bottom:1px solid var(--border-soft);}
  .trust-row{display:flex; justify-content:space-between; flex-wrap:wrap; gap:14px; font-family:'IBM Plex Mono'; font-size:12px; color:var(--ink-faint);}
  .trust-row div{display:flex; align-items:center; gap:7px;}
  .trust-row div::before{content:''; width:5px; height:5px; background:var(--gold); border-radius:50%;}

  /* section generic */
  section{padding:88px 0;}
  .sec-head{max-width:600px; margin-bottom:52px;}
  .sec-head .eyebrow{margin-bottom:16px;}
  .sec-head h2{font-size:34px; font-weight:600; margin-bottom:14px; line-height:1.15;}
  .sec-head p{color:var(--ink-soft); font-size:15.5px; line-height:1.6;}

  .feat-grid{display:grid; grid-template-columns:repeat(3,1fr); gap:22px;}
  .feat-card{
    background:var(--paper); border:1px solid var(--border-soft); border-radius:var(--radius); padding:26px;
    transition:.2s;
  }
  .feat-card:hover{border-color:var(--gold); transform:translateY(-2px); box-shadow:var(--shadow);}
  .feat-icon{width:38px; height:38px; border-radius:9px; background:var(--ink); display:flex; align-items:center; justify-content:center; margin-bottom:18px;}
  .feat-icon svg{width:18px; height:18px; stroke:var(--gold);}
  .feat-card h3{font-size:16px; font-weight:600; margin-bottom:8px;}
  .feat-card p{font-size:13.8px; color:var(--ink-soft); line-height:1.55;}

  /* restaurant split */
  .rest-split{display:grid; grid-template-columns:.9fr 1.1fr; gap:60px; align-items:center;}
  .phone{
    width:230px; margin:0 auto; background:var(--ink); border-radius:32px; padding:10px; box-shadow:var(--shadow);
  }
  .phone-screen{background:var(--paper); border-radius:24px; overflow:hidden; padding:16px 14px;}
  .phone-screen .menu-cat{font-family:'IBM Plex Mono'; font-size:9.5px; color:var(--gold-deep); text-transform:uppercase; letter-spacing:.06em; margin:14px 0 8px;}
  .menu-item{display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid var(--border-soft); font-size:12px;}
  .menu-item .price{font-family:'IBM Plex Mono'; font-weight:600;}
  .menu-item .add{width:20px; height:20px; border-radius:50%; background:var(--teal-soft); color:var(--teal); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700;}
  .qr-chip{display:flex; align-items:center; gap:8px; font-family:'IBM Plex Mono'; font-size:10px; color:var(--ink-faint); background:var(--bg); border-radius:8px; padding:8px 10px; margin-bottom:6px;}
  .qr-chip .dot{width:6px; height:6px; border-radius:1px; background:var(--ink);}

  .rest-list{list-style:none;}
  .rest-list li{display:flex; gap:16px; padding:16px 0; border-bottom:1px solid var(--border-soft);}
  .rest-list li:last-child{border-bottom:none;}
  .rest-num{font-family:'IBM Plex Mono'; font-size:12px; color:var(--gold-deep); flex:none; padding-top:2px;}
  .rest-list h4{font-size:15px; font-weight:600; margin-bottom:5px; font-family:'Space Grotesk';}
  .rest-list p{font-size:13.5px; color:var(--ink-soft); line-height:1.55;}

  /* payments flow */
  .flow-card{background:var(--paper); border:1px solid var(--border-soft); border-radius:var(--radius); padding:32px; box-shadow:var(--shadow);}
  .flow-title{font-family:'IBM Plex Mono'; font-size:11.5px; color:var(--ink-faint); text-transform:uppercase; letter-spacing:.06em; margin-bottom:18px;}
  .flow-row{display:flex; align-items:center; gap:0; flex-wrap:wrap;}
  .flow-step{background:var(--bg); border:1px solid var(--border); border-radius:9px; padding:11px 16px; font-size:13px; font-weight:500; white-space:nowrap;}
  .flow-arrow{color:var(--ink-faint); padding:0 12px; font-family:'IBM Plex Mono';}
  .flow-row + .flow-row{margin-top:16px;}

  /* pricing */
  .plans{display:grid; grid-template-columns:repeat(4,1fr); gap:18px;}
  .plan-card{background:var(--paper); border:1px solid var(--border-soft); border-radius:var(--radius); padding:26px 22px; position:relative;}
  .plan-card.feat{border:1.5px solid var(--gold); box-shadow:var(--shadow);}
  .plan-tag{position:absolute; top:-11px; right:20px; background:var(--gold); color:var(--ink); font-family:'IBM Plex Mono'; font-size:10px; font-weight:700; padding:4px 10px; border-radius:100px;}
  .plan-name{font-family:'Space Grotesk'; font-weight:700; font-size:16px; margin-bottom:6px;}
  .plan-price{font-family:'IBM Plex Mono'; font-size:24px; font-weight:600; margin-bottom:18px;}
  .plan-price span{font-size:12px; color:var(--ink-faint); font-weight:400;}
  .plan-feats{list-style:none; font-size:12.8px; color:var(--ink-soft); margin-bottom:22px;}
  .plan-feats li{padding:6px 0; display:flex; gap:8px; border-bottom:1px dashed var(--border-soft);}
  .plan-feats li::before{content:'—'; color:var(--gold-deep);}

  /* CTA banner */
  .cta-banner{background:var(--ink); border-radius:20px; padding:56px; display:flex; justify-content:space-between; align-items:center; color:#fff;}
  .cta-banner h2{color:#fff; font-size:28px; max-width:480px;}
  .cta-banner p{color:#AAB3CB; margin-top:8px; font-size:14px;}

  footer.site{padding:44px 0; border-top:1px solid var(--border-soft); display:flex; justify-content:space-between; align-items:center; color:var(--ink-faint); font-size:13px;}

  @media(max-width:900px){
    .hero-grid,.rest-split,.plans,.feat-grid{grid-template-columns:1fr;}
    .plans{grid-template-columns:repeat(2,1fr);}
    .hero h1{font-size:36px;}
    .nav-links{display:none;}
    .cta-banner{flex-direction:column; gap:20px; text-align:center; padding:36px;}
  }
</style>
</head>
<body>

  <header class="site">
    <nav class="wrap nav">
      <div class="logo"><div class="mark"></div>Khatabook Pro</div>
      <div class="nav-links">
        <a href="#features">Features</a>
        <a href="#restaurant">Restaurant</a>
        <a href="#payments">Payments</a>
        <a href="#pricing">Pricing</a>
      </div>
      <div class="nav-cta">
        @auth
            <a class="btn btn-ghost btn-sm" href="{{ route('dashboard') }}">See Dashboard</a>
        @else
            <a class="btn btn-ghost btn-sm" href="{{ route('login') }}">Log in</a>
            <a class="btn btn-gold btn-sm" href="{{ route('register') }}">Start Free</a>
        @endauth
      </div>
    </nav>
  </header>

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

  <footer class="site">
    <div class="wrap" style="display:flex;justify-content:space-between;">
      <div class="logo" style="font-size:15px;"><div class="mark" style="width:20px;height:20px;"></div>Khatabook Pro</div>
      <div>© 2026 Khatabook Pro · Laravel · MySQL · Razorpay</div>
    </div>
  </footer>

</body>
</html>

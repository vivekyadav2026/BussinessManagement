<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name', 'Vyapaargo') }} — SME Business Platform</title>
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
    position:sticky; top:0; z-index:110; background:rgba(243,245,243,.86); backdrop-filter:blur(10px);
    border-bottom:1px solid var(--border-soft);
  }
  .nav{display:flex; align-items:center; justify-content:space-between; padding:18px 32px;}
  .logo{display:flex; align-items:center; gap:9px; font-family:'Space Grotesk'; font-weight:700; font-size:18px;}
  .logo .mark{width:26px; height:26px; background:var(--ink); border-radius:6px; position:relative; flex:none;}
  .logo .mark::before{content:''; position:absolute; left:6px; right:6px; top:7px; height:2px; background:var(--gold); box-shadow:0 5px 0 var(--gold), 0 10px 0 var(--gold);}
  .nav-links{display:flex; gap:34px; font-size:14.5px; color:var(--ink-soft); font-weight:500;}
  .nav-links a:hover{color:var(--ink);}
  .nav-cta{display:flex; gap:10px; align-items:center;}

  /* Responsive Utility Classes */
  .page-title {
    font-size: 46px;
    line-height: 1.1;
    font-weight: 700;
    margin-bottom: 20px;
    font-family: 'Space Grotesk', sans-serif;
  }
  .page-title em {
    font-style: normal;
    color: var(--gold-deep);
  }
  .page-lead {
    font-size: 16.5px;
    color: var(--ink-soft);
    line-height: 1.55;
    margin: 0 auto;
  }
  .section-title {
    font-size: 32px;
    font-weight: 600;
    margin-bottom: 24px;
    font-family: 'Space Grotesk', sans-serif;
  }
  .grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
  }
  .payment-grid {
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    gap: 40px;
    align-items: center;
  }

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
  .hero{
    padding:24px 0 16px;
    background: radial-gradient(circle at 80% 20%, rgba(217, 154, 43, 0.08) 0%, transparent 50%), 
                radial-gradient(circle at 10% 80%, rgba(20, 99, 86, 0.06) 0%, transparent 50%);
  }
  .hero-grid{display:grid; grid-template-columns:1.05fr .95fr; gap:32px; align-items:center;}
  .eyebrow{
    display:inline-flex; align-items:center; gap:8px; font-family:'IBM Plex Mono'; font-size:12px;
    color:var(--gold-deep); background:#FBF1DD; border:1px solid #EFDDAE; padding:4px 10px; border-radius:100px;
    margin-bottom:12px; letter-spacing:.03em;
  }
  .eyebrow::before{content:'●'; font-size:8px;}
  .hero h1{font-size:46px; line-height:1.06; font-weight:600; margin-bottom:14px;}
  .hero h1 em{font-style:normal; color:var(--gold-deep); position:relative;}
  .hero p.lead{font-size:15.5px; color:var(--ink-soft); line-height:1.55; max-width:490px; margin-bottom:18px;}
  .hero-actions{display:flex; gap:14px; margin-bottom:20px;}
  .hero-note{font-size:12.5px; color:var(--ink-faint); display:flex; gap:18px;}
  .hero-note span{display:flex; align-items:center; gap:6px;}
  .hero-note span::before{content:'✓'; color:var(--teal); font-weight:700;}

  /* invoice mock card */
  .invoice-mock{
    background:var(--paper); border-radius:12px; padding:20px; position:relative; max-width:390px; margin-left:auto;
    box-shadow: 0 10px 30px -10px rgba(23,35,63,.15); border:1px solid var(--border-soft);
    transition: transform 0.3s ease;
  }
  .invoice-mock:hover{
    transform: translateY(-4px) rotate(1deg);
  }
  .invoice-mock::before, .invoice-mock::after{
    content:''; position:absolute; width:22px; height:22px; background:var(--bg); border-radius:50%; top:50%; transform:translateY(-50%);
  }
  .invoice-mock::before{left:-11px;} .invoice-mock::after{right:-11px;}
  .invoice-mock .perf{
    position:absolute; left:0; right:0; top:50%; border-top:2px dashed var(--border); transform:translateY(-1px);
  }
  .inv-top{display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;}
  .inv-top .who{font-family:'Space Grotesk'; font-weight:700; font-size:15px;}
  .inv-top .num{font-family:'IBM Plex Mono'; font-size:11px; color:var(--ink-faint);}
  .stamp{
    font-family:'Space Grotesk'; font-weight:700; font-size:13px; color:var(--teal); border:2.5px solid var(--teal);
    border-radius:50%; width:64px; height:64px; display:flex; align-items:center; justify-content:center; text-align:center;
    transform:rotate(-14deg); opacity:.9; letter-spacing:.02em;
  }
  .inv-lines{padding:12px 0; border-top:1px solid var(--border-soft); font-size:13px;}
  .inv-line{display:flex; justify-content:space-between; padding:4px 0; color:var(--ink-soft);}
  .inv-line span:last-child{font-family:'IBM Plex Mono'; color:var(--ink);}
  .inv-total{display:flex; justify-content:space-between; padding-top:12px; margin-top:4px; border-top:1.5px solid var(--ink); font-family:'Space Grotesk'; font-weight:700; font-size:16px;}
  .inv-total span:last-child{font-family:'IBM Plex Mono';}
  .inv-bottom{margin-top:16px; padding-top:12px; border-top:2px dashed var(--border); display:flex; justify-content:space-between; align-items:center;}
  .badge{font-family:'IBM Plex Mono'; font-size:10.5px; padding:4px 9px; border-radius:100px; font-weight:600; letter-spacing:.03em;}
  .badge-paid{background:var(--teal-soft); color:var(--teal);}

  /* trust strip */
  .trust{padding:12px 0; border-top:1px solid var(--border-soft); border-bottom:1px solid var(--border-soft);}
  .trust-row{display:flex; justify-content:space-between; flex-wrap:wrap; gap:14px; font-family:'IBM Plex Mono'; font-size:12px; color:var(--ink-faint);}
  .trust-row div{display:flex; align-items:center; gap:7px;}
  .trust-row div::before{content:''; width:5px; height:5px; background:var(--gold); border-radius:50%;}

  /* section generic */
  section{padding:28px 0;}
  .sec-head{max-width:600px; margin-bottom:18px;}
  .sec-head .eyebrow{margin-bottom:8px;}
  .sec-head h2{font-size:30px; font-weight:600; margin-bottom:8px; line-height:1.15;}
  .sec-head p{color:var(--ink-soft); font-size:14.5px; line-height:1.5;}

  .feat-grid{display:grid; grid-template-columns:repeat(3,1fr); gap:16px;}
  .feat-card{
    background:var(--paper); border:1px solid var(--border-soft); border-radius:var(--radius); padding:20px;
    transition:.2s;
  }
  .feat-card:hover{border-color:var(--gold); transform:translateY(-2px); box-shadow:var(--shadow);}
  .feat-icon{width:38px; height:38px; border-radius:9px; background:var(--ink); display:flex; align-items:center; justify-content:center; margin-bottom:14px;}
  .feat-icon svg{stroke: var(--gold) !important; width: 22px; height: 22px; display: block;}

  /* Component CSS moved from welcome */
  .feat-card h3{font-size:16px; font-weight:600; margin-bottom:8px;}
  .feat-card p{font-size:13.8px; color:var(--ink-soft); line-height:1.55;}
  .rest-split{display:grid; grid-template-columns:.9fr 1.1fr; gap:24px; align-items:center;}
  .phone{width:230px; margin:0 auto; background:var(--ink); border-radius:32px; padding:10px; box-shadow:var(--shadow);}
  .phone-screen{background:var(--paper); border-radius:24px; overflow:hidden; padding:16px 14px;}
  .phone-screen .menu-cat{font-family:'IBM Plex Mono'; font-size:9.5px; color:var(--gold-deep); text-transform:uppercase; letter-spacing:.06em; margin:14px 0 8px;}
  .menu-item{display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid var(--border-soft); font-size:12px;}
  .menu-item .price{font-family:'IBM Plex Mono'; font-weight:600;}
  .menu-item .add{width:20px; height:20px; border-radius:50%; background:var(--teal-soft); color:var(--teal); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700;}
  .qr-chip{display:flex; align-items:center; gap:8px; font-family:'IBM Plex Mono'; font-size:10px; color:var(--ink-faint); background:var(--bg); border-radius:8px; padding:8px 10px; margin-bottom:6px;}
  .qr-chip .dot{width:6px; height:6px; border-radius:1px; background:var(--ink);}
  .rest-list{list-style:none;}
  .rest-list li{display:flex; gap:16px; padding:12px 0; border-bottom:1px solid var(--border-soft);}
  .rest-list li:last-child{border-bottom:none;}
  .rest-num{font-family:'IBM Plex Mono'; font-size:12px; color:var(--gold-deep); flex:none; padding-top:2px;}
  .rest-list h4{font-size:15px; font-weight:600; margin-bottom:5px; font-family:'Space Grotesk';}
  .rest-list p{font-size:13.5px; color:var(--ink-soft); line-height:1.55;}
  .flow-card{background:var(--paper); border:1px solid var(--border-soft); border-radius:var(--radius); padding:24px; box-shadow:var(--shadow);}
  .flow-title{font-family:'IBM Plex Mono'; font-size:11.5px; color:var(--ink-faint); text-transform:uppercase; letter-spacing:.06em; margin-bottom:14px;}
  .flow-row{display:flex; align-items:center; gap:0; flex-wrap:wrap;}
  .flow-step{background:var(--bg); border:1px solid var(--border); border-radius:9px; padding:11px 16px; font-size:13px; font-weight:500; white-space:nowrap;}
  .flow-arrow{color:var(--ink-faint); padding:0 12px; font-family:'IBM Plex Mono';}
  .flow-row + .flow-row{margin-top:12px;}
  .plans-container {
    width: 100%;
    overflow: hidden;
  }
  .slider-dots {
    display: none;
    justify-content: center;
    gap: 8px;
    margin-top: 12px;
    padding-bottom: 8px;
  }
  .slider-dots .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--border);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .slider-dots .dot.active {
    background: var(--gold);
    width: 20px;
    border-radius: 4px;
  }
  .plans{display:grid; grid-template-columns:repeat(4,1fr); gap:10px;}
  .reviews-slider {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }
  .grid-2-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
  .side-cards-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .plan-card{background:var(--paper); border:1px solid var(--border-soft); border-radius:var(--radius); padding:20px 16px; position:relative; display: flex; flex-direction: column; justify-content: space-between;}
  .plan-card.feat{border:1.5px solid var(--gold); box-shadow:var(--shadow);}
  .plan-tag{position:absolute; top:-11px; right:20px; background:var(--gold); color:var(--ink); font-family:'IBM Plex Mono'; font-size:10px; font-weight:700; padding:4px 10px; border-radius:100px;}
  .plan-name{font-family:'Space Grotesk'; font-weight:700; font-size:16px; margin-bottom:6px;}
  .plan-price{font-family:'IBM Plex Mono'; font-size:24px; font-weight:600; margin-bottom:18px;}
  .plan-price span{font-size:12px; color:var(--ink-faint); font-weight:400;}
  .plan-feats{list-style:none; font-size:12.8px; color:var(--ink-soft); margin-bottom:22px;}
  .plan-feats li{padding:6px 0; display:flex; gap:8px; border-bottom:1px dashed var(--border-soft);}
  .plan-feats li::before{content:'—'; color:var(--gold-deep);}
  .cta-banner{
    background: radial-gradient(circle at 10% 20%, rgba(217, 154, 43, 0.15) 0%, transparent 40%), var(--ink);
    border-radius: 20px;
    padding: 28px 36px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #fff;
    border: 1px solid rgba(217, 154, 43, 0.2);
    box-shadow: 0 10px 30px -10px rgba(217, 154, 43, 0.1);
  }
  .cta-banner h2{color:#fff; font-size:24px; max-width:480px;}
  .cta-banner p{color:#AAB3CB; margin-top:6px; font-size:13px;}
  
  /* Generated Illustrations Styling */
  .illust-img {
    width: 100%;
    border-radius: var(--radius);
    border: 1px solid var(--border-soft);
    box-shadow: var(--shadow);
    transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
    object-fit: cover;
    display: block;
  }
  .illust-img:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(23,35,63,.06), 0 12px 30px rgba(23,35,63,.1);
  }
  
  /* Desktop defaults for custom toggle elements */
  .menu-toggle { display: none; }
  .mobile-menu { display: none; }
  .mobile-menu-backdrop { display: none; }

  footer.site{padding:44px 0; border-top:1px solid var(--border-soft); display:flex; justify-content:space-between; align-items:center; color:var(--ink-faint); font-size:13px;}

  @media(max-width:900px){
    .slider-dots { display: flex !important; }
    .hero-grid, .rest-split, .payment-grid { grid-template-columns: 1fr !important; gap: 24px !important; }
    .feat-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 16px !important; }
    .grid-2-cards { grid-template-columns: repeat(2, 1fr) !important; gap: 16px !important; }
    .side-cards-list { display: grid !important; grid-template-columns: repeat(2, 1fr) !important; gap: 16px !important; }
    .plans {
      display: flex !important;
      flex-wrap: nowrap !important;
      overflow-x: auto !important;
      scroll-snap-type: x mandatory !important;
      gap: 16px !important;
      padding: 16px 0 24px !important;
      margin: 0 !important;
      -webkit-overflow-scrolling: touch !important;
      scrollbar-width: none !important;
      width: 100% !important;
    }
    .plans::-webkit-scrollbar {
      display: none !important;
    }
    .plan-card {
      flex: 0 0 100% !important;
      scroll-snap-align: center !important;
    }
    .reviews-slider {
      display: flex !important;
      flex-wrap: nowrap !important;
      overflow-x: auto !important;
      scroll-snap-type: x mandatory !important;
      gap: 16px !important;
      padding: 16px 0 24px !important;
      margin: 0 !important;
      -webkit-overflow-scrolling: touch !important;
      scrollbar-width: none !important;
      width: 100% !important;
    }
    .reviews-slider::-webkit-scrollbar {
      display: none !important;
    }
    .reviews-slider .feat-card {
      flex: 0 0 100% !important;
      scroll-snap-align: center !important;
      margin-bottom: 0 !important;
    }
    .hero { padding: 32px 0 16px !important; }
    .hero h1 { font-size: 34px !important; margin-bottom: 12px !important; }
    .hero p.lead { font-size: 15px !important; margin-bottom: 16px !important; }
    .nav { padding: 12px 20px !important; }
    .nav-links { display: none !important; }
    .nav-cta { display: none !important; }
    .invoice-mock { margin: 24px auto 0 !important; width: 100% !important; max-width: 380px !important; }
    .cta-banner { flex-direction: column !important; text-align: center !important; gap: 16px !important; padding: 24px !important; }
    .cta-banner h2 { font-size: 20px !important; }
    .sec-head { margin-bottom: 16px !important; text-align: center !important; margin-left: auto !important; margin-right: auto !important; }
    .sec-head h2 { font-size: 24px !important; }
    .illust-img { height: auto !important; }
    .wrap { padding: 0 16px !important; }
    .trust-row { justify-content: center !important; gap: 16px 24px !important; }

    /* Page-level typography overrides in mobile view */
    .page-title { font-size: 30px !important; margin-bottom: 16px !important; }
    .page-lead { font-size: 15px !important; }
    .section-title { font-size: 24px !important; margin-bottom: 16px !important; }
    .grid-2 { grid-template-columns: 1fr !important; gap: 24px !important; }
    
    /* Mobile Menu Drawer Layout */
    .menu-toggle {
      display: flex !important;
      flex-direction: column;
      justify-content: space-between;
      width: 24px;
      height: 18px;
      background: transparent;
      border: none;
      cursor: pointer;
      padding: 0;
      z-index: 120;
      flex: none;
    }
    .menu-toggle span {
      width: 100%;
      height: 2px;
      background: var(--ink);
      transition: all 0.25s ease-in-out;
    }
    .mobile-menu-backdrop {
      display: block;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(23, 35, 63, 0.4);
      backdrop-filter: blur(4px);
      z-index: 99;
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.3s ease-in-out;
    }
    .mobile-menu-backdrop.open {
      pointer-events: auto;
      opacity: 1;
    }
    .mobile-menu {
      display: flex;
      position: fixed;
      top: 0;
      right: 0;
      left: auto;
      width: 80%;
      max-width: 320px;
      height: 100vh;
      background: var(--paper);
      z-index: 100;
      flex-direction: column;
      padding: 80px 24px 24px;
      gap: 20px;
      transform: translateX(100%);
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: -10px 0 30px rgba(23,35,63,.08);
    }
    .mobile-menu.open {
      transform: translateX(0);
    }
    .mobile-menu a {
      font-family: 'Space Grotesk';
      font-size: 20px;
      font-weight: 600;
      color: var(--ink-soft);
      border-bottom: 1px solid var(--border-soft);
      padding-bottom: 12px;
      width: 100%;
    }
    .mobile-menu a:hover {
      color: var(--gold-deep);
    }
    .mobile-menu .mob-cta {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-top: auto;
    }
    .mobile-menu .mob-cta .btn {
      width: 100%;
      justify-content: center;
      padding: 14px !important;
      font-size: 15px !important;
    }
    
    /* Toggle active state */
    .menu-toggle.active span:nth-child(1) {
      transform: translateY(8px) rotate(45deg);
      background: var(--ink);
    }
    .menu-toggle.active span:nth-child(2) {
      opacity: 0;
    }
    .menu-toggle.active span:nth-child(3) {
      transform: translateY(-8px) rotate(-45deg);
      background: var(--ink);
    }
  }
  @media(max-width:600px){
    .plan-card { padding: 20px 16px !important; flex: 0 0 100% !important; scroll-snap-align: center !important; }
    .reviews-slider .feat-card { flex: 0 0 100% !important; scroll-snap-align: center !important; }
    .plan-feats { font-size: 12.8px !important; }
    .feat-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 8px !important; }
    .feat-card { padding: 12px !important; }
    .feat-card h3 { font-size: 13px !important; }
    .feat-card p { font-size: 11.5px !important; line-height: 1.4 !important; }
    .grid-2-cards { grid-template-columns: repeat(2, 1fr) !important; gap: 8px !important; }
    .grid-2-cards .feat-card { padding: 12px !important; }
    .grid-2-cards .feat-card h3 { font-size: 13px !important; }
    .grid-2-cards .feat-card p { font-size: 11.5px !important; line-height: 1.4 !important; }
    .side-cards-list { display: grid !important; grid-template-columns: repeat(2, 1fr) !important; gap: 8px !important; }
    .side-cards-list .feat-card { padding: 12px !important; }
    .side-cards-list .feat-card h3 { font-size: 13px !important; }
    .side-cards-list .feat-card p { font-size: 11.5px !important; line-height: 1.4 !important; }
    .hero-actions { flex-direction: column !important; width: 100% !important; gap: 10px !important; }
    .hero-actions .btn { width: 100% !important; justify-content: center !important; }
    .hero-note { flex-direction: column !important; gap: 6px !important; align-items: center !important; }
  }
</style>
</head>
<body>

  <header class="site">
    <nav class="wrap nav">
      <div class="logo">
        <a href="{{ route('welcome') }}" style="display:flex; align-items:center; gap:9px;">
          <div class="mark"></div>Vyapaargo
        </a>
      </div>
      <div class="nav-links">
        <a href="{{ route('public.features') }}">Features</a>
        <a href="{{ route('public.restaurant') }}">Restaurant</a>
        <a href="{{ route('public.payments') }}">Payments</a>
        <a href="{{ route('public.pricing') }}">Pricing</a>
      </div>
      <div class="nav-cta">
        @auth
            <a class="btn btn-ghost btn-sm" href="{{ route('dashboard') }}">See Dashboard</a>
        @else
            <a class="btn btn-ghost btn-sm" href="{{ route('login') }}">Log in</a>
            <a class="btn btn-gold btn-sm" href="{{ route('register') }}">Start Free</a>
        @endauth
      </div>
      
      <!-- Mobile hamburger toggle -->
      <button class="menu-toggle" id="mob-toggle" aria-label="Toggle Navigation">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </nav>
  </header>

  <!-- Mobile Menu Drawer Container -->
  <div class="mobile-menu" id="mob-drawer">
    <a href="{{ route('public.features') }}">Features</a>
    <a href="{{ route('public.restaurant') }}">Restaurant</a>
    <a href="{{ route('public.payments') }}">Payments</a>
    <a href="{{ route('public.pricing') }}">Pricing</a>
    <div class="mob-cta">
      @auth
          <a class="btn btn-ghost" href="{{ route('dashboard') }}">See Dashboard</a>
      @else
          <a class="btn btn-ghost" href="{{ route('login') }}">Log in</a>
          <a class="btn btn-gold" href="{{ route('register') }}">Start Free Trial</a>
      @endauth
    </div>
  </div>

  <div class="mobile-menu-backdrop" id="mob-backdrop"></div>

  <main>
    @yield('content')
  </main>

  <footer class="site">
    <div class="wrap" style="width: 100%; display: flex; justify-content: space-between;">
      <div>© {{ date('Y') }} {{ config('app.name', 'Vyapaargo') }} · Laravel · MySQL · Razorpay</div>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggle = document.getElementById('mob-toggle');
      const drawer = document.getElementById('mob-drawer');
      const backdrop = document.getElementById('mob-backdrop');
      
      function toggleMenu() {
        toggle.classList.toggle('active');
        drawer.classList.toggle('open');
        backdrop.classList.toggle('open');
        
        if (drawer.classList.contains('open')) {
          document.body.style.overflow = 'hidden';
        } else {
          document.body.style.overflow = '';
        }
      }
      
      if (toggle && drawer && backdrop) {
        toggle.addEventListener('click', toggleMenu);
        backdrop.addEventListener('click', toggleMenu);
        
        // Close menu if links are clicked (useful for same page anchors)
        drawer.querySelectorAll('a').forEach(link => {
          link.addEventListener('click', function() {
            if (drawer.classList.contains('open')) {
              toggleMenu();
            }
          });
        });
      }

      // Slider dots navigation sync
      function setupSliderDots(sliderId, dotsId) {
        const slider = document.getElementById(sliderId);
        const dotsContainer = document.getElementById(dotsId);
        if (slider && dotsContainer) {
          const dots = dotsContainer.querySelectorAll('.dot');
          slider.addEventListener('scroll', function() {
            const index = Math.round(slider.scrollLeft / slider.offsetWidth);
            dots.forEach((dot, idx) => {
              if (idx === index) {
                dot.classList.add('active');
              } else {
                dot.classList.remove('active');
              }
            });
          });
        }
      }
      
      setupSliderDots('plans-slider', 'plans-dots');
      setupSliderDots('reviews-slider', 'reviews-dots');
    });
  </script>

</body>
</html>

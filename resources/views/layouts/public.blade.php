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
    padding: 64px 0 54px;
    background: radial-gradient(circle at 85% 10%, rgba(217, 154, 43, 0.12) 0%, transparent 50%), 
                radial-gradient(circle at 15% 90%, rgba(20, 99, 86, 0.1) 0%, transparent 50%),
                linear-gradient(180deg, rgba(248,250,252,0) 0%, rgba(241,245,249,0.5) 100%),
                var(--bg);
    position: relative;
    overflow: visible;
  }
  .hero-grid{display:grid; grid-template-columns:1.1fr 0.9fr; gap:36px; align-items:center;}
  .eyebrow{
    display:inline-flex; align-items:center; gap:8px; font-family:'IBM Plex Mono'; font-size:12px;
    color:var(--gold-deep); background:#FBF1DD; border:1px solid #EFDDAE; padding:5px 12px; border-radius:100px;
    margin-bottom:14px; letter-spacing:.03em;
  }
  .eyebrow::before{content:'●'; font-size:8px;}
  .hero h1{font-size:52px; line-height:1.08; font-weight:700; margin-bottom:16px;}
  .hero h1 em{font-style:normal; color:var(--teal); position:relative;}
  .hero p.lead{font-size:16px; color:var(--ink-soft); line-height:1.6; max-width:520px; margin-bottom:24px;}
  .hero-actions{display:flex; gap:14px; margin-bottom:24px; flex-wrap:wrap;}
  .hero-note{font-size:13px; color:var(--ink-soft); display:flex; gap:20px; flex-wrap:wrap;}
  .hero-note span{display:flex; align-items:center; gap:6px;}
  .hero-note span::before{content:'✓'; color:var(--teal); font-weight:700;}

  /* invoice mock card */
  .invoice-mock{
    background: #ffffff;
    border-radius: 18px;
    padding: 26px 24px;
    position: relative;
    max-width: 410px;
    margin-left: auto;
    box-shadow: 0 20px 45px -10px rgba(23,35,63,0.12), 0 1px 3px rgba(23,35,63,0.05);
    border: 1.5px solid var(--border-soft);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .invoice-mock:hover{
    transform: translateY(-4px);
    box-shadow: 0 25px 50px -12px rgba(23,35,63,0.18);
  }
  .invoice-mock::before, .invoice-mock::after{
    content:''; position:absolute; width:22px; height:22px; background:var(--bg); border-radius:50%; top:50%; transform:translateY(-50%);
  }
  .invoice-mock::before{left:-11px;} .invoice-mock::after{right:-11px;}
  .invoice-mock .perf{
    position:absolute; left:0; right:0; top:50%; border-top:2px dashed var(--border-soft); transform:translateY(-1px);
  }
  .inv-top{display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;}
  .inv-top .who{font-family:'Space Grotesk'; font-weight:700; font-size:17px; color:var(--ink);}
  .inv-top .num{font-family:'IBM Plex Mono'; font-size:11.5px; color:var(--ink-faint); margin-top:2px;}
  .stamp{
    font-family:'Space Grotesk'; font-weight:800; font-size:12.5px; color:var(--gold); border:2.5px solid var(--gold);
    border-radius:50%; width:58px; height:58px; display:flex; align-items:center; justify-content:center; text-align:center;
    transform:rotate(-12deg); opacity:.95; letter-spacing:.03em;
  }
  .inv-lines{padding:14px 0; border-top:1px solid var(--border-soft); font-size:13.5px;}
  .inv-line{display:flex; justify-content:space-between; padding:5px 0; color:var(--ink-soft);}
  .inv-line span:last-child{font-family:'IBM Plex Mono'; color:var(--ink); font-weight:500;}
  .inv-total{display:flex; justify-content:space-between; padding-top:14px; margin-top:6px; border-top:1.5px solid var(--ink); font-family:'Space Grotesk'; font-weight:700; font-size:17px;}
  .inv-total span:last-child{font-family:'IBM Plex Mono'; color:var(--teal);}
  .inv-bottom{margin-top:18px; padding-top:14px; border-top:2px dashed var(--border-soft); display:flex; justify-content:space-between; align-items:center;}
  .badge{font-family:'IBM Plex Mono'; font-size:10.5px; padding:4px 10px; border-radius:100px; font-weight:700; letter-spacing:.04em;}
  .badge-paid{background:#E6F4F1; color:var(--teal);}

  /* trust strip */
  .trust{padding:14px 0; border-top:1px solid var(--border-soft); border-bottom:1px solid var(--border-soft); background: #FAFBF9;}
  .trust-row{display:flex; justify-content:space-between; flex-wrap:wrap; gap:14px; font-family:'IBM Plex Mono'; font-size:12px; color:var(--ink-soft);}
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
    align-items: center;
    gap: 8px;
    margin-top: 16px;
    padding-bottom: 8px;
  }
  .slider-dots .dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--border);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
  }
  .slider-dots .dot.active {
    background: var(--gold);
    width: 24px;
    border-radius: 6px;
  }
  .plans{display:grid; grid-template-columns:repeat(4,1fr); gap:16px;}
  .addon-plans{display:grid; grid-template-columns:repeat(4,1fr); gap:16px;}
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
  .plan-card{background:#ffffff; border:1px solid var(--border-soft); border-radius:var(--radius); padding:24px 20px; position:relative; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s ease;}
  .plan-card:hover{border-color:var(--gold); box-shadow:0 8px 24px rgba(23,35,63,0.08); transform: translateY(-2px);}
  .plan-card.feat{border:2px solid var(--gold); box-shadow:0 8px 24px rgba(217,154,43,0.12);}
  .plan-tag{position:absolute; top:-12px; right:16px; background:var(--gold); color:var(--ink); font-family:'Space Grotesk', sans-serif; font-size:10px; font-weight:700; padding:4px 10px; border-radius:100px; letter-spacing: 0.04em; white-space: nowrap; box-shadow: 0 2px 6px rgba(0,0,0,0.1);}
  .plan-name{font-family:'Space Grotesk'; font-weight:700; font-size:16px; margin-bottom:6px;}
  .plan-price{font-family:'IBM Plex Mono'; font-size:24px; font-weight:600; margin-bottom:18px;}
  .plan-feats{list-style:none; font-size:13px; color:var(--ink-soft); margin-bottom:20px; padding:0;}
  .plan-feats li{padding:8px 0; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border-soft); font-size:13px; color:var(--ink);}
  .plan-feats li::before{content:none !important; display:none !important;}
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
    box-shadow: 0 10px 30px -5px rgba(23,35,63,0.12);
    transition: transform .3s ease, box-shadow .3s ease;
    object-fit: cover;
    display: block;
  }
  .illust-img:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px -10px rgba(23,35,63,0.18);
  }

  /* Industry Dual Cards */
  .industry-card {
    background: #ffffff;
    border: 1.5px solid var(--border-soft);
    border-radius: 20px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(23,35,63,0.04);
  }
  .industry-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px -10px rgba(23,35,63,0.12);
    border-color: var(--gold);
  }
  .industry-card.restaurant-theme:hover {
    border-color: var(--teal);
  }
  .industry-card-img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 20px;
    border: 1px solid var(--border-soft);
  }
  .industry-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 100px;
    margin-bottom: 12px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }
  .industry-tag.gold {
    background: #FDF4E5;
    color: var(--gold-deep);
    border: 1px solid #F5DEB3;
  }
  .industry-tag.teal {
    background: var(--teal-soft);
    color: var(--teal);
    border: 1px solid #BEE3D8;
  }

  /* Stats Counter Bar */
  .stats-strip {
    background: var(--paper);
    border-top: 1px solid var(--border-soft);
    border-bottom: 1px solid var(--border-soft);
    padding: 20px 0;
  }
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    text-align: center;
  }
  .stat-item h4 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 4px;
  }
  .stat-item p {
    font-size: 12.5px;
    color: var(--ink-faint);
    font-family: 'IBM Plex Mono', monospace;
  }

  @media(max-width: 768px) {
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
    }
  }
  
  /* Desktop defaults for custom toggle elements */
  .menu-toggle { display: none; }
  .mobile-menu { display: none; }
  .mobile-menu-backdrop { display: none; }

  footer.site{padding:44px 0; border-top:1px solid var(--border-soft); display:flex; justify-content:space-between; align-items:center; color:var(--ink-faint); font-size:13px;}

  @media(max-width:900px){
    .slider-dots { display: flex !important; justify-content: center !important; gap: 8px !important; margin-top: 12px !important; }
    .hero-grid, .rest-split, .payment-grid { grid-template-columns: 1fr !important; gap: 24px !important; }
    .feat-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 16px !important; }
    .grid-2-cards { grid-template-columns: repeat(2, 1fr) !important; gap: 16px !important; }
    .side-cards-list { display: grid !important; grid-template-columns: repeat(2, 1fr) !important; gap: 16px !important; }
    .plans, .addon-plans {
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: nowrap !important;
      overflow-x: auto !important;
      scroll-snap-type: x mandatory !important;
      gap: 16px !important;
      padding: 16px 16px 28px !important;
      margin: 0 !important;
      -webkit-overflow-scrolling: touch !important;
      scrollbar-width: none !important;
      width: 100% !important;
      box-sizing: border-box !important;
    }
    .plans::-webkit-scrollbar, .addon-plans::-webkit-scrollbar {
      display: none !important;
    }
    .plan-card, .addon-card {
      flex: 0 0 85% !important;
      min-width: 270px !important;
      max-width: 320px !important;
      scroll-snap-align: center !important;
      box-shadow: 0 4px 20px rgba(23,35,63,.08) !important;
      box-sizing: border-box !important;
    }
    .reviews-slider {
      display: flex !important;
      flex-wrap: nowrap !important;
      overflow-x: auto !important;
      scroll-snap-type: x mandatory !important;
      gap: 16px !important;
      padding: 16px 12px 28px !important;
      margin: 0 !important;
      -webkit-overflow-scrolling: touch !important;
      scrollbar-width: none !important;
      width: 100% !important;
    }
    .reviews-slider::-webkit-scrollbar {
      display: none !important;
    }
    .reviews-slider .feat-card {
      flex: 0 0 85% !important;
      max-width: 320px !important;
      scroll-snap-align: center !important;
      margin-bottom: 0 !important;
    }
    .hero { padding: 32px 0 16px !important; }
    .hero h1 { font-size: 34px !important; margin-bottom: 12px !important; }
    .hero p.lead { font-size: 15px !important; margin-bottom: 16px !important; }
    header.site { min-height: 72px !important; }
    .nav { padding: 18px 20px !important; min-height: 72px !important; display: flex !important; align-items: center !important; }
    .logo { font-size: 20px !important; gap: 10px !important; }
    .logo .mark { width: 28px !important; height: 28px !important; }
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
      width: 28px !important;
      height: 22px !important;
      background: transparent;
      border: none;
      cursor: pointer;
      padding: 0;
      z-index: 120;
      flex: none;
    }
    .menu-toggle span {
      width: 100%;
      height: 2.5px !important;
      background: var(--ink);
      border-radius: 2px;
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
    .stats-grid { grid-template-columns: 1fr !important; gap: 24px !important; }
    .plan-card, .addon-card { 
      padding: 20px 16px !important; 
      flex: 0 0 88% !important; 
      min-width: 260px !important; 
      max-width: 300px !important; 
      scroll-snap-align: center !important; 
      box-sizing: border-box !important;
    }
    .reviews-slider .feat-card { flex: 0 0 100% !important; scroll-snap-align: center !important; }
    .plan-feats { font-size: 12.8px !important; }
    .feat-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 8px !important; }
    .feat-card { padding: 12px !important; }
    .feat-card h3 { font-size: 13px !important; }
    .feat-card p { font-size: 11.5px !important; line-height: 1.4 !important; }
    .grid-2-cards { grid-template-columns: 1fr !important; gap: 24px !important; }
    .grid-2-cards .feat-card { padding: 20px !important; }
    .grid-2-cards .feat-card h3 { font-size: 18px !important; }
    .grid-2-cards .feat-card p { font-size: 14px !important; line-height: 1.5 !important; }
    .inner-feat-list { grid-template-columns: 1fr !important; }
    .side-cards-list { display: grid !important; grid-template-columns: repeat(2, 1fr) !important; gap: 8px !important; }
    .side-cards-list .feat-card { padding: 12px !important; }
    .side-cards-list .feat-card h3 { font-size: 13px !important; }
    .hero-actions { flex-direction: column !important; width: 100% !important; gap: 10px !important; }
    .hero-actions .btn { width: 100% !important; justify-content: center !important; }
    .hero-note { flex-direction: column !important; gap: 6px !important; align-items: center !important; }
  }

  /* Global Professional Footer Styling */
  footer.site-footer {
    background: #0F172A !important;
    color: #F8FAFC !important;
    padding: 60px 0 28px !important;
    border-top: 1px solid #1E293B !important;
    display: block !important;
    width: 100% !important;
    box-sizing: border-box !important;
  }
  .footer-grid {
    display: grid !important;
    grid-template-columns: 1.4fr 1fr 1fr 1fr !important;
    gap: 40px !important;
    margin-bottom: 48px !important;
  }
  .footer-col h4 {
    font-family: 'Space Grotesk', sans-serif !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #FFFFFF !important;
    margin-bottom: 18px !important;
    letter-spacing: 0.02em !important;
  }
  .footer-col ul {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
  }
  .footer-col ul li {
    margin-bottom: 11px !important;
    list-style: none !important;
    list-style-type: none !important;
  }
  .footer-col ul li::before, .footer-col ul li::marker {
    content: "" !important;
    display: none !important;
  }
  .footer-col ul li a {
    color: #94A3B8 !important;
    font-size: 13.5px !important;
    transition: color 0.2s ease !important;
    text-decoration: none !important;
    display: inline-block !important;
  }
  .footer-col ul li a:hover {
    color: var(--gold) !important;
    transform: translateX(2px);
  }
  .footer-bottom {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    border-top: 1px solid #1E293B !important;
    padding-top: 24px !important;
    flex-wrap: wrap !important;
    gap: 16px !important;
  }
  .footer-badges {
    display: flex !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
  }
  .f-badge {
    font-family: 'IBM Plex Mono', monospace !important;
    font-size: 11px !important;
    padding: 5px 12px !important;
    background: rgba(255,255,255,0.06) !important;
    border: 1px solid rgba(255,255,255,0.12) !important;
    color: #94A3B8 !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
  }

  @media(max-width: 900px) {
    .footer-grid {
      grid-template-columns: 1fr 1fr !important;
      gap: 30px !important;
    }
  }
  @media(max-width: 600px) {
    .footer-grid {
      grid-template-columns: 1fr !important;
      gap: 28px !important;
    }
    .footer-bottom {
      flex-direction: column !important;
      align-items: flex-start !important;
    }
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
            <a class="btn btn-gold btn-sm" href="{{ route('public.pricing') }}">Get Started</a>
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
          <a class="btn btn-gold" href="{{ route('public.pricing') }}">Get Started</a>
      @endauth
    </div>
  </div>

  <div class="mobile-menu-backdrop" id="mob-backdrop"></div>

  <main>
    @yield('content')
  </main>

  <footer class="site-footer" style="background: #0B132B; color: #F8FAFC; padding: 64px 0 28px; border-top: 1px solid #1E293B; display: block; width: 100%;">
    <div class="wrap">
      <div class="footer-grid" style="grid-template-columns: 1.3fr 0.9fr 0.9fr 1.2fr; gap: 36px; margin-bottom: 44px;">
        <!-- Col 1: Brand Info & Social Media Links -->
        <div class="footer-brand">
          <div class="logo" style="margin-bottom: 14px;">
            <a href="{{ route('welcome') }}" style="display:flex; align-items:center; gap:9px; color: #fff; text-decoration: none;">
              <div class="mark" style="background: #fff; position: relative;"><span style="position: absolute; left: 6px; right: 6px; top: 7px; height: 2px; background: var(--gold); box-shadow: 0 5px 0 var(--gold), 0 10px 0 var(--gold); display: block;"></span></div>
              <span style="color: #fff; font-size: 20px; font-weight: 700; font-family: 'Space Grotesk';">{{ $siteSettings['company_name'] ?? config('app.name', 'Vyapaargo') }}</span>
            </a>
          </div>
          <p style="color: #94A3B8; font-size: 13.5px; line-height: 1.6; margin-bottom: 20px; max-width: 320px;">
            {{ $siteSettings['company_tagline'] ?? 'The unified cloud business & restaurant ERP platform designed specifically for Indian SMEs, retail shops, and food joints.' }}
          </p>

          <!-- Dynamic Social Media Icons -->
          <div style="margin-bottom: 20px;">
            <div style="font-size: 11px; font-family: 'IBM Plex Mono'; color: #64748B; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 10px;">Connect With Us</div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
              @if(!empty($siteSettings['social_whatsapp']))
                <a href="{{ $siteSettings['social_whatsapp'] }}" target="_blank" rel="noopener noreferrer" title="Chat on WhatsApp" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; color: #25D366; transition: 0.2s; border: 1px solid rgba(255,255,255,0.1);">
                  <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                </a>
              @endif
              @if(!empty($siteSettings['social_linkedin']))
                <a href="{{ $siteSettings['social_linkedin'] }}" target="_blank" rel="noopener noreferrer" title="Follow on LinkedIn" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; color: #38BDF8; transition: 0.2s; border: 1px solid rgba(255,255,255,0.1);">
                  <svg width="17" height="17" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
              @endif
              @if(!empty($siteSettings['social_twitter']))
                <a href="{{ $siteSettings['social_twitter'] }}" target="_blank" rel="noopener noreferrer" title="Follow on X / Twitter" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; color: #E2E8F0; transition: 0.2s; border: 1px solid rgba(255,255,255,0.1);">
                  <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
              @endif
              @if(!empty($siteSettings['social_instagram']))
                <a href="{{ $siteSettings['social_instagram'] }}" target="_blank" rel="noopener noreferrer" title="Follow on Instagram" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; color: #F472B6; transition: 0.2s; border: 1px solid rgba(255,255,255,0.1);">
                  <svg width="17" height="17" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
              @endif
              @if(!empty($siteSettings['social_facebook']))
                <a href="{{ $siteSettings['social_facebook'] }}" target="_blank" rel="noopener noreferrer" title="Facebook Page" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; color: #60A5FA; transition: 0.2s; border: 1px solid rgba(255,255,255,0.1);">
                  <svg width="17" height="17" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                </a>
              @endif
              @if(!empty($siteSettings['social_youtube']))
                <a href="{{ $siteSettings['social_youtube'] }}" target="_blank" rel="noopener noreferrer" title="YouTube Channel" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; color: #EF4444; transition: 0.2s; border: 1px solid rgba(255,255,255,0.1);">
                  <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                </a>
              @endif
            </div>
          </div>

          <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 11.5px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); padding: 5px 12px; border-radius: 100px; color: #E2E8F0; font-family: 'IBM Plex Mono';">
            <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
            All Systems Operational • 99.9% Uptime
          </div>
        </div>

        <!-- Col 2: Core Platform Modules -->
        <div class="footer-col">
          <h4 style="font-family: 'Space Grotesk', sans-serif; font-size: 15px; font-weight: 700; color: #FFFFFF; margin-bottom: 18px; letter-spacing: 0.02em;">Core Modules</h4>
          <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('public.features') }}">GST Invoicing &amp; Billing</a></li>
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('public.features') }}">Barcode &amp; Inventory</a></li>
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('public.restaurant') }}">Restaurant POS &amp; KDS</a></li>
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('public.restaurant') }}">Table QR Digital Menus</a></li>
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('public.payments') }}">Razorpay &amp; UPI Auto-Pay</a></li>
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('public.features') }}">Staff Attendance &amp; Payroll</a></li>
          </ul>
        </div>

        <!-- Col 3: Legal & Platform Navigation -->
        <div class="footer-col">
          <h4 style="font-family: 'Space Grotesk', sans-serif; font-size: 15px; font-weight: 700; color: #FFFFFF; margin-bottom: 18px; letter-spacing: 0.02em;">Legal &amp; Company</h4>
          <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('public.pricing') }}">Pricing &amp; Plans</a></li>
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('public.privacy') }}">Privacy Policy</a></li>
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('public.terms') }}">Terms &amp; Conditions</a></li>
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('public.pricing') }}#faq">Frequently Asked Questions</a></li>
            <li style="margin-bottom: 11px; list-style: none;"><a href="{{ route('login') }}">Merchant Login</a></li>
            @php
              $trialDays = (int)\App\Models\SystemSetting::get('trial_days', 14);
              $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1') === '1';
            @endphp
            <li style="margin-bottom: 11px; list-style: none;">
              @if($enableTrial && $trialDays > 0)
              <a href="{{ route('public.pricing') }}">Start {{ $trialDays }}-Day Free Trial</a>
              @else
              <a href="{{ route('public.pricing') }}">Get Started</a>
              @endif
            </li>
          </ul>
        </div>

        <!-- Col 4: Dynamic Helpdesk & Physical Address -->
        <div class="footer-col">
          <h4 style="font-family: 'Space Grotesk', sans-serif; font-size: 15px; font-weight: 700; color: #FFFFFF; margin-bottom: 18px; letter-spacing: 0.02em;">Support &amp; Office</h4>
          
          <div style="font-size: 13px; color: #94A3B8; line-height: 1.6; margin-bottom: 12px;">
            <div style="color: #64748B; font-size: 11px; font-family: 'IBM Plex Mono'; text-transform: uppercase;">Direct Helpdesk</div>
            <div style="margin-top: 3px;">
              📞 <a href="tel:{{ $siteSettings['support_phone'] ?? '+919876543210' }}" style="color: #F8FAFC; font-weight: 600; text-decoration: none;">{{ $siteSettings['support_phone'] ?? '+91 98765 43210' }}</a>
            </div>
            <div style="margin-top: 2px;">
              ✉️ <a href="mailto:{{ $siteSettings['support_email'] ?? 'support@vyapaargo.com' }}" style="color: #94A3B8; text-decoration: none;">{{ $siteSettings['support_email'] ?? 'support@vyapaargo.com' }}</a>
            </div>
          </div>

          <div style="font-size: 13px; color: #94A3B8; line-height: 1.5; margin-bottom: 12px;">
            <div style="color: #64748B; font-size: 11px; font-family: 'IBM Plex Mono'; text-transform: uppercase;">Corporate Address</div>
            <div style="color: #CBD5E1; margin-top: 3px; font-size: 12.5px;">
              📍 {{ $siteSettings['company_address'] ?? 'Plot No. 42, Cyber City, Phase 2, Gurugram, Haryana - 122002, India' }}
            </div>
          </div>

          <div style="font-size: 12px; color: #64748B; font-family: 'IBM Plex Mono';">
            ⏰ {{ $siteSettings['business_hours'] ?? 'Mon - Sat: 9:00 AM - 7:00 PM IST' }}
          </div>
        </div>
      </div>

      <!-- Footer Bottom Bar -->
      <div class="footer-bottom">
        <div style="font-size: 13px; color: #64748B;">
          © {{ date('Y') }} {{ $siteSettings['company_name'] ?? config('app.name', 'Vyapaargo') }}. Made with ❤️ in India for growing businesses.
        </div>
        <div class="footer-badges">
          <span class="f-badge">GST Ready</span>
          <span class="f-badge">UPI &amp; Razorpay Verified</span>
          <span class="f-badge">256-Bit SSL Secure</span>
        </div>
      </div>
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

      // Dynamic & Clickable Slider dots navigation sync
      function setupSliderDots(sliderId, dotsId) {
        const slider = document.getElementById(sliderId);
        const dotsContainer = document.getElementById(dotsId);
        if (slider && dotsContainer) {
          const cards = Array.from(slider.children).filter(el => el.classList.contains('plan-card') || el.classList.contains('feat-card'));
          if (cards.length > 0) {
            dotsContainer.innerHTML = '';
            cards.forEach((card, i) => {
              const dot = document.createElement('span');
              dot.className = 'dot' + (i === 0 ? ' active' : '');
              dot.style.cursor = 'pointer';
              dot.setAttribute('title', 'Slide to item ' + (i + 1));
              dot.addEventListener('click', function(e) {
                e.preventDefault();
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
              });
              dotsContainer.appendChild(dot);
            });

            const dots = dotsContainer.querySelectorAll('.dot');
            
            let isScrolling;
            slider.addEventListener('scroll', function() {
              window.clearTimeout(isScrolling);
              isScrolling = setTimeout(function() {
                const sliderCenter = slider.scrollLeft + (slider.offsetWidth / 2);
                let closestIndex = 0;
                let minDistance = Infinity;

                cards.forEach((card, idx) => {
                  const cardCenter = card.offsetLeft - slider.offsetLeft + (card.offsetWidth / 2);
                  const distance = Math.abs(sliderCenter - cardCenter);
                  if (distance < minDistance) {
                    minDistance = distance;
                    closestIndex = idx;
                  }
                });

                dots.forEach((dot, idx) => {
                  if (idx === closestIndex) {
                    dot.classList.add('active');
                  } else {
                    dot.classList.remove('active');
                  }
                });
              }, 40);
            }, { passive: true });
          }
        }
      }
      
      function initAllSliders() {
        setupSliderDots('plans-slider', 'plans-dots');
        setupSliderDots('plans-business-slider', 'plans-business-dots');
        setupSliderDots('plans-restaurant-slider', 'plans-restaurant-dots');
        setupSliderDots('plans-addon-slider', 'plans-addon-dots');
        setupSliderDots('reviews-slider', 'reviews-dots');
      }

      window.reinitSliders = initAllSliders;
      initAllSliders();
    });
  </script>

</body>
</html>

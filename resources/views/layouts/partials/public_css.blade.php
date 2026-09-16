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
  .eyebrow::before{content:'\25CF\0020'; font-size:8px;}
  .hero h1{font-size:52px; line-height:1.08; font-weight:700; margin-bottom:16px;}
  .hero h1 em{font-style:normal; color:var(--teal); position:relative;}
  .hero p.lead{font-size:16px; color:var(--ink-soft); line-height:1.6; max-width:520px; margin-bottom:24px;}
  .hero-actions{display:flex; gap:14px; margin-bottom:24px; flex-wrap:wrap;}
  .hero-note{font-size:13px; color:var(--ink-soft); display:flex; gap:20px; flex-wrap:wrap;}
  .hero-note span{display:flex; align-items:center; gap:6px;}
  .hero-note span::before{content:'\2713\0020'; color:var(--teal); font-weight:700;}

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

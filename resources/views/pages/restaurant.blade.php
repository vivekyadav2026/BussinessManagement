@extends('layouts.public')

@section('content')
<section class="hero" style="padding-bottom: 20px;">
    <div class="wrap text-center" style="text-align: center; max-width: 700px; margin: 0 auto;">
        <div class="eyebrow">Restaurant POS</div>
        <h1 style="font-size: 42px; margin-bottom: 24px;">Built for modern <br><em>cafes & restaurants</em>.</h1>
        <p class="lead" style="margin: 0 auto;">Manage tables, send orders directly to the kitchen, and let customers order via QR codes seamlessly.</p>
    </div>
</section>

<section>
    <div class="wrap">
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
@endsection

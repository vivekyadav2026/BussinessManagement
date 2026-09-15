@extends('layouts.public')

@section('content')
<style>
  .legal-content h2 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: var(--ink);
    margin-top: 32px;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border-soft);
  }
  .legal-content h3 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 16px;
    font-weight: 600;
    color: var(--ink);
    margin-top: 20px;
    margin-bottom: 8px;
  }
  .legal-content p {
    font-size: 14.5px;
    color: var(--ink-soft);
    line-height: 1.7;
    margin-bottom: 16px;
  }
  .legal-content ul {
    margin-bottom: 20px;
    padding-left: 24px;
  }
  .legal-content ul li {
    font-size: 14.5px;
    color: var(--ink-soft);
    line-height: 1.65;
    margin-bottom: 8px;
  }
  .legal-card {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 20px;
    padding: 44px;
    box-shadow: 0 10px 35px -5px rgba(23,35,63,0.06);
  }
  @media(max-width: 768px) {
    .legal-card {
      padding: 24px 18px;
    }
  }
</style>

<section class="hero" style="padding-bottom: 28px;">
  <div class="wrap" style="max-width: 860px; text-align: center; margin: 0 auto;">
    <div class="eyebrow" style="background: #FDF4E5; color: var(--gold-deep); border-color: #F5DEB3;">
      TERMS OF SERVICE
    </div>
    <h1 class="page-title" style="margin-bottom: 14px;">Terms &amp; Conditions</h1>
    <p class="page-lead" style="max-width: 620px;">
      Terms governing your use of {{ $siteSettings['company_name'] ?? config('app.name', 'Vyapaargo') }} ERP and POS cloud platforms.
    </p>
    <div style="display: inline-flex; align-items: center; gap: 8px; font-family: 'IBM Plex Mono'; font-size: 12px; color: var(--ink-faint); margin-top: 16px;">
      <span>Effective Date: {{ date('F Y') }}</span>
      <span>•</span>
      <span>Governed by Indian Law</span>
    </div>
  </div>
</section>

<section style="padding: 20px 0 80px;">
  <div class="wrap" style="max-width: 860px; margin: 0 auto;">
    <div class="legal-card">
      <div class="legal-content">
        {!! $terms !!}
      </div>

      <div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
          <h4 style="font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 4px;">Need customized Enterprise SLA terms?</h4>
          <p style="font-size: 13px; color: var(--ink-soft); margin: 0;">Speak directly with our legal and enterprise solutions desk.</p>
        </div>
        <a href="mailto:{{ $siteSettings['support_email'] ?? 'support@vyapaargo.com' }}" class="btn btn-gold">
          Contact Enterprise Desk &rarr;
        </a>
      </div>
    </div>
  </div>
</section>
@endsection

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
          All Systems Operational &bull; 99.9% Uptime
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
          <div style="margin-top: 4px; display: flex; align-items: center; gap: 8px;">
            <svg width="14" height="14" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <a href="tel:{{ $siteSettings['support_phone'] ?? '+919876543210' }}" style="color: #F8FAFC; font-weight: 600; text-decoration: none;">{{ $siteSettings['support_phone'] ?? '+91 98765 43210' }}</a>
          </div>
          <div style="margin-top: 4px; display: flex; align-items: center; gap: 8px;">
            <svg width="14" height="14" fill="none" stroke="#38BDF8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <a href="mailto:{{ $siteSettings['support_email'] ?? 'support@vyapaargo.com' }}" style="color: #94A3B8; text-decoration: none;">{{ $siteSettings['support_email'] ?? 'support@vyapaargo.com' }}</a>
          </div>
        </div>

        <div style="font-size: 13px; color: #94A3B8; line-height: 1.5; margin-bottom: 12px;">
          <div style="color: #64748B; font-size: 11px; font-family: 'IBM Plex Mono'; text-transform: uppercase;">Corporate Address</div>
          <div style="color: #CBD5E1; margin-top: 4px; font-size: 12.5px; display: flex; align-items: flex-start; gap: 8px;">
            <svg width="15" height="15" fill="none" stroke="#F59E0B" viewBox="0 0 24 24" style="flex-shrink:0; margin-top: 2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>{{ $siteSettings['company_address'] ?? 'Plot No. 42, Cyber City, Phase 2, Gurugram, Haryana - 122002, India' }}</span>
          </div>
        </div>

        <div style="font-size: 12px; color: #64748B; font-family: 'IBM Plex Mono'; display: flex; align-items: center; gap: 8px;">
          <svg width="13" height="13" fill="none" stroke="#94A3B8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span>{{ $siteSettings['business_hours'] ?? 'Mon - Sat: 9:00 AM - 7:00 PM IST' }}</span>
        </div>
      </div>
    </div>

    <!-- Footer Bottom Bar -->
    <div class="footer-bottom">
      <div style="font-size: 13px; color: #64748B;">
        &copy; {{ date('Y') }} {{ $siteSettings['company_name'] ?? config('app.name', 'Vyapaargo') }}. Made with <span style="color:#EF4444;">&hearts;</span> in India for growing businesses.
      </div>
      <div class="footer-badges">
        <span class="f-badge">GST Ready</span>
        <span class="f-badge">UPI &amp; Razorpay Verified</span>
        <span class="f-badge">256-Bit SSL Secure</span>
      </div>
    </div>
  </div>
</footer>
<div>
@php
$colors = ['#2563eb','#059669','#7c3aed','#dc2626','#d97706','#0891b2'];
$lightBg = ['#eff6ff','#f0fdf4','#faf5ff','#fff1f2','#fffbeb','#ecfeff'];

// ── Hero Settings ──────────────────────────────────────────────────────────
$siteName    = \App\Models\Setting::get('brand.site_name', config('app.name'));
$heroBadge   = \App\Models\Setting::get('hero.badge_text',      'Open Access · Peer Reviewed · DOI Crossref');
$heroLine1   = \App\Models\Setting::get('hero.title_line1',     'Publish Your Research');
$heroLine2   = \App\Models\Setting::get('hero.title_line2',     'With ' . $siteName);
$heroSub     = \App\Models\Setting::get('hero.subtitle',        'A scientific journal management platform — from submission and double-blind peer review to publication indexed in Crossref and Google Scholar.');
$heroCta1    = \App\Models\Setting::get('hero.cta1_text',       'Explore Journals');
$heroCta2G   = \App\Models\Setting::get('hero.cta2_guest_text', 'Register Free');
$heroCta2A   = \App\Models\Setting::get('hero.cta2_auth_text',  'Submit Manuscript');
$statLbl1    = \App\Models\Setting::get('hero.stat_journals_label', 'Active Journals');
$statLbl2    = \App\Models\Setting::get('hero.stat_articles_label', 'Total Articles');
$statLbl3    = \App\Models\Setting::get('hero.stat_authors_label',  'Researchers');
$b1Title     = \App\Models\Setting::get('hero.badge1_title',    'Crossref DOI');
$b1Sub       = \App\Models\Setting::get('hero.badge1_subtitle', '10.xxxx/gjs.2026');
$b2Title     = \App\Models\Setting::get('hero.badge2_title',    'Google Scholar');
$b2Sub       = \App\Models\Setting::get('hero.badge2_subtitle', 'Auto-indexed');
$b3Title     = \App\Models\Setting::get('hero.badge3_title',    'Open Access');
$b3Sub       = \App\Models\Setting::get('hero.badge3_subtitle', 'Free access forever');
$b4Title     = \App\Models\Setting::get('hero.badge4_title',    'Peer Reviewed');
$b4Sub       = \App\Models\Setting::get('hero.badge4_subtitle', 'Double-blind review');
$b5Title     = \App\Models\Setting::get('hero.badge5_title',    'SINTA');
$b5Sub       = \App\Models\Setting::get('hero.badge5_subtitle', 'Kemendikbud');
$trustLabel  = \App\Models\Setting::get('hero.trust_bar_label', 'Indexed &amp; Listed in');
@endphp

{{-- ═══ HERO ═══ --}}
<section style="background:linear-gradient(135deg,#060d1f 0%,#0d1f4a 45%,#0a1628 100%);position:relative;overflow:hidden;min-height:90vh;display:flex;align-items:center;">

    {{-- Grid pattern overlay --}}
    <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(59,130,246,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.06) 1px,transparent 1px);background-size:48px 48px;pointer-events:none;"></div>

    {{-- Glow blobs --}}
    <div style="position:absolute;top:-10rem;left:-6rem;width:38rem;height:38rem;border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,.12) 0%,transparent 65%);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-8rem;right:-4rem;width:30rem;height:30rem;border-radius:50%;background:radial-gradient(circle,rgba(99,102,241,.1) 0%,transparent 65%);pointer-events:none;"></div>
    <div style="position:absolute;top:40%;left:55%;width:20rem;height:20rem;border-radius:50%;background:radial-gradient(circle,rgba(52,211,153,.07) 0%,transparent 65%);pointer-events:none;"></div>

    <div class="ji-hero-grid" style="position:relative;max-width:72rem;margin:0 auto;padding:5rem 1.5rem 4rem;display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;">

        {{-- LEFT: Text --}}
        <div>
            {{-- Badge --}}
            <div style="display:inline-flex;align-items:center;gap:.5rem;margin-bottom:1.75rem;padding:.375rem 1rem;border-radius:9999px;background:rgba(59,130,246,.15);border:1px solid rgba(59,130,246,.3);font-size:.75rem;font-weight:700;color:#93c5fd;">
                <span style="width:.5rem;height:.5rem;border-radius:50%;background:#34d399;flex-shrink:0;animation:pulse 2s infinite;"></span>
                {{ $heroBadge }}
            </div>

            <h1 style="font-size:clamp(2rem,4vw,3.25rem);font-weight:900;color:#fff;line-height:1.15;margin:0 0 1.25rem;letter-spacing:-.02em;">
                {{ $heroLine1 }}<br>
                <span style="background:linear-gradient(90deg,#60a5fa 0%,#a78bfa 50%,#34d399 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                    {{ $heroLine2 }}
                </span>
            </h1>

            <p style="font-size:1.0625rem;color:#94a3b8;line-height:1.8;margin:0 0 2rem;max-width:480px;">
                {{ $heroSub }}
            </p>

            <div class="ji-hero-btns" style="display:flex;flex-wrap:wrap;gap:.75rem;margin-bottom:2.5rem;">
                <a href="#journals"
                   style="display:inline-flex;align-items:center;gap:.5rem;padding:.875rem 1.75rem;background:linear-gradient(135deg,#2563eb,#4f46e5);color:#fff;font-size:.9375rem;font-weight:700;border-radius:.75rem;text-decoration:none;box-shadow:0 8px 24px rgba(37,99,235,.35);">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    {{ $heroCta1 }}
                </a>
                @guest
                <a href="{{ route('register') }}"
                   style="display:inline-flex;align-items:center;gap:.5rem;padding:.875rem 1.75rem;background:rgba(255,255,255,.08);color:#e2e8f0;font-size:.9375rem;font-weight:600;border-radius:.75rem;border:1px solid rgba(255,255,255,.18);text-decoration:none;">
                    {{ $heroCta2G }}
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                @else
                <a href="{{ route('submit') }}"
                   style="display:inline-flex;align-items:center;gap:.5rem;padding:.875rem 1.75rem;background:rgba(255,255,255,.08);color:#e2e8f0;font-size:.9375rem;font-weight:600;border-radius:.75rem;border:1px solid rgba(255,255,255,.18);text-decoration:none;">
                    {{ $heroCta2A }} →
                </a>
                @endguest
            </div>

            {{-- Stats row --}}
            <div style="display:flex;flex-wrap:wrap;gap:1.75rem;padding-top:1.75rem;border-top:1px solid rgba(255,255,255,.08);">
                @foreach([
                    ['n'=>$stats['journals'],  'l'=>$statLbl1, 'c'=>'#60a5fa'],
                    ['n'=>$stats['articles'],  'l'=>$statLbl2, 'c'=>'#34d399'],
                    ['n'=>$stats['authors'],   'l'=>$statLbl3, 'c'=>'#fb923c'],
                ] as $s)
                <div>
                    <div style="font-size:1.875rem;font-weight:900;color:{{ $s['c'] }};line-height:1;letter-spacing:-.02em;">{{ number_format($s['n']) }}</div>
                    <div style="font-size:.7rem;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-top:.2rem;">{{ $s['l'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT: Visual --}}
        <div class="ji-hero-right" style="position:relative;display:flex;align-items:center;justify-content:center;min-height:380px;">

            {{-- Glow center --}}
            <div style="position:absolute;inset:0;background:radial-gradient(ellipse at center,rgba(99,102,241,.18) 0%,transparent 70%);pointer-events:none;border-radius:50%;"></div>

            {{-- Decorative rings --}}
            <div style="position:absolute;width:22rem;height:22rem;border-radius:50%;border:1px solid rgba(99,102,241,.15);"></div>
            <div style="position:absolute;width:16rem;height:16rem;border-radius:50%;border:1px solid rgba(59,130,246,.2);"></div>
            <div style="position:absolute;width:10rem;height:10rem;border-radius:50%;border:1px solid rgba(52,211,153,.2);"></div>

            {{-- Center icon --}}
            <div style="position:relative;z-index:2;width:5rem;height:5rem;border-radius:50%;background:linear-gradient(135deg,#2563eb,#4f46e5);display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 8px rgba(37,99,235,.15),0 0 40px rgba(37,99,235,.3);">
                <svg style="width:2.25rem;height:2.25rem;color:#fff;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>

            {{-- Floating badge 1: DOI (kiri atas) --}}
            <div style="position:absolute;top:10%;left:5%;z-index:3;background:rgba(15,23,42,.85);border:1px solid rgba(248,114,114,.3);border-radius:.875rem;padding:.625rem 1rem;backdrop-filter:blur(12px);box-shadow:0 8px 24px rgba(0,0,0,.3);">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div style="width:1.75rem;height:1.75rem;border-radius:.5rem;background:linear-gradient(135deg,#f06014,#e05308);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:.875rem;height:.875rem;color:#fff;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:800;color:#fff;">{{ $b1Title }}</div>
                        <div style="font-size:.62rem;color:#94a3b8;">{{ $b1Sub }}</div>
                    </div>
                </div>
            </div>

            {{-- Floating badge 2: Google Scholar (kanan atas) --}}
            <div style="position:absolute;top:8%;right:2%;z-index:3;background:rgba(15,23,42,.85);border:1px solid rgba(96,165,250,.3);border-radius:.875rem;padding:.625rem 1rem;backdrop-filter:blur(12px);box-shadow:0 8px 24px rgba(0,0,0,.3);">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div style="width:1.75rem;height:1.75rem;border-radius:.5rem;background:linear-gradient(135deg,#4285f4,#1a73e8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:.875rem;height:.875rem;color:#fff;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4L22 9L12 14L2 9L12 4Z"/><path d="M4 10.5V18"/><circle cx="4" cy="19" r="1.2" fill="currentColor" stroke="none"/><path d="M7 12V16.5Q9.5 19.5 12 19.5Q14.5 19.5 17 16.5V12"/></svg>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:800;color:#fff;">{{ $b2Title }}</div>
                        <div style="font-size:.62rem;color:#60a5fa;">{{ $b2Sub }}</div>
                    </div>
                </div>
            </div>

            {{-- Floating badge 3: Open Access (kiri bawah) --}}
            <div style="position:absolute;bottom:12%;left:2%;z-index:3;background:rgba(15,23,42,.85);border:1px solid rgba(52,211,153,.3);border-radius:.875rem;padding:.625rem 1rem;backdrop-filter:blur(12px);box-shadow:0 8px 24px rgba(0,0,0,.3);">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div style="width:1.75rem;height:1.75rem;border-radius:50%;background:linear-gradient(135deg,#059669,#047857);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:.875rem;height:.875rem;color:#fff;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:800;color:#34d399;">{{ $b3Title }}</div>
                        <div style="font-size:.62rem;color:#94a3b8;">{{ $b3Sub }}</div>
                    </div>
                </div>
            </div>

            {{-- Floating badge 4: Peer Reviewed (kanan bawah) --}}
            <div style="position:absolute;bottom:10%;right:3%;z-index:3;background:rgba(15,23,42,.85);border:1px solid rgba(167,139,250,.3);border-radius:.875rem;padding:.625rem 1rem;backdrop-filter:blur(12px);box-shadow:0 8px 24px rgba(0,0,0,.3);">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div style="width:1.75rem;height:1.75rem;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#6d28d9);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:.875rem;height:.875rem;color:#fff;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:800;color:#a78bfa;">{{ $b4Title }}</div>
                        <div style="font-size:.62rem;color:#94a3b8;">{{ $b4Sub }}</div>
                    </div>
                </div>
            </div>

            {{-- Floating badge 5: SINTA (tengah kiri) --}}
            <div style="position:absolute;top:50%;left:-4%;transform:translateY(-50%);z-index:3;background:rgba(15,23,42,.85);border:1px solid rgba(251,146,60,.3);border-radius:.875rem;padding:.5rem .875rem;backdrop-filter:blur(12px);box-shadow:0 8px 24px rgba(0,0,0,.3);">
                <div style="font-size:.7rem;font-weight:800;color:#fb923c;">{{ $b5Title }}</div>
                <div style="font-size:.6rem;color:#94a3b8;margin-top:.1rem;">{{ $b5Sub }}</div>
            </div>

        </div>
    </div>

    {{-- Scroll hint --}}
    <div style="position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:.375rem;opacity:.4;">
        <div style="font-size:.65rem;color:#94a3b8;letter-spacing:.1em;text-transform:uppercase;">Scroll</div>
        <svg style="width:1rem;height:1rem;color:#94a3b8;animation:bounceDown 1.5s ease-in-out infinite;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </div>
</section>

{{-- ═══ TRUST BAR ═══ --}}
<div style="background:#fff;border-bottom:1px solid #e8edf5;padding:.875rem 1.5rem;">
    <div class="ji-trust-bar" style="max-width:72rem;margin:0 auto;display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:.5rem 1.25rem;">
        <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#94a3b8;margin-right:.5rem;">{!! $trustLabel !!}</span>
        @foreach([
            ['Google Scholar', '#4285f4'],
            ['Crossref DOI',   '#f06014'],
            ['OAI-PMH 2.0',   '#e8a000'],
            ['DOAJ Ready',    '#3dbea3'],
            ['PKP Index',     '#c0392b'],
            ['SINTA Kemendikbud', '#1a56db'],
        ] as [$name, $color])
        <span style="display:inline-flex;align-items:center;gap:.375rem;font-size:.78rem;font-weight:700;padding:.3rem .875rem;border-radius:9999px;background:{{ $color }}12;border:1px solid {{ $color }}28;color:{{ $color }};">
            <span style="width:.375rem;height:.375rem;border-radius:50%;background:{{ $color }};flex-shrink:0;"></span>
            {{ $name }}
        </span>
        @endforeach
    </div>
</div>

{{-- ═══ ALUR PENERBITAN ═══ --}}
<section style="background:linear-gradient(180deg,#f8fafc 0%,#fff 100%);padding:5rem 1.5rem;">
    <div style="max-width:72rem;margin:0 auto;">
        <div style="text-align:center;margin-bottom:3rem;">
            <span style="display:inline-block;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#2563eb;margin-bottom:.625rem;">How Does It Work?</span>
            <h2 style="font-size:1.875rem;font-weight:900;color:#0f172a;margin:0 0 .75rem;letter-spacing:-.02em;">Article Publication Workflow</h2>
            <p style="font-size:.9375rem;color:#64748b;max-width:460px;margin:0 auto;line-height:1.7;">From manuscript to indexed publication, all in one integrated platform.</p>
        </div>

        <div class="ji-workflow-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;position:relative;">
            {{-- connector line --}}
            <div class="ji-workflow-connector" style="position:absolute;top:2.5rem;left:calc(12.5% + 1.25rem);right:calc(12.5% + 1.25rem);height:2px;background:linear-gradient(90deg,#2563eb,#7c3aed,#059669,#f59e0b);border-radius:9999px;opacity:.3;"></div>

            @foreach([
                ['01', 'Submit Manuscript',  'Upload your article, fill in metadata, and select the journal matching your research field.', '#2563eb', '#eff6ff', 'M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5'],
                ['02', 'Peer Review',        'Editor assigns reviewers. The double-blind review process ensures objectivity.', '#7c3aed', '#faf5ff', 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'],
                ['03', 'Copyediting',        'Editorial team performs language editing, layout, and PDF/HTML galley creation.', '#059669', '#f0fdf4', 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z'],
                ['04', 'Publication &amp; DOI', 'Article is published and receives Crossref DOI. Automatically indexed in Google Scholar &amp; DOAJ.', '#d97706', '#fffbeb', 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253'],
            ] as [$num, $title, $desc, $color, $bg, $icon])
            <div style="position:relative;text-align:center;">
                {{-- Step number circle --}}
                <div style="width:5rem;height:5rem;border-radius:50%;background:{{ $bg }};border:2px solid {{ $color }}30;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;position:relative;z-index:1;box-shadow:0 4px 16px {{ $color }}20;">
                    <svg style="width:1.75rem;height:1.75rem;color:{{ $color }};" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                    </svg>
                    <div style="position:absolute;top:-.5rem;right:-.5rem;width:1.5rem;height:1.5rem;border-radius:50%;background:{{ $color }};display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:900;color:#fff;box-shadow:0 2px 6px {{ $color }}50;">{{ $num }}</div>
                </div>
                <h3 style="font-size:.9375rem;font-weight:800;color:#0f172a;margin:0 0 .5rem;">{{ $title }}</h3>
                <p style="font-size:.8rem;color:#64748b;line-height:1.65;">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
@keyframes bounceDown { 0%,100%{transform:translateY(0)} 50%{transform:translateY(6px)} }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

/* ── Hero ──────────────────────────────────────────────────────── */
@media(max-width:900px){
    .ji-hero-grid { grid-template-columns:1fr !important; padding:3.5rem 1.25rem 3rem !important; }
    .ji-hero-right { display:none !important; }
}
@media(max-width:640px){
    .ji-hero-grid { padding:2.75rem 1.25rem 2.5rem !important; gap:1.5rem !important; }
}
@media(max-width:480px){
    .ji-hero-btns { flex-direction:column !important; }
    .ji-hero-btns a { width:100% !important; justify-content:center !important; box-sizing:border-box; }
}

/* ── Trust bar — horizontal scroll on mobile ───────────────────── */
@media(max-width:640px){
    .ji-trust-bar { flex-wrap:nowrap !important; overflow-x:auto; justify-content:flex-start !important; padding:.875rem 1.25rem; scrollbar-width:none; -ms-overflow-style:none; }
    .ji-trust-bar::-webkit-scrollbar { display:none; }
    .ji-trust-bar > span:first-child { flex-shrink:0; }
    .ji-trust-bar > span { flex-shrink:0; }
}

/* ── Workflow ───────────────────────────────────────────────────── */
.ji-workflow-connector { }
@media(max-width:900px){
    .ji-workflow-grid { grid-template-columns:repeat(2,1fr) !important; gap:1.25rem !important; }
    .ji-workflow-connector { display:none !important; }
}
@media(max-width:400px){
    .ji-workflow-grid { grid-template-columns:1fr !important; }
}

/* ── Journal grid ───────────────────────────────────────────────── */
@media(max-width:1024px){
    .ji-jgrid { grid-template-columns:repeat(3,1fr) !important; }
}
@media(max-width:768px){
    .ji-jgrid { grid-template-columns:repeat(2,1fr) !important; gap:1rem !important; }
}
@media(max-width:360px){
    .ji-jgrid { grid-template-columns:1fr !important; }
}

/* ── Features grid ──────────────────────────────────────────────── */
@media(max-width:1024px){
    .ji-feat-grid { grid-template-columns:repeat(3,1fr) !important; }
}
@media(max-width:640px){
    .ji-feat-grid { grid-template-columns:repeat(2,1fr) !important; gap:.75rem !important; }
}
@media(max-width:380px){
    .ji-feat-grid { grid-template-columns:1fr !important; }
}

/* ── CTA section ────────────────────────────────────────────────── */
@media(max-width:480px){
    .ji-cta-btns { flex-direction:column !important; align-items:stretch !important; }
    .ji-cta-btns a { width:100% !important; justify-content:center !important; box-sizing:border-box; text-align:center; }
}

/* ── Section padding reduction on mobile ───────────────────────── */
@media(max-width:640px){
    section[style*="padding:5rem"] { padding:3rem 1.25rem !important; }
    .py-20 { padding-top:3rem !important; padding-bottom:3rem !important; }
    .px-6 { padding-left:1.25rem !important; padding-right:1.25rem !important; }
}

/* ── Journal card hover ─────────────────────────────────────────── */
.gjs-jcard:hover { transform:translateY(-3px);box-shadow:0 12px 36px rgba(0,0,0,.11) !important; }
.gjs-jcard:hover .gjs-jcard-img { opacity:.95; }

/* ── Recent articles — prevent overflow ────────────────────────── */
@media(max-width:640px){
    .max-w-6xl { padding-left:1.25rem; padding-right:1.25rem; }
}
</style>

{{-- ═══ JOURNALS ═══ --}}
<section id="journals" style="padding:5rem 1.5rem;background:linear-gradient(180deg,#f8fafc 0%,#ffffff 100%);">
<div style="max-width:75rem;margin:0 auto;">

    {{-- Section header --}}
    <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:.75rem;margin-bottom:2.5rem;flex-wrap:wrap;">
        <div>
            <div style="display:inline-flex;align-items:center;gap:.5rem;padding:.25rem .875rem;border-radius:9999px;background:#eff6ff;border:1px solid #bfdbfe;margin-bottom:.875rem;">
                <svg style="width:.75rem;height:.75rem;color:#2563eb;" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/></svg>
                <span style="font-size:.7rem;font-weight:800;color:#2563eb;text-transform:uppercase;letter-spacing:.08em;">Journal Directory</span>
            </div>
            <h2 style="font-size:2rem;font-weight:900;color:#0f172a;line-height:1.2;margin:0;">Available Journals</h2>
            <p style="font-size:.9rem;color:#64748b;margin:.5rem 0 0;">{{ $journals->count() }} indexed and verified scientific journals</p>
        </div>
        @if($journals->count() > 0)
        <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:.375rem;font-size:.875rem;font-weight:600;color:#2563eb;text-decoration:none;white-space:nowrap;">
            View All
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
        @endif
    </div>


    {{-- Journal grid — 4 kolom --}}
    <div class="ji-jgrid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;align-items:start;">
    @forelse($journals as $i => $journal)
    @php
        $clr          = $colors[$i % count($colors)];
        $bg           = $lightBg[$i % count($lightBg)];
        $abbr         = strtoupper(substr($journal->name_abbrev ?? $journal->name, 0, 4));
        $currentIssue = $journal->issues->first();
        $desc         = strip_tags($journal->focus_scope ?? $journal->description ?? '');
    @endphp

    <div class="gjs-jcard"
         style="display:flex;flex-direction:column;border-radius:1rem;overflow:hidden;background:#fff;border:1px solid #e2e8f2;box-shadow:0 1px 6px rgba(0,0,0,.06);transition:transform .2s ease,box-shadow .2s ease;">

        {{-- ── COVER (full width, portrait feel via background) ─────────── --}}
        <a href="{{ route('journals.home', $journal->slug) }}"
           style="display:block;position:relative;background:#f0f2f5;overflow:hidden;aspect-ratio:3/4;flex-shrink:0;">

            @if($journal->cover_image)
            <img src="{{ asset('storage/' . $journal->cover_image) }}"
                 alt="Cover {{ $journal->name }}"
                 class="gjs-jcard-img"
                 style="width:100%;height:100%;object-fit:contain;display:block;transition:transform .4s ease;background:#f8fafc;">
            @elseif($journal->logo)
            {{-- Logo centered on gradient bg --}}
            <div style="width:100%;height:100%;background:linear-gradient(145deg,{{ $clr }}18,{{ $clr }}08);display:flex;align-items:center;justify-content:center;">
                <img src="{{ asset('storage/' . $journal->logo) }}" alt="{{ $journal->name }}"
                     style="width:96px;height:96px;object-fit:contain;filter:drop-shadow(0 4px 12px rgba(0,0,0,.15));">
            </div>
            @else
            {{-- Gradient placeholder --}}
            <div style="width:100%;height:100%;background:linear-gradient(145deg,{{ $clr }}dd,{{ $clr }}88);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;">
                <span style="font-size:2.5rem;font-weight:900;color:#fff;letter-spacing:-.03em;text-shadow:0 4px 16px rgba(0,0,0,.25);">{{ $abbr }}</span>
                <span style="font-size:.6rem;color:rgba(255,255,255,.65);font-weight:700;text-transform:uppercase;letter-spacing:.12em;">Journal</span>
            </div>
            @endif

            {{-- Color accent strip --}}
            <div style="position:absolute;bottom:0;left:0;right:0;height:3px;background:{{ $clr }};"></div>

            {{-- Stats pill top-right --}}
            <div style="position:absolute;top:.625rem;right:.625rem;display:flex;gap:.375rem;">
                <span style="padding:.2rem .55rem;border-radius:9999px;background:rgba(0,0,0,.45);backdrop-filter:blur(6px);font-size:.65rem;font-weight:700;color:#fff;">
                    {{ number_format($journal->articles_count) }} Articles
                </span>
            </div>

            {{-- SINTA badge top-left --}}
            @if($journal->sinta_level)
            <div style="position:absolute;top:.625rem;left:.625rem;padding:.2rem .55rem;border-radius:9999px;background:#f59e0b;font-size:.65rem;font-weight:800;color:#fff;">
                SINTA {{ $journal->sinta_level }}
            </div>
            @endif
        </a>
        {{-- ── END COVER ────────────────────────────────────────────────── --}}

        {{-- ── INFO ────────────────────────────────────────────────────── --}}
        <div style="flex:1;display:flex;flex-direction:column;padding:1.125rem 1.25rem 1.125rem;">

            {{-- Name --}}
            <h3 style="font-size:.9375rem;font-weight:800;color:{{ $clr }};line-height:1.35;margin:0 0 .5rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                <a href="{{ route('journals.home', $journal->slug) }}" style="color:inherit;text-decoration:none;">{{ $journal->name }}</a>
            </h3>

            {{-- Publisher --}}
            @if($journal->publisher)
            <p style="font-size:.75rem;color:#64748b;margin:0 0 .625rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ $journal->publisher }}
            </p>
            @endif

            {{-- ISSN + freq --}}
            <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-bottom:.75rem;">
                @if($journal->issn_print)
                <span style="font-size:.65rem;font-family:monospace;padding:.15rem .5rem;border-radius:.25rem;background:#f8fafc;color:#475569;font-weight:600;border:1px solid #e2e8f0;">p {{ $journal->issn_print }}</span>
                @endif
                @if($journal->issn_online)
                <span style="font-size:.65rem;font-family:monospace;padding:.15rem .5rem;border-radius:.25rem;background:#f8fafc;color:#475569;font-weight:600;border:1px solid #e2e8f0;">e {{ $journal->issn_online }}</span>
                @endif
                @if($journal->publication_frequency)
                <span style="font-size:.65rem;padding:.15rem .5rem;border-radius:.25rem;background:#eff6ff;color:#1e40af;font-weight:600;border:1px solid #dbeafe;">{{ $journal->publication_frequency }}</span>
                @endif
                @if($journal->doi_prefix)
                <span style="font-size:.65rem;font-family:monospace;padding:.15rem .5rem;border-radius:.25rem;background:#f0fdf4;color:#166534;font-weight:600;border:1px solid #bbf7d0;">DOI {{ $journal->doi_prefix }}</span>
                @endif
            </div>

            {{-- Description --}}
            @if($desc)
            <p style="font-size:.8125rem;line-height:1.65;color:#4b5563;margin:0 0 .875rem;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;flex:1;">
                {{ $desc }}
            </p>
            @else
            <div style="flex:1;min-height:.5rem;"></div>
            @endif

            {{-- Stats strip --}}
            <div style="display:flex;gap:1rem;padding:.625rem .75rem;border-radius:.5rem;background:#f8fafc;margin-bottom:.875rem;">
                <div style="text-align:center;flex:1;">
                    <div style="font-size:1rem;font-weight:900;color:{{ $clr }};line-height:1;">{{ number_format($journal->articles_count) }}</div>
                    <div style="font-size:.65rem;color:#94a3b8;margin-top:.1rem;font-weight:500;">Articles</div>
                </div>
                <div style="width:1px;background:#e2e8f0;"></div>
                <div style="text-align:center;flex:1;">
                    <div style="font-size:1rem;font-weight:900;color:{{ $clr }};line-height:1;">{{ number_format($journal->issues_count) }}</div>
                    <div style="font-size:.65rem;color:#94a3b8;margin-top:.1rem;font-weight:500;">Issues</div>
                </div>
                @if($currentIssue)
                <div style="width:1px;background:#e2e8f0;"></div>
                <div style="text-align:center;flex:1;">
                    <div style="font-size:.65rem;font-weight:800;color:#64748b;line-height:1.2;">Vol. {{ $currentIssue->volume }}</div>
                    <div style="font-size:.6rem;color:#94a3b8;margin-top:.1rem;">Current Issue</div>
                </div>
                @endif
            </div>

            {{-- Buttons --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;">
                <a href="{{ route('journals.home', $journal->slug) }}"
                   style="display:flex;align-items:center;justify-content:center;gap:.3rem;padding:.5rem;border-radius:.5rem;background:{{ $clr }};color:#fff;font-size:.78rem;font-weight:700;text-decoration:none;">
                    <svg style="width:.8rem;height:.8rem;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    View Journal
                </a>
                @if($currentIssue)
                <a href="{{ route('journals.issues.show', [$journal->slug, $currentIssue->id]) }}"
                   style="display:flex;align-items:center;justify-content:center;gap:.3rem;padding:.5rem;border-radius:.5rem;background:#fff;color:{{ $clr }};font-size:.78rem;font-weight:700;text-decoration:none;border:1.5px solid {{ $clr }};">
                    <svg style="width:.8rem;height:.8rem;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Latest Issue
                </a>
                @else
                <div></div>
                @endif
            </div>
        </div>
        {{-- ── END INFO ─────────────────────────────────────────────────── --}}

    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:4rem 1rem;border-radius:1rem;border:2px dashed #e2e8f0;background:#f8fafc;">
        <svg style="width:3rem;height:3rem;color:#cbd5e1;margin:0 auto .75rem;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        <p style="font-size:1rem;font-weight:700;color:#94a3b8;margin:0 0 .25rem;">No active journals yet</p>
        <p style="font-size:.875rem;color:#cbd5e1;margin:0;">Journals will appear here once activated.</p>
    </div>
    @endforelse
    </div>

</div>
</section>

<style>
.gjs-jcard:hover { transform:translateY(-3px);box-shadow:0 12px 36px rgba(0,0,0,.11) !important; }
.gjs-jcard:hover .gjs-jcard-img { opacity:.95; }
</style>

{{-- ═══ RECENT ARTICLES ═══ --}}
@if($recentArticles->isNotEmpty())
<section class="py-20 px-6" style="background:#f8fafc;border-top:1px solid #e2e8f0;">
    <div class="max-w-6xl mx-auto">
        <div class="mb-10">
            <p class="text-xs font-bold uppercase tracking-widest mb-1.5" style="color:#7c3aed;">Recently Published</p>
            <h2 class="font-black" style="font-size:1.875rem;color:#0f172a;">Latest Articles</h2>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:1.25rem;">
            @foreach($recentArticles as $article)
            <a href="{{ route('journals.articles.show', [$article->journal->slug, $article->id]) }}"
               class="flex flex-col rounded-2xl p-5 transition-all duration-200"
               style="background:#fff;border:1px solid #e8edf5;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,.06);"
               onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,.08)';this.style.transform='translateY(-2px)'"
               onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,.06)';this.style.transform='translateY(0)'">

                {{-- Tags --}}
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @if($article->section)
                    <span class="text-xs font-semibold px-2 py-0.5 rounded" style="background:#f1f5f9;color:#475569;">{{ $article->section->title }}</span>
                    @endif
                    <span class="text-xs font-bold" style="color:#2563eb;">{{ $article->journal->name_abbrev ?? Str::limit($article->journal->name, 14) }}</span>
                </div>

                {{-- Title --}}
                <h3 class="font-bold leading-snug flex-1 mb-3" style="font-size:.9rem;color:#0f172a;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $article->submission->title }}
                </h3>

                {{-- Authors --}}
                <p class="text-xs mb-3 truncate" style="color:#64748b;">
                    {{ $article->submission->contributors->map(fn($c)=>$c->last_name.', '.substr($c->first_name,0,1).'.')->join(' · ') }}
                </p>

                {{-- Footer --}}
                <div class="flex items-center justify-between pt-3" style="border-top:1px solid #f1f5f9;">
                    <span class="text-xs" style="color:#94a3b8;">{{ $article->date_published?->format('M Y') ?? '—' }}</span>
                    @if($article->doi)
                    <span class="text-xs font-bold font-mono px-2 py-0.5 rounded" style="background:#ede9fe;color:#5b21b6;">DOI</span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══ FEATURES ═══ --}}
<section class="py-20 px-6" style="background:#ffffff;">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <p class="text-xs font-bold uppercase tracking-widest mb-1.5" style="color:#059669;">Platform Features</p>
            <h2 class="font-black mb-3" style="font-size:1.875rem;color:#0f172a;">Why Choose {{ \App\Models\Setting::get('brand.site_name', config('app.name')) }}?</h2>
            <p class="mx-auto leading-relaxed" style="color:#64748b;max-width:460px;font-size:.9375rem;">
                Designed for the entire scientific publication ecosystem.
            </p>
        </div>

        <div class="ji-feat-grid" style="display:grid;grid-template-columns:repeat(6,1fr);gap:1rem;">

            @foreach([
                ['Open Access',       'All articles freely accessible to anyone, anytime.',          '#eff6ff','#dbeafe','rgba(37,99,235,.12)','#2563eb',  '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'],
                ['Double Peer Review','Double-blind review ensures quality and integrity.',           '#f0fdf4','#dcfce7','rgba(5,150,105,.12)', '#059669', '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>'],
                ['DOI Crossref',      'Every article receives a permanent DOI via Crossref.',        '#faf5ff','#ede9fe','rgba(124,58,237,.12)','#7c3aed', '<path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>'],
                ['Globally Indexed',  'OAI-PMH for Google Scholar, DOAJ &amp; PKP Index.',          '#fffbeb','#fef3c7','rgba(217,119,6,.12)', '#d97706', '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/>'],
                ['Secure &amp; Trusted',   'CSP headers, MFA, data encryption, and audit logs.',    '#fff1f2','#fee2e2','rgba(220,38,38,.12)', '#dc2626', '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>'],
                ['Responsive &amp; Fast',  'Optimized for all devices with fast loading times.',     '#ecfeff','#cffafe','rgba(8,145,178,.12)', '#0891b2', '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>'],
            ] as [$title, $desc, $bg, $iconBg, $border, $color, $path])
            <div style="background:{{ $bg }};border:1px solid {{ $border }};border-radius:.875rem;padding:1rem;transition:transform .2s,box-shadow .2s;"
                 onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,.09)'"
                 onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="width:2.25rem;height:2.25rem;border-radius:.625rem;background:{{ $iconBg }};display:flex;align-items:center;justify-content:center;margin-bottom:.75rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:{{ $color }};" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">{!! $path !!}</svg>
                </div>
                <h3 style="font-size:.8125rem;font-weight:800;color:#0f172a;margin:0 0 .375rem;line-height:1.3;">{!! $title !!}</h3>
                <p style="font-size:.72rem;color:#64748b;line-height:1.55;margin:0;">{!! $desc !!}</p>
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ═══ CTA ═══ --}}
@guest
<section style="padding:5rem 1.5rem;background:linear-gradient(135deg,#060d1f 0%,#0d1f4a 60%,#0a1628 100%);position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(59,130,246,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.05) 1px,transparent 1px);background-size:48px 48px;pointer-events:none;"></div>
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:40rem;height:20rem;border-radius:50%;background:radial-gradient(ellipse,rgba(99,102,241,.15) 0%,transparent 70%);pointer-events:none;"></div>

    <div style="position:relative;max-width:56rem;margin:0 auto;text-align:center;">
        <div style="display:inline-flex;align-items:center;gap:.5rem;padding:.375rem 1rem;border-radius:9999px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);font-size:.78rem;font-weight:700;color:rgba(255,255,255,.8);margin-bottom:1.5rem;">
            🎓 Free for Indonesia's academic community
        </div>

        <h2 style="font-size:clamp(1.875rem,4vw,2.875rem);font-weight:900;color:#fff;line-height:1.2;letter-spacing:-.02em;margin:0 0 1.125rem;">
            Ready to Publish<br>
            <span style="background:linear-gradient(90deg,#60a5fa,#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Your Research?</span>
        </h2>
        <p style="font-size:1rem;color:#94a3b8;line-height:1.75;margin:0 0 2.5rem;max-width:500px;margin-left:auto;margin-right:auto;">
            Join hundreds of researchers. Easy submission, transparent review, internationally indexed publication.
        </p>

        {{-- Feature checklist --}}
        <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:.625rem 1.75rem;margin-bottom:2.5rem;">
            @foreach(['Free forever','DOI Crossref','Google Scholar','Transparent review','Fast publication'] as $f)
            <span style="display:flex;align-items:center;gap:.375rem;font-size:.8125rem;color:#94a3b8;font-weight:500;">
                <svg style="width:.875rem;height:.875rem;color:#34d399;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ $f }}
            </span>
            @endforeach
        </div>

        <div class="ji-cta-btns" style="display:flex;flex-wrap:wrap;justify-content:center;gap:.75rem;">
            <a href="{{ route('register') }}"
               style="display:inline-flex;align-items:center;gap:.5rem;padding:.9375rem 2.25rem;background:linear-gradient(135deg,#2563eb,#4f46e5);color:#fff;font-size:.9375rem;font-weight:800;border-radius:.875rem;text-decoration:none;box-shadow:0 12px 32px rgba(37,99,235,.4);">
                Create Free Account
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="{{ route('login') }}"
               style="display:inline-flex;align-items:center;padding:.9375rem 2.25rem;background:rgba(255,255,255,.07);color:#e2e8f0;font-size:.9375rem;font-weight:600;border-radius:.875rem;border:1px solid rgba(255,255,255,.16);text-decoration:none;">
                Already have an account
            </a>
        </div>
    </div>
</section>
@endguest

</div>

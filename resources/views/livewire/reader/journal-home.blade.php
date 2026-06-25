<div>

{{-- ══════════════════════════════════════════ JOURNAL HEADER (OJS-style) ══ --}}
@php
    $hs        = $journal->settings ?? [];
    $hBgType   = $hs['header_bg_type']   ?? 'default';
    $hBgColor  = $hs['header_bg_color']  ?? '#1e3a8a';
    $hBgColor2 = $hs['header_bg_color2'] ?? '#4338ca';
    $hLight    = (bool)($hs['header_text_light'] ?? true);
    $hTagline  = $hs['header_tagline'] ?? '';
    $siteBg    = $hs['site_bg_color']    ?? '#f1f5f9';
    $indexedBy = $hs['indexed_by']       ?? [];
    $sponsors  = $hs['sponsors']         ?? [];

    // Menu settings
    $menuShowIssues        = (bool)($hs['menu_show_issues']        ?? true);
    $menuShowAnnouncements = (bool)($hs['menu_show_announcements'] ?? true);
    $menuShowAbout         = (bool)($hs['menu_show_about']         ?? true);
    $menuShowBrowse        = (bool)($hs['menu_show_browse']        ?? true);
    $customMenuItems       = $hs['custom_menu_items']              ?? [];
    $customPages           = array_values(array_filter($hs['custom_pages'] ?? [], fn($p) => $p['enabled'] ?? true));

    $headerStyle = match($hBgType) {
        'color'    => "background:{$hBgColor};",
        'gradient' => "background:linear-gradient(135deg,{$hBgColor},{$hBgColor2});",
        'image'    => $journal->homepage_image
                        ? "background:url(" . asset('storage/' . $journal->homepage_image) . ") center/cover no-repeat;position:relative;"
                        : "background:linear-gradient(135deg,{$hBgColor},{$hBgColor2});",
        default    => '',
    };

    $textColorMain  = ($hBgType !== 'default' && $hLight) ? '#ffffff' : '#0f172a';
    $textColorMuted = ($hBgType !== 'default' && $hLight) ? 'rgba(255,255,255,0.75)' : '#64748b';
    $overlayNeeded  = $hBgType === 'image' && $journal->homepage_image;
@endphp
{{-- ══ JOURNAL HEADER — terpusat sejajar konten (mirip OJS) ══ --}}
@php
    $pIssn = $journal->issn_print  ?? '';
    $eIssn = $journal->issn_online ?? '';
@endphp
{{-- Outer: full-width dengan background site, agar sisi kiri-kanan terisi warna --}}
<div style="width:100%;background:{{ $siteBg }};">
    {{-- Inner: max-w terpusat, sejajar konten di bawahnya --}}
    <div style="max-width:80rem;margin:0 auto;overflow:hidden;">

        @if($hBgType === 'image' && $journal->homepage_image)
        {{-- Mode gambar: tampil proporsional penuh dalam container --}}
        <img src="{{ asset('storage/' . $journal->homepage_image) }}"
             alt="{{ $journal->name }}"
             style="display:block;width:100%;height:auto;">

        @elseif($hBgType === 'color' || $hBgType === 'gradient')
        {{-- Mode warna / gradien --}}
        <div style="{{ $headerStyle }}width:100%;padding:1.5rem 2rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:1.5rem;font-weight:900;line-height:1.2;color:{{ $textColorMain }};margin:0;">{{ $journal->name }}</h1>
                    @if($hTagline)<p style="font-size:.875rem;margin:.25rem 0 0;color:{{ $textColorMuted }};">{{ $hTagline }}</p>@endif
                </div>
                @if($pIssn || $eIssn)
                <div style="text-align:right;font-size:.75rem;color:{{ $textColorMuted }};line-height:1.8;">
                    @if($pIssn)<div>P-ISSN: <strong style="color:{{ $textColorMain }}">{{ $pIssn }}</strong></div>@endif
                    @if($eIssn)<div>E-ISSN: <strong style="color:{{ $textColorMain }}">{{ $eIssn }}</strong></div>@endif
                </div>
                @endif
            </div>
        </div>

        @else
        {{-- Mode default: banner gradien biru profesional --}}
        <div style="background:linear-gradient(135deg,#1e3a8a 0%,#1e40af 60%,#1d4ed8 100%);padding:1.25rem 2rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:.875rem;">
                    @if($journal->cover_image)
                    <img src="{{ asset('storage/' . $journal->cover_image) }}"
                         alt="{{ $journal->name }}"
                         style="height:3rem;width:auto;border-radius:.375rem;box-shadow:0 2px 8px rgba(0,0,0,.3);">
                    @else
                    <div style="width:2.75rem;height:2.75rem;border-radius:.375rem;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.75rem;color:#fff;letter-spacing:.05em;border:1px solid rgba(255,255,255,.25);">
                        {{ strtoupper(substr($journal->name, 0, 3)) }}
                    </div>
                    @endif
                    <div>
                        <div style="font-size:1.25rem;font-weight:900;color:#fff;line-height:1.25;text-shadow:0 1px 3px rgba(0,0,0,.3);">{{ $journal->name }}</div>
                        @if($hTagline)<div style="font-size:.8125rem;color:rgba(255,255,255,.8);margin-top:.125rem;">{{ $hTagline }}</div>@endif
                    </div>
                </div>
                @if($pIssn || $eIssn)
                <div style="text-align:right;font-size:.75rem;color:rgba(255,255,255,.85);line-height:1.8;">
                    @if($pIssn)<div>P-ISSN: <strong style="color:#fff">{{ $pIssn }}</strong></div>@endif
                    @if($eIssn)<div>E-ISSN: <strong style="color:#fff">{{ $eIssn }}</strong></div>@endif
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
{{-- ══ end journal header ══ --}}

{{-- Sub-navigation: OJS-style tabs (dinamis) --}}
<div style="background:#ffffff;border-bottom:1px solid #e2e8f0;border-top:1px solid #f1f5f9;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-stretch">
            <nav class="flex gap-0 overflow-x-auto text-sm flex-1">
                {{-- Beranda — selalu tampil --}}
                <a href="{{ route('journals.home', $journal->slug) }}"
                   class="shrink-0 px-4 py-3 font-semibold border-b-2 transition-colors whitespace-nowrap"
                   style="border-color:#1e40af;color:#1e40af;">
                    Beranda
                </a>

                @if($menuShowIssues)
                <a href="{{ route('journals.issues', $journal->slug) }}"
                   class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors whitespace-nowrap">
                    Terbitan
                </a>
                @endif

                @if($menuShowAnnouncements && $announcements->isNotEmpty())
                <a href="#pengumuman"
                   class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors whitespace-nowrap">
                    Pengumuman
                </a>
                @endif

                @if($menuShowAbout)
                {{-- About dropdown --}}
                <div class="relative shrink-0 flex" x-data="{ open: false }">
                    <button @mouseenter="open = true" @mouseleave="open = false" @click="open = !open"
                            class="flex items-center gap-1 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors text-sm whitespace-nowrap">
                        Tentang
                        <svg class="w-3 h-3 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @mouseleave="open = false" @click.outside="open = false" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute top-full left-0 w-52 bg-white border border-slate-200 rounded-b-xl rounded-tr-xl shadow-lg z-50 py-1.5">
                        @foreach([
                            ['about',               'Tentang Jurnal'],
                            ['editorial-team',      'Tim Editorial'],
                            ['submissions',         'Pengiriman Naskah'],
                            ['guidelines',          'Panduan Penulis'],
                            ['reviewer-guidelines', 'Panduan Reviewer'],
                            ['ethics',              'Etika Publikasi'],
                            ['privacy',             'Kebijakan Privasi'],
                            ['contact',             'Kontak'],
                        ] as [$pg, $plabel])
                        <a href="{{ route('journals.page', [$journal->slug, $pg]) }}"
                           class="block px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                           {{ $plabel }}
                        </a>
                        @endforeach
                        {{-- Halaman kustom --}}
                        @foreach($customPages as $cp)
                        <a href="{{ route('journals.page', [$journal->slug, $cp['slug']]) }}"
                           class="block px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                           {{ $cp['title'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($menuShowBrowse)
                <a href="{{ route('journals.browse', [$journal->slug, 'author']) }}"
                   class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors text-sm whitespace-nowrap">
                    Jelajahi
                </a>
                @endif

                {{-- Custom menu items --}}
                @foreach($customMenuItems as $cmi)
                <a href="{{ $cmi['url'] }}"
                   target="{{ $cmi['target'] ?? '_self' }}"
                   class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors text-sm whitespace-nowrap">
                    {{ $cmi['label'] }}
                </a>
                @endforeach
            </nav>

            {{-- Search icon --}}
            <div class="flex items-center pl-2 border-l border-slate-200 ml-2 relative" x-data="{ sopen: false }">
                <button @click="sopen = !sopen; $nextTick(() => $refs.searchInput?.focus())"
                        class="p-2 text-slate-500 hover:text-blue-600 transition-colors rounded-lg hover:bg-blue-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <div x-show="sopen" @click.outside="sopen = false" x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 top-full mt-1 w-72 bg-white border border-slate-200 rounded-xl shadow-lg z-50 p-2">
                    <form action="{{ route('journals.search', $journal->slug) }}" method="GET">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input name="q" type="text" x-ref="searchInput"
                                   placeholder="Cari artikel, penulis..."
                                   class="w-full pl-9 pr-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<div style="background:{{ $siteBg }};">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── MAIN AREA (left 2/3) ──────────────────────────────────────── --}}
        <div class="lg:col-span-2">

            {{-- ── JOURNAL INFO CARD ──────────────────────────────────────── --}}
            @php $isOA = ($journal->settings['open_access'] ?? true); @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-8 overflow-hidden">

                {{-- Top accent bar --}}
                <div style="height:4px;background:linear-gradient(90deg,#1e40af,#0891b2,#0d9488);"></div>

                <div class="p-5">
                    {{-- Cover + Info side by side --}}
                    <div class="flex gap-4">

                        {{-- Cover image — diperbesar --}}
                        <div class="shrink-0" style="width:96px;">
                            <div class="rounded-xl overflow-hidden border border-slate-200 shadow-md" style="width:96px;height:132px;">
                                @if($journal->cover_image)
                                <img src="{{ asset('storage/' . $journal->cover_image) }}" alt="{{ $journal->name }}"
                                     style="width:100%;height:100%;object-fit:cover;object-position:top;display:block;">
                                @elseif($journal->logo)
                                <img src="{{ Storage::disk('public')->url($journal->logo) }}" alt="{{ $journal->name }}"
                                     style="width:100%;height:100%;object-fit:contain;object-position:center;display:block;background:#eff6ff;padding:12px;">
                                @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(145deg,#1e40af,#0891b2);">
                                    <span style="color:#fff;font-weight:900;font-size:13px;text-align:center;padding:6px;line-height:1.2;">
                                        {{ strtoupper(substr($journal->name_abbrev ?? $journal->name, 0, 4)) }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Info kanan --}}
                        <div class="flex-1 min-w-0">

                            {{-- Nama + OPEN badge --}}
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h1 class="text-base font-black text-slate-900 leading-snug">{{ $journal->name }}</h1>
                                @if($isOA)
                                <span class="shrink-0 flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold"
                                      style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;white-space:nowrap;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1a7 7 0 015.292 11.583L21 16.414V22h-5.586l-1.002-1.002A7 7 0 1112 1zm0 2a5 5 0 100 10A5 5 0 0012 3zm0 1.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z"/></svg>
                                    OPEN
                                </span>
                                @endif
                            </div>

                            {{-- Publisher --}}
                            @if($journal->publisher)
                            <p class="text-xs font-semibold text-blue-700 mb-1.5">{{ $journal->publisher }}</p>
                            @endif

                            {{-- ISSN badges --}}
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                @if($journal->issn_print)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-mono font-semibold"
                                      style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;">
                                    p-ISSN&nbsp;<strong>{{ $journal->issn_print }}</strong>
                                </span>
                                @endif
                                @if($journal->issn_online)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-mono font-semibold"
                                      style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                                    e-ISSN&nbsp;<strong>{{ $journal->issn_online }}</strong>
                                </span>
                                @endif
                                @if($journal->apc_enabled && $journal->apc_amount)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold"
                                      style="background:#fff7ed;color:#9a3412;border:1px solid #fed7aa;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    APC {{ $journal->apc_currency }} {{ number_format($journal->apc_amount, 0, ',', '.') }}{{ $journal->apc_waiver_policy ? ' · Waiver' : '' }}
                                </span>
                                @endif
                            </div>

                            {{-- Deskripsi singkat --}}
                            @if($journal->focus_scope || $journal->about_journal)
                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                                {{ Str::limit(strip_tags($journal->focus_scope ?: $journal->about_journal), 180) }}
                            </p>
                            @endif

                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-100 mt-4 mb-3"></div>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-wrap gap-2">
                        @auth
                        <a href="{{ route('submit') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-all hover:shadow-md hover:-translate-y-px"
                           style="background:linear-gradient(135deg,#1e40af,#1d4ed8);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Kirim Naskah
                        </a>
                        @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-all hover:shadow-md hover:-translate-y-px"
                           style="background:linear-gradient(135deg,#1e40af,#1d4ed8);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Kirim Naskah
                        </a>
                        @endauth
                        <a href="{{ route('journals.issues', $journal->slug) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-slate-600 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Arsip Terbitan
                        </a>
                        @if($journal->wa_contact)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $journal->wa_contact) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-all hover:shadow-md hover:-translate-y-px"
                           style="background:linear-gradient(135deg,#16a34a,#15803d);">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.998 2C6.477 2 2 6.484 2 12.017c0 1.99.521 3.848 1.427 5.449L2.036 22l4.66-1.366A9.987 9.987 0 0011.998 22c5.521 0 9.998-4.484 9.998-10.017C21.996 6.484 17.519 2 11.998 2z"/></svg>
                            Chat Pengelola
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            {{-- ── END JOURNAL INFO CARD ──────────────────────────────────── --}}

            {{-- Current Issue: OJS TOC Style --}}
            @if($currentIssue)
            <div class="mb-8">
                {{-- Issue header banner --}}
                <div class="rounded-xl p-5 mb-6" style="background:#eff6ff;border:1px solid #bfdbfe;">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-blue-500 mb-1">Terbitan Saat Ini</p>
                            <h2 class="text-xl font-black text-blue-900">{{ $currentIssue->getLabel() }}</h2>
                            @if($currentIssue->date_published)
                            <p class="text-sm text-blue-600 mt-1">
                                Diterbitkan {{ $currentIssue->date_published->translatedFormat('d F Y') }}
                            </p>
                            @endif
                            @if($currentIssue->description)
                            <p class="text-sm text-blue-700 mt-2 leading-relaxed">{{ strip_tags($currentIssue->description) }}</p>
                            @endif
                        </div>
                        <a href="{{ route('journals.issues.show', [$journal->slug, $currentIssue->id]) }}"
                           class="shrink-0 text-xs font-semibold text-blue-700 hover:text-blue-900 underline whitespace-nowrap mt-1">
                            Lihat Semua →
                        </a>
                    </div>
                </div>

                {{-- TOC by section --}}
                @if($tocBySection->isNotEmpty())
                @foreach($tocBySection as $sectionTitle => $articles)
                <div class="mb-7">
                    {{-- Section heading (OJS style: uppercase, border-bottom) --}}
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-700 pb-2 mb-4"
                        style="border-bottom:1px solid #cbd5e1;">
                        {{ $sectionTitle }}
                    </h3>

                    <div class="space-y-0 divide-y divide-slate-100">
                        @foreach($articles as $article)
                        <div class="py-5 first:pt-0">
                            {{-- Title --}}
                            <h4 class="font-bold text-slate-900 leading-snug mb-1.5 text-base">
                                <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                                   class="hover:text-blue-700 transition-colors">
                                    {{ $article->submission->title }}
                                </a>
                            </h4>

                            {{-- Authors --}}
                            <p class="text-sm text-slate-500 mb-2">
                                {{ $article->submission->contributors->map(fn($c) => $c->full_name)->join(', ') }}
                            </p>

                            {{-- Abstract excerpt --}}
                            @if($article->submission->abstract)
                            <p class="text-sm text-slate-600 leading-relaxed mb-3 line-clamp-2">
                                {{ Str::limit($article->submission->abstract, 200) }}
                            </p>
                            @endif

                            {{-- Meta row: pages, DOI, stats, galley buttons --}}
                            <div class="flex items-center flex-wrap gap-x-4 gap-y-2 mt-1">
                                @if($article->pages)
                                <span class="text-xs text-slate-400">Hal. {{ $article->pages }}</span>
                                @endif
                                @if($article->doi)
                                <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                                   class="text-xs text-slate-400 hover:text-blue-600 transition-colors font-mono">
                                    https://doi.org/{{ $article->doi }}
                                </a>
                                @endif
                                {{-- View / Download stats --}}
                                @if($article->views > 0)
                                <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ number_format($article->views) }}
                                </span>
                                @endif
                                @if($article->downloads > 0)
                                <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    {{ number_format($article->downloads) }}
                                </span>
                                @endif
                                {{-- Galley buttons (OJS style) --}}
                                <div class="flex gap-1.5 ml-auto flex-wrap">
                                    <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                                       class="text-xs font-bold px-3 py-1.5 rounded border transition-colors"
                                       style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;">
                                        Abstrak
                                    </a>
                                    @foreach($article->galleys->take(3) as $galley)
                                    <a href="{{ route('journals.articles.galley.view', [$journal->slug, $article->id, $galley->id]) }}"
                                       class="text-xs font-bold px-3 py-1.5 rounded border transition-colors"
                                       style="background:#1e40af;border-color:#1e3a8a;color:#ffffff;">
                                        {{ strtoupper($galley->label ?? 'PDF') }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-10 text-slate-400 text-sm rounded-xl border border-dashed border-slate-200">
                    Belum ada artikel di terbitan ini.
                </div>
                @endif
            </div>
            @else
            <div class="rounded-xl border border-dashed border-slate-200 p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <p class="font-medium">Belum ada terbitan aktif</p>
            </div>
            @endif

            {{-- Announcements — below articles so they don't push down primary content --}}
            @if($announcements->isNotEmpty())
            <div id="pengumuman" class="mt-8">
                <h2 class="text-base font-black text-slate-800 uppercase tracking-wider pb-2 mb-5"
                    style="border-bottom:2px solid #1e40af;">
                    Pengumuman
                </h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach($announcements as $ann)
                    <div class="bg-white border border-slate-200 rounded-xl p-5 flex gap-4 hover:border-blue-200 hover:shadow-sm transition-all group">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5"
                             style="background:#eff6ff;">
                            <svg class="w-4.5 h-4.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-900 text-sm leading-snug mb-1 group-hover:text-blue-700 transition-colors">
                                {{ $ann->title }}
                            </h3>
                            <p class="text-xs text-slate-400 mb-2">
                                {{ $ann->date_posted?->format('d F Y') }}
                            </p>
                            @if($ann->description_short)
                            <p class="text-sm text-slate-600 leading-relaxed line-clamp-3">
                                {{ Str::limit(strip_tags($ann->description_short), 180) }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- ── SIDEBAR (right 1/3) ──────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Current Issue cover card --}}
            @if($currentIssue)
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3" style="background:#1e40af;">
                    <p class="text-xs font-bold text-white uppercase tracking-widest">Terbitan Saat Ini</p>
                </div>
                <div class="p-4 flex gap-4 items-start">
                    {{-- Issue cover placeholder --}}
                    <div class="w-16 h-20 rounded-lg shrink-0 flex items-center justify-center text-white font-black text-xs text-center leading-tight"
                         style="background:linear-gradient(145deg,#1e40af,#4338ca);">
                        Vol<br>{{ $currentIssue->volume ?? '—' }}<br>No.{{ $currentIssue->number ?? '—' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 text-sm leading-snug">{{ $currentIssue->getLabel() }}</p>
                        @if($currentIssue->date_published)
                        <p class="text-xs text-slate-500 mt-1">{{ $currentIssue->date_published->format('Y') }}</p>
                        @endif
                        <a href="{{ route('journals.issues.show', [$journal->slug, $currentIssue->id]) }}"
                           class="inline-block mt-3 text-xs font-semibold px-3 py-1.5 rounded-lg text-white transition-colors"
                           style="background:#1e40af;">
                            Lihat Terbitan
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Akreditasi & Indeksasi --}}
            @foreach($sidebarBlocks->where('type', 'accreditation') as $block)
                @include('reader.partials.sidebar-block', [
                    'block'   => $block,
                    'journal' => $journal,
                    'stats'   => $journalStats,
                ])
            @endforeach

            {{-- Informasi Jurnal (journal_info blocks) --}}
            @foreach($sidebarBlocks->where('type', 'journal_info') as $block)
                @include('reader.partials.sidebar-block', [
                    'block'   => $block,
                    'journal' => $journal,
                    'stats'   => $journalStats,
                ])
            @endforeach

            {{-- Browse --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3" style="background:#1e40af;">
                    <p class="text-xs font-bold text-white uppercase tracking-widest">Jelajahi</p>
                </div>
                <div class="p-4">
                    <ul class="space-y-1 text-sm">
                        <li>
                            <a href="{{ route('journals.issues', $journal->slug) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                Berdasarkan Terbitan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('journals.browse', [$journal->slug, 'author']) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Berdasarkan Penulis
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('journals.browse', [$journal->slug, 'title']) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h12"/></svg>
                                Berdasarkan Judul
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('journals.browse', [$journal->slug, 'keyword']) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                Berdasarkan Kata Kunci
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Past issues --}}
            @if($pastIssues->isNotEmpty())
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3" style="background:#1e40af;">
                    <p class="text-xs font-bold text-white uppercase tracking-widest">Terbitan Sebelumnya</p>
                </div>
                <div class="p-4">
                    <ul class="space-y-1 text-sm">
                        @foreach($pastIssues as $pi)
                        <li>
                            <a href="{{ route('journals.issues.show', [$journal->slug, $pi->id]) }}"
                               class="flex items-center gap-2 px-2 py-1.5 rounded text-slate-700 hover:text-blue-700 hover:bg-blue-50 transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                {{ $pi->getLabel() }}
                            </a>
                        </li>
                        @endforeach
                        <li class="pt-2 border-t border-slate-100">
                            <a href="{{ route('journals.issues', $journal->slug) }}"
                               class="text-xs text-blue-600 hover:underline">Lihat semua terbitan →</a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            {{-- submission block handled via sidebarBlocks below --}}

            {{-- ── Admin-configured sidebar blocks ───────────────────── --}}
            @foreach($sidebarBlocks->whereNotIn('type', ['journal_info','accreditation']) as $block)
                @include('reader.partials.sidebar-block', [
                    'block'   => $block,
                    'journal' => $journal,
                    'stats'   => $journalStats,
                ])
            @endforeach

            {{-- Pengumuman — paling bawah --}}
            @if($announcements->isNotEmpty())
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 flex items-center justify-between" style="background:#1e40af;">
                    <p class="text-xs font-bold text-white uppercase tracking-widest">Pengumuman</p>
                    <a href="#pengumuman" class="text-xs text-blue-200 hover:text-white transition-colors font-medium">
                        Lihat semua ↓
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($announcements->take(3) as $ann)
                    <div class="p-4">
                        <p class="font-semibold text-slate-800 text-sm leading-snug mb-0.5">{{ $ann->title }}</p>
                        <p class="text-xs text-slate-400 mb-1.5">{{ $ann->date_posted?->format('d M Y') }}</p>
                        @if($ann->description_short)
                        <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                            {{ Str::limit(strip_tags($ann->description_short), 100) }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- end sidebar --}}
    </div>

    {{-- Indexed By & Sponsors --}}
    @if(!empty($indexedBy) || !empty($sponsors))
    @php
    // Map indexer name → local SVG file (relative to public/images/indexers/)
    $indexerLogos = [
        'Google Scholar'   => 'google-scholar.svg',
        'GARUDA'           => 'garuda.svg',
        'Crossref'         => 'crossref.svg',
        'Scopus'           => 'scopus.svg',
        'Web of Science'   => 'wos.svg',
        'Scilit'           => 'scilit.svg',
        'DOAJ'             => 'doaj.svg',
        'Dimensions'       => 'dimensions.svg',
        'Index Copernicus' => 'index-copernicus.svg',
        'BASE'             => 'base.svg',
        'SINTA'            => 'sinta.svg',
        'ROAD'             => 'road.svg',
        'PKP Index'        => 'pkp-index.svg',
    ];
    @endphp
    <div class="mt-8 border-t border-slate-200 pt-8 space-y-6">
        @if(!empty($indexedBy))
        <div>
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Terindeks Oleh</h3>
            <div class="flex flex-wrap items-center gap-4">
                @foreach($indexedBy as $idx)
                @php
                    $localSvg = $indexerLogos[$idx['name']] ?? null;
                    $localPath = $localSvg ? public_path('images/indexers/' . $localSvg) : null;
                    $hasLocalLogo = $localPath && file_exists($localPath);
                @endphp
                @if(!empty($idx['logo']))
                {{-- Logo gambar yang diupload pengelola --}}
                <a href="{{ $idx['url'] ?? '#' }}" target="_blank" rel="noopener"
                   title="{{ $idx['name'] }}"
                   class="block bg-white border border-slate-200 rounded-lg p-2 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                    <img src="{{ asset('storage/' . $idx['logo']) }}" alt="{{ $idx['name'] }}"
                         class="h-8 w-auto object-contain max-w-[100px]">
                </a>
                @elseif($hasLocalLogo)
                {{-- Logo SVG bawaan sistem --}}
                <a href="{{ $idx['url'] ?? '#' }}" target="_blank" rel="noopener"
                   title="{{ $idx['name'] }}"
                   class="block hover:opacity-80 hover:-translate-y-0.5 transition-all">
                    <img src="{{ asset('images/indexers/' . $localSvg) }}" alt="{{ $idx['name'] }}"
                         class="h-10 w-auto object-contain" style="max-width:120px;">
                </a>
                @else
                {{-- Fallback text badge --}}
                <a href="{{ $idx['url'] ?? '#' }}" target="_blank" rel="noopener"
                   class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:border-blue-300 hover:text-blue-700 transition-all shadow-sm hover:-translate-y-0.5">
                    {{ $idx['name'] }}
                </a>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($sponsors))
        <div>
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Sponsor & Mitra</h3>
            <div class="flex flex-wrap items-center gap-6">
                @foreach($sponsors as $sp)
                @if(!empty($sp['logo']))
                <a href="{{ $sp['url'] ?? '#' }}" target="_blank" rel="noopener"
                   class="block hover:opacity-80 transition-opacity">
                    <img src="{{ asset('storage/' . $sp['logo']) }}" alt="{{ $sp['name'] }}"
                         class="h-12 w-auto object-contain grayscale hover:grayscale-0 transition-all">
                </a>
                @else
                <a href="{{ $sp['url'] ?? '#' }}" target="_blank" rel="noopener"
                   class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:border-orange-300 hover:text-orange-700 transition-all shadow-sm">
                    {{ $sp['name'] }}
                </a>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

</div>
</div>{{-- end site-bg wrapper --}}

</div>

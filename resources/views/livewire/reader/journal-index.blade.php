<div>
@php
$colors = ['#2563eb','#059669','#7c3aed','#dc2626','#d97706','#0891b2'];
$lightBg = ['#eff6ff','#f0fdf4','#faf5ff','#fff1f2','#fffbeb','#ecfeff'];
@endphp

{{-- ═══ HERO ═══ --}}
<section style="background:linear-gradient(150deg,#0c1a3a 0%,#1a3272 55%,#0e1e4a 100%);position:relative;overflow:hidden;">
    {{-- Decorative blobs --}}
    <div style="position:absolute;inset:0;pointer-events:none;overflow:hidden;">
        <div style="position:absolute;width:700px;height:500px;top:-150px;left:50%;transform:translateX(-50%);background:radial-gradient(ellipse,rgba(59,130,246,.2) 0%,transparent 70%);"></div>
        <div style="position:absolute;width:300px;height:300px;bottom:-80px;left:5%;background:radial-gradient(circle,rgba(139,92,246,.15) 0%,transparent 70%);"></div>
        <div style="position:absolute;width:250px;height:250px;bottom:-60px;right:5%;background:radial-gradient(circle,rgba(6,182,212,.12) 0%,transparent 70%);"></div>
    </div>

    <div class="relative max-w-5xl mx-auto px-6 py-24 text-center">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 mb-8 px-4 py-1.5 rounded-full text-xs font-semibold"
             style="background:rgba(59,130,246,.18);border:1px solid rgba(59,130,246,.35);color:#93c5fd;">
            <span class="w-2 h-2 rounded-full" style="background:#34d399;flex-shrink:0;"></span>
            Open Access &nbsp;·&nbsp; Peer Reviewed &nbsp;·&nbsp; DOI Crossref
        </div>

        {{-- Heading --}}
        <h1 class="font-black leading-tight mb-5" style="font-size:clamp(2.2rem,5vw,3.5rem);color:#ffffff;">
            Publikasikan Riset Anda<br>
            <span style="background:linear-gradient(90deg,#60a5fa,#a78bfa,#34d399);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                di Platform {{ \App\Models\Setting::get('brand.site_name', config('app.name')) }}
            </span>
        </h1>

        <p class="mb-10 mx-auto leading-relaxed" style="font-size:1.05rem;color:#94a3b8;max-width:540px;line-height:1.8;">
            Sistem manajemen jurnal ilmiah Indonesia — dari submission, review dua arah, hingga penerbitan berindeks Crossref dan Google Scholar.
        </p>

        {{-- Buttons --}}
        <div class="flex flex-wrap justify-center gap-3 mb-14">
            <a href="#journals"
               class="inline-flex items-center gap-2 font-bold rounded-xl transition-opacity hover:opacity-85"
               style="padding:.875rem 2rem;background:#2563eb;color:#fff;font-size:.9375rem;text-decoration:none;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Jelajahi Jurnal
            </a>
            @guest
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 font-semibold rounded-xl"
               style="padding:.875rem 2rem;background:rgba(255,255,255,.1);color:#e2e8f0;font-size:.9375rem;border:1px solid rgba(255,255,255,.2);text-decoration:none;">
                Daftar Gratis →
            </a>
            @else
            <a href="{{ route('submit') }}"
               class="inline-flex items-center gap-2 font-semibold rounded-xl"
               style="padding:.875rem 2rem;background:rgba(255,255,255,.1);color:#e2e8f0;font-size:.9375rem;border:1px solid rgba(255,255,255,.2);text-decoration:none;">
                Kirim Naskah →
            </a>
            @endguest
        </div>

        {{-- Stats --}}
        <div class="flex flex-wrap justify-center gap-10">
            @foreach([
                ['n'=>$stats['journals'],  'l'=>'Jurnal Aktif',  'c'=>'#60a5fa'],
                ['n'=>$stats['articles'],  'l'=>'Total Artikel', 'c'=>'#34d399'],
                ['n'=>$stats['issues'],    'l'=>'Terbitan',      'c'=>'#a78bfa'],
                ['n'=>$stats['authors'],   'l'=>'Pengguna',      'c'=>'#fb923c'],
            ] as $s)
            <div class="text-center">
                <div class="font-black leading-none mb-1" style="font-size:2rem;color:{{ $s['c'] }};">{{ number_format($s['n']) }}</div>
                <div style="font-size:.72rem;color:#64748b;text-transform:uppercase;letter-spacing:.07em;">{{ $s['l'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ TRUST BAR ═══ --}}
<div style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
    <div class="max-w-5xl mx-auto px-6 py-3.5 flex flex-wrap items-center justify-center gap-x-7 gap-y-2">
        <span class="text-xs font-bold uppercase tracking-widest" style="color:#94a3b8;">Terindeks di</span>
        <span class="inline-flex items-center gap-1.5 text-sm font-semibold" style="color:#475569;">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 4L22 9L12 14L2 9L12 4Z"/><path d="M4 10.5V18"/><circle cx="4" cy="19.2" r="1.2" fill="currentColor" stroke="none"/><path d="M7 12V16.5Q9.5 19.5 12 19.5Q14.5 19.5 17 16.5V12"/></svg>
            Google Scholar
        </span>
        <span class="inline-flex items-center gap-1.5 text-sm font-semibold" style="color:#475569;">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            Crossref DOI
        </span>
        <span class="inline-flex items-center gap-1.5 text-sm font-semibold" style="color:#475569;">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="5" cy="19" r="1.5" fill="currentColor" stroke="none"/><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/></svg>
            OAI-PMH 2.0
        </span>
        <span class="inline-flex items-center gap-1.5 text-sm font-semibold" style="color:#475569;">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
            DOAJ Ready
        </span>
        <span class="inline-flex items-center gap-1.5 text-sm font-semibold" style="color:#475569;">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><circle cx="10" cy="15" r="2.5"/><path d="M12 17L14.5 19.5"/></svg>
            PKP Index
        </span>
    </div>
</div>

{{-- ═══ JOURNALS ═══ --}}
<section id="journals" class="py-20 px-6" style="background:#ffffff;">
    <div class="max-w-6xl mx-auto">

        <div class="mb-10">
            <p class="text-xs font-bold uppercase tracking-widest mb-1.5" style="color:#2563eb;">Direktori Jurnal</p>
            <h2 class="font-black" style="font-size:1.875rem;color:#0f172a;">Jurnal yang Tersedia</h2>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.5rem;">
        @forelse($journals as $i => $journal)
        @php $clr = $colors[$i % count($colors)]; $bg = $lightBg[$i % count($lightBg)]; @endphp
        <a href="{{ route('journals.home', $journal->slug) }}"
           class="flex flex-col rounded-2xl overflow-hidden transition-all duration-200 hover:-translate-y-1"
           style="background:#fff;border:1px solid #e8edf5;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,.06);"
           onmouseover="this.style.boxShadow='0 12px 32px rgba(0,0,0,.1)'"
           onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,.06)'">

            {{-- Top bar --}}
            <div style="height:4px;background:{{ $clr }};"></div>

            <div class="flex flex-col flex-1 p-6">
                {{-- Header --}}
                <div class="flex items-start gap-3 mb-4">
                    <div class="rounded-xl shrink-0 overflow-hidden" style="width:48px;height:48px;border:1px solid #e2e8f0;">
                        @if($journal->logo)
                        <img src="{{ asset('storage/' . $journal->logo) }}" alt="{{ $journal->name }}"
                             style="width:100%;height:100%;object-fit:cover;">
                        @else
                        <div class="flex items-center justify-center w-full h-full" style="background:{{ $clr }};">
                            <span class="font-black text-center leading-tight" style="color:#fff;font-size:.65rem;padding:.1rem;">
                                {{ strtoupper(substr($journal->name_abbrev ?? $journal->name, 0, 4)) }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        @if($journal->name_abbrev)
                        <div class="text-xs font-bold uppercase tracking-wide mb-0.5" style="color:{{ $clr }};">
                            {{ $journal->name_abbrev }}
                        </div>
                        @endif
                        <h3 class="font-bold leading-snug" style="font-size:.9375rem;color:#0f172a;">
                            {{ $journal->name }}
                        </h3>
                        <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $journal->publisher }}</p>
                    </div>
                </div>

                {{-- ISSN --}}
                @if($journal->issn_online || $journal->issn_print)
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @if($journal->issn_print)
                    <span class="text-xs font-mono px-2 py-0.5 rounded" style="background:#f1f5f9;color:#475569;">p-ISSN {{ $journal->issn_print }}</span>
                    @endif
                    @if($journal->issn_online)
                    <span class="text-xs font-mono px-2 py-0.5 rounded" style="background:#f1f5f9;color:#475569;">e-ISSN {{ $journal->issn_online }}</span>
                    @endif
                </div>
                @endif

                {{-- Focus scope --}}
                @if($journal->focus_scope)
                <p class="text-sm leading-relaxed flex-1" style="color:#64748b;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ strip_tags($journal->focus_scope) }}
                </p>
                @endif

                {{-- Footer stats --}}
                <div class="flex items-center justify-between mt-4 pt-4" style="border-top:1px solid #f1f5f9;">
                    <div class="flex gap-5">
                        <div>
                            <div class="font-black leading-none" style="font-size:1.25rem;color:{{ $clr }};">{{ number_format($journal->articles_count) }}</div>
                            <div class="text-xs mt-0.5" style="color:#94a3b8;">Artikel</div>
                        </div>
                        <div>
                            <div class="font-black leading-none" style="font-size:1.25rem;color:{{ $clr }};">{{ number_format($journal->issues_count) }}</div>
                            <div class="text-xs mt-0.5" style="color:#94a3b8;">Terbitan</div>
                        </div>
                    </div>
                    <span class="text-sm font-bold" style="color:{{ $clr }};">Lihat →</span>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-16 rounded-2xl" style="border:2px dashed #e2e8f0;">
            <p class="text-base font-semibold" style="color:#94a3b8;">Belum ada jurnal aktif</p>
        </div>
        @endforelse
        </div>
    </div>
</section>

{{-- ═══ RECENT ARTICLES ═══ --}}
@if($recentArticles->isNotEmpty())
<section class="py-20 px-6" style="background:#f8fafc;border-top:1px solid #e2e8f0;">
    <div class="max-w-6xl mx-auto">
        <div class="mb-10">
            <p class="text-xs font-bold uppercase tracking-widest mb-1.5" style="color:#7c3aed;">Baru Diterbitkan</p>
            <h2 class="font-black" style="font-size:1.875rem;color:#0f172a;">Artikel Terbaru</h2>
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
            <p class="text-xs font-bold uppercase tracking-widest mb-1.5" style="color:#059669;">Keunggulan Platform</p>
            <h2 class="font-black mb-3" style="font-size:1.875rem;color:#0f172a;">Kenapa Memilih {{ \App\Models\Setting::get('brand.site_name', config('app.name')) }}?</h2>
            <p class="mx-auto leading-relaxed" style="color:#64748b;max-width:460px;font-size:.9375rem;">
                Dirancang untuk seluruh ekosistem publikasi ilmiah Indonesia.
            </p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1.25rem;">

            {{-- Akses Terbuka --}}
            <div class="rounded-2xl p-5 transition-all duration-200" style="background:#eff6ff;border:1px solid rgba(37,99,235,.1);" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,.08)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background:#dbeafe;">
                    <svg class="w-5 h-5" style="color:#2563eb;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </div>
                <h3 class="font-bold mb-1.5" style="font-size:.9375rem;color:#0f172a;">Akses Terbuka</h3>
                <p style="font-size:.8rem;color:#64748b;line-height:1.65;">Semua artikel dapat diakses siapa saja, kapan saja, gratis.</p>
            </div>

            {{-- Peer Review Ganda --}}
            <div class="rounded-2xl p-5 transition-all duration-200" style="background:#f0fdf4;border:1px solid rgba(5,150,105,.1);" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,.08)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background:#dcfce7;">
                    <svg class="w-5 h-5" style="color:#059669;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <h3 class="font-bold mb-1.5" style="font-size:.9375rem;color:#0f172a;">Peer Review Ganda</h3>
                <p style="font-size:.8rem;color:#64748b;line-height:1.65;">Double-blind review menjamin kualitas dan integritas ilmiah.</p>
            </div>

            {{-- DOI Crossref --}}
            <div class="rounded-2xl p-5 transition-all duration-200" style="background:#faf5ff;border:1px solid rgba(124,58,237,.1);" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,.08)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background:#ede9fe;">
                    <svg class="w-5 h-5" style="color:#7c3aed;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                </div>
                <h3 class="font-bold mb-1.5" style="font-size:.9375rem;color:#0f172a;">DOI Crossref</h3>
                <p style="font-size:.8rem;color:#64748b;line-height:1.65;">Setiap artikel mendapat DOI permanen via Crossref.</p>
            </div>

            {{-- Terindeks Global --}}
            <div class="rounded-2xl p-5 transition-all duration-200" style="background:#fffbeb;border:1px solid rgba(217,119,6,.1);" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,.08)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background:#fef3c7;">
                    <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/></svg>
                </div>
                <h3 class="font-bold mb-1.5" style="font-size:.9375rem;color:#0f172a;">Terindeks Global</h3>
                <p style="font-size:.8rem;color:#64748b;line-height:1.65;">OAI-PMH untuk Google Scholar, DOAJ & PKP Index.</p>
            </div>

            {{-- Aman & Terpercaya --}}
            <div class="rounded-2xl p-5 transition-all duration-200" style="background:#fff1f2;border:1px solid rgba(220,38,38,.1);" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,.08)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background:#fee2e2;">
                    <svg class="w-5 h-5" style="color:#dc2626;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </div>
                <h3 class="font-bold mb-1.5" style="font-size:.9375rem;color:#0f172a;">Aman &amp; Terpercaya</h3>
                <p style="font-size:.8rem;color:#64748b;line-height:1.65;">CSP headers, MFA, enkripsi data, dan audit log.</p>
            </div>

            {{-- Responsif & Cepat --}}
            <div class="rounded-2xl p-5 transition-all duration-200" style="background:#ecfeff;border:1px solid rgba(8,145,178,.1);" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,.08)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background:#cffafe;">
                    <svg class="w-5 h-5" style="color:#0891b2;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                </div>
                <h3 class="font-bold mb-1.5" style="font-size:.9375rem;color:#0f172a;">Responsif &amp; Cepat</h3>
                <p style="font-size:.8rem;color:#64748b;line-height:1.65;">Tampilan optimal di semua perangkat, loading cepat.</p>
            </div>

        </div>
    </div>
</section>

{{-- ═══ CTA ═══ --}}
@guest
<section class="py-20 px-6 text-center relative overflow-hidden"
         style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 50%,#4338ca 100%);">
    <div style="position:absolute;top:-100px;right:-100px;width:400px;height:400px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-80px;left:-80px;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
    <div class="relative max-w-xl mx-auto">
        <div class="inline-flex items-center gap-2 mb-6 px-4 py-1.5 rounded-full text-sm font-semibold"
             style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:rgba(255,255,255,.9);">
            🎓 Gratis untuk civitas akademika Indonesia
        </div>
        <h2 class="font-black mb-4 leading-snug" style="font-size:clamp(1.75rem,4vw,2.75rem);color:#ffffff;">
            Bergabunglah dengan komunitas peneliti {{ \App\Models\Setting::get('brand.site_name', config('app.name')) }}
        </h2>
        <p class="mb-8 leading-relaxed" style="color:rgba(191,219,254,1);font-size:1rem;line-height:1.75;">
            Submission mudah, review transparan, dan publikasi terindeks internasional.
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 font-extrabold rounded-xl transition-opacity hover:opacity-90"
               style="padding:.9rem 2.25rem;background:#fff;color:#1e3a8a;font-size:.9375rem;text-decoration:none;box-shadow:0 8px 24px rgba(0,0,0,.2);">
                Buat Akun Gratis →
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center font-semibold rounded-xl"
               style="padding:.9rem 2.25rem;background:rgba(255,255,255,.15);color:#fff;font-size:.9375rem;border:1px solid rgba(255,255,255,.3);text-decoration:none;">
                Sudah punya akun
            </a>
        </div>
    </div>
</section>
@endguest

</div>

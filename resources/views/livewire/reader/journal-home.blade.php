<div>

{{-- ══════════════════════════════════════════ JOURNAL HEADER (OJS-style) ══ --}}
@php
    $hs        = $journal->settings ?? [];
    $hBgType   = $hs['header_bg_type']   ?? 'default';
    $hBgColor  = $hs['header_bg_color']  ?? '#1e3a8a';
    $hBgColor2 = $hs['header_bg_color2'] ?? '#4338ca';
    $hLight    = (bool)($hs['header_text_light'] ?? true);
    $hTagline  = $hs['header_tagline'] ?? '';

    $headerStyle = match($hBgType) {
        'color'    => "background:{$hBgColor};",
        'gradient' => "background:linear-gradient(135deg,{$hBgColor},{$hBgColor2});",
        'image'    => $journal->homepage_image
                        ? "background:url(" . asset('storage/' . $journal->homepage_image) . ") center/cover no-repeat;position:relative;"
                        : "background:linear-gradient(135deg,{$hBgColor},{$hBgColor2});",
        default    => '',
    };

    $headerBorder = $hBgType === 'default' ? 'border-b border-slate-200' : '';
    $textColorMain = ($hBgType !== 'default' && $hLight) ? '#ffffff' : '#0f172a';
    $textColorMuted= ($hBgType !== 'default' && $hLight) ? 'rgba(255,255,255,0.75)' : '#64748b';
    $overlayNeeded = $hBgType === 'image' && $journal->homepage_image;
@endphp
{{-- Hero area (banner/color) — sub-nav terpisah supaya tidak tertutup overlay --}}
<div style="position:relative;overflow:hidden;{{ $headerStyle }}">
    @if($overlayNeeded)
    <div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,0.55) 0%,rgba(0,0,0,0.65) 100%);z-index:0;"></div>
    @endif
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" style="position:relative;z-index:1;">
        <div class="flex flex-col sm:flex-row gap-6">

            {{-- Cover image --}}
            <div class="shrink-0">
                @if($journal->cover_image)
                <img src="{{ asset('storage/' . $journal->cover_image) }}" alt="{{ $journal->name }}"
                     class="w-24 h-32 object-cover rounded-lg border border-slate-200 shadow-sm">
                @else
                <div class="w-24 h-32 rounded-lg flex items-center justify-center shadow-sm"
                     style="background:linear-gradient(145deg,#1e40af,#3730a3);">
                    <span class="text-white font-black text-base text-center px-1 leading-tight">
                        {{ strtoupper(substr($journal->name_abbrev ?? $journal->name, 0, 4)) }}
                    </span>
                </div>
                @endif
            </div>

            {{-- Journal info --}}
            <div class="flex-1">
                <h1 class="text-2xl font-black leading-snug mb-0.5" style="color:{{ $textColorMain }}">{{ $journal->name }}</h1>
                @if($hTagline)
                <p class="text-sm mb-2" style="color:{{ $textColorMuted }}">{{ $hTagline }}</p>
                @endif

                <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm mb-3" style="color:{{ $textColorMuted }}">
                    @if($journal->publisher)
                    <span>{{ $journal->publisher }}</span>
                    @endif
                    @if($journal->issn_print)
                    <span class="font-mono">p-ISSN: <strong style="color:{{ $textColorMain }}">{{ $journal->issn_print }}</strong></span>
                    @endif
                    @if($journal->issn_online)
                    <span class="font-mono">e-ISSN: <strong style="color:{{ $textColorMain }}">{{ $journal->issn_online }}</strong></span>
                    @endif
                </div>

                @if($journal->focus_scope)
                <p class="text-sm leading-relaxed mb-4 max-w-2xl line-clamp-2" style="color:{{ $textColorMuted }}">
                    {{ strip_tags($journal->focus_scope) }}
                </p>
                @endif

                <div class="flex flex-wrap gap-2">
                    @auth
                    <a href="{{ route('submit') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-colors"
                       style="background:#1e40af;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Kirim Naskah
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg"
                       style="background:#1e40af;">
                        Kirim Naskah
                    </a>
                    @endauth
                    <a href="{{ route('journals.issues', $journal->slug) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                       style="{{ $hBgType !== 'default' ? 'background:rgba(255,255,255,0.15);color:' . $textColorMain . ';' : 'background:#f1f5f9;color:#334155;' }}">
                        Arsip Terbitan
                    </a>
                    @if($journal->wa_contact)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $journal->wa_contact) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-colors"
                       style="background:#25d366;">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.998 2C6.477 2 2 6.484 2 12.017c0 1.99.521 3.848 1.427 5.449L2.036 22l4.66-1.366A9.987 9.987 0 0011.998 22c5.521 0 9.998-4.484 9.998-10.017C21.996 6.484 17.519 2 11.998 2z"/></svg>
                        Chat Pengelola
                    </a>
                    @endif
                </div>

                {{-- APC Banner --}}
                @if($journal->apc_enabled && $journal->apc_amount)
                <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold" style="background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    APC: {{ $journal->apc_currency }} {{ number_format($journal->apc_amount, 0, ',', '.') }}
                    @if($journal->apc_waiver_policy)
                    &nbsp;·&nbsp; <span style="color:#b45309;">Waiver tersedia</span>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Tentang Jurnal — di dalam hero, satu background ──────────── --}}
        @if($journal->focus_scope || $journal->about_journal)
        <div id="tentang" class="mt-6 pt-5" style="border-top:1px solid {{ $hBgType !== 'default' ? 'rgba(255,255,255,0.2)' : '#e2e8f0' }};"
             x-data="{ open: true }">
            <button @click="open = !open"
                    class="w-full flex items-center justify-between mb-3 text-left group">
                <span class="text-xs font-bold uppercase tracking-widest flex items-center gap-2"
                      style="color:{{ $textColorMuted }};">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    Tentang Jurnal
                </span>
                <svg class="w-4 h-4 shrink-0 transition-transform duration-200"
                     :class="open ? '' : 'rotate-180'"
                     style="color:{{ $textColorMuted }};"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-4">
                    @if($journal->focus_scope)
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest mb-2"
                           style="color:{{ $textColorMuted }};">Fokus dan Ruang Lingkup</p>
                        <div class="text-sm leading-relaxed prose prose-sm max-w-none"
                             style="color:{{ $textColorMain }};--tw-prose-body:{{ $textColorMain }};--tw-prose-headings:{{ $textColorMain }};">
                            {!! $journal->focus_scope !!}
                        </div>
                    </div>
                    @endif
                    @if($journal->about_journal)
                    <div class="{{ !$journal->focus_scope ? 'lg:col-span-2' : '' }}">
                        <div class="text-sm leading-relaxed prose prose-sm max-w-none"
                             style="color:{{ $textColorMain }};">
                            {!! $journal->about_journal !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
</div>{{-- end hero --}}

{{-- Sub-navigation: OJS-style tabs + About dropdown + Search --}}
<div class="border-t border-slate-200 relative" style="background:#f8fafc;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-stretch">
                <nav class="flex gap-0 overflow-x-auto text-sm flex-1">
                    <a href="{{ route('journals.home', $journal->slug) }}"
                       class="shrink-0 px-4 py-3 font-semibold border-b-2 transition-colors"
                       style="border-color:#1e40af;color:#1e40af;">
                        Beranda
                    </a>
                    <a href="{{ route('journals.issues', $journal->slug) }}"
                       class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors">
                        Terbitan
                    </a>
                    @if($announcements->isNotEmpty())
                    <a href="#pengumuman"
                       class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors">
                        Pengumuman
                    </a>
                    @endif

                    {{-- About dropdown --}}
                    <div class="relative shrink-0 flex" x-data="{ open: false }">
                        <button @mouseenter="open = true" @mouseleave.self="open = false" @click="open = !open"
                                class="flex items-center gap-1 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors text-sm">
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
                            ] as [$slug, $label])
                            <a href="{{ route('journals.page', [$journal->slug, $slug]) }}"
                               class="block px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                               {{ $label }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('journals.browse', [$journal->slug, 'author']) }}"
                       class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors text-sm">
                        Jelajahi
                    </a>
                </nav>

                {{-- Search icon button --}}
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


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── MAIN AREA (left 2/3) ──────────────────────────────────────── --}}
        <div class="lg:col-span-2">


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
</div>

</div>

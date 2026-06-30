{{-- ══ Shared journal header + sub-nav ══
     Usage: @include('reader.partials.journal-header', ['activeTab' => 'home'])
     activeTab: home | issues | about | browse | search | article
--}}
@php
    $__hs    = $journal->settings ?? [];
    $__bgTyp = $__hs['header_bg_type']   ?? 'default';
    $__bgC1  = $__hs['header_bg_color']  ?? '#1e3a8a';
    $__bgC2  = $__hs['header_bg_color2'] ?? '#0891b2';
    $__light = (bool)($__hs['header_text_light'] ?? true);
    $__tag   = $__hs['header_tagline'] ?? '';
    $__isOA  = (bool)($__hs['open_access'] ?? true);
    $__pIssn = $journal->issn_print  ?? '';
    $__eIssn = $journal->issn_online ?? '';

    $__showIssues  = (bool)($__hs['menu_show_issues']        ?? true);
    $__showAnn     = (bool)($__hs['menu_show_announcements'] ?? true);
    $__showAbout   = (bool)($__hs['menu_show_about']         ?? true);
    $__showBrowse  = (bool)($__hs['menu_show_browse']        ?? true);
    $__cmi         = $__hs['custom_menu_items'] ?? [];
    $__cpages      = array_values(array_filter($__hs['custom_pages'] ?? [], fn($p) => $p['enabled'] ?? true));
    $__hasAnn      = isset($announcements) && $announcements->isNotEmpty();

    $__tab  = $activeTab ?? 'home';
    $__nav  = fn($t) => $__tab === $t
                ? 'shrink-0 px-4 py-3 font-semibold border-b-2 whitespace-nowrap transition-colors'
                : 'shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors whitespace-nowrap';
    $__nst  = fn($t) => $__tab === $t ? 'border-color:#1e40af;color:#1e40af;' : '';

    // Journal URL
    $__journalUrl = url('/journals/'.$journal->slug);

    // About dropdown pages
    $__aboutPages = [
        ['about',              __('site.about_journal')],
        ['editorial-team',     __('site.editorial_team')],
        ['submissions',        __('site.submissions_guide')],
        ['guidelines',         __('site.author_guidelines')],
        ['reviewer-guidelines',__('site.reviewer_guidelines')],
        ['ethics',             __('site.publication_ethics')],
        ['privacy',            __('site.privacy_policy')],
        ['contact',            __('site.contact')],
    ];

    // Language switcher
    $__locales     = $availableLocales ?? ['id','en'];
    $__curLocale   = $currentLocale   ?? app()->getLocale();
    $__localeFlags = ['id'=>'🇮🇩','en'=>'🇬🇧','ar'=>'🇸🇦'];
    $__localeShort = ['id'=>'ID','en'=>'EN','ar'=>'AR'];
@endphp

{{-- ══════════════════════════════════════════════════════════
     JOURNAL BANNER
══════════════════════════════════════════════════════════ --}}

@if($__bgTyp === 'image' && $journal->homepage_image)
{{-- ── Mode GAMBAR: tampil penuh tanpa overlay teks ── --}}
<div style="width:100%;line-height:0;">
    <img src="{{ asset('storage/'.$journal->homepage_image) }}"
         alt="{{ $journal->name }}"
         style="display:block;width:100%;height:auto;">
</div>
{{-- URL bar --}}
<div style="width:100%;background:#1e293b;">
    <div style="max-width:80rem;margin:0 auto;padding:.4rem 2.5rem;display:flex;align-items:center;gap:.5rem;">
        <svg style="width:.75rem;height:.75rem;color:#94a3b8;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        <span style="font-size:.6875rem;font-family:monospace;color:#94a3b8;">{{ $__journalUrl }}</span>
    </div>
</div>

@elseif($__bgTyp === 'color' || $__bgTyp === 'gradient')
{{-- ── Mode WARNA / GRADIEN KUSTOM ── --}}
@php
    $__customBg = $__bgTyp === 'gradient'
        ? "linear-gradient(135deg,{$__bgC1},{$__bgC2})"
        : $__bgC1;
    $__cMain  = $__light ? '#ffffff' : '#0f172a';
    $__cMuted = $__light ? 'rgba(255,255,255,.78)' : '#374151';
@endphp
<div style="width:100%;background:{{ $__customBg }};">
    <div style="max-width:80rem;margin:0 auto;padding:1.75rem 2.5rem;">
        <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
            {{-- Logo --}}
            @if($journal->logo)
            <img src="{{ asset('storage/'.$journal->logo) }}" alt="{{ $journal->name }}"
                 style="height:4rem;width:auto;object-fit:contain;filter:drop-shadow(0 2px 6px rgba(0,0,0,.3));flex-shrink:0;">
            @elseif($journal->cover_image)
            <img src="{{ asset('storage/'.$journal->cover_image) }}" alt="{{ $journal->name }}"
                 style="height:4rem;width:auto;border-radius:.375rem;box-shadow:0 4px 12px rgba(0,0,0,.3);flex-shrink:0;">
            @endif
            <div style="flex:1;min-width:0;">
                <h1 style="font-size:1.75rem;font-weight:900;color:{{ $__cMain }};line-height:1.2;margin:0 0 .25rem;">{{ $journal->name }}</h1>
                @if($journal->publisher)<p style="font-size:.875rem;color:{{ $__cMuted }};margin:0;">{{ $journal->publisher }}</p>@endif
            </div>
            <div style="text-align:right;flex-shrink:0;">
                @if($__isOA)
                <div style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .75rem;border-radius:999px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);margin-bottom:.5rem;">
                    <svg style="width:.875rem;height:.875rem;color:{{ $__cMain }};" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1a7 7 0 015.292 11.583L21 16.414V22h-5.586l-1.002-1.002A7 7 0 1112 1zm0 2a5 5 0 100 10A5 5 0 0012 3zm0 1.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z"/></svg>
                    <span style="font-size:.75rem;font-weight:700;color:{{ $__cMain }};">OPEN ACCESS</span>
                </div>
                @endif
                @if($__pIssn || $__eIssn)
                <div style="font-size:.75rem;font-family:monospace;color:{{ $__cMuted }};line-height:1.9;">
                    @if($__pIssn)<div>P-ISSN: <strong style="color:{{ $__cMain }}">{{ $__pIssn }}</strong></div>@endif
                    @if($__eIssn)<div>E-ISSN: <strong style="color:{{ $__cMain }}">{{ $__eIssn }}</strong></div>@endif
                </div>
                @endif
            </div>
        </div>
    </div>
    {{-- URL bar --}}
    <div style="background:rgba(0,0,0,.22);border-top:1px solid rgba(255,255,255,.08);">
        <div style="max-width:80rem;margin:0 auto;padding:.375rem 2.5rem;display:flex;align-items:center;gap:.5rem;">
            <svg style="width:.75rem;height:.75rem;color:rgba(255,255,255,.55);flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <span style="font-size:.6875rem;font-family:monospace;color:rgba(255,255,255,.6);">{{ $__journalUrl }}</span>
        </div>
    </div>
</div>

@else
{{-- ── MODE DEFAULT: Banner profesional dengan cover dekoratif ── --}}
<div style="width:100%;background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 50%,#0891b2 100%);position:relative;overflow:hidden;">

    {{-- Dekorasi lingkaran background --}}
    <div style="position:absolute;inset:0;pointer-events:none;overflow:hidden;">
        <div style="position:absolute;right:-60px;top:-60px;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.06);"></div>
        <div style="position:absolute;right:120px;bottom:-80px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.04);"></div>
        <div style="position:absolute;left:45%;top:-40px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.035);"></div>
    </div>

    {{-- Content --}}
    <div style="max-width:80rem;margin:0 auto;padding:1.5rem 2.5rem;position:relative;z-index:1;">
        <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">

            {{-- Kiri: Logo / Cover image --}}
            @if($journal->logo)
            <div style="flex-shrink:0;">
                <img src="{{ asset('storage/'.$journal->logo) }}" alt="{{ $journal->name }}"
                     style="height:5.5rem;width:auto;object-fit:contain;filter:drop-shadow(0 4px 12px rgba(0,0,0,.4));">
            </div>
            @elseif($journal->cover_image)
            <div style="flex-shrink:0;position:relative;">
                <img src="{{ asset('storage/'.$journal->cover_image) }}" alt="{{ $journal->name }}"
                     style="height:5.5rem;width:auto;border-radius:.5rem;box-shadow:0 8px 28px rgba(0,0,0,.5);">
            </div>
            @else
            <div style="flex-shrink:0;width:4rem;height:5.5rem;border-radius:.5rem;background:rgba(255,255,255,.15);border:2px solid rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;text-align:center;">
                <span style="font-size:.65rem;font-weight:900;color:#fff;letter-spacing:.08em;padding:.25rem;line-height:1.3;">{{ strtoupper(substr($journal->name_abbrev ?? $journal->name,0,4)) }}</span>
            </div>
            @endif

            {{-- Tengah: Nama + info --}}
            <div style="flex:1;min-width:0;">
                <h1 style="font-size:1.875rem;font-weight:900;color:#fff;line-height:1.2;margin:0 0 .25rem;text-shadow:0 2px 8px rgba(0,0,0,.3);">{{ $journal->name }}</h1>
                @if($journal->publisher)
                <p style="font-size:.875rem;color:rgba(255,255,255,.8);margin:0 0 .625rem;font-weight:500;">{{ $journal->publisher }}</p>
                @elseif($__tag)
                <p style="font-size:.875rem;color:rgba(255,255,255,.75);margin:0 0 .625rem;font-style:italic;">{{ $__tag }}</p>
                @endif
                {{-- Badges --}}
                <div style="display:flex;flex-wrap:wrap;gap:.4rem;align-items:center;">
                    @if($__isOA)
                    <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .7rem;border-radius:999px;font-size:.6875rem;font-weight:700;background:rgba(251,191,36,.2);color:#fde68a;border:1px solid rgba(251,191,36,.4);">
                        <svg style="width:.75rem;height:.75rem;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1a7 7 0 015.292 11.583L21 16.414V22h-5.586l-1.002-1.002A7 7 0 1112 1zm0 2a5 5 0 100 10A5 5 0 0012 3zm0 1.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z"/></svg>
                        Open Access
                    </span>
                    @endif
                    @if(!empty($journal->sinta_level))
                    <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .7rem;border-radius:999px;font-size:.6875rem;font-weight:700;background:rgba(134,239,172,.18);color:#86efac;border:1px solid rgba(134,239,172,.35);">
                        SINTA {{ $journal->sinta_level }}
                    </span>
                    @endif
                    @if(!empty($journal->publication_frequency))
                    <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .7rem;border-radius:999px;font-size:.6875rem;font-weight:700;background:rgba(147,197,253,.15);color:#bae6fd;border:1px solid rgba(147,197,253,.3);">
                        {{ $journal->publication_frequency }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Kanan: ISSN box --}}
            @if($__pIssn || $__eIssn)
            <div style="flex-shrink:0;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:.625rem;padding:.75rem 1.125rem;text-align:right;backdrop-filter:blur(4px);">
                @if($__pIssn)
                <div style="font-size:.6875rem;font-family:monospace;color:rgba(255,255,255,.7);margin-bottom:.2rem;">P-ISSN</div>
                <div style="font-size:.9375rem;font-weight:800;font-family:monospace;color:#fff;letter-spacing:.05em;margin-bottom:.5rem;">{{ $__pIssn }}</div>
                @endif
                @if($__eIssn)
                <div style="font-size:.6875rem;font-family:monospace;color:rgba(255,255,255,.7);margin-bottom:.2rem;">E-ISSN</div>
                <div style="font-size:.9375rem;font-weight:800;font-family:monospace;color:#fff;letter-spacing:.05em;">{{ $__eIssn }}</div>
                @endif
            </div>
            @endif

        </div>
    </div>

    {{-- URL bar --}}
    <div style="background:rgba(0,0,0,.28);border-top:1px solid rgba(255,255,255,.08);position:relative;z-index:1;">
        <div style="max-width:80rem;margin:0 auto;padding:.4rem 2.5rem;display:flex;align-items:center;gap:.5rem;">
            <svg style="width:.75rem;height:.75rem;color:rgba(255,255,255,.5);flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <span style="font-size:.6875rem;font-family:monospace;color:rgba(255,255,255,.58);">{{ $__journalUrl }}</span>
        </div>
    </div>
</div>
@endif

{{-- ══ SUB-NAVIGATION ══ --}}
<div style="background:#ffffff;border-bottom:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-stretch">
            <nav class="flex gap-0 overflow-x-auto text-sm flex-1">

                <a href="{{ route('journals.home', $journal->slug) }}"
                   class="{{ $__nav('home') }}" style="{{ $__nst('home') }}">
                    {{ __('site.home') }}
                </a>

                @if($__showIssues)
                <a href="{{ route('journals.issues', $journal->slug) }}"
                   class="{{ $__nav('issues') }}" style="{{ $__nst('issues') }}">
                    {{ __('site.issues') }}
                </a>
                @endif

                @if($__showAnn && $__hasAnn)
                <a href="{{ $__tab === 'home' ? '#pengumuman' : route('journals.home', $journal->slug).'#pengumuman' }}"
                   class="{{ $__nav('announcements') }}" style="{{ $__nst('announcements') }}">
                    {{ __('site.announcements') }}
                </a>
                @endif

                @if($__showAbout)
                <div class="relative shrink-0 flex" x-data="{ open: false }">
                    <button @mouseenter="open = true" @mouseleave="open = false" @click="open = !open"
                            class="flex items-center gap-1 px-4 py-3 border-b-2 transition-colors text-sm whitespace-nowrap {{ in_array($__tab, ['about']) ? 'font-semibold' : 'text-slate-600 hover:text-blue-700 border-transparent hover:border-blue-300' }}"
                            style="{{ in_array($__tab, ['about']) ? 'border-color:#1e40af;color:#1e40af;' : '' }}">
                        {{ __('site.about') }}
                        <svg class="w-3 h-3 ml-0.5 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @mouseleave="open = false" @click.outside="open = false" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute top-full left-0 w-52 bg-white border border-slate-200 rounded-b-xl rounded-tr-xl shadow-lg z-50 py-1.5">
                        @foreach($__aboutPages as [$pg, $plbl])
                        <a href="{{ route('journals.page', [$journal->slug, $pg]) }}"
                           class="block px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            {{ $plbl }}
                        </a>
                        @endforeach
                        @foreach($__cpages as $cp)
                        <a href="{{ route('journals.page', [$journal->slug, $cp['slug']]) }}"
                           class="block px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            {{ $cp['title'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($__showBrowse)
                <a href="{{ route('journals.browse', [$journal->slug, 'author']) }}"
                   class="{{ $__nav('browse') }}" style="{{ $__nst('browse') }}">
                    {{ __('site.browse') }}
                </a>
                @endif

                @foreach($__cmi as $item)
                <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}"
                   class="{{ $__nav('') }}">
                    {{ $item['label'] }}
                </a>
                @endforeach

            </nav>

            {{-- Language Switcher + Search --}}
            <div class="flex items-center gap-0.5 ml-2 pl-2 border-l border-slate-200">

                {{-- Language switcher --}}
                @if(count($__locales) > 1)
                <div class="relative" x-data="{ lopen: false }">
                    <button @click="lopen = !lopen" @click.outside="lopen = false"
                            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                        <span>{{ $__localeFlags[$__curLocale] ?? '🌐' }}</span>
                        <span>{{ $__localeShort[$__curLocale] ?? strtoupper($__curLocale) }}</span>
                        <svg class="w-2.5 h-2.5" :class="lopen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="lopen" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 top-full mt-1 w-36 bg-white border border-slate-200 rounded-xl shadow-lg z-50 py-1">
                        <p class="px-3 pb-1 pt-0.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('site.select_language') }}</p>
                        @foreach($__locales as $__loc)
                        <a href="{{ route('language.switch', $__loc) }}"
                           class="flex items-center gap-2 px-3 py-1.5 text-sm {{ $__curLocale === $__loc ? 'font-bold text-blue-700 bg-blue-50' : 'text-slate-700 hover:bg-slate-50' }} transition-colors">
                            <span>{{ $__localeFlags[$__loc] ?? '🌐' }}</span>
                            <span>{{ __('site.language_'.$__loc) }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Search --}}
                <div class="relative" x-data="{ sopen: false }">
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
                                       placeholder="{{ __('site.search_articles') }}"
                                       class="w-full pl-9 pr-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
{{-- ══ end header ══ --}}

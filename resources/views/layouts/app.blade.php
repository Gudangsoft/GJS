<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName    = \App\Models\Setting::get('brand.site_name', config('app.name'));
        $siteDesc    = $description ?? \App\Models\Setting::get('brand.description', 'Platform jurnal ilmiah Indonesia');
        $ogImage     = \App\Models\Setting::get('brand.og_image')
                        ? asset('storage/' . \App\Models\Setting::get('brand.og_image'))
                        : null;
        $ogLocale    = \App\Models\Setting::get('seo.og_locale', 'id_ID');
        $twCard      = \App\Models\Setting::get('seo.twitter_card', 'summary_large_image');
        $twSite      = \App\Models\Setting::get('seo.twitter_site');
        $robots      = $metaRobots ?? \App\Models\Setting::get('seo.meta_robots', 'index,follow');
        $keywords    = $metaKeywords ?? \App\Models\Setting::get('seo.meta_keywords');
        $pageTitle   = isset($title) ? $title . ' — ' . $siteName : $siteName;
        $canonUrl    = request()->url();

        // Verifikasi
        $gsc     = \App\Models\Setting::get('seo.google_search_console');
        $bing    = \App\Models\Setting::get('seo.bing_verification');
        $yandex  = \App\Models\Setting::get('seo.yandex_verification');
        $ga4     = \App\Models\Setting::get('seo.google_analytics_id');
        $gtm     = \App\Models\Setting::get('seo.google_tag_manager');
    @endphp

    <title>{{ $pageTitle }}</title>

    {{-- ── SEO Dasar ──────────────────────────────────────────────────────── --}}
    <meta name="robots" content="{{ $robots }}">
    <meta name="description" content="{{ $siteDesc }}">
    @if($keywords)
    <meta name="keywords" content="{{ $keywords }}">
    @endif
    <link rel="canonical" href="{{ $canonUrl }}">

    {{-- ── Verifikasi Mesin Pencari ───────────────────────────────────────── --}}
    @if($gsc)
    <meta name="google-site-verification" content="{{ $gsc }}">
    @endif
    @if($bing)
    <meta name="msvalidate.01" content="{{ $bing }}">
    @endif
    @if($yandex)
    <meta name="yandex-verification" content="{{ $yandex }}">
    @endif

    {{-- ── Open Graph ─────────────────────────────────────────────────────── --}}
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $siteDesc }}">
    <meta property="og:url" content="{{ $canonUrl }}">
    <meta property="og:locale" content="{{ $ogLocale }}">
    @if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @endif

    {{-- ── Twitter Card ───────────────────────────────────────────────────── --}}
    <meta name="twitter:card" content="{{ $twCard }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $siteDesc }}">
    @if($twSite)
    <meta name="twitter:site" content="@{{ ltrim($twSite, '@') }}">
    @endif
    @if($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    {{-- ── Google Scholar Citation Meta Tag (per artikel, via @stack) ────── --}}
    @stack('citation_meta')

    {{-- ── Extra meta (per halaman) ──────────────────────────────────────── --}}
    @stack('head_meta')

    {{-- ── Favicon ─────────────────────────────────────────────────────────  --}}
    @php $favicon = \App\Models\Setting::get('brand.favicon'); @endphp
    @if($favicon)
    <link rel="icon" href="{{ asset('storage/' . $favicon) }}">
    @else
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')

    {{-- ── Google Tag Manager ─────────────────────────────────────────────── --}}
    @if($gtm)
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtm }}');</script>
    @elseif($ga4)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $ga4 }}');</script>
    @endif
</head>
<body class="h-full bg-slate-50 antialiased text-slate-800">
{{-- GTM noscript --}}
@if($gtm)
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- ── Top Navigation ─────────────────────────────────────────────────────── --}}
<div x-data="{ open: false, mobileOpen: false }">
<header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">

            {{-- Brand --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                @if(!empty($brandLogo))
                <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-8 w-auto object-contain">
                @else
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                    <span class="text-white font-black text-xs tracking-tight">{{ $brandAbbrev }}</span>
                </div>
                @endif
                <span class="font-semibold text-slate-900 text-base hidden sm:block">{{ $brandName }}</span>
            </a>

            @isset($journalName)
            <div class="hidden md:flex items-center gap-2 text-sm text-slate-500">
                <span>/</span>
                <span class="font-medium text-slate-700">{{ $journalName }}</span>
            </div>
            @endisset

            <div class="flex-1"></div>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-colors">Beranda</a>
                @auth
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-colors">Dashboard</a>
                @if(auth()->user()->hasAnyRole(['editor','journal_manager','super_admin']))
                <a href="{{ route('editor.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-colors">Editor</a>
                @endif
                @if(auth()->user()->hasRole('reviewer'))
                <a href="{{ route('reviewer.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-colors">Reviewer</a>
                @endif
                <a href="{{ route('submit') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">Kirim Naskah</a>
                @endauth
            </nav>

            {{-- Auth area + hamburger --}}
            <div class="flex items-center gap-2">
                @guest
                <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors hidden sm:inline-block">Masuk</a>
                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">Daftar</a>
                @else
                {{-- Notification Bell --}}
                <livewire:notification-bell />
                {{-- User dropdown (desktop) --}}
                <div class="relative hidden md:block">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                        <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-700 font-semibold text-xs">{{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}</span>
                        </div>
                        <span class="max-w-32 truncate">{{ auth()->user()->first_name }}</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50"
                         style="display:none;">
                        <div class="px-3 py-2 border-b border-slate-100">
                            <p class="text-xs font-medium text-slate-900 truncate">{{ auth()->user()->email }}</p>
                            <p class="text-xs text-slate-500 mt-0.5 capitalize">{{ auth()->user()->roles->first()?->name ?? 'author' }}</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>Dashboard</a>
                        <a href="{{ route('submit') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Kirim Naskah</a>
                        @if(auth()->user()->hasAnyRole(['editor','journal_manager','super_admin']))
                        <a href="{{ route('editor.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Panel Editor</a>
                        @endif
                        @if(auth()->user()->hasRole('reviewer'))
                        <a href="{{ route('reviewer.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Panel Reviewer</a>
                        @endif
                        @if(auth()->user()->hasAnyRole(['super_admin','journal_manager','editor']))
                        <a href="/admin" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Panel Admin</a>
                        @endif
                        <div class="border-t border-slate-100 mt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endguest

                {{-- Hamburger button (mobile only) --}}
                <button @click="mobileOpen = !mobileOpen"
                        class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors"
                        aria-label="Buka menu">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu panel --}}
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden border-t border-slate-100 bg-white shadow-lg"
         style="display:none;">
        <div class="px-4 py-3 space-y-0.5">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Beranda
            </a>
            @auth
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('submit') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Kirim Naskah
            </a>
            @if(auth()->user()->hasAnyRole(['editor','journal_manager','super_admin']))
            <a href="{{ route('editor.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Panel Editor
            </a>
            @endif
            @if(auth()->user()->hasRole('reviewer'))
            <a href="{{ route('reviewer.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Panel Reviewer
            </a>
            @endif
            @if(auth()->user()->hasAnyRole(['super_admin','journal_manager','editor']))
            <a href="/admin" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Panel Admin
            </a>
            @endif
            <div class="pt-2 mt-1 border-t border-slate-100">
                <div class="px-3 py-2 mb-1">
                    <p class="text-xs font-semibold text-slate-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
            @else
            <a href="{{ route('login') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Masuk</a>
            <a href="{{ route('register') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 text-center transition-colors">Daftar Gratis</a>
            @endauth
        </div>
    </div>
</header>
</div>

{{-- ── Impersonation Banner ────────────────────────────────────────────────── --}}
@if(session('impersonating_as'))
<div style="background:#92400e;color:#fef3c7;padding:.625rem 1rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;font-size:.875rem;position:sticky;top:64px;z-index:40;">
    <div style="display:flex;align-items:center;gap:.5rem;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
        <span>Anda sedang login sebagai <strong>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</strong> ({{ auth()->user()->email }})</span>
    </div>
    <a href="{{ route('impersonate.stop') }}"
       style="background:#fef3c7;color:#92400e;padding:.3rem .875rem;border-radius:.5rem;font-weight:600;font-size:.8125rem;text-decoration:none;white-space:nowrap;display:flex;align-items:center;gap:.375rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
        Kembali ke Admin
    </a>
</div>
@endif

{{-- ── Flash Messages ──────────────────────────────────────────────────────── --}}
@if(session('success'))
<div class="bg-green-50 border-b border-green-200 px-4 py-3">
    <div class="max-w-7xl mx-auto flex items-center gap-2 text-sm text-green-800">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
</div>
@endif

@if(session('error'))
<div class="bg-red-50 border-b border-red-200 px-4 py-3">
    <div class="max-w-7xl mx-auto flex items-center gap-2 text-sm text-red-800">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v2a1 1 0 002 0V9a1 1 0 00-2 0zM10 15a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
</div>
@endif

{{-- ── Main Content ────────────────────────────────────────────────────────── --}}
<main class="min-h-[60vh]">
    {{ $slot }}
</main>

{{-- ── Footer ──────────────────────────────────────────────────────────────── --}}
<footer class="bg-slate-900 text-slate-300 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-8">

        {{-- Top grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">

            {{-- Brand --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    @if(!empty($brandLogo))
                    <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-9 w-auto object-contain">
                    @else
                    <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-black text-sm">{{ $brandAbbrev }}</span>
                    </div>
                    @endif
                    <span class="font-bold text-white text-lg">{{ $brandName }}</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed mb-5">
                    {{ $brandFooterTagline ?: 'Platform pengelolaan jurnal ilmiah Indonesia yang terbuka, aman, dan memenuhi standar internasional.' }}
                </p>
                {{-- Indexing badges --}}
                @if($brandFooterIdx)
                <div class="flex flex-wrap gap-1.5">
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-md bg-blue-950/50 border border-blue-800/50 text-blue-300">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4L22 9L12 14L2 9L12 4Z"/><path d="M4 10.5V18"/><circle cx="4" cy="19.2" r="1.2" fill="currentColor" stroke="none"/><path d="M7 12V16.5Q9.5 19.5 12 19.5Q14.5 19.5 17 16.5V12"/></svg>
                        Google Scholar
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-md bg-emerald-950/50 border border-emerald-800/50 text-emerald-300">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                        Crossref DOI
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-md bg-amber-950/50 border border-amber-800/50 text-amber-300">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="19" r="1.5" fill="currentColor" stroke="none"/><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/></svg>
                        OAI-PMH 2.0
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-md bg-violet-950/50 border border-violet-800/50 text-violet-300">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                        DOAJ Ready
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-md bg-rose-950/50 border border-rose-800/50 text-rose-300">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><circle cx="10" cy="15" r="2.5"/><path d="M12 17L14.5 19.5"/></svg>
                        PKP Index
                    </span>
                </div>
                @endif

                {{-- Social icons --}}
                @if($brandFooterSoc)
                <div class="flex items-center gap-2 mt-4">
                    @foreach([
                        'twitter'   => ['href'=>$brandSocials['twitter'],  'label'=>'Twitter/X', 'path'=>'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z'],
                        'facebook'  => ['href'=>$brandSocials['facebook'], 'label'=>'Facebook',  'path'=>'M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z'],
                        'instagram' => ['href'=>$brandSocials['instagram'],'label'=>'Instagram', 'path'=>'M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37zm1.5-4.87h.01M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2z'],
                        'linkedin'  => ['href'=>$brandSocials['linkedin'], 'label'=>'LinkedIn',  'path'=>'M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z M2 6.5a2 2 0 1 1 4 0 2 2 0 0 1-4 0z'],
                        'youtube'   => ['href'=>$brandSocials['youtube'],  'label'=>'YouTube',   'path'=>'M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z'],
                        'whatsapp'  => ['href'=>$brandSocials['whatsapp'], 'label'=>'WhatsApp',  'path'=>'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z'],
                    ] as $key => $soc)
                    @if(!empty($brandSocials[$key]))
                    <a href="{{ $soc['href'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $soc['label'] }}"
                       class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-800 text-slate-400 hover:bg-blue-600 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $soc['path'] }}"/></svg>
                    </a>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- For Readers --}}
            <div>
                <h3 class="text-sm font-semibold text-white mb-4">Untuk Pembaca</h3>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="{{ route('home') }}#journals" class="hover:text-white transition-colors">Daftar Jurnal</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar Akun</a></li>
                </ul>
            </div>

            {{-- For Authors --}}
            <div>
                <h3 class="text-sm font-semibold text-white mb-4">Untuk Penulis</h3>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    @auth
                    <li><a href="{{ route('submit') }}" class="hover:text-white transition-colors">Kirim Naskah</a></li>
                    <li><a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Dashboard Penulis</a></li>
                    @else
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar sebagai Penulis</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Kirim Naskah</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Technical --}}
            <div>
                <h3 class="text-sm font-semibold text-white mb-4">Teknis & Pengindeksan</h3>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    <li><a href="{{ route('oai') }}?verb=Identify" class="hover:text-white transition-colors">OAI-PMH 2.0</a></li>
                    <li><a href="{{ route('oai') }}?verb=ListSets" class="hover:text-white transition-colors">OAI Sets</a></li>
                    <li><a href="{{ route('sitemap') }}" class="hover:text-white transition-colors">Sitemap XML</a></li>
                    @if(auth()->check() && auth()->user()->hasAnyRole(['super_admin','journal_manager','editor']))
                    <li><a href="/admin" class="hover:text-white transition-colors">Panel Admin</a></li>
                    @endif
                </ul>
            </div>

            {{-- Custom column from settings --}}
            @if(!empty($brandFooterColTitle) && !empty($brandFooterLinks))
            <div>
                <h3 class="text-sm font-semibold text-white mb-4">{{ $brandFooterColTitle }}</h3>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    @foreach($brandFooterLinks as $link)
                    @if(!empty($link['url']) && !empty($link['label']))
                    <li><a href="{{ $link['url'] }}" class="hover:text-white transition-colors">{{ $link['label'] }}</a></li>
                    @endif
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        {{-- Contact row (jika ada) --}}
        @if(!empty($brandSocials['facebook']) || !empty($brandSocials['twitter']) || !empty(\App\Models\Setting::get('brand.contact_email')))
        @php $contactEmail = \App\Models\Setting::get('brand.contact_email'); $contactPhone = \App\Models\Setting::get('brand.contact_phone'); @endphp
        @if($contactEmail || $contactPhone)
        <div class="border-t border-slate-800 pt-5 pb-2 flex flex-wrap gap-x-6 gap-y-1 text-xs text-slate-500">
            @if($contactEmail)
            <a href="mailto:{{ $contactEmail }}" class="hover:text-slate-300 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                {{ $contactEmail }}
            </a>
            @endif
            @if($contactPhone)
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                {{ $contactPhone }}
            </span>
            @endif
        </div>
        @endif
        @endif

        {{-- Bottom bar --}}
        <div class="border-t border-slate-800 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-4 text-xs text-slate-500">
                @if(!empty($brandCopyright))
                <span>{{ $brandCopyright }}</span>
                @else
                <span>&copy; {{ date('Y') }} {{ $brandName }}. Seluruh hak dilindungi.</span>
                @endif
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-500">
                <span>Dibangun dengan</span>
                <span class="flex items-center gap-1 text-slate-400">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                    Laravel &amp; Filament
                </span>
            </div>
        </div>
    </div>
</footer>

<x-toast />
@livewireScripts
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? 'Platform jurnal ilmiah Indonesia' }}">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full bg-slate-50 antialiased text-slate-800">

{{-- ── Top Navigation ─────────────────────────────────────────────────────── --}}
<div x-data="{ open: false, mobileOpen: false }">
<header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">

            {{-- Brand --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                    <span class="text-white font-black text-xs tracking-tight">GJS</span>
                </div>
                <span class="font-semibold text-slate-900 text-base hidden sm:block">{{ config('app.name') }}</span>
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
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">🏠 Beranda</a>
            @auth
            <a href="{{ route('dashboard') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">📊 Dashboard</a>
            <a href="{{ route('submit') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">📝 Kirim Naskah</a>
            @if(auth()->user()->hasAnyRole(['editor','journal_manager','super_admin']))
            <a href="{{ route('editor.dashboard') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">✏️ Panel Editor</a>
            @endif
            @if(auth()->user()->hasRole('reviewer'))
            <a href="{{ route('reviewer.dashboard') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">🔍 Panel Reviewer</a>
            @endif
            @if(auth()->user()->hasAnyRole(['super_admin','journal_manager','editor']))
            <a href="/admin" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">⚙️ Panel Admin</a>
            @endif
            <div class="pt-2 border-t border-slate-100">
                <div class="px-3 py-1.5 mb-1">
                    <p class="text-xs font-medium text-slate-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">🚪 Keluar</button>
                </form>
            </div>
            @else
            <a href="{{ route('login') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Masuk</a>
            <a href="{{ route('register') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg text-center transition-colors">Daftar Gratis</a>
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
                    <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-black text-sm">GJS</span>
                    </div>
                    <span class="font-bold text-white text-lg">{{ config('app.name') }}</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed mb-5">
                    Platform pengelolaan jurnal ilmiah Indonesia yang terbuka, aman, dan memenuhi standar internasional.
                </p>
                {{-- Indexing badges --}}
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs bg-slate-800 border border-slate-700 text-slate-300 px-2.5 py-1 rounded-md font-medium">
                        <svg class="w-3 h-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Google Scholar
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs bg-slate-800 border border-slate-700 text-slate-300 px-2.5 py-1 rounded-md font-medium">
                        <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        Crossref DOI
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs bg-slate-800 border border-slate-700 text-slate-300 px-2.5 py-1 rounded-md font-medium">
                        <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        OAI-PMH
                    </span>
                </div>
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
                    <li>
                        <a href="{{ route('oai') }}?verb=Identify" class="hover:text-white transition-colors flex items-center gap-1.5">
                            OAI-PMH 2.0
                        </a>
                    </li>
                    <li><a href="{{ route('oai') }}?verb=ListSets" class="hover:text-white transition-colors">OAI Sets</a></li>
                    <li><a href="{{ route('sitemap') }}" class="hover:text-white transition-colors">Sitemap XML</a></li>
                    @if(auth()->check() && auth()->user()->hasAnyRole(['super_admin','journal_manager','editor']))
                    <li><a href="/admin" class="hover:text-white transition-colors">Panel Admin</a></li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-slate-800 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-4 text-xs text-slate-500">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak dilindungi.</span>
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

@livewireScripts
</body>
</html>

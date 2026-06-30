<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
    $siteName   = \App\Models\Setting::get('brand.site_name', config('app.name'));
    $faviconRaw = \App\Models\Setting::get('brand.favicon');
    $logoRaw    = \App\Models\Setting::get('brand.logo');
    $brandLogo  = $logoRaw  ? asset('storage/' . $logoRaw)   : null;
    $brandName  = $siteName;
    $brandAbbrev = mb_strtoupper(mb_substr(preg_replace('/[^A-Za-z]/', '', $siteName), 0, 3)) ?: 'GJS';
    @endphp
    <title>{{ $title ?? "Panel Penulis" }} — {{ $siteName }}</title>
    @if($faviconRaw)
    <link rel="icon" href="{{ asset('storage/' . $faviconRaw) }}">
    @else
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">
    @livewireStyles
    @vite(["resources/css/app.css", "resources/js/app.js"])
</head>
<body class="h-full antialiased text-slate-800" style="background:#f1f5f9;"
      x-data="{ sidebarOpen: true, mobileOpen: false }">

@php
$user = auth()->user();

$draftCount  = \App\Models\Submission::where('user_id', $user->id)->where('status', 'draft')->count();
$activeCount = \App\Models\Submission::where('user_id', $user->id)
    ->whereNotIn('status', ['published', 'declined', 'archived', 'draft'])->count();
$publishedCount = \App\Models\Submission::where('user_id', $user->id)->where('status', 'published')->count();

$navGroups = [
    'BERANDA' => [
        [
            'label' => 'Dashboard',
            'match' => 'dashboard/author',
            'url'   => route('dashboard.author'),
            'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'badge' => null,
        ],
    ],
    'NASKAH' => [
        [
            'label' => 'Kirim Naskah Baru',
            'match' => 'submit',
            'url'   => route('submit'),
            'icon'  => 'M12 4v16m8-8H4',
            'badge' => null,
            'highlight' => true,
        ],
        [
            'label' => 'Dalam Proses',
            'match' => null,
            'url'   => route('dashboard.author') . '?tab=active',
            'icon'  => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
            'badge' => $activeCount > 0 ? $activeCount : null,
            'badge_color' => '#3b82f6',
        ],
        [
            'label' => 'Diterbitkan',
            'match' => null,
            'url'   => route('dashboard.author') . '?tab=published',
            'icon'  => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'badge' => $publishedCount > 0 ? $publishedCount : null,
            'badge_color' => '#10b981',
        ],
        [
            'label' => 'Draft Tersimpan',
            'match' => null,
            'url'   => route('dashboard.author') . '?tab=drafts',
            'icon'  => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'badge' => $draftCount > 0 ? $draftCount : null,
            'badge_color' => '#94a3b8',
        ],
    ],
    'DOKUMEN' => [
        [
            'label' => 'LOA Saya',
            'match' => null,
            'url'   => route('dashboard.author') . '?tab=loa',
            'icon'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'badge' => null,
            'badge_color' => '#6366f1',
        ],
        [
            'label' => 'Hasil Turnitin',
            'match' => null,
            'url'   => route('dashboard.author') . '?tab=turnitin',
            'icon'  => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'badge' => null,
        ],
    ],
    'LAINNYA' => [
        [
            'label' => 'Notifikasi',
            'match' => 'notifications',
            'url'   => route('notifications.index'),
            'icon'  => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
            'badge' => null,
        ],
    ],
];
@endphp

{{-- ═══ TOP BAR ═══════════════════════════════════════════════════════════════ --}}
<header class="fixed top-0 left-0 right-0 z-50 h-14 flex items-center px-4 gap-3 shadow-lg"
        style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8);">

    {{-- Hamburger desktop --}}
    <button @click="sidebarOpen = !sidebarOpen"
            class="hidden lg:flex w-8 h-8 items-center justify-center rounded-lg transition-colors"
            style="color:#93c5fd;" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='transparent'">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    {{-- Hamburger mobile --}}
    <button @click="mobileOpen = !mobileOpen"
            class="flex lg:hidden w-8 h-8 items-center justify-center rounded-lg transition-colors"
            style="color:#93c5fd;" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='transparent'">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Brand --}}
    <a href="{{ route('dashboard.author') }}" class="flex items-center gap-2 mr-2">
        @if(!empty($brandLogo))
        <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-7 w-auto object-contain">
        @else
        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 font-black text-xs text-white"
             style="background:#2563eb;">
            {{ $brandAbbrev ?? 'GJS' }}
        </div>
        @endif
        <span class="text-white font-bold text-sm hidden sm:block">Panel Penulis</span>
    </a>

    {{-- Active badge --}}
    @if($activeCount > 0)
    <a href="{{ route('dashboard.author') }}?tab=active"
       class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold transition-colors"
       style="background:rgba(59,130,246,.25);color:#bfdbfe;border:1px solid rgba(59,130,246,.35);">
        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $activeCount }} dalam proses
    </a>
    @endif

    <div class="flex-1"></div>

    {{-- Quick submit --}}
    <a href="{{ route('submit') }}"
       class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all"
       style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.2);"
       onmouseover="this.style.background='rgba(255,255,255,.25)'"
       onmouseout="this.style.background='rgba(255,255,255,.15)'">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Kirim Naskah
    </a>

    {{-- View portal --}}
    <a href="{{ route('home') }}" target="_blank"
       class="hidden sm:flex items-center gap-1.5 text-xs transition-colors px-2 py-1 rounded"
       style="color:#93c5fd;" onmouseover="this.style.color='#fff';this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.color='#93c5fd';this.style.background='transparent'">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        Portal
    </a>

    {{-- Language Switcher --}}
    @php
        $__authLocales   = $availableLocales ?? ['id','en'];
        $__authCurLocale = $currentLocale ?? app()->getLocale();
        $__authFlags     = ['id'=>'🇮🇩','en'=>'🇬🇧','ar'=>'🇸🇦'];
        $__authShort     = ['id'=>'ID','en'=>'EN','ar'=>'AR'];
    @endphp
    @if(count($__authLocales) > 1)
    <div class="relative" x-data="{ langOpen: false }">
        <button @click="langOpen = !langOpen" @click.outside="langOpen = false"
                class="flex items-center gap-1 px-2 py-1 rounded-md text-xs font-semibold text-white border border-white/20 hover:bg-white/10 transition-colors">
            <span>{{ $__authFlags[$__authCurLocale] ?? '🌐' }}</span>
            <span>{{ $__authShort[$__authCurLocale] ?? strtoupper($__authCurLocale) }}</span>
        </button>
        <div x-show="langOpen"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50"
             style="display:none;">
            <div class="px-3 py-1.5 text-xs font-semibold text-slate-400 uppercase tracking-wide border-b border-slate-100 mb-1">Bahasa</div>
            @foreach($__authLocales as $__loc)
            <a href="{{ route('language.switch', $__loc) }}"
               class="flex items-center gap-2.5 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ $__authCurLocale === $__loc ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <span>{{ $__authFlags[$__loc] ?? '🌐' }}</span>
                <span>{{ __('site.language_'.$__loc) }}</span>
                @if($__authCurLocale === $__loc)<svg class="w-3.5 h-3.5 ml-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>@endif
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Notification bell --}}
    <livewire:notification-bell />

    {{-- User menu --}}
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center gap-2 pl-2">
            @if($user->avatar)
            <img src="{{ Storage::url($user->avatar) }}"
                 class="w-8 h-8 rounded-full object-cover"
                 style="border:2px solid rgba(255,255,255,.3);">
            @else
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                 style="background:#2563eb;">
                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
            </div>
            @endif
            <div class="hidden sm:block text-left">
                <p class="text-white text-xs font-semibold leading-tight">{{ $user->first_name }}</p>
                <p class="text-xs leading-tight" style="color:#93c5fd;">Penulis</p>
            </div>
        </button>
        <div x-show="open" @click.outside="open = false" x-cloak
             class="absolute right-0 top-10 w-48 bg-white rounded-xl shadow-xl border border-slate-200 py-1 z-50">
            <div class="px-3 py-2 border-b border-slate-100">
                <p class="text-xs font-bold text-slate-900 truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
            </div>
            <a href="{{ route('dashboard.author') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('author.profil') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profil Saya
            </a>
            @if($user->hasRole('reviewer'))
            <a href="{{ route('reviewer.dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Panel Reviewer
            </a>
            @endif
            <div class="border-t border-slate-100 my-1"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>
</header>

{{-- ═══ BODY LAYOUT ══════════════════════════════════════════════════════════ --}}
<div class="flex pt-14 min-h-screen">

    {{-- Mobile overlay --}}
    <div x-show="mobileOpen" @click="mobileOpen = false" x-cloak
         class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

    {{-- ── SIDEBAR DESKTOP ──────────────────────────────────────────────── --}}
    <aside :class="sidebarOpen ? 'w-60' : 'w-16'"
           class="fixed top-14 bottom-0 left-0 z-40 flex flex-col flex-shrink-0 overflow-y-auto overflow-x-hidden transition-all duration-200 hidden lg:flex"
           style="background:#0f172a;">

        <div class="py-1 flex-1">
            @foreach($navGroups as $group => $items)
            <div class="mt-2">
                <p x-show="sidebarOpen" x-cloak
                   class="px-4 pt-2 pb-1 text-xs font-bold tracking-widest uppercase"
                   style="color:#475569;font-size:0.62rem;">{{ $group }}</p>
                @foreach($items as $item)
                @php
                    $isActive = $item['match'] && str_starts_with(request()->path(), $item['match']);
                    $isHighlight = !empty($item['highlight']);
                @endphp
                <a href="{{ $item['url'] }}"
                   title="{{ $item['label'] }}"
                   class="flex items-center gap-3 mx-2 my-0.5 px-3 py-2 rounded-lg text-sm font-medium transition-all relative"
                   style="{{ $isActive ? 'background:#1d4ed8;color:#fff;' : ($isHighlight ? 'background:rgba(29,78,216,.2);color:#60a5fa;' : 'color:#94a3b8;') }}"
                   onmouseover="{{ $isActive ? '' : "this.style.background='#1e293b';this.style.color='#e2e8f0'" }}"
                   onmouseout="{{ $isActive ? '' : ($isHighlight ? "this.style.background='rgba(29,78,216,.2)';this.style.color='#60a5fa'" : "this.style.background='transparent';this.style.color='#94a3b8'") }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate whitespace-nowrap flex-1">{{ $item['label'] }}</span>
                    @if(!empty($item['badge']))
                    <span x-show="sidebarOpen" x-cloak
                          style="background:{{ $item['badge_color'] ?? '#64748b' }};color:#fff;font-size:.625rem;font-weight:800;border-radius:.375rem;padding:.1rem .35rem;line-height:1.4;flex-shrink:0;">
                        {{ $item['badge'] }}
                    </span>
                    <span x-show="!sidebarOpen"
                          style="position:absolute;top:.35rem;right:.35rem;width:.5rem;height:.5rem;border-radius:50%;background:{{ $item['badge_color'] ?? '#64748b' }};display:block;flex-shrink:0;"></span>
                    @endif
                </a>
                @endforeach
            </div>
            @endforeach
        </div>

        {{-- Bottom --}}
        <div class="p-3 border-t" style="border-color:#1e293b;">
            <a href="{{ route('home') }}"
               title="Kembali ke Portal"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all"
               style="color:#64748b;"
               onmouseover="this.style.color='#94a3b8';this.style.background='#1e293b'"
               onmouseout="this.style.color='#64748b';this.style.background='transparent'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="sidebarOpen" x-cloak class="text-sm">Kembali ke Portal</span>
            </a>
        </div>
    </aside>

    {{-- ── SIDEBAR MOBILE ───────────────────────────────────────────────── --}}
    <aside x-show="mobileOpen" @click.outside="mobileOpen = false" x-cloak
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0 -translate-x-full"
           x-transition:enter-end="opacity-100 translate-x-0"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100 translate-x-0"
           x-transition:leave-end="opacity-0 -translate-x-full"
           class="fixed top-14 bottom-0 left-0 z-40 w-64 overflow-y-auto flex flex-col lg:hidden"
           style="background:#0f172a;">

        <div class="flex items-center gap-3 mx-3 my-3 px-3 py-2.5 rounded-xl"
             style="background:rgba(29,78,216,.2);border:1px solid rgba(59,130,246,.25);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                 style="background:#1d4ed8;">
                {{ strtoupper(substr($user->first_name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-white truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                <p class="text-xs" style="color:#93c5fd;">Penulis</p>
            </div>
        </div>

        <div class="py-1 flex-1">
            @foreach($navGroups as $group => $items)
            <div class="mt-2">
                <p class="px-4 pt-2 pb-1 text-xs font-bold tracking-widest uppercase" style="color:#475569;font-size:0.62rem;">{{ $group }}</p>
                @foreach($items as $item)
                @php $isActive = $item['match'] && str_starts_with(request()->path(), $item['match']); @endphp
                <a href="{{ $item['url'] }}" @click="mobileOpen = false"
                   class="flex items-center gap-3 mx-2 my-0.5 px-3 py-2 rounded-lg text-sm font-medium transition-all"
                   style="{{ $isActive ? 'background:#1d4ed8;color:#fff;' : 'color:#94a3b8;' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="truncate flex-1">{{ $item['label'] }}</span>
                    @if(!empty($item['badge']))
                    <span style="background:{{ $item['badge_color'] ?? '#64748b' }};color:#fff;font-size:.625rem;font-weight:800;border-radius:.375rem;padding:.1rem .35rem;line-height:1.4;">
                        {{ $item['badge'] }}
                    </span>
                    @endif
                </a>
                @endforeach
            </div>
            @endforeach
        </div>

        <div class="p-3 border-t" style="border-color:#1e293b;">
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all"
               style="color:#64748b;">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-sm">Kembali ke Portal</span>
            </a>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ─────────────────────────────────────────────────── --}}
    <main :class="sidebarOpen ? 'lg:ml-60' : 'lg:ml-16'"
          class="flex-1 min-w-0 transition-all duration-200 overflow-x-hidden">
        {{ $slot }}
    </main>

</div>

<x-toast />
@livewireScripts
</body>
</html>

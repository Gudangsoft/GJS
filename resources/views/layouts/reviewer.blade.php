<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? "Panel Reviewer" }} — {{ config("app.name") }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">
    @livewireStyles
    @vite(["resources/css/app.css", "resources/js/app.js"])
</head>
<body class="h-full antialiased text-slate-800" style="background:#f1f5f9;"
      x-data="{ sidebarOpen: true, mobileOpen: false }">

@php
$user      = auth()->user();
$pendingCount = \App\Models\ReviewAssignment::where('reviewer_id', $user->id)
    ->where('status', 'awaiting_response')->count();
$activeCount  = \App\Models\ReviewAssignment::where('reviewer_id', $user->id)
    ->where('status', 'accepted')->count();
$overdueCount = \App\Models\ReviewAssignment::where('reviewer_id', $user->id)
    ->where('status', 'accepted')
    ->whereNotNull('date_due')
    ->where('date_due', '<', now())
    ->count();

$navGroups = [
    'BERANDA' => [
        [
            'label' => 'Dashboard',
            'match' => 'reviewer/dashboard',
            'url'   => route('reviewer.dashboard'),
            'icon'  => 'M3 7h18M3 12h18M3 17h7',
            'badge' => null,
        ],
    ],
    'PENUGASAN REVIEW' => [
        [
            'label' => 'Undangan',
            'match' => null,
            'url'   => route('reviewer.dashboard') . '?tab=pending',
            'icon'  => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'badge' => $pendingCount > 0 ? $pendingCount : null,
            'badge_color' => '#f59e0b',
        ],
        [
            'label' => 'Sedang Berjalan',
            'match' => 'reviewer/assignments',
            'url'   => route('reviewer.dashboard') . '?tab=active',
            'icon'  => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'badge' => $activeCount > 0 ? $activeCount : null,
            'badge_color' => $overdueCount > 0 ? '#ef4444' : '#38bdf8',
        ],
        [
            'label' => 'Selesai',
            'match' => null,
            'url'   => route('reviewer.dashboard') . '?tab=completed',
            'icon'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'badge' => null,
        ],
    ],
    'NOTIFIKASI' => [
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
        style="background:#064e3b;">

    {{-- Hamburger desktop --}}
    <button @click="sidebarOpen = !sidebarOpen"
            class="hidden lg:flex w-8 h-8 items-center justify-center rounded-lg transition-colors"
            style="color:#6ee7b7;" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='transparent'">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    {{-- Hamburger mobile --}}
    <button @click="mobileOpen = !mobileOpen"
            class="flex lg:hidden w-8 h-8 items-center justify-center rounded-lg transition-colors"
            style="color:#6ee7b7;" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='transparent'">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Brand --}}
    <a href="{{ route('reviewer.dashboard') }}" class="flex items-center gap-2 mr-2">
        @if(!empty($brandLogo))
        <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-7 w-auto object-contain">
        @else
        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 font-black text-xs text-white"
             style="background:#059669;">
            {{ $brandAbbrev ?? 'GJS' }}
        </div>
        @endif
        <span class="text-white font-bold text-sm hidden sm:block">Panel Reviewer</span>
    </a>

    {{-- Overdue badge --}}
    @if($overdueCount > 0)
    <a href="{{ route('reviewer.dashboard') }}"
       class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold transition-colors"
       style="background:rgba(239,68,68,.2);color:#fca5a5;border:1px solid rgba(239,68,68,.3);">
        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H2.645c-1.73 0-2.813-1.874-1.948-3.374L10.052 3.378c.866-1.5 3.032-1.5 3.898 0l7.353 12.748zM12 15.75h.008v.008H12v-.008z"/>
        </svg>
        {{ $overdueCount }} overdue
    </a>
    @endif

    <div class="flex-1"></div>

    {{-- View portal --}}
    <a href="{{ route('home') }}" target="_blank"
       class="hidden sm:flex items-center gap-1.5 text-xs transition-colors px-2 py-1 rounded"
       style="color:#6ee7b7;" onmouseover="this.style.color='#fff';this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.color='#6ee7b7';this.style.background='transparent'">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        Portal
    </a>

    {{-- Notification bell --}}
    <livewire:notification-bell />

    {{-- User menu --}}
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center gap-2 pl-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                 style="background:#059669;">
                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
            </div>
            <div class="hidden sm:block text-left">
                <p class="text-white text-xs font-semibold leading-tight">{{ $user->first_name }}</p>
                <p class="text-xs leading-tight" style="color:#6ee7b7;">Reviewer</p>
            </div>
        </button>
        <div x-show="open" @click.outside="open = false" x-cloak
             class="absolute right-0 top-10 w-48 bg-white rounded-xl shadow-xl border border-slate-200 py-1 z-50">
            <div class="px-3 py-2 border-b border-slate-100">
                <p class="text-xs font-bold text-slate-900 truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
            </div>
            <a href="{{ route('reviewer.dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h7"/></svg>
                Dashboard
            </a>
            <a href="{{ route('dashboard.author') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Panel Penulis
            </a>
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
           style="background:#1e293b;">

        {{-- Reviewer mini profile --}}
        <div x-show="sidebarOpen" x-cloak
             class="flex items-center gap-3 mx-3 my-3 px-3 py-2.5 rounded-xl"
             style="background:rgba(5,150,105,.15);border:1px solid rgba(5,150,105,.25);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                 style="background:#059669;">
                {{ strtoupper(substr($user->first_name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-white truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                @if($user->affiliation)
                <p class="text-xs truncate" style="color:#6ee7b7;">{{ Str::limit($user->affiliation, 22) }}</p>
                @else
                <p class="text-xs" style="color:#6ee7b7;">Reviewer</p>
                @endif
            </div>
        </div>
        {{-- Collapsed: just avatar --}}
        <div x-show="!sidebarOpen"
             class="flex justify-center my-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                 style="background:#059669;">
                {{ strtoupper(substr($user->first_name, 0, 1)) }}
            </div>
        </div>

        {{-- Nav items --}}
        <div class="py-1 flex-1">
            @foreach($navGroups as $group => $items)
            <div class="mt-2">
                <p x-show="sidebarOpen" x-cloak
                   class="px-4 pt-2 pb-1 text-xs font-bold tracking-widest uppercase"
                   style="color:#475569;font-size:0.62rem;">{{ $group }}</p>
                @foreach($items as $item)
                @php
                    $isActive = $item['match'] && str_starts_with(request()->path(), $item['match']);
                @endphp
                <a href="{{ $item['url'] }}"
                   title="{{ $item['label'] }}"
                   class="flex items-center gap-3 mx-2 my-0.5 px-3 py-2 rounded-lg text-sm font-medium transition-all relative"
                   style="{{ $isActive ? 'background:#059669;color:#fff;' : 'color:#94a3b8;' }}"
                   onmouseover="{{ $isActive ? '' : "this.style.background='#334155';this.style.color='#e2e8f0'" }}"
                   onmouseout="{{ $isActive ? '' : "this.style.background='transparent';this.style.color='#94a3b8'" }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate whitespace-nowrap flex-1">{{ $item['label'] }}</span>
                    @if(!empty($item['badge']))
                    <span x-show="sidebarOpen" x-cloak
                          style="background:{{ $item['badge_color'] ?? '#64748b' }};color:#fff;font-size:.625rem;font-weight:800;border-radius:.375rem;padding:.1rem .35rem;line-height:1.4;flex-shrink:0;">
                        {{ $item['badge'] }}
                    </span>
                    {{-- collapsed badge dot --}}
                    <span x-show="!sidebarOpen"
                          style="position:absolute;top:.35rem;right:.35rem;width:.5rem;height:.5rem;border-radius:50%;background:{{ $item['badge_color'] ?? '#64748b' }};display:block;flex-shrink:0;"></span>
                    @endif
                </a>
                @endforeach
            </div>
            @endforeach
        </div>

        {{-- Bottom: back to portal --}}
        <div class="p-3 border-t" style="border-color:#334155;">
            <a href="{{ route('home') }}"
               title="Kembali ke Portal"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all"
               style="color:#64748b;"
               onmouseover="this.style.color='#94a3b8';this.style.background='#334155'"
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
           style="background:#1e293b;">

        {{-- Mini profile mobile --}}
        <div class="flex items-center gap-3 mx-3 my-3 px-3 py-2.5 rounded-xl"
             style="background:rgba(5,150,105,.15);border:1px solid rgba(5,150,105,.25);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                 style="background:#059669;">
                {{ strtoupper(substr($user->first_name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-white truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                <p class="text-xs" style="color:#6ee7b7;">Reviewer</p>
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
                   style="{{ $isActive ? 'background:#059669;color:#fff;' : 'color:#94a3b8;' }}">
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

        <div class="p-3 border-t" style="border-color:#334155;">
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

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? "Pengelola Jurnal" }} - {{ config("app.name") }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">
    @livewireStyles
    @vite(["resources/css/app.css", "resources/js/app.js"])
</head>
<body class="h-full bg-slate-100 antialiased text-slate-800" x-data="{ sidebarOpen: true, mobileOpen: false }">

@php
$user = auth()->user();
if ($user->hasAnyRole(['super_admin', 'admin'])) {
    $managedJournals = \App\Models\Journal::orderBy("name")->get();
} else {
    $managedJournals = \App\Models\Journal::whereHas("managers", fn($q) => $q->where("users.id", $user->id))
        ->orWhereHas("editors", fn($q) => $q->where("users.id", $user->id))
        ->orderBy("name")->get();
}

// Pakai session untuk jurnal aktif, fallback ke yang pertama
$activeJournalId = session("manager_active_journal");
$activeJournal = $managedJournals->firstWhere("id", $activeJournalId) ?? $managedJournals->first();
@endphp

{{-- TOP BAR --}}
<header style="background:#1e3a5f;" class="fixed top-0 left-0 right-0 z-50 h-14 flex items-center px-4 gap-3 shadow-lg">

    <button @click="sidebarOpen = !sidebarOpen"
            class="hidden lg:flex w-8 h-8 items-center justify-center rounded-lg text-blue-200 hover:bg-blue-800 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <button @click="mobileOpen = !mobileOpen"
            class="flex lg:hidden w-8 h-8 items-center justify-center rounded-lg text-blue-200 hover:bg-blue-800 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <a href="{{ route("manager.dashboard") }}" class="flex items-center gap-2 mr-2">
        <div class="w-7 h-7 bg-blue-500 rounded-lg flex items-center justify-center shrink-0">
            <span class="text-white font-black text-xs">GJS</span>
        </div>
        <span class="text-white font-bold text-sm hidden sm:block">Panel Pengelola</span>
    </a>

    @if($activeJournal)
    @if($managedJournals->count() > 1)
    {{-- Dropdown switcher jurnal --}}
    <div x-data="{ jOpen: false }" class="relative">
        <button @click="jOpen = !jOpen"
                class="flex items-center gap-2 px-3 py-1.5 rounded-lg border transition-colors hover:bg-blue-800/50"
                style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.2);">
            @if($activeJournal->logo)
            <img src="{{ Storage::disk("public")->url($activeJournal->logo) }}" class="w-5 h-5 rounded object-cover" alt="">
            @else
            <div class="w-5 h-5 rounded bg-blue-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                {{ strtoupper(substr($activeJournal->name_abbrev ?: $activeJournal->name, 0, 1)) }}
            </div>
            @endif
            <span class="text-white text-xs font-semibold max-w-28 truncate">{{ $activeJournal->name_abbrev ?: Str::limit($activeJournal->name, 20) }}</span>
            <svg class="w-3.5 h-3.5 text-blue-200 shrink-0 transition-transform" :class="jOpen ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="jOpen" @click.outside="jOpen = false" x-cloak x-transition
             class="absolute left-0 top-11 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-2 z-50">
            <p class="px-3 pb-1.5 text-xs font-bold text-slate-400 uppercase tracking-widest">Jurnal yang Dikelola</p>
            @foreach($managedJournals as $jItem)
            <form method="POST" action="{{ route("manager.switch-journal") }}">
                @csrf
                <input type="hidden" name="journal_id" value="{{ $jItem->id }}">
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-left transition-colors hover:bg-slate-50
                               {{ $jItem->id === $activeJournal->id ? "bg-blue-50" : "" }}">
                    @if($jItem->logo)
                    <img src="{{ Storage::disk("public")->url($jItem->logo) }}" class="w-8 h-8 rounded-lg object-cover shrink-0" alt="">
                    @else
                    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr($jItem->name_abbrev ?: $jItem->name, 0, 2)) }}
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-900 truncate text-sm">{{ $jItem->name_abbrev ?: $jItem->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Str::limit($jItem->name, 35) }}</p>
                    </div>
                    @if($jItem->id === $activeJournal->id)
                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    @endif
                </button>
            </form>
            @endforeach
        </div>
    </div>
    @else
    {{-- Hanya 1 jurnal — tampilkan statis tanpa dropdown --}}
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border" style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.2);">
        @if($activeJournal->logo)
        <img src="{{ Storage::disk("public")->url($activeJournal->logo) }}" class="w-5 h-5 rounded object-cover" alt="">
        @else
        <div class="w-5 h-5 rounded bg-blue-500 flex items-center justify-center text-white text-xs font-bold">
            {{ strtoupper(substr($activeJournal->name_abbrev ?: $activeJournal->name, 0, 1)) }}
        </div>
        @endif
        <span class="text-white text-xs font-semibold">{{ $activeJournal->name_abbrev ?: Str::limit($activeJournal->name, 25) }}</span>
    </div>
    @endif
    @endif

    <div class="flex-1"></div>

    @if($activeJournal)
    <a href="{{ route("journals.home", $activeJournal->slug) }}" target="_blank"
       class="hidden sm:flex items-center gap-1.5 text-xs text-blue-200 hover:text-white transition-colors px-2 py-1 rounded hover:bg-blue-800">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        Lihat Jurnal
    </a>
    @endif

    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center gap-2 pl-2">
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">
                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? "", 0, 1)) }}
            </div>
            <div class="hidden sm:block text-left">
                <p class="text-white text-xs font-semibold leading-tight">{{ $user->first_name }}</p>
                <p class="text-blue-300 text-xs leading-tight">{{ $user->roles->first()?->name }}</p>
            </div>
        </button>
        <div x-show="open" @click.outside="open = false" x-cloak
             class="absolute right-0 top-10 w-44 bg-white rounded-xl shadow-xl border border-slate-200 py-1 z-50">
            <a href="{{ route("home") }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Beranda Portal
            </a>
            <div class="border-t border-slate-100 my-1"></div>
            <form method="POST" action="{{ route("logout") }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>
</header>

{{-- LAYOUT --}}
<div class="flex pt-14 min-h-screen">

    {{-- Mobile overlay --}}
    <div x-show="mobileOpen" @click="mobileOpen = false" x-cloak
         class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

    {{-- SIDEBAR DESKTOP --}}
    <aside :class="sidebarOpen ? `w-60` : `w-16`"
           class="fixed top-14 bottom-0 left-0 z-40 flex flex-col flex-shrink-0 overflow-y-auto overflow-x-hidden transition-all duration-200 hidden lg:flex"
           style="background:#1e293b;">

        @if($activeJournal)
        @php
        $jId = $activeJournal->id;

        $navGroups = [
            "BERANDA" => [
                ["label"=>"Dashboard","icon"=>"M3 7h18M3 12h18M3 17h18","url"=>route("manager.dashboard"),"match"=>"manager/dashboard"],
            ],
            "EDITORIAL" => [
                ["label"=>"Submission","icon"=>"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z","url"=>route("manager.submissions"),"match"=>"manager/submissions"],
                ["label"=>"Penugasan Review","icon"=>"M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4","url"=>route("manager.reviews"),"match"=>"manager/reviews"],
            ],
            "TERBITAN" => [
                ["label"=>"Terbitan (Issue)","icon"=>"M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253","url"=>route("manager.issues"),"match"=>"manager/issues"],
                ["label"=>"Letter of Acceptance","icon"=>"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z","url"=>route("manager.loa"),"match"=>"manager/loa"],
            ],
            "PENGATURAN JURNAL" => [
                ["label"=>"Profil & Pengaturan","icon"=>"M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z","url"=>route("manager.settings"),"match"=>"manager/settings"],
                ["label"=>"Seksi / Rubrik","icon"=>"M4 6h16M4 10h16M4 14h16M4 18h7","url"=>route("manager.sections"),"match"=>"manager/sections"],
                ["label"=>"Pengumuman","icon"=>"M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z","url"=>route("manager.announcements"),"match"=>"manager/announcements"],
                ["label"=>"Plugin Sidebar","icon"=>"M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z","url"=>route("manager.plugins"),"match"=>"manager/plugins"],
                ["label"=>"Halaman Jurnal","icon"=>"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z","url"=>route("manager.pages"),"match"=>"manager/pages"],
                ["label"=>"Menu Navigasi","icon"=>"M4 6h16M4 12h16M4 18h7","url"=>route("manager.menu"),"match"=>"manager/menu"],
                ["label"=>"Import dari OJS","icon"=>"M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12","url"=>route("manager.ojs-import"),"match"=>"manager/ojs-import"],
            ],
            "PENGGUNA" => [
                ["label"=>"Daftar Pengguna","icon"=>"M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z","url"=>route("manager.users"),"match"=>"manager/users"],
            ],
            "KOMUNIKASI" => [
                ["label"=>"Email Blast","icon"=>"M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z","url"=>route("manager.email-blast"),"match"=>"manager/email-blast"],
                ["label"=>"WA Blast","icon"=>"M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z","url"=>route("manager.wa-blast"),"match"=>"manager/wa-blast"],
            ],
        ];
        @endphp

        <div class="py-2 flex-1">
        @foreach($navGroups as $group => $items)
        <div class="mt-2">
            <p x-show="sidebarOpen" x-cloak
               class="px-4 pt-2 pb-1 text-xs font-bold tracking-widest uppercase"
               style="color:#475569;font-size:0.65rem;">{{ $group }}</p>
            @foreach($items as $item)
            @php $isActive = str_starts_with(request()->path(), $item["match"]); @endphp
            <a href="{{ $item["url"] }}"
               title="{{ $item["label"] }}"
               class="flex items-center gap-3 mx-2 my-0.5 px-3 py-2 rounded-lg text-sm font-medium transition-all"
               style="{{ $isActive ? "background:#1d4ed8;color:#fff;" : "color:#94a3b8;" }}"
               onmouseover="{{ $isActive ? "" : "this.style.background=`#334155`;this.style.color=`#e2e8f0`" }}"
               onmouseout="{{ $isActive ? "" : "this.style.background=`transparent`;this.style.color=`#94a3b8`" }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item["icon"] }}"/>
                </svg>
                <span x-show="sidebarOpen" x-cloak class="truncate whitespace-nowrap">{{ $item["label"] }}</span>
            </a>
            @endforeach
        </div>
        @endforeach
        </div>

        @endif

        <div class="p-3 border-t" style="border-color:#334155;">
            <a href="{{ route("home") }}" title="Kembali ke Portal"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all"
               style="color:#64748b;"
               onmouseover="this.style.color=`#94a3b8`;this.style.background=`#334155`"
               onmouseout="this.style.color=`#64748b`;this.style.background=`transparent`">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="sidebarOpen" x-cloak class="text-sm">Kembali ke Portal</span>
            </a>
        </div>
    </aside>

    {{-- SIDEBAR MOBILE --}}
    <aside x-show="mobileOpen" @click.outside="mobileOpen = false" x-cloak
           class="fixed top-14 bottom-0 left-0 z-40 w-64 overflow-y-auto flex flex-col lg:hidden"
           style="background:#1e293b;">
        @if($activeJournal)
        <div class="py-2 flex-1">
        @foreach($navGroups as $group => $items)
        <div class="mt-2">
            <p class="px-4 pt-2 pb-1 text-xs font-bold tracking-widest uppercase" style="color:#475569;font-size:0.65rem;">{{ $group }}</p>
            @foreach($items as $item)
            @php $isActive = str_starts_with(request()->path(), $item["match"]); @endphp
            <a href="{{ $item["url"] }}" @click="mobileOpen = false"
               class="flex items-center gap-3 mx-2 my-0.5 px-3 py-2 rounded-lg text-sm font-medium transition-all"
               style="{{ $isActive ? "background:#1d4ed8;color:#fff;" : "color:#94a3b8;" }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item["icon"] }}"/>
                </svg>
                <span class="truncate">{{ $item["label"] }}</span>
            </a>
            @endforeach
        </div>
        @endforeach
        </div>
        @endif
    </aside>

    {{-- MAIN --}}
    <main :class="sidebarOpen ? `lg:ml-60` : `lg:ml-16`"
          class="flex-1 min-w-0 transition-all duration-200 overflow-x-hidden">
        {{ $slot }}
    </main>

</div>

<x-toast />
@livewireScripts
</body>
</html>
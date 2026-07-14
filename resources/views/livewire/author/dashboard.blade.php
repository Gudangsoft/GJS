<div class="p-6 lg:p-8 max-w-6xl mx-auto">

    {{-- ── HEADER ─────────────────────────────────────────────────────── --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Selamat datang, {{ auth()->user()->first_name }}!</h1>
            <p class="text-sm text-slate-500 mt-0.5">Panel Penulis · {{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <a href="{{ route('submit') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg shadow-blue-500/30 transition-all hover:shadow-blue-500/50 hover:-translate-y-0.5"
           style="background:linear-gradient(135deg,#1d4ed8,#2563eb);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Kirim Naskah Baru
        </a>
    </div>

    {{-- ── STAT CARDS ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
        $stats = [
            [
                'label'   => 'Dalam Proses',
                'count'   => $active->count(),
                'icon'    => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'gradient'=> 'linear-gradient(135deg,#1d4ed8,#3b82f6)',
                'shadow'  => 'rgba(59,130,246,.25)',
                'tab'     => 'active',
            ],
            [
                'label'   => 'Diterbitkan',
                'count'   => $published->count(),
                'icon'    => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                'gradient'=> 'linear-gradient(135deg,#059669,#10b981)',
                'shadow'  => 'rgba(16,185,129,.25)',
                'tab'     => 'published',
            ],
            [
                'label'   => 'Draft',
                'count'   => $drafts->count(),
                'icon'    => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                'gradient'=> 'linear-gradient(135deg,#475569,#64748b)',
                'shadow'  => 'rgba(100,116,139,.2)',
                'tab'     => 'drafts',
            ],
            [
                'label'   => 'Ditolak',
                'count'   => $declined->count(),
                'icon'    => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                'gradient'=> 'linear-gradient(135deg,#b91c1c,#ef4444)',
                'shadow'  => 'rgba(239,68,68,.2)',
                'tab'     => 'declined',
            ],
        ];
        @endphp

        @foreach($stats as $s)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden transition-all hover:-translate-y-0.5 hover:shadow-md cursor-pointer"
             onclick="document.querySelectorAll('[data-tab]').forEach(t => t.classList.remove('active-tab')); document.querySelector('[data-tab={{ $s['tab'] }}]')?.click();">
            <div class="px-5 py-4">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">{{ $s['label'] }}</p>
                        <p class="text-3xl font-black text-slate-900">{{ $s['count'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                         style="background:{{ $s['gradient'] }};box-shadow:0 4px 12px {{ $s['shadow'] }};">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $s['icon'] }}"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="h-1" style="background:{{ $s['gradient'] }};"></div>
        </div>
        @endforeach
    </div>

    {{-- ── SUBMISSION TABS ─────────────────────────────────────────────── --}}
    <div x-data="{
        tab: (new URLSearchParams(window.location.search)).get('tab') || 'active',
        setTab(t) {
            this.tab = t;
            const url = new URL(window.location);
            url.searchParams.set('tab', t);
            history.replaceState(null, '', url);
        }
    }">
        {{-- Tab Bar --}}
        <div class="flex flex-wrap gap-1 p-1 rounded-xl mb-6" style="background:#e2e8f0;">
            @foreach([
                ['active',    'Dalam Proses', $active->count(),    '#3b82f6', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['published', 'Diterbitkan',  $published->count(), '#10b981', 'M5 13l4 4L19 7'],
                ['drafts',    'Draft',        $drafts->count(),    '#94a3b8', 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                ['declined',  'Ditolak',      $declined->count(),  '#ef4444', 'M6 18L18 6M6 6l12 12'],
                ['loa',       'LOA',          $loas->count(),      '#6366f1', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['turnitin',  'Turnitin',     $turnitin->count(),  '#f59e0b', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ] as [$key, $label, $count, $color, $icon])
            <button data-tab="{{ $key }}"
                    @click="setTab('{{ $key }}')"
                    :class="tab === '{{ $key }}' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500 hover:text-slate-700'"
                    class="flex items-center gap-2 flex-1 justify-center px-3 py-2.5 rounded-lg text-sm font-semibold transition-all">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
                <span class="hidden sm:inline">{{ $label }}</span>
                @if($count > 0)
                <span :style="tab === '{{ $key }}' ? 'background:{{ $color }};color:#fff' : 'background:#cbd5e1;color:#64748b'"
                      class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-bold transition-all">
                    {{ $count }}
                </span>
                @endif
            </button>
            @endforeach
        </div>

        {{-- ── TAB: Dalam Proses ──────────────────────────────────────── --}}
        <div x-show="tab === 'active'" x-cloak>
            @forelse($active as $sub)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-3 overflow-hidden transition-all hover:shadow-md hover:border-blue-100">
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        {{-- Status dot --}}
                        <div class="mt-1 w-2.5 h-2.5 rounded-full shrink-0"
                             style="background:
                                @if(in_array($sub->status, ['under_review','review'])) #f59e0b
                                @elseif($sub->status === 'revision_required') #ef4444
                                @elseif(in_array($sub->status, ['accepted_for_review','accepted','copyediting','production','galley'])) #10b981
                                @else #3b82f6
                                @endif;
                             margin-top:.35rem;"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-bold text-slate-900 leading-snug hover:text-blue-700 transition-colors">
                                    <a href="{{ route('submissions.show', $sub->id) }}">
                                        {{ $sub->title ?: '(Tanpa Judul)' }}
                                    </a>
                                </h3>
                                <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full"
                                      style="
                                        @if(in_array($sub->status, ['under_review','review'])) background:#fef3c7;color:#b45309;
                                        @elseif($sub->status === 'revision_required') background:#fee2e2;color:#b91c1c;
                                        @elseif(in_array($sub->status, ['accepted_for_review','accepted','copyediting','production','galley'])) background:#d1fae5;color:#065f46;
                                        @else background:#dbeafe;color:#1e40af;
                                        @endif
                                      ">
                                    {{ ucfirst(str_replace('_', ' ', $sub->status)) }}
                                </span>
                            </div>
                            <div class="flex items-center flex-wrap gap-x-4 gap-y-1 mt-2">
                                @if($sub->journal)
                                <span class="text-xs text-slate-500 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    {{ $sub->journal->name }}
                                </span>
                                @endif
                                @if($sub->section)
                                <span class="text-xs text-slate-400">{{ $sub->section->title }}</span>
                                @endif
                                @if($sub->submitted_at)
                                <span class="text-xs text-slate-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $sub->submitted_at->diffForHumans() }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <a href="{{ route('submissions.show', $sub->id) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all"
                           style="background:#1d4ed8;"
                           onmouseover="this.style.background='#1e40af'" onmouseout="this.style.background='#1d4ed8'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-100">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
                     style="background:#eff6ff;">
                    <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="font-bold text-slate-500 mb-1">Tidak ada naskah dalam proses</p>
                <a href="{{ route('submit') }}" class="text-sm text-blue-600 font-semibold hover:underline">
                    Kirim naskah pertama Anda →
                </a>
            </div>
            @endforelse
        </div>

        {{-- ── TAB: Diterbitkan ───────────────────────────────────────── --}}
        <div x-show="tab === 'published'" x-cloak>
            @forelse($published as $sub)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-3 overflow-hidden transition-all hover:shadow-md hover:border-emerald-100">
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="mt-1 w-2.5 h-2.5 rounded-full shrink-0 bg-emerald-500" style="margin-top:.35rem;"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-bold text-slate-900 leading-snug">
                                    @if($sub->article?->issue)
                                    <a href="{{ route('journals.articles.show', [$sub->journal->slug, $sub->article->id]) }}"
                                       class="hover:text-emerald-700 transition-colors">
                                        {{ $sub->title }}
                                    </a>
                                    @else
                                    {{ $sub->title }}
                                    @endif
                                </h3>
                                <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full"
                                      style="background:#d1fae5;color:#065f46;">Diterbitkan</span>
                            </div>
                            <div class="flex items-center flex-wrap gap-x-4 gap-y-1 mt-2">
                                @if($sub->journal)
                                <span class="text-xs text-slate-500">{{ $sub->journal->name }}</span>
                                @endif
                                @if($sub->article?->issue)
                                <span class="text-xs text-slate-400">{{ $sub->article->issue->getLabel() }}</span>
                                @endif
                                @if($sub->article?->doi)
                                <a href="https://doi.org/{{ $sub->article->doi }}" target="_blank"
                                   class="text-xs text-slate-400 hover:text-blue-600 font-mono transition-colors">
                                    doi:{{ $sub->article->doi }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($sub->article?->issue)
                    <div class="mt-4">
                        <a href="{{ route('journals.articles.show', [$sub->journal->slug, $sub->article->id]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                           style="background:#d1fae5;color:#065f46;"
                           onmouseover="this.style.background='#a7f3d0'" onmouseout="this.style.background='#d1fae5'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Lihat Artikel
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-100">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#ecfdf5;">
                    <svg class="w-8 h-8 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="font-bold text-slate-500">Belum ada artikel yang diterbitkan</p>
            </div>
            @endforelse
        </div>

        {{-- ── TAB: Draft ─────────────────────────────────────────────── --}}
        <div x-show="tab === 'drafts'" x-cloak>
            @forelse($drafts as $sub)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-3 overflow-hidden transition-all hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="mt-1 w-2.5 h-2.5 rounded-full shrink-0 bg-slate-300" style="margin-top:.35rem;"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-bold text-slate-900 leading-snug">
                                    {{ $sub->title ?: '(Draft tanpa judul)' }}
                                </h3>
                                <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full"
                                      style="background:#f1f5f9;color:#64748b;">Draft</span>
                            </div>
                            <div class="flex items-center flex-wrap gap-x-4 gap-y-1 mt-2">
                                @if($sub->journal)
                                <span class="text-xs text-slate-500">{{ $sub->journal->name }}</span>
                                @endif
                                <span class="text-xs text-slate-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Diperbarui {{ $sub->updated_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <a href="{{ route('submit') }}?draft={{ $sub->id }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                           style="background:#f1f5f9;color:#334155;"
                           onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Lanjutkan
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-100">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#f8fafc;">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <p class="font-bold text-slate-500">Tidak ada draft tersimpan</p>
            </div>
            @endforelse
        </div>

        {{-- ── TAB: Ditolak ───────────────────────────────────────────── --}}
        <div x-show="tab === 'declined'" x-cloak>
            @forelse($declined as $sub)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-3 overflow-hidden transition-all hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="mt-1 w-2.5 h-2.5 rounded-full shrink-0 bg-red-400" style="margin-top:.35rem;"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-bold text-slate-700 leading-snug">
                                    {{ $sub->title ?: '(Tanpa Judul)' }}
                                </h3>
                                <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full"
                                      style="background:#fee2e2;color:#b91c1c;">Ditolak</span>
                            </div>
                            <div class="flex items-center flex-wrap gap-x-4 gap-y-1 mt-2">
                                @if($sub->journal)
                                <span class="text-xs text-slate-500">{{ $sub->journal->name }}</span>
                                @endif
                                @if($sub->submitted_at)
                                <span class="text-xs text-slate-400">{{ $sub->submitted_at->translatedFormat('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-100">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fff5f5;">
                    <svg class="w-8 h-8 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="font-bold text-slate-500">Tidak ada submission yang ditolak</p>
            </div>
            @endforelse
        </div>

        {{-- ── TAB: LOA ───────────────────────────────────────────────── --}}
        <div x-show="tab === 'loa'" x-cloak>
            @forelse($loas as $loa)
            <div class="bg-white rounded-2xl border border-indigo-100 shadow-sm mb-4 overflow-hidden transition-all hover:shadow-md">
                {{-- Header stripe --}}
                <div class="px-5 py-3 flex items-center justify-between gap-3"
                     style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-bold text-white tracking-wide uppercase">Letter of Acceptance</span>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                          style="background:rgba(255,255,255,.2);color:#e0e7ff;">
                        {{ $loa->status === 'issued' ? 'Diterbitkan' : ucfirst($loa->status) }}
                    </span>
                </div>

                <div class="p-5">
                    {{-- Nomor LOA --}}
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">No. LOA:</span>
                        <span class="font-black text-indigo-700 text-sm">{{ $loa->loa_number }}</span>
                    </div>

                    {{-- Judul artikel --}}
                    <h3 class="font-bold text-slate-900 leading-snug mb-3">{{ $loa->article_title }}</h3>

                    {{-- Detail grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
                        @if($loa->journal)
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xs text-slate-400 mb-0.5">Jurnal</p>
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $loa->journal->name }}</p>
                        </div>
                        @endif
                        @if($loa->acceptance_date)
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xs text-slate-400 mb-0.5">Tanggal Diterima</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $loa->acceptance_date->translatedFormat('d M Y') }}</p>
                        </div>
                        @endif
                        @if($loa->expected_publication_date)
                        <div class="bg-emerald-50 rounded-xl p-3">
                            <p class="text-xs text-emerald-600 mb-0.5">Target Terbit</p>
                            <p class="text-sm font-semibold text-emerald-800">{{ $loa->expected_publication_date->translatedFormat('d M Y') }}</p>
                        </div>
                        @endif
                        @if($loa->volume && $loa->number)
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xs text-slate-400 mb-0.5">Volume / Nomor</p>
                            <p class="text-sm font-semibold text-slate-800">Vol. {{ $loa->volume }}, No. {{ $loa->number }} ({{ $loa->year }})</p>
                        </div>
                        @endif
                        @if($loa->issuedBy)
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xs text-slate-400 mb-0.5">Diterbitkan Oleh</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $loa->issuedBy->first_name }} {{ $loa->issuedBy->last_name }}</p>
                        </div>
                        @endif
                    </div>

                    @if($loa->notes)
                    <div class="rounded-xl p-3 mb-4 text-sm text-indigo-900 leading-relaxed" style="background:#eef2ff;">
                        {{ $loa->notes }}
                    </div>
                    @endif

                    {{-- Verification code + actions --}}
                    <div class="flex flex-wrap items-center gap-3 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2 font-mono text-xs text-slate-400 bg-slate-50 px-3 py-1.5 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            {{ $loa->verification_code }}
                        </div>
                        <div class="flex gap-2 ml-auto">
                            <a href="{{ $loa->verifyUrl() }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                               style="background:#eef2ff;color:#4338ca;"
                               onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Verifikasi
                            </a>
                            <a href="{{ route('loa.preview', $loa->id) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white transition-all"
                               style="background:linear-gradient(135deg,#4f46e5,#6366f1);"
                               onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh LOA
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-100">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#eef2ff;">
                    <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="font-bold text-slate-500 mb-1">Belum ada LOA yang diterbitkan</p>
                <p class="text-sm text-slate-400">LOA akan muncul di sini setelah naskah Anda diterima oleh editor.</p>
            </div>
            @endforelse
        </div>

        {{-- ── TAB: Turnitin ──────────────────────────────────────────── --}}
        <div x-show="tab === 'turnitin'" x-cloak>
            @if($turnitin->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-100">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fffbeb;">
                    <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="font-bold text-slate-500 mb-1">Belum ada hasil pemeriksaan kemiripan</p>
                <p class="text-sm text-slate-400">Hasil Turnitin akan muncul setelah editor melakukan pemeriksaan terhadap naskah Anda.</p>
            </div>
            @else
            {{-- Info banner --}}
            <div class="rounded-2xl p-4 mb-5 flex items-start gap-3" style="background:#fffbeb;border:1px solid #fde68a;">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-900">Panduan Skor Kemiripan</p>
                    <p class="text-xs text-amber-700 mt-0.5">
                        <span class="font-bold text-emerald-700">&lt; 15%</span> Aman ·
                        <span class="font-bold text-yellow-700">15–30%</span> Perlu direvisi ·
                        <span class="font-bold text-red-700">&gt; 30%</span> Tinggi, kemungkinan ditolak
                    </p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($turnitin as $sub)
                @php
                    $score = $sub->similarity_score;
                    if ($score < 15) {
                        $scoreColor = '#059669'; $scoreBg = '#d1fae5'; $scoreLabel = 'Aman';
                        $barColor = 'linear-gradient(90deg,#10b981,#34d399)';
                    } elseif ($score <= 30) {
                        $scoreColor = '#b45309'; $scoreBg = '#fef3c7'; $scoreLabel = 'Perhatian';
                        $barColor = 'linear-gradient(90deg,#f59e0b,#fbbf24)';
                    } else {
                        $scoreColor = '#b91c1c'; $scoreBg = '#fee2e2'; $scoreLabel = 'Tinggi';
                        $barColor = 'linear-gradient(90deg,#ef4444,#f87171)';
                    }
                @endphp
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden transition-all hover:shadow-md">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-slate-900 leading-snug">
                                    <a href="{{ route('submissions.show', $sub->id) }}" class="hover:text-blue-700 transition-colors">
                                        {{ $sub->title }}
                                    </a>
                                </h3>
                                @if($sub->journal)
                                <p class="text-xs text-slate-500 mt-1">{{ $sub->journal->name }}</p>
                                @endif
                            </div>
                            {{-- Score badge --}}
                            <div class="shrink-0 text-center">
                                <div class="w-16 h-16 rounded-2xl flex flex-col items-center justify-center"
                                     style="background:{{ $scoreBg }};">
                                    <span class="text-2xl font-black leading-none" style="color:{{ $scoreColor }};">{{ $score }}%</span>
                                    <span class="text-xs font-bold mt-0.5" style="color:{{ $scoreColor }};">{{ $scoreLabel }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Progress bar --}}
                        <div class="mb-3">
                            <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all"
                                     style="width:{{ min($score, 100) }}%;background:{{ $barColor }};"></div>
                            </div>
                            <div class="flex justify-between text-xs text-slate-400 mt-1">
                                <span>0%</span>
                                <span>15%</span>
                                <span>30%</span>
                                <span>100%</span>
                            </div>
                        </div>

                        {{-- Meta --}}
                        <div class="flex items-center flex-wrap gap-4 text-xs text-slate-400 pt-3 border-t border-slate-100">
                            @if($sub->similarity_checked_at)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Diperiksa {{ $sub->similarity_checked_at->translatedFormat('d M Y') }}
                            </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ ucfirst(str_replace('_', ' ', $sub->status)) }}
                            </span>
                            <a href="{{ route('submissions.show', $sub->id) }}"
                               class="ml-auto font-semibold text-blue-600 hover:underline flex items-center gap-1">
                                Lihat Submission
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>

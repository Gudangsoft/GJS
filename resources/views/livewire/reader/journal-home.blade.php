<div>

{{-- ══════════════════════════════════════════ JOURNAL HEADER (OJS-style) ══ --}}
<div class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                <h1 class="text-2xl font-black text-slate-900 leading-snug mb-1">{{ $journal->name }}</h1>

                <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-500 mb-3">
                    @if($journal->publisher)
                    <span>{{ $journal->publisher }}</span>
                    @endif
                    @if($journal->issn_print)
                    <span class="font-mono">p-ISSN: <strong class="text-slate-700">{{ $journal->issn_print }}</strong></span>
                    @endif
                    @if($journal->issn_online)
                    <span class="font-mono">e-ISSN: <strong class="text-slate-700">{{ $journal->issn_online }}</strong></span>
                    @endif
                </div>

                @if($journal->focus_scope)
                <p class="text-sm text-slate-600 leading-relaxed mb-4 max-w-2xl line-clamp-2">
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
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        Arsip Terbitan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Sub-navigation (OJS-style top tabs) --}}
    <div class="border-t border-slate-200" style="background:#f8fafc;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-0 overflow-x-auto text-sm">
                <a href="{{ route('journals.home', $journal->slug) }}"
                   class="shrink-0 px-4 py-3 font-semibold border-b-2 transition-colors"
                   style="border-color:#1e40af;color:#1e40af;">
                    Beranda
                </a>
                <a href="{{ route('journals.issues', $journal->slug) }}"
                   class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors">
                    Arsip
                </a>
                @if($announcements->isNotEmpty())
                <a href="#pengumuman"
                   class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors">
                    Pengumuman
                </a>
                @endif
                @if($journal->focus_scope || $journal->about_journal)
                <a href="#tentang"
                   class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors">
                    Tentang
                </a>
                @endif
            </nav>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════ BODY: 2-COLUMN (OJS Layout) ══ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── MAIN AREA (left 2/3) ──────────────────────────────────────── --}}
        <div class="lg:col-span-2">

            {{-- Announcements (if any) --}}
            @if($announcements->isNotEmpty())
            <div id="pengumuman" class="mb-8">
                <h2 class="text-base font-black text-slate-800 uppercase tracking-wider pb-2 mb-4"
                    style="border-bottom:2px solid #1e40af;">
                    Pengumuman
                </h2>
                <div class="space-y-4">
                    @foreach($announcements->take(2) as $ann)
                    <div class="border border-slate-200 rounded-xl p-5 bg-white">
                        <h3 class="font-bold text-slate-900 mb-1">{{ $ann->title }}</h3>
                        <p class="text-xs text-slate-400 mb-2">{{ $ann->date_posted?->format('d F Y') }}</p>
                        @if($ann->description_short)
                        <p class="text-sm text-slate-600 leading-relaxed">{{ Str::limit($ann->description_short, 250) }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

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

                            {{-- Meta row: pages, DOI, galley buttons --}}
                            <div class="flex items-center flex-wrap gap-3">
                                @if($article->pages)
                                <span class="text-xs text-slate-400">
                                    Hal. {{ $article->pages }}
                                </span>
                                @endif
                                @if($article->doi)
                                <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                                   class="text-xs text-slate-400 hover:text-blue-600 transition-colors font-mono">
                                    https://doi.org/{{ $article->doi }}
                                </a>
                                @endif
                                {{-- Galley buttons (like OJS: [PDF] [HTML]) --}}
                                <div class="flex gap-1.5 ml-auto">
                                    <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                                       class="text-xs font-bold px-3 py-1.5 rounded border transition-colors"
                                       style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;">
                                        Abstrak
                                    </a>
                                    @foreach($article->galleys->take(3) as $galley)
                                    <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                                       class="text-xs font-bold px-3 py-1.5 rounded border transition-colors"
                                       style="background:#1e40af;border-color:#1e3a8a;color:#ffffff;">
                                        {{ strtoupper($galley->label) }}
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

            {{-- About section --}}
            @if($journal->focus_scope || $journal->about_journal)
            <div id="tentang" class="mt-8">
                <h2 class="text-base font-black text-slate-800 uppercase tracking-wider pb-2 mb-4"
                    style="border-bottom:2px solid #1e40af;">
                    Tentang Jurnal
                </h2>
                @if($journal->focus_scope)
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-slate-700 mb-2">Fokus dan Ruang Lingkup</h3>
                    <div class="text-sm text-slate-600 leading-relaxed">
                        {!! $journal->focus_scope !!}
                    </div>
                </div>
                @endif
                @if($journal->about_journal)
                <div class="text-sm text-slate-600 leading-relaxed">
                    {!! $journal->about_journal !!}
                </div>
                @endif
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

            {{-- Journal Info --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3" style="background:#1e40af;">
                    <p class="text-xs font-bold text-white uppercase tracking-widest">Informasi Jurnal</p>
                </div>
                <div class="p-4">
                    <dl class="space-y-3 text-sm">
                        @if($journal->review_mode)
                        <div class="flex items-start gap-2">
                            <dt class="text-slate-500 shrink-0 w-28">Mode Review</dt>
                            <dd class="font-medium text-slate-800">
                                {{ match($journal->review_mode) {
                                    'double_blind' => 'Double Blind',
                                    'single_blind' => 'Single Blind',
                                    'open'         => 'Terbuka',
                                    default        => $journal->review_mode,
                                } }}
                            </dd>
                        </div>
                        @endif
                        @if($journal->issn_print)
                        <div class="flex items-start gap-2">
                            <dt class="text-slate-500 shrink-0 w-28">p-ISSN</dt>
                            <dd class="font-mono font-medium text-slate-800">{{ $journal->issn_print }}</dd>
                        </div>
                        @endif
                        @if($journal->issn_online)
                        <div class="flex items-start gap-2">
                            <dt class="text-slate-500 shrink-0 w-28">e-ISSN</dt>
                            <dd class="font-mono font-medium text-slate-800">{{ $journal->issn_online }}</dd>
                        </div>
                        @endif
                        @if($journal->primary_locale)
                        <div class="flex items-start gap-2">
                            <dt class="text-slate-500 shrink-0 w-28">Bahasa</dt>
                            <dd class="font-medium text-slate-800">{{ strtoupper($journal->primary_locale) === 'ID' ? 'Indonesia' : 'English' }}</dd>
                        </div>
                        @endif
                        @if($journal->email)
                        <div class="flex items-start gap-2">
                            <dt class="text-slate-500 shrink-0 w-28">Kontak</dt>
                            <dd><a href="mailto:{{ $journal->email }}" class="text-blue-600 hover:underline text-xs break-all">{{ $journal->email }}</a></dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

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
                            <a href="{{ route('journals.issues', $journal->slug) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Berdasarkan Penulis
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('journals.issues', $journal->slug) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h12"/></svg>
                                Berdasarkan Judul
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

            {{-- Announcements sidebar --}}
            @if($announcements->isNotEmpty())
            <div id="pengumuman" class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3" style="background:#1e40af;">
                    <p class="text-xs font-bold text-white uppercase tracking-widest">Pengumuman</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($announcements as $ann)
                    <div class="p-4">
                        <p class="font-semibold text-slate-800 text-sm mb-0.5">{{ $ann->title }}</p>
                        <p class="text-xs text-slate-400">{{ $ann->date_posted?->format('d M Y') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Submit CTA --}}
            <div class="rounded-xl p-5 text-white text-center" style="background:linear-gradient(135deg,#1e40af,#4338ca);">
                <svg class="w-8 h-8 mx-auto mb-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="font-bold text-sm mb-1">Kirim Naskah Anda</p>
                <p class="text-xs text-blue-200 mb-4">Naskah penelitian, review, dan studi kasus</p>
                @auth
                <a href="{{ route('submit') }}" class="block py-2 px-4 bg-white text-blue-800 rounded-lg text-sm font-bold hover:bg-blue-50 transition-colors">
                    Mulai Submit
                </a>
                @else
                <a href="{{ route('login') }}" class="block py-2 px-4 bg-white text-blue-800 rounded-lg text-sm font-bold hover:bg-blue-50 transition-colors">
                    Masuk untuk Submit
                </a>
                @endauth
            </div>

        </div>{{-- end sidebar --}}
    </div>
</div>

</div>

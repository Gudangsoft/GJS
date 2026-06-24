<div>
@php $totalArticles = $articles->sum(fn($s) => $s->count()); @endphp

{{-- ═══ HERO HEADER ═══ --}}
<div class="relative overflow-hidden" style="background:linear-gradient(135deg,#0c1a3a 0%,#1a3272 55%,#0e1e4a 100%);">

    {{-- Background decorations --}}
    <div style="position:absolute;inset:0;pointer-events:none;overflow:hidden;">
        <div style="position:absolute;width:700px;height:400px;top:-150px;right:-150px;background:radial-gradient(ellipse,rgba(59,130,246,.15) 0%,transparent 70%);"></div>
        <div style="position:absolute;width:350px;height:350px;bottom:-100px;left:0;background:radial-gradient(circle,rgba(139,92,246,.1) 0%,transparent 70%);"></div>
    </div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-xs mb-8 flex-wrap" style="color:rgba(148,163,184,.8);">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('journals.home', $journal->slug) }}" class="hover:text-white transition-colors truncate max-w-[140px]">{{ $journal->name_abbrev ?? $journal->name }}</a>
            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('journals.issues', $journal->slug) }}" class="hover:text-white transition-colors">Arsip Terbitan</a>
            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white font-semibold">{{ $issue->getLabel() }}</span>
        </nav>

        {{-- Issue identity --}}
        <div class="flex items-start gap-5 sm:gap-7">

            {{-- Issue icon --}}
            <div class="hidden sm:flex shrink-0 w-20 h-24 rounded-xl flex-col items-center justify-center gap-1"
                 style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);">
                <svg class="w-8 h-8" style="color:#93c5fd;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                </svg>
                @if($issue->year)
                <span class="text-xs font-bold" style="color:#93c5fd;">{{ $issue->year }}</span>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold mb-1.5" style="color:#93c5fd;">{{ $journal->name }}</p>
                <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight mb-3">
                    {{ $issue->getLabel() }}
                </h1>

                @if($issue->date_published)
                <p class="flex items-center gap-1.5 text-sm mb-5" style="color:#94a3b8;">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18"/></svg>
                    Diterbitkan {{ $issue->date_published->format('d F Y') }}
                </p>
                @endif

                {{-- Stats chips --}}
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full"
                          style="background:rgba(59,130,246,.2);border:1px solid rgba(59,130,246,.3);color:#93c5fd;">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        {{ $totalArticles }} Artikel
                    </span>
                    @if($issue->volume)
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full"
                          style="background:rgba(139,92,246,.2);border:1px solid rgba(139,92,246,.3);color:#c4b5fd;">
                        Vol. {{ $issue->volume }}
                    </span>
                    @endif
                    @if($issue->number)
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full"
                          style="background:rgba(52,211,153,.15);border:1px solid rgba(52,211,153,.25);color:#6ee7b7;">
                        No. {{ $issue->number }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        @if($issue->description)
        <div class="mt-7 pt-6" style="border-top:1px solid rgba(255,255,255,.08);">
            <p class="text-sm leading-relaxed" style="color:#94a3b8;">
                {{ Str::limit(strip_tags($issue->description), 320) }}
            </p>
        </div>
        @endif
    </div>
</div>

{{-- ═══ MAIN CONTENT ═══ --}}
<div class="bg-slate-50 min-h-screen">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-8">
        <p class="text-sm text-slate-500">
            <span class="font-semibold text-slate-800">{{ $totalArticles }}</span> artikel dalam terbitan ini
        </p>
        <a href="{{ route('journals.issues', $journal->slug) }}"
           class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Semua Terbitan
        </a>
    </div>

    {{-- Articles by section --}}
    @php $globalNum = 0; @endphp
    @forelse($articles as $sectionTitle => $sectionArticles)
    <div class="mb-10">

        {{-- Section header --}}
        @if($sectionTitle)
        <div class="flex items-center gap-3 mb-5">
            <div class="w-1 h-5 rounded-full bg-blue-600 shrink-0"></div>
            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ $sectionTitle }}</h2>
            <div class="flex-1 h-px bg-slate-200"></div>
            <span class="text-xs font-semibold text-slate-400">{{ $sectionArticles->count() }} artikel</span>
        </div>
        @endif

        <div class="space-y-3">
            @foreach($sectionArticles as $article)
            @php $globalNum++ @endphp

            <article class="group relative bg-white rounded-2xl border border-slate-200 overflow-hidden transition-all duration-200 hover:border-blue-300 hover:shadow-lg hover:-translate-y-0.5">

                {{-- Hover accent line --}}
                <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-violet-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>

                <div class="p-5 sm:p-6">
                    <div class="flex gap-4 sm:gap-5">

                        {{-- Number badge --}}
                        <div class="shrink-0 mt-0.5">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100 group-hover:bg-blue-600 transition-all duration-200">
                                <span class="text-xs font-black text-slate-400 group-hover:text-white transition-colors duration-200 leading-none">
                                    {{ str_pad($globalNum, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>

                        {{-- Article content --}}
                        <div class="flex-1 min-w-0">

                            {{-- Badges row --}}
                            <div class="flex flex-wrap items-center gap-1.5 mb-2.5">
                                @if($article->section && !$sectionTitle)
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                      style="background:#eff6ff;color:#1d4ed8;">{{ $article->section->title }}</span>
                                @endif
                                @if($article->doi)
                                <span class="text-xs font-bold px-2 py-0.5 rounded"
                                      style="background:#ede9fe;color:#5b21b6;font-family:monospace;font-size:.65rem;">DOI</span>
                                @endif
                                @if($article->date_published)
                                <span class="text-xs text-slate-400">{{ $article->date_published->format('Y') }}</span>
                                @endif
                            </div>

                            {{-- Title --}}
                            <h3 class="font-bold text-slate-900 leading-snug mb-2 text-base group-hover:text-blue-800 transition-colors duration-200">
                                <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                                   class="hover:underline underline-offset-2 decoration-blue-300">
                                    {{ $article->submission->title }}
                                </a>
                            </h3>

                            {{-- Authors --}}
                            @if($article->submission->contributors->isNotEmpty())
                            <p class="text-sm text-slate-500 mb-3 leading-snug">
                                {{ $article->submission->contributors->map(fn($c) => $c->last_name.', '.substr($c->first_name, 0, 1).'.')->join(' · ') }}
                            </p>
                            @endif

                            {{-- Abstract snippet --}}
                            @if($article->submission->abstract)
                            <p class="text-sm text-slate-400 leading-relaxed mb-4"
                               style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ Str::limit(strip_tags($article->submission->abstract), 200) }}
                            </p>
                            @endif

                            {{-- Footer row --}}
                            <div class="flex items-center gap-3 flex-wrap">

                                {{-- Pages --}}
                                @if($article->pages)
                                <span class="inline-flex items-center gap-1 text-xs text-slate-400 font-medium">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    Hal. {{ $article->pages }}
                                </span>
                                @endif

                                {{-- DOI link --}}
                                @if($article->doi)
                                <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-blue-600 transition-colors font-mono">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                                    {{ $article->doi }}
                                </a>
                                @endif

                                {{-- Views --}}
                                @if(isset($article->views) && $article->views > 0)
                                <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ number_format($article->views) }}
                                </span>
                                @endif

                                {{-- Action buttons --}}
                                <div class="ml-auto flex items-center gap-2 flex-wrap">

                                    {{-- Galley buttons — PDF opens viewer, others download --}}
                                    @foreach($article->galleys as $galley)
                                    @php
                                        $hasFile = !empty($galley->remote_url) || !empty($galley->submission_file_id);
                                        $isPdf   = $hasFile && str_contains(strtolower($galley->label), 'pdf');
                                        $href    = $isPdf
                                            ? route('journals.articles.galley.view', [$journal->slug, $article->id, $galley->id])
                                            : route('journals.articles.galley', [$journal->slug, $article->id, $galley->id]);
                                    @endphp
                                    @if($hasFile)
                                    <a href="{{ $href }}"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                                       @if($isPdf)
                                       style="background:#fee2e2;color:#b91c1c;"
                                       onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'"
                                       @else
                                       style="background:#eff6ff;color:#1d4ed8;"
                                       onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'"
                                       @endif>
                                        @if($isPdf)
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                        @else
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                        @endif
                                        {{ $galley->label }}
                                    </a>
                                    @endif
                                    @endforeach

                                    {{-- Detail button --}}
                                    <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition-all"
                                       style="background:#0f172a;color:#fff;"
                                       onmouseover="this.style.background='#1e3a8a'" onmouseout="this.style.background='#0f172a'">
                                        Baca Artikel
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            @endforeach
        </div>
    </div>
    @empty
    <div class="text-center py-20 rounded-2xl bg-white border-2 border-dashed border-slate-200">
        <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
        </div>
        <p class="font-semibold text-slate-400">Belum ada artikel di terbitan ini.</p>
        <a href="{{ route('journals.home', $journal->slug) }}"
           class="inline-flex items-center gap-1.5 mt-3 text-sm font-medium text-blue-600 hover:underline">
            Kembali ke beranda jurnal
        </a>
    </div>
    @endforelse

    {{-- Back to archive --}}
    @if($totalArticles > 0)
    <div class="mt-10 pt-8 border-t border-slate-200 flex items-center justify-between">
        <a href="{{ route('journals.issues', $journal->slug) }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-700 transition-colors">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Arsip Terbitan
        </a>
        <a href="{{ route('journals.home', $journal->slug) }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-700 transition-colors">
            Beranda Jurnal
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
    </div>
    @endif

</div>
</div>
</div>

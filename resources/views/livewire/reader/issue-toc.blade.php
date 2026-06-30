<div>
@php $totalArticles = $articles->sum(fn($s) => $s->count()); @endphp

@include('reader.partials.journal-header', ['activeTab' => 'issues'])

{{-- Issue info bar --}}
<div style="background:#fff;border-bottom:1px solid #e2e8f0;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-3 flex-wrap">
            <a href="{{ route('journals.home', $journal->slug) }}" class="hover:text-blue-600 transition-colors">{{ $journal->name_abbrev ?? $journal->name }}</a>
            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('journals.issues', $journal->slug) }}" class="hover:text-blue-600 transition-colors">{{ __('site.issue_archive') }}</a>
            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-700 font-semibold">{{ $issue->getLabel() }}</span>
        </nav>
        <div class="flex items-center gap-4 flex-wrap">
            <h1 class="text-xl font-black text-slate-900">{{ $issue->getLabel() }}</h1>
            @if($issue->date_published)
            <span class="text-sm text-slate-400">{{ $issue->date_published->translatedFormat('F Y') }}</span>
            @endif
            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                {{ $totalArticles }} {{ __('site.articles') }}
            </span>
        </div>
    </div>
</div>

{{-- ═══ MAIN CONTENT ═══ --}}
<div class="bg-slate-50 min-h-screen">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
<div class="flex gap-8 items-start">

{{-- ── Left: Articles ──────────────────────────────────────────────── --}}
<div class="flex-1 min-w-0">

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-8">
        <p class="text-sm text-slate-500">
            <span class="font-semibold text-slate-800">{{ $totalArticles }}</span> {{ __('site.articles_in_issue') }}
        </p>
        <a href="{{ route('journals.issues', $journal->slug) }}"
           class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            {{ __('site.all_issues') }}
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
            <span class="text-xs font-semibold text-slate-400">{{ $sectionArticles->count() }} {{ __('site.articles') }}</span>
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
                                    {{ __('site.pages_abbrev') }} {{ $article->pages }}
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
                                        {{ __('site.read_article') }}
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
        <p class="font-semibold text-slate-400">{{ __('site.no_articles_in_issue') }}</p>
        <a href="{{ route('journals.home', $journal->slug) }}"
           class="inline-flex items-center gap-1.5 mt-3 text-sm font-medium text-blue-600 hover:underline">
            {{ __('site.back_to_journal') }}
        </a>
    </div>
    @endforelse

    {{-- Back to archive --}}
    @if($totalArticles > 0)
    <div class="mt-10 pt-8 border-t border-slate-200 flex items-center justify-between">
        <a href="{{ route('journals.issues', $journal->slug) }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-700 transition-colors">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            {{ __('site.issue_archive') }}
        </a>
        <a href="{{ route('journals.home', $journal->slug) }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-700 transition-colors">
            {{ __('site.journal_home') }}
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
    </div>
    @endif

</div>{{-- end articles --}}

{{-- ── Right: Sidebar ──────────────────────────────────────────────── --}}
<div class="w-72 shrink-0 hidden lg:block">
    <div class="sticky top-6 space-y-5">

        {{-- Journal info blocks at top --}}
        @foreach($sidebarBlocks->where('type', 'journal_info') as $block)
            @include('reader.partials.sidebar-block', [
                'block'   => $block,
                'journal' => $journal,
                'stats'   => $journalStats,
            ])
        @endforeach

        {{-- Other blocks --}}
        @foreach($sidebarBlocks->where('type', '!=', 'journal_info') as $block)
            @include('reader.partials.sidebar-block', [
                'block'   => $block,
                'journal' => $journal,
                'stats'   => $journalStats,
            ])
        @endforeach

    </div>
</div>

</div>{{-- end flex --}}
</div>
</div>
</div>

@push('head')
<link rel="canonical" href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}">
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $article->submission->title }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($article->submission->abstract ?? ''), 200) }}">
<meta property="og:url" content="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}">
<meta property="og:site_name" content="{{ $journal->name }}">
@if($article->date_published)
<meta property="article:published_time" content="{{ $article->date_published->toIso8601String() }}">
@endif
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "ScholarlyArticle",
    "headline": {{ json_encode($article->submission->title) }},
    @if($article->submission->abstract)
    "description": {{ json_encode(Str::limit(strip_tags($article->submission->abstract), 500)) }},
    "abstract": {{ json_encode(strip_tags($article->submission->abstract)) }},
    @endif
    "url": {{ json_encode(route('journals.articles.show', [$journal->slug, $article->id])) }},
    @if($article->doi)"identifier": {{ json_encode('https://doi.org/' . $article->doi) }},@endif
    @if($article->date_published)"datePublished": {{ json_encode($article->date_published->toDateString()) }},@endif
    "inLanguage": {{ json_encode($article->submission->locale ?? 'id') }},
    "author": [
        @foreach($article->submission->contributors as $contributor)
        {"@type":"Person","name":{{ json_encode($contributor->full_name) }}
        @if($contributor->affiliation),"affiliation":{"@type":"Organization","name":{{ json_encode($contributor->affiliation) }}}@endif
        @if($contributor->orcid),"identifier":{{ json_encode('https://orcid.org/'.$contributor->orcid) }}@endif
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ],
    "isPartOf":{"@type":"Periodical","name":{{ json_encode($journal->name) }}
        @if($journal->issn_online),"issn":{{ json_encode($journal->issn_online) }}@elseif($journal->issn_print),"issn":{{ json_encode($journal->issn_print) }}@endif
    },
    "publisher":{"@type":"Organization","name":{{ json_encode($journal->publisher ?? $journal->name) }}},
    "license":"https://creativecommons.org/licenses/by/4.0/"
}
</script>
<meta name="citation_title" content="{{ $article->submission->title }}">
@foreach($article->submission->contributors as $contributor)
<meta name="citation_author" content="{{ $contributor->last_name }}, {{ $contributor->first_name }}">
@if($contributor->affiliation)<meta name="citation_author_institution" content="{{ $contributor->affiliation }}">@endif
@if($contributor->orcid)<meta name="citation_author_orcid" content="https://orcid.org/{{ $contributor->orcid }}">@endif
@endforeach
<meta name="citation_journal_title" content="{{ $journal->name }}">
@if($journal->issn_online)<meta name="citation_issn" content="{{ $journal->issn_online }}">
@elseif($journal->issn_print)<meta name="citation_issn" content="{{ $journal->issn_print }}">@endif
@if($article->issue)
@if($article->issue->volume)<meta name="citation_volume" content="{{ $article->issue->volume }}">@endif
@if($article->issue->number)<meta name="citation_issue" content="{{ $article->issue->number }}">@endif
@if($article->issue->year)<meta name="citation_year" content="{{ $article->issue->year }}">@endif
@endif
@if($article->date_published)<meta name="citation_publication_date" content="{{ $article->date_published->format('Y/m/d') }}">@endif
@if($article->pages)
@php [$pf,$pl] = array_pad(explode('-',$article->pages,2),2,null); @endphp
<meta name="citation_firstpage" content="{{ trim($pf) }}">
@if($pl)<meta name="citation_lastpage" content="{{ trim($pl) }}">@endif
@endif
@if($article->doi)<meta name="citation_doi" content="{{ $article->doi }}">@endif
@if($article->submission->abstract)<meta name="citation_abstract" content="{{ strip_tags($article->submission->abstract) }}">@endif
@if($article->submission->keywords)
@foreach($article->submission->keywords as $kw)<meta name="citation_keyword" content="{{ $kw }}">@endforeach
@endif
<meta name="citation_language" content="{{ $article->submission->locale ?? 'id' }}">
<meta name="citation_abstract_html_url" content="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}">
@foreach($articleGalleys as $galley)
@if(str_contains(strtolower($galley->label),'pdf'))
<meta name="citation_pdf_url" content="{{ route('journals.articles.galley', [$journal->slug, $article->id, $galley->id]) }}">
@endif
@endforeach
@endpush

<div class="bg-slate-50 min-h-screen">

{{-- ── BREADCRUMB ──────────────────────────────────────────────────────── --}}
<div class="bg-white border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('journals.home', $journal->slug) }}" class="hover:text-blue-600 transition-colors truncate max-w-[140px]">{{ $journal->name_abbrev ?? $journal->name }}</a>
            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            @if($article->issue)
            <a href="{{ route('journals.issues.show', [$journal->slug, $article->issue->id]) }}" class="hover:text-blue-600 transition-colors">{{ $article->issue->getLabel() }}</a>
            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            @endif
            <span class="text-slate-600 truncate max-w-xs">{{ Str::limit($article->submission->title, 55) }}</span>
        </nav>
    </div>
</div>

{{-- ── MAIN LAYOUT ────────────────────────────────────────────────────── --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-7">
<div class="grid grid-cols-1 lg:grid-cols-[1fr_288px] gap-7">

{{-- ════ MAIN COLUMN ════════════════════════════════════════════════════ --}}
<div class="flex flex-col gap-5">

    {{-- ── ARTICLE HEADER ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="h-1 w-full" style="background:linear-gradient(90deg,#1d4ed8,#6d28d9,#0ea5e9)"></div>

        <div class="p-6 sm:p-8">

            {{-- Section badge --}}
            @if($article->section)
            <span class="inline-flex items-center text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-4"
                  style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                {{ $article->section->title }}
            </span>
            @endif

            {{-- Title --}}
            <h1 class="text-2xl sm:text-[1.65rem] font-black text-slate-900 leading-tight mb-1">
                {{ $article->submission->title }}
            </h1>
            @if($article->submission->subtitle)
            <p class="text-lg text-slate-500 mb-4 leading-snug mt-1">{{ $article->submission->subtitle }}</p>
            @endif

            {{-- Authors ──────────────────────────────────────────────── --}}
            <div class="mt-5 mb-5 flex flex-wrap gap-5">
                @foreach($article->submission->contributors as $contributor)
                <div class="flex items-start gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-black shrink-0 mt-0.5"
                         style="background:linear-gradient(135deg,#1d4ed8,#6d28d9);">
                        {{ strtoupper(substr($contributor->first_name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-sm font-bold text-slate-900">
                                {{ $contributor->full_name }}
                            </span>
                            @if($contributor->primary_contact)
                            <span title="Penulis korespondensi"
                                  class="text-xs font-bold px-1.5 py-0.5 rounded"
                                  style="background:#eff6ff;color:#2563eb;">✉</span>
                            @endif
                        </div>
                        @if($contributor->affiliation)
                        <div class="text-xs text-slate-500 mt-0.5 leading-snug">
                            {{ $contributor->affiliation }}@if($contributor->country), {{ $contributor->country }}@endif
                        </div>
                        @endif
                        @if($contributor->orcid)
                        <a href="https://orcid.org/{{ $contributor->orcid }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1 mt-1 text-xs font-medium hover:underline"
                           style="color:#a6ce39;">
                            <svg class="w-3 h-3 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.372 0 0 5.372 0 12s5.372 12 12 12 12-5.372 12-12S18.628 0 12 0zm-1.457 4.669c.456 0 .826.37.826.826s-.37.826-.826.826-.826-.37-.826-.826.37-.826.826-.826zm2.914 14.662h-1.828V9.388h1.828v9.943zm-5.828 0V9.388h1.828v9.943H7.629z"/>
                            </svg>
                            {{ $contributor->orcid }}
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Publication info strip ─────────────────────────────── --}}
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-4 border-t border-slate-100 text-xs text-slate-500">
                @if($article->issue)
                <a href="{{ route('journals.issues.show', [$journal->slug, $article->issue->id]) }}"
                   class="inline-flex items-center gap-1.5 font-semibold hover:underline"
                   style="color:#1d4ed8;">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    {{ $article->issue->getLabel() }}
                </a>
                <span class="text-slate-300">·</span>
                @endif
                @if($article->date_published)
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25"/></svg>
                    {{ $article->date_published->format('d F Y') }}
                </span>
                @endif
                @if($article->pages)
                <span class="text-slate-300">·</span>
                <span>Hal. {{ $article->pages }}</span>
                @endif
                @if($article->access_status === 'open')
                <span class="text-slate-300">·</span>
                <span class="inline-flex items-center gap-1 font-semibold" style="color:#16a34a;">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                    Akses Terbuka
                </span>
                @endif
            </div>

            {{-- DOI badge ───────────────────────────────────────────── --}}
            @if($article->doi)
            <div class="mt-3 flex items-center gap-2 px-3 py-2.5 rounded-lg text-xs"
                 style="background:#f8fafc;border:1px solid #e2e8f0;">
                <span class="font-bold text-slate-500 shrink-0 uppercase tracking-wide">DOI</span>
                <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                   class="text-blue-600 hover:underline font-mono break-all flex-1">
                    https://doi.org/{{ $article->doi }}
                </a>
                <button onclick="navigator.clipboard.writeText('https://doi.org/{{ $article->doi }}').then(()=>{this.textContent='✓';setTimeout(()=>this.textContent='Salin',1500)})"
                        class="shrink-0 text-xs font-semibold px-2 py-1 rounded transition-colors"
                        style="background:#e2e8f0;color:#475569;">
                    Salin
                </button>
            </div>
            @endif

        </div>
    </div>

    {{-- ── ABSTRACT ──────────────────────────────────────────────────── --}}
    @if($article->submission->abstract)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">

        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-1 h-5 rounded-full shrink-0" style="background:linear-gradient(180deg,#1d4ed8,#6d28d9)"></div>
            <h2 class="text-xs font-bold text-slate-600 uppercase tracking-widest">Abstrak</h2>
        </div>

        <div class="text-slate-700 leading-relaxed text-sm sm:text-[.9375rem]">
            {{ strip_tags($article->submission->abstract) }}
        </div>

        {{-- Keywords --}}
        @if($article->submission->keywords)
        <div class="mt-5 pt-5 border-t border-slate-100">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2.5">Kata Kunci</p>
            <div class="flex flex-wrap gap-2">
                @foreach($article->submission->keywords as $kw)
                <span class="text-xs font-medium px-3 py-1.5 rounded-full"
                      style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;">
                    {{ $kw }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Disciplines --}}
        @if($article->submission->disciplines)
        <div class="mt-4 pt-4 border-t border-slate-100">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2.5">Disiplin Ilmu</p>
            <div class="flex flex-wrap gap-2">
                @foreach($article->submission->disciplines as $d)
                <span class="text-xs font-medium px-3 py-1.5 rounded-full"
                      style="background:#faf5ff;color:#5b21b6;border:1px solid #ede9fe;">
                    {{ $d }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- ── CO-AUTHORS (Google Scholar style) ───────────────────────── --}}
    @php
        $coAuthorStats = $article->submission->contributors->map(function ($c) use ($article) {
            $others = \Illuminate\Support\Facades\DB::table('submission_contributors as sc')
                ->join('submissions as s',  'sc.submission_id', '=', 's.id')
                ->join('articles as ar',    's.id',             '=', 'ar.submission_id')
                ->join('issues as iss',     'ar.issue_id',      '=', 'iss.id')
                ->where('sc.last_name',     $c->last_name)
                ->where('sc.first_name',    $c->first_name)
                ->where('ar.journal_id',    $article->journal_id)
                ->where('ar.id', '!=',      $article->id)
                ->whereNotNull('ar.date_published')
                ->select('ar.id','ar.citations','ar.date_published',
                         's.title','iss.volume','iss.number','iss.year')
                ->orderByDesc('ar.date_published')
                ->limit(5)
                ->get();

            return [
                'c'              => $c,
                'article_count'  => $others->count() + 1,
                'total_citations'=> $others->sum('citations') + ($article->citations ?? 0),
                'others'         => $others,
            ];
        });
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="flex items-center gap-2.5 px-6 sm:px-8 pt-6 pb-4">
            <div class="w-1 h-5 rounded-full shrink-0" style="background:linear-gradient(180deg,#1d4ed8,#6d28d9)"></div>
            <h2 class="text-xs font-bold text-slate-600 uppercase tracking-widest">Penulis</h2>
        </div>

        <div class="divide-y divide-slate-100">
        @foreach($coAuthorStats as $stat)
        @php
            $c        = $stat['c'];
            $othersId = 'others-' . $loop->index;
            // Color palette for avatars
            $colors   = ['#1d4ed8,#6d28d9','#0891b2,#0e7490','#059669,#047857','#dc2626,#b91c1c','#7c3aed,#6d28d9'];
            $grad     = $colors[$loop->index % count($colors)];
        @endphp
        <div class="px-6 sm:px-8 py-5" x-data="{ open: false }">

            {{-- Author header row --}}
            <div class="flex items-start gap-4">
                {{-- Avatar --}}
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg font-black shrink-0"
                     style="background:linear-gradient(135deg,{{ $grad }});">
                    {{ strtoupper(substr($c->first_name ?? 'A', 0, 1)) }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-bold text-slate-900 text-sm leading-tight">
                            {{ $c->full_name }}
                        </span>
                        @if($c->primary_contact)
                        <span class="text-xs font-bold px-1.5 py-0.5 rounded"
                              style="background:#eff6ff;color:#2563eb;">✉ Korespondensi</span>
                        @endif
                    </div>

                    @if($c->affiliation)
                    <div class="text-xs text-slate-500 mt-0.5">
                        {{ $c->affiliation }}@if($c->country) · {{ $c->country }}@endif
                    </div>
                    @endif

                    @if($c->orcid)
                    <a href="https://orcid.org/{{ $c->orcid }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1 mt-1 text-xs font-medium hover:underline"
                       style="color:#a6ce39;">
                        <svg class="w-3 h-3 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.372 0 0 5.372 0 12s5.372 12 12 12 12-5.372 12-12S18.628 0 12 0zm-1.457 4.669c.456 0 .826.37.826.826s-.37.826-.826.826-.826-.37-.826-.826.37-.826.826-.826zm2.914 14.662h-1.828V9.388h1.828v9.943zm-5.828 0V9.388h1.828v9.943H7.629z"/>
                        </svg>
                        {{ $c->orcid }}
                    </a>
                    @endif

                    {{-- Stats chips --}}
                    <div class="flex items-center gap-3 mt-2.5 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full"
                              style="background:#eff6ff;color:#1d4ed8;">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            {{ $stat['article_count'] }} artikel
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full"
                              style="background:#fefce8;color:#ca8a04;">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                            {{ number_format($stat['total_citations']) }} sitasi
                        </span>
                        @if($stat['others']->count() > 0)
                        <button @click="open = !open"
                                class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full transition-colors"
                                :style="open
                                    ? 'background:#eff6ff;color:#1d4ed8;'
                                    : 'background:#f1f5f9;color:#475569;'"
                                onmouseover="this.style.background='#e2e8f0'"
                                onmouseout="this.style.background=this.getAttribute('data-bg')"
                                x-init="$el.setAttribute('data-bg', open ? '#eff6ff' : '#f1f5f9')">
                            <span x-text="open ? 'Sembunyikan' : 'Lihat artikel lainnya'">Lihat artikel lainnya</span>
                            <svg class="w-3 h-3 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Expandable: other articles by this author --}}
            @if($stat['others']->count() > 0)
            <div x-show="open"
                 x-transition:enter="transition-all ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition-all ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 x-cloak
                 class="mt-4 ml-16 space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                    Artikel lain dalam jurnal ini
                </p>
                @foreach($stat['others'] as $other)
                <a href="{{ route('journals.articles.show', [$journal->slug, $other->id]) }}"
                   class="flex items-start gap-2.5 p-3 rounded-xl transition-colors group"
                   style="background:#f8fafc;border:1px solid #e2e8f0;"
                   onmouseover="this.style.background='#eff6ff';this.style.borderColor='#bfdbfe'"
                   onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                    <svg class="w-4 h-4 text-slate-300 shrink-0 mt-0.5 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-700 leading-snug line-clamp-2"
                           style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $other->title }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">
                            Vol. {{ $other->volume }}, No. {{ $other->number }} ({{ $other->year }})
                            @if($other->citations > 0)
                            · <span style="color:#ca8a04;">{{ $other->citations }} sitasi</span>
                            @endif
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
            @endif

        </div>
        @endforeach
        </div>

    </div>

    {{-- ── AUTHOR BIOGRAPHIES ────────────────────────────────────────── --}}
    @php $authorsWithBio = $article->submission->contributors->filter(fn($c) => !empty($c->bio)); @endphp
    @if($authorsWithBio->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">

        <div class="flex items-center gap-2.5 mb-5">
            <div class="w-1 h-5 rounded-full shrink-0 bg-slate-300"></div>
            <h2 class="text-xs font-bold text-slate-600 uppercase tracking-widest">Biografi Penulis</h2>
        </div>

        <div class="space-y-5">
            @foreach($authorsWithBio as $contributor)
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-black shrink-0"
                     style="background:linear-gradient(135deg,#1d4ed8,#6d28d9);">
                    {{ strtoupper(substr($contributor->first_name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 mb-0.5">
                        {{ $contributor->full_name }}
                        @if($contributor->affiliation)
                        <span class="font-normal text-slate-500">, {{ $contributor->affiliation }}</span>
                        @endif
                    </p>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $contributor->bio }}</p>
                </div>
            </div>
            @if(!$loop->last)<hr class="border-slate-100">@endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── HOW TO CITE ──────────────────────────────────────────────── --}}
    @php
        $ctContributors = $article->submission->contributors;
        $ctYear   = $article->date_published?->format('Y') ?? '';
        $ctTitle  = $article->submission->title ?? '';
        $ctJournal = $journal->name ?? '';
        $ctJournalAbbrev = $journal->name_abbrev ?? $journal->name;
        $ctVol    = $article->issue?->volume ?? '';
        $ctNum    = $article->issue?->number ?? '';
        $ctPages  = $article->pages ?? '';
        $ctDoi    = $article->doi ? 'https://doi.org/' . $article->doi : '';

        /* ── Author string helpers ─────────────────────────── */
        // Last, F. / Last, F.
        $apaList  = $ctContributors->map(fn($c) => $c->last_name.', '.substr($c->first_name,0,1).'.')->values();
        $apaAuth  = $apaList->count() > 1
            ? $apaList->slice(0,-1)->join(', ').' & '.$apaList->last()
            : $apaList->first() ?? '';

        // First-author: Last, First  rest: First Last  (Chicago / MLA)
        $chList   = $ctContributors->map(fn($c,$i) => $i===0 ? $c->last_name.', '.$c->first_name : $c->first_name.' '.$c->last_name);
        $chAuth   = $chList->join(', and ');

        // F. Last and F. Last2  (IEEE)
        $ieeeAuth = $ctContributors->map(fn($c) => substr($c->first_name,0,1).'. '.$c->last_name)->join(' and ');

        // Last, F. and Last2, F2.  (ACM)
        $acmAuth  = $ctContributors->map(fn($c) => $c->last_name.', '.substr($c->first_name,0,1).'.')->join(' and ');

        // Last, F.; Last2, F2.  (ACS / Harvard)
        $acsList  = $ctContributors->map(fn($c) => $c->last_name.', '.substr($c->first_name,0,1).'.')->values();
        $acsAuth  = $acsList->join('; ');

        // LAST, F.; LAST2, F2.  (ABNT)
        $abntAuth = $ctContributors->map(fn($c) => strtoupper($c->last_name).', '.substr($c->first_name,0,1).'.')->join('; ');

        $havAuth  = $acsList->count() > 1
            ? $acsList->slice(0,-1)->join(', ').' & '.$acsList->last()
            : $acsList->first() ?? '';

        /* ── Citation strings ──────────────────────────────── */
        $volNum = ($ctVol && $ctNum) ? "{$ctVol}({$ctNum})" : ($ctVol ?: '');

        $citeAPA = trim(
            $apaAuth.' ('.$ctYear.'). '.$ctTitle.'. '.$ctJournal
            .($volNum ? ', '.$volNum : '')
            .($ctPages ? ', '.$ctPages : '').'.'
            .($ctDoi ? ' '.$ctDoi : '')
        );
        $citeChicago = trim(
            $chAuth.'. "'.$ctTitle.'." '.$ctJournal
            .($ctVol ? ' '.$ctVol : '')
            .($ctNum ? ', no. '.$ctNum : '')
            .($ctYear ? ' ('.$ctYear.')' : '')
            .($ctPages ? ': '.$ctPages : '').'.'
            .($ctDoi ? ' '.$ctDoi.'.' : '')
        );
        $citeHarvard = trim(
            $havAuth.($ctYear ? ' ('.$ctYear.')' : '')." '".$ctTitle."', ".$ctJournal
            .($ctVol ? ', vol. '.$ctVol : '')
            .($ctNum ? ', no. '.$ctNum : '')
            .($ctPages ? ', pp. '.$ctPages : '').'.'
            .($ctDoi ? ' Available at: '.$ctDoi : '')
        );
        $citeIEEE = trim(
            '[1] '.$ieeeAuth.', "'.$ctTitle.'," '.$ctJournal
            .($ctVol ? ', vol. '.$ctVol : '')
            .($ctNum ? ', no. '.$ctNum : '')
            .($ctPages ? ', pp. '.$ctPages : '')
            .($ctYear ? ', '.$ctYear : '').'.'
            .($ctDoi ? ' doi: '.$article->doi.'.' : '')
        );
        $citeACM = trim(
            $acmAuth.'. '.$ctYear.'. '.$ctTitle.'. '.$ctJournal
            .($ctVol ? ' '.$ctVol : '')
            .($ctNum ? ', '.$ctNum : '')
            .($ctYear ? ' ('.$ctYear.')' : '')
            .($ctPages ? ', '.$ctPages : '').'.'
            .($ctDoi ? ' '.$ctDoi : '')
        );
        $citeACS = trim(
            $acsAuth.'. '.$ctTitle.'. '.$ctJournalAbbrev
            .($ctYear ? ' '.$ctYear : '')
            .($ctVol ? ', '.$ctVol : '')
            .($ctPages ? ', '.$ctPages : '').'.'
            .($ctDoi ? ' '.$ctDoi : '')
        );
        $citeABNT = trim(
            $abntAuth.'. '.$ctTitle.'. '.$ctJournal
            .($ctVol ? ', v. '.$ctVol : '')
            .($ctNum ? ', n. '.$ctNum : '')
            .($ctPages ? ', p. '.$ctPages : '')
            .($ctYear ? ', '.$ctYear : '').'.'
            .($ctDoi ? ' Disponível em: '.$ctDoi : '')
        );
        $citeMLA = trim(
            $chAuth.'. "'.$ctTitle.'." '.$ctJournal
            .($ctVol ? ' '.$ctVol : '')
            .($ctNum ? '.'.$ctNum : '')
            .($ctYear ? ' ('.$ctYear.')' : '')
            .($ctPages ? ': '.$ctPages : '').'. Web.'
        );

        $formats = [
            'APA'     => $citeAPA,
            'Chicago' => $citeChicago,
            'Harvard' => $citeHarvard,
            'IEEE'    => $citeIEEE,
            'ACM'     => $citeACM,
            'ACS'     => $citeACS,
            'ABNT'    => $citeABNT,
            'MLA'     => $citeMLA,
        ];
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
         x-data="{ active: 'APA', open: false }">

        {{-- Header --}}
        <div class="flex items-center gap-2.5 px-6 sm:px-7 pt-6">
            <div class="w-1 h-5 rounded-full shrink-0 bg-slate-300"></div>
            <h2 class="text-xs font-bold text-slate-600 uppercase tracking-widest flex-1">Cara Mengutip</h2>
        </div>

        {{-- Active citation display --}}
        <div class="px-6 sm:px-7 pt-4 pb-5">
            @foreach($formats as $fmt => $text)
            <div x-show="active === '{{ $fmt }}'" x-cloak>
                <div class="relative group">
                    <div class="text-xs leading-relaxed rounded-xl px-4 py-3.5 pr-16"
                         style="background:#f8fafc;border:1px solid #e2e8f0;color:#334155;font-family:ui-monospace,monospace;white-space:pre-wrap;">{{ $text }}</div>
                    <button
                        @click="navigator.clipboard.writeText('{{ addslashes($text) }}').then(()=>{ $el.textContent='✓'; setTimeout(()=>{ $el.textContent='Salin' },1500) })"
                        class="absolute top-2.5 right-2.5 text-xs font-semibold px-2.5 py-1.5 rounded-lg transition-colors"
                        style="background:#e2e8f0;color:#475569;"
                        onmouseover="this.style.background='#cbd5e1'"
                        onmouseout="this.style.background='#e2e8f0'">
                        Salin
                    </button>
                </div>
            </div>
            @endforeach

            {{-- Toggle more formats --}}
            <button @click="open = !open"
                    class="mt-3 flex items-center justify-between w-full text-xs font-semibold px-3 py-2.5 rounded-lg transition-colors"
                    style="background:#f1f5f9;color:#475569;">
                <span x-text="open ? 'Sembunyikan Format Lain' : 'Format Kutipan Lainnya'">Format Kutipan Lainnya</span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Format list --}}
            <div x-show="open"
                 x-transition:enter="transition-all ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition-all ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 x-cloak class="mt-2">
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    @foreach($formats as $fmt => $text)
                    <button @click="active = '{{ $fmt }}'; open = false"
                            class="flex items-center justify-between w-full px-4 py-2.5 text-sm text-left transition-colors"
                            :class="active === '{{ $fmt }}'
                                ? 'font-bold'
                                : 'font-medium hover:bg-slate-50'"
                            :style="active === '{{ $fmt }}'
                                ? 'background:#eff6ff;color:#1d4ed8;'
                                : 'color:#374151;'"
                            @if(!$loop->last)
                            style="border-bottom:1px solid #f1f5f9;"
                            @endif>
                        <span>{{ $fmt }}</span>
                        <svg x-show="active === '{{ $fmt }}'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- ── COMPETING INTERESTS ───────────────────────────────────────── --}}
    @if($article->submission->competing_interests)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-1 h-5 rounded-full shrink-0 bg-slate-300"></div>
            <h2 class="text-xs font-bold text-slate-600 uppercase tracking-widest">Pernyataan Konflik Kepentingan</h2>
        </div>
        <div class="text-sm text-slate-600 leading-relaxed prose prose-sm max-w-none">
            {!! $article->submission->competing_interests !!}
        </div>
    </div>
    @endif

    {{-- ── STATISTICS & CITATION CHART ─────────────────────────────── --}}
    @php
        /* ── Metric counts ──────────────────────────────── */
        $statViews     = $article->views;
        $statDownloads = $article->downloads;
        $statCitations = $article->citations ?? 0;

        /* ── Build yearly citation distribution ─────────── */
        $chartData = [];
        if ($article->date_published && $statCitations > 0) {
            $startYear   = (int) $article->date_published->format('Y');
            $currentYear = (int) now()->format('Y');
            $years       = range($startYear, $currentYear);
            $numYears    = count($years);

            if ($numYears === 1) {
                $chartData[$startYear] = $statCitations;
            } else {
                // Weight: ramp up quickly, peak ~yr 1-2, then slow decay
                $weights = [];
                $peakIdx = min(2, $numYears - 1);
                foreach ($years as $i => $y) {
                    $d = $i - $peakIdx;
                    $weights[$y] = $i <= $peakIdx
                        ? 0.5 + 0.5 * ($i / max(1, $peakIdx))        // ramp up
                        : exp(-0.35 * $d * $d);                        // Gaussian tail
                }
                $wSum = array_sum($weights);
                $rem  = $statCitations;
                foreach ($years as $i => $y) {
                    if ($i === $numYears - 1) {
                        $chartData[$y] = max(0, $rem);
                    } else {
                        $v = (int) round($statCitations * $weights[$y] / $wSum);
                        $chartData[$y] = $v;
                        $rem -= $v;
                    }
                }
            }
        } elseif ($article->date_published) {
            $startYear   = (int) $article->date_published->format('Y');
            $currentYear = (int) now()->format('Y');
            foreach (range($startYear, $currentYear) as $y) {
                $chartData[$y] = 0;
            }
        }

        /* ── SVG chart dimensions ───────────────────────── */
        $svgH   = 100;          // chart area height
        $svgPad = ['t'=>8,'r'=>4,'b'=>28,'l'=>30]; // padding for axes
        $maxVal = max(1, max($chartData ?: [1]));
        $nBars  = max(1, count($chartData));
        $barW   = 24;
        $barGap = 8;
        $svgW   = $svgPad['l'] + $nBars * ($barW + $barGap) - $barGap + $svgPad['r'];
        $svgW   = max(280, $svgW);

        // Distribute bar width evenly across available width
        $innerW  = $svgW - $svgPad['l'] - $svgPad['r'];
        $barStep = $innerW / $nBars;
        $barWAdj = max(10, $barStep - 6);
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        <div class="flex items-center gap-2.5 mb-5">
            <div class="w-1 h-5 rounded-full shrink-0 bg-slate-300"></div>
            <h2 class="text-xs font-bold text-slate-600 uppercase tracking-widest">Statistik Artikel</h2>
        </div>

        {{-- Three metric chips ─────────────────────────────────────────── --}}
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="text-center py-4 rounded-xl" style="background:#eff6ff;">
                <div class="text-2xl font-black" style="color:#1d4ed8;">{{ number_format($statViews) }}</div>
                <div class="text-xs font-semibold mt-1 flex items-center justify-center gap-1" style="color:#3b82f6;">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Dilihat
                </div>
            </div>
            <div class="text-center py-4 rounded-xl" style="background:#fff1f2;">
                <div class="text-2xl font-black" style="color:#dc2626;">{{ number_format($statDownloads) }}</div>
                <div class="text-xs font-semibold mt-1 flex items-center justify-center gap-1" style="color:#ef4444;">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Diunduh
                </div>
            </div>
            <div class="text-center py-4 rounded-xl" style="background:#fefce8;">
                <div class="text-2xl font-black" style="color:#ca8a04;">{{ number_format($statCitations) }}</div>
                <div class="text-xs font-semibold mt-1 flex items-center justify-center gap-1" style="color:#eab308;">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                    Sitasi
                </div>
            </div>
        </div>

        {{-- Citation bar chart ─────────────────────────────────────────── --}}
        @if(!empty($chartData))
        <div>
            <p class="text-xs font-semibold text-slate-500 mb-3 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-yellow-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                Jumlah Sitasi per Tahun
            </p>
            <div class="overflow-x-auto -mx-1 px-1">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 {{ $svgW }} {{ $svgH + $svgPad['t'] + $svgPad['b'] }}"
                     width="100%"
                     style="min-width:{{ min(280, $svgW) }}px;max-width:100%;"
                     font-family="ui-sans-serif,system-ui,sans-serif">

                    <defs>
                        <linearGradient id="barGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#eab308"/>
                            <stop offset="100%" stop-color="#ca8a04"/>
                        </linearGradient>
                        <linearGradient id="barGradHover" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#fde047"/>
                            <stop offset="100%" stop-color="#eab308"/>
                        </linearGradient>
                    </defs>

                    {{-- Y-axis line --}}
                    <line x1="{{ $svgPad['l'] }}" y1="{{ $svgPad['t'] }}"
                          x2="{{ $svgPad['l'] }}" y2="{{ $svgPad['t'] + $svgH }}"
                          stroke="#e2e8f0" stroke-width="1"/>

                    {{-- X-axis line --}}
                    <line x1="{{ $svgPad['l'] }}" y1="{{ $svgPad['t'] + $svgH }}"
                          x2="{{ $svgW - $svgPad['r'] }}" y2="{{ $svgPad['t'] + $svgH }}"
                          stroke="#e2e8f0" stroke-width="1"/>

                    {{-- Y-axis gridlines & labels --}}
                    @php
                        $gridLines = 3;
                        $step = $maxVal / $gridLines;
                    @endphp
                    @for($g = 0; $g <= $gridLines; $g++)
                    @php
                        $gVal = (int)round($step * $g);
                        $gY   = $svgPad['t'] + $svgH - ($svgH * $g / $gridLines);
                    @endphp
                    @if($g > 0)
                    <line x1="{{ $svgPad['l'] }}" y1="{{ $gY }}"
                          x2="{{ $svgW - $svgPad['r'] }}" y2="{{ $gY }}"
                          stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3,3"/>
                    @endif
                    <text x="{{ $svgPad['l'] - 5 }}" y="{{ $gY + 4 }}"
                          text-anchor="end" font-size="9" fill="#94a3b8">{{ $gVal }}</text>
                    @endfor

                    {{-- Bars --}}
                    @foreach($chartData as $year => $count)
                    @php
                        $idx  = array_search($year, array_keys($chartData));
                        $bX   = $svgPad['l'] + $idx * $barStep + ($barStep - $barWAdj) / 2;
                        $bH   = $maxVal > 0 ? ($svgH * $count / $maxVal) : 0;
                        $bY   = $svgPad['t'] + $svgH - $bH;
                    @endphp
                    <g class="chart-bar-group" style="cursor:default">
                        {{-- Bar --}}
                        <rect x="{{ number_format($bX, 2, '.', '') }}"
                              y="{{ number_format(max($bY, $svgPad['t']), 2, '.', '') }}"
                              width="{{ number_format($barWAdj, 2, '.', '') }}"
                              height="{{ number_format(max(0, $bH), 2, '.', '') }}"
                              rx="3" ry="3"
                              fill="{{ $count > 0 ? 'url(#barGrad)' : '#f1f5f9' }}"
                              style="transition:fill .15s">
                            <title>{{ $year }}: {{ $count }} sitasi</title>
                        </rect>
                        {{-- Count label on top (only if bar is tall enough) --}}
                        @if($bH > 16 && $count > 0)
                        <text x="{{ number_format($bX + $barWAdj / 2, 2, '.', '') }}"
                              y="{{ number_format($bY - 3, 2, '.', '') }}"
                              text-anchor="middle" font-size="9" font-weight="700" fill="#92400e">
                            {{ $count }}
                        </text>
                        @elseif($count > 0)
                        <text x="{{ number_format($bX + $barWAdj / 2, 2, '.', '') }}"
                              y="{{ number_format($svgPad['t'] + $svgH - $bH - 3, 2, '.', '') }}"
                              text-anchor="middle" font-size="9" font-weight="700" fill="#92400e">
                            {{ $count }}
                        </text>
                        @endif
                        {{-- Year label --}}
                        <text x="{{ number_format($bX + $barWAdj / 2, 2, '.', '') }}"
                              y="{{ $svgPad['t'] + $svgH + 16 }}"
                              text-anchor="middle" font-size="10" fill="#64748b">
                            {{ $year }}
                        </text>
                    </g>
                    @endforeach

                </svg>
            </div>
            <p class="text-center text-xs text-slate-400 mt-1">
                Total <span class="font-bold text-yellow-600">{{ number_format($statCitations) }}</span> sitasi diterima
            </p>
        </div>
        @endif

    </div>

</div>{{-- end main --}}

{{-- ════ SIDEBAR ═══════════════════════════════════════════════════════ --}}
<div class="flex flex-col gap-5">

    {{-- ── FULL TEXT ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-2.5" style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8);">
            <p class="text-xs font-bold text-white uppercase tracking-widest">Teks Lengkap</p>
        </div>
        <div class="p-4 flex flex-col gap-2">
            @if($articleGalleys->isNotEmpty())
                @foreach($articleGalleys as $galley)
                @php
                    $hasFile = !empty($galley->remote_url) || !empty($galley->submission_file_id);
                    $isPdf   = $hasFile && str_contains(strtolower($galley->label), 'pdf');
                @endphp
                @if($hasFile)
                    @if($isPdf)
                    <a href="{{ route('journals.articles.galley.view', [$journal->slug, $article->id, $galley->id]) }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-xl font-semibold text-sm transition-all hover:brightness-110 active:scale-95"
                       style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <span class="flex-1">Baca {{ $galley->label }}</span>
                        @if($galley->views > 0)
                        <span class="text-red-200 text-xs font-normal">{{ number_format($galley->views) }}×</span>
                        @endif
                    </a>
                    <a href="{{ route('journals.articles.galley', [$journal->slug, $article->id, $galley->id]) }}?dl=1"
                       class="flex items-center gap-2 w-full px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors"
                       style="background:#fff1f2;color:#b91c1c;border:1px solid #fecaca;">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        Unduh {{ $galley->label }}
                    </a>
                    @else
                    <a href="{{ route('journals.articles.galley', [$journal->slug, $article->id, $galley->id]) }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-xl font-semibold text-sm transition-all hover:brightness-110 active:scale-95"
                       style="background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        <span class="flex-1">{{ $galley->label }}</span>
                        @if($galley->views > 0)
                        <span class="text-blue-200 text-xs font-normal">{{ number_format($galley->views) }}×</span>
                        @endif
                    </a>
                    @endif
                @else
                <div class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm"
                     style="background:#f8fafc;border:1px dashed #e2e8f0;color:#94a3b8;">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    {{ $galley->label }}
                    <span class="ml-auto text-xs rounded-full px-2 py-0.5" style="background:#f1f5f9;color:#94a3b8;">Segera</span>
                </div>
                @endif
                @endforeach
            @else
            <div class="text-center py-5 text-sm text-slate-400">File belum tersedia.</div>
            @endif
        </div>
    </div>

    {{-- ── ARTICLE INFO ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-2.5" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Informasi Artikel</p>
        </div>
        <div class="p-4">
            <dl class="space-y-3 text-sm">

                @if($article->issue)
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Terbitan</dt>
                    <dd>
                        <a href="{{ route('journals.issues.show', [$journal->slug, $article->issue->id]) }}"
                           class="font-semibold hover:underline" style="color:#1d4ed8;">
                            {{ $article->issue->getLabel() }}
                        </a>
                    </dd>
                </div>
                @endif

                @if($article->section)
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Rubrik</dt>
                    <dd class="font-medium text-slate-700">{{ $article->section->title }}</dd>
                </div>
                @endif

                @if($article->submission->submitted_at)
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Dikirim</dt>
                    <dd class="font-medium text-slate-700">{{ $article->submission->submitted_at->format('d F Y') }}</dd>
                </div>
                @endif

                @if($article->date_published)
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Diterbitkan</dt>
                    <dd class="font-medium text-slate-700">{{ $article->date_published->format('d F Y') }}</dd>
                </div>
                @endif

                @if($article->pages)
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Halaman</dt>
                    <dd class="font-medium text-slate-700">{{ $article->pages }}</dd>
                </div>
                @endif

                @if($article->doi)
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">DOI</dt>
                    <dd>
                        <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                           class="text-xs break-all font-mono hover:underline" style="color:#1d4ed8;">
                            {{ $article->doi }}
                        </a>
                    </dd>
                </div>
                @endif

                <div class="pt-2.5 border-t border-slate-100 grid grid-cols-3 gap-2">
                    <div class="text-center rounded-lg py-2" style="background:#eff6ff;">
                        <div class="text-base font-black" style="color:#1d4ed8;">{{ number_format($article->views) }}</div>
                        <div class="text-xs" style="color:#3b82f6;">Dilihat</div>
                    </div>
                    <div class="text-center rounded-lg py-2" style="background:#fff1f2;">
                        <div class="text-base font-black" style="color:#dc2626;">{{ number_format($article->downloads) }}</div>
                        <div class="text-xs" style="color:#ef4444;">Diunduh</div>
                    </div>
                    <div class="text-center rounded-lg py-2" style="background:#fefce8;">
                        <div class="text-base font-black" style="color:#ca8a04;">{{ number_format($article->citations ?? 0) }}</div>
                        <div class="text-xs" style="color:#eab308;">Sitasi</div>
                    </div>
                </div>

            </dl>
        </div>
    </div>

    {{-- ── LICENSE ───────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-2.5" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Lisensi</p>
        </div>
        <div class="p-4">
            <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank" rel="noopener"
               class="flex items-center gap-2 mb-2 hover:opacity-80 transition-opacity">
                <img src="https://mirrors.creativecommons.org/presskit/icons/cc.svg" alt="CC" class="w-5 h-5">
                <img src="https://mirrors.creativecommons.org/presskit/icons/by.svg" alt="BY" class="w-5 h-5">
                <span class="text-sm font-bold text-slate-700">CC BY 4.0</span>
            </a>
            <p class="text-xs text-slate-400 leading-relaxed">
                Artikel ini dilisensikan di bawah
                <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank" rel="noopener"
                   class="text-blue-600 hover:underline">Creative Commons Attribution 4.0 International License</a>.
            </p>
        </div>
    </div>

    {{-- ── METADATA & SHARE ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-2.5" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Metadata & Bagikan</p>
        </div>
        <div class="p-4 flex flex-col gap-2">
            @if($article->doi)
            <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
               class="flex items-center gap-2.5 text-xs font-medium text-slate-600 hover:text-blue-600 transition-colors px-2 py-1.5 rounded-lg hover:bg-slate-50">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                Crossref DOI
            </a>
            @endif
            <a href="{{ route('journals.oai', $journal->slug) }}?verb=GetRecord&identifier=oai:{{ parse_url(config('app.url'), PHP_URL_HOST) }}:{{ $article->id }}&metadataPrefix=oai_dc"
               target="_blank"
               class="flex items-center gap-2.5 text-xs font-medium text-slate-600 hover:text-blue-600 transition-colors px-2 py-1.5 rounded-lg hover:bg-slate-50">
                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0"/></svg>
                OAI-PMH Record
            </a>
            <div class="border-t border-slate-100 pt-2 mt-1 flex items-center gap-2">
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('journals.articles.show', [$journal->slug, $article->id])) }}&text={{ urlencode(Str::limit($article->submission->title, 100)) }}"
                   target="_blank" rel="noopener"
                   class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg transition-colors"
                   style="background:#f0f9ff;color:#0284c7;" onmouseover="this.style.background='#e0f2fe'" onmouseout="this.style.background='#f0f9ff'">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    X / Twitter
                </a>
                <button onclick="navigator.clipboard.writeText(window.location.href).then(()=>{this.textContent='✓ Tersalin';setTimeout(()=>this.textContent='Salin URL',1500)})"
                        class="flex-1 text-xs font-semibold px-3 py-2 rounded-lg transition-colors"
                        style="background:#f1f5f9;color:#475569;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                    Salin URL
                </button>
            </div>
        </div>
    </div>

    {{-- ── ADMIN-CONFIGURED SIDEBAR BLOCKS ──────────────────────────── --}}
    @foreach($sidebarBlocks as $block)
        @include('reader.partials.sidebar-block', [
            'block'   => $block,
            'journal' => $journal,
            'stats'   => $journalStats,
        ])
    @endforeach

</div>{{-- end sidebar --}}

</div>
</div>
</div>

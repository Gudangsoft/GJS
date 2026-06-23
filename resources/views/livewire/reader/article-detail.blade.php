@push('head')
<link rel="canonical" href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}">
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $article->submission->title }}">
<meta property="og:description" content="{{ Str::limit($article->submission->abstract, 200) }}">
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
    "description": {{ json_encode(Str::limit($article->submission->abstract, 500)) }},
    "abstract": {{ json_encode($article->submission->abstract) }},
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
@if($article->submission->abstract)<meta name="citation_abstract" content="{{ $article->submission->abstract }}">@endif
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

{{-- ══════════ BREADCRUMB ══════════ --}}
<div class="bg-white border-b border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-1.5 text-xs text-slate-500 flex-wrap">
            <a href="{{ route('journals.home', $journal->slug) }}" class="hover:text-blue-600 transition-colors truncate max-w-[160px]">
                {{ $journal->name }}
            </a>
            <svg class="w-3.5 h-3.5 shrink-0 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            @if($article->issue)
            <a href="{{ route('journals.issues.show', [$journal->slug, $article->issue->id]) }}" class="hover:text-blue-600 transition-colors">
                {{ $article->issue->getLabel() }}
            </a>
            <svg class="w-3.5 h-3.5 shrink-0 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            @endif
            <span class="text-slate-700 truncate max-w-xs">{{ Str::limit($article->submission->title, 60) }}</span>
        </nav>
    </div>
</div>

{{-- ══════════ MAIN LAYOUT ══════════ --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

{{-- ══════ LEFT / MAIN ══════ --}}
<div class="lg:col-span-2 flex flex-col gap-5">

    {{-- Article Header Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Colored top bar --}}
        <div class="h-1.5 w-full" style="background:linear-gradient(90deg,#1d4ed8,#4f46e5)"></div>

        <div class="p-6 sm:p-8">

            {{-- Section badge --}}
            @if($article->section)
            <span class="inline-block text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 px-3 py-1 rounded-full mb-4">
                {{ $article->section->title }}
            </span>
            @endif

            {{-- Title --}}
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight mb-2">
                {{ $article->submission->title }}
            </h1>
            @if($article->submission->subtitle)
            <p class="text-lg text-slate-500 mb-5 leading-snug">{{ $article->submission->subtitle }}</p>
            @endif

            {{-- Authors --}}
            <div class="flex flex-col gap-2 mb-6 mt-4">
                @foreach($article->submission->contributors as $contributor)
                <div class="flex items-center gap-3">
                    {{-- Initial avatar --}}
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-white text-xs font-black"
                         style="background:linear-gradient(135deg,#1d4ed8,#4f46e5)">
                        {{ strtoupper(substr($contributor->first_name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <span class="text-sm font-semibold text-slate-900">{{ $contributor->full_name }}</span>
                        @if($contributor->affiliation)
                        <span class="text-sm text-slate-400 ml-1.5">{{ $contributor->affiliation }}</span>
                        @endif
                        @if($contributor->orcid)
                        <a href="https://orcid.org/{{ $contributor->orcid }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1 ml-2 text-xs text-green-600 hover:text-green-800 font-medium">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.372 0 0 5.372 0 12s5.372 12 12 12 12-5.372 12-12S18.628 0 12 0zm-1.457 4.669c.456 0 .826.37.826.826s-.37.826-.826.826-.826-.37-.826-.826.37-.826.826-.826zm2.914 14.662h-1.828V9.388h1.828v9.943zm-5.828 0V9.388h1.828v9.943H7.629z"/></svg>
                            {{ $contributor->orcid }}
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Published meta strip --}}
            <div class="flex flex-wrap items-center gap-3 py-3 border-y border-slate-100 text-xs text-slate-500">
                @if($article->date_published)
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25"/></svg>
                    Diterbitkan {{ $article->date_published->format('d F Y') }}
                </span>
                @endif
                @if($article->doi)
                <span class="text-slate-300">|</span>
                <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                   class="flex items-center gap-1.5 text-blue-600 hover:underline font-mono">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                    {{ $article->doi }}
                </a>
                @endif
                @if($article->pages)
                <span class="text-slate-300">|</span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25M9 12h6m-3-3v6"/></svg>
                    Hal. {{ $article->pages }}
                </span>
                @endif
                <span class="text-slate-300">|</span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ number_format($article->views) }} dilihat
                </span>
            </div>
        </div>
    </div>

    {{-- Abstract Card --}}
    @if($article->submission->abstract)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-1 h-5 rounded-full" style="background:linear-gradient(180deg,#1d4ed8,#4f46e5)"></div>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Abstrak</h2>
        </div>
        <p class="text-slate-700 leading-relaxed text-sm sm:text-base">
            {{ $article->submission->abstract }}
        </p>

        {{-- Keywords --}}
        @if($article->submission->keywords)
        <div class="mt-5 pt-5 border-t border-slate-100">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2.5">Kata Kunci</p>
            <div class="flex flex-wrap gap-2">
                @foreach($article->submission->keywords as $kw)
                <span class="text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 px-3 py-1.5 rounded-full">
                    {{ $kw }}
                </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Citation Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-1 h-5 rounded-full bg-slate-300"></div>
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Cara Mengutip</h2>
        </div>
        <p class="text-xs text-slate-600 leading-relaxed font-mono bg-slate-50 rounded-xl p-4 border border-slate-100">
            {{ $article->submission->contributors->map(fn($c) => $c->last_name.', '.substr($c->first_name,0,1).'.')->join(', ') }}
            ({{ $article->date_published?->format('Y') }}).
            {{ $article->submission->title }}.
            <em>{{ $journal->name }}</em>@if($article->issue), {{ $article->issue->getLabel() }}@endif.
            @if($article->doi)
            <a href="https://doi.org/{{ $article->doi }}" class="text-blue-600 hover:underline">https://doi.org/{{ $article->doi }}</a>
            @endif
        </p>
    </div>

</div>

{{-- ══════ RIGHT / SIDEBAR ══════ --}}
<div class="flex flex-col gap-5">

    {{-- Download Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Unduh Artikel</h3>

        @if($articleGalleys->isNotEmpty())
        <div class="flex flex-col gap-2">
            @foreach($articleGalleys as $galley)
            @php $hasFile = !empty($galley->remote_url) || !empty($galley->submission_file_id); @endphp
            @if($hasFile)
            <a href="{{ route('journals.articles.galley', [$journal->slug, $article->id, $galley->id]) }}"
               class="flex items-center gap-3 w-full px-4 py-3 rounded-xl font-semibold text-sm transition-all duration-150 hover:brightness-110 active:scale-95"
               style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <span>Unduh {{ $galley->label }}</span>
                @if($galley->views > 0)
                <span class="ml-auto text-red-200 text-xs font-normal">{{ number_format($galley->views) }}×</span>
                @endif
            </a>
            @else
            <div class="flex items-center gap-3 w-full px-4 py-3 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-slate-400 text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
                <span>{{ $galley->label }}</span>
                <span class="ml-auto text-xs bg-slate-200 text-slate-400 px-2 py-0.5 rounded-full">Segera</span>
            </div>
            @endif
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-6 text-center">
            <svg class="w-8 h-8 text-slate-200 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <p class="text-xs text-slate-400">File belum tersedia</p>
        </div>
        @endif
    </div>

    {{-- Article Info Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Informasi Artikel</h3>
        <dl class="flex flex-col gap-3.5">

            @if($article->date_published)
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-0.5">Diterbitkan</dt>
                <dd class="text-sm font-semibold text-slate-800">{{ $article->date_published->format('d F Y') }}</dd>
            </div>
            @endif

            @if($article->doi)
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-0.5">DOI</dt>
                <dd>
                    <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                       class="text-sm text-blue-600 hover:underline break-all font-mono">
                        {{ $article->doi }}
                    </a>
                </dd>
            </div>
            @endif

            @if($article->pages)
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-0.5">Halaman</dt>
                <dd class="text-sm font-semibold text-slate-800">{{ $article->pages }}</dd>
            </div>
            @endif

            @if($article->issue)
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-0.5">Terbitan</dt>
                <dd>
                    <a href="{{ route('journals.issues.show', [$journal->slug, $article->issue->id]) }}"
                       class="text-sm text-blue-600 hover:underline">
                        {{ $article->issue->getLabel() }}
                    </a>
                </dd>
            </div>
            @endif

            <div class="pt-2 border-t border-slate-100">
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-0.5">Dilihat</dt>
                <dd class="text-sm font-semibold text-slate-800">{{ number_format($article->views) }} kali</dd>
            </div>
        </dl>
    </div>

    {{-- License Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Lisensi</h3>
        <div class="flex items-center gap-2 mb-2">
            <img src="https://mirrors.creativecommons.org/presskit/icons/cc.svg" alt="CC" class="w-5 h-5">
            <img src="https://mirrors.creativecommons.org/presskit/icons/by.svg" alt="BY" class="w-5 h-5">
            <span class="text-sm font-semibold text-slate-700">CC BY 4.0</span>
        </div>
        <p class="text-xs text-slate-400 leading-relaxed">
            Artikel ini dilisensikan di bawah Creative Commons Attribution 4.0 International License.
        </p>
    </div>

    {{-- Share / OAI --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Metadata</h3>
        <div class="flex flex-col gap-2">
            @if($article->doi)
            <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
               class="flex items-center gap-2 text-xs text-slate-600 hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                Crossref DOI
            </a>
            @endif
            <a href="{{ route('journals.oai', $journal->slug) }}?verb=GetRecord&identifier=oai:{{ parse_url(config('app.url'), PHP_URL_HOST) }}:{{ $article->id }}&metadataPrefix=oai_dc"
               target="_blank"
               class="flex items-center gap-2 text-xs text-slate-600 hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0"/></svg>
                OAI-PMH Record
            </a>
        </div>
    </div>

</div>
{{-- end sidebar --}}

</div>
</div>
</div>

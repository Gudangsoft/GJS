@push('head')
{{-- Canonical URL --}}
<link rel="canonical" href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}">

{{-- Open Graph --}}
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $article->submission->title }}">
<meta property="og:description" content="{{ Str::limit($article->submission->abstract, 200) }}">
<meta property="og:url" content="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}">
<meta property="og:site_name" content="{{ $journal->name }}">
@if($article->date_published)
<meta property="article:published_time" content="{{ $article->date_published->toIso8601String() }}">
@endif

{{-- JSON-LD ScholarlyArticle (schema.org) --}}
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
    @if($article->doi)
    "identifier": {{ json_encode('https://doi.org/' . $article->doi) }},
    @endif
    @if($article->date_published)
    "datePublished": {{ json_encode($article->date_published->toDateString()) }},
    @endif
    "inLanguage": {{ json_encode($article->submission->locale ?? 'id') }},
    "author": [
        @foreach($article->submission->contributors as $i => $contributor)
        {
            "@type": "Person",
            "name": {{ json_encode($contributor->full_name) }}
            @if($contributor->affiliation),"affiliation": {"@type": "Organization", "name": {{ json_encode($contributor->affiliation) }}}@endif
            @if($contributor->orcid),"identifier": {{ json_encode('https://orcid.org/' . $contributor->orcid) }}@endif
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ],
    "isPartOf": {
        "@type": "Periodical",
        "name": {{ json_encode($journal->name) }}
        @if($journal->issn_online),"issn": {{ json_encode($journal->issn_online) }}@elseif($journal->issn_print),"issn": {{ json_encode($journal->issn_print) }}@endif
    },
    "publisher": {
        "@type": "Organization",
        "name": {{ json_encode($journal->publisher ?? $journal->name) }}
    },
    "license": "https://creativecommons.org/licenses/by/4.0/"
}
</script>

{{-- Google Scholar / Highwire citation meta tags --}}
<meta name="citation_title" content="{{ $article->submission->title }}">
@foreach($article->submission->contributors as $contributor)
<meta name="citation_author" content="{{ $contributor->last_name }}, {{ $contributor->first_name }}">
@if($contributor->affiliation)
<meta name="citation_author_institution" content="{{ $contributor->affiliation }}">
@endif
@if($contributor->orcid)
<meta name="citation_author_orcid" content="https://orcid.org/{{ $contributor->orcid }}">
@endif
@endforeach
<meta name="citation_journal_title" content="{{ $journal->name }}">
@if($journal->issn_online)
<meta name="citation_issn" content="{{ $journal->issn_online }}">
@elseif($journal->issn_print)
<meta name="citation_issn" content="{{ $journal->issn_print }}">
@endif
@if($article->issue)
@if($article->issue->volume)<meta name="citation_volume" content="{{ $article->issue->volume }}">@endif
@if($article->issue->number)<meta name="citation_issue" content="{{ $article->issue->number }}">@endif
@if($article->issue->year)<meta name="citation_year" content="{{ $article->issue->year }}">@endif
@endif
@if($article->date_published)
<meta name="citation_publication_date" content="{{ $article->date_published->format('Y/m/d') }}">
@endif
@if($article->pages)
@php [$pageFirst, $pageLast] = array_pad(explode('-', $article->pages, 2), 2, null); @endphp
<meta name="citation_firstpage" content="{{ trim($pageFirst) }}">
@if($pageLast)<meta name="citation_lastpage" content="{{ trim($pageLast) }}">@endif
@endif
@if($article->doi)
<meta name="citation_doi" content="{{ $article->doi }}">
@endif
@if($article->submission->abstract)
<meta name="citation_abstract" content="{{ $article->submission->abstract }}">
@endif
@if($article->submission->keywords)
@foreach($article->submission->keywords as $kw)
<meta name="citation_keyword" content="{{ $kw }}">
@endforeach
@endif
<meta name="citation_language" content="{{ $article->submission->locale ?? 'id' }}">
<meta name="citation_abstract_html_url" content="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}">
@foreach($this->approvedGalleys as $galley)
@if(str_contains(strtolower($galley->label), 'pdf'))
<meta name="citation_pdf_url" content="{{ route('journals.articles.galley', [$journal->slug, $article->id, $galley->id]) }}">
@endif
@endforeach
@endpush

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6 flex-wrap">
        <a href="{{ route('journals.home', $journal->slug) }}" class="hover:text-blue-600">{{ $journal->name }}</a>
        @if($article->issue)
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('journals.issues.show', [$journal->slug, $article->issue->id]) }}" class="hover:text-blue-600">
            {{ $article->issue->getLabel() }}
        </a>
        @endif
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-700 truncate max-w-xs">{{ Str::limit($article->submission->title, 50) }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Main --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 p-7">

                {{-- Section badge --}}
                @if($article->section)
                <span class="inline-block text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full mb-3">
                    {{ $article->section->title }}
                </span>
                @endif

                {{-- Title --}}
                <h1 class="text-2xl font-bold text-slate-900 leading-tight mb-2">
                    {{ $article->submission->title }}
                </h1>
                @if($article->submission->subtitle)
                <p class="text-lg text-slate-600 mb-4">{{ $article->submission->subtitle }}</p>
                @endif

                {{-- Authors --}}
                <div class="mb-5 pb-5 border-b border-slate-100">
                    <p class="text-sm font-medium text-slate-700 mb-1">Penulis</p>
                    <div class="flex flex-wrap gap-x-4 gap-y-1">
                        @foreach($article->submission->contributors as $contributor)
                        <div class="text-sm">
                            <span class="font-medium text-slate-800">{{ $contributor->full_name }}</span>
                            @if($contributor->affiliation)
                            <span class="text-slate-500">, {{ $contributor->affiliation }}</span>
                            @endif
                            @if($contributor->orcid)
                            <a href="https://orcid.org/{{ $contributor->orcid }}" target="_blank" rel="noopener"
                               class="ml-1 text-xs text-green-600 hover:underline">ORCID</a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Abstract --}}
                @if($article->submission->abstract)
                <div class="mb-5">
                    <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-2">Abstrak</h2>
                    <div class="text-sm text-slate-700 leading-relaxed">
                        {{ $article->submission->abstract }}
                    </div>
                </div>
                @endif

                {{-- Keywords --}}
                @if($article->submission->keywords)
                <div class="mb-5 pb-5 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-2">Kata Kunci</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($article->submission->keywords as $kw)
                        <span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full">
                            {{ $kw }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Citation --}}
                <div class="bg-slate-50 rounded-xl p-4">
                    <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Cara Mengutip</h2>
                    <p class="text-xs text-slate-600 leading-relaxed font-mono">
                        {{ $article->submission->contributors->map(fn($c) => $c->last_name . ', ' . substr($c->first_name, 0, 1) . '.')->join(', ') }}
                        ({{ $article->date_published?->format('Y') }}).
                        {{ $article->submission->title }}.
                        <em>{{ $journal->name }}</em>,
                        @if($article->issue) {{ $article->issue->getLabel() }}. @endif
                        @if($article->doi)https://doi.org/{{ $article->doi }}@endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Download buttons --}}
            @if($this->approvedGalleys->isNotEmpty())
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Unduh Artikel</h3>
                <div class="space-y-2">
                    @foreach($this->approvedGalleys as $galley)
                    @php $hasFile = !empty($galley->remote_url) || !empty($galley->submission_file_id); @endphp
                    @if($hasFile)
                    <a href="{{ route('journals.articles.galley', [$journal->slug, $article->id, $galley->id]) }}"
                       class="flex items-center gap-3 w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium text-sm transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh {{ $galley->label }}</span>
                        @if($galley->views > 0)
                        <span class="ml-auto opacity-70 text-xs font-normal">{{ number_format($galley->views) }}×</span>
                        @endif
                    </a>
                    @else
                    <div class="flex items-center gap-3 w-full px-4 py-3 rounded-lg bg-slate-100 border border-slate-200 border-dashed text-slate-400 text-sm cursor-not-allowed">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>{{ $galley->label }}</span>
                        <span class="ml-auto text-xs bg-slate-200 text-slate-500 px-2 py-0.5 rounded-full">Segera Hadir</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Article info --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Informasi Artikel</h3>
                <dl class="space-y-2 text-sm">
                    @if($article->date_published)
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wide">Diterbitkan</dt>
                        <dd class="font-medium text-slate-700">{{ $article->date_published->format('d F Y') }}</dd>
                    </div>
                    @endif
                    @if($article->doi)
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wide">DOI</dt>
                        <dd>
                            <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                               class="text-blue-600 hover:underline break-all">
                                {{ $article->doi }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    @if($article->pages)
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wide">Halaman</dt>
                        <dd class="font-medium text-slate-700">{{ $article->pages }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wide">Dilihat</dt>
                        <dd class="font-medium text-slate-700">{{ number_format($article->views) }} kali</dd>
                    </div>
                </dl>
            </div>

            {{-- License --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Lisensi</h3>
                <div class="flex items-center gap-2">
                    <img src="https://mirrors.creativecommons.org/presskit/icons/cc.svg" alt="CC" class="w-5 h-5">
                    <img src="https://mirrors.creativecommons.org/presskit/icons/by.svg" alt="BY" class="w-5 h-5">
                    <span class="text-xs text-slate-600">CC BY 4.0</span>
                </div>
            </div>
        </div>
    </div>
</div>

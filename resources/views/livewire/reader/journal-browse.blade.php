<div>
{{-- Header --}}
<div class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="text-xs text-slate-400 mb-2 flex items-center gap-1.5">
            <a href="{{ route('journals.home', $journal->slug) }}" class="hover:text-blue-600">{{ $journal->name_abbrev ?? $journal->name }}</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span>Jelajahi</span>
        </nav>
        <h1 class="text-xl font-black text-slate-900">
            Jelajahi Berdasarkan {{ match($by) { 'author' => 'Penulis', 'title' => 'Judul', 'keyword' => 'Kata Kunci', default => '' } }}
        </h1>

        {{-- Browse type tabs --}}
        <div class="flex gap-1 mt-4">
            @foreach(['author' => 'Penulis', 'title' => 'Judul', 'keyword' => 'Kata Kunci'] as $type => $label)
            <a href="{{ route('journals.browse', [$journal->slug, $type]) }}"
               class="px-4 py-2 text-sm rounded-lg font-medium transition-colors {{ $by === $type ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
               {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Alphabet filter --}}
    <div class="border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
            <div class="flex flex-wrap gap-1">
                <a href="{{ route('journals.browse', [$journal->slug, $by]) }}"
                   class="w-8 h-8 flex items-center justify-center text-xs font-bold rounded transition-colors {{ !$letter ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                   Semua
                </a>
                @foreach($letters as $l)
                <a href="{{ route('journals.browse', [$journal->slug, $by]) }}?letter={{ $l }}"
                   class="w-8 h-8 flex items-center justify-center text-xs font-bold rounded transition-colors {{ $letter === $l ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                   {{ $l }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    @if($by === 'author')
        @if($items->isEmpty())
        <p class="text-slate-400 text-sm text-center py-10">Tidak ada penulis ditemukan{{ $letter ? ' dengan huruf ' . $letter : '' }}.</p>
        @else
        <div class="columns-1 sm:columns-2 lg:columns-3 gap-4 space-y-4">
            @foreach($items as $authorName => $contributions)
            <div class="bg-white border border-slate-200 rounded-xl p-4 break-inside-avoid hover:border-blue-200 transition-colors">
                <p class="font-bold text-slate-800 text-sm mb-2">{{ $authorName }}</p>
                <ul class="space-y-1">
                    @foreach($contributions->take(3) as $contrib)
                    @if($contrib->submission?->article)
                    <li>
                        <a href="{{ route('journals.articles.show', [$journal->slug, $contrib->submission->article->id]) }}"
                           class="text-xs text-blue-600 hover:underline leading-snug block">
                           {{ Str::limit($contrib->submission->title ?? '', 60) }}
                        </a>
                    </li>
                    @endif
                    @endforeach
                    @if($contributions->count() > 3)
                    <li class="text-xs text-slate-400">+{{ $contributions->count() - 3 }} artikel lainnya</li>
                    @endif
                </ul>
            </div>
            @endforeach
        </div>
        @endif

    @elseif($by === 'title')
        @if($items->isEmpty())
        <p class="text-slate-400 text-sm text-center py-10">Tidak ada artikel ditemukan{{ $letter ? ' dengan huruf ' . $letter : '' }}.</p>
        @else
        @foreach($items as $groupLetter => $articles)
        <div class="mb-6">
            <h2 class="text-2xl font-black text-slate-300 mb-2 border-b border-slate-100 pb-1">{{ $groupLetter }}</h2>
            <ul class="space-y-2">
                @foreach($articles as $article)
                <li class="flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 text-slate-300 shrink-0 mt-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    <div>
                        <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                           class="text-sm font-semibold text-slate-800 hover:text-blue-700 transition-colors leading-snug block">
                           {{ $article->submission->title ?? '—' }}
                        </a>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $article->submission->contributors->map(fn($c)=>$c->full_name)->join(', ') }}
                            @if($article->issue) · {{ $article->issue->getLabel() }} @endif
                        </p>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
        @endif

    @elseif($by === 'keyword')
        @if($items->isEmpty())
        <p class="text-slate-400 text-sm text-center py-10">Tidak ada kata kunci ditemukan{{ $letter ? ' dengan huruf ' . $letter : '' }}.</p>
        @else
        @php $maxCount = $items->max() ?: 1; @endphp
        <div class="flex flex-wrap gap-2">
            @foreach($items as $kw => $cnt)
            @php $size = 11 + round(($cnt/$maxCount)*7); @endphp
            <a href="{{ route('journals.search', $journal->slug) }}?kw={{ urlencode($kw) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border transition-all hover:shadow-sm"
               style="font-size:{{ $size }}px;background:rgba(30,64,175,{{ round(0.04+($cnt/$maxCount)*0.1,2) }});border-color:rgba(30,64,175,{{ round(0.12+($cnt/$maxCount)*0.2,2) }});color:rgba(30,64,175,{{ round(0.5+($cnt/$maxCount)*0.5,2) }});">
                {{ $kw }}
                <span class="text-xs opacity-60">({{ $cnt }})</span>
            </a>
            @endforeach
        </div>
        @endif
    @endif

</div>
</div>

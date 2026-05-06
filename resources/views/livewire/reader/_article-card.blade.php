<div class="bg-white rounded-xl border border-slate-200 hover:border-blue-300 hover:shadow-sm transition-all p-5">
    <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
            {{-- Section badge --}}
            @if($article->section)
            <span class="inline-block text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded mb-2">
                {{ $article->section->title }}
            </span>
            @endif

            {{-- Title --}}
            <h3 class="font-semibold text-slate-900 mb-2 leading-snug">
                <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                   class="hover:text-blue-700 transition-colors">
                    {{ $article->submission->title }}
                </a>
            </h3>

            {{-- Authors --}}
            <p class="text-sm text-slate-500 mb-2">
                {{ $article->submission->contributors->map(fn($c) => $c->full_name)->join(', ') }}
            </p>

            {{-- Meta --}}
            <div class="flex items-center gap-3 text-xs text-slate-400">
                @if($article->date_published)
                <span>{{ $article->date_published->format('d M Y') }}</span>
                @endif
                @if($article->pages)
                <span>Hal. {{ $article->pages }}</span>
                @endif
                @if($article->doi)
                <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                   class="text-blue-500 hover:underline">DOI</a>
                @endif
            </div>
        </div>

        {{-- Galley buttons --}}
        @if($article->galleys->isNotEmpty())
        <div class="flex flex-col gap-1 shrink-0">
            @foreach($article->galleys->take(2) as $galley)
            <span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2 py-1 rounded font-medium">
                {{ $galley->label }}
            </span>
            @endforeach
        </div>
        @endif
    </div>
</div>

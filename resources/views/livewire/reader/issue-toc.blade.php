<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('journals.home', $journal->slug) }}" class="hover:text-blue-600">{{ $journal->name }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('journals.issues', $journal->slug) }}" class="hover:text-blue-600">Arsip</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-700 font-medium">{{ $issue->getLabel() }}</span>
    </div>

    {{-- Issue header --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-8">
        <p class="text-sm text-slate-500 mb-1">{{ $journal->name }}</p>
        <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ $issue->getLabel() }}</h1>
        @if($issue->date_published)
        <p class="text-sm text-slate-500">Diterbitkan {{ $issue->date_published->format('d F Y') }}</p>
        @endif
        @if($issue->description)
        <div class="mt-3 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
            {!! $issue->description !!}
        </div>
        @endif
    </div>

    {{-- Articles by section --}}
    @forelse($articles as $sectionTitle => $sectionArticles)
    <div class="mb-8">
        @if($sectionTitle)
        <h2 class="text-base font-bold text-slate-700 uppercase tracking-wide mb-4 border-b border-slate-200 pb-2">
            {{ $sectionTitle }}
        </h2>
        @endif
        <div class="space-y-4">
            @foreach($sectionArticles as $article)
            <div class="bg-white rounded-xl border border-slate-200 hover:border-blue-300 hover:shadow-sm transition-all p-5">
                <h3 class="font-semibold text-slate-900 leading-snug mb-2">
                    <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                       class="hover:text-blue-700 transition-colors">
                        {{ $article->submission->title }}
                    </a>
                </h3>
                <p class="text-sm text-slate-500 mb-3">
                    {{ $article->submission->contributors->map(fn($c) => $c->full_name)->join('; ') }}
                </p>
                <div class="flex items-center gap-3 flex-wrap">
                    @if($article->pages)
                    <span class="text-xs text-slate-400">Hal. {{ $article->pages }}</span>
                    @endif
                    @if($article->doi)
                    <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                       class="text-xs text-blue-500 hover:underline">
                        https://doi.org/{{ $article->doi }}
                    </a>
                    @endif
                    <div class="flex gap-1 ml-auto">
                        @foreach($article->galleys as $galley)
                        <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                           class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded font-medium hover:bg-blue-100 transition-colors">
                            {{ $galley->label }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <p class="text-slate-400 text-center py-12">Belum ada artikel di terbitan ini.</p>
    @endforelse
</div>

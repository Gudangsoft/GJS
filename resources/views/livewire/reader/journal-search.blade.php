<div>
@include('reader.partials.journal-header', ['activeTab' => 'search'])

{{-- Search bar --}}
<div class="bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex gap-3 items-center">
            <div class="flex-1 relative max-w-xl">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.400ms="query"
                       type="text"
                       placeholder="{{ __('site.search_articles') }}"
                       class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @if($query)
                <button wire:click="$set('query','')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                @endif
            </div>
            <span class="text-xs text-slate-400">{{ __('site.search') }}</span>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    @if(strlen($q) < 2)
    <div class="text-center py-16">
        <svg class="w-16 h-16 mx-auto mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <p class="text-slate-400 font-medium">{{ __('site.search_min_chars') }}</p>
        <p class="text-slate-300 text-sm mt-1">{{ __('site.search_hint') }}</p>
    </div>

    @elseif($totalCount === 0)
    <div class="text-center py-16">
        <svg class="w-16 h-16 mx-auto mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-slate-500 font-semibold">{{ __('site.no_results_for') }} "<span class="text-slate-700">{{ $q }}</span>"</p>
        <p class="text-slate-400 text-sm mt-2">{{ __('site.try_different_keyword') }}</p>
    </div>

    @else
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-slate-600">
            {{ __('site.found_results', ['count' => $totalCount]) }} {{ __('site.for') }}
            "<span class="text-blue-700 font-semibold">{{ $q }}</span>"
        </p>
        <div wire:loading class="text-xs text-slate-400 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            {{ __('site.searching') }}
        </div>
    </div>

    <div class="space-y-4">
        @foreach($results as $article)
        <div class="bg-white border border-slate-200 rounded-xl p-5 hover:border-blue-200 hover:shadow-sm transition-all group">
            {{-- Section badge --}}
            @if($article->section)
            <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full mb-2"
                  style="background:#eff6ff;color:#1d4ed8;">{{ $article->section->title }}</span>
            @endif

            {{-- Title --}}
            <h2 class="font-bold text-slate-900 text-base leading-snug mb-1.5 group-hover:text-blue-700 transition-colors">
                <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}">
                    {!! preg_replace('/(' . preg_quote($q, '/') . ')/iu', '<mark class="bg-yellow-100 text-yellow-900 rounded px-0.5">$1</mark>', e($article->submission->title ?? '')) !!}
                </a>
            </h2>

            {{-- Authors --}}
            <p class="text-sm text-slate-500 mb-2">
                {{ $article->submission->contributors->map(fn($c) => $c->full_name)->join(', ') }}
            </p>

            {{-- Abstract snippet --}}
            @if($article->submission->abstract)
            <p class="text-sm text-slate-600 leading-relaxed line-clamp-2 mb-3">
                {{ Str::limit(strip_tags($article->submission->abstract), 200) }}
            </p>
            @endif

            {{-- Meta --}}
            <div class="flex items-center flex-wrap gap-3 text-xs text-slate-400">
                @if($article->issue)
                <span>{{ $article->issue->getLabel() }}</span>
                @endif
                @if($article->pages)
                <span>{{ __('site.pages_abbrev') }} {{ $article->pages }}</span>
                @endif
                @if($article->date_published)
                <span>{{ \Carbon\Carbon::parse($article->date_published)->format('Y') }}</span>
                @endif
                <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                   class="ml-auto text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    {{ __('site.read_article') }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($results->hasPages())
    <div class="mt-6">
        {{ $results->links() }}
    </div>
    @endif
    @endif

</div>
</div>

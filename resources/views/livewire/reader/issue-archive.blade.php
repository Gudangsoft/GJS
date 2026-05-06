<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
            <a href="{{ route('journals.home', $journal->slug) }}" class="hover:text-blue-600">{{ $journal->name }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-700 font-medium">Arsip Terbitan</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Arsip Terbitan</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $journal->name }}</p>
    </div>

    {{-- Issues by year --}}
    @forelse($issues as $year => $yearIssues)
    <div class="mb-8">
        <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4">{{ $year }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($yearIssues as $issue)
            <a href="{{ route('journals.issues.show', [$journal->slug, $issue->id]) }}"
               class="group bg-white rounded-xl border border-slate-200 hover:border-blue-300 hover:shadow-sm transition-all p-4 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-slate-900 group-hover:text-blue-700 transition-colors">
                        {{ $issue->getLabel() }}
                    </p>
                    @if($issue->date_published)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $issue->date_published->format('d M Y') }}</p>
                    @endif
                    <p class="text-xs text-slate-500 mt-1">{{ $issue->articles_count }} artikel</p>
                </div>
                <svg class="w-5 h-5 text-slate-300 group-hover:text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endforeach
        </div>
    </div>
    @empty
    <div class="text-center py-20 text-slate-400">
        <p class="font-medium">Belum ada terbitan yang dipublikasikan.</p>
    </div>
    @endforelse
</div>

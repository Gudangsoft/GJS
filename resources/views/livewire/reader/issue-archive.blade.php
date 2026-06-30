<div>

@include('reader.partials.journal-header', ['activeTab' => 'issues'])

{{-- ══════════════════ MAIN CONTENT ══════════════════ --}}
@php
    $totalIssues   = $issues->flatten()->count();
    $totalArticles = $issues->flatten()->sum('articles_count');
    $years         = $issues->keys()->toArray();

    $gradientPalette = [
        'linear-gradient(145deg,#1d4ed8,#312e81)',
        'linear-gradient(145deg,#4f46e5,#6d28d9)',
        'linear-gradient(145deg,#7c3aed,#5b21b6)',
        'linear-gradient(145deg,#0f766e,#0e7490)',
        'linear-gradient(145deg,#0369a1,#1e40af)',
        'linear-gradient(145deg,#047857,#065f46)',
        'linear-gradient(145deg,#1e293b,#334155)',
        'linear-gradient(145deg,#9333ea,#7c3aed)',
    ];
    $gi = 0;
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    @if($issues->isEmpty())
    <div class="flex flex-col items-center justify-center py-28 text-center">
        <div class="w-20 h-20 rounded-2xl bg-blue-50 flex items-center justify-center mb-5">
            <svg class="w-10 h-10 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-slate-700 mb-2">{{ __('site.no_issues_published') }}</h2>
        <p class="text-sm text-slate-400 max-w-xs">{{ __('site.no_issues_msg') }}</p>
    </div>

    @else

    {{-- Stats strip --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 px-5 py-4 text-center">
            <p class="text-2xl font-black text-blue-700">{{ $totalIssues }}</p>
            <p class="text-xs font-medium text-slate-500 mt-0.5 uppercase tracking-wide">{{ __('site.issues') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 px-5 py-4 text-center">
            <p class="text-2xl font-black text-blue-700">{{ $totalArticles }}</p>
            <p class="text-xs font-medium text-slate-500 mt-0.5 uppercase tracking-wide">{{ __('site.articles') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 px-5 py-4 text-center">
            <p class="text-2xl font-black text-blue-700">{{ count($years) }}</p>
            <p class="text-xs font-medium text-slate-500 mt-0.5 uppercase tracking-wide">{{ __('site.years') }}</p>
        </div>
    </div>

    {{-- 2-column layout --}}
    <div class="flex gap-8 items-start">

        {{-- ── LEFT: Sticky year sidebar ── --}}
        <aside class="hidden lg:block w-44 shrink-0">
            <div class="sticky top-6">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">{{ __('site.years') }}</p>
                <nav class="flex flex-col gap-1">
                    @foreach($years as $year)
                    <a href="#year-{{ $year }}"
                       class="group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                        <span>{{ $year }}</span>
                        <span class="text-xs text-slate-400 group-hover:text-blue-500">
                            {{ $issues[$year]->count() }}
                        </span>
                    </a>
                    @endforeach
                </nav>

                {{-- OAI link --}}
                <div class="mt-6 pt-5 border-t border-slate-100">
                    <a href="{{ route('journals.oai', $journal->slug) }}"
                       class="flex items-center gap-2 text-xs text-slate-400 hover:text-blue-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 0 1 1.06 0z"/>
                        </svg>
                        OAI-PMH Feed
                    </a>
                </div>
            </div>
        </aside>

        {{-- ── RIGHT: Issues content ── --}}
        <div class="flex-1 min-w-0">

            {{-- Mobile year quick-jump --}}
            <div class="flex lg:hidden items-center gap-2 overflow-x-auto pb-2 mb-6">
                <span class="text-xs text-slate-400 shrink-0">{{ __('site.years') }}:</span>
                @foreach($years as $year)
                <a href="#year-{{ $year }}"
                   class="shrink-0 px-3 py-1.5 text-xs font-bold rounded-full bg-white border border-slate-200 text-slate-600 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all">
                    {{ $year }}
                </a>
                @endforeach
            </div>

            {{-- Issues by year --}}
            @foreach($issues as $year => $yearIssues)
            <section id="year-{{ $year }}" class="mb-12 scroll-mt-6">

                {{-- Year heading --}}
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                         style="background:linear-gradient(135deg,#1d4ed8,#4f46e5)">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800 leading-none">{{ $year }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $yearIssues->count() }} {{ __('site.issues') }}</p>
                    </div>
                    <div class="flex-1 h-px bg-slate-100 ml-2"></div>
                </div>

                {{-- Issue cards --}}
                <div class="flex flex-col gap-3">
                    @foreach($yearIssues as $issue)
                    @php
                        $grad = $gradientPalette[$gi % count($gradientPalette)];
                        $gi++;
                        $isCurrent = (bool) $issue->current;
                    @endphp

                    <a href="{{ route('journals.issues.show', [$journal->slug, $issue->id]) }}"
                       class="group relative flex bg-white rounded-2xl border overflow-hidden transition-all duration-200 hover:shadow-md
                              {{ $isCurrent ? 'border-amber-300 shadow-sm' : 'border-slate-200 hover:border-blue-200' }}">

                        {{-- Cover column --}}
                        <div class="relative w-24 sm:w-32 shrink-0 overflow-hidden">
                            @if($issue->cover_image)
                            <img src="{{ asset('storage/' . $issue->cover_image) }}"
                                 alt="{{ $issue->getLabel() }}"
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            @else
                            <div class="w-full h-full min-h-[120px] flex flex-col items-center justify-center p-3 relative overflow-hidden"
                                 style="background:{{ $grad }}">
                                {{-- Subtle pattern --}}
                                <div class="absolute inset-0 opacity-10"
                                     style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:12px 12px;"></div>
                                <span class="text-white font-black text-xs text-center leading-tight z-10 drop-shadow">
                                    {{ strtoupper(substr($journal->name_abbrev ?? $journal->name, 0, 5)) }}
                                </span>
                                @if($issue->volume || $issue->number)
                                <div class="border-t border-white/20 mt-2 pt-2 text-center z-10 w-full">
                                    @if($issue->volume)
                                    <p class="text-white/90 text-xs font-bold leading-tight">VOL.{{ $issue->volume }}</p>
                                    @endif
                                    @if($issue->number)
                                    <p class="text-white/70 text-xs leading-tight">NO.{{ $issue->number }}</p>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @endif

                            {{-- Current ribbon --}}
                            @if($isCurrent)
                            <div class="absolute top-0 left-0 right-0 flex justify-center">
                                <span class="bg-amber-400 text-amber-900 text-xs font-black px-2 py-0.5 w-full text-center"
                                      style="font-size:9px;letter-spacing:.04em">{{ __('site.current_issue_ribbon') }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Info column --}}
                        <div class="flex-1 flex flex-col justify-between p-4 sm:p-5 min-w-0">

                            <div>
                                {{-- Label + current badge --}}
                                <div class="flex items-start justify-between gap-2 mb-1.5">
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-blue-700 transition-colors leading-snug">
                                        {{ $issue->getLabel() ?: (__('site.issues').' '.$year) }}
                                    </h3>
                                    @if($isCurrent)
                                    <span class="shrink-0 inline-flex items-center gap-1 bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold px-2 py-0.5 rounded-full">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                        {{ __('site.current_label') }}
                                    </span>
                                    @endif
                                </div>

                                {{-- Issue title --}}
                                @if($issue->show_title && $issue->title)
                                <p class="text-sm text-slate-600 leading-relaxed mb-2 line-clamp-2">{{ $issue->title }}</p>
                                @endif

                                {{-- Description --}}
                                @if($issue->description)
                                <p class="text-xs text-slate-400 leading-relaxed line-clamp-2 hidden sm:block">{{ strip_tags($issue->description) }}</p>
                                @endif
                            </div>

                            {{-- Footer meta --}}
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                                <div class="flex items-center gap-3 text-xs text-slate-400">
                                    @if($issue->date_published)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                        </svg>
                                        {{ $issue->date_published->translatedFormat('d M Y') }}
                                    </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                        {{ $issue->articles_count }} {{ __('site.articles') }}
                                    </span>
                                </div>

                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 group-hover:text-blue-800 transition-colors">
                                    {{ __('site.view_issue') }}
                                    <svg class="w-3.5 h-3.5 transition-transform duration-150 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>

                    @endforeach
                </div>

            </section>
            @endforeach

            {{-- Back to top --}}
            <div class="flex justify-center pt-6 border-t border-slate-100">
                <button onclick="window.scrollTo({top:0,behavior:'smooth'})"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm text-slate-500 hover:text-slate-800 border border-slate-200 bg-white rounded-xl hover:bg-slate-50 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
                    </svg>
                    {{ __('site.back_to_top') }}
                </button>
            </div>
        </div>
    </div>

    @endif
</div>

</div>

<div>
@php
    $name    = $author->full_name ?? $author->name ?? 'Penulis';
    $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->implode('');
@endphp

{{-- ── Header ─────────────────────────────────────────────────────────── --}}
<div class="bg-gradient-to-br from-blue-700 to-blue-900 text-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">

            {{-- Avatar --}}
            <div class="shrink-0">
                @if($author->avatar)
                <img src="{{ asset('storage/' . $author->avatar) }}" alt="{{ $name }}"
                     class="w-24 h-24 rounded-full object-cover border-4 border-white/30 shadow-xl">
                @else
                <div class="w-24 h-24 rounded-full bg-white/20 border-4 border-white/30 flex items-center justify-center text-3xl font-black text-white shadow-xl">
                    {{ $initials }}
                </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="text-center sm:text-left flex-1">
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">{{ $name }}</h1>

                @if($author->affiliation)
                <p class="text-blue-200 mt-1 text-sm font-medium">{{ $author->affiliation }}</p>
                @endif

                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 mt-3">
                    @if($author->orcid)
                    <a href="https://orcid.org/{{ $author->orcid }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 bg-[#A6CE39] text-white text-xs font-bold px-2.5 py-1 rounded-full">
                        <svg style="width:.875rem;height:.875rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.372 0 0 5.372 0 12s5.372 12 12 12 12-5.372 12-12S18.628 0 12 0zM7.369 4.378c.525 0 .947.431.947.947s-.422.947-.947.947a.95.95 0 0 1-.947-.947c0-.525.422-.947.947-.947zm-.722 3.038h1.444v10.041H6.647V7.416zm3.562 0h3.9c3.712 0 5.344 2.653 5.344 5.025 0 2.578-2.016 5.016-5.325 5.016h-3.919V7.416zm1.444 1.303v7.444h2.297c3.272 0 3.872-2.203 3.872-3.722 0-2.016-1.1-3.722-3.872-3.722h-2.297z"/></svg>
                        {{ $author->orcid }}
                    </a>
                    @endif

                    @if($author->country)
                    <span class="text-blue-200 text-xs">🌏 {{ $author->country }}</span>
                    @endif

                    @if($author->url)
                    <a href="{{ $author->url }}" target="_blank" rel="noopener"
                       class="text-blue-200 hover:text-white text-xs underline underline-offset-2 transition-colors">
                        {{ parse_url($author->url, PHP_URL_HOST) ?? $author->url }}
                    </a>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="flex flex-wrap gap-4 mt-4 justify-center sm:justify-start">
                    <div class="text-center">
                        <div class="text-2xl font-black">{{ $articles->count() }}</div>
                        <div class="text-blue-200 text-xs">Artikel</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-black">{{ $journals->count() }}</div>
                        <div class="text-blue-200 text-xs">Jurnal</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-black">{{ $articles->sum(fn($a) => $a->citations ?? 0) }}</div>
                        <div class="text-blue-200 text-xs">Sitasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Content ─────────────────────────────────────────────────────────── --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Bio --}}
            @if($author->bio)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <h2 class="text-sm font-bold text-slate-800 mb-3">Tentang</h2>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $author->bio }}</p>
            </div>
            @endif

            {{-- Journals --}}
            @if($journals->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <h2 class="text-sm font-bold text-slate-800 mb-3">Jurnal Terkait</h2>
                <div class="space-y-2">
                    @foreach($journals as $journal)
                    <a href="{{ route('journals.home', $journal->slug) }}"
                       class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                        <svg style="width:.875rem;height:.875rem;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        {{ $journal->name_abbrev ?? $journal->name }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Co-authors --}}
            @if($coAuthors->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <h2 class="text-sm font-bold text-slate-800 mb-3">Kolaborator</h2>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($coAuthors as $co)
                    @php $coUser = \App\Models\User::where('email', $co->email)->first(); @endphp
                    @if($coUser)
                    <a href="{{ route('authors.show', $coUser) }}"
                       class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full hover:bg-blue-100 transition-colors">
                        {{ $co->first_name }} {{ $co->last_name }}
                    </a>
                    @else
                    <span class="text-xs bg-slate-50 text-slate-600 border border-slate-200 px-2 py-0.5 rounded-full">
                        {{ $co->first_name }} {{ $co->last_name }}
                    </span>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- ── Articles ─────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            <h2 class="text-base font-bold text-slate-800">
                Artikel ({{ $articles->count() }})
            </h2>

            @forelse($articles as $article)
            @php
                $sub    = $article->submission;
                $issue  = $article->issue;
                $jou    = $article->journal;
                $authors = $sub?->contributors ?? collect();
                $pdfGalley = $article->galleys?->first(fn($g) => stripos($g->label, 'pdf') !== false);
            @endphp
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md transition-shadow">

                {{-- Title --}}
                <h3 class="text-sm font-semibold text-blue-700 hover:text-blue-900 leading-snug mb-1">
                    <a href="{{ route('journals.articles.show', [$jou?->slug ?? '#', $article->id]) }}">
                        {{ $sub?->title ?? '(Tanpa Judul)' }}
                    </a>
                </h3>

                {{-- Authors --}}
                @if($authors->isNotEmpty())
                <p class="text-xs text-slate-500 mb-2">
                    {{ $authors->map(fn($c) => trim("{$c->first_name} {$c->last_name}"))->implode(', ') }}
                </p>
                @endif

                {{-- Journal + Issue --}}
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                    @if($jou)
                    <span class="inline-flex items-center gap-1 text-slate-600 font-medium">
                        <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                        </svg>
                        {{ $jou->name_abbrev ?? $jou->name }}
                    </span>
                    @endif

                    @if($issue)
                    <span>{{ $issue->getLabel() }}</span>
                    @endif

                    @if($article->date_published)
                    <span>{{ $article->date_published->format('d M Y') }}</span>
                    @endif

                    @if($article->doi)
                    <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                       class="text-blue-500 hover:text-blue-700 font-mono">
                        {{ $article->doi }}
                    </a>
                    @endif
                </div>

                {{-- Abstract snippet --}}
                @if($sub?->abstract)
                <p class="text-xs text-slate-500 mt-2 leading-relaxed" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ strip_tags($sub->abstract) }}
                </p>
                @endif

                {{-- Actions --}}
                <div class="flex gap-2 mt-3">
                    <a href="{{ route('journals.articles.show', [$jou?->slug ?? '#', $article->id]) }}"
                       class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                        Baca →
                    </a>
                    @if($pdfGalley)
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('journals.articles.galley', [$jou?->slug ?? '#', $article->id, $pdfGalley->id]) }}"
                       class="text-xs font-semibold text-red-600 hover:text-red-800 transition-colors flex items-center gap-1">
                        <svg style="width:.75rem;height:.75rem;" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                        PDF
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-100">
                <p class="text-slate-400 text-sm">Belum ada artikel terpublikasi.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
</div>

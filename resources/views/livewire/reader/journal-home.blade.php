<div>

@php
    $hs     = $journal->settings ?? [];
    $siteBg = $hs['site_bg_color'] ?? '#f1f5f9';
    $indexedBy = $hs['indexed_by'] ?? [];
    $sponsors  = $hs['sponsors']   ?? [];
@endphp
@include('reader.partials.journal-header', ['activeTab' => 'home'])





<div style="background:{{ $siteBg }};">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── MAIN AREA (left 2/3) ──────────────────────────────────────── --}}
        <div class="lg:col-span-2">

            {{-- ── JOURNAL INFO CARD ──────────────────────────────────────── --}}
            @php
                $isOA = $isOA ?? ($journal->settings['open_access'] ?? true);
                $statArticles = $journalStats['articles'] ?? $journalStats['total_articles'] ?? null;
                $statIssues   = $journalStats['issues']   ?? $journalStats['total_issues']   ?? null;
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-8 overflow-hidden">
                <div class="p-5">
                    <div class="flex gap-4">

                        {{-- Cover / Logo --}}
                        @if($journal->cover_image || $journal->logo)
                        <div class="shrink-0">
                            <div style="width:100px;height:136px;border-radius:.5rem;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 4px 14px rgba(0,0,0,.12);">
                                @if($journal->cover_image)
                                <img src="{{ asset('storage/'.$journal->cover_image) }}" alt="{{ $journal->name }}"
                                     style="width:100%;height:100%;object-fit:cover;display:block;">
                                @else
                                <div style="width:100%;height:100%;background:#eff6ff;display:flex;align-items:center;justify-content:center;padding:.75rem;">
                                    <img src="{{ asset('storage/'.$journal->logo) }}" alt="{{ $journal->name }}"
                                         style="max-width:100%;max-height:100%;object-fit:contain;">
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Info kanan --}}
                        <div class="flex-1 min-w-0">
                            {{-- ISSN --}}
                            @if($journal->issn_print || $journal->issn_online)
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                @if($journal->issn_print)
                                <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;">
                                    p-ISSN <strong>{{ $journal->issn_print }}</strong>
                                </span>
                                @endif
                                @if($journal->issn_online)
                                <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                                    e-ISSN <strong>{{ $journal->issn_online }}</strong>
                                </span>
                                @endif
                            </div>
                            @endif

                            {{-- Deskripsi --}}
                            @if($journal->focus_scope || $journal->about_journal)
                            <p class="text-sm text-slate-600 leading-relaxed mb-3 line-clamp-3">
                                {{ Str::limit(strip_tags($journal->focus_scope ?: $journal->about_journal), 280) }}
                            </p>
                            @endif

                            {{-- Stats --}}
                            @if($statArticles !== null || $statIssues !== null)
                            <div class="flex flex-wrap gap-x-4 mb-3" style="font-size:.8125rem;color:#64748b;">
                                @if($statArticles !== null)
                                <span><strong style="color:#1e40af;font-size:.9375rem;font-weight:800;">{{ number_format($statArticles) }}</strong> {{ __('site.articles') }}</span>
                                @endif
                                @if($statIssues !== null)
                                <span><strong style="color:#1e40af;font-size:.9375rem;font-weight:800;">{{ number_format($statIssues) }}</strong> {{ __('site.issues') }}</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-100 mt-4 mb-3"></div>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-wrap gap-2">
                        @auth
                        <a href="{{ route('submit') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-all hover:shadow-md hover:-translate-y-px"
                           style="background:linear-gradient(135deg,#1e40af,#1d4ed8);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ __('site.submit_manuscript') }}
                        </a>
                        @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-all hover:shadow-md hover:-translate-y-px"
                           style="background:linear-gradient(135deg,#1e40af,#1d4ed8);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ __('site.submit_manuscript') }}
                        </a>
                        @endauth
                        <a href="{{ route('journals.issues', $journal->slug) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-slate-600 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            {{ __('site.issue_archive') }}
                        </a>
                        @if($journal->wa_contact)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $journal->wa_contact) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-all hover:shadow-md hover:-translate-y-px"
                           style="background:linear-gradient(135deg,#16a34a,#15803d);">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.998 2C6.477 2 2 6.484 2 12.017c0 1.99.521 3.848 1.427 5.449L2.036 22l4.66-1.366A9.987 9.987 0 0011.998 22c5.521 0 9.998-4.484 9.998-10.017C21.996 6.484 17.519 2 11.998 2z"/></svg>
                            {{ __('site.chat_manager') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            {{-- ── END JOURNAL INFO CARD ──────────────────────────────────── --}}

            {{-- TOC Terbitan Saat Ini --}}
            @if($currentIssue)
            <div class="mb-8">
                {{-- Issue label --}}
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-widest mb-0.5">{{ __('site.current_issue') }}</p>
                        <h2 class="text-lg font-black text-slate-900">{{ $currentIssue->getLabel() }}
                            @if($currentIssue->date_published)
                            <span class="text-sm font-normal text-slate-400 ml-2">{{ $currentIssue->date_published->translatedFormat('F Y') }}</span>
                            @endif
                        </h2>
                    </div>
                    <a href="{{ route('journals.issues.show', [$journal->slug, $currentIssue->id]) }}"
                       class="text-xs font-semibold text-blue-600 hover:text-blue-800 whitespace-nowrap">
                        {{ __('site.show_all') }} →
                    </a>
                </div>

                {{-- TOC by section --}}
                @if($tocBySection->isNotEmpty())
                @foreach($tocBySection as $sectionTitle => $articles)
                <div class="mb-7">
                    {{-- Section heading — left-border accent style --}}
                    <h3 style="background:#f8fafc;border-left:3px solid #1e40af;padding:.625rem 1rem;border-radius:.375rem;font-size:.8125rem;font-weight:800;color:#1e40af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:1rem;">
                        {{ $sectionTitle }}
                    </h3>

                    <div class="space-y-0 divide-y divide-slate-100">
                        @foreach($articles as $article)
                        <div class="py-5 first:pt-0">
                            {{-- Title --}}
                            <h4 style="font-size:1rem;font-weight:700;color:#0f172a;line-height:1.4;margin:0 0 .375rem;">
                                <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                                   style="color:inherit;text-decoration:none;"
                                   onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#0f172a'">
                                    {{ $article->submission->title }}
                                </a>
                            </h4>

                            {{-- Authors — biru, italic, gaya akademik --}}
                            <p style="font-size:.8125rem;color:#1e40af;font-style:italic;margin:0 0 .5rem;">
                                {{ $article->submission->contributors->map(fn($c) => $c->full_name)->join(', ') }}
                            </p>

                            {{-- Abstract excerpt --}}
                            @if($article->submission->abstract)
                            <p class="line-clamp-2" style="font-size:.8125rem;color:#64748b;line-height:1.6;margin:0 0 .625rem;">
                                {{ Str::limit(strip_tags($article->submission->abstract), 220) }}
                            </p>
                            @endif

                            {{-- Meta row: pages, DOI, stats, galley buttons --}}
                            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:.375rem .75rem;margin-top:.25rem;">
                                @if($article->pages)
                                <span style="display:inline-flex;align-items:center;padding:.15rem .5rem;border-radius:.25rem;font-size:.6875rem;font-weight:600;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;">
                                    Hal. {{ $article->pages }}
                                </span>
                                @endif
                                @if($article->doi)
                                <a href="https://doi.org/{{ $article->doi }}" target="_blank" rel="noopener"
                                   style="display:inline-flex;align-items:center;gap:.25rem;font-size:.6875rem;color:#15803d;font-family:monospace;text-decoration:none;"
                                   onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                    <svg style="width:.65rem;height:.65rem;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    {{ $article->doi }}
                                </a>
                                @endif
                                {{-- View / Download stats --}}
                                @if($article->views > 0)
                                <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.6875rem;color:#94a3b8;">
                                    <svg style="width:.7rem;height:.7rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ number_format($article->views) }}
                                </span>
                                @endif
                                @if($article->downloads > 0)
                                <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.6875rem;color:#94a3b8;">
                                    <svg style="width:.7rem;height:.7rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    {{ number_format($article->downloads) }}
                                </span>
                                @endif
                                {{-- Galley buttons (OJS style) --}}
                                <div style="display:flex;gap:.375rem;flex-wrap:wrap;margin-left:auto;">
                                    <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
                                       style="display:inline-flex;align-items:center;font-size:.6875rem;font-weight:700;padding:.25rem .75rem;border-radius:.3rem;background:#fff;border:1.5px solid #1e40af;color:#1e40af;text-decoration:none;transition:background .15s;"
                                       onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='#fff'">
                                        Abstrak
                                    </a>
                                    @foreach($article->galleys->take(3) as $galley)
                                    <a href="{{ route('journals.articles.galley.view', [$journal->slug, $article->id, $galley->id]) }}"
                                       style="display:inline-flex;align-items:center;font-size:.6875rem;font-weight:700;padding:.25rem .75rem;border-radius:.3rem;background:#1e40af;border:1.5px solid #1e3a8a;color:#fff;text-decoration:none;transition:background .15s;"
                                       onmouseover="this.style.background='#1e3a8a'" onmouseout="this.style.background='#1e40af'">
                                        {{ strtoupper($galley->label ?? 'PDF') }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-10 text-slate-400 text-sm rounded-xl border border-dashed border-slate-200">
                    Belum ada artikel di terbitan ini.
                </div>
                @endif
            </div>
            @else
            <div class="rounded-xl border border-dashed border-slate-200 p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <p class="font-medium">Belum ada terbitan aktif</p>
            </div>
            @endif

            {{-- Announcements — below articles so they don't push down primary content --}}
            @if($announcements->isNotEmpty())
            <div id="pengumuman" class="mt-8">
                <h2 class="text-base font-black text-slate-800 uppercase tracking-wider pb-2 mb-5"
                    style="border-bottom:2px solid #1e40af;">
                    Pengumuman
                </h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach($announcements as $ann)
                    <div class="bg-white border border-slate-200 rounded-xl p-5 flex gap-4 hover:border-blue-200 hover:shadow-sm transition-all group">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5"
                             style="background:#eff6ff;">
                            <svg class="w-4.5 h-4.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-900 text-sm leading-snug mb-1 group-hover:text-blue-700 transition-colors">
                                {{ $ann->title }}
                            </h3>
                            <p class="text-xs text-slate-400 mb-2">
                                {{ $ann->date_posted?->translatedFormat('d F Y') }}
                            </p>
                            @if($ann->description_short)
                            <p class="text-sm text-slate-600 leading-relaxed line-clamp-3">
                                {{ Str::limit(strip_tags($ann->description_short), 180) }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- ── SIDEBAR (right 1/3) ──────────────────────────────────────── --}}
        <div class="space-y-5">


            {{-- Akreditasi & Indeksasi --}}
            @foreach($sidebarBlocks->where('type', 'accreditation') as $block)
                @include('reader.partials.sidebar-block', [
                    'block'   => $block,
                    'journal' => $journal,
                    'stats'   => $journalStats,
                ])
            @endforeach

            {{-- Informasi Jurnal (journal_info blocks) --}}
            @foreach($sidebarBlocks->where('type', 'journal_info') as $block)
                @include('reader.partials.sidebar-block', [
                    'block'   => $block,
                    'journal' => $journal,
                    'stats'   => $journalStats,
                ])
            @endforeach

            {{-- Publisher Info --}}
            @if($journal->publisher || $journal->mailing_address || $journal->email || $journal->contact_name || $journal->contact_phone || $journal->tech_support_email)
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3" style="background:linear-gradient(135deg,#0f172a,#1e3a5f);">
                    <p class="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        Informasi Penerbit
                    </p>
                </div>
                <div class="p-4 space-y-3">

                    @if($journal->publisher)
                    <div class="flex gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5" style="background:#eff6ff;">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Penerbit</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $journal->publisher }}</p>
                        </div>
                    </div>
                    @endif

                    @if($journal->mailing_address)
                    <div class="flex gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5" style="background:#f0fdf4;">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Alamat</p>
                            <p class="text-sm text-slate-700 leading-relaxed">{{ $journal->mailing_address }}</p>
                        </div>
                    </div>
                    @endif

                    @if($journal->email)
                    <div class="flex gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5" style="background:#faf5ff;">
                            <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Email Redaksi</p>
                            <a href="mailto:{{ $journal->email }}" class="text-sm font-medium text-blue-600 hover:underline break-all">{{ $journal->email }}</a>
                        </div>
                    </div>
                    @endif

                    @if($journal->contact_name || $journal->contact_phone)
                    <div class="flex gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5" style="background:#fff7ed;">
                            <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Kontak</p>
                            @if($journal->contact_name)
                            <p class="text-sm font-semibold text-slate-800">{{ $journal->contact_name }}</p>
                            @endif
                            @if($journal->contact_phone)
                            <a href="tel:{{ $journal->contact_phone }}" class="text-sm text-slate-600 hover:text-blue-600 transition-colors">{{ $journal->contact_phone }}</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($journal->tech_support_name || $journal->tech_support_email)
                    <div class="flex gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5" style="background:#f0fdf4;">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Technical Support</p>
                            @if($journal->tech_support_name)
                            <p class="text-sm font-semibold text-slate-800">{{ $journal->tech_support_name }}</p>
                            @endif
                            @if($journal->tech_support_email)
                            <a href="mailto:{{ $journal->tech_support_email }}" class="text-sm text-blue-600 hover:underline">{{ $journal->tech_support_email }}</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($journal->country)
                    @php
                        $countryName = \Locale::getDisplayRegion('-'.$journal->country, 'id') ?: $journal->country;
                    @endphp
                    <div class="pt-2 border-t border-slate-100 flex items-center gap-2">
                        <span class="text-xs text-slate-400">Negara:</span>
                        <span class="text-xs font-semibold text-slate-700">{{ $countryName }}</span>
                    </div>
                    @endif

                </div>
            </div>
            @endif

            {{-- Browse --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3" style="background:#1e40af;">
                    <p class="text-xs font-bold text-white uppercase tracking-widest">Jelajahi</p>
                </div>
                <div class="p-4">
                    <ul class="space-y-1 text-sm">
                        <li>
                            <a href="{{ route('journals.issues', $journal->slug) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                {{ __('site.issues') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('journals.browse', [$journal->slug, 'author']) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Berdasarkan Penulis
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('journals.browse', [$journal->slug, 'title']) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h12"/></svg>
                                Berdasarkan Judul
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('journals.browse', [$journal->slug, 'keyword']) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                Berdasarkan Kata Kunci
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Past issues --}}
            @if($pastIssues->isNotEmpty())
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3" style="background:#1e40af;">
                    <p class="text-xs font-bold text-white uppercase tracking-widest">{{ __('site.issue_archive') }}</p>
                </div>
                <div class="p-4">
                    <ul class="space-y-1 text-sm">
                        @foreach($pastIssues as $pi)
                        <li>
                            <a href="{{ route('journals.issues.show', [$journal->slug, $pi->id]) }}"
                               class="flex items-center gap-2 px-2 py-1.5 rounded text-slate-700 hover:text-blue-700 hover:bg-blue-50 transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                {{ $pi->getLabel() }}
                            </a>
                        </li>
                        @endforeach
                        <li class="pt-2 border-t border-slate-100">
                            <a href="{{ route('journals.issues', $journal->slug) }}"
                               class="text-xs text-blue-600 hover:underline">Lihat semua terbitan →</a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            {{-- submission block handled via sidebarBlocks below --}}

            {{-- ── Admin-configured sidebar blocks ───────────────────── --}}
            @foreach($sidebarBlocks->whereNotIn('type', ['journal_info','accreditation']) as $block)
                @include('reader.partials.sidebar-block', [
                    'block'   => $block,
                    'journal' => $journal,
                    'stats'   => $journalStats,
                ])
            @endforeach

            {{-- Pengumuman — paling bawah --}}
            @if($announcements->isNotEmpty())
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 flex items-center justify-between" style="background:#1e40af;">
                    <p class="text-xs font-bold text-white uppercase tracking-widest">Pengumuman</p>
                    <a href="#pengumuman" class="text-xs text-blue-200 hover:text-white transition-colors font-medium">
                        Lihat semua ↓
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($announcements->take(3) as $ann)
                    <div class="p-4">
                        <p class="font-semibold text-slate-800 text-sm leading-snug mb-0.5">{{ $ann->title }}</p>
                        <p class="text-xs text-slate-400 mb-1.5">{{ $ann->date_posted?->translatedFormat('d M Y') }}</p>
                        @if($ann->description_short)
                        <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                            {{ Str::limit(strip_tags($ann->description_short), 100) }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- end sidebar --}}
    </div>

    {{-- Indexed By & Sponsors --}}
    @if(!empty($indexedBy) || !empty($sponsors))
    @php
    // Map indexer name → local SVG file (relative to public/images/indexers/)
    $indexerLogos = [
        'Google Scholar'   => 'google-scholar.svg',
        'GARUDA'           => 'garuda.svg',
        'Crossref'         => 'crossref.svg',
        'Scopus'           => 'scopus.svg',
        'Web of Science'   => 'wos.svg',
        'Scilit'           => 'scilit.svg',
        'DOAJ'             => 'doaj.svg',
        'Dimensions'       => 'dimensions.svg',
        'Index Copernicus' => 'index-copernicus.svg',
        'BASE'             => 'base.svg',
        'SINTA'            => 'sinta.svg',
        'ROAD'             => 'road.svg',
        'PKP Index'        => 'pkp-index.svg',
    ];
    @endphp
    <div class="mt-8 border-t border-slate-200 pt-8 space-y-6">
        @if(!empty($indexedBy))
        <div>
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Terindeks Oleh</h3>
            <div class="flex flex-wrap items-center gap-4">
                @foreach($indexedBy as $idx)
                @php
                    $localSvg = $indexerLogos[$idx['name']] ?? null;
                    $localPath = $localSvg ? public_path('images/indexers/' . $localSvg) : null;
                    $hasLocalLogo = $localPath && file_exists($localPath);
                @endphp
                @if(!empty($idx['logo']))
                {{-- Logo gambar yang diupload pengelola --}}
                <a href="{{ $idx['url'] ?? '#' }}" target="_blank" rel="noopener"
                   title="{{ $idx['name'] }}"
                   class="block bg-white border border-slate-200 rounded-lg p-2 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                    <img src="{{ asset('storage/' . $idx['logo']) }}" alt="{{ $idx['name'] }}"
                         class="h-8 w-auto object-contain max-w-[100px]">
                </a>
                @elseif($hasLocalLogo)
                {{-- Logo SVG bawaan sistem --}}
                <a href="{{ $idx['url'] ?? '#' }}" target="_blank" rel="noopener"
                   title="{{ $idx['name'] }}"
                   class="block hover:opacity-80 hover:-translate-y-0.5 transition-all">
                    <img src="{{ asset('images/indexers/' . $localSvg) }}" alt="{{ $idx['name'] }}"
                         class="h-10 w-auto object-contain" style="max-width:120px;">
                </a>
                @else
                {{-- Fallback text badge --}}
                <a href="{{ $idx['url'] ?? '#' }}" target="_blank" rel="noopener"
                   class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:border-blue-300 hover:text-blue-700 transition-all shadow-sm hover:-translate-y-0.5">
                    {{ $idx['name'] }}
                </a>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($sponsors))
        <div>
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Sponsor & Mitra</h3>
            <div class="flex flex-wrap items-center gap-6">
                @foreach($sponsors as $sp)
                @if(!empty($sp['logo']))
                <a href="{{ $sp['url'] ?? '#' }}" target="_blank" rel="noopener"
                   class="block hover:opacity-80 transition-opacity">
                    <img src="{{ asset('storage/' . $sp['logo']) }}" alt="{{ $sp['name'] }}"
                         class="h-12 w-auto object-contain grayscale hover:grayscale-0 transition-all">
                </a>
                @else
                <a href="{{ $sp['url'] ?? '#' }}" target="_blank" rel="noopener"
                   class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:border-orange-300 hover:text-orange-700 transition-all shadow-sm">
                    {{ $sp['name'] }}
                </a>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

</div>
</div>{{-- end site-bg wrapper --}}

</div>

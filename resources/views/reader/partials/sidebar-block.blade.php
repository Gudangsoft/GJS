{{--
    Sidebar block partial.
    Props: $block (JournalSidebarBlock), $journal (Journal), $stats (array, optional)
--}}
@php
    $blockTitle = $block->getDisplayTitle();
@endphp

{{-- ── submission: full-gradient card ────────────────────────────────── --}}
@if($block->type === 'submission')
@php
    $submitUrl   = $block->setting('button_url') ?: route('submit');
    $submitLabel = $block->setting('button_label', 'Kirim Naskah Sekarang');
    $callText    = $block->setting('call_text');
@endphp
<div class="rounded-2xl overflow-hidden shadow-sm" style="background:linear-gradient(135deg,#1e40af 0%,#4338ca 100%);">
    <div class="p-5 text-center">
        <svg class="w-9 h-9 mx-auto mb-3 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="font-bold text-white text-sm mb-1.5">Kirim Naskah Anda</p>
        @if($callText)
        <p class="text-xs text-blue-200 leading-relaxed mb-4">{{ $callText }}</p>
        @else
        <p class="text-xs text-blue-200 mb-4">Naskah penelitian, review, dan studi kasus</p>
        @endif
        @auth
        <a href="{{ $submitUrl }}"
           class="block w-full py-2.5 px-4 bg-white text-blue-800 rounded-xl text-sm font-bold hover:bg-blue-50 transition-colors">
            {{ $submitLabel }}
        </a>
        @else
        <a href="{{ route('login') }}"
           class="block w-full py-2.5 px-4 bg-white text-blue-800 rounded-xl text-sm font-bold hover:bg-blue-50 transition-colors">
            Masuk untuk Submit
        </a>
        @endauth
    </div>
</div>

@else

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-4 py-2.5" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ $blockTitle }}</p>
    </div>

    <div class="p-4">

    {{-- ── accreditation ────────────────────────────────────────────── --}}
    @if($block->type === 'accreditation')
    @php
        $sintaLevel  = $journal->sinta_level;   // S1–S6 or null
        $sintaId     = $journal->sinta_id;
        $sintaScore  = $journal->sinta_score;
        $sintaScore3 = $journal->sinta_score_3yr;
        $accNo       = $journal->accreditation_no;
        $accPeriod   = $journal->accreditation_period;
        $doajId      = $journal->doaj_id;
        $garudaId    = $journal->garuda_id;

        $sintaUrl = $block->setting('url_sinta')
            ?: ($sintaId ? 'https://sinta.kemdikbud.go.id/journals/profile/'.$sintaId : null);

        $sintaColors = [
            'S1' => ['bg'=>'#b91c1c','text'=>'#fff','ring'=>'#fca5a5','label'=>'SINTA 1'],
            'S2' => ['bg'=>'#15803d','text'=>'#fff','ring'=>'#86efac','label'=>'SINTA 2'],
            'S3' => ['bg'=>'#1d4ed8','text'=>'#fff','ring'=>'#93c5fd','label'=>'SINTA 3'],
            'S4' => ['bg'=>'#c2410c','text'=>'#fff','ring'=>'#fdba74','label'=>'SINTA 4'],
            'S5' => ['bg'=>'#7c3aed','text'=>'#fff','ring'=>'#c4b5fd','label'=>'SINTA 5'],
            'S6' => ['bg'=>'#475569','text'=>'#fff','ring'=>'#cbd5e1','label'=>'SINTA 6'],
        ];
        $sc = $sintaLevel ? ($sintaColors[$sintaLevel] ?? $sintaColors['S6']) : null;

        $customIndexes = $block->setting('custom_indexes')
            ? array_map('trim', explode(',', $block->setting('custom_indexes')))
            : [];
    @endphp

    {{-- SINTA Badge --}}
    @if($block->setting('show_sinta', true) && $sintaLevel && $sc)
    <div class="mb-4">
        @if($sintaUrl)
        <a href="{{ $sintaUrl }}" target="_blank" rel="noopener" class="block group">
        @endif
            <div class="flex items-stretch rounded-xl overflow-hidden border-2 transition-shadow group-hover:shadow-md"
                 style="border-color:{{ $sc['ring'] }};">
                {{-- SINTA wordmark panel --}}
                <div class="flex flex-col items-center justify-center px-3 py-3 shrink-0"
                     style="background:{{ $sc['bg'] }};min-width:60px;">
                    <span class="font-black text-white tracking-widest" style="font-size:0.6rem;letter-spacing:.15em;">SINTA</span>
                    <span class="font-black text-white leading-none mt-0.5" style="font-size:2rem;">{{ substr($sintaLevel,1) }}</span>
                </div>
                {{-- Info panel --}}
                <div class="flex-1 px-3 py-2.5" style="background:{{ $sc['ring'] }}18;">
                    <p class="font-black text-xs leading-tight" style="color:{{ $sc['bg'] }};">{{ $sc['label'] }}</p>
                    <p class="text-xs font-medium text-slate-600 mt-0.5">Terakreditasi</p>
                    @if($sintaScore && $block->setting('show_sinta_score', true))
                    <p class="text-xs text-slate-500 mt-1">
                        Skor: <strong style="color:{{ $sc['bg'] }}">{{ $sintaScore }}</strong>
                        @if($sintaScore3) · 3yr: <strong>{{ $sintaScore3 }}</strong>@endif
                    </p>
                    @endif
                    @if($accPeriod)
                    <p class="text-xs text-slate-400 mt-0.5">Periode {{ $accPeriod }}</p>
                    @endif
                </div>
            </div>
        @if($sintaUrl)
        </a>
        @endif
    </div>
    @endif

    {{-- No SK Akreditasi --}}
    @if($block->setting('show_accreditation_no', true) && $accNo)
    @php $urlSk = $block->setting('url_sk'); @endphp
    <div class="mb-3 text-xs text-slate-500 flex items-start gap-1.5">
        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        <div>
            <span class="font-semibold text-slate-600">No. SK:</span> {{ $accNo }}
            @if($accPeriod) <span class="text-slate-400">({{ $accPeriod }})</span>@endif
            @if($urlSk)
            <br>
            <a href="{{ $urlSk }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1 mt-1 font-semibold text-green-700 hover:text-green-900 hover:underline">
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                Lihat Dokumen SK
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- Indexing badges --}}
    @php
        $indexes = [];
        if ($block->setting('show_garuda', true) && ($garudaId || $block->setting('url_garuda')))
            $indexes[] = ['label'=>'Garuda','color'=>'#1d4ed8',
                'url'=> $block->setting('url_garuda') ?: ($garudaId ? 'https://garuda.kemdikbud.go.id/journal/'.$garudaId : null)];
        if ($block->setting('show_doaj', false) && ($doajId || $block->setting('url_doaj')))
            $indexes[] = ['label'=>'DOAJ','color'=>'#16a34a',
                'url'=> $block->setting('url_doaj') ?: ($doajId ? 'https://doaj.org/toc/'.$doajId : null)];
        if ($block->setting('show_google_scholar', true))
            $indexes[] = ['label'=>'Google Scholar','color'=>'#4285f4','url'=>$block->setting('url_google_scholar') ?: null];
        if ($block->setting('show_scopus', false))
            $indexes[] = ['label'=>'Scopus','color'=>'#e97706','url'=>$block->setting('url_scopus') ?: null];
        if ($block->setting('show_wos', false))
            $indexes[] = ['label'=>'Web of Science','color'=>'#7c3aed','url'=>$block->setting('url_wos') ?: null];
        foreach ($customIndexes as $ci)
            $indexes[] = ['label'=>$ci,'color'=>'#475569','url'=>null];
        foreach ($block->setting('extra_indexes', []) as $xi)
            if (!empty(trim($xi['label'] ?? '')))
                $indexes[] = ['label'=>trim($xi['label']),'color'=>'#475569','url'=>trim($xi['url'] ?? '') ?: null];
    @endphp
    @if(!empty($indexes))
    <div class="mt-2">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Terindeks di</p>
        <div class="flex flex-wrap gap-1.5">
            @foreach($indexes as $idx)
            @if($idx['url'])
            <a href="{{ $idx['url'] }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full transition-opacity hover:opacity-80"
               style="background:{{ $idx['color'] }}18;color:{{ $idx['color'] }};border:1px solid {{ $idx['color'] }}40;">
                <svg class="w-2.5 h-2.5 shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                {{ $idx['label'] }}
            </a>
            @else
            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background:{{ $idx['color'] }}18;color:{{ $idx['color'] }};border:1px solid {{ $idx['color'] }}40;">
                <svg class="w-2.5 h-2.5 shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                {{ $idx['label'] }}
            </span>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- SINTA link button --}}
    @if($sintaUrl)
    <a href="{{ $sintaUrl }}" target="_blank" rel="noopener"
       class="mt-3 flex items-center justify-center gap-1.5 w-full text-xs font-semibold px-3 py-2 rounded-lg transition-colors"
       style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;"
       onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
        Lihat di SINTA
    </a>
    @endif

    {{-- ── journal_info ──────────────────────────────────────────────── --}}
    @elseif($block->type === 'journal_info')
        <dl class="space-y-2.5 text-sm">
            @if($journal->name)
            <div>
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Nama Jurnal</dt>
                <dd class="font-medium text-slate-800 leading-snug">{{ $journal->name }}</dd>
            </div>
            @endif

            @if($block->setting('show_issn_print', true) && $journal->issn_print)
            <div class="flex items-center justify-between">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">ISSN Cetak</dt>
                <dd class="font-mono font-semibold text-slate-800 text-xs">{{ $journal->issn_print }}</dd>
            </div>
            @endif

            @if($block->setting('show_issn_online', true) && $journal->issn_online)
            <div class="flex items-center justify-between">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">e-ISSN</dt>
                <dd class="font-mono font-semibold text-slate-800 text-xs">{{ $journal->issn_online }}</dd>
            </div>
            @endif

            @if($block->setting('show_publisher', true) && $journal->publisher)
            <div>
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Penerbit</dt>
                <dd class="text-slate-700 text-sm leading-snug">{{ $journal->publisher }}</dd>
            </div>
            @endif

            @if($block->setting('show_doi_prefix', false) && $journal->settings['doi_prefix'] ?? null)
            <div class="flex items-center justify-between">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">DOI Prefix</dt>
                <dd class="font-mono text-blue-600 text-xs">{{ $journal->settings['doi_prefix'] }}</dd>
            </div>
            @endif

            @if($block->setting('show_review_mode', true) && $journal->review_mode)
            <div>
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Peer Review</dt>
                <dd class="text-slate-700">
                    {{ match($journal->review_mode) {
                        'single_blind' => 'Single Blind',
                        'double_blind' => 'Double Blind',
                        'triple_blind' => 'Triple Blind',
                        'open'         => 'Open Review',
                        default        => $journal->review_mode,
                    } }}
                </dd>
            </div>
            @endif

            @if($block->setting('show_frequency', false) && $block->setting('frequency_text'))
            <div>
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Frekuensi Terbit</dt>
                <dd class="text-slate-700">{{ $block->setting('frequency_text') }}</dd>
            </div>
            @endif

            @if($journal->primary_locale)
            <div class="flex items-center justify-between">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Bahasa</dt>
                <dd class="text-slate-700 text-sm">{{ strtoupper($journal->primary_locale) === 'ID' ? 'Indonesia' : 'English' }}</dd>
            </div>
            @endif

            @if($journal->email)
            <div>
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Kontak</dt>
                <dd><a href="mailto:{{ $journal->email }}" class="text-blue-600 hover:underline text-xs break-all">{{ $journal->email }}</a></dd>
            </div>
            @endif
        </dl>

    {{-- ── article_template ───────────────────────────────────────────── --}}
    @elseif($block->type === 'article_template')
        @if($block->setting('description'))
        <p class="text-xs text-slate-500 leading-relaxed mb-3">{{ $block->setting('description') }}</p>
        @endif
        <div class="flex flex-col gap-2">
            @if($block->setting('file_docx'))
            <a href="{{ Storage::disk('public')->url($block->setting('file_docx')) }}"
               download
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-semibold text-sm transition-colors"
               style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;"
               onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <span class="flex-1 text-left">{{ $block->setting('label_docx', 'Unduh Template (DOCX)') }}</span>
                <svg class="w-4 h-4 shrink-0 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            </a>
            @endif

            @if($block->setting('file_pdf'))
            <a href="{{ Storage::disk('public')->url($block->setting('file_pdf')) }}"
               download
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-semibold text-sm transition-colors"
               style="background:#fff1f2;color:#b91c1c;border:1px solid #fecaca;"
               onmouseover="this.style.background='#ffe4e6'" onmouseout="this.style.background='#fff1f2'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <span class="flex-1 text-left">{{ $block->setting('label_pdf', 'Unduh Panduan (PDF)') }}</span>
                <svg class="w-4 h-4 shrink-0 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            </a>
            @endif

            @if(!$block->setting('file_docx') && !$block->setting('file_pdf'))
            <p class="text-xs text-slate-400 italic text-center py-2">Belum ada file template yang diunggah.</p>
            @endif
        </div>

    {{-- ── statistics ──────────────────────────────────────────────────── --}}
    @elseif($block->type === 'statistics')
        @php
            $jStats = $stats ?? [];
        @endphp
        <dl class="space-y-2">
            @if($block->setting('show_articles', true))
            <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                <dt class="text-xs text-slate-500 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Total Artikel
                </dt>
                <dd class="font-black text-blue-700 text-sm">{{ number_format($jStats['articles'] ?? 0) }}</dd>
            </div>
            @endif
            @if($block->setting('show_issues', true))
            <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                <dt class="text-xs text-slate-500 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    Total Terbitan
                </dt>
                <dd class="font-black text-purple-700 text-sm">{{ number_format($jStats['issues'] ?? 0) }}</dd>
            </div>
            @endif
            @if($block->setting('show_views', false))
            <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                <dt class="text-xs text-slate-500 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Total Tayangan
                </dt>
                <dd class="font-black text-emerald-700 text-sm">{{ number_format($jStats['views'] ?? 0) }}</dd>
            </div>
            @endif
            @if($block->setting('show_downloads', false))
            <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                <dt class="text-xs text-slate-500 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Total Unduhan
                </dt>
                <dd class="font-black text-red-700 text-sm">{{ number_format($jStats['downloads'] ?? 0) }}</dd>
            </div>
            @endif
            @if($block->setting('show_citations', false))
            <div class="flex items-center justify-between py-1.5">
                <dt class="text-xs text-slate-500 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                    Total Sitasi
                </dt>
                <dd class="font-black text-yellow-700 text-sm">{{ number_format($jStats['citations'] ?? 0) }}</dd>
            </div>
            @endif
        </dl>

    {{-- ── focus_scope ─────────────────────────────────────────────────── --}}
    @elseif($block->type === 'focus_scope')
        @if($journal->focus_scope)
        <div class="text-sm text-slate-600 leading-relaxed prose prose-sm max-w-none">
            {!! $journal->focus_scope !!}
        </div>
        @endif
        @if($block->setting('extra_text'))
        <div class="mt-3 pt-3 border-t border-slate-100 text-sm text-slate-600 leading-relaxed prose prose-sm max-w-none">
            {!! $block->setting('extra_text') !!}
        </div>
        @endif
        @if(!$journal->focus_scope && !$block->setting('extra_text'))
        <p class="text-xs text-slate-400 italic">Fokus & ruang lingkup belum diisi.</p>
        @endif

    {{-- ── current_issue ──────────────────────────────────────────────── --}}
    @elseif($block->type === 'current_issue')
    @php
        $ci = \App\Models\Issue::where('journal_id', $journal->id)
            ->where('published', true)
            ->orderByDesc('current')->orderByDesc('date_published')
            ->first();
        $ciArticles = $ci && $block->setting('show_toc_preview', true)
            ? \App\Models\Article::with('submission')
                ->where('issue_id', $ci->id)
                ->orderBy('sequence')
                ->take((int)($block->setting('max_articles', 5)))
                ->get()
            : collect();
    @endphp
    @if($ci)
        @if($block->setting('show_cover', true) && $ci->cover_image)
        <div class="mb-3 rounded-xl overflow-hidden">
            <img src="{{ Storage::disk('public')->url($ci->cover_image) }}"
                 alt="{{ $ci->cover_image_alt_text ?? $ci->title }}"
                 class="w-full object-cover max-h-52">
        </div>
        @endif
        <p class="text-xs font-bold text-blue-700 mb-1">
            @if($ci->show_volume && $ci->volume) Vol. {{ $ci->volume }} @endif
            @if($ci->show_number && $ci->number) No. {{ $ci->number }} @endif
            @if($ci->show_year && $ci->year) ({{ $ci->year }}) @endif
        </p>
        @if($ci->show_title && $ci->title)
        <p class="text-sm font-semibold text-slate-700 mb-2">{{ $ci->title }}</p>
        @endif
        @if($ciArticles->isNotEmpty())
        <ul class="space-y-2 mb-3">
            @foreach($ciArticles as $ca)
            <li class="border-l-2 border-blue-200 pl-2">
                <a href="{{ route('journals.articles.show', [$journal->slug, $ca->id]) }}"
                   class="text-xs text-slate-700 hover:text-blue-600 leading-snug block">
                   {{ Str::limit($ca->submission->title ?? '', 70) }}
                </a>
            </li>
            @endforeach
        </ul>
        @endif
        <a href="{{ route('journals.issues.show', [$journal->slug, $ci->id]) }}"
           class="block text-center text-xs font-semibold py-2 rounded-lg transition-colors"
           style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;"
           onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
            Lihat Terbitan Lengkap →
        </a>
    @else
        <p class="text-xs text-slate-400 italic">Belum ada terbitan yang dipublikasikan.</p>
    @endif

    {{-- ── most_read ────────────────────────────────────────────────────── --}}
    @elseif($block->type === 'most_read')
    @php
        $metric   = $block->setting('metric', 'views');
        $mrCount  = max(1, (int)$block->setting('count', 5));
        $mrColumn = in_array($metric, ['downloads','views']) ? $metric : 'views';
        $topArts  = \App\Models\Article::with('submission')
            ->where('journal_id', $journal->id)
            ->orderByDesc($mrColumn)
            ->take($mrCount)
            ->get();
    @endphp
    @if($topArts->isNotEmpty())
    <ol class="space-y-3">
        @foreach($topArts as $idx => $ta)
        <li class="flex gap-2.5">
            <span class="shrink-0 w-5 h-5 rounded-full text-xs font-black flex items-center justify-center mt-0.5"
                  style="background:#fffbeb;color:#b45309;border:1px solid #fde68a;">{{ $idx+1 }}</span>
            <div class="flex-1 min-w-0">
                <a href="{{ route('journals.articles.show', [$journal->slug, $ta->id]) }}"
                   class="text-xs text-slate-700 hover:text-amber-700 leading-snug block font-medium">
                   {{ Str::limit($ta->submission->title ?? '', 65) }}
                </a>
                <p class="text-xs text-slate-400 mt-0.5">
                    @if($mrColumn === 'views') {{ number_format($ta->views) }} tayangan
                    @else {{ number_format($ta->downloads) }} unduhan @endif
                </p>
            </div>
        </li>
        @endforeach
    </ol>
    @else
    <p class="text-xs text-slate-400 italic">Belum ada data artikel.</p>
    @endif

    {{-- ── recent_articles ─────────────────────────────────────────────── --}}
    @elseif($block->type === 'recent_articles')
    @php
        $raCount = max(1, (int)$block->setting('count', 5));
        $recent  = \App\Models\Article::with('submission')
            ->where('journal_id', $journal->id)
            ->whereNotNull('date_published')
            ->orderByDesc('date_published')
            ->take($raCount)
            ->get();
    @endphp
    @if($recent->isNotEmpty())
    <ul class="space-y-3">
        @foreach($recent as $ra)
        <li>
            <a href="{{ route('journals.articles.show', [$journal->slug, $ra->id]) }}"
               class="text-xs text-slate-700 hover:text-cyan-700 leading-snug block font-medium">
               {{ Str::limit($ra->submission->title ?? '', 70) }}
            </a>
            @if($ra->date_published)
            <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($ra->date_published)->translatedFormat('d M Y') }}</p>
            @endif
        </li>
        @endforeach
    </ul>
    @else
    <p class="text-xs text-slate-400 italic">Belum ada artikel yang diterbitkan.</p>
    @endif

    {{-- ── keyword_cloud ───────────────────────────────────────────────── --}}
    @elseif($block->type === 'keyword_cloud')
    @php
        $maxKw = max(10, (int)$block->setting('max_keywords', 30));
        $kwRaw = \App\Models\Submission::whereHas('article', fn($q) => $q->where('journal_id', $journal->id))
            ->whereNotNull('keywords')
            ->pluck('keywords');
        $kwCounts = collect();
        foreach ($kwRaw as $kwArr) {
            foreach ((array)$kwArr as $kw) {
                $k = mb_strtolower(trim($kw));
                if ($k) $kwCounts[$k] = ($kwCounts[$k] ?? 0) + 1;
            }
        }
        $kwCounts = $kwCounts->sortDesc()->take($maxKw);
        $maxCount = $kwCounts->max() ?: 1;
    @endphp
    @if($kwCounts->isNotEmpty())
    <div class="flex flex-wrap gap-1.5">
        @foreach($kwCounts as $kw => $cnt)
        @php
            $size   = 10 + round(($cnt / $maxCount) * 5);
            $weight = $cnt >= ($maxCount * .6) ? '700' : '500';
            $alpha  = 0.5 + round(($cnt / $maxCount) * 0.5, 2);
        @endphp
        <a href="{{ route('journals.home', $journal->slug) }}?kw={{ urlencode($kw) }}"
           class="inline-block rounded-full px-2.5 py-1 transition-opacity hover:opacity-80"
           style="font-size:{{ $size }}px;font-weight:{{ $weight }};background:rgba(109,40,217,{{ round($alpha*0.15,2) }});color:rgba(109,40,217,{{ round(0.6+$alpha*0.4,2) }});border:1px solid rgba(109,40,217,{{ round($alpha*0.25,2) }});">
            {{ $kw }}
        </a>
        @endforeach
    </div>
    @else
    <p class="text-xs text-slate-400 italic">Belum ada kata kunci tersedia.</p>
    @endif

    {{-- ── announcements_list ──────────────────────────────────────────── --}}
    @elseif($block->type === 'announcements_list')
    @php
        $alCount = max(1, (int)$block->setting('count', 3));
        $annList = \App\Models\Announcement::where('journal_id', $journal->id)
            ->where(fn($q) => $q->whereNull('date_expire')->orWhere('date_expire', '>=', now()))
            ->orderByDesc('date_posted')
            ->take($alCount)
            ->get();
    @endphp
    @if($annList->isNotEmpty())
    <ul class="space-y-3">
        @foreach($annList as $ann)
        <li class="border-b border-slate-50 pb-3 last:border-0 last:pb-0">
            <a href="{{ route('journals.home', $journal->slug) }}#ann-{{ $ann->id }}"
               class="text-xs font-semibold text-slate-700 hover:text-teal-700 leading-snug block">
               {{ Str::limit($ann->title, 65) }}
            </a>
            @if($block->setting('show_date', true) && $ann->date_posted)
            <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($ann->date_posted)->translatedFormat('d M Y') }}</p>
            @endif
            @if($ann->description_short)
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ Str::limit(strip_tags($ann->description_short), 80) }}</p>
            @endif
        </li>
        @endforeach
    </ul>
    @else
    <p class="text-xs text-slate-400 italic">Belum ada pengumuman.</p>
    @endif

    {{-- ── open_access ──────────────────────────────────────────────────── --}}
    @elseif($block->type === 'open_access')
    @php
        $license   = $block->setting('license', $journal->license_type ?? 'cc_by');
        $ccMap = [
            'cc_by'       => ['label'=>'CC BY 4.0',       'icon'=>'🅭🅯',     'color'=>'#15803d', 'url'=>'https://creativecommons.org/licenses/by/4.0/'],
            'cc_by_nc'    => ['label'=>'CC BY-NC 4.0',    'icon'=>'🅭🅯🅮',  'color'=>'#0369a1', 'url'=>'https://creativecommons.org/licenses/by-nc/4.0/'],
            'cc_by_nc_nd' => ['label'=>'CC BY-NC-ND 4.0', 'icon'=>'🅭🅯🅮⊝', 'color'=>'#7c3aed','url'=>'https://creativecommons.org/licenses/by-nc-nd/4.0/'],
            'cc_by_nc_sa' => ['label'=>'CC BY-NC-SA 4.0', 'icon'=>'🅭🅯🅮🄎', 'color'=>'#b45309','url'=>'https://creativecommons.org/licenses/by-nc-sa/4.0/'],
            'cc_by_sa'    => ['label'=>'CC BY-SA 4.0',    'icon'=>'🅭🄎',     'color'=>'#0f766e', 'url'=>'https://creativecommons.org/licenses/by-sa/4.0/'],
        ];
        $cc = $ccMap[$license] ?? $ccMap['cc_by'];
        $stmt = $block->setting('custom_statement') ?: $journal->open_access_statement
            ?: 'Jurnal ini adalah jurnal akses terbuka yang memungkinkan semua konten tersedia secara gratis kepada pengguna. Pengguna diizinkan untuk membaca, mengunduh, menyalin, mendistribusikan, mencetak, mencari, atau menautkan ke teks lengkap artikel.';
    @endphp
    <a href="{{ $cc['url'] }}" target="_blank" rel="noopener"
       class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl mb-3 transition-colors"
       style="background:{{ $cc['color'] }}12;border:1px solid {{ $cc['color'] }}30;"
       onmouseover="this.style.background='{{ $cc['color'] }}20'" onmouseout="this.style.background='{{ $cc['color'] }}12'">
        <span class="text-2xl leading-none">©</span>
        <div>
            <p class="text-xs font-bold" style="color:{{ $cc['color'] }};">Open Access</p>
            <p class="text-xs" style="color:{{ $cc['color'] }}80;">{{ $cc['label'] }}</p>
        </div>
    </a>
    @if($block->setting('show_statement', true))
    <p class="text-xs text-slate-500 leading-relaxed">{{ Str::limit(strip_tags($stmt), 200) }}</p>
    @endif

    {{-- ── peer_review ──────────────────────────────────────────────────── --}}
    @elseif($block->type === 'peer_review')
    @php
        $reviewModes = [
            'single_blind' => 'Single Blind',
            'double_blind' => 'Double Blind',
            'triple_blind' => 'Triple Blind',
            'open'         => 'Open Review',
        ];
        $modeName = $reviewModes[$journal->review_mode ?? ''] ?? $journal->review_mode;
        $weeks    = $journal->num_weeks_per_review ?? null;
    @endphp
    <div class="space-y-3">
        @if($block->setting('show_mode', true) && $modeName)
        <div class="flex items-center gap-2.5 p-2.5 rounded-lg" style="background:#eff6ff;">
            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="text-xs text-blue-500 font-semibold uppercase tracking-wide">Sistem Review</p>
                <p class="text-sm font-bold text-blue-800">{{ $modeName }}</p>
            </div>
        </div>
        @endif
        @if($block->setting('show_duration', true) && $weeks)
        <div class="flex items-center gap-2.5 p-2.5 rounded-lg" style="background:#f0fdf4;">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="text-xs text-green-500 font-semibold uppercase tracking-wide">Durasi Review</p>
                <p class="text-sm font-bold text-green-800">{{ $weeks }} Minggu</p>
            </div>
        </div>
        @endif
        @if($block->setting('custom_text'))
        <p class="text-xs text-slate-500 leading-relaxed">{{ $block->setting('custom_text') }}</p>
        @endif
    </div>

    {{-- ── social_links ────────────────────────────────────────────────── --}}
    @elseif($block->type === 'social_links')
    @php
        $socials = [
            ['key'=>'url_twitter',   'label'=>'Twitter / X', 'color'=>'#000000',
             'icon'=>'<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.741l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'],
            ['key'=>'url_facebook',  'label'=>'Facebook',    'color'=>'#1877f2',
             'icon'=>'<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>'],
            ['key'=>'url_instagram', 'label'=>'Instagram',   'color'=>'#e1306c',
             'icon'=>'<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>'],
            ['key'=>'url_youtube',   'label'=>'YouTube',     'color'=>'#ff0000',
             'icon'=>'<path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>'],
            ['key'=>'url_linkedin',  'label'=>'LinkedIn',    'color'=>'#0a66c2',
             'icon'=>'<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>'],
            ['key'=>'url_telegram',  'label'=>'Telegram',    'color'=>'#2CA5E0',
             'icon'=>'<path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>'],
        ];
        $activeSocials = array_filter($socials, fn($s) => !empty(trim($block->setting($s['key']) ?? '')));
    @endphp
    @if(!empty($activeSocials))
    <div class="flex flex-wrap gap-2">
        @foreach($activeSocials as $s)
        <a href="{{ $block->setting($s['key']) }}" target="_blank" rel="noopener"
           title="{{ $s['label'] }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform hover:scale-110 hover:shadow-md"
           style="background:{{ $s['color'] }}18;border:1px solid {{ $s['color'] }}30;">
            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="{{ $s['color'] }}">{!! $s['icon'] !!}</svg>
        </a>
        @endforeach
    </div>
    @else
    <p class="text-xs text-slate-400 italic">Belum ada link media sosial yang dikonfigurasi.</p>
    @endif

    {{-- ── preservation ────────────────────────────────────────────────── --}}
    @elseif($block->type === 'preservation')
    @php
        $badges = [
            ['key'=>'show_lockss',   'url_key'=>'url_lockss',   'label'=>'LOCKSS',    'desc'=>'Lots of Copies Keep Stuff Safe',    'color'=>'#1d4ed8'],
            ['key'=>'show_pkp_pn',   'url_key'=>null,            'label'=>'PKP PN',    'desc'=>'PKP Preservation Network',         'color'=>'#7c3aed'],
            ['key'=>'show_portico',  'url_key'=>'url_portico',  'label'=>'Portico',    'desc'=>'Digital Preservation Service',      'color'=>'#0891b2'],
            ['key'=>'show_clockss',  'url_key'=>null,            'label'=>'CLOCKSS',   'desc'=>'Controlled Lots of Copies',         'color'=>'#0f766e'],
            ['key'=>'show_cope',     'url_key'=>'url_cope',     'label'=>'COPE',       'desc'=>'Committee on Publication Ethics',   'color'=>'#b45309'],
            ['key'=>'show_crossref', 'url_key'=>null,            'label'=>'CrossRef',  'desc'=>'DOI Registration Agency',          'color'=>'#15803d'],
            ['key'=>'show_mendeley', 'url_key'=>null,            'label'=>'Mendeley',  'desc'=>'Reference Manager',                'color'=>'#dc2626'],
        ];
        $activeBadges = array_filter($badges, fn($b) => $block->setting($b['key'], false));
    @endphp
    @if(!empty($activeBadges))
    <div class="space-y-2">
        @foreach($activeBadges as $b)
        @php $bUrl = $b['url_key'] ? $block->setting($b['url_key']) : null; @endphp
        <div class="{{ $bUrl ? '' : '' }}">
            @if($bUrl)<a href="{{ $bUrl }}" target="_blank" rel="noopener">@endif
            <div class="flex items-center gap-2 px-2.5 py-2 rounded-lg transition-colors {{ $bUrl ? 'hover:opacity-80' : '' }}"
                 style="background:{{ $b['color'] }}10;border:1px solid {{ $b['color'] }}25;">
                <div class="w-2 h-2 rounded-full shrink-0" style="background:{{ $b['color'] }};"></div>
                <div>
                    <p class="text-xs font-bold" style="color:{{ $b['color'] }};">{{ $b['label'] }}</p>
                    <p class="text-xs text-slate-400">{{ $b['desc'] }}</p>
                </div>
            </div>
            @if($bUrl)</a>@endif
        </div>
        @endforeach
    </div>
    @else
    <p class="text-xs text-slate-400 italic">Belum ada layanan pengarsipan yang diaktifkan.</p>
    @endif

    {{-- ── custom_html ─────────────────────────────────────────────────── --}}
    @elseif($block->type === 'custom_html')
        @if($block->setting('html'))
        <div class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none">
            {!! $block->setting('html') !!}
        </div>
        @else
        <p class="text-xs text-slate-400 italic">Konten belum diisi.</p>
        @endif

    @endif

    </div>
</div>

@endif{{-- end submission @else --}}

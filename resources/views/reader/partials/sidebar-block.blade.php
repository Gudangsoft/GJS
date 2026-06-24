{{--
    Sidebar block partial.
    Props: $block (JournalSidebarBlock), $journal (Journal), $stats (array, optional)
--}}
@php
    $blockTitle = $block->getDisplayTitle();
@endphp

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

        $sintaUrl = $block->setting('sinta_url_override')
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
    <div class="mb-3 text-xs text-slate-500 flex items-start gap-1.5">
        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        <div>
            <span class="font-semibold text-slate-600">No. SK:</span> {{ $accNo }}
            @if($accPeriod) <span class="text-slate-400">({{ $accPeriod }})</span>@endif
        </div>
    </div>
    @endif

    {{-- Indexing badges --}}
    @php
        $indexes = [];
        if ($block->setting('show_garuda', true) && $garudaId) $indexes[] = ['label'=>'Garuda','color'=>'#1d4ed8','url'=>'https://garuda.kemdikbud.go.id/journal/'.$garudaId];
        if ($block->setting('show_doaj', false) && $doajId)    $indexes[] = ['label'=>'DOAJ','color'=>'#16a34a','url'=>'https://doaj.org/toc/'.$doajId];
        if ($block->setting('show_google_scholar', true))       $indexes[] = ['label'=>'Google Scholar','color'=>'#4285f4','url'=>null];
        foreach ($customIndexes as $ci)                         $indexes[] = ['label'=>$ci,'color'=>'#475569','url'=>null];
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
        </dl>

    {{-- ── submission ─────────────────────────────────────────────────── --}}
    @elseif($block->type === 'submission')
        @if($block->setting('call_text'))
        <p class="text-sm text-slate-600 leading-relaxed mb-3">
            {{ $block->setting('call_text') }}
        </p>
        @endif
        @php
            $submitUrl   = $block->setting('button_url') ?: route('journals.home', $journal->slug);
            $submitLabel = $block->setting('button_label', 'Kirim Naskah');
        @endphp
        <a href="{{ $submitUrl }}"
           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl font-semibold text-sm transition-all hover:brightness-110 active:scale-95"
           style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            {{ $submitLabel }}
        </a>

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

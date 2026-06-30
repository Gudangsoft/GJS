@php
use Carbon\Carbon;

$statusMap = [
    'draft'             => ['label'=>'Draft',          'color'=>'#64748b','bg'=>'#f1f5f9','border'=>'#cbd5e1','step'=>0],
    'submitted'         => ['label'=>'Dikirim',        'color'=>'#2563eb','bg'=>'#eff6ff','border'=>'#bfdbfe','step'=>1],
    'queued'            => ['label'=>'Antrian Editor', 'color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fde68a','step'=>1],
    'assigned'          => ['label'=>'Ditugaskan',     'color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fde68a','step'=>2],
    'review'            => ['label'=>'Dalam Review',   'color'=>'#7c3aed','bg'=>'#faf5ff','border'=>'#ddd6fe','step'=>2],
    'revision_required' => ['label'=>'Perlu Revisi',   'color'=>'#ea580c','bg'=>'#fff7ed','border'=>'#fed7aa','step'=>3],
    'resubmit'          => ['label'=>'Resubmit',       'color'=>'#ea580c','bg'=>'#fff7ed','border'=>'#fed7aa','step'=>3],
    'accepted'          => ['label'=>'Diterima',       'color'=>'#16a34a','bg'=>'#f0fdf4','border'=>'#bbf7d0','step'=>4],
    'copyediting'       => ['label'=>'Copy Editing',   'color'=>'#0891b2','bg'=>'#ecfeff','border'=>'#a5f3fc','step'=>4],
    'production'        => ['label'=>'Produksi',       'color'=>'#0891b2','bg'=>'#ecfeff','border'=>'#a5f3fc','step'=>4],
    'scheduled'         => ['label'=>'Terjadwal',      'color'=>'#16a34a','bg'=>'#f0fdf4','border'=>'#bbf7d0','step'=>5],
    'published'         => ['label'=>'Diterbitkan',    'color'=>'#15803d','bg'=>'#f0fdf4','border'=>'#86efac','step'=>5],
    'declined'          => ['label'=>'Ditolak',        'color'=>'#dc2626','bg'=>'#fef2f2','border'=>'#fecaca','step'=>-1],
];
$s      = $statusMap[$submission->status] ?? ['label'=>$submission->status,'color'=>'#64748b','bg'=>'#f1f5f9','border'=>'#cbd5e1','step'=>0];
$step   = $s['step'];

$steps = [
    ['label'=>'Draft',    'icon'=>'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'],
    ['label'=>'Dikirim',  'icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
    ['label'=>'Review',   'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
    ['label'=>'Revisi',   'icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
    ['label'=>'Diterima', 'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['label'=>'Terbit',   'icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
];

$recLabels = [
    'accept'             => ['label'=>'Diterima',            'color'=>'#16a34a','bg'=>'#f0fdf4','border'=>'#86efac','icon'=>'M5 13l4 4L19 7'],
    'pending_revisions'  => ['label'=>'Revisi Minor',        'color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fde68a','icon'=>'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'],
    'resubmit_here'      => ['label'=>'Revisi Mayor',        'color'=>'#ea580c','bg'=>'#fff7ed','border'=>'#fed7aa','icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
    'resubmit_elsewhere' => ['label'=>'Kirim ke Jurnal Lain','color'=>'#dc2626','bg'=>'#fef2f2','border'=>'#fecaca','icon'=>'M17 8l4 4m0 0l-4 4m4-4H3'],
    'decline'            => ['label'=>'Ditolak',             'color'=>'#dc2626','bg'=>'#fef2f2','border'=>'#fecaca','icon'=>'M6 18L18 6M6 6l12 12'],
    'see_comments'       => ['label'=>'Lihat Komentar',      'color'=>'#64748b','bg'=>'#f8fafc','border'=>'#e2e8f0','icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
];
$roundStatusMap = [
    'revisions_requested'=> ['label'=>'Revisi Diminta',    'color'=>'#ea580c','bg'=>'#fff7ed','border'=>'#fed7aa'],
    'awaiting_reviewers' => ['label'=>'Menunggu Reviewer', 'color'=>'#2563eb','bg'=>'#eff6ff','border'=>'#bfdbfe'],
    'reviews_ready'      => ['label'=>'Siap Diputuskan',   'color'=>'#7c3aed','bg'=>'#faf5ff','border'=>'#ddd6fe'],
    'reviews_completed'  => ['label'=>'Review Selesai',    'color'=>'#16a34a','bg'=>'#f0fdf4','border'=>'#bbf7d0'],
    'accepted'           => ['label'=>'Diterima',          'color'=>'#16a34a','bg'=>'#f0fdf4','border'=>'#bbf7d0'],
    'declined'           => ['label'=>'Ditolak',           'color'=>'#dc2626','bg'=>'#fef2f2','border'=>'#fecaca'],
    'pending'            => ['label'=>'Pending',           'color'=>'#64748b','bg'=>'#f8fafc','border'=>'#e2e8f0'],
];
$totalRounds = $submission->reviewRounds->count();
@endphp

<div class="min-h-screen" style="background:#f6f8fb;">

{{-- ══ HERO HEADER ══════════════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#1e3a5f 0%,#1e40af 60%,#312e81 100%);padding:2rem 1.5rem 3rem;">
    <div style="max-width:64rem;margin:0 auto;">

        {{-- Breadcrumb --}}
        <div style="display:flex;align-items:center;gap:.5rem;font-size:.75rem;margin-bottom:1.25rem;">
            <a href="{{ route('dashboard') }}" style="color:#93c5fd;text-decoration:none;display:flex;align-items:center;gap:.25rem;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <span style="color:#60a5fa;">›</span>
            <span style="color:#bfdbfe;">Submission #{{ $submission->id }}</span>
        </div>

        {{-- Status badge --}}
        <div style="margin-bottom:.75rem;">
            <span style="display:inline-block;font-size:.7rem;font-weight:700;padding:.3rem .875rem;border-radius:9999px;letter-spacing:.05em;background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                ● {{ strtoupper($s['label']) }}
            </span>
        </div>

        {{-- Title --}}
        <h1 style="font-size:1.4rem;font-weight:800;color:#ffffff;line-height:1.35;margin-bottom:.625rem;max-width:52rem;">
            {{ $submission->title }}
        </h1>
        @if($submission->subtitle)
        <p style="font-size:.95rem;color:#bfdbfe;margin-bottom:.75rem;">{{ $submission->subtitle }}</p>
        @endif

        {{-- Meta row --}}
        <div style="display:flex;flex-wrap:wrap;gap:.875rem;font-size:.75rem;color:#93c5fd;margin-top:.5rem;">
            @if($submission->journal)
            <span>⊡ {{ $submission->journal->name_abbrev ?? $submission->journal->name }}</span>
            @endif
            @if($submission->submitted_at)
            <span>⊡ Dikirim {{ $submission->submitted_at->format('d M Y') }}</span>
            @endif
            @if($submission->section)
            <span>◇ {{ $submission->section->title }}</span>
            @endif
        </div>

    </div>
</div>

{{-- ══ PROGRESS STEPPER ════════════════════════════════════════════════ --}}
<div style="background:#f0f4f8;padding:.75rem 1.5rem;border-bottom:1px solid #e2e8f0;">
<div style="max-width:64rem;margin:0 auto;">
    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 px-6 py-4 overflow-x-auto">
        <div class="flex items-center min-w-max gap-0">
            @foreach($steps as $i => $st)
            @php
                $isDone    = $step > $i && $step !== -1;
                $isCurrent = $step === $i;
                $declined  = $submission->status === 'declined';
            @endphp
            <div class="flex items-center">
                <div class="flex flex-col items-center gap-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all
                        {{ $declined && $i > 0 ? 'bg-slate-100 text-slate-300' : ($isDone ? 'bg-blue-600 text-white' : ($isCurrent ? 'text-white shadow-md' : 'bg-slate-100 text-slate-400')) }}"
                         style="{{ $isCurrent && !$declined ? 'background:'.$s['color'].';box-shadow:0 0 0 3px '.$s['color'].'40;' : '' }}">
                        @if($isDone)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $st['icon'] }}"/></svg>
                        @endif
                    </div>
                    <span class="text-xs font-medium whitespace-nowrap
                        {{ $isDone ? 'text-blue-600' : ($isCurrent ? 'font-bold' : 'text-slate-400') }}"
                          style="{{ $isCurrent && !$declined ? 'color:'.$s['color'].';' : '' }}">
                        {{ $st['label'] }}
                    </span>
                </div>
                @if($i < count($steps)-1)
                <div class="w-10 sm:w-16 h-0.5 mx-1 mb-4 {{ $isDone ? 'bg-blue-300' : 'bg-slate-200' }}"></div>
                @endif
            </div>
            @endforeach

            @if($submission->status === 'declined')
            <div class="ml-2 flex flex-col items-center gap-1">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <span class="text-xs font-bold text-red-600">Ditolak</span>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

{{-- ══ AKSI MENDESAK (Revisi Required) ═══════════════════════════════ --}}
@if($submission->status === 'revision_required')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
    <div class="rounded-2xl border-2 border-orange-300 overflow-hidden" style="background:linear-gradient(135deg,#fff7ed,#fffbeb);">
        <div class="flex items-start gap-4 p-5">
            <div class="w-10 h-10 rounded-xl bg-orange-100 border border-orange-300 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="flex-1">
                <p class="font-bold text-orange-900 text-sm">Revisi Diperlukan</p>
                <p class="text-xs text-orange-700 mt-0.5 leading-relaxed">
                    Editor telah meninjau komentar reviewer dan meminta Anda melakukan revisi. Baca komentar di bawah, lalu unggah versi naskah yang direvisi.
                </p>
            </div>
            <button class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white shadow transition-all hover:opacity-90 active:scale-95"
                    style="background:linear-gradient(135deg,#ea580c,#dc2626);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Unggah Revisi
            </button>
        </div>
    </div>
</div>
@endif

{{-- ══ MAIN CONTENT ══════════════════════════════════════════════════ --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── KOLOM UTAMA ──────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Abstrak --}}
            @if($submission->getTranslation('abstract', 'id', false) || $submission->getTranslation('abstract', 'en', false))
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Abstrak</h2>
                @if($submission->getTranslation('abstract', 'id', false))
                <div class="mb-3">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">🇮🇩 Bahasa Indonesia</span>
                    <p class="mt-1 text-sm text-slate-700 leading-relaxed">{{ $submission->getTranslation('abstract', 'id', false) }}</p>
                </div>
                @endif
                @if($submission->getTranslation('abstract', 'en', false))
                <div class="@if($submission->getTranslation('abstract', 'id', false)) pt-3 border-t border-slate-100 @endif">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">🇬🇧 English</span>
                    <p class="mt-1 text-sm text-slate-700 leading-relaxed">{{ $submission->getTranslation('abstract', 'en', false) }}</p>
                </div>
                @endif
                @if($submission->keywords)
                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-2">
                    @foreach($submission->keywords as $kw)
                    <span class="text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full">{{ $kw }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            {{-- Published article banner --}}
            @if($submission->article && $submission->status === 'published')
            <div class="rounded-2xl border border-green-200 overflow-hidden shadow-sm"
                 style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
                <div class="flex items-center gap-3 px-5 py-3 border-b border-green-200" style="background:#16a34a10;">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="font-bold text-green-900 text-sm">Artikel Diterbitkan</p>
                </div>
                <div class="p-5 space-y-3">
                    @if($submission->article->issue)
                    <p class="text-sm text-green-800">Diterbitkan di <strong>{{ $submission->article->issue->getLabel() }}</strong></p>
                    @endif
                    @if($submission->article->doi)
                    <div class="flex items-center gap-2">
                        <a href="https://doi.org/{{ $submission->article->doi }}" target="_blank" rel="noopener"
                           class="text-sm text-green-700 hover:underline font-mono">
                            https://doi.org/{{ $submission->article->doi }}
                        </a>
                    </div>
                    @endif
                    @if($submission->article->galleys->isNotEmpty())
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach($submission->article->galleys as $galley)
                        <a class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-lg border border-green-300 text-green-800 bg-white hover:bg-green-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ $galley->label }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ── RIWAYAT REVIEW ─────────────────────────────────── --}}
            @if($submission->reviewRounds->isNotEmpty())
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <h2 class="text-base font-bold text-slate-900">Riwayat Review & Keputusan</h2>
                    <span class="text-xs font-semibold bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full">
                        {{ $totalRounds }} putaran
                    </span>
                </div>

                <div class="space-y-4">
                @foreach($submission->reviewRounds->sortBy('round') as $round)
                @php
                    $isLast  = $round->round === $totalRounds;
                    $rs      = $roundStatusMap[$round->status] ?? ['label'=>$round->status,'color'=>'#64748b','bg'=>'#f8fafc','border'=>'#e2e8f0'];
                    $doneAss = $round->assignments->where('status','completed');
                    $hasComm = $doneAss->filter(fn($a) => $a->review && $a->review->comments_for_author)->count() > 0;
                    $autoOpen = $isLast || $hasComm;
                @endphp
                <div x-data="{ open: {{ $autoOpen ? 'true' : 'false' }} }"
                     class="bg-white rounded-2xl border shadow-sm overflow-hidden transition-all"
                     style="border-color:{{ $isLast ? $rs['border'] : '#e2e8f0' }};">

                    {{-- Round header --}}
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-black text-white shrink-0 shadow-sm"
                                 style="background:{{ $isLast ? $rs['color'] : '#94a3b8' }};">
                                {{ $round->round }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-900 text-sm">Putaran Review {{ $round->round }}</p>
                                <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-2">
                                    <span>{{ $round->assignments->count() }} reviewer</span>
                                    <span class="text-slate-200">·</span>
                                    <span>{{ $doneAss->count() }} selesai</span>
                                    @if($round->created_at)
                                    <span class="text-slate-200">·</span>
                                    <span>{{ $round->created_at->format('d M Y') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-xs font-bold px-2.5 py-1.5 rounded-lg border"
                                  style="background:{{ $rs['bg'] }};color:{{ $rs['color'] }};border-color:{{ $rs['border'] }};">
                                {{ $rs['label'] }}
                            </span>
                            <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center">
                                <svg :class="open ? 'rotate-180' : ''" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </button>

                    {{-- Timeline body --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2">

                        <div class="border-t border-slate-100 px-5 pb-5 pt-4">
                        <div class="relative pl-8">
                            {{-- Vertical line --}}
                            <div class="absolute left-3 top-2 bottom-2 w-px" style="background:linear-gradient(to bottom,#dbeafe,#e0e7ff,#e0e7ff 80%,transparent);"></div>

                            <div class="space-y-6">

                            {{-- EVENT: Editor menugaskan reviewer --}}
                            <div class="relative flex gap-3">
                                <div class="absolute -left-5 w-4 h-4 rounded-full bg-blue-100 border-2 border-blue-300 flex items-center justify-center shrink-0 mt-0.5">
                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-blue-700">Editor</p>
                                    <p class="text-sm font-semibold text-slate-800 mt-0.5">Menugaskan reviewer untuk putaran ini</p>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        {{ $round->assignments->count() }} reviewer dipilih secara double blind
                                        @if($round->created_at) · {{ $round->created_at->format('d M Y') }}@endif
                                    </p>
                                </div>
                            </div>

                            {{-- SETIAP REVIEWER --}}
                            @foreach($round->assignments->sortBy('id') as $idx => $assignment)
                            @php
                                $rn       = $idx + 1;
                                $rv       = $assignment->review;
                                $rec      = $rv ? ($recLabels[$rv->recommendation] ?? ['label'=>$rv->recommendation,'color'=>'#64748b','bg'=>'#f8fafc','border'=>'#e2e8f0','icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z']) : null;
                                $avColors = ['#4f46e5','#0891b2','#059669','#d97706','#9333ea'];
                                $avBg     = $avColors[$idx % count($avColors)];
                            @endphp

                            <div class="relative flex gap-3">
                                {{-- Avatar --}}
                                <div class="absolute -left-6 w-6 h-6 rounded-full flex items-center justify-center shrink-0 text-white text-[10px] font-black border-2 border-white shadow-sm mt-0.5"
                                     style="background:{{ $avBg }}">R{{ $rn }}</div>

                                <div class="flex-1">
                                    <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                                        <div>
                                            <span class="text-xs font-bold" style="color:{{ $avBg }}">Reviewer {{ $rn }}</span>
                                            <span class="text-xs text-slate-400 ml-1">· identitas dirahasiakan</span>
                                        </div>
                                        @if($assignment->date_assigned)
                                        <span class="text-xs text-slate-400">{{ Carbon::parse($assignment->date_assigned)->format('d M Y') }}</span>
                                        @endif
                                    </div>

                                    {{-- Status pill --}}
                                    @php
                                    $asBadge = match($assignment->status){
                                        'completed'         => ['Selesai','#16a34a','#f0fdf4','#bbf7d0'],
                                        'accepted'          => ['Sedang Mereview','#2563eb','#eff6ff','#bfdbfe'],
                                        'awaiting_response' => ['Konfirmasi Ditunggu','#d97706','#fffbeb','#fde68a'],
                                        'declined'          => ['Menolak Penugasan','#dc2626','#fef2f2','#fecaca'],
                                        default             => [$assignment->status,'#64748b','#f8fafc','#e2e8f0'],
                                    };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-lg border"
                                          style="color:{{ $asBadge[0]==='Selesai'?$asBadge[1]:$asBadge[1] }};background:{{ $asBadge[2] }};border-color:{{ $asBadge[3] }};">
                                        @if($assignment->status === 'completed')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @elseif($assignment->status === 'accepted')
                                        <svg class="w-3 h-3 animate-pulse" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                        @endif
                                        {{ $asBadge[0] }}
                                        @if($assignment->date_due && $assignment->status === 'accepted' && !$rv)
                                        <span class="font-normal text-slate-500"> · Jatuh tempo {{ Carbon::parse($assignment->date_due)->format('d M') }}</span>
                                        @endif
                                        @if($assignment->date_completed)
                                        <span class="font-normal opacity-70"> · {{ Carbon::parse($assignment->date_completed)->format('d M Y') }}</span>
                                        @endif
                                    </span>

                                    {{-- Komentar --}}
                                    @if($rv && $rv->comments_for_author)
                                    <div class="mt-3 rounded-xl overflow-hidden border"
                                         style="border-color:{{ $rec['border'] }}">
                                        <div class="flex items-center justify-between px-4 py-2.5"
                                             style="background:{{ $rec['bg'] }};border-bottom:1px solid {{ $rec['border'] }};">
                                            <span class="text-xs font-bold uppercase tracking-wider" style="color:{{ $rec['color'] }};">
                                                Komentar untuk Penulis
                                            </span>
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-lg"
                                                  style="background:{{ $rec['color'] }};color:#fff;">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $rec['icon'] }}"/></svg>
                                                {{ $rec['label'] }}
                                            </span>
                                        </div>
                                        <div class="px-4 py-4 bg-white">
                                            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $rv->comments_for_author }}</p>
                                        </div>
                                    </div>
                                    @elseif($assignment->status === 'accepted' && !$rv)
                                    <div class="mt-3 flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-100">
                                        <svg class="w-4 h-4 text-slate-300 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                                        </svg>
                                        <p class="text-xs text-slate-400">Reviewer sedang menyelesaikan review. Komentar akan muncul setelah selesai.</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                            {{-- EVENT: Keputusan Editor --}}
                            @if(!$isLast || in_array($round->status, ['revisions_requested','accepted','declined']))
                            @php
                                $edColor = match($round->status){
                                    'revisions_requested' => '#ea580c',
                                    'accepted'            => '#16a34a',
                                    'declined'            => '#dc2626',
                                    default               => '#64748b',
                                };
                                $edBg = match($round->status){
                                    'revisions_requested' => '#fff7ed',
                                    'accepted'            => '#f0fdf4',
                                    'declined'            => '#fef2f2',
                                    default               => '#f8fafc',
                                };
                            @endphp
                            <div class="relative flex gap-3">
                                <div class="absolute -left-5 w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5"
                                     style="background:{{ $edBg }};border-color:{{ $edColor }};">
                                    <div class="w-1.5 h-1.5 rounded-full" style="background:{{ $edColor }};"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold" style="color:{{ $edColor }}">Keputusan Editor</p>
                                    @if($round->status === 'revisions_requested')
                                    <div class="mt-2 px-4 py-3 rounded-xl border text-sm" style="background:#fff7ed;border-color:#fed7aa;">
                                        <p class="font-bold text-orange-900 mb-1">Revisi Diminta</p>
                                        <p class="text-orange-800 text-xs leading-relaxed">
                                            Editor telah meninjau komentar reviewer dan meminta Anda melakukan revisi. Perbaiki naskah sesuai catatan reviewer di atas, lalu unggah kembali.
                                        </p>
                                    </div>
                                    @elseif($round->status === 'accepted')
                                    <div class="mt-2 px-4 py-3 rounded-xl border" style="background:#f0fdf4;border-color:#bbf7d0;">
                                        <p class="font-bold text-green-900 text-sm">Naskah Diterima</p>
                                        <p class="text-green-700 text-xs mt-0.5">Selamat! Naskah Anda diterima dan akan diproses ke tahap berikutnya.</p>
                                    </div>
                                    @elseif($round->status === 'declined')
                                    <div class="mt-2 px-4 py-3 rounded-xl border" style="background:#fef2f2;border-color:#fecaca;">
                                        <p class="font-bold text-red-900 text-sm">Naskah Ditolak</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- EVENT: Penulis kirim revisi (jika bukan putaran terakhir) --}}
                            @if(!$isLast)
                            <div class="relative flex gap-3">
                                <div class="absolute -left-5 w-4 h-4 rounded-full bg-indigo-100 border-2 border-indigo-300 flex items-center justify-center shrink-0 mt-0.5">
                                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-indigo-700">Penulis</p>
                                    <p class="text-sm font-semibold text-slate-800 mt-0.5">Mengunggah naskah revisi</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Revisi diterima — naskah diteruskan ke putaran review berikutnya</p>
                                </div>
                            </div>
                            @endif

                            </div>{{-- end space-y-6 --}}
                        </div>{{-- end pl-8 --}}
                        </div>
                    </div>{{-- end x-show --}}
                </div>
                @endforeach
                </div>
            </div>
            @endif

        </div>{{-- end kolom utama --}}

        {{-- ── SIDEBAR ───────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Detail Submission --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100" style="background:#f8fafc;">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Submission</p>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">ID</span>
                        <span class="text-sm font-bold text-slate-700 font-mono">#{{ $submission->id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Status</span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg"
                              style="background:{{ $s['bg'] }};color:{{ $s['color'] }};border:1px solid {{ $s['border'] }};">
                            {{ $s['label'] }}
                        </span>
                    </div>
                    @if($submission->submitted_at)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Tanggal Kirim</span>
                        <span class="text-xs font-semibold text-slate-700">{{ $submission->submitted_at->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($submission->section)
                    <div class="flex items-start justify-between gap-2">
                        <span class="text-xs text-slate-400 shrink-0">Seksi</span>
                        <span class="text-xs font-semibold text-slate-700 text-right">{{ $submission->section->title }}</span>
                    </div>
                    @endif
                    @if($submission->journal)
                    <div class="flex items-start justify-between gap-2">
                        <span class="text-xs text-slate-400 shrink-0">Jurnal</span>
                        <span class="text-xs font-semibold text-slate-700 text-right">{{ $submission->journal->name_abbrev ?? $submission->journal->name }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Bahasa</span>
                        <span class="text-xs font-semibold text-slate-700 uppercase">{{ $submission->locale }}</span>
                    </div>
                    @if($totalRounds > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Putaran Review</span>
                        <span class="text-xs font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-lg">{{ $totalRounds }}×</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Penulis / Contributors --}}
            @if($submission->contributors->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100" style="background:#f8fafc;">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tim Penulis</p>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($submission->contributors as $c)
                    @php
                        $initials = strtoupper(substr($c->first_name ?? '?', 0, 1) . substr($c->last_name ?? '', 0, 1));
                        $ciColors = ['#4f46e5','#0891b2','#059669','#d97706','#7c3aed'];
                        $ciBg = $ciColors[$loop->index % count($ciColors)];
                    @endphp
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center text-white text-xs font-black"
                             style="background:{{ $ciBg }}">{{ $initials }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $c->full_name }}</p>
                            @if($c->affiliation)
                            <p class="text-xs text-slate-400 truncate">{{ $c->affiliation }}</p>
                            @endif
                            @if($c->primary_contact)
                            <span class="inline-block text-xs text-blue-600 font-semibold mt-0.5">✉ Korespondensi</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Aksi Cepat --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100" style="background:#f8fafc;">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Aksi</p>
                </div>
                <div class="p-4 space-y-2">
                    @if($submission->status === 'revision_required')
                    <button class="w-full flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-bold text-white transition-all hover:opacity-90"
                            style="background:linear-gradient(135deg,#ea580c,#dc2626);">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Unggah Revisi
                    </button>
                    @endif
                    <a href="{{ route('dashboard') }}"
                       class="w-full flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-all">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                        Kembali ke Dashboard
                    </a>
                    <a href="{{ route('submit') }}"
                       class="w-full flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Kirim Naskah Baru
                    </a>
                </div>
            </div>

            {{-- LOA Block --}}
            @php $loa = $submission->loa ?? \App\Models\LetterOfAcceptance::where('submission_id', $submission->id)->latest()->first(); @endphp
            @if($loa)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2" style="background:#f0fdf4;">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <p class="text-xs font-bold text-green-800 uppercase tracking-widest">Letter of Acceptance</p>
                </div>
                <div class="p-4 space-y-3">
                    <div class="text-center py-2">
                        <span class="inline-block bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full border border-green-200">Naskah Diterima ✓</span>
                    </div>
                    <div class="text-xs text-slate-500 space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nomor LOA</span>
                            <span class="font-mono font-semibold text-slate-700">{{ $loa->loa_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Tanggal</span>
                            <span class="font-semibold text-slate-700">{{ $loa->acceptance_date?->format('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="space-y-2 pt-1">
                        <a href="{{ route('loa.preview', $loa) }}" target="_blank"
                           class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-white transition-colors"
                           style="background:#1e3a5f;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat LOA
                        </a>
                        <a href="{{ $loa->verifyUrl() }}" target="_blank"
                           class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Verifikasi Keaslian
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>{{-- end sidebar --}}
    </div>
</div>

</div>{{-- end min-h-screen --}}

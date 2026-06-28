<div x-data="{ assignModal: false, decisionModal: false, openReview: null }">

{{-- Header --}}
<div style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 100%);padding:2rem 1.5rem;">
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('editor.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm mb-3" style="color:#93c5fd;text-decoration:none;">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Dashboard Editor
        </a>
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <p class="text-sm font-semibold mb-1" style="color:#93c5fd;">
                    {{ $submission->journal->name_abbrev ?? $submission->journal->name }}
                    @if($submission->section) · {{ $submission->section->title }} @endif
                </p>
                <h1 class="text-xl font-black text-white leading-snug" style="max-width:640px;">
                    {{ $submission->title }}
                </h1>
            </div>
            @php
            $statusMap = [
                'submitted'=>['Submitted','#2563eb'],'queued'=>['Antrian','#0891b2'],
                'assigned'=>['Ditugaskan','#d97706'],'review'=>['Dalam Review','#7c3aed'],
                'revision_required'=>['Perlu Revisi','#dc2626'],'accepted'=>['Diterima','#059669'],
                'declined'=>['Ditolak','#94a3b8'],
            ];
            [$stLabel,$stColor] = $statusMap[$submission->status] ?? [$submission->status,'#64748b'];
            @endphp
            <span class="shrink-0 text-sm font-bold px-3 py-1.5 rounded-full"
                  style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);">
                {{ $stLabel }}
            </span>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-6 py-8">

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6 items-start">

        {{-- Main column --}}
        <div class="flex-1 min-w-0 space-y-5">

            {{-- Submission Info --}}
            <div class="rounded-2xl p-6" style="background:#fff;border:1px solid #e2e8f0;">
                <h2 class="font-bold text-sm uppercase tracking-wide mb-4" style="color:#64748b;">Informasi Naskah</h2>
                <dl class="space-y-3">
                    <div class="flex gap-3">
                        <dt class="text-sm font-semibold w-28 shrink-0" style="color:#94a3b8;">Penulis</dt>
                        <dd class="text-sm font-medium" style="color:#0f172a;">
                            {{ $submission->submitter?->first_name }} {{ $submission->submitter?->last_name }}
                            <span class="text-xs ml-1" style="color:#94a3b8;">({{ $submission->submitter?->email }})</span>
                        </dd>
                    </div>
                    @if($submission->contributors->isNotEmpty())
                    <div class="flex gap-3">
                        <dt class="text-sm font-semibold w-28 shrink-0" style="color:#94a3b8;">Kontributor</dt>
                        <dd class="text-sm" style="color:#475569;">
                            {{ $submission->contributors->map(fn($c)=>$c->first_name.' '.$c->last_name)->join(', ') }}
                        </dd>
                    </div>
                    @endif
                    <div class="flex gap-3">
                        <dt class="text-sm font-semibold w-28 shrink-0" style="color:#94a3b8;">Dikirim</dt>
                        <dd class="text-sm" style="color:#475569;">
                            {{ $submission->submitted_at?->format('d M Y, H:i') ?? '—' }}
                        </dd>
                    </div>
                    @if($submission->abstract)
                    <div>
                        <dt class="text-sm font-semibold mb-1.5" style="color:#94a3b8;">Abstrak</dt>
                        <dd class="text-sm leading-relaxed" style="color:#475569;">
                            {{ strip_tags($submission->abstract) }}
                        </dd>
                    </div>
                    @endif
                    @if($submission->keywords)
                    <div class="flex gap-3">
                        <dt class="text-sm font-semibold w-28 shrink-0 mt-0.5" style="color:#94a3b8;">Kata Kunci</dt>
                        <dd class="flex flex-wrap gap-1.5">
                            @foreach(is_array($submission->keywords) ? $submission->keywords : explode(',', $submission->keywords ?? '') as $kw)
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:#f1f5f9;color:#475569;">{{ trim($kw) }}</span>
                            @endforeach
                        </dd>
                    </div>
                    @endif
                </dl>

                {{-- Files --}}
                @if($submission->files->isNotEmpty())
                <div class="mt-4 pt-4" style="border-top:1px solid #f1f5f9;">
                    <p class="text-sm font-semibold mb-2" style="color:#94a3b8;">File Naskah</p>
                    <div class="space-y-2">
                        @foreach($submission->files as $file)
                        <a href="{{ asset('storage/' . $file->path) }}" target="_blank"
                           class="flex items-center gap-2 text-sm rounded-lg px-3 py-2 transition-colors"
                           style="background:#f8fafc;color:#2563eb;text-decoration:none;border:1px solid #e2e8f0;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ $file->original_file_name }}
                            <span class="text-xs ml-auto" style="color:#94a3b8;">{{ strtoupper($file->genre ?? 'FILE') }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Plagiarism Check --}}
            @if(session('plagiarism_done'))
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('plagiarism_done') }}
            </div>
            @endif

            <div class="rounded-2xl overflow-hidden" style="background:#fff;border:1px solid #e2e8f0;">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid #f1f5f9;">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" style="color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <h2 class="font-bold text-sm uppercase tracking-wide" style="color:#64748b;">Cek Plagiarisme</h2>
                        @if($submission->similarity_checked_at)
                        <span class="text-xs ml-2" style="color:#94a3b8;">
                            Terakhir: {{ $submission->similarity_checked_at->diffForHumans() }}
                        </span>
                        @endif
                    </div>
                    <button wire:click="runPlagiarismCheck" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold rounded-xl px-3 py-1.5 transition-opacity"
                            style="background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe;">
                        <span wire:loading.remove wire:target="runPlagiarismCheck">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </span>
                        <span wire:loading wire:target="runPlagiarismCheck">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        </span>
                        <span wire:loading.remove wire:target="runPlagiarismCheck">Cek Sekarang</span>
                        <span wire:loading wire:target="runPlagiarismCheck">Memeriksa...</span>
                    </button>
                </div>

                <div class="p-6">
                @if($plagiarismCheck)
                    {{-- Score meter --}}
                    @php
                        $score = $plagiarismCheck->overall_score;
                        [$meterColor, $label, $labelStyle] = match(true) {
                            $score <= 15 => ['#22c55e', 'Aman',        'background:#f0fdf4;color:#166534;border-color:#bbf7d0'],
                            $score <= 30 => ['#f59e0b', 'Sedang',      'background:#fffbeb;color:#92400e;border-color:#fde68a'],
                            $score <= 50 => ['#f97316', 'Perlu Revisi','background:#fff7ed;color:#9a3412;border-color:#fed7aa'],
                            default      => ['#ef4444', 'Plagiat Tinggi','background:#fef2f2;color:#991b1b;border-color:#fecaca'],
                        };
                    @endphp
                    <div class="flex items-center gap-6 mb-5">
                        <div class="relative w-24 h-24 shrink-0">
                            <svg class="w-24 h-24 -rotate-90" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f1f5f9" stroke-width="3"/>
                                <circle cx="18" cy="18" r="15.9" fill="none"
                                        stroke="{{ $meterColor }}" stroke-width="3"
                                        stroke-dasharray="{{ min($score,100) }} {{ 100-min($score,100) }}"
                                        stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-xl font-black" style="color:{{ $meterColor }};">{{ $score }}%</span>
                            </div>
                        </div>
                        <div>
                            <span class="inline-block text-xs font-bold px-3 py-1 rounded-full border mb-2" style="{{ $labelStyle }}">{{ $label }}</span>
                            <p class="text-sm" style="color:#475569;">
                                Dibandingkan <strong>{{ $plagiarismCheck->sources_checked }}</strong> naskah lain dalam jurnal ini.
                                Teks sumber: <strong>{{ $plagiarismCheck->source_length }}</strong> kata.
                            </p>
                            <p class="text-xs mt-1" style="color:#94a3b8;">
                                Diperiksa {{ $plagiarismCheck->checked_at->format('d M Y H:i') }} oleh {{ $plagiarismCheck->checker?->first_name }}
                            </p>
                        </div>
                    </div>

                    {{-- Matched sources --}}
                    @if(count($plagiarismCheck->results) > 0)
                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:#94a3b8;">Sumber Kemiripan</p>
                        @foreach($plagiarismCheck->results as $match)
                        @php
                            $s = $match['score'];
                            $c = $s <= 15 ? '#22c55e' : ($s <= 30 ? '#f59e0b' : ($s <= 50 ? '#f97316' : '#ef4444'));
                        @endphp
                        <div class="flex items-start gap-3 px-4 py-3 rounded-xl" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center text-xs font-black text-white"
                                 style="background:{{ $c }};">{{ $s }}%</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate" style="color:#0f172a;">{{ $match['title'] }}</p>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <a href="{{ route('editor.submissions.review', $match['submission_id']) }}"
                                       target="_blank"
                                       class="text-xs underline" style="color:#7c3aed;">Lihat naskah →</a>
                                    <span class="text-xs px-2 py-0.5 rounded-full"
                                          style="background:#f1f5f9;color:#64748b;">{{ ucfirst($match['status']) }}</span>
                                </div>
                                @if(!empty($match['matched']))
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach($match['matched'] as $phrase)
                                    <span class="text-xs px-2 py-0.5 rounded" style="background:#fef3c7;color:#92400e;">"{{ $phrase }}"</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <svg class="w-8 h-8 mx-auto mb-2" style="color:#22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-semibold" style="color:#166534;">Tidak ditemukan kemiripan signifikan.</p>
                    </div>
                    @endif

                @else
                    <div class="text-center py-8" style="color:#94a3b8;">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <p class="text-sm">Belum pernah dicek.</p>
                        <p class="text-xs mt-1">Klik <strong>Cek Sekarang</strong> untuk menjalankan analisis kemiripan.</p>
                    </div>
                @endif
                </div>
            </div>

            {{-- Reviewer Guidelines for Section --}}
            @if($submission->section?->reviewer_guidelines)
            <div class="rounded-2xl p-5" style="background:#fffbeb;border:1px solid #fde68a;">
                <div class="flex items-start gap-3">
                    <svg style="width:1.25rem;height:1.25rem;color:#d97706;flex-shrink:0;margin-top:2px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-yellow-800 mb-1">Panduan Reviewer — Seksi: {{ $submission->section->title }}</p>
                        <p class="text-sm text-yellow-700 leading-relaxed">{!! nl2br(e($submission->section->reviewer_guidelines)) !!}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Review Assignments --}}
            <div class="rounded-2xl p-6" style="background:#fff;border:1px solid #e2e8f0;">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-bold text-sm uppercase tracking-wide" style="color:#64748b;">Penugasan Reviewer</h2>
                    <button @click="assignModal = true"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold rounded-xl px-3 py-1.5"
                            style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tugaskan Reviewer
                    </button>
                </div>

                @if($assignments->isEmpty())
                <p class="text-sm text-center py-8" style="color:#94a3b8;">Belum ada reviewer ditugaskan.</p>
                @else
                <div class="space-y-3">
                    @foreach($assignments as $assignment)
                    @php
                    $aStatus = [
                        'awaiting_response' => ['Menunggu Konfirmasi','#d97706','#fffbeb'],
                        'accepted'          => ['Sedang Review','#2563eb','#eff6ff'],
                        'declined'          => ['Ditolak','#dc2626','#fff1f2'],
                        'completed'         => ['Selesai','#059669','#f0fdf4'],
                        'cancelled'         => ['Dibatalkan','#94a3b8','#f8fafc'],
                    ][$assignment->status] ?? [$assignment->status,'#64748b','#f8fafc'];
                    @endphp
                    <div class="rounded-xl p-4" style="background:#f8fafc;border:1px solid #e2e8f0;">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-sm" style="color:#0f172a;">
                                        {{ $assignment->reviewer->first_name }} {{ $assignment->reviewer->last_name }}
                                    </span>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                          style="background:{{ $aStatus[2] }};color:{{ $aStatus[1] }};">
                                        {{ $aStatus[0] }}
                                    </span>
                                </div>
                                <p class="text-xs" style="color:#94a3b8;">
                                    {{ $assignment->reviewer->email }}
                                    @if($assignment->date_due)
                                    · Deadline: {{ $assignment->date_due->format('d M Y') }}
                                    @endif
                                    @if($assignment->review_method === 'double_blind')
                                    · Double Blind
                                    @elseif($assignment->review_method === 'single_blind')
                                    · Single Blind
                                    @else
                                    · Open Review
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($assignment->review)
                                <button @click="openReview = openReview === {{ $assignment->id }} ? null : {{ $assignment->id }}"
                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg"
                                        style="background:#f0fdf4;color:#059669;border:1px solid #bbf7d0;">
                                    Lihat Review
                                </button>
                                @endif
                                @if(in_array($assignment->status, ['awaiting_response','accepted']))
                                <button wire:click="cancelAssignment({{ $assignment->id }})"
                                        wire:confirm="Batalkan penugasan reviewer ini?"
                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg"
                                        style="background:#fff1f2;color:#dc2626;border:1px solid #fecaca;">
                                    Batalkan
                                </button>
                                @endif
                            </div>
                        </div>

                        {{-- Review detail (expandable) --}}
                        @if($assignment->review)
                        <div x-show="openReview === {{ $assignment->id }}" x-cloak class="mt-4 pt-4" style="border-top:1px solid #e2e8f0;">
                            @php
                            $recMap = [
                                'accept'             => ['Terima','#059669','#f0fdf4'],
                                'pending_revisions'  => ['Revisi Minor','#d97706','#fffbeb'],
                                'resubmit_here'      => ['Revisi Mayor','#7c3aed','#faf5ff'],
                                'resubmit_elsewhere' => ['Submit ke Jurnal Lain','#dc2626','#fff1f2'],
                                'decline'            => ['Tolak','#dc2626','#fff1f2'],
                                'see_comments'       => ['Lihat Komentar','#64748b','#f8fafc'],
                            ][$assignment->review->recommendation] ?? [$assignment->review->recommendation,'#64748b','#f8fafc'];
                            @endphp
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs font-semibold" style="color:#64748b;">Rekomendasi:</span>
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full"
                                      style="background:{{ $recMap[2] }};color:{{ $recMap[1] }};">
                                    {{ $recMap[0] }}
                                </span>
                            </div>
                            @if($assignment->review->comments_for_author)
                            <div class="mb-3">
                                <p class="text-xs font-semibold mb-1" style="color:#94a3b8;">Komentar untuk Penulis</p>
                                <p class="text-sm leading-relaxed" style="color:#475569;">{{ $assignment->review->comments_for_author }}</p>
                            </div>
                            @endif
                            @if($assignment->review->comments_for_editors)
                            <div class="rounded-lg p-3" style="background:#fffbeb;border:1px solid #fef3c7;">
                                <p class="text-xs font-semibold mb-1" style="color:#92400e;">Komentar untuk Editor (Konfidensial)</p>
                                <p class="text-sm leading-relaxed" style="color:#78350f;">{{ $assignment->review->comments_for_editors }}</p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="w-full lg:w-80 shrink-0 space-y-4">

            {{-- Decision Card --}}
            <div class="rounded-2xl p-5" style="background:#fff;border:1px solid #e2e8f0;">
                <h3 class="font-bold text-sm uppercase tracking-wide mb-4" style="color:#64748b;">Keputusan Editorial</h3>

                @if(in_array($submission->status, ['accepted','declined','revision_required']))
                @php $dMap = ['accepted'=>['Diterima','#059669','#f0fdf4'],'declined'=>['Ditolak','#dc2626','#fff1f2'],'revision_required'=>['Perlu Revisi','#d97706','#fffbeb']]; $dm = $dMap[$submission->status]; @endphp
                <div class="text-center py-4 rounded-xl mb-3" style="background:{{ $dm[2] }};border:1px solid {{ $dm[1] }}22;">
                    <p class="font-bold" style="color:{{ $dm[1] }};">{{ $dm[0] }}</p>
                    <p class="text-xs mt-0.5" style="color:{{ $dm[1] }};">Keputusan sudah dibuat</p>
                </div>
                @endif

                <button @click="decisionModal = true"
                        class="w-full py-2.5 rounded-xl text-sm font-bold text-white transition-opacity hover:opacity-90"
                        style="background:linear-gradient(135deg,#1e40af,#7c3aed);">
                    {{ in_array($submission->status, ['accepted','declined','revision_required']) ? 'Ubah Keputusan' : 'Buat Keputusan' }}
                </button>
            </div>

            {{-- Timeline --}}
            <div class="rounded-2xl p-5" style="background:#fff;border:1px solid #e2e8f0;">
                <h3 class="font-bold text-sm uppercase tracking-wide mb-3" style="color:#64748b;">Ringkasan Review</h3>
                @php
                $total     = $assignments->count();
                $completed = $assignments->where('status','completed')->count();
                $accepted  = $assignments->where('status','accepted')->count();
                $waiting   = $assignments->where('status','awaiting_response')->count();
                @endphp
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span style="color:#64748b;">Total Reviewer</span>
                        <span class="font-bold" style="color:#0f172a;">{{ $total }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color:#64748b;">Review Selesai</span>
                        <span class="font-bold" style="color:#059669;">{{ $completed }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color:#64748b;">Sedang Review</span>
                        <span class="font-bold" style="color:#2563eb;">{{ $accepted }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color:#64748b;">Menunggu Konfirmasi</span>
                        <span class="font-bold" style="color:#d97706;">{{ $waiting }}</span>
                    </div>
                </div>
            </div>

            {{-- ── Aktivitas Editorial ──────────────────────────────────────── --}}
            <div class="rounded-2xl p-5" style="background:#fff;border:1px solid #e2e8f0;">
                <h3 class="font-bold text-sm uppercase tracking-wide mb-4" style="color:#64748b;">Aktivitas Editorial</h3>

                @if(empty($activityTimeline))
                <p class="text-xs text-center py-4" style="color:#94a3b8;">Belum ada aktivitas.</p>
                @else
                <div style="position:relative;">
                    {{-- Vertical line --}}
                    <div style="position:absolute;left:.6875rem;top:.5rem;bottom:.5rem;width:2px;background:#f1f5f9;"></div>

                    <div style="display:flex;flex-direction:column;gap:0;">
                        @foreach($activityTimeline as $i => $ev)
                        <div style="display:flex;gap:.875rem;position:relative;padding-bottom:{{ !$loop->last ? '1.125rem' : '0' }};">
                            {{-- Dot --}}
                            <div style="width:1.375rem;height:1.375rem;border-radius:50%;background:{{ $ev['color'] }}18;border:2px solid {{ $ev['color'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:.125rem;position:relative;z-index:1;">
                                <svg style="width:.625rem;height:.625rem;color:{{ $ev['color'] }};" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $ev['icon'] }}"/>
                                </svg>
                            </div>
                            {{-- Content --}}
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:.8125rem;font-weight:700;color:#0f172a;margin:0 0 .125rem;line-height:1.35;">{{ $ev['label'] }}</p>
                                @if($ev['note'])
                                <p style="font-size:.72rem;color:#64748b;margin:0 0 .125rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ev['note'] }}</p>
                                @endif
                                <p style="font-size:.7rem;color:#94a3b8;margin:0;">{{ $ev['at']->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

{{-- MODAL: Assign Reviewer --}}
<div x-show="assignModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(0,0,0,.5);"
     @keydown.escape.window="assignModal = false">
    <div class="w-full max-w-lg rounded-2xl p-6" style="background:#fff;box-shadow:0 25px 50px rgba(0,0,0,.25);"
         @click.outside="assignModal = false">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg" style="color:#0f172a;">Tugaskan Reviewer</h3>
            <button @click="assignModal = false" style="color:#94a3b8;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form wire:submit="assignReviewer" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">Pilih Reviewer</label>
                <select wire:model="reviewerId" class="w-full rounded-xl border px-3 py-2.5 text-sm" style="border-color:#d1d5db;color:#0f172a;">
                    <option value="">-- Pilih Reviewer --</option>
                    @foreach($availableReviewers as $reviewer)
                    <option value="{{ $reviewer->id }}">
                        {{ $reviewer->last_name }}, {{ $reviewer->first_name }}
                        @if($reviewer->affiliation) ({{ $reviewer->affiliation }}) @endif
                    </option>
                    @endforeach
                </select>
                @error('reviewerId') <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">Metode Review</label>
                <select wire:model="reviewMethod" class="w-full rounded-xl border px-3 py-2.5 text-sm" style="border-color:#d1d5db;color:#0f172a;">
                    <option value="double_blind">Double Blind</option>
                    <option value="single_blind">Single Blind</option>
                    <option value="open">Open Review</option>
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">Deadline Respon</label>
                    <input type="date" wire:model="dateResponseDue"
                           class="w-full rounded-xl border px-3 py-2.5 text-sm" style="border-color:#d1d5db;">
                    @error('dateResponseDue') <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">Deadline Review</label>
                    <input type="date" wire:model="dateDue"
                           class="w-full rounded-xl border px-3 py-2.5 text-sm" style="border-color:#d1d5db;">
                    @error('dateDue') <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="assignModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold"
                        style="background:#f1f5f9;color:#64748b;">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white"
                        style="background:#2563eb;"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>Tugaskan & Kirim Undangan</span>
                    <span wire:loading>Mengirim...</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Editorial Decision --}}
<div x-show="decisionModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(0,0,0,.5);"
     @keydown.escape.window="decisionModal = false">
    <div class="w-full max-w-lg rounded-2xl p-6" style="background:#fff;box-shadow:0 25px 50px rgba(0,0,0,.25);"
         @click.outside="decisionModal = false">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg" style="color:#0f172a;">Keputusan Editorial</h3>
            <button @click="decisionModal = false" style="color:#94a3b8;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form wire:submit="makeDecision" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-2" style="color:#374151;">Keputusan</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach([
                        ['value'=>'accepted',          'label'=>'Terima',        'color'=>'#059669','bg'=>'#f0fdf4','border'=>'#86efac'],
                        ['value'=>'revision_required', 'label'=>'Revisi',        'color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fcd34d'],
                        ['value'=>'declined',          'label'=>'Tolak',         'color'=>'#dc2626','bg'=>'#fff1f2','border'=>'#fca5a5'],
                    ] as $opt)
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="decision" value="{{ $opt['value'] }}" class="sr-only">
                        <div class="text-center py-2.5 rounded-xl text-sm font-bold border-2 transition-all"
                             style="{{ $decision === $opt['value'] ? 'background:'.$opt['bg'].';border-color:'.$opt['color'].';color:'.$opt['color'] : 'background:#f8fafc;border-color:#e2e8f0;color:#94a3b8' }}">
                            {{ $opt['label'] }}
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('decision') <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">
                    Pesan untuk Penulis <span style="color:#dc2626;">*</span>
                </label>
                <textarea wire:model="decisionMessage" rows="5"
                          placeholder="Tulis pesan lengkap untuk penulis..."
                          class="w-full rounded-xl border px-3 py-2.5 text-sm resize-none"
                          style="border-color:#d1d5db;color:#0f172a;"></textarea>
                @error('decisionMessage') <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p> @enderror
            </div>

            <div class="rounded-xl p-3 text-xs" style="background:#fffbeb;border:1px solid #fef3c7;color:#92400e;">
                ⚠️ Email keputusan editorial akan dikirim otomatis ke penulis setelah Anda mengklik Kirim.
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" @click="decisionModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold"
                        style="background:#f1f5f9;color:#64748b;">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white"
                        style="background:linear-gradient(135deg,#1e40af,#7c3aed);"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>Kirim Keputusan</span>
                    <span wire:loading>Mengirim...</span>
                </button>
            </div>
        </form>
    </div>
</div>

</div>

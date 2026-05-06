<div>
{{-- Header --}}
<div style="background:linear-gradient(135deg,#065f46 0%,#059669 100%);padding:2rem 1.5rem;">
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('reviewer.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm mb-3" style="color:#6ee7b7;text-decoration:none;">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Dashboard Reviewer
        </a>
        <p class="text-sm font-semibold mb-1" style="color:#6ee7b7;">Form Review</p>
        <h1 class="text-xl font-black text-white leading-snug" style="max-width:600px;">
            {{ Str::limit($assignment->submission->title, 80) }}
        </h1>
    </div>
</div>

<div class="max-w-4xl mx-auto px-6 py-8">

    @if($assignment->status === 'completed')
    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        Review sudah dikirim. Anda masih dapat mengedit review jika diperlukan.
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">

        {{-- Review Form --}}
        <div>
            <form wire:submit="submitReview" class="space-y-5">

                {{-- Recommendation --}}
                <div class="rounded-2xl p-6" style="background:#fff;border:1px solid #e2e8f0;">
                    <h2 class="font-bold text-sm uppercase tracking-wide mb-4" style="color:#64748b;">Rekomendasi</h2>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                        @foreach([
                            ['value'=>'accept',             'label'=>'Terima',                'desc'=>'Naskah layak diterima',                        'color'=>'#059669','bg'=>'#f0fdf4','border'=>'#86efac'],
                            ['value'=>'pending_revisions',  'label'=>'Revisi Minor',          'desc'=>'Perlu perbaikan kecil sebelum diterima',       'color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fcd34d'],
                            ['value'=>'resubmit_here',      'label'=>'Revisi Mayor',          'desc'=>'Perlu perbaikan besar, submit ulang ke jurnal ini','color'=>'#7c3aed','bg'=>'#faf5ff','border'=>'#c4b5fd'],
                            ['value'=>'resubmit_elsewhere', 'label'=>'Submit Jurnal Lain',    'desc'=>'Topik lebih sesuai untuk jurnal lain',          'color'=>'#0891b2','bg'=>'#ecfeff','border'=>'#a5f3fc'],
                            ['value'=>'decline',            'label'=>'Tolak',                 'desc'=>'Naskah tidak layak untuk publikasi',            'color'=>'#dc2626','bg'=>'#fff1f2','border'=>'#fca5a5'],
                            ['value'=>'see_comments',       'label'=>'Lihat Komentar',        'desc'=>'Lihat komentar untuk detail',                  'color'=>'#64748b','bg'=>'#f8fafc','border'=>'#e2e8f0'],
                        ] as $opt)
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="recommendation" value="{{ $opt['value'] }}" class="sr-only">
                            <div class="p-3 rounded-xl border-2 transition-all"
                                 style="{{ $recommendation === $opt['value'] ? 'background:'.$opt['bg'].';border-color:'.$opt['color'] : 'background:#f8fafc;border-color:#e2e8f0' }}">
                                <p class="text-sm font-bold mb-0.5" style="color:{{ $recommendation === $opt['value'] ? $opt['color'] : '#374151' }};">{{ $opt['label'] }}</p>
                                <p class="text-xs leading-snug" style="color:#64748b;">{{ $opt['desc'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('recommendation') <p class="text-xs mt-2" style="color:#dc2626;">{{ $message }}</p> @enderror
                </div>

                {{-- Comments for Author --}}
                <div class="rounded-2xl p-6" style="background:#fff;border:1px solid #e2e8f0;">
                    <h2 class="font-bold text-sm uppercase tracking-wide mb-1.5" style="color:#64748b;">Komentar untuk Penulis</h2>
                    <p class="text-xs mb-4" style="color:#94a3b8;">Komentar ini akan dibagikan ke penulis. Tulis secara konstruktif dan profesional.</p>
                    <textarea wire:model="commentsForAuthor" rows="8"
                              placeholder="Tulis komentar, saran, dan catatan untuk penulis..."
                              class="w-full rounded-xl border px-4 py-3 text-sm resize-none"
                              style="border-color:#d1d5db;color:#0f172a;line-height:1.7;"></textarea>
                    @error('commentsForAuthor') <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p> @enderror
                </div>

                {{-- Comments for Editor --}}
                <div class="rounded-2xl p-6" style="background:#fffbeb;border:1px solid #fef3c7;">
                    <h2 class="font-bold text-sm uppercase tracking-wide mb-1.5" style="color:#92400e;">Komentar untuk Editor (Konfidensial)</h2>
                    <p class="text-xs mb-4" style="color:#a16207;">Komentar ini TIDAK akan dibagikan ke penulis — hanya untuk editor.</p>
                    <textarea wire:model="commentsForEditor" rows="4"
                              placeholder="Catatan konfidensial untuk editor (opsional)..."
                              class="w-full rounded-xl border px-4 py-3 text-sm resize-none"
                              style="border-color:#fcd34d;color:#0f172a;background:#fff;line-height:1.7;"></textarea>
                    @error('commentsForEditor') <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p> @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3.5 rounded-2xl text-base font-bold text-white transition-opacity hover:opacity-90"
                        style="background:linear-gradient(135deg,#065f46,#059669);"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        {{ $assignment->status === 'completed' ? 'Perbarui Review' : 'Kirim Review' }}
                    </span>
                    <span wire:loading>Mengirim Review...</span>
                </button>
            </form>
        </div>

        {{-- Sidebar: Submission Info --}}
        <div class="space-y-4">

            <div class="rounded-2xl p-5" style="background:#fff;border:1px solid #e2e8f0;">
                <h3 class="font-bold text-sm uppercase tracking-wide mb-3" style="color:#64748b;">Info Naskah</h3>
                <dl class="space-y-2.5">
                    @if($assignment->review_method === 'double_blind')
                    <div class="rounded-lg px-3 py-2 text-xs font-medium" style="background:#eff6ff;color:#1e40af;">
                        🔒 Double Blind — identitas penulis disembunyikan
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-semibold" style="color:#94a3b8;">Jurnal</dt>
                        <dd class="text-sm font-medium" style="color:#0f172a;">{{ $assignment->submission->journal->name }}</dd>
                    </div>
                    @if($assignment->submission->section)
                    <div>
                        <dt class="text-xs font-semibold" style="color:#94a3b8;">Seksi</dt>
                        <dd class="text-sm" style="color:#475569;">{{ $assignment->submission->section->title }}</dd>
                    </div>
                    @endif
                    @if($assignment->date_due)
                    <div>
                        <dt class="text-xs font-semibold" style="color:#94a3b8;">Deadline Review</dt>
                        <dd class="text-sm font-semibold {{ $assignment->date_due->isPast() ? '' : '' }}"
                            style="color:{{ $assignment->date_due->isPast() ? '#dc2626' : '#0f172a' }};">
                            {{ $assignment->date_due->format('d M Y') }}
                            @if($assignment->date_due->isPast()) <span class="text-xs">(Lewat deadline)</span> @endif
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Abstract --}}
            @if($assignment->submission->abstract && $assignment->review_method !== 'double_blind')
            <div class="rounded-2xl p-5" style="background:#fff;border:1px solid #e2e8f0;">
                <h3 class="font-bold text-sm uppercase tracking-wide mb-2" style="color:#64748b;">Abstrak</h3>
                <p class="text-sm leading-relaxed" style="color:#475569;">
                    {{ Str::limit(strip_tags($assignment->submission->abstract), 300) }}
                </p>
            </div>
            @endif

            {{-- Files --}}
            @if($assignment->submission->files->isNotEmpty())
            <div class="rounded-2xl p-5" style="background:#fff;border:1px solid #e2e8f0;">
                <h3 class="font-bold text-sm uppercase tracking-wide mb-3" style="color:#64748b;">File Naskah</h3>
                <div class="space-y-2">
                    @foreach($assignment->submission->files as $file)
                    <a href="{{ asset('storage/' . $file->path) }}" target="_blank"
                       class="flex items-center gap-2 text-sm rounded-lg px-3 py-2"
                       style="background:#f8fafc;color:#059669;text-decoration:none;border:1px solid #e2e8f0;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        {{ $file->original_file_name }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
</div>

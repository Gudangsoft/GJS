<div style="background:#f1f5f9;min-height:100vh;">

{{-- ══ HERO ═══════════════════════════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#022c22 0%,#064e3b 55%,#059669 100%);position:relative;overflow:hidden;">
    <div style="position:absolute;top:-4rem;right:-4rem;width:18rem;height:18rem;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>

    <div style="padding:2.25rem 1.5rem 1.75rem;">
        <p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#93c5fd;margin-bottom:.375rem;">Panel Reviewer</p>
        <h1 style="font-size:1.625rem;font-weight:800;color:#fff;line-height:1.2;margin:0 0 .375rem;">Dokumen Saya</h1>
        <p style="font-size:.875rem;color:#94a3b8;margin:0;">Surat tugas dan sertifikat review Anda</p>

        {{-- Quick stats --}}
        <div style="display:flex;gap:1rem;margin-top:1.25rem;flex-wrap:wrap;">
            <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:.75rem;padding:.625rem 1rem;display:flex;align-items:center;gap:.5rem;">
                <svg style="width:1rem;height:1rem;color:#6ee7b7;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span style="font-size:.8125rem;font-weight:700;color:#fff;">{{ $suratTugas->count() }} Surat Tugas</span>
            </div>
            <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:.75rem;padding:.625rem 1rem;display:flex;align-items:center;gap:.5rem;">
                <svg style="width:1rem;height:1rem;color:#fbbf24;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                <span style="font-size:.8125rem;font-weight:700;color:#fff;">{{ $sertifikat->count() }} Sertifikat</span>
            </div>
        </div>
    </div>
</div>

<div style="padding:1.5rem;">

    {{-- ── SECTION: SURAT TUGAS ─────────────────────────────────────────────── --}}
    <div style="margin-bottom:2rem;">
        <div style="display:flex;align-items:center;gap:.625rem;margin-bottom:1rem;">
            <div style="width:2rem;height:2rem;border-radius:.5rem;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:1.125rem;height:1.125rem;color:#3b82f6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0;">Surat Tugas</h2>
                <p style="font-size:.8125rem;color:#64748b;margin:0;">Untuk semua penugasan yang diterima dan selesai</p>
            </div>
        </div>

        @if($suratTugas->isEmpty())
        <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;padding:2.5rem;text-align:center;">
            <svg style="width:2.5rem;height:2.5rem;color:#d1d5db;margin:0 auto .75rem;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p style="font-size:.9375rem;font-weight:600;color:#374151;margin:0 0 .25rem;">Belum ada surat tugas</p>
            <p style="font-size:.8125rem;color:#9ca3af;margin:0;">Surat tugas akan tersedia setelah Anda menerima undangan review</p>
        </div>
        @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem;">
            @foreach($suratTugas as $a)
            @php
                $journal = $a->submission?->journal;
                $title   = $a->submission?->title ?? 'Tanpa Judul';
                $isCompleted = $a->status === 'completed';
                $statusColor = $isCompleted ? '#059669' : '#3b82f6';
                $statusBg    = $isCompleted ? '#f0fdf4' : '#eff6ff';
                $statusLabel = $isCompleted ? 'Selesai' : 'Berlangsung';
            @endphp
            <div style="background:#fff;border-radius:1rem;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #e2e8f0;padding:1.25rem;display:flex;flex-direction:column;gap:.875rem;">
                {{-- Header --}}
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;">
                    <div style="flex:1;min-width:0;">
                        @if($journal)
                        <p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin:0 0 .25rem;">
                            {{ Str::limit($journal->name, 40) }}
                        </p>
                        @endif
                        <p style="font-size:.9375rem;font-weight:700;color:#0f172a;margin:0;line-height:1.4;">
                            {{ Str::limit($title, 70) }}
                        </p>
                    </div>
                    <span style="flex-shrink:0;background:{{ $statusBg }};color:{{ $statusColor }};border:1px solid {{ $statusColor }}33;border-radius:.375rem;padding:.2rem .6rem;font-size:.75rem;font-weight:700;">
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Meta --}}
                <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                    @if($a->date_assigned)
                    <div style="display:flex;align-items:center;gap:.375rem;">
                        <svg style="width:.875rem;height:.875rem;color:#9ca3af;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span style="font-size:.8125rem;color:#64748b;">{{ $a->date_assigned->translatedFormat('d M Y') }}</span>
                    </div>
                    @endif
                    @if($a->round)
                    <div style="display:flex;align-items:center;gap:.375rem;">
                        <svg style="width:.875rem;height:.875rem;color:#9ca3af;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span style="font-size:.8125rem;color:#64748b;">Putaran {{ $a->round }}</span>
                    </div>
                    @endif
                </div>

                {{-- Action --}}
                <a href="{{ route('reviewer.surat-tugas', $a->id) }}" target="_blank"
                   style="display:inline-flex;align-items:center;justify-content:center;gap:.5rem;background:#f1f5f9;color:#374151;border:1px solid #e2e8f0;border-radius:.625rem;padding:.5rem 1rem;font-size:.8125rem;font-weight:600;text-decoration:none;transition:all .15s;"
                   onmouseover="this.style.background='#e2e8f0';this.style.borderColor='#cbd5e1'"
                   onmouseout="this.style.background='#f1f5f9';this.style.borderColor='#e2e8f0'">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Unduh Surat Tugas
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── SECTION: SERTIFIKAT ─────────────────────────────────────────────── --}}
    <div>
        <div style="display:flex;align-items:center;gap:.625rem;margin-bottom:1rem;">
            <div style="width:2rem;height:2rem;border-radius:.5rem;background:#fefce8;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:1.125rem;height:1.125rem;color:#ca8a04;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div>
                <h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0;">Sertifikat</h2>
                <p style="font-size:.8125rem;color:#64748b;margin:0;">Tersedia untuk review yang telah selesai</p>
            </div>
        </div>

        @if($sertifikat->isEmpty())
        <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;padding:2.5rem;text-align:center;">
            <svg style="width:2.5rem;height:2.5rem;color:#d1d5db;margin:0 auto .75rem;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
            <p style="font-size:.9375rem;font-weight:600;color:#374151;margin:0 0 .25rem;">Belum ada sertifikat</p>
            <p style="font-size:.8125rem;color:#9ca3af;margin:0;">Sertifikat akan tersedia setelah proses review selesai</p>
        </div>
        @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem;">
            @foreach($sertifikat as $a)
            @php
                $journal = $a->submission?->journal;
                $title   = $a->submission?->title ?? 'Tanpa Judul';
            @endphp
            <div style="background:#fff;border-radius:1rem;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #e2e8f0;padding:1.25rem;display:flex;flex-direction:column;gap:.875rem;">
                {{-- Ribbon --}}
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;">
                    <div style="flex:1;min-width:0;">
                        @if($journal)
                        <p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin:0 0 .25rem;">
                            {{ Str::limit($journal->name, 40) }}
                        </p>
                        @endif
                        <p style="font-size:.9375rem;font-weight:700;color:#0f172a;margin:0;line-height:1.4;">
                            {{ Str::limit($title, 70) }}
                        </p>
                    </div>
                    <span style="flex-shrink:0;background:#fef3c7;color:#92400e;border:1px solid #fde68a;border-radius:.375rem;padding:.2rem .6rem;font-size:.75rem;font-weight:700;">
                        Selesai
                    </span>
                </div>

                {{-- Completion date --}}
                @if($a->date_completed)
                <div style="display:flex;align-items:center;gap:.375rem;">
                    <svg style="width:.875rem;height:.875rem;color:#9ca3af;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span style="font-size:.8125rem;color:#64748b;">Selesai: {{ $a->date_completed->translatedFormat('d M Y') }}</span>
                </div>
                @endif

                {{-- Actions --}}
                <div style="display:flex;gap:.625rem;">
                    <a href="{{ route('reviewer.sertifikat', $a->id) }}" target="_blank"
                       style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:.5rem;background:#fef3c7;color:#92400e;border:1px solid #fde68a;border-radius:.625rem;padding:.5rem .75rem;font-size:.8125rem;font-weight:600;text-decoration:none;transition:all .15s;"
                       onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Unduh Sertifikat
                    </a>
                    <a href="{{ route('reviewer.surat-tugas', $a->id) }}" target="_blank"
                       style="display:inline-flex;align-items:center;justify-content:center;gap:.5rem;background:#f1f5f9;color:#374151;border:1px solid #e2e8f0;border-radius:.625rem;padding:.5rem .75rem;font-size:.8125rem;font-weight:600;text-decoration:none;transition:all .15s;"
                       onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        Surat Tugas
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
</div>

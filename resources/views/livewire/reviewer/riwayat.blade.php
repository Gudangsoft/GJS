<div style="background:#f1f5f9;min-height:100vh;">

{{-- ══ HERO ═══════════════════════════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#022c22 0%,#064e3b 55%,#059669 100%);position:relative;overflow:hidden;">
    <div style="position:absolute;top:-4rem;right:-4rem;width:18rem;height:18rem;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>

    <div style="padding:2.25rem 1.5rem 1.75rem;">
        <p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#93c5fd;margin-bottom:.375rem;">Panel Reviewer</p>
        <h1 style="font-size:1.625rem;font-weight:800;color:#fff;line-height:1.2;margin:0 0 .375rem;">Riwayat Review</h1>
        <p style="font-size:.875rem;color:#94a3b8;margin:0;">Semua penugasan review Anda dari awal hingga sekarang</p>

        {{-- Summary stats --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:.75rem;margin-top:1.5rem;">
            @foreach([
                ['n' => $stats['invited'],   'label' => 'Total Undangan',  'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',                     'color' => '#38bdf8'],
                ['n' => $stats['accepted'],  'label' => 'Diterima',         'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',   'color' => '#a78bfa'],
                ['n' => $stats['completed'], 'label' => 'Selesai',          'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                               'color' => '#34d399'],
                ['n' => $stats['declined'],  'label' => 'Ditolak',          'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',                                                       'color' => '#f87171'],
                ['n' => $stats['avg_days'] !== null ? $stats['avg_days'].' hr' : '—', 'label' => 'Rata-Rata', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                              'color' => '#fb923c'],
            ] as $s)
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:.875rem;padding:.875rem 1rem;">
                <svg style="width:1rem;height:1rem;color:{{ $s['color'] }};margin-bottom:.375rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/>
                </svg>
                <div style="font-size:1.375rem;font-weight:800;color:#fff;line-height:1;">{{ $s['n'] }}</div>
                <div style="font-size:.75rem;font-weight:600;color:#94a3b8;margin-top:.2rem;">{{ $s['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div style="padding:1.5rem;">

    {{-- ── FILTER BAR ──────────────────────────────────────────────────────── --}}
    <div style="background:#fff;border-radius:1rem;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #e2e8f0;padding:1rem 1.25rem;margin-bottom:1.25rem;">
        <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;">
            {{-- Status filter --}}
            <div style="flex:1;min-width:150px;">
                <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.04em;">Status</label>
                <select wire:model.live="filterStatus"
                        style="width:100%;border:1px solid #d1d5db;border-radius:.5rem;padding:.4375rem .75rem;font-size:.875rem;color:#374151;background:#fff;outline:none;cursor:pointer;">
                    <option value="all">Semua Status</option>
                    <option value="awaiting_response">Menunggu Respons</option>
                    <option value="accepted">Sedang Berjalan</option>
                    <option value="completed">Selesai</option>
                    <option value="declined">Ditolak</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>

            {{-- Journal filter --}}
            @if($journals->isNotEmpty())
            <div style="flex:1;min-width:180px;">
                <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.04em;">Jurnal</label>
                <select wire:model.live="filterJournal"
                        style="width:100%;border:1px solid #d1d5db;border-radius:.5rem;padding:.4375rem .75rem;font-size:.875rem;color:#374151;background:#fff;outline:none;cursor:pointer;">
                    <option value="">Semua Jurnal</option>
                    @foreach($journals as $j)
                    <option value="{{ $j->id }}">{{ Str::limit($j->name, 40) }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Year filter --}}
            @if($years->isNotEmpty())
            <div style="min-width:120px;">
                <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.04em;">Tahun</label>
                <select wire:model.live="filterYear"
                        style="width:100%;border:1px solid #d1d5db;border-radius:.5rem;padding:.4375rem .75rem;font-size:.875rem;color:#374151;background:#fff;outline:none;cursor:pointer;">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $yr)
                    <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Reset --}}
            @if($filterStatus !== 'all' || $filterJournal !== '' || $filterYear !== '')
            <div>
                <button wire:click="resetFilters"
                        style="background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;border-radius:.5rem;padding:.4375rem .875rem;font-size:.8125rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:.375rem;transition:all .15s;"
                        onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset Filter
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- ── TABLE ────────────────────────────────────────────────────────────── --}}
    <div style="background:#fff;border-radius:1rem;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #e2e8f0;overflow:hidden;">

        @if($assignments->isEmpty())
        <div style="padding:3rem;text-align:center;">
            <svg style="width:2.5rem;height:2.5rem;color:#d1d5db;margin:0 auto .875rem;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p style="font-size:.9375rem;font-weight:600;color:#374151;margin:0 0 .25rem;">Tidak ada data</p>
            <p style="font-size:.8125rem;color:#9ca3af;margin:0;">Tidak ada penugasan yang cocok dengan filter yang dipilih</p>
        </div>
        @else

        {{-- Desktop table --}}
        <div class="riwayat-table-wrap" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:700px;">
                <thead>
                    <tr style="border-bottom:2px solid #f1f5f9;">
                        <th style="padding:.75rem 1.25rem;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Submission</th>
                        <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Ditugaskan</th>
                        <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Deadline</th>
                        <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Selesai</th>
                        <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Status</th>
                        <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Rekomendasi</th>
                        <th style="padding:.75rem 1rem;text-align:center;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $a)
                    @php
                        $journal = $a->submission?->journal;
                        $title   = $a->submission?->title ?? 'Tanpa Judul';
                        $rec     = $a->review?->recommendation;

                        $statusMap = [
                            'awaiting_response' => ['label' => 'Menunggu',  'bg' => '#fffbeb', 'color' => '#92400e', 'border' => '#fde68a'],
                            'accepted'          => ['label' => 'Berjalan',  'bg' => '#eff6ff', 'color' => '#1d4ed8', 'border' => '#bfdbfe'],
                            'completed'         => ['label' => 'Selesai',   'bg' => '#f0fdf4', 'color' => '#166534', 'border' => '#bbf7d0'],
                            'declined'          => ['label' => 'Ditolak',   'bg' => '#fef2f2', 'color' => '#991b1b', 'border' => '#fecaca'],
                            'cancelled'         => ['label' => 'Batal',     'bg' => '#f8fafc', 'color' => '#64748b', 'border' => '#e2e8f0'],
                        ];
                        $recMap = [
                            'accept'           => ['label' => 'Diterima',        'color' => '#166534', 'bg' => '#f0fdf4'],
                            'minor_revision'   => ['label' => 'Revisi Minor',    'color' => '#92400e', 'bg' => '#fffbeb'],
                            'major_revision'   => ['label' => 'Revisi Mayor',    'color' => '#7c3aed', 'bg' => '#f5f3ff'],
                            'reject'           => ['label' => 'Ditolak',         'color' => '#991b1b', 'bg' => '#fef2f2'],
                            'see_comments'     => ['label' => 'Lihat Komentar',  'color' => '#374151', 'bg' => '#f9fafb'],
                        ];
                        $st  = $statusMap[$a->status] ?? ['label' => $a->status, 'bg' => '#f9fafb', 'color' => '#374151', 'border' => '#e2e8f0'];
                        $rv  = $rec ? ($recMap[$rec] ?? ['label' => $rec, 'color' => '#374151', 'bg' => '#f9fafb']) : null;

                        $isOverdue = $a->status === 'accepted' && $a->date_due && $a->date_due->isPast();
                    @endphp
                    <tr style="border-bottom:1px solid #f1f5f9;transition:background .1s;"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        {{-- Submission --}}
                        <td style="padding:.875rem 1.25rem;max-width:260px;">
                            @if($journal)
                            <p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#94a3b8;margin:0 0 .2rem;">{{ Str::limit($journal->name, 30) }}</p>
                            @endif
                            <p style="font-size:.875rem;font-weight:600;color:#0f172a;margin:0;line-height:1.4;">
                                {{ Str::limit($title, 60) }}
                            </p>
                            @if($a->round)
                            <p style="font-size:.75rem;color:#94a3b8;margin:.125rem 0 0;">Putaran {{ $a->round }}</p>
                            @endif
                        </td>

                        {{-- Ditugaskan --}}
                        <td style="padding:.875rem 1rem;white-space:nowrap;">
                            <span style="font-size:.8125rem;color:#374151;">
                                {{ $a->date_assigned ? $a->date_assigned->translatedFormat('d M Y') : '—' }}
                            </span>
                        </td>

                        {{-- Deadline --}}
                        <td style="padding:.875rem 1rem;white-space:nowrap;">
                            @if($a->date_due)
                            <span style="font-size:.8125rem;color:{{ $isOverdue ? '#ef4444' : '#374151' }};font-weight:{{ $isOverdue ? '700' : '400' }};">
                                {{ $a->date_due->translatedFormat('d M Y') }}
                                @if($isOverdue)
                                <span style="font-size:.7rem;"> (terlambat)</span>
                                @endif
                            </span>
                            @else
                            <span style="font-size:.8125rem;color:#9ca3af;">—</span>
                            @endif
                        </td>

                        {{-- Selesai --}}
                        <td style="padding:.875rem 1rem;white-space:nowrap;">
                            <span style="font-size:.8125rem;color:#374151;">
                                {{ $a->date_completed ? $a->date_completed->translatedFormat('d M Y') : '—' }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td style="padding:.875rem 1rem;">
                            <span style="background:{{ $st['bg'] }};color:{{ $st['color'] }};border:1px solid {{ $st['border'] }};border-radius:.375rem;padding:.2rem .6rem;font-size:.75rem;font-weight:700;white-space:nowrap;">
                                {{ $st['label'] }}
                            </span>
                        </td>

                        {{-- Rekomendasi --}}
                        <td style="padding:.875rem 1rem;">
                            @if($rv)
                            <span style="background:{{ $rv['bg'] }};color:{{ $rv['color'] }};border-radius:.375rem;padding:.2rem .6rem;font-size:.75rem;font-weight:600;white-space:nowrap;">
                                {{ $rv['label'] }}
                            </span>
                            @else
                            <span style="font-size:.8125rem;color:#d1d5db;">—</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td style="padding:.875rem 1rem;text-align:center;">
                            <div style="display:flex;align-items:center;justify-content:center;gap:.375rem;">
                                @if($a->status === 'accepted')
                                <a href="{{ route('reviewer.review', $a->id) }}"
                                   title="Isi Review"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:1.875rem;height:1.875rem;border-radius:.375rem;background:#f0fdf4;color:#059669;border:1px solid #bbf7d0;transition:all .15s;text-decoration:none;"
                                   onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('reviewer.surat-tugas', $a->id) }}" target="_blank"
                                   title="Surat Tugas"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:1.875rem;height:1.875rem;border-radius:.375rem;background:#eff6ff;color:#3b82f6;border:1px solid #bfdbfe;transition:all .15s;text-decoration:none;"
                                   onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </a>
                                @elseif($a->status === 'completed')
                                <a href="{{ route('reviewer.sertifikat', $a->id) }}" target="_blank"
                                   title="Sertifikat"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:1.875rem;height:1.875rem;border-radius:.375rem;background:#fef3c7;color:#d97706;border:1px solid #fde68a;transition:all .15s;text-decoration:none;"
                                   onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('reviewer.surat-tugas', $a->id) }}" target="_blank"
                                   title="Surat Tugas"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:1.875rem;height:1.875rem;border-radius:.375rem;background:#eff6ff;color:#3b82f6;border:1px solid #bfdbfe;transition:all .15s;text-decoration:none;"
                                   onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </a>
                                @else
                                <span style="font-size:.75rem;color:#d1d5db;">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($assignments->hasPages())
        <div style="padding:.875rem 1.25rem;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
            <p style="font-size:.8125rem;color:#64748b;margin:0;">
                Menampilkan {{ $assignments->firstItem() }}–{{ $assignments->lastItem() }} dari {{ $assignments->total() }} data
            </p>
            <div style="display:flex;gap:.375rem;align-items:center;">
                @if($assignments->onFirstPage())
                <span style="display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:.375rem;background:#f1f5f9;color:#d1d5db;font-size:.875rem;border:1px solid #e2e8f0;">
                    &laquo;
                </span>
                @else
                <button wire:click="previousPage" style="display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:.375rem;background:#fff;color:#374151;font-size:.875rem;border:1px solid #e2e8f0;cursor:pointer;transition:all .1s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                    &laquo;
                </button>
                @endif

                @foreach($assignments->getUrlRange(max(1,$assignments->currentPage()-2), min($assignments->lastPage(),$assignments->currentPage()+2)) as $page => $url)
                @if($page === $assignments->currentPage())
                <span style="display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:.375rem;background:#059669;color:#fff;font-size:.875rem;font-weight:700;border:1px solid #059669;">{{ $page }}</span>
                @else
                <button wire:click="gotoPage({{ $page }})" style="display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:.375rem;background:#fff;color:#374151;font-size:.875rem;border:1px solid #e2e8f0;cursor:pointer;transition:all .1s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                    {{ $page }}
                </button>
                @endif
                @endforeach

                @if($assignments->hasMorePages())
                <button wire:click="nextPage" style="display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:.375rem;background:#fff;color:#374151;font-size:.875rem;border:1px solid #e2e8f0;cursor:pointer;transition:all .1s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                    &raquo;
                </button>
                @else
                <span style="display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:.375rem;background:#f1f5f9;color:#d1d5db;font-size:.875rem;border:1px solid #e2e8f0;">
                    &raquo;
                </span>
                @endif
            </div>
        </div>
        @endif

        @endif {{-- end isEmpty --}}
    </div>
</div>

<style>
@media (max-width: 640px) {
    .riwayat-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
}
</style>
</div>

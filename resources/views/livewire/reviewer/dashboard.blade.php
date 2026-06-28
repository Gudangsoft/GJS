<div style="background:#f1f5f9;min-height:100vh;">

{{-- ══ HERO HEADER ═══════════════════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#0c1a3a 0%,#1a3272 55%,#1d4ed8 100%);position:relative;overflow:hidden;">
    <div style="position:absolute;top:-4rem;right:-4rem;width:18rem;height:18rem;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-3rem;left:30%;width:12rem;height:12rem;border-radius:50%;background:rgba(255,255,255,.03);pointer-events:none;"></div>

    <div style="max-width:72rem;margin:0 auto;padding:2.25rem 1.5rem 0;">
        <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
            <div>
                <p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#93c5fd;margin-bottom:.375rem;">Panel Reviewer</p>
                <h1 style="font-size:1.625rem;font-weight:800;color:#fff;line-height:1.2;margin:0;">
                    Selamat datang, {{ auth()->user()->first_name }}
                </h1>
                <p style="font-size:.875rem;color:#94a3b8;margin-top:.375rem;">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>

            @if($overdueCount > 0)
            <div style="background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);border-radius:.875rem;padding:.75rem 1.125rem;display:flex;align-items:center;gap:.625rem;">
                <svg style="width:1rem;height:1rem;color:#f87171;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
                <span style="font-size:.8125rem;font-weight:700;color:#fca5a5;">{{ $overdueCount }} review melewati deadline</span>
            </div>
            @endif
        </div>

        {{-- Stats row --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(145px,1fr));gap:.875rem;padding-bottom:1.5rem;">
            @foreach([
                ['n' => $counts['pending'],   'label' => 'Undangan Baru',   'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',                          'color' => '#fbbf24', 'tab' => 'pending',   'urgent' => $counts['pending'] > 0],
                ['n' => $counts['active'],    'label' => 'Sedang Berjalan', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',       'color' => '#38bdf8', 'tab' => 'active',    'urgent' => $overdueCount > 0],
                ['n' => $counts['completed'], 'label' => 'Selesai',         'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                    'color' => '#34d399', 'tab' => 'completed', 'urgent' => false],
                ['n' => $avgDays ?? '—',      'label' => 'Hari Rata-Rata',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                     'color' => '#a78bfa', 'tab' => null,        'urgent' => false],
                ['n' => ($acceptanceRate !== null ? $acceptanceRate.'%' : '—'), 'label' => 'Tingkat Terima', 'icon' => 'M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => '#fb923c', 'tab' => null, 'urgent' => false],
            ] as $s)
            @php $isActive = $tab === ($s['tab'] ?? ''); @endphp
            <div @if($s['tab']) wire:click="setTab('{{ $s['tab'] }}')" @endif
                 style="
                    background:{{ $isActive ? 'rgba(255,255,255,.18)' : 'rgba(255,255,255,.07)' }};
                    border:1px solid {{ $isActive ? 'rgba(255,255,255,.4)' : 'rgba(255,255,255,.1)' }};
                    border-radius:.875rem;padding:1rem 1.125rem;
                    {{ $s['tab'] ? 'cursor:pointer;' : '' }}
                    transition:all .15s;
                    {{ $s['urgent'] ? 'box-shadow:0 0 0 2px rgba(251,191,36,.4);' : '' }}
                 ">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:{{ $s['color'] }};" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/>
                    </svg>
                    @if($s['urgent'])
                    <span style="width:.5rem;height:.5rem;border-radius:50%;background:#f59e0b;display:block;"></span>
                    @endif
                </div>
                <div style="font-size:1.625rem;font-weight:800;color:#fff;line-height:1;">{{ $s['n'] }}</div>
                <div style="font-size:.75rem;font-weight:600;color:#94a3b8;margin-top:.25rem;">{{ $s['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div style="max-width:72rem;margin:0 auto;padding:1.5rem;">

@if(session('success'))
<div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">
    <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="reviewer-grid" style="display:grid;grid-template-columns:1fr 272px;gap:1.25rem;align-items:start;">

{{-- ══ LEFT — Assignments ═══════════════════════════════════════════════════ --}}
<div>
    {{-- Tab nav --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-4 overflow-hidden">
        <div style="display:flex;border-bottom:1px solid #f1f5f9;">
            @foreach([
                ['tab' => 'pending',   'label' => 'Undangan',  'count' => $counts['pending'],   'dot' => $counts['pending'] > 0 ? '#f59e0b' : null],
                ['tab' => 'active',    'label' => 'Aktif',     'count' => $counts['active'],    'dot' => $overdueCount > 0 ? '#ef4444' : null],
                ['tab' => 'completed', 'label' => 'Selesai',   'count' => $counts['completed'], 'dot' => null],
                ['tab' => 'declined',  'label' => 'Ditolak',   'count' => $counts['declined'],  'dot' => null],
            ] as $t)
            <button wire:click="setTab('{{ $t['tab'] }}')"
                    style="flex:1;padding:.875rem .5rem;font-size:.8125rem;font-weight:700;border:none;background:transparent;cursor:pointer;transition:all .15s;color:{{ $tab === $t['tab'] ? '#1d4ed8' : '#64748b' }};border-bottom:2px solid {{ $tab === $t['tab'] ? '#1d4ed8' : 'transparent' }};margin-bottom:-1px;">
                <span style="display:inline-flex;align-items:center;gap:.375rem;">
                    {{ $t['label'] }}
                    @if($t['count'] > 0)
                    <span style="font-size:.6875rem;font-weight:800;background:{{ $tab === $t['tab'] ? '#1d4ed8' : '#e2e8f0' }};color:{{ $tab === $t['tab'] ? '#fff' : '#64748b' }};border-radius:.375rem;padding:.125rem .375rem;line-height:1.4;">{{ $t['count'] }}</span>
                    @endif
                    @if($t['dot'])
                    <span style="width:.4375rem;height:.4375rem;border-radius:50%;background:{{ $t['dot'] }};display:inline-block;"></span>
                    @endif
                </span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Assignment cards --}}
    @if($assignments->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="text-center py-16 px-6">
            <div style="width:3.5rem;height:3.5rem;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg style="width:1.75rem;height:1.75rem;color:#cbd5e1;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                </svg>
            </div>
            <p class="font-semibold text-slate-400 text-sm">
                @if($tab === 'pending') Tidak ada undangan review saat ini.
                @elseif($tab === 'active') Tidak ada review yang sedang berjalan.
                @elseif($tab === 'completed') Belum ada review yang diselesaikan.
                @else Tidak ada review yang ditolak.
                @endif
            </p>
        </div>
    </div>

    @else
    <div style="display:flex;flex-direction:column;gap:.75rem;">
        @foreach($assignments as $a)
        @php
            $isOverdue     = $a->date_due && $a->date_due->isPast() && $a->status === 'accepted';
            $daysLeft      = $a->date_due ? now()->diffInDays($a->date_due, false) : null;
            $urgentDeadline = $daysLeft !== null && $daysLeft <= 3 && $daysLeft >= 0;
            $methodLabel   = match($a->review_method ?? '') {
                'double_blind' => 'Double Blind',
                'single_blind' => 'Single Blind',
                'open'         => 'Open Review',
                default        => 'Double Blind',
            };
        @endphp

        <div style="background:#fff;border-radius:1rem;border:1px solid {{ $isOverdue ? '#fecaca' : ($urgentDeadline ? '#fed7aa' : '#e2e8f0') }};box-shadow:0 1px 4px rgba(0,0,0,.05);overflow:hidden;{{ $isOverdue ? 'box-shadow:0 0 0 2px rgba(239,68,68,.15),0 1px 4px rgba(0,0,0,.05);' : '' }}">

            @if($isOverdue)
            <div style="background:#fef2f2;border-bottom:1px solid #fecaca;padding:.5rem 1.25rem;display:flex;align-items:center;gap:.5rem;">
                <svg style="width:.875rem;height:.875rem;color:#dc2626;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <span style="font-size:.75rem;font-weight:700;color:#dc2626;">Deadline terlewat {{ abs((int)$daysLeft) }} hari yang lalu</span>
            </div>
            @elseif($urgentDeadline)
            <div style="background:#fff7ed;border-bottom:1px solid #fed7aa;padding:.5rem 1.25rem;display:flex;align-items:center;gap:.5rem;">
                <svg style="width:.875rem;height:.875rem;color:#ea580c;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span style="font-size:.75rem;font-weight:700;color:#ea580c;">{{ $daysLeft === 0 ? 'Deadline hari ini!' : "Sisa $daysLeft hari lagi" }}</span>
            </div>
            @endif

            <div style="padding:1.25rem 1.375rem;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                    <div style="flex:1;min-width:0;">

                        {{-- Meta badges --}}
                        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.4rem;margin-bottom:.625rem;">
                            @if($a->submission->journal)
                            <span style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;background:#eff6ff;color:#1d4ed8;border-radius:.375rem;padding:.2rem .5rem;">
                                {{ $a->submission->journal->name_abbrev ?? Str::limit($a->submission->journal->name,18) }}
                            </span>
                            @endif
                            @if($a->submission->section)
                            <span style="font-size:.7rem;font-weight:600;background:#f8fafc;color:#475569;border-radius:.375rem;padding:.2rem .5rem;border:1px solid #e2e8f0;">
                                {{ $a->submission->section->title }}
                            </span>
                            @endif
                            <span style="font-size:.7rem;font-weight:600;background:#faf5ff;color:#7c3aed;border-radius:.375rem;padding:.2rem .5rem;border:1px solid #e9d5ff;">
                                {{ $methodLabel }}
                            </span>
                            @if(($a->round ?? 1) > 1)
                            <span style="font-size:.7rem;font-weight:700;background:#fff7ed;color:#c2410c;border-radius:.375rem;padding:.2rem .5rem;border:1px solid #fed7aa;">
                                Putaran {{ $a->round }}
                            </span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <h3 style="font-size:.9375rem;font-weight:700;color:#0f172a;line-height:1.4;margin:0 0 .5rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $a->submission->title }}
                        </h3>

                        {{-- Date row --}}
                        <div style="display:flex;flex-wrap:wrap;gap:.75rem 1.25rem;font-size:.8rem;color:#94a3b8;">
                            @if($a->date_assigned)
                            <span style="display:flex;align-items:center;gap:.3rem;">
                                <svg style="width:.8125rem;height:.8125rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Ditugaskan {{ $a->date_assigned->format('d M Y') }}
                            </span>
                            @endif
                            @if($a->date_due)
                            <span style="display:flex;align-items:center;gap:.3rem;color:{{ $isOverdue ? '#dc2626' : ($urgentDeadline ? '#ea580c' : '#94a3b8') }};font-weight:{{ ($isOverdue || $urgentDeadline) ? '700' : '400' }};">
                                <svg style="width:.8125rem;height:.8125rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Deadline {{ $a->date_due->format('d M Y') }}
                            </span>
                            @endif
                            @if($a->status === 'completed' && $a->date_completed)
                            <span style="display:flex;align-items:center;gap:.3rem;color:#059669;font-weight:600;">
                                <svg style="width:.8125rem;height:.8125rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Selesai {{ $a->date_completed->format('d M Y') }}
                            </span>
                            @endif
                        </div>

                        {{-- Reviewer guidelines --}}
                        @if($tab === 'active' && $a->submission->section?->reviewer_guidelines)
                        <details style="margin-top:.75rem;">
                            <summary style="font-size:.75rem;font-weight:700;color:#b45309;cursor:pointer;list-style:none;display:flex;align-items:center;gap:.375rem;">
                                <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Panduan Reviewer — Seksi {{ $a->submission->section->title }}
                            </summary>
                            <div style="margin-top:.5rem;padding:.75rem 1rem;background:#fffbeb;border-radius:.5rem;border:1px solid #fde68a;font-size:.8125rem;color:#92400e;line-height:1.65;">
                                {!! nl2br(e($a->submission->section->reviewer_guidelines)) !!}
                            </div>
                        </details>
                        @endif
                    </div>

                    {{-- Action buttons --}}
                    <div style="display:flex;flex-direction:column;gap:.5rem;align-items:flex-end;flex-shrink:0;min-width:8rem;">
                        @if($a->status === 'awaiting_response')
                        <button wire:click="acceptInvitation({{ $a->id }})" wire:confirm="Terima undangan review ini?"
                                style="display:inline-flex;align-items:center;gap:.4rem;background:linear-gradient(135deg,#059669,#047857);color:#fff;font-size:.8125rem;font-weight:700;padding:.5rem 1.125rem;border-radius:.625rem;border:none;cursor:pointer;white-space:nowrap;width:100%;justify-content:center;">
                            <svg style="width:.8125rem;height:.8125rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Terima
                        </button>
                        <button wire:click="declineInvitation({{ $a->id }})" wire:confirm="Tolak undangan review ini?"
                                style="display:inline-flex;align-items:center;gap:.4rem;background:#fff1f2;color:#dc2626;font-size:.8125rem;font-weight:700;padding:.5rem 1.125rem;border-radius:.625rem;border:1px solid #fecaca;cursor:pointer;white-space:nowrap;width:100%;justify-content:center;">
                            <svg style="width:.8125rem;height:.8125rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak
                        </button>

                        @elseif($a->status === 'accepted')
                        <a href="{{ route('reviewer.review', $a) }}"
                           style="display:inline-flex;align-items:center;justify-content:center;gap:.4rem;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;font-size:.8125rem;font-weight:700;padding:.5rem 1.25rem;border-radius:.625rem;text-decoration:none;white-space:nowrap;width:100%;">
                            <svg style="width:.8125rem;height:.8125rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/></svg>
                            {{ $a->review ? 'Lanjutkan' : 'Mulai Review' }}
                        </a>

                        @elseif($a->status === 'completed')
                        <span style="display:inline-flex;align-items:center;justify-content:center;gap:.4rem;background:#f0fdf4;color:#059669;font-size:.8125rem;font-weight:700;padding:.5rem 1rem;border-radius:.625rem;border:1px solid #bbf7d0;width:100%;">
                            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Terkirim
                        </span>
                        @if($a->review)
                        <a href="{{ route('reviewer.review', $a) }}"
                           style="display:inline-flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:600;color:#64748b;text-decoration:none;padding:.375rem .75rem;border-radius:.5rem;border:1px solid #e2e8f0;background:#f8fafc;width:100%;">
                            Lihat Review
                        </a>
                        @endif

                        @elseif(in_array($a->status, ['declined','cancelled']))
                        <span style="display:inline-flex;align-items:center;justify-content:center;gap:.4rem;background:#fef2f2;color:#dc2626;font-size:.8125rem;font-weight:700;padding:.5rem 1rem;border-radius:.625rem;border:1px solid #fecaca;width:100%;">
                            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Ditolak
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ══ RIGHT SIDEBAR ═══════════════════════════════════════════════════════ --}}
<div style="display:flex;flex-direction:column;gap:1rem;">

    {{-- Upcoming deadlines --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div style="padding:.875rem 1.125rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.625rem;">
            <svg style="width:1rem;height:1rem;color:#f59e0b;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p style="font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#475569;margin:0;">Deadline 14 Hari</p>
        </div>

        @if($upcomingDeadlines->isEmpty())
        <div style="padding:1.5rem;text-align:center;">
            <svg style="width:2rem;height:2rem;color:#e2e8f0;margin:0 auto .5rem;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
            </svg>
            <p style="font-size:.8125rem;color:#94a3b8;font-weight:500;">Tidak ada deadline mendekat</p>
        </div>
        @else
        <div>
            @foreach($upcomingDeadlines as $d)
            @php $days = now()->diffInDays($d->date_due, false); @endphp
            <div style="padding:.75rem 1.125rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;gap:.75rem;">
                <div style="text-align:center;width:2.75rem;flex-shrink:0;">
                    <div style="font-size:1.125rem;font-weight:800;line-height:1;color:{{ $days <= 3 ? '#dc2626' : ($days <= 7 ? '#ea580c' : '#1d4ed8') }};">
                        {{ $d->date_due->format('d') }}
                    </div>
                    <div style="font-size:.625rem;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-top:.1rem;">
                        {{ $d->date_due->format('M') }}
                    </div>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:.8125rem;font-weight:600;color:#0f172a;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ Str::limit($d->submission->title, 32) }}
                    </p>
                    <p style="font-size:.75rem;color:{{ $days <= 3 ? '#dc2626' : '#94a3b8' }};margin:.15rem 0 0;font-weight:{{ $days <= 3 ? '700' : '500' }};">
                        {{ $days === 0 ? 'Hari ini!' : "Dalam $days hari" }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Reviewer profile card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div style="padding:.875rem 1.125rem;border-bottom:1px solid #f1f5f9;">
            <p style="font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#475569;margin:0;">Profil Saya</p>
        </div>
        <div style="padding:1.125rem;">
            <div style="display:flex;align-items:center;gap:.875rem;margin-bottom:1rem;">
                <div style="width:3rem;height:3rem;border-radius:50%;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.125rem;font-weight:800;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->first_name,0,1)) }}
                </div>
                <div style="min-width:0;">
                    <p style="font-size:.9375rem;font-weight:700;color:#0f172a;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    </p>
                    <p style="font-size:.75rem;color:#64748b;margin:.125rem 0 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->email }}</p>
                </div>
            </div>

            @if(auth()->user()->affiliation)
            <div style="font-size:.8rem;color:#475569;background:#f8fafc;border-radius:.5rem;padding:.625rem .75rem;margin-bottom:.75rem;line-height:1.5;">
                <span style="font-weight:700;">Institusi:</span> {{ auth()->user()->affiliation }}
            </div>
            @endif

            @if(auth()->user()->orcid)
            <a href="https://orcid.org/{{ auth()->user()->orcid }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:.4rem;font-size:.8rem;font-weight:600;color:#a6ce39;text-decoration:none;margin-bottom:.75rem;">
                <svg style="width:.875rem;height:.875rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.372 0 0 5.372 0 12s5.372 12 12 12 12-5.372 12-12S18.628 0 12 0zm-1.457 4.669c.456 0 .826.37.826.826s-.37.826-.826.826-.826-.37-.826-.826.37-.826.826-.826zm2.914 14.662h-1.828V9.388h1.828v9.943zm-5.828 0V9.388h1.828v9.943H7.629z"/></svg>
                {{ auth()->user()->orcid }}
            </a>
            @endif

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;border-top:1px solid #f1f5f9;padding-top:.75rem;">
                @foreach([
                    ['n' => $counts['completed'], 'label' => 'Selesai',    'c' => '#059669'],
                    ['n' => $avgDays ?? '—',      'label' => 'Hari Rata²', 'c' => '#7c3aed'],
                ] as $m)
                <div style="text-align:center;padding:.5rem;background:#f8fafc;border-radius:.5rem;">
                    <div style="font-size:1.25rem;font-weight:800;color:{{ $m['c'] }};line-height:1;">{{ $m['n'] }}</div>
                    <div style="font-size:.6875rem;font-weight:600;color:#94a3b8;margin-top:.2rem;">{{ $m['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div style="padding:.875rem 1.125rem;border-bottom:1px solid #f1f5f9;">
            <p style="font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#475569;margin:0;">Tautan Cepat</p>
        </div>
        <div style="padding:.375rem;">
            @foreach([
                ['label' => 'Beranda Jurnal', 'href' => route('home'), 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['label' => 'Dashboard Penulis', 'href' => route('dashboard.author'), 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
            ] as $link)
            <a href="{{ $link['href'] }}"
               style="display:flex;align-items:center;gap:.625rem;padding:.625rem .75rem;border-radius:.5rem;text-decoration:none;color:#475569;font-size:.8125rem;font-weight:600;transition:background .15s;"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <svg style="width:.875rem;height:.875rem;flex-shrink:0;color:#94a3b8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
                </svg>
                {{ $link['label'] }}
                <svg style="width:.75rem;height:.75rem;margin-left:auto;color:#cbd5e1;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endforeach
        </div>
    </div>

</div>{{-- /sidebar --}}
</div>{{-- /grid --}}

</div>{{-- /content --}}
</div>{{-- /root --}}

<style>
@media (max-width: 900px) {
    .reviewer-grid { grid-template-columns: 1fr !important; }
}
</style>

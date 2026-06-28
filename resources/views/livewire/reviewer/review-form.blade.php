@php
$sub     = $assignment->submission;
$blind   = in_array($assignment->review_method, ['double_blind','triple_blind']);
$recMap  = [
    'accept'              => ['Terima',            '#059669','#f0fdf4','#bbf7d0'],
    'pending_revisions'   => ['Revisi Minor',       '#d97706','#fffbeb','#fde68a'],
    'resubmit_here'       => ['Revisi Mayor',       '#7c3aed','#faf5ff','#ddd6fe'],
    'resubmit_elsewhere'  => ['Submit Jurnal Lain', '#0891b2','#ecfeff','#a5f3fc'],
    'decline'             => ['Tolak',              '#dc2626','#fff1f2','#fecaca'],
    'see_comments'        => ['Lihat Komentar',     '#64748b','#f8fafc','#e2e8f0'],
];
$criteriaLabels = [
    'relevance'   => ['Relevansi Topik',    'Kesesuaian topik dengan fokus jurnal dan kontribusi terhadap bidang ilmu'],
    'originality' => ['Orisinalitas',        'Kebaruan ide, temuan, atau pendekatan yang ditawarkan naskah'],
    'methodology' => ['Metodologi',          'Ketepatan, kejelasan, dan kekakuan metode penelitian yang digunakan'],
    'analysis'    => ['Kedalaman Analisis',  'Kualitas interpretasi data, diskusi hasil, dan ketajaman argumentasi'],
    'writing'     => ['Kualitas Penulisan',  'Kejelasan, struktur, bahasa, dan gaya penulisan ilmiah'],
    'references'  => ['Referensi & Sitasi', 'Kelengkapan, kemutakhiran, dan ketepatan penggunaan referensi'],
];
$ratingOptions = [
    'excellent' => ['Sangat Baik', '#059669', '#f0fdf4'],
    'good'      => ['Baik',        '#2563eb', '#eff6ff'],
    'fair'      => ['Cukup',       '#d97706', '#fffbeb'],
    'poor'      => ['Kurang',      '#dc2626', '#fff1f2'],
    'na'        => ['N/A',         '#64748b', '#f8fafc'],
];
$steps = [1 => 'Naskah', 2 => 'Panduan', 3 => 'Review', 4 => 'Selesai'];
@endphp

<div style="background:#f1f5f9;min-height:100vh;">

{{-- PAGE HEADER --}}
<div style="background:#fff;border-bottom:1px solid #e2e8f0;padding:.875rem 1.5rem;position:sticky;top:0;z-index:20;">
    <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
        <a href="{{ route('reviewer.dashboard') }}"
           style="display:inline-flex;align-items:center;gap:.375rem;color:#64748b;font-size:.8125rem;font-weight:600;text-decoration:none;flex-shrink:0;">
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Dashboard
        </a>
        <div style="width:1px;height:1rem;background:#e2e8f0;flex-shrink:0;"></div>
        <p style="font-size:.875rem;font-weight:600;color:#0f172a;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            {{ Str::limit($sub->title, 80) }}
        </p>
        @if($assignment->status === 'completed')
        <span style="font-size:.75rem;font-weight:700;background:#f0fdf4;color:#059669;border:1px solid #bbf7d0;border-radius:.375rem;padding:.25rem .625rem;flex-shrink:0;">
            ✓ Review Terkirim
        </span>
        @endif
    </div>
</div>

<div style="padding:1.5rem 1.5rem 4rem;">

@if(session('saved'))
<div style="display:flex;align-items:center;gap:.5rem;padding:.625rem 1rem;border-radius:.75rem;background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;font-size:.875rem;font-weight:600;margin-bottom:1rem;">
    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('saved') }}
</div>
@endif

{{-- STEP INDICATOR --}}
<div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;margin-bottom:1.25rem;overflow:hidden;max-width:600px;">
    <div style="display:flex;">
        @foreach($steps as $n => $label)
        @php
            $isDone    = $n < $step || $assignment->status === 'completed';
            $isCurrent = $n === $step && $assignment->status !== 'completed';
            $isLast    = $n === count($steps);
        @endphp
        <button wire:click="goStep({{ $n }})"
                style="flex:1;display:flex;flex-direction:column;align-items:center;gap:.375rem;padding:.875rem .5rem;border:none;cursor:{{ ($isDone || $isCurrent) ? 'pointer' : 'default' }};background:{{ $isCurrent ? '#f0fdf4' : '#fff' }};border-right:{{ $isLast ? 'none' : '1px solid #f1f5f9' }};border-bottom:2px solid {{ $isCurrent ? '#059669' : ($isDone ? '#bbf7d0' : 'transparent') }};"
                {{ (!$isDone && !$isCurrent) ? 'disabled' : '' }}>
            <span style="width:1.75rem;height:1.75rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800;background:{{ $isDone ? '#059669' : ($isCurrent ? '#059669' : '#e2e8f0') }};color:{{ ($isDone || $isCurrent) ? '#fff' : '#94a3b8' }};">
                @if($isDone && !$isCurrent)
                <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                @else{{ $n }}@endif
            </span>
            <span style="font-size:.75rem;font-weight:{{ $isCurrent ? '700' : '500' }};color:{{ $isCurrent ? '#059669' : ($isDone ? '#475569' : '#94a3b8') }};">{{ $label }}</span>
        </button>
        @endforeach
    </div>
</div>

{{-- ════════ STEP 1 — NASKAH ════════ --}}
@if($step === 1)
<div style="display:grid;grid-template-columns:1fr 280px;gap:1.25rem;align-items:start;" class="rg">
<div style="display:flex;flex-direction:column;gap:1rem;">

    @if($blind)
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:.875rem;padding:.875rem 1.125rem;display:flex;gap:.75rem;">
        <svg style="width:1rem;height:1rem;color:#2563eb;flex-shrink:0;margin-top:.1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
        <div>
            <p style="font-size:.8125rem;font-weight:700;color:#1e40af;margin:0 0 .25rem;">{{ $assignment->review_method === 'double_blind' ? 'Double Blind Review' : 'Triple Blind Review' }}</p>
            <p style="font-size:.8rem;color:#3b82f6;line-height:1.5;margin:0;">Identitas penulis dan reviewer disembunyikan untuk menjaga objektivitas review.</p>
        </div>
    </div>
    @endif

    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Informasi Naskah</p>
        </div>
        <div style="padding:1.25rem;">
            @if($sub->section)
            <span style="display:inline-block;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;background:#f0fdf4;color:#059669;border:1px solid #bbf7d0;border-radius:.375rem;padding:.2rem .6rem;margin-bottom:.875rem;">{{ $sub->section->title }}</span>
            @endif
            <h2 style="font-size:1.25rem;font-weight:800;color:#0f172a;line-height:1.4;margin:0 0 .625rem;">{{ $sub->title }}</h2>
            @if($sub->subtitle)<p style="font-size:.9375rem;color:#475569;font-style:italic;margin:0 0 .875rem;">{{ $sub->subtitle }}</p>@endif

            @if(!$blind && $sub->contributors->isNotEmpty())
            <div style="margin-bottom:1rem;">
                <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin:0 0 .5rem;">Penulis</p>
                <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
                    @foreach($sub->contributors as $c)
                    <div style="display:flex;align-items:center;gap:.5rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:.5rem;padding:.375rem .625rem;">
                        <div style="width:1.5rem;height:1.5rem;border-radius:50%;background:linear-gradient(135deg,#059669,#047857);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.625rem;font-weight:800;flex-shrink:0;">{{ strtoupper(substr($c->first_name,0,1)) }}</div>
                        <div>
                            <p style="font-size:.8rem;font-weight:600;color:#0f172a;margin:0;">{{ $c->full_name }}</p>
                            @if($c->affiliation)<p style="font-size:.7rem;color:#94a3b8;margin:0;">{{ Str::limit($c->affiliation,35) }}</p>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($sub->keywords)
            <div style="margin-bottom:1rem;">
                <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin:0 0 .5rem;">Kata Kunci</p>
                <div style="display:flex;flex-wrap:wrap;gap:.375rem;">
                    @foreach($sub->keywords as $kw)
                    <span style="font-size:.75rem;background:#f1f5f9;color:#475569;border-radius:9999px;padding:.25rem .625rem;border:1px solid #e2e8f0;">{{ $kw }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($sub->abstract)
            <div>
                <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin:0 0 .5rem;">Abstrak</p>
                <div style="font-size:.875rem;color:#374151;line-height:1.8;background:#f8fafc;border-radius:.75rem;padding:1rem;border:1px solid #e2e8f0;">{!! $sub->abstract !!}</div>
            </div>
            @endif
        </div>
    </div>

    @if($sub->files->isNotEmpty())
    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">File Naskah untuk Diunduh</p>
        </div>
        <div style="padding:.75rem 1.25rem;display:flex;flex-direction:column;gap:.5rem;">
            @foreach($sub->files as $file)
            @php $ext = strtoupper(pathinfo($file->original_file_name ?? '', PATHINFO_EXTENSION)); @endphp
            <a href="{{ asset('storage/' . $file->path) }}" target="_blank"
               style="display:flex;align-items:center;gap:.875rem;padding:.75rem 1rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:.75rem;text-decoration:none;"
               onmouseover="this.style.background='#f0fdf4';this.style.borderColor='#bbf7d0'" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                <div style="width:2.25rem;height:2.25rem;background:#fff1f2;border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.625rem;font-weight:800;color:#dc2626;">{{ $ext ?: 'FILE' }}</div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:.8125rem;font-weight:600;color:#0f172a;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $file->original_file_name ?? 'Naskah' }}</p>
                    <p style="font-size:.75rem;color:#94a3b8;margin:.125rem 0 0;">{{ ($file->file_type ?? 'Submission') . ($file->file_size ? ' · ' . round($file->file_size/1024,1) . ' KB' : '') }}</p>
                </div>
                <svg style="width:.875rem;height:.875rem;color:#059669;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>{{-- /left --}}

<div style="display:flex;flex-direction:column;gap:1rem;">
    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.875rem 1.125rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Detail Penugasan</p>
        </div>
        <div style="padding:1.125rem;display:flex;flex-direction:column;gap:.875rem;">
            <div><p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin:0 0 .25rem;">Jurnal</p><p style="font-size:.875rem;font-weight:600;color:#0f172a;margin:0;">{{ $sub->journal->name }}</p></div>
            @php $mMap=['double_blind'=>'Double Blind','single_blind'=>'Single Blind','triple_blind'=>'Triple Blind','open'=>'Open Review']; @endphp
            <div><p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin:0 0 .25rem;">Metode</p><p style="font-size:.875rem;color:#0f172a;margin:0;">{{ $mMap[$assignment->review_method] ?? $assignment->review_method }}</p></div>
            <div><p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin:0 0 .25rem;">Ditugaskan</p><p style="font-size:.875rem;color:#0f172a;margin:0;">{{ $assignment->date_assigned?->format('d M Y') ?? '—' }}</p></div>
            @if($assignment->date_due)
            @php $overdue=(bool)$assignment->date_due->isPast(); $daysLeft=(int)now()->diffInDays($assignment->date_due,false); @endphp
            <div>
                <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin:0 0 .25rem;">Deadline</p>
                <p style="font-size:.875rem;font-weight:600;color:{{ $overdue ? '#dc2626' : ($daysLeft<=3 ? '#ea580c' : '#0f172a') }};margin:0;">
                    {{ $assignment->date_due->format('d M Y') }}
                    @if($overdue) <span style="display:block;font-size:.75rem;font-weight:700;color:#dc2626;">⚠ Lewat {{ abs($daysLeft) }} hari</span>
                    @elseif($daysLeft<=7) <span style="display:block;font-size:.75rem;color:#ea580c;">Sisa {{ $daysLeft }} hari</span>
                    @endif
                </p>
            </div>
            @endif
            <div><p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin:0 0 .25rem;">Putaran</p><p style="font-size:.875rem;color:#0f172a;margin:0;">Putaran {{ $assignment->round ?? 1 }}</p></div>
        </div>
    </div>

    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.75rem 1rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;"><p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Langkah Review</p></div>
        <div style="padding:.75rem 1rem;display:flex;flex-direction:column;gap:.375rem;">
            @foreach($steps as $n => $lbl)
            @php $done=$n<$step||$assignment->status==='completed'; @endphp
            <div style="display:flex;align-items:center;gap:.625rem;">
                <div style="width:1.375rem;height:1.375rem;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:.625rem;font-weight:800;background:{{ $done?'#059669':($n===$step?'#f0fdf4':'#f1f5f9') }};color:{{ $done?'#fff':($n===$step?'#059669':'#94a3b8') }};border:{{ $n===$step?'2px solid #059669':'none' }};">
                    @if($done) <svg style="width:.625rem;height:.625rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> @else{{ $n }}@endif
                </div>
                <span style="font-size:.8rem;font-weight:{{ $n===$step?'700':'500' }};color:{{ $n===$step?'#059669':($done?'#475569':'#94a3b8') }};">{{ $lbl }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
</div>
<div style="display:flex;justify-content:flex-end;margin-top:1.25rem;">
    <button wire:click="nextStep" style="display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#059669,#047857);color:#fff;font-size:.875rem;font-weight:700;padding:.625rem 1.5rem;border-radius:.75rem;border:none;cursor:pointer;">Lanjut ke Panduan <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></button>
</div>

{{-- ════════ STEP 2 — PANDUAN ════════ --}}
@elseif($step === 2)
<div style="display:flex;flex-direction:column;gap:1rem;max-width:900px;">

    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Panduan Umum Reviewer</p>
        </div>
        <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.875rem;">
            @foreach([
                ['#059669','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','Evaluasi naskah secara objektif berdasarkan kualitas ilmiah, bukan preferensi pribadi.'],
                ['#f59e0b','M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H2.645c-1.73 0-2.813-1.874-1.948-3.374L10.052 3.378c.866-1.5 3.032-1.5 3.898 0l7.353 12.748zM12 15.75h.008v.008H12v-.008z','Jika ada konflik kepentingan dengan penulis atau topik, segera informasikan editor.'],
                ['#7c3aed','M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z','Jaga kerahasiaan naskah. Jangan mendiskusikan konten ke pihak lain.'],
                ['#2563eb','M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','Selesaikan review sebelum deadline. Hubungi editor jika membutuhkan perpanjangan waktu.'],
                ['#0891b2','M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z','Berikan komentar konstruktif dan spesifik yang membantu penulis memperbaiki naskah.'],
            ] as [$color,$icon,$text])
            <div style="display:flex;gap:.75rem;align-items:flex-start;padding:.75rem;background:#f8fafc;border-radius:.75rem;">
                <div style="width:2rem;height:2rem;border-radius:.5rem;background:{{ $color }}1a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:1rem;height:1rem;color:{{ $color }};" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                </div>
                <p style="font-size:.875rem;color:#374151;margin:0;padding-top:.25rem;line-height:1.65;">{{ $text }}</p>
            </div>
            @endforeach
        </div>
    </div>

    @if($sub->section?->reviewer_guidelines)
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:1rem;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;border-bottom:1px solid #fde68a;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#92400e;margin:0;">Panduan Khusus — {{ $sub->section->title }}</p>
        </div>
        <div style="padding:1.25rem;font-size:.875rem;color:#92400e;line-height:1.8;">{!! nl2br(e($sub->section->reviewer_guidelines)) !!}</div>
    </div>
    @endif

    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Kriteria yang Akan Dinilai</p>
        </div>
        <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:1fr 1fr;gap:.625rem;">
            @foreach($criteriaLabels as $key => [$name, $desc])
            <div style="display:flex;align-items:flex-start;gap:.75rem;padding:.75rem;background:#f8fafc;border-radius:.75rem;border:1px solid {{ $criteria[$key] ? '#bbf7d0' : '#e2e8f0' }};">
                <div style="width:1.5rem;height:1.5rem;border-radius:50%;background:{{ $criteria[$key] ? '#059669' : '#e2e8f0' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:.125rem;">
                    <svg style="width:.75rem;height:.75rem;color:#fff;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $criteria[$key] ? 'M4.5 12.75l6 6 9-13.5' : 'M12 4.5v15m7.5-7.5h-15' }}"/></svg>
                </div>
                <div style="flex:1;">
                    <p style="font-size:.875rem;font-weight:700;color:#0f172a;margin:0;">{{ $name }}</p>
                    <p style="font-size:.75rem;color:#94a3b8;margin:.125rem 0 0;">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Pernyataan Konflik Kepentingan</p>
        </div>
        <div style="padding:1.25rem;">
            <label style="display:flex;align-items:flex-start;gap:.75rem;cursor:pointer;margin-bottom:1rem;">
                <input type="checkbox" wire:model.live="noConflict" style="width:1rem;height:1rem;margin-top:.25rem;accent-color:#059669;flex-shrink:0;">
                <span style="font-size:.875rem;color:#374151;line-height:1.6;">Saya menyatakan tidak memiliki konflik kepentingan dengan penulis, institusi, atau topik penelitian ini yang dapat mempengaruhi objektivitas review saya.</span>
            </label>
            @if(!$noConflict)
            <div>
                <label style="display:block;font-size:.8rem;font-weight:700;color:#64748b;margin-bottom:.5rem;">Jelaskan konflik kepentingan Anda:</label>
                <textarea wire:model="competingInterests" rows="3" placeholder="Deskripsikan hubungan atau kepentingan yang mungkin mempengaruhi review Anda..." style="width:100%;padding:.75rem;border:1px solid #e2e8f0;border-radius:.625rem;font-size:.875rem;line-height:1.6;resize:vertical;box-sizing:border-box;"></textarea>
            </div>
            @endif
        </div>
    </div>
</div>

<div style="display:flex;justify-content:space-between;margin-top:1.25rem;max-width:900px;">
    <button wire:click="prevStep" style="display:inline-flex;align-items:center;gap:.5rem;background:#fff;color:#475569;font-size:.875rem;font-weight:700;padding:.625rem 1.375rem;border-radius:.75rem;border:1px solid #e2e8f0;cursor:pointer;"><svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg> Kembali</button>
    <button wire:click="nextStep" style="display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#059669,#047857);color:#fff;font-size:.875rem;font-weight:700;padding:.625rem 1.5rem;border-radius:.75rem;border:none;cursor:pointer;">Mulai Review <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></button>
</div>

{{-- ════════ STEP 3 — FORM REVIEW ════════ --}}
@elseif($step === 3)

@if($assignment->status === 'completed')
<div style="display:flex;align-items:center;gap:.5rem;padding:.625rem 1rem;border-radius:.75rem;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-size:.875rem;font-weight:600;margin-bottom:1rem;">
    <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    Review sudah dikirim. Anda masih dapat mengedit jika diperlukan.
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 300px;gap:1.25rem;align-items:start;" class="rg">
<div style="display:flex;flex-direction:column;gap:1rem;">

    {{-- File untuk Direview --}}
    <div style="background:#fff;border-radius:1rem;border:2px solid #059669;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-bottom:1px solid #bbf7d0;display:flex;align-items:center;gap:.625rem;">
            <div style="width:2rem;height:2rem;background:#059669;border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:1.125rem;height:1.125rem;color:#fff;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            </div>
            <div>
                <p style="font-size:.8125rem;font-weight:800;color:#065f46;margin:0;">File yang Perlu Direview</p>
                <p style="font-size:.75rem;color:#059669;margin:0;">Unduh dan baca naskah sebelum mengisi form review</p>
            </div>
        </div>
        <div style="padding:1rem 1.25rem;">
            @if($sub->files->isNotEmpty())
            <div style="display:flex;flex-direction:column;gap:.625rem;">
                @foreach($sub->files as $file)
                @php
                    $ext  = strtoupper(pathinfo($file->original_file_name ?? '', PATHINFO_EXTENSION));
                    $extColors = ['PDF'=>['#dc2626','#fff1f2'], 'DOCX'=>['#2563eb','#eff6ff'], 'DOC'=>['#2563eb','#eff6ff']];
                    [$extColor, $extBg] = $extColors[$ext] ?? ['#7c3aed','#faf5ff'];
                    $sizeText = $file->file_size ? (round($file->file_size/1024) >= 1024 ? round($file->file_size/1024/1024,1).' MB' : round($file->file_size/1024).' KB') : '';
                @endphp
                <a href="{{ asset('storage/' . $file->path) }}" target="_blank"
                   style="display:flex;align-items:center;gap:1rem;padding:1rem 1.125rem;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:.875rem;text-decoration:none;transition:all .15s;"
                   onmouseover="this.style.background='#f0fdf4';this.style.borderColor='#059669';this.style.boxShadow='0 2px 8px rgba(5,150,105,.12)'"
                   onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                    <div style="width:3rem;height:3.5rem;background:{{ $extBg }};border-radius:.625rem;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;border:1.5px solid {{ $extColor }}33;">
                        <svg style="width:1.125rem;height:1.125rem;color:{{ $extColor }};" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        <span style="font-size:.5rem;font-weight:800;color:{{ $extColor }};letter-spacing:.05em;margin-top:.125rem;">{{ $ext ?: '???' }}</span>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:.9375rem;font-weight:700;color:#0f172a;margin:0 0 .25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $file->original_file_name ?? 'Naskah' }}
                        </p>
                        <div style="display:flex;align-items:center;gap:.625rem;flex-wrap:wrap;">
                            @if($file->genre)
                            <span style="font-size:.7rem;font-weight:700;background:#f1f5f9;color:#475569;border-radius:.25rem;padding:.1rem .4rem;">{{ $file->genre }}</span>
                            @endif
                            @if($sizeText)
                            <span style="font-size:.75rem;color:#94a3b8;">{{ $sizeText }}</span>
                            @endif
                            @if($file->revision && $file->revision > 1)
                            <span style="font-size:.7rem;font-weight:700;background:#eff6ff;color:#2563eb;border-radius:.25rem;padding:.1rem .4rem;">Revisi {{ $file->revision }}</span>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0;">
                        <span style="font-size:.8125rem;font-weight:700;color:#059669;white-space:nowrap;">Unduh</span>
                        <div style="width:2rem;height:2rem;background:#059669;border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                            <svg style="width:.875rem;height:.875rem;color:#fff;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div style="text-align:center;padding:1.5rem;color:#94a3b8;">
                <svg style="width:2.5rem;height:2.5rem;margin:0 auto .75rem;display:block;color:#cbd5e1;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <p style="font-size:.875rem;font-weight:600;color:#64748b;margin:0 0 .375rem;">Belum ada file tersedia</p>
                <p style="font-size:.8rem;margin:0;">Hubungi editor jika file naskah belum diunggah</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Kriteria Penilaian --}}
    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Penilaian Kriteria</p>
            <p style="font-size:.75rem;color:#94a3b8;margin:.25rem 0 0;">Klik tombol untuk memberikan nilai pada setiap aspek naskah</p>
        </div>
        <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.875rem;">
            @foreach($criteriaLabels as $key => [$name, $desc])
            @php $selected = $criteria[$key] ?? ''; @endphp
            <div style="border-radius:.875rem;border:2px solid {{ $selected ? '#059669' : '#e2e8f0' }};overflow:hidden;transition:border-color .15s;">
                <div style="padding:.875rem 1.125rem;background:{{ $selected ? '#f0fdf4' : '#f8fafc' }};border-bottom:1px solid {{ $selected ? '#bbf7d0' : '#f1f5f9' }};">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                        <div>
                            <p style="font-size:.9375rem;font-weight:700;color:#0f172a;margin:0 0 .2rem;">{{ $name }}</p>
                            <p style="font-size:.8rem;color:#94a3b8;margin:0;">{{ $desc }}</p>
                        </div>
                        @if($selected)
                        @php [$selLabel,$selColor,$selBg] = $ratingOptions[$selected]; @endphp
                        <span style="font-size:.75rem;font-weight:800;color:{{ $selColor }};background:{{ $selBg }};border:1.5px solid {{ $selColor }};border-radius:.375rem;padding:.25rem .625rem;flex-shrink:0;">{{ $selLabel }}</span>
                        @else
                        <span style="font-size:.75rem;color:#cbd5e1;flex-shrink:0;">Belum dinilai</span>
                        @endif
                    </div>
                </div>
                <div style="padding:.75rem 1.125rem;display:flex;gap:.5rem;flex-wrap:wrap;background:#fff;">
                    @foreach($ratingOptions as $rVal => [$rLabel, $rColor, $rBg])
                    <button type="button"
                            wire:click="setCriteria('{{ $key }}', '{{ $rVal }}')"
                            style="
                                padding:.375rem .875rem;border-radius:.5rem;font-size:.8125rem;font-weight:700;cursor:pointer;
                                border:2px solid {{ $selected===$rVal ? $rColor : '#e2e8f0' }};
                                background:{{ $selected===$rVal ? $rColor : '#fff' }};
                                color:{{ $selected===$rVal ? '#fff' : $rColor }};
                                transition:all .12s;
                            "
                            onmouseover="if('{{ $selected }}'!=='{{ $rVal }}'){this.style.background='{{ $rBg }}';this.style.borderColor='{{ $rColor }}'}"
                            onmouseout="if('{{ $selected }}'!=='{{ $rVal }}'){this.style.background='#fff';this.style.borderColor='#e2e8f0'}">
                        {{ $rLabel }}
                    </button>
                    @endforeach
                    @if($selected)
                    <button type="button"
                            wire:click="setCriteria('{{ $key }}', '')"
                            style="padding:.375rem .625rem;border-radius:.5rem;font-size:.75rem;font-weight:600;cursor:pointer;border:1.5px solid #e2e8f0;background:#f8fafc;color:#94a3b8;margin-left:auto;">
                        ✕ Reset
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Rekomendasi --}}
    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Rekomendasi</p>
            <p style="font-size:.75rem;color:#94a3b8;margin:.25rem 0 0;">Pilih keputusan akhir Anda untuk naskah ini</p>
        </div>
        <div style="padding:1.25rem;display:grid;grid-template-columns:1fr 1fr 1fr;gap:.75rem;">
            @foreach([
                ['accept',            'Terima',           'Layak diterima tanpa atau revisi minor',             'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                                                                                                                                                                                                            '#059669','#f0fdf4','#86efac'],
                ['pending_revisions', 'Revisi Minor',     'Perbaikan kecil sebelum diterima',                  'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125',                                                                                                                                                                                                                                                             '#d97706','#fffbeb','#fcd34d'],
                ['resubmit_here',     'Revisi Mayor',     'Perbaikan substansial, submit ulang',               'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99',                                                                                                                                                                                                                                                     '#7c3aed','#faf5ff','#c4b5fd'],
                ['resubmit_elsewhere','Jurnal Lain',      'Lebih cocok untuk jurnal lain',                     'M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25',                                                                                                                                                                                                                                                                                            '#0891b2','#ecfeff','#a5f3fc'],
                ['decline',           'Tolak',            'Tidak memenuhi standar publikasi',                  'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                                                                                                                                                                                      '#dc2626','#fff1f2','#fca5a5'],
                ['see_comments',      'Lihat Komentar',   'Keputusan sesuai komentar',                         'M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z','#64748b','#f8fafc','#e2e8f0'],
            ] as [$val,$label,$desc,$icon,$color,$bg,$border])
            <button type="button"
                    wire:click="$set('recommendation','{{ $val }}')"
                    style="
                        padding:1rem;border-radius:.875rem;border:2px solid;cursor:pointer;text-align:left;
                        border-color:{{ $recommendation===$val ? $color : '#e2e8f0' }};
                        background:{{ $recommendation===$val ? $bg : '#fff' }};
                        transition:all .12s;
                    "
                    onmouseover="if('{{ $recommendation }}'!=='{{ $val }}'){this.style.background='{{ $bg }}';this.style.borderColor='{{ $color }}'}"
                    onmouseout="if('{{ $recommendation }}'!=='{{ $val }}'){this.style.background='#fff';this.style.borderColor='#e2e8f0'}">
                <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.375rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:{{ $color }};flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                    <span style="font-size:.9375rem;font-weight:800;color:{{ $recommendation===$val ? $color : '#1e293b' }};">{{ $label }}</span>
                </div>
                <p style="font-size:.75rem;color:#64748b;margin:0;line-height:1.4;">{{ $desc }}</p>
            </button>
            @endforeach
        </div>
        @error('recommendation') <p style="font-size:.8rem;color:#dc2626;padding:.5rem 1.25rem 1rem;">{{ $message }}</p> @enderror
    </div>

    {{-- Komentar untuk Penulis --}}
    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Komentar untuk Penulis</p>
            <p style="font-size:.75rem;color:#94a3b8;margin:.25rem 0 0;">Akan dibagikan ke penulis · Wajib diisi (min. 30 karakter)</p>
        </div>
        <div style="padding:1.25rem;">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.625rem;padding:.75rem 1rem;margin-bottom:.875rem;font-size:.8rem;color:#166534;line-height:1.6;">
                <strong>Tips:</strong> Awali dengan ringkasan umum, lanjutkan dengan catatan spesifik per bagian (pendahuluan, metode, hasil, diskusi, referensi). Gunakan penomoran agar penulis mudah merespons.
            </div>
            <textarea wire:model="commentsForAuthor" rows="12"
                placeholder="Contoh:&#10;&#10;Naskah ini membahas topik yang relevan. Namun, terdapat beberapa hal yang perlu diperbaiki:&#10;&#10;1. PENDAHULUAN: ...&#10;2. METODOLOGI: ...&#10;3. HASIL DAN DISKUSI: ..."
                style="width:100%;padding:.875rem 1rem;border:1.5px solid #e2e8f0;border-radius:.75rem;font-size:.9rem;line-height:1.8;resize:vertical;box-sizing:border-box;font-family:inherit;outline:none;"
                onfocus="this.style.borderColor='#059669'" onblur="this.style.borderColor='#e2e8f0'"></textarea>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:.5rem;">
                @error('commentsForAuthor') <p style="font-size:.8rem;color:#dc2626;margin:0;">{{ $message }}</p> @else <span></span> @enderror
                <span style="font-size:.8rem;color:{{ Str::length($commentsForAuthor)>=30 ? '#059669' : '#94a3b8' }};">{{ Str::length($commentsForAuthor) }} / min 30 karakter</span>
            </div>
        </div>
    </div>

    {{-- Komentar Konfidensial --}}
    <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:1rem;overflow:hidden;">
        <div style="padding:.875rem 1.25rem;border-bottom:1px solid #fde68a;display:flex;align-items:center;gap:.5rem;">
            <svg style="width:.875rem;height:.875rem;color:#b45309;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            <div>
                <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#92400e;margin:0;">Komentar Konfidensial untuk Editor</p>
                <p style="font-size:.75rem;color:#a16207;margin:.125rem 0 0;">Tidak akan ditampilkan ke penulis · Opsional</p>
            </div>
        </div>
        <div style="padding:1.25rem;">
            <textarea wire:model="commentsForEditor" rows="5"
                placeholder="Catatan untuk editor: kekhawatiran plagiarisme, konflik kepentingan, atau masalah etika lain..."
                style="width:100%;padding:.875rem 1rem;border:1.5px solid #fde68a;border-radius:.75rem;font-size:.9rem;line-height:1.8;background:#fff;resize:vertical;box-sizing:border-box;font-family:inherit;outline:none;"></textarea>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;padding:1.25rem;background:#fff;border-radius:1rem;border:1px solid #e2e8f0;">
        <button wire:click="prevStep" style="display:inline-flex;align-items:center;gap:.5rem;background:#f8fafc;color:#475569;font-size:.875rem;font-weight:700;padding:.625rem 1.25rem;border-radius:.75rem;border:1px solid #e2e8f0;cursor:pointer;">
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg> Kembali
        </button>
        <button wire:click="saveProgress" wire:loading.attr="disabled" style="display:inline-flex;align-items:center;gap:.5rem;background:#f8fafc;color:#475569;font-size:.875rem;font-weight:700;padding:.625rem 1.25rem;border-radius:.75rem;border:1px solid #e2e8f0;cursor:pointer;">
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
            <span wire:loading.remove wire:target="saveProgress">Simpan Draft</span>
            <span wire:loading wire:target="saveProgress">Menyimpan...</span>
        </button>
        <div style="margin-left:auto;display:flex;align-items:center;gap:.75rem;">
            <p style="font-size:.8rem;color:#94a3b8;margin:0;">
                @php $filled=count(array_filter($criteria)); @endphp
                {{ $filled }}/{{ count($criteria) }} kriteria · {{ $recommendation ? ($recMap[$recommendation][0]??'') : 'Pilih rekomendasi' }}
            </p>
            <button wire:click="submitReview"
                    wire:confirm="Kirim review sekarang? Pastikan semua komentar sudah lengkap."
                    wire:loading.attr="disabled"
                    style="display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#059669,#047857);color:#fff;font-size:.9375rem;font-weight:800;padding:.75rem 2rem;border-radius:.875rem;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(5,150,105,.3);">
                <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                <span wire:loading.remove wire:target="submitReview">{{ $assignment->status==='completed' ? 'Perbarui Review' : 'Kirim Review' }}</span>
                <span wire:loading wire:target="submitReview">Mengirim...</span>
            </button>
        </div>
    </div>

</div>{{-- /main col --}}

{{-- Right Sidebar --}}
<div style="display:flex;flex-direction:column;gap:1rem;position:sticky;top:4.5rem;">

    @if($recommendation)
    @php [$rl,$rc,$rbg,$rborder] = $recMap[$recommendation]; @endphp
    <div style="background:{{ $rbg }};border:2px solid {{ $rborder }};border-radius:1rem;padding:1.125rem;">
        <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#64748b;margin:0 0 .375rem;">Rekomendasi Dipilih</p>
        <p style="font-size:1.125rem;font-weight:800;color:{{ $rc }};margin:0;">{{ $rl }}</p>
    </div>
    @endif

    {{-- Progress Kriteria --}}
    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.75rem 1rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Progress Kriteria</p>
                @php $fc=count(array_filter($criteria)); $tc=count($criteria); @endphp
                <span style="font-size:.8rem;font-weight:700;color:{{ $fc===$tc ? '#059669' : '#94a3b8' }};">{{ $fc }}/{{ $tc }}</span>
            </div>
            <div style="background:#e2e8f0;border-radius:9999px;height:.375rem;margin-top:.5rem;overflow:hidden;">
                <div style="height:100%;background:#059669;border-radius:9999px;width:{{ $tc>0 ? round(($fc/$tc)*100) : 0 }}%;transition:width .3s;"></div>
            </div>
        </div>
        <div style="padding:.75rem 1rem;">
            @foreach($criteriaLabels as $key => [$name])
            @php [$rl2,$rc2,$rb2] = $ratingOptions[$criteria[$key]??''] ?? ['—','#cbd5e1','#f8fafc']; @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.375rem 0;border-bottom:1px solid #f8fafc;font-size:.8rem;">
                <span style="color:#64748b;">{{ $name }}</span>
                <span style="font-weight:700;color:{{ $criteria[$key] ? $rc2 : '#cbd5e1' }};background:{{ $criteria[$key] ? $rb2 : 'transparent' }};padding:.1rem .375rem;border-radius:.25rem;font-size:.75rem;">{{ $criteria[$key] ? $rl2 : '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Download --}}
    @if($sub->files->isNotEmpty())
    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.75rem 1rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Unduh Naskah</p>
        </div>
        <div style="padding:.625rem .875rem;display:flex;flex-direction:column;gap:.375rem;">
            @foreach($sub->files as $f)
            <a href="{{ asset('storage/'.$f->path) }}" target="_blank" style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;font-weight:600;color:#059669;text-decoration:none;padding:.5rem .625rem;border-radius:.5rem;background:#f0fdf4;border:1px solid #bbf7d0;">
                <svg style="width:.875rem;height:.875rem;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($f->original_file_name??'Naskah',24) }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Assignment Info --}}
    <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="padding:.75rem 1rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin:0;">Info Penugasan</p>
        </div>
        <div style="padding:.875rem 1rem;display:flex;flex-direction:column;gap:.625rem;font-size:.8rem;">
            @if($assignment->date_due)
            @php $ov=(bool)$assignment->date_due->isPast(); $dl=(int)now()->diffInDays($assignment->date_due,false); @endphp
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#64748b;">Deadline</span>
                <span style="font-weight:700;color:{{ $ov ? '#dc2626' : ($dl<=3 ? '#ea580c' : '#0f172a') }};">{{ $assignment->date_due->format('d M Y') }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#64748b;">Putaran</span>
                <span style="font-weight:600;color:#0f172a;">{{ $assignment->round ?? 1 }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#64748b;">Metode</span>
                <span style="font-weight:600;color:#0f172a;">{{ ['double_blind'=>'Double Blind','single_blind'=>'Single','triple_blind'=>'Triple','open'=>'Open'][$assignment->review_method]??'-' }}</span>
            </div>
        </div>
    </div>
</div>

</div>{{-- /grid --}}

{{-- ════════ STEP 4 — SELESAI ════════ --}}
@elseif($step === 4)
@php
    [$rl4,$rc4,$rbg4,$rborder4] = $recMap[$assignment->review->recommendation??''] ?? ['—','#94a3b8','#f8fafc','#e2e8f0'];
    $filled4 = array_filter($criteria);
    $totalCriteria = count($filled4);
    $excellentCount = count(array_filter($criteria, fn($v) => $v === 'excellent'));
    $goodCount      = count(array_filter($criteria, fn($v) => $v === 'good'));
@endphp

{{-- ── Hero Banner ─────────────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#022c22 0%,#064e3b 55%,#059669 100%);position:relative;overflow:hidden;">
    {{-- Decorative rings --}}
    <div style="position:absolute;top:-5rem;right:-5rem;width:22rem;height:22rem;border-radius:50%;border:2px solid rgba(255,255,255,.06);pointer-events:none;"></div>
    <div style="position:absolute;top:-2rem;right:-2rem;width:14rem;height:14rem;border-radius:50%;border:2px solid rgba(255,255,255,.08);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-4rem;left:10%;width:16rem;height:16rem;border-radius:50%;background:rgba(255,255,255,.03);pointer-events:none;"></div>

    <div style="padding:2.5rem 2rem 2.5rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;">
        {{-- Success icon --}}
        <div style="position:relative;flex-shrink:0;">
            <div style="width:6rem;height:6rem;border-radius:50%;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;border:2px solid rgba(255,255,255,.2);">
                <div style="width:4.5rem;height:4.5rem;border-radius:50%;background:rgba(52,211,153,.2);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:2.5rem;height:2.5rem;color:#34d399;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            {{-- Pulse ring --}}
            <div style="position:absolute;inset:-6px;border-radius:50%;border:2px solid rgba(52,211,153,.3);animation:ping 2s cubic-bezier(0,0,.2,1) infinite;"></div>
        </div>

        {{-- Text --}}
        <div style="flex:1;min-width:200px;">
            <p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#6ee7b7;margin:0 0 .375rem;">Review Selesai</p>
            <h2 style="font-size:1.75rem;font-weight:900;color:#fff;margin:0 0 .5rem;line-height:1.15;">Review Berhasil Dikirim!</h2>
            <p style="font-size:.9rem;color:rgba(255,255,255,.65);margin:0;line-height:1.6;max-width:36rem;">Terima kasih atas kontribusi Anda. Editor akan memproses dan menghubungi bila diperlukan klarifikasi.</p>
        </div>

        {{-- Recommendation badge --}}
        @if($assignment->review)
        <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:1rem;padding:1.125rem 1.5rem;text-align:center;flex-shrink:0;">
            <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.55);margin:0 0 .5rem;">Rekomendasi</p>
            <span style="font-size:1rem;font-weight:800;color:{{ $rc4 }};background:{{ $rbg4 }};border:1.5px solid {{ $rborder4 }};border-radius:.625rem;padding:.375rem 1rem;display:inline-block;">{{ $rl4 }}</span>
        </div>
        @endif
    </div>
</div>

{{-- ── Main Content ────────────────────────────────────────────────────────── --}}
<div style="padding:1.75rem 2rem;display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start;" class="step4-grid">

    {{-- LEFT — Summary --}}
    <div>

        {{-- Artikel yang diulas --}}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:1.375rem;margin-bottom:1.25rem;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin:0 0 .875rem;">Artikel yang Diulas</p>
            <p style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 .375rem;line-height:1.4;">
                {{ $blind ? '[Judul dirahasiakan — blind review]' : ($sub->title ?? '—') }}
            </p>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:.625rem;">
                <span style="font-size:.8rem;color:#64748b;display:flex;align-items:center;gap:.375rem;">
                    <svg style="width:.875rem;height:.875rem;color:#94a3b8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.966 8.966 0 00-6 2.292m0-14.25v14.25"/></svg>
                    {{ $sub->journal->name ?? '—' }}
                </span>
                <span style="font-size:.8rem;color:#64748b;display:flex;align-items:center;gap:.375rem;">
                    <svg style="width:.875rem;height:.875rem;color:#94a3b8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                    Putaran ke-{{ $assignment->round ?? 1 }}
                </span>
                @if($assignment->date_completed)
                <span style="font-size:.8rem;color:#64748b;display:flex;align-items:center;gap:.375rem;">
                    <svg style="width:.875rem;height:.875rem;color:#94a3b8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/></svg>
                    Selesai {{ $assignment->date_completed->translatedFormat('d F Y') }}
                </span>
                @endif
            </div>
        </div>

        {{-- Penilaian Kriteria --}}
        @if(!empty($filled4))
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:1.375rem;margin-bottom:1.25rem;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin:0;">Penilaian Kriteria</p>
                <span style="font-size:.75rem;font-weight:700;color:#059669;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.5rem;padding:.2rem .6rem;">{{ $totalCriteria }}/{{ count($criteriaLabels) }} diisi</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.625rem;">
                @foreach($criteriaLabels as $key => [$name])
                @php [$rL,$rC,$rBg2] = $ratingOptions[$criteria[$key]??'']??['Belum diisi','#94a3b8','#f8fafc']; $hasVal = !empty($criteria[$key]); @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;padding:.625rem .75rem;background:{{ $hasVal ? $rBg2 : '#f8fafc' }};border-radius:.625rem;border:1px solid {{ $hasVal ? 'transparent' : '#f1f5f9' }};">
                    <span style="font-size:.8rem;font-weight:600;color:#475569;">{{ $name }}</span>
                    <span style="font-size:.75rem;font-weight:800;color:{{ $rC }};">{{ $rL }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Komentar --}}
        @if($assignment->review && $assignment->review->comments_for_author)
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:1.375rem;margin-bottom:1.25rem;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin:0 0 .75rem;">Komentar untuk Penulis</p>
            <p style="font-size:.875rem;color:#334155;line-height:1.7;margin:0;white-space:pre-wrap;">{{ Str::limit($assignment->review->comments_for_author, 300) }}</p>
        </div>
        @endif

    </div>

    {{-- RIGHT — Actions --}}
    <div style="position:sticky;top:1.5rem;">

        {{-- Statistik singkat --}}
        @if($assignment->date_assigned && $assignment->date_completed)
        @php $reviewDays = $assignment->date_assigned->diffInDays($assignment->date_completed); @endphp
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:1.125rem;margin-bottom:1rem;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin:0 0 .875rem;">Statistik Review</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.625rem;">
                <div style="text-align:center;padding:.75rem;background:#f8fafc;border-radius:.625rem;">
                    <div style="font-size:1.5rem;font-weight:800;color:#0f172a;line-height:1;">{{ $reviewDays }}</div>
                    <div style="font-size:.7rem;font-weight:600;color:#94a3b8;margin-top:.25rem;">Hari</div>
                </div>
                <div style="text-align:center;padding:.75rem;background:#f0fdf4;border-radius:.625rem;">
                    <div style="font-size:1.5rem;font-weight:800;color:#059669;line-height:1;">{{ $excellentCount + $goodCount }}</div>
                    <div style="font-size:.7rem;font-weight:600;color:#94a3b8;margin-top:.25rem;">Nilai Positif</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Dokumen --}}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:1.125rem;margin-bottom:1rem;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin:0 0 .875rem;">Dokumen Anda</p>
            <div style="display:flex;flex-direction:column;gap:.625rem;">
                <a href="{{ route('reviewer.surat-tugas', $assignment) }}" target="_blank"
                   style="display:flex;align-items:center;gap:.75rem;padding:.875rem 1rem;background:#eff6ff;border:1px solid #bfdbfe;border-radius:.75rem;text-decoration:none;transition:opacity .15s;"
                   onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                    <div style="width:2.25rem;height:2.25rem;background:#2563eb;border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:1.125rem;height:1.125rem;color:#fff;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:#1e40af;">Surat Tugas</div>
                        <div style="font-size:.75rem;color:#3b82f6;">Buka & cetak dokumen</div>
                    </div>
                    <svg style="width:.875rem;height:.875rem;color:#93c5fd;margin-left:auto;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <a href="{{ route('reviewer.sertifikat', $assignment) }}" target="_blank"
                   style="display:flex;align-items:center;gap:.75rem;padding:.875rem 1rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.75rem;text-decoration:none;transition:opacity .15s;"
                   onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                    <div style="width:2.25rem;height:2.25rem;background:#059669;border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:1.125rem;height:1.125rem;color:#fff;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 013.138-3.138zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:#065f46;">Sertifikat Reviewer</div>
                        <div style="font-size:.75rem;color:#059669;">Unduh sertifikat resmi</div>
                    </div>
                    <svg style="width:.875rem;height:.875rem;color:#6ee7b7;margin-left:auto;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>

        {{-- Action buttons --}}
        <div style="display:flex;flex-direction:column;gap:.625rem;">
            <button wire:click="goStep(3)"
                    style="display:flex;align-items:center;justify-content:center;gap:.5rem;background:#fff;color:#475569;font-size:.875rem;font-weight:700;padding:.75rem 1.25rem;border-radius:.75rem;border:1px solid #e2e8f0;cursor:pointer;width:100%;">
                <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/></svg>
                Edit Review
            </button>
            <a href="{{ route('reviewer.dashboard') }}"
               style="display:flex;align-items:center;justify-content:center;gap:.5rem;background:linear-gradient(135deg,#059669,#047857);color:#fff;font-size:.875rem;font-weight:700;padding:.875rem 1.25rem;border-radius:.75rem;text-decoration:none;box-shadow:0 4px 14px rgba(5,150,105,.3);">
                <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Kembali ke Dashboard
            </a>
        </div>

    </div>

</div>
@endif

</div>{{-- /content --}}
</div>{{-- /root --}}

<style>
@media (max-width:900px) { .rg { grid-template-columns:1fr !important; } }
@media (max-width:860px) { .step4-grid { grid-template-columns:1fr !important; } }
@keyframes ping { 75%,100% { transform:scale(1.4); opacity:0; } }
</style>

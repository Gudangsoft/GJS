<div>
{{-- Header --}}
<div style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 100%);padding:2rem 1.5rem;">
    <div class="max-w-6xl mx-auto">
        <p class="text-sm font-semibold mb-1" style="color:#93c5fd;">Panel Editor</p>
        <h1 class="text-2xl font-black text-white">Dashboard Editor</h1>
    </div>
</div>

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Stat cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-bottom:2rem;">
        @foreach([
            ['tab'=>'pending',  'label'=>'Antrian Baru',    'color'=>'#2563eb','bg'=>'#eff6ff','n'=>$counts['pending']],
            ['tab'=>'review',   'label'=>'Dalam Review',    'color'=>'#d97706','bg'=>'#fffbeb','n'=>$counts['review']],
            ['tab'=>'revision', 'label'=>'Perlu Revisi',    'color'=>'#7c3aed','bg'=>'#faf5ff','n'=>$counts['revision']],
            ['tab'=>'decided',  'label'=>'Sudah Diputuskan','color'=>'#059669','bg'=>'#f0fdf4','n'=>$counts['decided']],
        ] as $s)
        <button wire:click="setTab('{{ $s['tab'] }}')"
                class="text-left rounded-2xl p-4 transition-all"
                style="background:{{ $tab === $s['tab'] ? $s['bg'] : '#fff' }};border:2px solid {{ $tab === $s['tab'] ? $s['color'] : '#e2e8f0' }};">
            <div class="font-black text-2xl leading-none mb-1" style="color:{{ $s['color'] }};">{{ $s['n'] }}</div>
            <div class="text-xs font-semibold" style="color:#64748b;">{{ $s['label'] }}</div>
        </button>
        @endforeach
    </div>

    {{-- Tab nav --}}
    <div class="flex gap-1 mb-6 p-1 rounded-xl" style="background:#f1f5f9;">
        @foreach([
            ['tab'=>'pending',  'label'=>'Antrian Baru'],
            ['tab'=>'review',   'label'=>'Dalam Review'],
            ['tab'=>'revision', 'label'=>'Perlu Revisi'],
            ['tab'=>'decided',  'label'=>'Sudah Diputuskan'],
        ] as $t)
        <button wire:click="setTab('{{ $t['tab'] }}')"
                class="flex-1 py-2 px-3 rounded-lg text-sm font-semibold transition-all"
                style="{{ $tab === $t['tab'] ? 'background:#fff;color:#1e40af;box-shadow:0 1px 3px rgba(0,0,0,.1)' : 'color:#64748b;background:transparent' }}">
            {{ $t['label'] }}
        </button>
        @endforeach
    </div>

    {{-- Submission list --}}
    @if($submissions->isEmpty())
    <div class="text-center py-16 rounded-2xl" style="border:2px dashed #e2e8f0;">
        <div style="font-size:2.5rem;margin-bottom:.75rem;">📭</div>
        <p class="font-semibold" style="color:#94a3b8;">Tidak ada naskah di kategori ini</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($submissions as $sub)
        @php
        $statusMap = [
            'submitted'         => ['label'=>'Submitted',      'color'=>'#2563eb','bg'=>'#eff6ff'],
            'queued'            => ['label'=>'Antrian',        'color'=>'#0891b2','bg'=>'#ecfeff'],
            'assigned'          => ['label'=>'Ditugaskan',     'color'=>'#d97706','bg'=>'#fffbeb'],
            'review'            => ['label'=>'Dalam Review',   'color'=>'#7c3aed','bg'=>'#faf5ff'],
            'revision_required' => ['label'=>'Revisi',         'color'=>'#dc2626','bg'=>'#fff1f2'],
            'resubmit'          => ['label'=>'Resubmit',       'color'=>'#dc2626','bg'=>'#fff1f2'],
            'accepted'          => ['label'=>'Diterima ✓',     'color'=>'#059669','bg'=>'#f0fdf4'],
            'declined'          => ['label'=>'Ditolak',        'color'=>'#94a3b8','bg'=>'#f8fafc'],
        ];
        $st = $statusMap[$sub->status] ?? ['label'=>$sub->status,'color'=>'#64748b','bg'=>'#f8fafc'];
        @endphp
        <div class="rounded-2xl p-5 transition-all" style="background:#fff;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05);">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full"
                              style="background:{{ $st['bg'] }};color:{{ $st['color'] }};">
                            {{ $st['label'] }}
                        </span>
                        @if($sub->journal)
                        <span class="text-xs font-semibold" style="color:#2563eb;">{{ $sub->journal->name_abbrev ?? $sub->journal->name }}</span>
                        @endif
                        @if($sub->section)
                        <span class="text-xs px-2 py-0.5 rounded" style="background:#f1f5f9;color:#475569;">{{ $sub->section->title }}</span>
                        @endif
                    </div>
                    <h3 class="font-bold leading-snug mb-1" style="color:#0f172a;font-size:.9375rem;">
                        {{ $sub->title }}
                    </h3>
                    <p class="text-sm" style="color:#64748b;">
                        {{ $sub->submitter?->first_name }} {{ $sub->submitter?->last_name }}
                        <span class="mx-1.5" style="color:#cbd5e1;">·</span>
                        {{ $sub->submitted_at?->format('d M Y') ?? $sub->created_at->format('d M Y') }}
                    </p>
                </div>
                <a href="{{ route('editor.submissions.review', $sub) }}"
                   class="shrink-0 inline-flex items-center gap-1.5 font-semibold rounded-xl text-sm transition-opacity hover:opacity-85"
                   style="padding:.6rem 1.25rem;background:#2563eb;color:#fff;text-decoration:none;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Kelola Review
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
</div>

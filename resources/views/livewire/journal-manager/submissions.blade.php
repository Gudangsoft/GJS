<div style="background:#f6f8fb;min-height:100vh;">

<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Submission</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola semua naskah yang masuk ke jurnal Anda.</p>
        </div>
    </div>
</div>

<div style="max-width:72rem;margin:0 auto;padding:1.5rem;">

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
@endif

@if($journal)

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="flex items-center gap-1 px-5 pt-4 pb-0 border-b border-slate-100 overflow-x-auto">
        @foreach([
            ['pending',  'Menunggu',    $counts['pending']  ?? 0],
            ['review',   'Dalam Review',$counts['review']   ?? 0],
            ['revision', 'Revisi',      $counts['revision'] ?? 0],
            ['decided',  'Diputuskan',  $counts['decided']  ?? 0],
            ['all',      'Semua',       $counts['all']      ?? 0],
        ] as [$key, $label, $count])
        <button wire:click="setTab('{{ $key }}')"
                class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 transition-all whitespace-nowrap
                    {{ $tab === $key ? 'border-blue-600 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            {{ $label }}
            @if($count > 0)
            <span class="text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center
                {{ $tab === $key ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                {{ $count }}
            </span>
            @endif
        </button>
        @endforeach
    </div>

    @if($submissions->isEmpty())
    <div class="text-center py-16 text-slate-400">
        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-sm font-medium">Tidak ada submission di kategori ini.</p>
    </div>
    @else
    @php
    $statusBadge = [
        'submitted'         => ['Dikirim',      '#2563eb','#eff6ff'],
        'queued'            => ['Antrian',       '#d97706','#fffbeb'],
        'assigned'          => ['Ditugaskan',    '#d97706','#fffbeb'],
        'review'            => ['Dalam Review',  '#7c3aed','#faf5ff'],
        'revision_required' => ['Perlu Revisi',  '#ea580c','#fff7ed'],
        'resubmit'          => ['Resubmit',      '#ea580c','#fff7ed'],
        'accepted'          => ['Diterima',      '#16a34a','#f0fdf4'],
        'declined'          => ['Ditolak',       '#dc2626','#fef2f2'],
        'copyediting'       => ['Copy Editing',  '#0891b2','#ecfeff'],
        'production'        => ['Produksi',      '#0891b2','#ecfeff'],
        'scheduled'         => ['Terjadwal',     '#16a34a','#f0fdf4'],
        'published'         => ['Diterbitkan',   '#15803d','#f0fdf4'],
    ];
    @endphp
    <div class="divide-y divide-slate-50">
        @foreach($submissions as $sub)
        @php [$bLabel,$bColor,$bBg] = $statusBadge[$sub->status] ?? [$sub->status,'#64748b','#f8fafc']; @endphp
        <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 transition-colors">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                          style="background:{{ $bBg }};color:{{ $bColor }};">{{ $bLabel }}</span>
                    @if($sub->section)
                    <span class="text-xs text-slate-400">{{ $sub->section->title }}</span>
                    @endif
                </div>
                <p class="font-semibold text-slate-900 text-sm truncate">{{ $sub->title ?: '(Tanpa Judul)' }}</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    #{{ $sub->id }}
                    @if($sub->submitter) · {{ $sub->submitter->first_name }} {{ $sub->submitter->last_name }}@endif
                    @if($sub->submitted_at) · {{ $sub->submitted_at->format('d M Y') }}@endif
                </p>
            </div>
            <a href="{{ route('editor.submissions.review', $sub->id) }}"
               class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg text-white transition-colors shrink-0"
               style="background:#1d4ed8;">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                Kelola
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>

@else
<div class="text-center py-20 text-slate-400">
    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
    <p class="font-semibold">Anda belum ditugaskan ke jurnal manapun.</p>
</div>
@endif

</div>
</div>

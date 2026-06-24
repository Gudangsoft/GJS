<div style="background:#f6f8fb;min-height:100vh;">

{{-- ══ PAGE HEADER ════════════════════════════════════════════════════ --}}
<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Dashboard</h1>
            <p class="text-sm text-slate-500 mt-0.5">Selamat datang, {{ auth()->user()->first_name }} — ringkasan aktivitas jurnal Anda.</p>
        </div>
        <a href="{{ route('manager.submissions') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Submission Baru
        </a>
    </div>
</div>

<div style="max-width:72rem;margin:0 auto;padding:1.5rem;">

{{-- ══ JOURNAL SELECTOR (jika mengelola lebih dari 1 jurnal) ══════════ --}}
@if($journals->count() > 1)
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Pilih Jurnal</p>
    <div class="flex flex-wrap gap-2">
        @foreach($journals as $j)
        <button wire:click="setJournal('{{ $j->slug }}')"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all border
                    {{ $activeJournalSlug === $j->slug
                        ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                        : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600' }}">
            {{ $j->name_abbrev ?: $j->name }}
        </button>
        @endforeach
    </div>
</div>
@endif

@if($journal)

{{-- ══ JOURNAL INFO STRIP ══════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        @if($journal->logo)
        <img src="{{ Storage::disk('public')->url($journal->logo) }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200" alt="">
        @else
        <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black text-lg">
            {{ strtoupper(substr($journal->name_abbrev ?: $journal->name, 0, 2)) }}
        </div>
        @endif
        <div>
            <p class="font-bold text-slate-900">{{ $journal->name }}</p>
            <div class="flex items-center gap-3 mt-0.5">
                @if($journal->issn_print)
                <span class="text-xs text-slate-400">ISSN {{ $journal->issn_print }}</span>
                @endif
                @if($journal->sinta_level)
                <span class="text-xs font-bold px-2 py-0.5 rounded-full text-white"
                      style="background:{{ match($journal->sinta_level){ 'S1'=>'#b91c1c','S2'=>'#15803d','S3'=>'#1d4ed8',default=>'#64748b' } }}">
                    {{ $journal->sinta_level }}
                </span>
                @endif
                <span class="text-xs px-2 py-0.5 rounded-full {{ $journal->enabled ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $journal->enabled ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('journals.home', $journal->slug) }}" target="_blank"
           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Lihat Jurnal
        </a>
        <a href="{{ route('manager.settings') }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
    </div>
</div>

{{-- ══ STAT CARDS ══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $statCards = [
        ['label'=>'Total Submission','value'=>$counts['total'] ?? 0,'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','color'=>'#1d4ed8','bg'=>'#eff6ff'],
        ['label'=>'Menunggu Review','value'=>$counts['pending'] ?? 0,'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'#d97706','bg'=>'#fffbeb'],
        ['label'=>'Dalam Review','value'=>$counts['review'] ?? 0,'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4','color'=>'#7c3aed','bg'=>'#faf5ff'],
        ['label'=>'Artikel Terbit','value'=>$journalStats['articles'] ?? 0,'icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','color'=>'#16a34a','bg'=>'#f0fdf4'],
    ];
    @endphp
    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:{{ $card['bg'] }};">
            <svg class="w-5 h-5" style="color:{{ $card['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-black" style="color:{{ $card['color'] }};">{{ $card['value'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">{{ $card['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ══ AKSI CEPAT ══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    @php
    $quickActions = [
        ['label'=>'Buat Terbitan','desc'=>'Issue baru','icon'=>'M12 4v16m8-8H4','color'=>'#1d4ed8','bg'=>'#eff6ff','url'=>route('manager.issues')],
        ['label'=>'Plugin Sidebar','desc'=>'Kelola blok','icon'=>'M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z','color'=>'#7c3aed','bg'=>'#faf5ff','url'=>route('manager.plugins')],
        ['label'=>'Pengumuman','desc'=>'Tambah baru','icon'=>'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z','color'=>'#0891b2','bg'=>'#ecfeff','url'=>route('manager.announcements')],
        ['label'=>'Profil Jurnal','desc'=>'Edit info','icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z','color'=>'#059669','bg'=>'#f0fdf4','url'=>route('manager.settings')],
    ];
    @endphp
    @foreach($quickActions as $action)
    <a href="{{ $action['url'] }}"
       class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 hover:shadow-md hover:border-blue-200 transition-all group">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 transition-colors"
             style="background:{{ $action['bg'] }};">
            <svg class="w-4.5 h-4.5" style="color:{{ $action['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-slate-800 group-hover:text-blue-700 transition-colors">{{ $action['label'] }}</p>
            <p class="text-xs text-slate-400">{{ $action['desc'] }}</p>
        </div>
    </a>
    @endforeach
</div>

{{-- ══ SUBMISSION TABLE WITH TABS ══════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    {{-- Tab header --}}
    <div class="flex items-center justify-between px-5 pt-4 pb-0 border-b border-slate-100">
        <div class="flex gap-1">
            @foreach([
                ['pending',  'Menunggu',     $counts['pending']  ?? 0, '#d97706'],
                ['review',   'Review',        $counts['review']   ?? 0, '#7c3aed'],
                ['revision', 'Revisi',        $counts['revision'] ?? 0, '#ea580c'],
                ['decided',  'Diputuskan',    $counts['decided']  ?? 0, '#16a34a'],
            ] as [$key, $label, $count, $color])
            <button wire:click="setTab('{{ $key }}')"
                    class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 transition-all
                        {{ $tab === $key ? 'border-blue-600 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                {{ $label }}
                @if($count > 0)
                <span class="text-xs font-black px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center
                    {{ $tab === $key ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                    {{ $count }}
                </span>
                @endif
            </button>
            @endforeach
        </div>
    </div>

    {{-- Submission list --}}
    @if($submissions->isEmpty())
    <div class="text-center py-12 text-slate-400">
        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-sm font-medium">Tidak ada submission di kategori ini.</p>
    </div>
    @else
    <div class="divide-y divide-slate-50">
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
                <p class="font-semibold text-slate-900 text-sm truncate">{{ $sub->title }}</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    #{{ $sub->id }}
                    @if($sub->submitter) · {{ $sub->submitter->first_name }} {{ $sub->submitter->last_name }}@endif
                    @if($sub->submitted_at) · {{ $sub->submitted_at->format('d M Y') }}@endif
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('manager.submissions') }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg text-white transition-colors"
                   style="background:#1d4ed8;">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    Kelola
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@else
{{-- No journals assigned --}}
<div class="text-center py-20 text-slate-400">
    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
    <p class="font-semibold">Anda belum ditugaskan ke jurnal manapun.</p>
    <p class="text-sm mt-1">Hubungi administrator untuk mendapatkan akses.</p>
</div>
@endif

</div>{{-- end max-w --}}
</div>

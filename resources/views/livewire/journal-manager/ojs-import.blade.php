<div class="max-w-4xl mx-auto space-y-6 py-2">

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-800">Import / Export Jurnal</h1>
        <p class="text-sm text-slate-500 mt-1">
            Jurnal: <span class="font-semibold text-slate-700">{{ $journal?->name ?? '(belum dipilih)' }}</span>
        </p>
    </div>
    @if($done)
    <button type="button" wire:click="resetResult"
            class="text-xs text-slate-500 hover:text-slate-700 border border-slate-200 rounded-lg px-3 py-1.5 hover:bg-slate-50 transition-colors">
        ← Import Baru
    </button>
    @endif
</div>

{{-- ── Mode toggle: Import | Export ────────────────────────────────────────── --}}
<div class="flex gap-1 p-1 bg-slate-100 rounded-xl w-fit">
    <button type="button" wire:click="$set('mode','import')"
            class="px-5 py-2 text-sm font-semibold rounded-lg transition-all
                {{ $mode === 'import' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
        <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Import
        </span>
    </button>
    <button type="button" wire:click="$set('mode','export')"
            class="px-5 py-2 text-sm font-semibold rounded-lg transition-all
                {{ $mode === 'export' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
        <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 7.5m0 0L7.5 12m4.5-4.5V21"/>
            </svg>
            Export
        </span>
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- ── EXPORT MODE ─────────────────────────────────────────────────────────── --}}
@if($mode === 'export')

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-bold text-slate-800">Plugin Import/Export</h2>
        <p class="text-xs text-slate-500 mt-0.5">Pilih plugin untuk mengekspor data jurnal ke berbagai format standar</p>
    </div>

    {{-- Plugin list --}}
    <div class="divide-y divide-slate-100">
        @foreach($exportPlugins as $plugin)
        <div class="flex items-start gap-4 px-6 py-4 hover:bg-slate-50/60 transition-colors"
             wire:key="ep-{{ $plugin['key'] }}">

            {{-- Icon --}}
            <div style="width:2.5rem;height:2.5rem;border-radius:0.75rem;background:{{ $plugin['bg'] }};flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                <svg style="width:1.25rem;height:1.25rem;color:{{ $plugin['color'] }};" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $plugin['icon'] }}"/>
                </svg>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="#" wire:click.prevent="$set('exportPlugin','{{ $plugin['key'] }}')"
                       class="text-sm font-semibold text-blue-600 hover:underline">{{ $plugin['name'] }}</a>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $plugin['mode'] === 'both'
                            ? 'bg-purple-50 text-purple-600 border border-purple-200'
                            : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">
                        {{ $plugin['mode'] === 'both' ? 'Import & Export' : 'Export' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">{{ $plugin['desc'] }}</p>

                {{-- Expand if selected --}}
                @if($exportPlugin === $plugin['key'])
                <div class="mt-3 p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-3">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Filter Edisi <span class="text-slate-400 font-normal">(opsional)</span></label>
                            <select wire:model.live="exportIssueId"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">Semua edisi terpublikasi</option>
                                @foreach($issueOptions as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="button" wire:click="exportData"
                                    wire:loading.attr="disabled"
                                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;background:{{ $plugin['color'] }};color:#fff;font-size:0.8125rem;font-weight:700;border:none;cursor:pointer;white-space:nowrap;">
                                <svg wire:loading.remove wire:target="exportData" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 7.5m0 0L7.5 12m4.5-4.5V21"/>
                                </svg>
                                <svg wire:loading wire:target="exportData" style="width:1rem;height:1rem;animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.25;"/>
                                    <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" style="opacity:.75;"/>
                                </svg>
                                <span wire:loading.remove wire:target="exportData">Ekspor XML</span>
                                <span wire:loading wire:target="exportData">Mengekspor...</span>
                            </button>
                            <button type="button" wire:click="$set('exportPlugin','')"
                                    style="padding:8px 10px;border-radius:8px;border:1px solid #e2e8f0;background:white;color:#94a3b8;cursor:pointer;">
                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 flex items-center gap-1">
                        <svg style="width:0.875rem;height:0.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                        File XML akan diunduh ke komputer Anda. Upload file ke platform tujuan (CrossRef, DOAJ, PubMed, dll).
                    </p>
                </div>
                @endif
            </div>

        </div>
        @endforeach
    </div>
</div>

{{-- Info box --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
        <p class="text-xs font-bold text-blue-800 mb-1">CrossRef</p>
        <p class="text-xs text-blue-700">Upload XML ke <span class="font-mono">submission.crossref.org</span> untuk mendaftarkan DOI artikel.</p>
    </div>
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4">
        <p class="text-xs font-bold text-emerald-800 mb-1">DOAJ</p>
        <p class="text-xs text-emerald-700">Upload XML ke panel DOAJ untuk memperbarui metadata artikel di direktori.</p>
    </div>
    <div class="bg-violet-50 border border-violet-200 rounded-2xl p-4">
        <p class="text-xs font-bold text-violet-800 mb-1">PubMed / DataCite</p>
        <p class="text-xs text-violet-700">Gunakan antarmuka masing-masing platform untuk mengunggah file XML yang diunduh.</p>
    </div>
</div>

@endif {{-- end export mode --}}

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- ── IMPORT MODE ─────────────────────────────────────────────────────────── --}}
@if($mode === 'import')

{{-- ── Method Tabs ──────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    {{-- Tab bar --}}
    <div class="flex border-b border-slate-100">
        @php
        $tabs = [
            'oai'      => ['label' => 'OAI-PMH', 'sub' => 'Universal · Tanpa API key', 'icon' => 'M5 12h14M12 5l7 7-7 7'],
            'crossref' => ['label' => 'CrossRef', 'sub' => 'Cari via ISSN · Metadata kaya', 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
            'rest'     => ['label' => 'OJS REST API', 'sub' => 'OJS 3.x · Data lengkap', 'icon' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ];
        @endphp
        @foreach($tabs as $key => $tab)
        <button type="button" wire:click="$set('method', '{{ $key }}')"
                class="flex-1 flex flex-col items-center gap-0.5 px-4 py-4 text-center transition-colors border-b-2
                    {{ $method === $key
                        ? 'border-blue-600 bg-blue-50/60 text-blue-700'
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}"/>
            </svg>
            <span class="text-sm font-semibold leading-tight">{{ $tab['label'] }}</span>
            <span class="text-[11px] leading-tight opacity-70 hidden sm:block">{{ $tab['sub'] }}</span>
        </button>
        @endforeach
    </div>

    {{-- ── OAI-PMH Tab ──────────────────────────────────────────────────────── --}}
    @if($method === 'oai')
    <div class="p-6 space-y-5">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
            <strong>OAI-PMH</strong> adalah protokol standar yang didukung semua OJS.
            Tidak memerlukan API key. URL OAI biasanya di:
            <code class="bg-blue-100 rounded px-1 py-0.5 text-xs font-mono ml-1">https://jurnal.example.com/index.php/{slug}/oai</code>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    URL Endpoint OAI <span class="text-red-500">*</span>
                </label>
                <input wire:model.live="oaiUrl" type="url"
                       placeholder="https://jurnal.example.com/index.php/jiki/oai"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
                @error('oaiUrl')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Set Spec <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <input wire:model.live="oaiSet" type="text"
                       placeholder="Kosongkan = ambil semua. Contoh: jiki:ART"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <p class="text-xs text-slate-400 mt-1">
                    Diisi jika OJS multi-jurnal dan hanya ingin mengambil satu jurnal atau seksi tertentu.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- ── CrossRef Tab ─────────────────────────────────────────────────────── --}}
    @if($method === 'crossref')
    <div class="p-6 space-y-5">
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-800">
            <strong>CrossRef</strong> menyimpan metadata artikel berdasarkan DOI. Masukkan ISSN jurnal
            untuk mengambil semua artikel yang terdaftar. Tidak perlu akun CrossRef.
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    ISSN Cetak <span class="text-red-500">*</span>
                </label>
                <input wire:model.live="crossrefIssn" type="text"
                       placeholder="2301-9271"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                @error('crossrefIssn')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    ISSN Online <span class="text-slate-400 font-normal">(jika ada)</span>
                </label>
                <input wire:model.live="crossrefIssnOnline" type="text"
                       placeholder="2685-1016"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <p class="text-xs text-slate-400 mt-1">Jika diisi, ISSN online yang digunakan untuk pencarian.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ── REST API Tab ─────────────────────────────────────────────────────── --}}
    @if($method === 'rest')
    <div class="p-6 space-y-5">
        <div class="bg-violet-50 border border-violet-200 rounded-xl p-4 text-sm text-violet-800">
            <strong>OJS REST API</strong> tersedia di OJS 3.x. Memberikan data paling lengkap termasuk
            file galley, metadata seksi, dan data edisi. Memerlukan URL API yang aktif.
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    URL Jurnal OJS <span class="text-red-500">*</span>
                </label>
                <input wire:model.live="ojsUrl" type="url"
                       placeholder="https://jurnal.example.com/index.php/jiki"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-400">
                @error('ojsUrl')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-slate-400 mt-1">
                    URL lengkap termasuk path konteks jurnal. Sistem akan mengakses <code class="bg-slate-100 rounded px-1 font-mono">/api/v1/...</code> dari URL ini.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    API Key <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <input wire:model.live="apiKey" type="password"
                       placeholder="Kosongkan jika OJS bersifat publik"
                       class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-400">
                <p class="text-xs text-slate-400 mt-1">Di OJS: Profil → API Key.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Yang akan diimpor</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['all' => 'Semua (Edisi + Artikel)', 'issues' => 'Edisi saja', 'articles' => 'Artikel saja'] as $val => $label)
                    <label class="flex items-center gap-2 cursor-pointer px-4 py-2 rounded-xl border text-sm font-medium transition-all
                        {{ $importWhat === $val
                            ? 'border-violet-500 bg-violet-50 text-violet-700'
                            : 'border-slate-200 bg-white text-slate-600 hover:border-violet-300' }}">
                        <input type="radio" wire:model.live="importWhat" value="{{ $val }}" class="sr-only">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Action bar (shared) ─────────────────────────────────────────────── --}}
    <div class="px-6 pb-6 space-y-3">
    {{-- SSL toggle --}}
    <div class="flex items-center gap-2">
        <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-slate-500">
            <input type="checkbox" wire:model.live="sslVerify"
                   class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-400">
            Verifikasi SSL
        </label>
        @if(!$sslVerify)
        <span class="text-[11px] text-amber-600 bg-amber-50 border border-amber-200 rounded px-2 py-0.5">
            SSL dinonaktifkan — hanya untuk debug/dev
        </span>
        @endif
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        {{-- Test connection --}}
        <button type="button" wire:click="testConnection" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-colors border border-slate-200 disabled:opacity-60">
            <svg wire:loading.remove wire:target="testConnection" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <svg wire:loading wire:target="testConnection" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Tes Koneksi
        </button>

        {{-- Connection result badge --}}
        @if($connOk !== null)
        <span class="flex items-center gap-1.5 text-sm font-medium {{ $connOk ? 'text-green-600' : 'text-red-600' }}">
            @if($connOk)
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            @else
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            @endif
            {{ $connMsg }}
        </span>
        @endif

        <div class="flex-1"></div>

        {{-- Import button --}}
        <button type="button" wire:click="startImport"
                wire:loading.attr="disabled"
                wire:confirm="Mulai import sekarang? Artikel yang sudah ada akan diperbarui (tidak duplikat)."
                class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-60">
            <svg wire:loading.remove wire:target="startImport" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <svg wire:loading wire:target="startImport" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <span wire:loading.remove wire:target="startImport">Mulai Import</span>
            <span wire:loading wire:target="startImport">Mengimpor...</span>
        </button>
    </div>
    </div>{{-- end action bar --}}

    {{-- Progress indicator --}}
    <div wire:loading wire:target="startImport"
         class="mx-6 mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200">
        <div class="flex items-center gap-3">
            <svg class="animate-spin w-5 h-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-blue-800">Sedang mengimpor data...</p>
                <p class="text-xs text-blue-600 mt-0.5">Jangan tutup halaman ini. Proses berjalan di server dan bisa memakan beberapa menit.</p>
            </div>
        </div>
    </div>
</div>

{{-- ── Hasil Import ──────────────────────────────────────────────────────────── --}}
@if($done)
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-slate-800">Hasil Import</h2>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        @php
        $cards = [
            ['label' => 'Edisi Baru',     'val' => $importStats['issuesCreated'],   'color' => 'blue'],
            ['label' => 'Seksi Baru',     'val' => $importStats['sectionsCreated'], 'color' => 'purple'],
            ['label' => 'Artikel Baru',   'val' => $importStats['articlesCreated'], 'color' => 'green'],
            ['label' => 'Diperbarui',     'val' => $importStats['articlesUpdated'], 'color' => 'orange'],
            ['label' => 'Error',          'val' => $importStats['errors'],          'color' => 'red'],
        ];
        $colorMap = [
            'blue'   => 'bg-blue-50 text-blue-700 border-blue-200',
            'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
            'green'  => 'bg-green-50 text-green-700 border-green-200',
            'orange' => 'bg-orange-50 text-orange-700 border-orange-200',
            'red'    => 'bg-red-50 text-red-700 border-red-200',
        ];
        @endphp
        @foreach($cards as $card)
        <div class="rounded-xl border p-4 text-center {{ $colorMap[$card['color']] }}">
            <div class="text-3xl font-black">{{ $card['val'] }}</div>
            <div class="text-xs font-semibold mt-1 opacity-80">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Log detail --}}
    @if(!empty($importLog))
    <div x-data="{ open: false }">
        <button type="button" @click="open = !open"
                class="text-xs text-blue-600 hover:underline flex items-center gap-1">
            <svg class="w-3 h-3" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="transition: transform 0.2s">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            <span x-text="open ? 'Sembunyikan log' : 'Tampilkan log detail ({{ count($importLog) }} entri)'"></span>
        </button>
        <div x-show="open" x-cloak x-transition
             class="mt-3 max-h-72 overflow-y-auto rounded-xl bg-slate-900 p-4 text-xs font-mono space-y-0.5">
            @foreach($importLog as $entry)
            <div class="flex gap-2 {{ $entry['level'] === 'error' ? 'text-red-400' : ($entry['level'] === 'warn' ? 'text-yellow-400' : 'text-slate-300') }}">
                <span class="text-slate-600 shrink-0">{{ $entry['time'] }}</span>
                <span>{{ $entry['msg'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

{{-- ── Panduan & Tips ────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Cara menemukan URL OAI --}}
    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5 space-y-3">
        <h3 class="text-sm font-bold text-slate-700">Cara menemukan URL OAI-PMH</h3>
        <div class="space-y-2 text-xs text-slate-600">
            <p><span class="font-semibold">OJS multi-jurnal:</span></p>
            <code class="block bg-white border border-slate-200 rounded-lg px-3 py-2 font-mono text-slate-800 overflow-x-auto">https://jurnal.example.com/index.php/{slug}/oai</code>
            <p class="mt-2"><span class="font-semibold">OJS single-jurnal:</span></p>
            <code class="block bg-white border border-slate-200 rounded-lg px-3 py-2 font-mono text-slate-800 overflow-x-auto">https://jurnal.example.com/oai</code>
            <p class="mt-2 text-slate-500">
                Atau buka halaman jurnal OJS → <em>About → Submissions</em> → cari link OAI.
            </p>
        </div>
    </div>

    {{-- CLI artisan --}}
    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5 space-y-3">
        <h3 class="text-sm font-bold text-slate-700">Alternatif via Terminal (Artisan)</h3>
        <div class="bg-slate-900 rounded-xl p-4 text-xs font-mono space-y-1.5 overflow-x-auto">
            <div class="text-slate-400"># Import via OAI-PMH</div>
            <div class="text-green-400">php artisan ojs:import --url=<span class="text-yellow-400">URL_OAI</span> --journal=<span class="text-yellow-400">ID</span></div>
            <div class="mt-2 text-slate-400"># Tes koneksi saja</div>
            <div class="text-green-400">php artisan ojs:import --url=URL --journal=ID --test</div>
            <div class="mt-2 text-slate-400"># Hanya artikel</div>
            <div class="text-green-400">php artisan ojs:import --url=URL --journal=ID --only=articles</div>
        </div>
    </div>
</div>

@endif {{-- end import mode --}}

</div>

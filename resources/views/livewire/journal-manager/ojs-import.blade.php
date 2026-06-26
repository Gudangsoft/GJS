<div class="max-w-4xl mx-auto space-y-6 py-2">

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-800">Import Jurnal</h1>
        <p class="text-sm text-slate-500 mt-1">
            Import artikel &amp; edisi ke jurnal
            <span class="font-semibold text-slate-700">{{ $journal?->name ?? '(belum dipilih)' }}</span>
            dari sumber eksternal.
        </p>
    </div>
    @if($done)
    <button type="button" wire:click="resetResult"
            class="text-xs text-slate-500 hover:text-slate-700 border border-slate-200 rounded-lg px-3 py-1.5 hover:bg-slate-50 transition-colors">
        ← Import Baru
    </button>
    @endif
</div>

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

</div>

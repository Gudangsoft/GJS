<div>
<div class="max-w-3xl mx-auto space-y-6 py-2">

{{-- Header --}}
<div>
    <h1 class="text-xl font-bold text-slate-800">Import dari OJS</h1>
    <p class="text-sm text-slate-500 mt-1">
        Tarik data edisi &amp; artikel dari OJS 3.x via REST API ke jurnal
        <span class="font-semibold text-slate-700">{{ $journal?->name ?? '(belum dipilih)' }}</span>.
    </p>
</div>

{{-- Konfigurasi --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Konfigurasi OJS</h2>

    <div class="space-y-4">
        {{-- URL --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">URL Jurnal OJS <span class="text-red-500">*</span></label>
            <input wire:model.live="ojsUrl" type="url"
                   placeholder="https://jurnal.example.com/index.php/nama-jurnal"
                   class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <p class="text-xs text-slate-400 mt-1">URL lengkap termasuk path konteks jurnal (jika multi-jurnal OJS).</p>
        </div>

        {{-- API Key --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">API Key <span class="text-slate-400 font-normal">(opsional)</span></label>
            <input wire:model.live="apiKey" type="password"
                   placeholder="Kosongkan jika OJS bersifat publik"
                   class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <p class="text-xs text-slate-400 mt-1">
                Di OJS: Profil Pengguna → API Key.
                Diperlukan jika jurnal tidak sepenuhnya publik.
            </p>
        </div>

        {{-- Apa yang diimpor --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Yang akan diimpor</label>
            <div class="flex flex-wrap gap-3">
                @foreach(['all' => 'Semua (Edisi + Artikel)', 'issues' => 'Edisi saja', 'articles' => 'Artikel saja'] as $val => $label)
                <label class="flex items-center gap-2 cursor-pointer px-4 py-2 rounded-xl border text-sm font-medium transition-all
                    {{ $importWhat === $val ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-600 hover:border-blue-300' }}">
                    <input type="radio" wire:model.live="importWhat" value="{{ $val }}" class="sr-only">
                    {{ $label }}
                </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tombol tes koneksi --}}
    <div class="flex items-center gap-3 pt-2">
        <button type="button" wire:click="testConnection" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-colors border border-slate-200 disabled:opacity-60">
            <span wire:loading wire:target="testConnection">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </span>
            <svg wire:loading.remove wire:target="testConnection" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Tes Koneksi
        </button>

        @if($connOk !== null)
        <span class="flex items-center gap-1.5 text-sm font-medium {{ $connOk ? 'text-green-600' : 'text-red-600' }}">
            @if($connOk)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            @endif
            {{ $connMsg }}
        </span>
        @endif
    </div>
</div>

{{-- Tombol Import --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <div class="flex items-start gap-4">
        <div class="flex-1">
            <h2 class="text-sm font-bold text-slate-700">Mulai Import</h2>
            <p class="text-xs text-slate-500 mt-1">
                Artikel yang sudah ada (berdasarkan DOI atau OJS ID) akan <strong>diperbarui</strong>, bukan diduplikasi.
                Proses bisa memakan waktu beberapa menit tergantung jumlah artikel.
            </p>
        </div>
        <button type="button" wire:click="startImport"
                wire:loading.attr="disabled"
                wire:confirm="Mulai import dari OJS sekarang?"
                class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-60">
            <span wire:loading wire:target="startImport">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                Mengimpor...
            </span>
            <span wire:loading.remove wire:target="startImport">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Mulai Import
            </span>
        </button>
    </div>

    {{-- Progress indicator --}}
    <div wire:loading wire:target="startImport" class="mt-4 p-4 rounded-xl bg-blue-50 border border-blue-200">
        <div class="flex items-center gap-3">
            <svg class="animate-spin w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            <div>
                <p class="text-sm font-semibold text-blue-800">Sedang mengimpor data dari OJS...</p>
                <p class="text-xs text-blue-600 mt-0.5">Jangan tutup halaman ini. Proses berjalan di server.</p>
            </div>
        </div>
    </div>
</div>

{{-- Hasil Import --}}
@if($done && !empty($importStats))
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Hasil Import</h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @php
        $statCards = [
            ['label'=>'Seksi Baru',    'val'=>$importStats['sectionsCreated'],  'color'=>'purple'],
            ['label'=>'Edisi Baru',    'val'=>$importStats['issuesCreated'],    'color'=>'blue'],
            ['label'=>'Edisi Update',  'val'=>$importStats['issuesUpdated'],    'color'=>'slate'],
            ['label'=>'Artikel Baru',  'val'=>$importStats['articlesCreated'],  'color'=>'green'],
            ['label'=>'Artikel Update','val'=>$importStats['articlesUpdated'],  'color'=>'orange'],
            ['label'=>'Error',         'val'=>$importStats['errors'],           'color'=>'red'],
        ];
        @endphp
        @foreach($statCards as $card)
        @php
        $colors = [
            'purple'=>'bg-purple-50 text-purple-700 border-purple-200',
            'blue'  =>'bg-blue-50 text-blue-700 border-blue-200',
            'slate' =>'bg-slate-50 text-slate-700 border-slate-200',
            'green' =>'bg-green-50 text-green-700 border-green-200',
            'orange'=>'bg-orange-50 text-orange-700 border-orange-200',
            'red'   =>'bg-red-50 text-red-700 border-red-200',
        ];
        @endphp
        <div class="rounded-xl border p-4 {{ $colors[$card['color']] }}">
            <div class="text-2xl font-black">{{ $card['val'] }}</div>
            <div class="text-xs font-semibold mt-0.5">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Log detail --}}
    @if(!empty($importLog))
    <div>
        <button type="button" x-data="{ open: false }" @click="open = !open"
                class="text-xs text-blue-600 hover:underline">
            <span x-text="open ? 'Sembunyikan log' : 'Tampilkan log detail (' . {{ count($importLog) }} . ' entri)'"></span>
        </button>
        <div x-data="{ open: false }" x-show="open" x-cloak class="mt-2 max-h-64 overflow-y-auto rounded-xl bg-slate-900 p-4 text-xs font-mono space-y-0.5">
            @foreach($importLog as $entry)
            <div class="{{ $entry['level'] === 'error' ? 'text-red-400' : ($entry['level'] === 'warn' ? 'text-yellow-400' : 'text-slate-300') }}">
                <span class="text-slate-500">{{ $entry['time'] }}</span>
                {{ $entry['msg'] }}
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

{{-- Panduan CLI --}}
<div class="bg-slate-50 rounded-2xl border border-slate-200 p-5 space-y-3">
    <h2 class="text-sm font-bold text-slate-700">Alternatif via Terminal (Artisan)</h2>
    <p class="text-xs text-slate-500">Untuk dataset besar atau otomasi, gunakan perintah berikut di terminal server:</p>
    <div class="bg-slate-900 rounded-xl p-4 text-xs font-mono text-green-400 space-y-1 overflow-x-auto">
        <div class="text-slate-400"># Import semua (seksi + edisi + artikel)</div>
        <div>php artisan ojs:import --url="{{ $ojsUrl ?: 'https://jurnal.example.com/index.php/jiki' }}" --journal={{ $journal?->id ?? 'ID' }}</div>
        <div class="mt-2 text-slate-400"># Tes koneksi saja</div>
        <div>php artisan ojs:import --url=URL --journal=ID --test</div>
        <div class="mt-2 text-slate-400"># Import dengan API key</div>
        <div>php artisan ojs:import --url=URL --journal=ID --api-key=APIKEY</div>
        <div class="mt-2 text-slate-400"># Import hanya artikel</div>
        <div>php artisan ojs:import --url=URL --journal=ID --only=articles</div>
    </div>
</div>

</div>
</div>

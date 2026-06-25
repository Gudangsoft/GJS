<div>

{{-- PAGE HEADER --}}
<div class="px-6 py-5 border-b border-slate-200 bg-white flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Halaman Jurnal</h1>
        <p class="text-sm text-slate-500 mt-0.5">Buat dan kelola halaman kustom yang tampil di web jurnal.</p>
    </div>
    @if(!$editing)
    <button wire:click="newPage"
            class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-lg transition-colors"
            style="background:#1d4ed8;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Halaman
    </button>
    @endif
</div>

<div class="max-w-4xl mx-auto px-6 py-6 space-y-6">

    @if($editing)
    {{-- ═══ FORM EDIT / TAMBAH ═══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-blue-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-blue-100 flex items-center justify-between" style="background:#eff6ff;">
            <h2 class="text-sm font-bold text-blue-800">
                {{ $editIndex === -1 ? 'Tambah Halaman Baru' : 'Edit Halaman' }}
            </h2>
            <button wire:click="cancelEdit" class="text-blue-400 hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-5 space-y-4">

            {{-- Judul --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Judul Halaman <span class="text-red-500">*</span></label>
                <input wire:model.live="editTitle" type="text" placeholder="mis. Biaya Publikasi, Tim Redaksi..."
                       class="w-full px-3 py-2 text-sm rounded-lg border @error('editTitle') border-red-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('editTitle') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Slug --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">
                    Slug URL <span class="text-red-500">*</span>
                    <span class="font-normal text-slate-400 ml-1">(diisi otomatis dari judul)</span>
                </label>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400 whitespace-nowrap">/about/</span>
                    <input wire:model="editSlug" type="text" placeholder="biaya-publikasi"
                           class="flex-1 px-3 py-2 text-sm rounded-lg border @error('editSlug') border-red-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                </div>
                @error('editSlug') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-slate-400">Hanya huruf kecil, angka, dan tanda hubung (-). Tidak bisa diubah setelah ada pengunjung menyimpan link halaman ini.</p>
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-3">
                <button type="button"
                        wire:click="$toggle('editEnabled')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                        style="background:{{ $editEnabled ? '#2563eb' : '#cbd5e1' }};">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                          style="transform:translateX({{ $editEnabled ? '20px' : '4px' }})"></span>
                </button>
                <label class="text-sm font-medium text-slate-700">
                    {{ $editEnabled ? 'Halaman aktif (publik)' : 'Halaman nonaktif (tersembunyi)' }}
                </label>
            </div>

            {{-- Konten --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">
                    Konten Halaman <span class="text-red-500">*</span>
                    <span class="font-normal text-slate-400 ml-1">(HTML didukung)</span>
                </label>
                <textarea wire:model="editContent" rows="14" placeholder="<p>Tulis konten halaman di sini. HTML diperbolehkan.</p>"
                          class="w-full px-3 py-2 text-sm rounded-lg border @error('editContent') border-red-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono resize-y"></textarea>
                @error('editContent') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 pt-1">
                <button wire:click="savePage"
                        class="flex-1 py-2.5 text-sm font-bold text-white rounded-lg transition-colors"
                        style="background:#1d4ed8;">
                    Simpan Halaman
                </button>
                <button wire:click="cancelEdit"
                        class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══ DAFTAR HALAMAN ════════════════════════════════════════════ --}}
    @if(empty($pages))
    <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-14 text-center">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-slate-500 font-medium">Belum ada halaman kustom</p>
        <p class="text-slate-400 text-sm mt-1">Klik "Tambah Halaman" untuk membuat halaman baru seperti Tim Editorial, Biaya Publikasi, dll.</p>
    </div>
    @else
    <div class="space-y-3">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider px-1">{{ count($pages) }} Halaman</p>
        @foreach($pages as $i => $page)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3 px-4 py-3 transition-shadow hover:shadow-md">

            {{-- Status dot --}}
            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ ($page['enabled'] ?? true) ? '#22c55e' : '#cbd5e1' }};"></span>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800 truncate">{{ $page['title'] ?? '(Tanpa Judul)' }}</p>
                <p class="text-xs text-slate-400 font-mono">/about/{{ $page['slug'] ?? '' }}</p>
            </div>

            {{-- Preview link --}}
            <a href="{{ route('journals.page', [request()->route('journal') ?? '_', $page['slug']]) }}"
               target="_blank"
               class="shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
               title="Lihat halaman">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>

            {{-- Toggle enabled --}}
            <button wire:click="toggleEnabled({{ $i }})"
                    class="shrink-0 w-9 h-5 rounded-full transition-colors focus:outline-none"
                    style="background:{{ ($page['enabled'] ?? true) ? '#22c55e' : '#cbd5e1' }};"
                    title="{{ ($page['enabled'] ?? true) ? 'Nonaktifkan' : 'Aktifkan' }}">
                <span class="block w-4 h-4 rounded-full bg-white shadow transition-transform mx-0.5"
                      style="transform:translateX({{ ($page['enabled'] ?? true) ? '16px' : '0' }})"></span>
            </button>

            {{-- Edit --}}
            <button wire:click="editPage({{ $i }})"
                    class="shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                    title="Edit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>

            {{-- Delete --}}
            <button wire:click="deletePage({{ $i }})"
                    wire:confirm="Hapus halaman '{{ $page['title'] ?? '' }}'? Tindakan ini tidak dapat dibatalkan."
                    class="shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                    title="Hapus">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
        @endforeach
    </div>
    @endif

    {{-- INFO HALAMAN BAWAAN --}}
    <div class="rounded-xl p-4 border border-slate-200" style="background:#f8fafc;">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Halaman Bawaan (tidak perlu dibuat)</p>
        <div class="flex flex-wrap gap-2">
            @foreach(['Tentang Jurnal','Tim Editorial','Panduan Penulis','Panduan Reviewer','Etika Publikasi','Kebijakan Privasi','Kontak','Pengiriman Naskah'] as $p)
            <span class="px-2.5 py-1 text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg">{{ $p }}</span>
            @endforeach
        </div>
        <p class="text-xs text-slate-400 mt-2">Halaman di atas sudah tersedia secara otomatis. Isi kontennya lewat menu <a href="{{ route('manager.settings') }}" class="text-blue-600 hover:underline">Profil & Pengaturan</a>.</p>
    </div>

</div>
</div>

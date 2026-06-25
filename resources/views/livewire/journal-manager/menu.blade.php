<div>

{{-- PAGE HEADER --}}
<div class="px-6 py-5 border-b border-slate-200 bg-white flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Menu Navigasi Jurnal</h1>
        <p class="text-sm text-slate-500 mt-0.5">Atur menu yang tampil di halaman web jurnal Anda.</p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-6 py-6 space-y-6">

    {{-- ═══ PRESET MENU ITEMS ════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between" style="background:#f8fafc;">
            <h2 class="text-sm font-bold text-slate-700">Menu Bawaan</h2>
            <button wire:click="savePresets"
                    class="px-4 py-1.5 text-xs font-bold text-white rounded-lg transition-colors"
                    style="background:#1d4ed8;">
                Simpan
            </button>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach([
                ['field'=>'menu_show_issues',        'label'=>'Terbitan',    'desc'=>'Halaman arsip terbitan / issue'],
                ['field'=>'menu_show_announcements', 'label'=>'Pengumuman',  'desc'=>'Tampil hanya jika ada pengumuman aktif'],
                ['field'=>'menu_show_about',         'label'=>'Tentang',     'desc'=>'Dropdown berisi halaman-halaman jurnal'],
                ['field'=>'menu_show_browse',        'label'=>'Jelajahi',    'desc'=>'Browse artikel berdasarkan penulis / kata kunci'],
            ] as $preset)
            <div class="flex items-center justify-between px-5 py-4">
                <div>
                    <p class="text-sm font-semibold text-slate-800">{{ $preset['label'] }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $preset['desc'] }}</p>
                </div>
                <button type="button"
                        wire:click="$toggle('{{ $preset['field'] }}')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none shrink-0"
                        style="background:{{ $this->{$preset['field']} ? '#2563eb' : '#cbd5e1' }};">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                          style="transform:translateX({{ $this->{$preset['field']} ? '20px' : '4px' }})"></span>
                </button>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ CUSTOM MENU ITEMS ═════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between" style="background:#f8fafc;">
            <h2 class="text-sm font-bold text-slate-700">Menu Kustom</h2>
            @if(!$editing)
            <button wire:click="newItem"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white rounded-lg transition-colors"
                    style="background:#1d4ed8;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Item
            </button>
            @endif
        </div>

        {{-- Form edit/tambah --}}
        @if($editing)
        <div class="p-5 border-b border-blue-100" style="background:#eff6ff;">
            <p class="text-xs font-bold text-blue-700 uppercase tracking-wider mb-4">
                {{ $editIndex === -1 ? 'Tambah Item Menu' : 'Edit Item Menu' }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Label <span class="text-red-500">*</span></label>
                    <input wire:model="editLabel" type="text" placeholder="mis. Galeri, Berita, Blog..."
                           class="w-full px-3 py-2 text-sm rounded-lg border @error('editLabel') border-red-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('editLabel') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">URL <span class="text-red-500">*</span></label>
                    <input wire:model="editUrl" type="text" placeholder="https://... atau /path/relative"
                           class="w-full px-3 py-2 text-sm rounded-lg border @error('editUrl') border-red-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('editUrl') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-2">Buka di</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="editTarget" type="radio" value="_self" class="w-3.5 h-3.5 text-blue-600">
                        <span class="text-sm text-slate-700">Tab yang sama</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="editTarget" type="radio" value="_blank" class="w-3.5 h-3.5 text-blue-600">
                        <span class="text-sm text-slate-700">Tab baru</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-2">
                <button wire:click="saveItem"
                        class="px-5 py-2 text-sm font-bold text-white rounded-lg transition-colors"
                        style="background:#1d4ed8;">
                    Simpan
                </button>
                <button wire:click="cancelEdit"
                        class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                    Batal
                </button>
            </div>
        </div>
        @endif

        {{-- List --}}
        @if(empty($items))
        <div class="px-5 py-10 text-center text-slate-400">
            <svg class="w-9 h-9 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            <p class="text-sm">Belum ada menu kustom. Klik "Tambah Item" untuk menambahkan link eksternal atau halaman lain.</p>
        </div>
        @else
        <div class="divide-y divide-slate-100">
            @foreach($items as $i => $item)
            <div class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors group">
                {{-- Reorder --}}
                <div class="flex flex-col gap-0.5 shrink-0">
                    <button wire:click="moveUp({{ $i }})"
                            class="p-0.5 text-slate-300 hover:text-slate-600 transition-colors {{ $i === 0 ? 'opacity-30 pointer-events-none' : '' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                        </svg>
                    </button>
                    <button wire:click="moveDown({{ $i }})"
                            class="p-0.5 text-slate-300 hover:text-slate-600 transition-colors {{ $i === count($items)-1 ? 'opacity-30 pointer-events-none' : '' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800">{{ $item['label'] ?? '' }}</p>
                    <p class="text-xs text-slate-400 font-mono truncate">{{ $item['url'] ?? '' }}</p>
                </div>

                {{-- Target badge --}}
                <span class="shrink-0 text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-medium">
                    {{ ($item['target'] ?? '_self') === '_blank' ? 'Tab baru' : 'Tab sama' }}
                </span>

                {{-- Edit --}}
                <button wire:click="editItem({{ $i }})"
                        class="shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                        title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>

                {{-- Delete --}}
                <button wire:click="deleteItem({{ $i }})"
                        wire:confirm="Hapus item '{{ $item['label'] ?? '' }}'?"
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
    </div>

    {{-- INFO --}}
    <div class="rounded-xl p-4 border border-blue-100" style="background:#eff6ff;">
        <p class="text-xs font-bold text-blue-700 mb-1">Catatan</p>
        <ul class="text-xs text-blue-600 space-y-1 list-disc list-inside">
            <li>Menu bawaan (Terbitan, Tentang, dll) bisa ditampilkan/sembunyikan menggunakan toggle di atas.</li>
            <li>Menu kustom muncul setelah menu bawaan di navigasi jurnal.</li>
            <li>Untuk menambah halaman jurnal kustom, gunakan menu <a href="{{ route('manager.pages') }}" class="font-bold underline">Halaman Jurnal</a>.</li>
        </ul>
    </div>

</div>
</div>

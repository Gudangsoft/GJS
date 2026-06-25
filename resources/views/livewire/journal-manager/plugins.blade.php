<div
    x-data="{
        _sortInst: null,
        initSortable() {
            if (typeof Sortable === 'undefined') {
                setTimeout(() => this.initSortable(), 100);
                return;
            }
            const el = document.getElementById('sortable-blocks');
            if (!el) return;
            if (this._sortInst) { try { this._sortInst.destroy(); } catch(e){} }
            this._sortInst = Sortable.create(el, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'opacity-30',
                dragClass:  'shadow-lg',
                onEnd: (evt) => {
                    const ids = [...el.querySelectorAll('[data-id]')].map(n => parseInt(n.dataset.id));
                    $wire.updateOrder(ids);
                }
            });
        }
    }"
    x-init="$nextTick(() => initSortable())"
    x-on:livewire:morphed.window="$nextTick(() => initSortable())"
>
<script src="{{ asset('js/sortable.min.js') }}"></script>

<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <h1 class="text-xl font-bold text-slate-900">Plugin Sidebar</h1>
    <p class="text-sm text-slate-500 mt-0.5">Kelola blok yang tampil di sidebar halaman jurnal. Seret untuk mengatur urutan.</p>
</div>

<div class="max-w-5xl mx-auto px-6 py-6 grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

    {{-- KOLOM KIRI: Blok Terpasang --}}
    <div class="space-y-3">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider px-1">Blok Terpasang</h2>

        @if($blocks->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-400">
            <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <p class="text-sm">Belum ada blok terpasang.<br>Pasang dari daftar plugin di sebelah kanan.</p>
        </div>
        @else
        <div id="sortable-blocks" class="space-y-2">
            @foreach($blocks as $block)
            @php $def = $pluginMap[$block->type] ?? null; @endphp
            <div data-id="{{ $block->id }}"
                 class="bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3 px-4 py-3 group transition-shadow hover:shadow-md">

                {{-- Drag handle --}}
                <span class="drag-handle text-slate-300 hover:text-slate-500 cursor-grab active:cursor-grabbing shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                </span>

                {{-- Color dot --}}
                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $def['color'] ?? '#94a3b8' }};"></span>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">
                        {{ $block->title ?: ($def['name'] ?? ucfirst($block->type)) }}
                    </p>
                    <p class="text-xs text-slate-400">{{ $def['name'] ?? $block->type }}</p>
                </div>

                {{-- Toggle aktif --}}
                <button wire:click="toggleBlock({{ $block->id }})"
                        class="shrink-0 w-9 h-5 rounded-full transition-colors focus:outline-none"
                        style="background:{{ $block->enabled ? '#22c55e' : '#cbd5e1' }};">
                    <span class="block w-4 h-4 rounded-full bg-white shadow transition-transform mx-0.5"
                          style="transform:translateX({{ $block->enabled ? '16px' : '0' }})"></span>
                </button>

                {{-- Edit --}}
                <button wire:click="openEdit({{ $block->id }})"
                        class="shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>

                {{-- Delete --}}
                <button wire:click="deleteBlock({{ $block->id }})" wire:confirm="Hapus blok ini?"
                        class="shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- KOLOM KANAN: Katalog Plugin + Edit Form --}}
    <div class="space-y-3">

        {{-- Edit Form --}}
        @if($showEditForm)
        @php
            $editBlock = $blocks->firstWhere('id', $editingBlockId);
            $editDef   = $editBlock ? ($pluginMap[$editBlock->type] ?? null) : null;
        @endphp
        <div class="bg-white rounded-2xl border border-blue-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-blue-100 flex items-center justify-between" style="background:#eff6ff;">
                <h3 class="text-xs font-bold text-blue-800 uppercase tracking-wider">Edit Blok</h3>
                <button wire:click="$set('showEditForm', false)" class="text-blue-400 hover:text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Judul Blok (opsional)</label>
                    <input wire:model="editTitle" type="text" placeholder="Judul kustom atau kosongkan untuk default"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                @if($editDef && !empty($editDef['settings_schema']))
                @foreach($editDef['settings_schema'] as $field)
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ $field['label'] }}</label>
                    @if($field['type'] === 'toggle')
                    <button type="button"
                            wire:click="$set('editSettings.{{ $field['key'] }}', !{{ json_encode($editSettings[$field['key']] ?? false) }})"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                            :class="{{ json_encode($editSettings[$field['key']] ?? false) }} ? 'bg-blue-600' : 'bg-slate-300'">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                              style="transform:translateX({{ ($editSettings[$field['key']] ?? false) ? '20px' : '4px' }})"></span>
                    </button>
                    @elseif($field['type'] === 'textarea')
                    <textarea wire:model="editSettings.{{ $field['key'] }}" rows="3"
                              class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    @elseif($field['type'] === 'html')
                    <textarea wire:model="editSettings.{{ $field['key'] }}" rows="5"
                              class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none font-mono"></textarea>
                    @elseif($field['type'] === 'index_list')
                    {{-- Dynamic index list --}}
                    <div class="space-y-2">
                        @foreach($editSettings['extra_indexes'] ?? [] as $xi => $xIdx)
                        <div class="flex gap-2 items-center">
                            <input wire:model="editSettings.extra_indexes.{{ $xi }}.label"
                                   type="text" placeholder="Nama (mis. Dimensions)"
                                   class="flex-1 px-3 py-1.5 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input wire:model="editSettings.extra_indexes.{{ $xi }}.url"
                                   type="text" placeholder="URL (opsional)"
                                   class="flex-1 px-3 py-1.5 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="button" wire:click="removeExtraIndex({{ $xi }})"
                                    class="p-1.5 text-slate-300 hover:text-red-500 transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        @endforeach
                        <button type="button" wire:click="addExtraIndex"
                                class="mt-1 text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Indeksasi
                        </button>
                    </div>
                    @else
                    <input wire:model="editSettings.{{ $field['key'] }}" type="text"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @endif
                </div>
                @endforeach
                @endif

                <div class="flex gap-2 pt-1">
                    <button wire:click="saveEdit"
                            class="flex-1 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan
                    </button>
                    <button wire:click="$set('showEditForm', false)"
                            class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- Plugin Catalog --}}
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider px-1">Katalog Plugin</h2>
        <div class="space-y-2">
            @foreach($available as $plugin)
            @php $installed = in_array($plugin['type'], $installedTypes); @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3 px-4 py-3"
                 style="{{ $installed ? 'opacity:.5;pointer-events:none;' : '' }}">
                <span class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-xs font-black"
                      style="background:{{ $plugin['bg'] }};color:{{ $plugin['color'] }};">
                    {{ strtoupper(substr($plugin['name'], 0, 1)) }}
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800">{{ $plugin['name'] }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $plugin['description'] }}</p>
                </div>
                @if($installed)
                <span class="text-xs font-bold text-slate-400">Terpasang</span>
                @else
                <button wire:click="installPlugin('{{ $plugin['type'] }}')"
                        class="shrink-0 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                        style="background:{{ $plugin['bg'] }};color:{{ $plugin['color'] }};">
                    + Pasang
                </button>
                @endif
            </div>
            @endforeach
        </div>
    </div>

</div>
</div>
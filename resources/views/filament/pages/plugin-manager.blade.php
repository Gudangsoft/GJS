<x-filament-panels::page>

{{-- ── Journal selector ──────────────────────────────────────────────── --}}
<div class="mb-6">
    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 block mb-2">
        Pilih Jurnal yang Dikelola
    </label>
    <select wire:model.live="selectedJournalId"
            class="w-full max-w-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        <option value="">— Pilih jurnal —</option>
        @foreach($this->getJournals() as $j)
        <option value="{{ $j->id }}">
            {{ $j->name }}{{ $j->sinta_level ? ' [' . $j->sinta_level . ']' : '' }}
        </option>
        @endforeach
    </select>
</div>

@if($selectedJournalId)

{{-- ═══════════════════════════════════════════════════════════════════
     SECTION 1 — Available Plugins
═══════════════════════════════════════════════════════════════════ --}}
<div class="mb-8">
    <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-1">Plugin Tersedia</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Klik <strong>Pasang</strong> untuk menambahkan blok ke sidebar jurnal ini. Satu jurnal bisa memasang plugin yang sama lebih dari sekali.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach(\App\Filament\Pages\PluginManager::availablePlugins() as $plugin)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col">

            {{-- Color header strip --}}
            <div class="h-1 w-full" style="background:{{ $plugin['color'] }}"></div>

            <div class="p-4 flex-1 flex flex-col gap-3">
                {{-- Icon + name --}}
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                         style="background:{{ $plugin['bg'] }};">
                        <x-dynamic-component :component="'heroicon-o-' . $plugin['icon']"
                                             class="w-5 h-5"
                                             style="color:{{ $plugin['color'] }};"/>
                    </div>
                    <div>
                        <p class="font-bold text-sm text-gray-900 dark:text-gray-100">{{ $plugin['name'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">
                            {{ $plugin['description'] }}
                        </p>
                    </div>
                </div>

                {{-- Install button --}}
                <button wire:click="installPlugin('{{ $plugin['type'] }}')"
                        wire:loading.attr="disabled"
                        class="mt-auto flex items-center justify-center gap-1.5 w-full px-3 py-2 rounded-lg text-sm font-semibold transition-all hover:brightness-110 active:scale-95"
                        style="background:{{ $plugin['color'] }};color:#fff;">
                    <x-heroicon-o-plus class="w-4 h-4 shrink-0"/>
                    Pasang Plugin
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     SECTION 2 — Active Blocks for selected journal
═══════════════════════════════════════════════════════════════════ --}}
@php $activeBlocks = $this->getActiveBlocks(); @endphp
<div>
    <div class="flex items-center justify-between mb-3">
        <div>
            <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Plugin Aktif</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $activeBlocks->count() }} blok terpasang — urutkan dengan tombol ↑↓, nonaktifkan, atau hapus.
            </p>
        </div>
        <a href="{{ \App\Filament\Resources\SidebarBlocks\SidebarBlockResource::getUrl('create') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg transition-colors"
           style="background:#f1f5f9;color:#475569;">
            <x-heroicon-o-pencil-square class="w-3.5 h-3.5 shrink-0"/>
            Konfigurasi Lanjutan
        </a>
    </div>

    @if($activeBlocks->isEmpty())
    <div class="text-center py-10 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 text-gray-400">
        <x-heroicon-o-squares-plus class="w-10 h-10 mx-auto mb-2 opacity-40"/>
        <p class="text-sm font-medium">Belum ada plugin terpasang.</p>
        <p class="text-xs mt-1">Pilih plugin di atas dan klik Pasang.</p>
    </div>
    @else
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
        @foreach($activeBlocks as $block)
        @php
            $pluginDef = collect(\App\Filament\Pages\PluginManager::availablePlugins())->firstWhere('type', $block->type);
            $color = $pluginDef['color'] ?? '#475569';
            $bg    = $pluginDef['bg']    ?? '#f8fafc';
        @endphp
        <div class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-gray-800 {{ !$block->enabled ? 'opacity-50' : '' }}">

            {{-- Drag handle / order --}}
            <div class="flex flex-col gap-0.5 shrink-0">
                <button wire:click="moveUp({{ $block->id }})"
                        class="p-0.5 rounded text-gray-300 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        title="Naikan">
                    <x-heroicon-s-chevron-up class="w-4 h-4"/>
                </button>
                <button wire:click="moveDown({{ $block->id }})"
                        class="p-0.5 rounded text-gray-300 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        title="Turunkan">
                    <x-heroicon-s-chevron-down class="w-4 h-4"/>
                </button>
            </div>

            {{-- Color dot + name --}}
            <div class="w-2 h-8 rounded-full shrink-0" style="background:{{ $color }}"></div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                    {{ $block->getDisplayTitle() }}
                </p>
                <p class="text-xs text-gray-400 truncate">
                    {{ $pluginDef['name'] ?? $block->type }}
                    @if(!$block->enabled)
                    <span class="ml-1 font-semibold text-amber-500">· Nonaktif</span>
                    @endif
                </p>
            </div>

            {{-- Urutan badge --}}
            <span class="text-xs font-mono text-gray-400 shrink-0 w-5 text-center">#{{ $block->sort_order }}</span>

            {{-- Actions --}}
            <div class="flex items-center gap-1 shrink-0">
                {{-- Edit --}}
                <a href="{{ \App\Filament\Resources\SidebarBlocks\SidebarBlockResource::getUrl('edit', ['record' => $block->id]) }}"
                   class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                   title="Konfigurasi">
                    <x-heroicon-o-cog-6-tooth class="w-4 h-4"/>
                </a>
                {{-- Toggle --}}
                <button wire:click="toggleBlock({{ $block->id }})"
                        class="p-1.5 rounded-lg transition-colors {{ $block->enabled ? 'text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20' : 'text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        title="{{ $block->enabled ? 'Nonaktifkan' : 'Aktifkan' }}">
                    @if($block->enabled)
                    <x-heroicon-o-eye class="w-4 h-4"/>
                    @else
                    <x-heroicon-o-eye-slash class="w-4 h-4"/>
                    @endif
                </button>
                {{-- Delete --}}
                <button wire:click="deleteBlock({{ $block->id }})"
                        wire:confirm="Hapus blok '{{ $block->getDisplayTitle() }}'?"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                        title="Hapus">
                    <x-heroicon-o-trash class="w-4 h-4"/>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@else
{{-- No journal selected state --}}
<div class="text-center py-16 text-gray-400">
    <x-heroicon-o-puzzle-piece class="w-14 h-14 mx-auto mb-3 opacity-30"/>
    <p class="font-semibold text-base">Pilih jurnal di atas</p>
    <p class="text-sm mt-1">untuk mengelola plugin sidebar-nya.</p>
</div>
@endif

</x-filament-panels::page>

<x-filament-panels::page>

{{-- ── Journal Selector ────────────────────────────────────────────────── --}}
<div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1.5">
                Pilih Jurnal
            </label>
            <select wire:model.live="selectedJournalId"
                    class="w-full max-w-lg rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                <option value="">— Pilih jurnal untuk dikelola —</option>
                @foreach($journals as $j)
                <option value="{{ $j->id }}" {{ $selectedJournalId == $j->id ? 'selected' : '' }}>
                    {{ $j->name_abbrev ? '[' . $j->name_abbrev . '] ' : '' }}{{ $j->name }}{{ $j->sinta_level ? ' · ' . $j->sinta_level : '' }}
                </option>
                @endforeach
            </select>
        </div>
        @if($selectedJournalId)
        <div class="shrink-0 pt-5 sm:pt-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 text-sm font-semibold border border-primary-200 dark:border-primary-800">
                <svg style="width:1rem;height:1rem;" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a.75.75 0 01.75.75v.258a33.186 33.186 0 016.668.83.75.75 0 01-.336 1.461 31.28 31.28 0 00-1.103-.232l1.702 7.545a.75.75 0 01-.387.832A4.981 4.981 0 0115 14c-.825 0-1.606-.2-2.294-.556a.75.75 0 01-.387-.832l1.77-7.849a31.743 31.743 0 00-3.339-.254v10.03a1.75 1.75 0 010 3.5 1.75 1.75 0 010-3.5V5.54a31.743 31.743 0 00-3.339.254l1.77 7.849a.75.75 0 01-.387.832A4.98 4.98 0 015 14a4.98 4.98 0 01-2.294-.556.75.75 0 01-.387-.832l1.702-7.545c-.37.07-.738.149-1.103.232a.75.75 0 01-.336-1.46 33.187 33.187 0 016.668-.832V2.75A.75.75 0 0110 2z"/></svg>
                {{ $activeBlocks->count() }} plugin aktif
            </span>
        </div>
        @endif
    </div>
</div>

@if($selectedJournalId)

<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

    {{-- ── KIRI: Plugin Tersedia ────────────────────────────────────── --}}
    <div class="xl:col-span-2 space-y-3">

        <div>
            <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100">Plugin Tersedia</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Klik <strong>Pasang</strong> untuk menambahkan ke sidebar jurnal</p>
        </div>

        <div class="space-y-2">
            @foreach($allPlugins as $plugin)
            @php
                $count    = $installedCounts[$plugin['type']] ?? 0;
                $isUnique = $plugin['unique'] ?? false;
                $isMaxed  = $isUnique && $count > 0;
            @endphp
            <div style="border-radius:0.75rem;overflow:hidden;background:{{ $isMaxed ? '#f9fafb' : '#ffffff' }};border:1px solid {{ $isMaxed ? '#e5e7eb' : '#e5e7eb' }};" class="dark:bg-gray-800 dark:border-gray-700 shadow-sm {{ $isMaxed ? 'opacity-60' : '' }}">
                {{-- Color strip --}}
                <div style="height:3px;background:{{ $plugin['color'] }};"></div>
                <div class="p-3 flex items-center gap-3">

                    {{-- Icon box --}}
                    <div style="width:2.25rem;height:2.25rem;border-radius:0.5rem;background:{{ $plugin['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <x-dynamic-component
                            :component="'heroicon-o-' . $plugin['icon']"
                            style="width:1.25rem;height:1.25rem;color:{{ $plugin['color'] }};flex-shrink:0;"/>
                    </div>

                    {{-- Info --}}
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:0.375rem;flex-wrap:wrap;">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $plugin['name'] }}</p>
                            @if($count > 0)
                            <span style="padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700;{{ $isMaxed ? 'background:#e5e7eb;color:#6b7280;' : 'background:' . $plugin['color'] . ';color:#fff;' }}">{{ $count }}×</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" style="line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $plugin['description'] }}</p>
                    </div>

                    {{-- Action --}}
                    @if($isMaxed)
                    <span style="flex-shrink:0;display:inline-flex;align-items:center;gap:4px;padding:6px 10px;border-radius:8px;background:#f3f4f6;color:#9ca3af;font-size:12px;font-weight:600;">
                        <svg style="width:0.875rem;height:0.875rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        Terpasang
                    </span>
                    @else
                    <button wire:click="installPlugin('{{ $plugin['type'] }}')"
                            style="flex-shrink:0;display:inline-flex;align-items:center;gap:4px;padding:6px 10px;border-radius:8px;background:{{ $plugin['color'] }};color:#fff;font-size:12px;font-weight:600;cursor:pointer;border:none;transition:filter .15s;">
                        <svg style="width:0.875rem;height:0.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Pasang
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pasang Semua --}}
        @php
            $missingCount = collect($allPlugins)
                ->filter(fn($p) => $p['type'] !== 'custom_html' && !($installedCounts[$p['type']] ?? 0))
                ->count();
        @endphp
        @if($missingCount > 0)
        <button wire:click="installAll"
                wire:confirm="Pasang {{ $missingCount }} plugin yang belum terpasang ke jurnal ini?"
                style="width:100%;display:flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.625rem 1rem;border-radius:0.75rem;border:2px dashed #93c5fd;color:#2563eb;font-size:0.875rem;font-weight:600;cursor:pointer;background:transparent;transition:background .15s;">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
            Pasang Semua ({{ $missingCount }} plugin)
        </button>
        @else
        <div style="display:flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.625rem 1rem;border-radius:0.75rem;border:1px solid #e5e7eb;color:#9ca3af;font-size:0.75rem;font-weight:500;">
            <svg style="width:1rem;height:1rem;color:#22c55e;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
            Semua plugin sudah terpasang
        </div>
        @endif
    </div>

    {{-- ── KANAN: Plugin Terpasang ──────────────────────────────────── --}}
    <div class="xl:col-span-3 space-y-3">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100">Plugin Terpasang</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Urutan tampil di sidebar jurnal publik</p>
            </div>
            <a href="{{ \App\Filament\Resources\SidebarBlocks\SidebarBlockResource::getUrl('index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 transition-colors">
                <svg style="width:0.875rem;height:0.875rem;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75.125v-6m0 0l2.625-2.625M3.75 13.5l2.625 2.625M3.75 13.5V7.125a1.125 1.125 0 011.125-1.125h14.25a1.125 1.125 0 011.125 1.125v6.375m0 0l2.625-2.625m-2.625 2.625l-2.625 2.625m2.625-2.625v.375"/></svg>
                Lihat Semua
            </a>
        </div>

        @if($activeBlocks->isEmpty())
        <div style="text-align:center;padding:3.5rem 1rem;border-radius:0.75rem;border:2px dashed #e5e7eb;" class="dark:border-gray-700">
            <svg style="width:3rem;height:3rem;margin:0 auto 0.75rem;color:#d1d5db;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875c-1.243 0-2.25.84-2.25 1.875 0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.4.959.4v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"/></svg>
            <p style="font-size:0.875rem;font-weight:600;color:#6b7280;">Belum ada plugin terpasang</p>
            <p style="font-size:0.75rem;color:#9ca3af;margin-top:0.25rem;">Pilih plugin di kiri lalu klik <strong>Pasang</strong></p>
        </div>
        @else

        <p style="font-size:0.6875rem;color:#9ca3af;display:flex;align-items:center;gap:4px;">
            <svg style="width:0.75rem;height:0.75rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Tampilan sidebar halaman publik jurnal
        </p>

        <div style="border-radius:0.75rem;border:1px solid #e5e7eb;overflow:hidden;" class="dark:border-gray-700 shadow-sm">
            @foreach($activeBlocks as $index => $block)
            @php
                $def   = collect($allPlugins)->firstWhere('type', $block->type);
                $color = $def['color'] ?? '#475569';
                $bg    = $def['bg']    ?? '#f8fafc';
                $icon  = $def['icon']  ?? 'squares-plus';
            @endphp
            <div style="display:flex;align-items:center;gap:0.75rem;padding:0.875rem 1rem;background:{{ $block->enabled ? '#ffffff' : '#f9fafb' }};{{ !$loop->last ? 'border-bottom:1px solid #f1f5f9;' : '' }}{{ !$block->enabled ? 'opacity:.45;' : '' }}"
                 class="dark:bg-gray-800"
                 wire:key="block-{{ $block->id }}">

                {{-- Urutan naik/turun --}}
                <div style="display:flex;flex-direction:column;flex-shrink:0;">
                    <button wire:click="moveUp({{ $block->id }})"
                            @if($loop->first) disabled @endif
                            style="padding:2px;border-radius:4px;border:none;background:transparent;cursor:{{ $loop->first ? 'not-allowed' : 'pointer' }};color:{{ $loop->first ? '#e5e7eb' : '#9ca3af' }};">
                        <svg style="width:1rem;height:1rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z" clip-rule="evenodd"/></svg>
                    </button>
                    <button wire:click="moveDown({{ $block->id }})"
                            @if($loop->last) disabled @endif
                            style="padding:2px;border-radius:4px;border:none;background:transparent;cursor:{{ $loop->last ? 'not-allowed' : 'pointer' }};color:{{ $loop->last ? '#e5e7eb' : '#9ca3af' }};">
                        <svg style="width:1rem;height:1rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                    </button>
                </div>

                {{-- Plugin icon --}}
                <div style="width:2rem;height:2rem;border-radius:0.5rem;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <x-dynamic-component :component="'heroicon-o-' . $icon"
                                         style="width:1rem;height:1rem;flex-shrink:0;color:{{ $color }};"/>
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:0;">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $block->getDisplayTitle() }}
                    </p>
                    <p style="font-size:0.75rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $def['name'] ?? $block->type }}
                        @if(!$block->enabled)
                        <span style="color:#f59e0b;font-weight:600;"> · Nonaktif</span>
                        @endif
                    </p>
                </div>

                {{-- Nomor urut --}}
                <span style="font-size:0.6875rem;font-family:monospace;color:#d1d5db;width:1.25rem;text-align:right;flex-shrink:0;">{{ $index + 1 }}</span>

                {{-- Aksi --}}
                <div style="display:flex;align-items:center;gap:2px;flex-shrink:0;">
                    <a href="{{ \App\Filament\Resources\SidebarBlocks\SidebarBlockResource::getUrl('edit', ['record' => $block->id]) }}"
                       title="Konfigurasi"
                       style="padding:6px;border-radius:8px;color:#9ca3af;display:inline-flex;align-items:center;transition:background .15s;"
                       onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb';"
                       onmouseout="this.style.background='transparent';this.style.color='#9ca3af';">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>
                    <button wire:click="toggleBlock({{ $block->id }})"
                            title="{{ $block->enabled ? 'Nonaktifkan' : 'Aktifkan' }}"
                            style="padding:6px;border-radius:8px;border:none;display:inline-flex;align-items:center;background:transparent;cursor:pointer;color:{{ $block->enabled ? '#22c55e' : '#d1d5db' }};transition:background .15s;"
                            onmouseover="this.style.background='{{ $block->enabled ? '#f0fdf4' : '#f3f4f6' }}';"
                            onmouseout="this.style.background='transparent';">
                        @if($block->enabled)
                        <svg style="width:1rem;height:1rem;" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41z" clip-rule="evenodd"/></svg>
                        @else
                        <svg style="width:1rem;height:1rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd"/><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z"/></svg>
                        @endif
                    </button>
                    <button wire:click="deleteBlock({{ $block->id }})"
                            wire:confirm="Hapus plugin ini dari jurnal?"
                            title="Hapus"
                            style="padding:6px;border-radius:8px;border:none;display:inline-flex;align-items:center;background:transparent;cursor:pointer;color:#d1d5db;transition:background .15s,color .15s;"
                            onmouseover="this.style.background='#fef2f2';this.style.color='#dc2626';"
                            onmouseout="this.style.background='transparent';this.style.color='#d1d5db';">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

@else
{{-- Empty State --}}
<div style="text-align:center;padding:5rem 1rem;">
    <div style="width:4rem;height:4rem;border-radius:1rem;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
        <svg style="width:2rem;height:2rem;color:#cbd5e1;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875c-1.243 0-2.25.84-2.25 1.875 0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.4.959.4v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"/></svg>
    </div>
    <p style="font-size:1rem;font-weight:600;color:#374151;">Pilih jurnal di atas</p>
    <p style="font-size:0.875rem;color:#9ca3af;margin-top:0.375rem;line-height:1.5;">
        Plugin sidebar mengatur blok informasi yang tampil<br>di halaman publik setiap jurnal.
    </p>
    <div style="margin-top:1.5rem;display:flex;flex-wrap:wrap;justify-content:center;gap:0.5rem;max-width:28rem;margin-left:auto;margin-right:auto;">
        @foreach($allPlugins as $p)
        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:8px;border:1px solid #e5e7eb;font-size:0.75rem;font-weight:500;color:#6b7280;">
            <x-dynamic-component :component="'heroicon-o-' . $p['icon']" style="width:0.875rem;height:0.875rem;flex-shrink:0;"/>
            {{ $p['name'] }}
        </span>
        @endforeach
    </div>
</div>
@endif

</x-filament-panels::page>

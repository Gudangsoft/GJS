<div style="background:#f6f8fb;min-height:100vh;">

<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Plugin Sidebar</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelola blok sidebar yang ditampilkan di halaman jurnal Anda.</p>
    </div>
</div>

<div style="max-width:72rem;margin:0 auto;padding:1.5rem;">

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
@endif

@if($journal)

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Available Plugins --}}
    <div>
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">Plugin Tersedia</h2>
        <div class="space-y-3">
            @foreach($available as $plugin)
            @php $alreadyInstalled = in_array($plugin['type'], $installedTypes); @endphp
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                     style="background:{{ $plugin['bg'] }};">
                    <span class="text-sm font-black" style="color:{{ $plugin['color'] }};">{{ strtoupper(substr($plugin['type'], 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-900 text-sm">{{ $plugin['name'] }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $plugin['description'] }}</p>
                </div>
                <div class="shrink-0">
                    @if($alreadyInstalled)
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400">Terpasang</span>
                    @else
                    <button wire:click="installPlugin('{{ $plugin['type'] }}')"
                            class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white transition-colors"
                            style="background:{{ $plugin['color'] }};">
                        Pasang
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Active Blocks --}}
    <div>
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">Blok Aktif di Sidebar</h2>
        @if($blocks->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center text-slate-400">
            <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
            <p class="text-sm">Belum ada plugin terpasang.</p>
        </div>
        @else
        <div class="space-y-2">
            @foreach($blocks as $block)
            @php
            $pluginDef = $available->firstWhere('type', $block->type);
            $color = $pluginDef['color'] ?? '#64748b';
            $bg    = $pluginDef['bg'] ?? '#f8fafc';
            $name  = $pluginDef['name'] ?? $block->type;
            @endphp
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3.5 flex items-center gap-3
                        {{ !$block->enabled ? 'opacity-60' : '' }}">
                <div class="flex flex-col gap-0.5">
                    <button wire:click="moveUp({{ $block->id }})"
                            class="text-slate-300 hover:text-slate-600 transition-colors leading-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <button wire:click="moveDown({{ $block->id }})"
                            class="text-slate-300 hover:text-slate-600 transition-colors leading-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:{{ $bg }};">
                    <span class="text-xs font-black" style="color:{{ $color }};">{{ strtoupper(substr($block->type, 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-900 text-sm">{{ $name }}</p>
                    <p class="text-xs text-slate-400">#{{ $block->sort_order }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button wire:click="toggleBlock({{ $block->id }})"
                            class="text-xs font-semibold px-2.5 py-1 rounded-lg border transition-colors
                                {{ $block->enabled ? 'text-amber-700 border-amber-200 bg-amber-50' : 'text-green-700 border-green-200 bg-green-50' }}">
                        {{ $block->enabled ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                    <button wire:click="deleteBlock({{ $block->id }})"
                            wire:confirm="Hapus blok ini?"
                            class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-red-200 bg-red-50 text-red-700">
                        Hapus
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

@else
<div class="text-center py-20 text-slate-400">
    <p class="font-semibold">Anda belum ditugaskan ke jurnal manapun.</p>
</div>
@endif

</div>
</div>

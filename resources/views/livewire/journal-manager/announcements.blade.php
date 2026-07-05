<div style="background:#f6f8fb;min-height:100vh;">

<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Pengumuman</h1>
            <p class="text-sm text-slate-500 mt-0.5">Buat dan kelola pengumuman untuk jurnal Anda.</p>
        </div>
        @if(!$showForm)
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Pengumuman Baru
        </button>
        @endif
    </div>
</div>

<div style="max-width:64rem;margin:0 auto;padding:1.5rem;width:100%;box-sizing:border-box;">

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
@endif

@if($showForm)
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
    <h2 class="text-base font-bold text-slate-900 mb-4">{{ $editingId ? 'Edit Pengumuman' : 'Pengumuman Baru' }}</h2>
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Judul <span class="text-red-500">*</span></label>
            <input wire:model="title" type="text" required
                   class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Deskripsi Singkat</label>
            <textarea wire:model="description_short" rows="2"
                      class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                      placeholder="Ringkasan singkat pengumuman..."></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Deskripsi Lengkap</label>
            <textarea wire:model="description" rows="5"
                      class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Kadaluarsa</label>
            <input wire:model="date_expire" type="date"
                   class="w-full sm:w-48 px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                Simpan
            </button>
            <button type="button" wire:click="cancelForm"
                    class="px-5 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                Batal
            </button>
        </div>
    </form>
</div>
@endif

@if($journal)
<div class="space-y-4">
    @forelse($announcements as $ann)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-slate-900 text-sm">{{ $ann->title }}</h3>
                @if($ann->description_short)
                <p class="text-sm text-slate-500 mt-1">{{ $ann->description_short }}</p>
                @endif
                <div class="flex items-center gap-3 mt-2">
                    @if($ann->date_posted)
                    <span class="text-xs text-slate-400">Diposting: {{ $ann->date_posted->format('d M Y') }}</span>
                    @endif
                    @if($ann->date_expire)
                    <span class="text-xs {{ $ann->date_expire->isPast() ? 'text-red-500' : 'text-slate-400' }}">
                        Kadaluarsa: {{ $ann->date_expire->format('d M Y') }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button wire:click="openEdit({{ $ann->id }})"
                        class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
                    Edit
                </button>
                <button wire:click="delete({{ $ann->id }})"
                        wire:confirm="Yakin hapus pengumuman ini?"
                        class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                    Hapus
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="text-center py-16 text-slate-400">
            <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            <p class="text-sm font-medium">Belum ada pengumuman.</p>
        </div>
    </div>
    @endforelse
</div>
@else
<div class="text-center py-20 text-slate-400">
    <p class="font-semibold">Anda belum ditugaskan ke jurnal manapun.</p>
</div>
@endif

</div>
</div>

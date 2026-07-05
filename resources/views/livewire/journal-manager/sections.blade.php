<div style="background:#f6f8fb;min-height:100vh;">

<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Seksi / Rubrik</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola kategori / rubrik untuk submission jurnal Anda.</p>
        </div>
        @if(!$showForm)
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Seksi Baru
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
    <h2 class="text-base font-bold text-slate-900 mb-4">{{ $editingId ? 'Edit Seksi' : 'Seksi Baru' }}</h2>
    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Judul Seksi <span class="text-red-500">*</span></label>
                <input wire:model="title" type="text" required
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Singkatan (Abbrev)</label>
                <input wire:model="abbrev" type="text"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Kebijakan Seksi</label>
            <textarea wire:model="policy" rows="3"
                      class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Urutan</label>
                <input wire:model="sequence" type="number" min="0"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="is_inactive" type="checkbox" class="w-4 h-4 rounded text-blue-600">
                    <span class="text-sm font-medium text-slate-700">Nonaktifkan Seksi</span>
                </label>
            </div>
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
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    @if($sections->isEmpty())
    <div class="text-center py-16 text-slate-400">
        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h7"/></svg>
        <p class="text-sm font-medium">Belum ada seksi / rubrik.</p>
    </div>
    @else
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Seksi</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Singkatan</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Urutan</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($sections as $sec)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5">
                    <p class="font-semibold text-slate-900">{{ $sec->title }}</p>
                    @if($sec->policy)
                    <p class="text-xs text-slate-400 truncate max-w-xs">{{ Str::limit($sec->policy, 60) }}</p>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-slate-600">{{ $sec->abbrev ?? '—' }}</td>
                <td class="px-5 py-3.5 text-slate-600">{{ $sec->sequence }}</td>
                <td class="px-5 py-3.5">
                    @if($sec->is_inactive)
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">Nonaktif</span>
                    @else
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-700">Aktif</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2 justify-end">
                        <button wire:click="openEdit({{ $sec->id }})"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
                            Edit
                        </button>
                        <button wire:click="delete({{ $sec->id }})"
                                wire:confirm="Yakin hapus seksi ini?"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@else
<div class="text-center py-20 text-slate-400">
    <p class="font-semibold">Anda belum ditugaskan ke jurnal manapun.</p>
</div>
@endif

</div>
</div>

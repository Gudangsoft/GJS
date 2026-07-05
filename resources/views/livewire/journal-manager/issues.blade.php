<div style="background:#f6f8fb;min-height:100vh;">

<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Terbitan (Issue)</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola volume dan nomor terbitan jurnal Anda.</p>
        </div>
        @if(!$showForm)
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Terbitan Baru
        </button>
        @endif
    </div>
</div>

<div style="max-width:64rem;margin:0 auto;padding:1.5rem;width:100%;box-sizing:border-box;">

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
@endif

@if($showForm)
{{-- FORM --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
    <h2 class="text-base font-bold text-slate-900 mb-4">{{ $editingId ? 'Edit Terbitan' : 'Terbitan Baru' }}</h2>
    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Volume</label>
                <input wire:model="volume" type="text" placeholder="Misal: 12"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('volume')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor</label>
                <input wire:model="number" type="text" placeholder="Misal: 2"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun <span class="text-red-500">*</span></label>
                <input wire:model="year" type="text" placeholder="Misal: 2025" required
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('year')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Judul Terbitan</label>
            <input wire:model="title" type="text" placeholder="Opsional"
                   class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Terbit</label>
            <input wire:model="date_published" type="date"
                   class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input wire:model="published" type="checkbox" class="w-4 h-4 rounded text-blue-600">
                <span class="text-sm font-medium text-slate-700">Terbitkan</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input wire:model="current" type="checkbox" class="w-4 h-4 rounded text-blue-600">
                <span class="text-sm font-medium text-slate-700">Terbitan Terkini</span>
            </label>
        </div>

        {{-- Cover Image --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-2">Cover Terbitan</label>
            <div class="flex items-start gap-4">

                {{-- Kotak gambar = trigger input file --}}
                <label for="issueCoverInput" class="w-24 h-32 rounded-xl border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden shrink-0 bg-slate-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors group relative">
                    @if($newCoverImage)
                        <img src="{{ $newCoverImage->temporaryUrl() }}" class="w-full h-full object-cover">
                    @elseif($existingCover)
                        <img src="{{ Storage::disk('public')->url($existingCover) }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-center text-slate-300 group-hover:text-blue-400 transition-colors">
                            <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-xs mt-1">Klik pilih</p>
                        </div>
                    @endif
                    {{-- Overlay saat sudah ada gambar --}}
                    @if($newCoverImage || $existingCover)
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </div>
                    @endif
                </label>

                <div class="flex-1">
                    <input id="issueCoverInput" wire:model="newCoverImage" type="file" accept="image/*" class="hidden">
                    <button type="button" onclick="document.getElementById('issueCoverInput').click()"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-blue-50 hover:border-blue-300 text-slate-600 hover:text-blue-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Pilih Gambar
                    </button>
                    <p class="text-xs text-slate-400 mt-2">PNG, JPG. Maks 2MB.<br>Rekomendasi: 400×560px (rasio 2:3).</p>
                    @error('newCoverImage')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @if($existingCover && $editingId)
                    <button type="button" wire:click="removeCover({{ $editingId }})"
                            class="mt-2 text-xs text-red-500 hover:underline block">
                        Hapus cover
                    </button>
                    @endif
                </div>
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
    @if($issues->isEmpty())
    <div class="text-center py-16 text-slate-400">
        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        <p class="text-sm font-medium">Belum ada terbitan.</p>
    </div>
    @else
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Terbitan</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Terbit</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($issues as $issue)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-14 rounded-lg overflow-hidden shrink-0 bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center">
                            @if($issue->cover_image)
                                <img src="{{ Storage::disk('public')->url($issue->cover_image) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-white text-xs font-bold leading-tight text-center px-1">{{ $issue->volume ?? '—' }}<br>{{ $issue->number ? '#'.$issue->number : '' }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">
                                @if($issue->volume) Vol. {{ $issue->volume }}@endif
                                @if($issue->number) No. {{ $issue->number }}@endif
                                @if($issue->year) ({{ $issue->year }})@endif
                            </p>
                            @if($issue->title)<p class="text-xs text-slate-400">{{ $issue->title }}</p>@endif
                        </div>
                    </div>
                    @if($issue->current)<span class="ml-13 text-xs font-bold text-blue-600">Terkini</span>@endif
                </td>
                <td class="px-5 py-3.5 text-slate-600">
                    {{ $issue->date_published ? $issue->date_published->format('d M Y') : '—' }}
                </td>
                <td class="px-5 py-3.5">
                    @if($issue->published)
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-700">Terbit</span>
                    @else
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">Draf</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2 justify-end">
                        <button wire:click="togglePublish({{ $issue->id }})"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors
                                    {{ $issue->published ? 'text-amber-700 border-amber-200 bg-amber-50 hover:bg-amber-100' : 'text-green-700 border-green-200 bg-green-50 hover:bg-green-100' }}">
                            {{ $issue->published ? 'Sembunyikan' : 'Terbitkan' }}
                        </button>
                        <button wire:click="openEdit({{ $issue->id }})"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
                            Edit
                        </button>
                        <button wire:click="delete({{ $issue->id }})"
                                wire:confirm="Yakin hapus terbitan ini?"
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

<div style="background:#f6f8fb;min-height:100vh;">

<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Pengaturan Jurnal</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola profil dan konfigurasi jurnal Anda.</p>
        </div>
        <button wire:click="save"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Simpan
        </button>
    </div>
</div>

<div style="max-width:72rem;margin:0 auto;padding:1.5rem;">

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

@if($journal)
<form wire:submit="save" class="space-y-6">

    {{-- Identitas Jurnal --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Identitas Jurnal</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Jurnal <span class="text-red-500">*</span></label>
                <input wire:model="name" type="text" required
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Singkatan</label>
                    <input wire:model="name_abbrev" type="text"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">URL Jurnal</label>
                    <input wire:model="url" type="url"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">ISSN Cetak</label>
                    <input wire:model="issn_print" type="text" placeholder="xxxx-xxxx"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">e-ISSN (Online)</label>
                    <input wire:model="issn_online" type="text" placeholder="xxxx-xxxx"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Penerbit</label>
                    <input wire:model="publisher" type="text"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Email Jurnal</label>
                    <input wire:model="email" type="email"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Kontak</label>
                <input wire:model="contact_name" type="text"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    {{-- Akreditasi SINTA --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Akreditasi SINTA</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Level SINTA</label>
                <select wire:model="sinta_level"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Pilih Level —</option>
                    @foreach(['S1','S2','S3','S4','S5','S6'] as $lvl)
                    <option value="{{ $lvl }}">{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">SINTA ID</label>
                <input wire:model="sinta_id" type="text"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    {{-- Review & Lisensi --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Review & Lisensi</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Mode Review</label>
                <select wire:model="review_mode"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="double_blind">Double Blind</option>
                    <option value="single_blind">Single Blind</option>
                    <option value="open">Open Review</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Durasi Review (minggu)</label>
                <input wire:model="num_weeks_per_review" type="number" min="1" max="52"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tipe Lisensi</label>
                <select wire:model="license_type"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="cc_by">CC BY</option>
                    <option value="cc_by_nc">CC BY-NC</option>
                    <option value="cc_by_sa">CC BY-SA</option>
                    <option value="cc_by_nc_sa">CC BY-NC-SA</option>
                    <option value="cc_by_nd">CC BY-ND</option>
                    <option value="cc_by_nc_nd">CC BY-NC-ND</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Fokus & Panduan --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Fokus & Panduan</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Fokus & Ruang Lingkup</label>
                <textarea wire:model="focus_scope" rows="4"
                          class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Panduan Penulis</label>
                <textarea wire:model="author_guidelines" rows="4"
                          class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
        </div>
    </div>

    {{-- Status Jurnal --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Status Jurnal</h2>
        <div class="space-y-3">
            <label class="flex items-center gap-3 cursor-pointer">
                <input wire:model="enabled" type="checkbox" class="w-4 h-4 rounded text-blue-600">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Jurnal Aktif</p>
                    <p class="text-xs text-slate-500">Jurnal akan tampil di portal dan dapat diakses publik.</p>
                </div>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input wire:model="disable_submissions" type="checkbox" class="w-4 h-4 rounded text-red-600">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Nonaktifkan Submission</p>
                    <p class="text-xs text-slate-500">Penulis tidak dapat mengirimkan naskah baru.</p>
                </div>
            </label>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Simpan Pengaturan
        </button>
    </div>

</form>
@else
<div class="text-center py-20 text-slate-400">
    <p class="font-semibold">Anda belum ditugaskan ke jurnal manapun.</p>
</div>
@endif

</div>
</div>

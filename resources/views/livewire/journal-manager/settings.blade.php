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

    {{-- 1. Identitas Jurnal --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100" style="background:#f0f5ff;">
            <h2 class="text-xs font-bold text-blue-800 uppercase tracking-wider">Identitas Jurnal</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Jurnal <span class="text-red-500">*</span></label>
                <input wire:model="name" type="text" required
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Singkatan / Akronim</label>
                    <input wire:model="name_abbrev" type="text" placeholder="Contoh: IJAS"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">URL Jurnal</label>
                    <input wire:model="url" type="url" placeholder="https://..."
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
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Frekuensi Terbit</label>
                    <input wire:model="publication_frequency" type="text" placeholder="Contoh: 2 kali setahun (Maret & September)"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Bahasa Utama</label>
                    <select wire:model="primary_locale"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="id">Indonesia</option>
                        <option value="en">English</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">DOI Prefix</label>
                    <input wire:model="doi_prefix" type="text" placeholder="Contoh: 10.12345"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Cover & Logo Jurnal --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100" style="background:#f0f5ff;">
            <h2 class="text-xs font-bold text-blue-800 uppercase tracking-wider">Logo & Cover Jurnal</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- Logo --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">Logo Jurnal</label>
                    <div class="flex items-start gap-4">
                        <div class="w-20 h-20 rounded-xl border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden shrink-0 bg-slate-50">
                            @if($newLogo)
                                <img src="{{ $newLogo->temporaryUrl() }}" class="w-full h-full object-contain">
                            @elseif($journal->logo)
                                <img src="{{ Storage::disk('public')->url($journal->logo) }}" class="w-full h-full object-contain">
                            @else
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input wire:model="newLogo" type="file" accept="image/*"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-xs text-slate-400 mt-1.5">PNG, JPG, SVG. Maks 2MB.<br>Rekomendasi: 200×200px.</p>
                            @error('newLogo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Cover Image --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">Cover / Gambar Utama Jurnal</label>
                    <div class="flex items-start gap-4">
                        <div class="w-20 h-20 rounded-xl border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden shrink-0 bg-slate-50">
                            @if($newCoverImage)
                                <img src="{{ $newCoverImage->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif($journal->cover_image)
                                <img src="{{ Storage::disk('public')->url($journal->cover_image) }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input wire:model="newCoverImage" type="file" accept="image/*"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-xs text-slate-400 mt-1.5">PNG, JPG. Maks 2MB.<br>Rekomendasi: 800×400px.</p>
                            @error('newCoverImage')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- 3. Kontak --}}
    {{-- NOTE: old "3. Akreditasi" below renumbered to 4 --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100" style="background:#f0f5ff;">
            <h2 class="text-xs font-bold text-blue-800 uppercase tracking-wider">Kontak</h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Pengelola / Kontak</label>
                    <input wire:model="contact_name" type="text"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Email Jurnal</label>
                    <input wire:model="email" type="email"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Telepon</label>
                <input wire:model="contact_phone" type="text" placeholder="+62..."
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Korespondensi</label>
                <textarea wire:model="mailing_address" rows="3" placeholder="Alamat lengkap redaksi..."
                          class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
        </div>
    </div>

    {{-- APC & WA Pengelola --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100 flex items-center gap-2" style="background:#fff7ed;">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h2 class="text-xs font-bold text-orange-800 uppercase tracking-wider">APC & Kontak Pengelola</h2>
        </div>
        <div class="p-6 space-y-4">
            {{-- APC toggle --}}
            <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50">
                <button type="button" wire:click="$toggle('apc_enabled')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                        :class="{ 'bg-orange-500': $wire.apc_enabled, 'bg-slate-300': !$wire.apc_enabled }">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                          :class="{ 'translate-x-6': $wire.apc_enabled, 'translate-x-1': !$wire.apc_enabled }"></span>
                </button>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Article Processing Charge (APC)</p>
                    <p class="text-xs text-slate-400">Aktifkan jika jurnal memungut biaya pemrosesan dari penulis</p>
                </div>
            </div>

            @if($apc_enabled)
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah APC</label>
                    <input wire:model="apc_amount" type="number" min="0" step="0.01" placeholder="500000"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Mata Uang</label>
                    <select wire:model="apc_currency" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="IDR">IDR (Rupiah)</option>
                        <option value="USD">USD (Dollar)</option>
                        <option value="EUR">EUR (Euro)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Kebijakan Keringanan APC</label>
                <textarea wire:model="apc_waiver_policy" rows="3" placeholder="Tuliskan kebijakan waiver / keringanan biaya di sini..."
                          class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
            </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor WA Pengelola (publik)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">WA</span>
                    <input wire:model="wa_contact" type="text" placeholder="628123456789"
                           class="w-full pl-10 pr-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <p class="text-xs text-slate-400 mt-1">Format internasional tanpa + (contoh: 628123456789). Akan tampil sebagai tombol chat di halaman jurnal.</p>
            </div>
        </div>
    </div>

    {{-- 3. Akreditasi & Indeksasi --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100" style="background:#f0f5ff;">
            <h2 class="text-xs font-bold text-blue-800 uppercase tracking-wider">Akreditasi & Indeksasi</h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                    <input wire:model="sinta_id" type="text" placeholder="Nomor ID di SINTA"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Periode Akreditasi</label>
                    <input wire:model="accreditation_period" type="text" placeholder="Contoh: 2020–2024"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor SK Akreditasi</label>
                <input wire:model="accreditation_no" type="text" placeholder="Contoh: 200/M/KPT/2020"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">DOAJ ID / URL</label>
                    <input wire:model="doaj_id" type="text" placeholder="ID atau URL DOAJ"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Garuda ID / URL</label>
                    <input wire:model="garuda_id" type="text" placeholder="ID atau URL Garuda"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Review & Lisensi --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100" style="background:#f0f5ff;">
            <h2 class="text-xs font-bold text-blue-800 uppercase tracking-wider">Review & Lisensi</h2>
        </div>
        <div class="p-6 space-y-4">
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
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pemegang Hak Cipta</label>
                <input wire:model="copyright_holder" type="text" placeholder="Nama lembaga atau penerbit"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pernyataan Open Access</label>
                <textarea wire:model="open_access_statement" rows="3"
                          class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pernyataan Hak Cipta</label>
                <textarea wire:model="copyright_notice" rows="3"
                          class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
        </div>
    </div>

    {{-- 5. Konten Jurnal --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100" style="background:#f0f5ff;">
            <h2 class="text-xs font-bold text-blue-800 uppercase tracking-wider">Konten Jurnal</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Fokus & Ruang Lingkup</label>
                <textarea wire:model="focus_scope" rows="4"
                          class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tentang Jurnal</label>
                <textarea wire:model="about_journal" rows="4"
                          class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Panduan Penulis</label>
                <textarea wire:model="author_guidelines" rows="4"
                          class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pernyataan Etika</label>
                <textarea wire:model="ethics_statement" rows="3"
                          class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
        </div>
    </div>

    {{-- 6. Turnitin --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100 flex items-center gap-2" style="background:#fdf2f8;">
            <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <h2 class="text-xs font-bold text-pink-800 uppercase tracking-wider">Turnitin (Cek Plagiarisme)</h2>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-xs text-slate-500">Konfigurasi akun Turnitin untuk pengecekan kemiripan naskah. Diperoleh dari dashboard Turnitin Anda.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Turnitin API Key</label>
                    <input wire:model="turnitin_api_key" type="password" placeholder="Masukkan API Key Turnitin"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-pink-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Account ID / Integration ID</label>
                    <input wire:model="turnitin_account_id" type="text" placeholder="Contoh: 123456"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-pink-400">
                </div>
            </div>
            @if($turnitin_api_key)
            <div class="flex items-center gap-2 text-xs text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded-lg">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                API Key sudah dikonfigurasi
            </div>
            @endif
        </div>
    </div>

    {{-- 7. WhatsApp API --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100 flex items-center gap-2" style="background:#f0fff4;">
            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.998 2C6.477 2 2 6.484 2 12.017c0 1.99.521 3.848 1.427 5.449L2.036 22l4.66-1.366A9.987 9.987 0 0011.998 22c5.521 0 9.998-4.484 9.998-10.017C21.996 6.484 17.519 2 11.998 2z"/></svg>
            <h2 class="text-xs font-bold text-green-800 uppercase tracking-wider">WhatsApp API (Fonnte)</h2>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-xs text-slate-500">
                Daftarkan di <strong>fonnte.com</strong> untuk mendapatkan token. Token digunakan untuk WA Blast.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Token Fonnte API</label>
                    <input wire:model="wa_api_token" type="password" placeholder="Token dari fonnte.com"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor WA Pengirim</label>
                    <input wire:model="wa_sender_number" type="text" placeholder="628123456789"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
            </div>
            @if($wa_api_token)
            <div class="flex items-center gap-2 text-xs text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded-lg">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Token WA sudah dikonfigurasi — <a href="{{ route('manager.wa-blast') }}" class="underline">Buka WA Blast →</a>
            </div>
            @endif
        </div>
    </div>

    {{-- 8. Tanda Tangan LOA --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100" style="background:#fef3c7;">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <h2 class="text-xs font-bold text-amber-800 uppercase tracking-wider">Tanda Tangan Letter of Acceptance (LOA)</h2>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-xs text-slate-500">Nama dan jabatan yang akan muncul di bagian tanda tangan pada dokumen LOA.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Penandatangan</label>
                    <input wire:model="loa_signer_name" type="text" placeholder="Nama lengkap Editor-in-Chief"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan / Peran</label>
                    <input wire:model="loa_signer_title" type="text" placeholder="Contoh: Editor-in-Chief"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            @if($loa_signer_name || $loa_signer_title)
            <div class="mt-2 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                <p class="font-semibold">Pratinjau tanda tangan LOA:</p>
                <p class="mt-1"><strong>{{ $loa_signer_name ?: '—' }}</strong></p>
                <p class="text-xs text-amber-600">{{ $loa_signer_title ?: '—' }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- 7. Status Jurnal --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100" style="background:#f0f5ff;">
            <h2 class="text-xs font-bold text-blue-800 uppercase tracking-wider">Status Jurnal</h2>
        </div>
        <div class="p-6 space-y-3">
            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-slate-50 transition-colors">
                <input wire:model="enabled" type="checkbox" class="w-4 h-4 rounded text-blue-600">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Jurnal Aktif</p>
                    <p class="text-xs text-slate-500">Jurnal tampil di portal dan dapat diakses publik.</p>
                </div>
            </label>
            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-slate-50 transition-colors">
                <input wire:model="disable_submissions" type="checkbox" class="w-4 h-4 rounded text-red-600">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Nonaktifkan Submission</p>
                    <p class="text-xs text-slate-500">Penulis tidak dapat mengirimkan naskah baru.</p>
                </div>
            </label>
        </div>
    </div>

    <div class="flex justify-end pb-4">
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
{{-- ╔═══════════════════════════════════════════════╗
     SETTINGS PAGE — 6-tab layout
     Sticky: tab bar saja di top:56px (bawah fixed app header)
     Tidak pakai overflow-x:hidden pada wrapper (merusak sticky)
╚═══════════════════════════════════════════════╝ --}}
<div x-data="{ tab: 'identitas' }">

{{-- ════ PAGE HEADER (non-sticky, scrolls away) ════ --}}
<div class="bg-white border-b border-slate-200 px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
    <div class="min-w-0">
        <h1 class="text-base font-bold text-slate-900 leading-tight">Pengaturan Jurnal</h1>
        @if($journal)
        <p class="text-xs text-slate-500 mt-0.5">
            <span class="font-semibold text-blue-700 truncate">
                {{ $name ?: 'Jurnal #' . $journalId }}
            </span>
            @if($journalId)<span class="text-slate-300 mx-1">·</span><span class="text-slate-400">ID #{{ $journalId }}</span>@endif
        </p>
        @endif
    </div>
    <button wire:click="save" wire:loading.attr="disabled"
            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 active:bg-blue-800 disabled:opacity-60 shadow-sm transition-colors">
        <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <span wire:loading.remove wire:target="save">Simpan</span>
        <span wire:loading wire:target="save">Menyimpan…</span>
    </button>
</div>

{{-- ════ STICKY TAB BAR ════ --}}
@php
$tabErrors = [
    'identitas'  => $errors->hasAny(['name','name_abbrev','url','issn_print','issn_online','publisher','doi_prefix','publication_months','publication_months.*']),
    'tampilan'   => $errors->hasAny(['newLogo','newCoverImage','newFavicon','newHeaderBanner','description']),
    'konten'     => $errors->hasAny(['focus_scope','about_journal','author_guidelines','reviewer_guidelines','ethics_statement','privacy_statement','submission_acknowledgement','submission_checklist','review_mode','num_weeks_per_review','license_type','copyright_holder','open_access_statement','copyright_notice','num_weeks_per_response']),
    'akreditasi' => $errors->hasAny(['sinta_level','sinta_id','sinta_score','sinta_score_3yr','accreditation_no','accreditation_period','doaj_id','garuda_id','new_indexer_logo','new_sponsor_logo']),
    'operasional'=> $errors->hasAny(['email','contact_name','contact_phone','mailing_address','apc_amount','apc_currency','wa_contact','loa_signer_name','loa_signer_title','country','timezone','tech_support_name','tech_support_email']),
    'integrasi'  => $errors->hasAny(['turnitin_api_key','turnitin_account_id','wa_api_token','wa_sender_number','custom_header_html','custom_footer_html']),
];
@endphp

<div class="bg-white border-b border-slate-200 sticky z-20" style="top:56px;">
    <div class="flex overflow-x-auto" style="scrollbar-width:none;-ms-overflow-style:none;">

        @foreach([
            ['identitas',  'Identitas',   '📋'],
            ['tampilan',   'Tampilan',    '🎨'],
            ['konten',     'Konten',      '📖'],
            ['akreditasi', 'Akreditasi',  '🏅'],
            ['operasional','Operasional', '⚙️'],
            ['integrasi',  'Integrasi',   '🔌'],
        ] as [$key, $label, $emoji])

        <button type="button"
                @click="tab = '{{ $key }}'"
                class="relative shrink-0 flex items-center gap-1.5 px-4 py-3 text-xs font-semibold whitespace-nowrap border-b-2 transition-colors"
                :class="tab === '{{ $key }}'
                    ? 'border-blue-600 text-blue-700 bg-blue-50/60'
                    : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
            <span class="text-base leading-none select-none">{{ $emoji }}</span>
            <span>{{ $label }}</span>
            @if($tabErrors[$key])
            <span class="absolute top-2 right-2 w-1.5 h-1.5 rounded-full bg-red-500"></span>
            @endif
        </button>

        @endforeach

    </div>
</div>

{{-- ════ MAIN CONTENT ════ --}}
<div class="px-4 sm:px-6 py-6 mx-auto" style="max-width:64rem;">

@if(!$journal)
<div class="text-center py-20 text-slate-400">
    <p class="font-semibold">Anda belum ditugaskan ke jurnal manapun.</p>
</div>
@else

{{-- Error summary --}}
@if($errors->any())
<div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm">
    <p class="font-bold text-red-800 mb-1 flex items-center gap-1.5">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        {{ $errors->count() }} kesalahan ditemukan — tab bermasalah ditandai titik merah
    </p>
    <ul class="list-disc list-inside text-red-700 text-xs space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form wire:submit="save" class="space-y-0">

{{-- ══════════════════════════════════════════
     TAB 1 — IDENTITAS
══════════════════════════════════════════ --}}
<div x-show="tab === 'identitas'" class="space-y-5">

    {{-- Identitas Jurnal --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Identitas Jurnal</p>
        </div>
        <div class="p-5 space-y-4">

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Jurnal <span class="text-red-500">*</span></label>
                <input wire:model="name" type="text" placeholder="Nama lengkap jurnal..."
                       class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Singkatan / Akronim</label>
                    <input wire:model="name_abbrev" type="text" placeholder="Contoh: IJAS"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">URL Jurnal</label>
                    <input wire:model="url" type="text" placeholder="https://..."
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">ISSN Cetak</label>
                    <input wire:model="issn_print" type="text" placeholder="xxxx-xxxx"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">e-ISSN (Online)</label>
                    <input wire:model="issn_online" type="text" placeholder="xxxx-xxxx"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Penerbit</label>
                    <input wire:model="publisher" type="text"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">DOI Prefix</label>
                    <input wire:model="doi_prefix" type="text" placeholder="Contoh: 10.12345"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Frekuensi Terbit / Tahun</label>
                    <select wire:model.live="publication_freq_count"
                            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">1× (Tahunan)</option>
                        <option value="2">2× (Semesteran)</option>
                        <option value="3">3× (Tiga kali setahun)</option>
                        <option value="4">4× (Kuartalan)</option>
                        <option value="6">6× (Dua bulanan)</option>
                        <option value="12">12× (Bulanan)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Bahasa Utama</label>
                    <select wire:model="primary_locale"
                            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="id">Indonesia</option>
                        <option value="en">English</option>
                    </select>
                </div>
            </div>

            {{-- Bulan Terbit --}}
            @php
            $bulanSingkat = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
                             7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
            $bulanPenuh   = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                             7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
            $sisa = $publication_freq_count - count(array_filter($publication_months));
            @endphp
            <div class="rounded-xl border border-slate-200 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <p class="text-xs font-semibold text-slate-600">Bulan Terbit</p>
                    @if($sisa > 0)
                        <span class="text-xs text-blue-600 font-semibold">— pilih {{ $sisa }} lagi</span>
                    @else
                        <span class="text-xs text-green-600 font-semibold">✓ lengkap</span>
                    @endif
                </div>
                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                    @foreach($bulanSingkat as $num => $singkat)
                    @php $dipilih = in_array($num, array_map('intval', $publication_months)); @endphp
                    <label class="flex items-center gap-1.5 cursor-pointer select-none group">
                        <input type="checkbox"
                               wire:click="toggleMonth({{ $num }})"
                               @checked($dipilih)
                               {{ (!$dipilih && $sisa === 0) ? 'disabled' : '' }}
                               class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600 cursor-pointer disabled:cursor-not-allowed disabled:opacity-30">
                        <span class="text-xs {{ $dipilih ? 'font-bold text-blue-700' : ($sisa===0 ? 'text-slate-300' : 'text-slate-600') }}">{{ $singkat }}</span>
                    </label>
                    @endforeach
                </div>
                @if(!empty($publication_months))
                @php
                    $dipilihNama = array_values(array_filter(array_map(fn($m) => $bulanPenuh[intval($m)] ?? null, $publication_months)));
                    sort($dipilihNama);
                @endphp
                <p class="text-xs text-slate-500 mt-3 pt-3 border-t border-slate-100">
                    Terbit setiap: <strong class="text-slate-700">{{ implode(', ', $dipilihNama) }}</strong>
                </p>
                @endif
            </div>

        </div>
    </div>

    {{-- Status Jurnal --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Status Jurnal</p>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                <input wire:model="enabled" type="checkbox" class="w-4 h-4 mt-0.5 rounded text-blue-600 cursor-pointer">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Jurnal Aktif</p>
                    <p class="text-xs text-slate-500 mt-0.5">Jurnal tampil di portal dan dapat diakses publik</p>
                </div>
            </label>
            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                <input wire:model="disable_submissions" type="checkbox" class="w-4 h-4 mt-0.5 rounded text-red-500 cursor-pointer">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Nonaktifkan Submission</p>
                    <p class="text-xs text-slate-500 mt-0.5">Penulis tidak dapat mengirim naskah baru</p>
                </div>
            </label>
        </div>
    </div>

    @include('livewire.journal-manager._settings-save-btn')
</div>

{{-- ══════════════════════════════════════════
     TAB 2 — TAMPILAN
══════════════════════════════════════════ --}}
<div x-show="tab === 'tampilan'" class="space-y-5" style="display:none;">

    {{-- Logo, Cover & Favicon --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Logo, Cover & Favicon</p>
        </div>
        <div class="p-5 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                {{-- Logo --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">Logo Jurnal</label>
                    <div class="w-full h-24 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden mb-2">
                        @if($newLogo)
                            <img src="{{ $newLogo->temporaryUrl() }}" class="w-full h-full object-contain">
                        @elseif($journal->logo)
                            <img src="{{ Storage::disk('public')->url($journal->logo) }}" class="w-full h-full object-contain">
                        @else
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                    <input wire:model="newLogo" type="file" accept="image/*"
                           class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">PNG/JPG/SVG. Maks 2MB. 200×200px.</p>
                    @error('newLogo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Cover --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">Cover / Gambar Utama</label>
                    <div class="w-full h-24 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden mb-2">
                        @if($newCoverImage)
                            <img src="{{ $newCoverImage->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($journal->cover_image)
                            <img src="{{ Storage::disk('public')->url($journal->cover_image) }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                    <input wire:model="newCoverImage" type="file" accept="image/*"
                           class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">PNG/JPG. Maks 2MB. 800×400px.</p>
                    @error('newCoverImage')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Favicon --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">Favicon <span class="text-slate-400 font-normal">(ikon tab browser)</span></label>
                    <div class="w-full h-24 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden mb-2">
                        @if($newFavicon)
                            <img src="{{ $newFavicon->temporaryUrl() }}" class="w-14 h-14 object-contain">
                        @elseif($journal->favicon)
                            <img src="{{ Storage::disk('public')->url($journal->favicon) }}" class="w-14 h-14 object-contain">
                        @else
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                        @endif
                    </div>
                    <input wire:model="newFavicon" type="file" accept="image/*"
                           class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">PNG/ICO. Maks 512KB. 32×32px.</p>
                    @error('newFavicon')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Deskripsi Singkat Jurnal</label>
                <textarea wire:model="description" rows="2" placeholder="Deskripsi singkat untuk listing dan meta-description (maks 1000 karakter)..."
                          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
        </div>
    </div>

    {{-- Header Jurnal --}}
    <div
        x-data="{
            bgType:    @entangle('header_bg_type'),
            bgColor:   @entangle('header_bg_color'),
            bgColor2:  @entangle('header_bg_color2'),
            textLight: @entangle('header_text_light'),
            tagline:   @entangle('header_tagline'),
            get previewStyle() {
                if (this.bgType === 'color')    return 'background:' + this.bgColor;
                if (this.bgType === 'gradient') return 'background:linear-gradient(135deg,' + this.bgColor + ',' + this.bgColor2 + ')';
                if (this.bgType === 'image')    return 'background:linear-gradient(135deg,#1e3a8a,#4338ca)';
                return 'background:#ffffff;border:1px solid #e2e8f0';
            },
            get textStyle() {
                return (this.bgType !== 'default' && this.textLight) ? 'color:#fff' : 'color:#0f172a';
            }
        }"
        class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100" style="background:#fdf4ff;">
            <p class="text-xs font-bold text-purple-700 uppercase tracking-widest">Header Jurnal</p>
        </div>
        <div class="p-5 space-y-5">

            {{-- Preview --}}
            <div>
                <p class="text-xs text-slate-400 mb-2 font-medium">Pratinjau</p>
                <div class="rounded-xl overflow-hidden border border-slate-200 p-5 min-h-20 transition-all duration-300" :style="previewStyle">
                    <p class="font-black text-lg leading-tight" :style="textStyle">{{ $name ?: 'Nama Jurnal' }}</p>
                    <p class="text-sm mt-1 opacity-75" :style="textStyle" x-show="tagline" x-text="tagline"></p>
                    <div class="flex gap-2 mt-3">
                        <div class="h-1.5 w-16 rounded-full opacity-30" :style="'background:' + ((bgType!=='default'&&textLight)?'#fff':'#334155')"></div>
                        <div class="h-1.5 w-10 rounded-full opacity-20" :style="'background:' + ((bgType!=='default'&&textLight)?'#fff':'#334155')"></div>
                    </div>
                </div>
            </div>

            {{-- Tipe Background --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Tipe Background</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach([
                        ['default',  'Putih Default',  '⬜'],
                        ['color',    'Warna Solid',    '🟦'],
                        ['gradient', 'Gradien',        '🌈'],
                        ['image',    'Gambar/Banner',  '🖼️'],
                    ] as [$val, $lbl, $ico])
                    <button type="button" wire:click="$set('header_bg_type','{{ $val }}')" x-on:click="bgType='{{ $val }}'"
                            class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-xs font-semibold transition-all"
                            :class="bgType==='{{ $val }}' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-slate-200 text-slate-500 hover:border-slate-300 hover:bg-slate-50'">
                        <span>{{ $ico }}</span><span>{{ $lbl }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Warna --}}
            <div x-show="bgType==='color'||bgType==='gradient'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <span x-show="bgType==='gradient'">Warna Awal</span>
                        <span x-show="bgType==='color'">Warna Background</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="color" wire:model.live="header_bg_color" x-model="bgColor"
                               class="w-10 h-10 rounded-lg border border-slate-200 cursor-pointer p-0.5 shrink-0">
                        <input type="text" wire:model.live="header_bg_color" x-model="bgColor" placeholder="#1e3a8a"
                               class="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-mono">
                    </div>
                </div>
                <div x-show="bgType==='gradient'">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Warna Akhir</label>
                    <div class="flex gap-2">
                        <input type="color" wire:model.live="header_bg_color2" x-model="bgColor2"
                               class="w-10 h-10 rounded-lg border border-slate-200 cursor-pointer p-0.5 shrink-0">
                        <input type="text" wire:model.live="header_bg_color2" x-model="bgColor2" placeholder="#4338ca"
                               class="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-mono">
                    </div>
                </div>
            </div>

            {{-- Preset warna --}}
            <div x-show="bgType==='color'||bgType==='gradient'">
                <p class="text-xs text-slate-400 mb-2">Preset cepat:</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach([
                        ['#1e3a8a','#3730a3'],['#0f766e','#0891b2'],['#7c3aed','#db2777'],
                        ['#b91c1c','#dc2626'],['#065f46','#059669'],['#92400e','#d97706'],
                        ['#1e293b','#334155'],['#000000','#1e293b'],
                    ] as [$c1,$c2])
                    <button type="button"
                            x-on:click="bgColor='{{ $c1 }}';bgColor2='{{ $c2 }}';$wire.set('header_bg_color','{{ $c1 }}');$wire.set('header_bg_color2','{{ $c2 }}')"
                            class="w-8 h-8 rounded-lg border-2 border-white shadow-sm ring-1 ring-slate-200 hover:scale-110 transition-transform"
                            style="background:linear-gradient(135deg,{{ $c1 }},{{ $c2 }})"></button>
                    @endforeach
                </div>
            </div>

            {{-- Banner Upload --}}
            <div x-show="bgType==='image'">
                <label class="block text-xs font-semibold text-slate-600 mb-2">Gambar Banner Header</label>
                <div class="flex items-center gap-4">
                    <div class="w-28 h-16 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0">
                        @if($journal->homepage_image)
                        <img src="{{ asset('storage/'.$journal->homepage_image) }}" class="w-full h-full object-cover">
                        @else
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input wire:model="newHeaderBanner" type="file" accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
                        <p class="text-xs text-slate-400 mt-1">JPG/PNG. Maks 4MB. Rekomendasi: 1400×300px.</p>
                        @error('newHeaderBanner')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Teks Terang --}}
            <div x-show="bgType!=='default'"
                 class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50">
                <button type="button" wire:click="$toggle('header_text_light')"
                        x-on:click="textLight=!textLight"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                        :class="textLight ? 'bg-purple-500' : 'bg-slate-300'">
                    <span class="inline-block h-4 w-4 rounded-full bg-white shadow transition-transform"
                          :class="textLight ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Teks Terang (Putih)</p>
                    <p class="text-xs text-slate-400">Aktifkan untuk background gelap</p>
                </div>
            </div>

            {{-- Tagline --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tagline / Slogan <span class="text-slate-400 font-normal">(opsional)</span></label>
                <input wire:model.live="header_tagline" x-model="tagline" type="text"
                       placeholder="Contoh: Jurnal Ilmiah Bidang Kesehatan Masyarakat"
                       class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

        </div>
    </div>

    {{-- Background Halaman --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Background Halaman Jurnal</p>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center gap-3 flex-wrap">
                <input type="color" wire:model.live="site_bg_color"
                       class="w-10 h-10 rounded-lg border border-slate-200 cursor-pointer p-0.5 shrink-0">
                <input type="text" wire:model.live="site_bg_color" placeholder="#f1f5f9"
                       class="w-28 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400 font-mono">
                <div class="flex gap-1.5 flex-wrap">
                    @foreach(['#f8fafc','#f1f5f9','#fafaf9','#fff7ed','#f0fdf4','#eff6ff','#fdf4ff','#ffffff','#1e293b'] as $c)
                    <button type="button" wire:click="$set('site_bg_color','{{ $c }}')"
                            class="w-7 h-7 rounded-lg border-2 border-white shadow ring-1 ring-slate-200 hover:scale-110 transition-transform"
                            style="background:{{ $c }}" title="{{ $c }}"></button>
                    @endforeach
                </div>
            </div>
            <div class="rounded-xl px-4 py-3 text-xs text-slate-400 border border-slate-200"
                 style="background:{{ $site_bg_color }}">
                Pratinjau warna background halaman jurnal
            </div>
        </div>
    </div>

    @include('livewire.journal-manager._settings-save-btn')
</div>

{{-- ══════════════════════════════════════════
     TAB 3 — KONTEN
══════════════════════════════════════════ --}}
<div x-show="tab === 'konten'" class="space-y-5" style="display:none;">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Konten Jurnal</p>
        </div>
        <div class="p-5 space-y-4">
            @foreach([
                ['focus_scope',             'Fokus & Ruang Lingkup', 4, ''],
                ['about_journal',           'Tentang Jurnal',         4, ''],
                ['author_guidelines',       'Panduan Penulis',        4, 'Panduan lengkap untuk penulis yang akan menyerahkan naskah...'],
                ['reviewer_guidelines',     'Panduan Reviewer',       4, 'Panduan bagi reviewer dalam menilai naskah...'],
                ['ethics_statement',        'Pernyataan Etika',       3, ''],
                ['privacy_statement',       'Kebijakan Privasi',      3, ''],
                ['submission_acknowledgement','Ucapan Terima Kasih Pengiriman', 3, 'Pesan kepada penulis setelah naskah diterima...'],
            ] as [$field, $lbl, $rows, $ph])
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">{{ $lbl }}</label>
                <textarea wire:model="{{ $field }}" rows="{{ $rows }}" placeholder="{{ $ph }}"
                          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            @endforeach
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">
                    Daftar Periksa Naskah
                    <span class="font-normal text-slate-400 ml-1">— satu item per baris</span>
                </label>
                <textarea wire:model="submission_checklist" rows="5"
                          placeholder="Naskah ditulis menggunakan template jurnal&#10;Abstrak tidak melebihi 250 kata&#10;Daftar pustaka menggunakan gaya APA 7"
                          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none font-mono text-xs"></textarea>
            </div>
        </div>
    </div>

    {{-- Pengumuman --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-green-50">
            <p class="text-xs font-bold text-green-700 uppercase tracking-widest">Pengumuman</p>
        </div>
        <div class="p-5 space-y-4">
            <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                <input wire:model="announcements_enabled" type="checkbox" class="w-4 h-4 rounded text-green-600">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Aktifkan Fitur Pengumuman</p>
                    <p class="text-xs text-slate-500">Tampilkan pengumuman di beranda jurnal</p>
                </div>
            </label>
            @if($announcements_enabled)
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Intro Pengumuman</label>
                <textarea wire:model="announcements_intro" rows="3"
                          placeholder="Teks pengantar sebelum daftar pengumuman..."
                          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 resize-none"></textarea>
            </div>
            @endif
        </div>
    </div>

    {{-- Review & Lisensi --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Review & Lisensi</p>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Mode Review</label>
                    <select wire:model="review_mode" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="double_blind">Double Blind</option>
                        <option value="single_blind">Single Blind</option>
                        <option value="open">Open Review</option>
                        <option value="triple_blind">Triple Blind</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Durasi Review (mgg)</label>
                    <input wire:model="num_weeks_per_review" type="number" min="1" max="52"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Durasi Respons (mgg)</label>
                    <input wire:model="num_weeks_per_response" type="number" min="1" max="52"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tipe Lisensi</label>
                    <select wire:model="license_type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="cc_by">CC BY</option>
                        <option value="cc_by_nc">CC BY-NC</option>
                        <option value="cc_by_sa">CC BY-SA</option>
                        <option value="cc_by_nc_sa">CC BY-NC-SA</option>
                        <option value="cc_by_nd">CC BY-ND</option>
                        <option value="cc_by_nc_nd">CC BY-NC-ND</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input wire:model="requires_author_competinginterests" type="checkbox" class="w-4 h-4 mt-0.5 rounded text-blue-600">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Wajib: Competing Interests Penulis</p>
                        <p class="text-xs text-slate-500">Penulis harus isi pernyataan kepentingan</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input wire:model="requires_reviewer_competinginterests" type="checkbox" class="w-4 h-4 mt-0.5 rounded text-blue-600">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Wajib: Competing Interests Reviewer</p>
                        <p class="text-xs text-slate-500">Reviewer harus isi pernyataan kepentingan</p>
                    </div>
                </label>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pemegang Hak Cipta</label>
                <input wire:model="copyright_holder" type="text"
                       class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pernyataan Open Access</label>
                <textarea wire:model="open_access_statement" rows="2"
                          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pernyataan Hak Cipta</label>
                <textarea wire:model="copyright_notice" rows="2"
                          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
        </div>
    </div>

    @include('livewire.journal-manager._settings-save-btn')
</div>

{{-- ══════════════════════════════════════════
     TAB 4 — AKREDITASI
══════════════════════════════════════════ --}}
<div x-show="tab === 'akreditasi'" class="space-y-5" style="display:none;">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Akreditasi & Indeksasi</p>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Level SINTA</label>
                    <select wire:model="sinta_level" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Pilih —</option>
                        @foreach(['S1','S2','S3','S4','S5','S6'] as $l)
                        <option value="{{ $l }}">{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">SINTA ID</label>
                    <input wire:model="sinta_id" type="text" placeholder="ID di SINTA"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Skor SINTA</label>
                    <input wire:model="sinta_score" type="number" step="0.01" placeholder="3.45"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Skor SINTA 3 Thn</label>
                    <input wire:model="sinta_score_3yr" type="number" step="0.01" placeholder="2.80"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor SK Akreditasi</label>
                    <input wire:model="accreditation_no" type="text" placeholder="200/M/KPT/2020"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Periode Akreditasi</label>
                    <input wire:model="accreditation_period" type="text" placeholder="2020–2024"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">DOAJ ID / URL</label>
                    <input wire:model="doaj_id" type="text"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Garuda ID / URL</label>
                    <input wire:model="garuda_id" type="text"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    </div>

    {{-- Indexed By --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-blue-50">
            <p class="text-xs font-bold text-blue-700 uppercase tracking-widest">Indexed By</p>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-2">Tambah cepat:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach([
                        ['Google Scholar','https://scholar.google.com','#4285F4'],
                        ['GARUDA','https://garuda.kemdikbud.go.id','#c0392b'],
                        ['Crossref','https://www.crossref.org','#e67e22'],
                        ['Scopus','https://www.scopus.com','#ff6c00'],
                        ['DOAJ','https://doaj.org','#27ae60'],
                        ['Dimensions','https://www.dimensions.ai','#2980b9'],
                        ['Index Copernicus','https://indexcopernicus.com','#922b21'],
                        ['BASE','https://www.base-search.net','#1a237e'],
                        ['SINTA','https://sinta.kemdikbud.go.id','#7b1fa2'],
                        ['OpenAlex','https://openalex.org','#0277bd'],
                        ['Web of Science','https://clarivate.com','#1a5276'],
                        ['PKP Index','https://index.pkp.sfu.ca','#ad1457'],
                    ] as [$in,$iu,$ic])
                    @php $added = collect($indexed_by)->pluck('name')->contains($in); @endphp
                    <button type="button"
                            wire:click="addPresetIndexer('{{ $in }}','{{ $iu }}')"
                            {{ $added ? 'disabled' : '' }}
                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 rounded-lg font-bold text-white transition-all {{ $added ? 'opacity-50 cursor-not-allowed' : 'hover:opacity-90 hover:-translate-y-px shadow-sm' }}"
                            style="background:{{ $ic }}">
                        @if($added)✓@else+@endif {{ $in }}
                    </button>
                    @endforeach
                </div>
            </div>
            @if(!empty($indexed_by))
            <div class="space-y-2">
                @foreach($indexed_by as $ii => $item)
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    @if(!empty($item['logo']))
                    <img src="{{ asset('storage/'.$item['logo']) }}" class="w-9 h-9 object-contain rounded border border-slate-200 bg-white p-1 shrink-0">
                    @else
                    <div class="w-9 h-9 rounded border border-slate-200 bg-blue-50 flex items-center justify-center text-xs font-black text-blue-700 shrink-0">{{ strtoupper(substr($item['name'],0,2)) }}</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $item['name'] }}</p>
                        @if(!empty($item['url']))<p class="text-xs text-slate-400 truncate">{{ $item['url'] }}</p>@endif
                    </div>
                    <button type="button" wire:click="removeIndexer({{ $ii }})"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @endforeach
            </div>
            @endif
            <div class="border border-dashed border-blue-300 rounded-xl p-4 bg-blue-50/40 space-y-3">
                <p class="text-xs font-semibold text-blue-700">+ Tambah dengan logo kustom</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input wire:model="new_indexer_name" type="text" placeholder="Nama database/lembaga"
                           class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                    <input wire:model="new_indexer_url" type="text" placeholder="URL (opsional)"
                           class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <input wire:model="new_indexer_logo" type="file" accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 cursor-pointer">
                        @error('new_indexer_logo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="button" wire:click="addIndexer"
                            class="shrink-0 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Sponsor & Mitra --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-orange-50">
            <p class="text-xs font-bold text-orange-700 uppercase tracking-widest">Sponsor & Mitra Jurnal</p>
        </div>
        <div class="p-5 space-y-4">
            @if(!empty($sponsors))
            <div class="space-y-2">
                @foreach($sponsors as $si => $sp)
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    @if(!empty($sp['logo']))
                    <img src="{{ asset('storage/'.$sp['logo']) }}" class="w-11 h-9 object-contain rounded border border-slate-200 bg-white p-1 shrink-0">
                    @else
                    <div class="w-11 h-9 rounded border border-slate-200 bg-orange-50 flex items-center justify-center text-xs font-black text-orange-700 shrink-0">{{ strtoupper(substr($sp['name'],0,2)) }}</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $sp['name'] }}</p>
                        @if(!empty($sp['url']))<p class="text-xs text-slate-400 truncate">{{ $sp['url'] }}</p>@endif
                    </div>
                    <button type="button" wire:click="removeSponsor({{ $si }})"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @endforeach
            </div>
            @endif
            <div class="border border-dashed border-orange-300 rounded-xl p-4 bg-orange-50/40 space-y-3">
                <p class="text-xs font-semibold text-orange-700">+ Tambah Sponsor / Mitra</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input wire:model.live="new_sponsor_name" type="text" placeholder="Nama organisasi"
                           class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                    <input wire:model.live="new_sponsor_url" type="text" placeholder="Website (opsional)"
                           class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <input wire:model="new_sponsor_logo" type="file" accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-100 file:text-orange-700 hover:file:bg-orange-200 cursor-pointer">
                        @error('new_sponsor_logo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="button" wire:click="addSponsor"
                            class="shrink-0 px-4 py-2 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-600 transition-colors">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.journal-manager._settings-save-btn')
</div>

{{-- ══════════════════════════════════════════
     TAB 5 — OPERASIONAL
══════════════════════════════════════════ --}}
<div x-show="tab === 'operasional'" class="space-y-5" style="display:none;">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Kontak</p>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Kontak / Pengelola</label>
                    <input wire:model="contact_name" type="text"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Email Jurnal</label>
                    <input wire:model="email" type="email"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Telepon</label>
                <input wire:model="contact_phone" type="text" placeholder="+62..."
                       class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Korespondensi</label>
                <textarea wire:model="mailing_address" rows="3"
                          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-orange-50">
            <p class="text-xs font-bold text-orange-700 uppercase tracking-widest">APC & Kontak Pengelola</p>
        </div>
        <div class="p-5 space-y-4">
            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                <input wire:model="apc_enabled" type="checkbox" class="w-4 h-4 mt-0.5 rounded text-orange-500">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Article Processing Charge (APC)</p>
                    <p class="text-xs text-slate-500">Aktifkan jika jurnal memungut biaya dari penulis</p>
                </div>
            </label>
            @if($apc_enabled)
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah APC</label>
                    <input wire:model="apc_amount" type="number" min="0" step="0.01" placeholder="500000"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Mata Uang</label>
                    <select wire:model="apc_currency" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="IDR">IDR (Rupiah)</option>
                        <option value="USD">USD (Dollar)</option>
                        <option value="EUR">EUR (Euro)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Kebijakan Keringanan APC</label>
                <textarea wire:model="apc_waiver_policy" rows="3"
                          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
            </div>
            @endif
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor WA Pengelola (publik)</label>
                <input wire:model="wa_contact" type="text" placeholder="628123456789 (tanpa +)"
                       class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                <p class="text-xs text-slate-400 mt-1">Format internasional tanpa tanda + (contoh: 628123456789)</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-amber-50">
            <p class="text-xs font-bold text-amber-700 uppercase tracking-widest">Tanda Tangan Letter of Acceptance (LOA)</p>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Penandatangan</label>
                    <input wire:model="loa_signer_name" type="text" placeholder="Nama lengkap Editor-in-Chief"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan / Peran</label>
                    <input wire:model="loa_signer_title" type="text" placeholder="Contoh: Editor-in-Chief"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            @if($loa_signer_name || $loa_signer_title)
            <div class="px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl">
                <p class="text-xs font-bold text-amber-700 mb-1">Pratinjau tanda tangan:</p>
                <p class="text-sm font-semibold text-slate-800">{{ $loa_signer_name ?: '—' }}</p>
                <p class="text-xs text-amber-600">{{ $loa_signer_title ?: '—' }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-slate-50">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Lokasi & Dukungan Teknis</p>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Negara</label>
                    <input wire:model="country" type="text" placeholder="Indonesia"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Zona Waktu</label>
                    <select wire:model="timezone" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Asia/Jakarta">WIB — Asia/Jakarta (UTC+7)</option>
                        <option value="Asia/Makassar">WITA — Asia/Makassar (UTC+8)</option>
                        <option value="Asia/Jayapura">WIT — Asia/Jayapura (UTC+9)</option>
                        <option value="UTC">UTC</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Dukungan Teknis</label>
                    <input wire:model="tech_support_name" type="text"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Email Dukungan Teknis</label>
                    <input wire:model="tech_support_email" type="email"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tech_support_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    @include('livewire.journal-manager._settings-save-btn')
</div>

{{-- ══════════════════════════════════════════
     TAB 6 — INTEGRASI
══════════════════════════════════════════ --}}
<div x-show="tab === 'integrasi'" class="space-y-5" style="display:none;">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-pink-50">
            <p class="text-xs font-bold text-pink-700 uppercase tracking-widest">Turnitin (Cek Plagiarisme)</p>
        </div>
        <div class="p-5 space-y-4">
            <p class="text-xs text-slate-500">Kredensial dari dashboard Turnitin Anda untuk cek kemiripan otomatis.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Turnitin API Key</label>
                    <input wire:model="turnitin_api_key" type="password" placeholder="API Key dari Turnitin"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Account / Integration ID</label>
                    <input wire:model="turnitin_account_id" type="text" placeholder="Contoh: 123456"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400">
                </div>
            </div>
            @if($turnitin_api_key)
            <p class="flex items-center gap-1.5 text-xs text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded-lg">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                API Key sudah dikonfigurasi
            </p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-green-50">
            <p class="text-xs font-bold text-green-700 uppercase tracking-widest">WhatsApp API (Fonnte)</p>
        </div>
        <div class="p-5 space-y-4">
            <p class="text-xs text-slate-500">Daftarkan di <strong>fonnte.com</strong> untuk mendapatkan token. Digunakan untuk WA Blast.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Token Fonnte API</label>
                    <input wire:model="wa_api_token" type="password" placeholder="Token dari fonnte.com"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor WA Pengirim</label>
                    <input wire:model="wa_sender_number" type="text" placeholder="628123456789"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
            </div>
            @if($wa_api_token)
            <p class="flex items-center gap-1.5 text-xs text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded-lg">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Token sudah dikonfigurasi —
                <a href="{{ route('manager.wa-blast') }}" class="underline font-semibold ml-1">Buka WA Blast →</a>
            </p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-2.5 border-b border-slate-100 bg-purple-50">
            <p class="text-xs font-bold text-purple-700 uppercase tracking-widest">Custom HTML (Lanjutan)</p>
        </div>
        <div class="p-5 space-y-4">
            <p class="text-xs text-slate-500">HTML kustom untuk diinjeksi ke halaman jurnal — skrip analitik, badge khusus, dll.</p>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Custom Header HTML</label>
                <textarea wire:model="custom_header_html" rows="4"
                          placeholder="<!-- Contoh: Google Analytics, Hotjar, dll. -->"
                          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 resize-none font-mono text-xs"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Custom Footer HTML</label>
                <textarea wire:model="custom_footer_html" rows="4"
                          placeholder="<!-- HTML tambahan di bawah footer -->"
                          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 resize-none font-mono text-xs"></textarea>
            </div>
        </div>
    </div>

    @include('livewire.journal-manager._settings-save-btn')
</div>

</form>
@endif

</div>{{-- /content --}}
</div>{{-- /x-data --}}

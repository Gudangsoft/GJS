<div style="background:#f6f8fb;min-height:100vh;">

{{-- PAGE HEADER --}}
<div class="px-6 py-5 border-b border-slate-200 bg-white flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Letter of Acceptance (LOA)</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelola surat penerimaan naskah untuk jurnal {{ $journal?->name_abbrev }}</p>
    </div>
    <button wire:click="openCreate"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat LOA
    </button>
</div>

<div class="max-w-6xl mx-auto px-6 py-6">

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium flex items-center gap-2">
    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- FORM PANEL --}}
@if($showForm)
<div class="bg-white rounded-2xl border border-blue-200 shadow-sm mb-6 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100" style="background:#f0f5ff;">
        <h2 class="font-bold text-blue-900 text-base">
            {{ $editingId ? 'Edit LOA' : 'Buat LOA Baru' }}
        </h2>
        <button wire:click="$set('showForm',false)" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Submission pilih --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Submission (Naskah Diterima)</label>
            <select wire:model.live="submission_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none">
                <option value="">— Pilih submission —</option>
                @foreach($acceptedSubmissions as $sub)
                <option value="{{ $sub->id }}">#{{ $sub->id }} — {{ Str::limit($sub->title, 70) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Nomor LOA --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor LOA <span class="text-red-500">*</span></label>
            <input wire:model="loa_number" type="text" placeholder="LOA/JIKI/2026/001"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Status <span class="text-red-500">*</span></label>
            <select wire:model="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
                <option value="draft">Draft</option>
                <option value="issued">Diterbitkan</option>
                <option value="revoked">Dicabut</option>
            </select>
        </div>

        {{-- Judul --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Judul Artikel <span class="text-red-500">*</span></label>
            <input wire:model="article_title" type="text"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
        </div>

        {{-- Penulis (dinamis) --}}
        <div class="md:col-span-2">
            <div class="flex items-center justify-between mb-1">
                <label class="text-xs font-semibold text-slate-600">Penulis <span class="font-normal text-slate-400">(nama + afiliasi)</span></label>
                <button type="button" wire:click="addAuthor"
                        class="text-xs text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Penulis
                </button>
            </div>
            <div class="space-y-2">
                @foreach($authors as $i => $author)
                <div class="flex gap-2 items-start">
                    <span class="mt-2 text-xs font-bold text-slate-400 w-5 text-right shrink-0">{{ $i+1 }}.</span>
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input wire:model="authors.{{ $i }}.name" type="text" placeholder="Nama lengkap"
                               class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none w-full">
                        <input wire:model="authors.{{ $i }}.affiliation" type="text" placeholder="Institusi / Universitas"
                               class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none w-full">
                    </div>
                    <button type="button" wire:click="removeAuthor({{ $i }})"
                            class="mt-2 p-1 text-slate-300 hover:text-red-500 transition-colors shrink-0"
                            @if(count($authors) <= 1) disabled @endif>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tanggal penerimaan --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Penerimaan <span class="text-red-500">*</span></label>
            <input wire:model="acceptance_date" type="date"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
        </div>

        {{-- Estimasi terbit --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Estimasi Terbit</label>
            <input wire:model="expected_publication_date" type="date"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
        </div>

        {{-- Volume / Nomor / Tahun --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Volume</label>
            <input wire:model="volume" type="text" placeholder="e.g. 12"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor</label>
                <input wire:model="number" type="text" placeholder="e.g. 2"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun</label>
                <input wire:model="year" type="text" placeholder="{{ date('Y') }}"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
            </div>
        </div>

        {{-- Catatan --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan Tambahan</label>
            <textarea wire:model="notes" rows="2"
                      class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none resize-none"></textarea>
        </div>

        {{-- Tombol --}}
        <div class="md:col-span-2 flex gap-2 pt-1">
            <button wire:click="save"
                    class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                {{ $editingId ? 'Simpan Perubahan' : 'Buat LOA' }}
            </button>
            <button wire:click="$set('showForm',false)"
                    class="px-4 py-2 text-slate-600 text-sm font-medium rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>
@endif

{{-- TABS + SEARCH --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="flex items-center justify-between px-5 pt-4 pb-0 border-b border-slate-100 flex-wrap gap-3">
        {{-- Tabs --}}
        <div class="flex gap-1">
            @foreach([['all','Semua'],['draft','Draft'],['issued','Diterbitkan'],['revoked','Dicabut']] as [$key,$label])
            <button wire:click="$set('tab','{{ $key }}')"
                    class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-t-lg border-b-2 transition-all
                        {{ $tab === $key ? 'border-blue-600 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                {{ $label }}
                @if($counts[$key] > 0)
                <span class="text-xs font-black px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center
                    {{ $tab === $key ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                    {{ $counts[$key] }}
                </span>
                @endif
            </button>
            @endforeach
        </div>
        {{-- Search --}}
        <div class="relative pb-2">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul atau nomor LOA…"
                   class="pl-9 pr-4 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-200 outline-none w-56">
        </div>
    </div>

    {{-- LIST --}}
    @if($loas->isEmpty())
    <div class="text-center py-14 text-slate-400">
        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="font-medium text-sm">Belum ada LOA di kategori ini.</p>
        <button wire:click="openCreate" class="mt-2 text-blue-600 text-sm hover:underline">Buat LOA pertama →</button>
    </div>
    @else
    <div class="divide-y divide-slate-50">
        @foreach($loas as $loa)
        @php
        $statusStyle = match($loa->status) {
            'issued'  => 'bg-green-100 text-green-700',
            'draft'   => 'bg-amber-100 text-amber-700',
            'revoked' => 'bg-red-100 text-red-700',
            default   => 'bg-slate-100 text-slate-500',
        };
        $statusLabel = match($loa->status) {
            'issued'  => 'Diterbitkan',
            'draft'   => 'Draft',
            'revoked' => 'Dicabut',
            default   => $loa->status,
        };
        @endphp
        <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 transition-colors">
            {{-- Icon --}}
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                    <span class="text-xs font-bold text-slate-500">{{ $loa->loa_number }}</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $statusStyle }}">{{ $statusLabel }}</span>
                </div>
                <p class="font-semibold text-slate-900 text-sm truncate">{{ $loa->article_title }}</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ is_array($loa->authors) ? collect($loa->authors)->pluck('name')->filter()->implode(', ') : $loa->authors }}
                    &bull; Diterima: {{ $loa->acceptance_date?->format('d M Y') }}
                    @if($loa->volume) &bull; Vol. {{ $loa->volume }} @endif
                    @if($loa->number) No. {{ $loa->number }} @endif
                    @if($loa->year) ({{ $loa->year }}) @endif
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1.5 shrink-0">
                <a href="{{ route('loa.preview', $loa) }}" target="_blank"
                   class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Preview / Cetak
                </a>
                <button wire:click="openEdit({{ $loa->id }})"
                        class="text-xs font-semibold px-3 py-1.5 rounded-lg text-slate-600 bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-colors">
                    Edit
                </button>
                <button wire:click="delete({{ $loa->id }})"
                        wire:confirm="Hapus LOA {{ $loa->loa_number }}?"
                        class="text-xs font-semibold px-2.5 py-1.5 rounded-lg text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

</div>
</div>

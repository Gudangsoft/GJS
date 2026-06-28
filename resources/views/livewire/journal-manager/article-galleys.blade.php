<div style="background:#f6f8fb;min-height:100vh;">

{{-- ── Header ─────────────────────────────────────────────────────────────── --}}
<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <div class="flex items-center gap-3">
        <a href="{{ url()->previous() }}" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-900">Kelola Galley</h1>
            <p class="text-sm text-slate-500 mt-0.5 truncate max-w-xl">{{ Str::limit($article->submission->title ?? '—', 80) }}</p>
        </div>
    </div>
</div>

<div style="max-width:56rem;margin:0 auto;padding:1.5rem;">

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
@endif

{{-- ── Galley list ─────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-5">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Daftar Galley</h2>
        <button wire:click="openCreate"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:brightness-110 active:scale-95"
                style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah Galley
        </button>
    </div>

    @if($galleys->isEmpty())
    <div class="text-center py-12 text-slate-400">
        <svg style="width:2.5rem;height:2.5rem;margin:0 auto 0.5rem;opacity:.35;" fill="none" stroke="currentColor" stroke-width="1.25" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
        <p class="text-sm font-medium">Belum ada galley. Klik "Tambah Galley" untuk menambahkan.</p>
    </div>
    @else
    <div class="divide-y divide-slate-100">
        @foreach($galleys as $g)
        @php
            $typeLabel = $g->isHtml() ? 'HTML' : (str_contains(strtolower($g->label),'pdf') ? 'PDF' : 'Lainnya');
            $typeColor = $g->isHtml() ? ['#0891b2','#ecfeff'] : (str_contains(strtolower($g->label),'pdf') ? ['#dc2626','#fff1f2'] : ['#6366f1','#eef2ff']);
        @endphp
        <div class="flex items-center gap-4 px-5 py-4">
            {{-- Type badge --}}
            <span class="text-xs font-bold px-2 py-0.5 rounded-full shrink-0"
                  style="background:{{ $typeColor[1] }};color:{{ $typeColor[0] }};">
                {{ $typeLabel }}
            </span>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-slate-800">{{ $g->label }}</span>
                    @if(!$g->is_approved)
                    <span class="text-xs px-1.5 py-0.5 rounded" style="background:#fef9c3;color:#a16207;">Draft</span>
                    @endif
                </div>
                <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-3">
                    <span>Urutan: {{ (int)$g->sequence }}</span>
                    <span>{{ $g->locale === 'id' ? 'Indonesia' : 'English' }}</span>
                    @if($g->html_content)
                    <span style="color:#0891b2;">HTML {{ Str::wordCount(strip_tags($g->html_content)) }} kata</span>
                    @elseif($g->remote_url)
                    <span class="truncate max-w-xs">{{ $g->remote_url }}</span>
                    @elseif($g->submission_file_id)
                    <span>File upload</span>
                    @else
                    <span style="color:#94a3b8;">Belum ada konten</span>
                    @endif
                    @if($g->views > 0)
                    <span>{{ number_format($g->views) }}× dibaca</span>
                    @endif
                </div>
            </div>

            {{-- Preview link --}}
            @if($g->hasContent())
            <a href="{{ route('journals.articles.galley.view', [$journal->slug ?? 'preview', $article->id, $g->id]) }}"
               target="_blank"
               class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
               style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;">
                Preview
            </a>
            @endif

            {{-- Edit --}}
            <button wire:click="openEdit({{ $g->id }})"
                    class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                    style="background:#f8fafc;color:#475569;border:1px solid #e2e8f0;">
                Edit
            </button>

            {{-- Delete --}}
            <button wire:click="delete({{ $g->id }})"
                    wire:confirm="Hapus galley '{{ $g->label }}'? Tindakan ini tidak bisa dibatalkan."
                    class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                    style="background:#fff1f2;color:#dc2626;border:1px solid #fecaca;">
                Hapus
            </button>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ── Form modal ──────────────────────────────────────────────────────────── --}}
@if($showForm)
<div class="fixed inset-0 z-50 flex items-start justify-center pt-12 px-4"
     style="background:rgba(15,23,42,.6);backdrop-filter:blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col overflow-hidden">
        {{-- Modal header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">
                {{ $editId ? 'Edit Galley' : 'Tambah Galley Baru' }}
            </h3>
            <button wire:click="$set('showForm', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal body --}}
        <div class="overflow-y-auto p-6 flex-1">
            <div class="grid grid-cols-2 gap-4 mb-4">
                {{-- Label --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Label <span class="text-red-500">*</span></label>
                    <input wire:model="label" type="text" placeholder="PDF, HTML, ePUB..."
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('label') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Locale --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Bahasa</label>
                    <select wire:model="locale"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="id">Indonesia</option>
                        <option value="en">English</option>
                    </select>
                </div>

                {{-- Sequence --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Urutan Tampil</label>
                    <input wire:model="sequence" type="number" min="0"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Status --}}
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="isApproved" type="checkbox"
                               class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                        <span class="text-sm font-semibold text-slate-700">Galley disetujui (tampil ke publik)</span>
                    </label>
                </div>
            </div>

            {{-- Remote URL --}}
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">URL Eksternal</label>
                <input wire:model="remoteUrl" type="url" placeholder="https://..."
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('remoteUrl') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-slate-400">Isi jika galley dihosting di URL eksternal (kosongkan jika menggunakan konten HTML di bawah).</p>
            </div>

            {{-- HTML Content --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">
                    Konten HTML
                    <span class="ml-1 font-normal normal-case text-slate-400">(untuk galley HTML — isi body artikel dalam format HTML)</span>
                </label>
                <textarea wire:model="htmlContent"
                          rows="20"
                          placeholder="<h2>Pendahuluan</h2>&#10;<p>Isi artikel dalam format HTML...</p>&#10;&#10;<h2>Metode</h2>&#10;<p>...</p>"
                          class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y"
                          style="min-height:16rem;"></textarea>
                <p class="mt-1 text-xs text-slate-400">
                    Masukkan konten artikel dalam HTML. Gunakan: <code class="bg-slate-100 px-1 rounded">&lt;h2&gt;</code> untuk judul bagian,
                    <code class="bg-slate-100 px-1 rounded">&lt;p&gt;</code> untuk paragraf,
                    <code class="bg-slate-100 px-1 rounded">&lt;figure&gt;&lt;img&gt;&lt;figcaption&gt;</code> untuk gambar,
                    <code class="bg-slate-100 px-1 rounded">&lt;table&gt;</code> untuk tabel.
                </p>
            </div>
        </div>

        {{-- Modal footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50">
            <button wire:click="$set('showForm', false)"
                    class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                Batal
            </button>
            <button wire:click="save"
                    class="px-5 py-2 text-sm font-semibold text-white rounded-xl transition-all hover:brightness-110 active:scale-95"
                    style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">
                Simpan Galley
            </button>
        </div>
    </div>
</div>
@endif

</div>
</div>

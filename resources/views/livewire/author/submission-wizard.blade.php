<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Kirim Naskah Baru</h1>
        <p class="text-sm text-slate-500 mt-1">Langkah {{ $step }} dari {{ $totalSteps }}</p>
    </div>

    {{-- Progress bar --}}
    <div class="flex items-center gap-2 mb-8">
        @for($i = 1; $i <= $totalSteps; $i++)
        <div class="flex-1 h-1.5 rounded-full {{ $i <= $step ? 'bg-blue-600' : 'bg-slate-200' }}"></div>
        @endfor
    </div>

    {{-- Step labels --}}
    <div class="grid grid-cols-5 gap-1 mb-8 text-center">
        @foreach(['Jurnal','Metadata','Penulis','Detail','Konfirmasi'] as $i => $label)
        <div wire:click="goToStep({{ $i + 1 }})"
             class="cursor-pointer">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold mx-auto mb-1
                {{ $step > $i + 1 ? 'bg-blue-600 text-white' : ($step === $i + 1 ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-slate-200 text-slate-500') }}">
                @if($step > $i + 1)
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @else
                {{ $i + 1 }}
                @endif
            </div>
            <p class="text-xs {{ $step === $i + 1 ? 'text-blue-700 font-semibold' : 'text-slate-400' }}">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-7">

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm font-semibold text-red-800 mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                <li class="text-sm text-red-700">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ── STEP 1: Journal & Section ──────────────────────────────────── --}}
        @if($step === 1)
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Pilih Jurnal & Seksi</h2>
            <p class="text-sm text-slate-500 mb-6">Pilih jurnal tujuan dan seksi naskah Anda.</p>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jurnal Tujuan *</label>
                    <div class="space-y-2">
                        @foreach($journals as $j)
                        <label class="flex items-start gap-3 p-3 border rounded-xl cursor-pointer transition-all
                            {{ $journalId === $j->id ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300' }}">
                            <input type="radio" wire:model.live="journalId" value="{{ $j->id }}" class="mt-0.5 text-blue-600 focus:ring-blue-500">
                            <div>
                                <p class="font-medium text-slate-900 text-sm">{{ $j->name }}</p>
                                <p class="text-xs text-slate-500">{{ $j->publisher }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('journalId')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                @if($journalId && $sections->isNotEmpty())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Seksi Naskah *</label>
                    <select wire:model="sectionId"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih seksi --</option>
                        @foreach($sections as $sec)
                        <option value="{{ $sec->id }}">{{ $sec->title }}</option>
                        @endforeach
                    </select>
                    @error('sectionId')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                @elseif($journalId)
                <p class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-lg p-3">
                    Jurnal ini belum memiliki seksi aktif. Hubungi editor jurnal.
                </p>
                @endif
            </div>
        </div>

        {{-- ── STEP 2: Metadata ───────────────────────────────────────────── --}}
        @elseif($step === 2)
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Metadata Naskah</h2>
            <p class="text-sm text-slate-500 mb-6">Isi informasi dasar tentang naskah Anda.</p>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Judul Naskah *</label>
                    <input type="text" wire:model="title" placeholder="Judul lengkap naskah..."
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror">
                    @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Subjudul <span class="text-slate-400">(opsional)</span></label>
                    <input type="text" wire:model="subtitle" placeholder="Subjudul naskah..."
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Abstrak *</label>
                    <textarea wire:model="abstract" rows="6" placeholder="Tulis abstrak naskah (150–300 kata)..."
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y @error('abstract') border-red-400 @enderror"></textarea>
                    <p class="mt-1 text-xs text-slate-400">{{ str_word_count($abstract) }} kata</p>
                    @error('abstract')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kata Kunci <span class="text-slate-400">(pisahkan dengan koma)</span></label>
                    <input type="text" wire:model="keywordsInput" placeholder="kata kunci 1, kata kunci 2, ..."
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-400">Minimal 3 kata kunci</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Bahasa Naskah</label>
                    <select wire:model="locale"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="id">Bahasa Indonesia</option>
                        <option value="en">English</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ── STEP 3: Contributors ───────────────────────────────────────── --}}
        @elseif($step === 3)
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Data Penulis</h2>
            <p class="text-sm text-slate-500 mb-6">Tambahkan semua penulis naskah ini beserta afiliasinya.</p>

            <div class="space-y-4">
                @foreach($contributors as $i => $contributor)
                <div class="border border-slate-200 rounded-xl p-4 {{ $i === 0 ? 'bg-blue-50 border-blue-200' : 'bg-white' }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-slate-700">Penulis {{ $i + 1 }}</span>
                            @if($contributor['primary_contact'])
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Korespondensi</span>
                            @endif
                        </div>
                        @if($i > 0)
                        <button wire:click="removeContributor({{ $i }})"
                                type="button"
                                class="text-xs text-red-500 hover:text-red-700">
                            Hapus
                        </button>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nama Depan *</label>
                            <input type="text" wire:model="contributors.{{ $i }}.first_name"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error("contributors.$i.first_name")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nama Belakang *</label>
                            <input type="text" wire:model="contributors.{{ $i }}.last_name"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error("contributors.$i.last_name")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Email *</label>
                            <input type="email" wire:model="contributors.{{ $i }}.email"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error("contributors.$i.email")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Afiliasi</label>
                            <input type="text" wire:model="contributors.{{ $i }}.affiliation"
                                   placeholder="Universitas / Institusi"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                @endforeach

                <button wire:click="addContributor" type="button"
                        class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Penulis
                </button>
            </div>
        </div>

        {{-- ── STEP 4: Details + File Upload ─────────────────────────────── --}}
        @elseif($step === 4)
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Unggah Naskah & Detail</h2>
            <p class="text-sm text-slate-500 mb-6">Unggah file naskah utama dan isi informasi tambahan.</p>

            <div class="space-y-5">
                {{-- File Upload --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        File Naskah <span class="text-slate-400">(PDF/DOC/DOCX, maks. 20MB)</span>
                    </label>
                    <div x-data="{ isDragging: false }"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="isDragging = false"
                         :class="isDragging ? 'border-blue-400 bg-blue-50' : 'border-slate-300 hover:border-blue-400'"
                         class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                         onclick="document.getElementById('manuscriptFileInput').click()">

                        @if($manuscriptFile)
                        <div class="flex items-center justify-center gap-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div class="text-left">
                                <p class="text-sm font-medium text-slate-900">{{ $manuscriptFile->getClientOriginalName() }}</p>
                                <p class="text-xs text-slate-500">{{ number_format($manuscriptFile->getSize() / 1024, 0) }} KB</p>
                            </div>
                            <button wire:click="$set('manuscriptFile', null)" type="button"
                                    class="ml-2 text-xs text-red-500 hover:text-red-700" onclick="event.stopPropagation()">
                                Ganti
                            </button>
                        </div>
                        @else
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-slate-600 font-medium">Klik atau seret file ke sini</p>
                        <p class="text-xs text-slate-400 mt-1">PDF, DOC, DOCX hingga 20MB</p>
                        @endif

                        <input id="manuscriptFileInput" type="file" wire:model="manuscriptFile"
                               accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                               class="hidden">
                    </div>
                    <div wire:loading wire:target="manuscriptFile" class="mt-2 text-xs text-blue-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Mengunggah...
                    </div>
                    @error('manuscriptFile')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Surat Pengantar <span class="text-slate-400">(opsional)</span>
                    </label>
                    <textarea wire:model="coverLetter" rows="4"
                              placeholder="Sampaikan pesan kepada editor jurnal..."
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Pernyataan Konflik Kepentingan
                    </label>
                    <textarea wire:model="competingInterests" rows="3"
                              placeholder="Nyatakan apakah ada konflik kepentingan dalam penelitian ini..."
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y"></textarea>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-sm text-blue-800">
                        <strong>Perhatian:</strong> Dengan mengirimkan naskah ini, Anda menyatakan bahwa:
                    </p>
                    <ul class="mt-2 space-y-1 text-sm text-blue-700 list-disc list-inside">
                        <li>Naskah ini merupakan karya orisinil dan belum diterbitkan sebelumnya</li>
                        <li>Semua penulis telah menyetujui pengiriman naskah ini</li>
                        <li>Anda tidak mengirimkan naskah ini ke jurnal lain secara bersamaan</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ── STEP 5: Review & Submit ────────────────────────────────────── --}}
        @elseif($step === 5)
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Konfirmasi & Kirim</h2>
            <p class="text-sm text-slate-500 mb-6">Periksa kembali semua informasi sebelum mengirimkan naskah.</p>

            <div class="space-y-4 text-sm">
                {{-- Summary --}}
                <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                    @if($journalId)
                    @php $selectedJournal = $journals->find($journalId) @endphp
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jurnal</span>
                        <span class="font-medium text-slate-900">{{ $selectedJournal?->name }}</span>
                    </div>
                    @endif
                    @if($sectionId)
                    @php $selectedSection = $sections->find($sectionId) @endphp
                    <div class="flex justify-between">
                        <span class="text-slate-500">Seksi</span>
                        <span class="font-medium text-slate-900">{{ $selectedSection?->title }}</span>
                    </div>
                    @endif
                    <div class="pt-2 border-t border-slate-200">
                        <p class="text-slate-500 mb-1">Judul</p>
                        <p class="font-medium text-slate-900">{{ $title }}</p>
                        @if($subtitle)<p class="text-slate-600 mt-0.5">{{ $subtitle }}</p>@endif
                    </div>
                    <div class="pt-2 border-t border-slate-200">
                        <p class="text-slate-500 mb-1">Penulis ({{ count($contributors) }})</p>
                        @foreach($contributors as $c)
                        <p class="text-slate-800">
                            {{ $c['first_name'] }} {{ $c['last_name'] }}
                            @if($c['affiliation']) <span class="text-slate-500">— {{ $c['affiliation'] }}</span>@endif
                            @if($c['primary_contact']) <span class="text-blue-600 text-xs">(korespondensi)</span>@endif
                        </p>
                        @endforeach
                    </div>
                    @if($keywordsInput)
                    <div class="pt-2 border-t border-slate-200">
                        <p class="text-slate-500 mb-1">Kata Kunci</p>
                        <p class="text-slate-800">{{ $keywordsInput }}</p>
                    </div>
                    @endif
                    @if($manuscriptFile)
                    <div class="pt-2 border-t border-slate-200">
                        <p class="text-slate-500 mb-1">File Naskah</p>
                        <p class="text-slate-800 flex items-center gap-1">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $manuscriptFile->getClientOriginalName() }}
                        </p>
                    </div>
                    @else
                    <div class="pt-2 border-t border-slate-200">
                        <p class="text-slate-500 mb-1">File Naskah</p>
                        <p class="text-amber-600 text-sm">Belum diunggah (opsional)</p>
                    </div>
                    @endif
                </div>

                <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <p class="text-sm text-amber-800">
                        Setelah dikirim, naskah akan masuk ke antrian editorial. Anda akan menerima notifikasi dari editor.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Navigation Buttons --}}
    <div class="flex items-center justify-between mt-6">
        @if($step > 1)
        <button wire:click="back" type="button"
                class="flex items-center gap-2 px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </button>
        @else
        <div></div>
        @endif

        @if($step < $totalSteps)
        <button wire:click="next" type="button"
                class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
            Selanjutnya
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        @else
        <button wire:click="submit" type="button"
                wire:loading.attr="disabled"
                class="flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition-colors">
            <span wire:loading.remove>
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Kirim Naskah
            </span>
            <span wire:loading>Mengirim...</span>
        </button>
        @endif
    </div>
</div>

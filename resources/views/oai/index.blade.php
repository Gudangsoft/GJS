@push('head')
<meta name="robots" content="noindex, follow">
@endpush

<x-layouts.app :title="'OAI-PMH 2.0 — ' . config('app.name')">

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Header ──────────────────────────────────────────────────────────────── --}}
    <div class="mb-8">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="5" cy="19" r="1.5" fill="currentColor" stroke="none"/>
                    <path d="M4 11a9 9 0 0 1 9 9"/>
                    <path d="M4 4a16 16 0 0 1 16 16"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-slate-900">OAI-PMH 2.0 Repository</h1>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Aktif
                    </span>
                </div>
                <p class="text-slate-500 mt-1 text-sm">{{ $repositoryName }}</p>
            </div>
        </div>

        <div class="mt-5 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-900 leading-relaxed">
            <strong>Informasi untuk Harvester.</strong>
            Repository ini mematuhi protokol
            <a href="https://www.openarchives.org/OAI/openarchivesprotocol.html" target="_blank" rel="noopener" class="underline font-medium">Open Archives Initiative Protocol for Metadata Harvesting (OAI-PMH) 2.0</a>.
            Gunakan Base URL di bawah pada konfigurasi software harvester (DOAJ, BASE, OpenDOAR, CORE, PKP Index, dll.).
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Kolom Kiri (2/3) ────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Repository Information --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h2 class="text-sm font-semibold text-slate-700">Informasi Repository</h2>
                </div>
                <dl class="divide-y divide-slate-100">
                    @foreach([
                        ['Nama Repository',     $repositoryName,  false],
                        ['Base URL',            $baseUrl,         true],
                        ['Versi Protokol',      'OAI-PMH 2.0',   false],
                        ['Email Administrator', $adminEmail,      false],
                        ['Datestamp Terlama',   $earliestDate,    false],
                        ['Deleted Record',      'no',             false],
                        ['Granularitas',        'YYYY-MM-DDThh:mm:ssZ', false],
                    ] as [$label, $value, $mono])
                    <div class="px-5 py-3 flex flex-col sm:flex-row sm:items-baseline gap-1 sm:gap-6">
                        <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide sm:w-44 shrink-0">{{ $label }}</dt>
                        <dd class="text-sm text-slate-800 {{ $mono ? 'font-mono text-blue-700 break-all' : '' }}">{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>

            {{-- Metadata Formats --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <h2 class="text-sm font-semibold text-slate-700">Format Metadata Tersedia</h2>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-blue-50 border border-blue-100">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shrink-0 shadow-sm">
                            <span class="text-white text-xs font-black tracking-tight">DC</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">oai_dc &mdash; Dublin Core</p>
                            <p class="text-xs text-slate-500 mt-0.5 font-mono truncate">http://www.openarchives.org/OAI/2.0/oai_dc/</p>
                            <p class="text-xs text-slate-500 mt-0.5 font-mono truncate">Schema: http://www.openarchives.org/OAI/2.0/oai_dc.xsd</p>
                        </div>
                        <a href="{{ $baseUrl }}?verb=ListMetadataFormats" target="_blank"
                           class="shrink-0 text-xs text-blue-600 hover:text-blue-800 hover:underline font-medium flex items-center gap-1">
                            Lihat XML
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Available Sets --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <h2 class="text-sm font-semibold text-slate-700">Set Tersedia (Jurnal)</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full font-medium">{{ $journals->count() }} jurnal</span>
                        <a href="{{ $baseUrl }}?verb=ListSets" target="_blank"
                           class="text-xs text-blue-600 hover:underline font-medium">ListSets XML →</a>
                    </div>
                </div>

                @if($journals->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-slate-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Belum ada jurnal aktif.
                </div>
                @else
                <div class="divide-y divide-slate-100">
                    @foreach($journals as $i => $journal)
                    <div class="px-5 py-4 flex items-start gap-4 hover:bg-slate-50/70 transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-xs font-bold text-slate-500 shrink-0 mt-0.5">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $journal->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-600">journal:{{ $journal->slug }}</span>
                                @if($journal->issn_online)
                                <span class="ml-2 text-slate-400">e-ISSN {{ $journal->issn_online }}</span>
                                @endif
                                @if($journal->issn_print)
                                <span class="ml-1 text-slate-400">p-ISSN {{ $journal->issn_print }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <a href="{{ $baseUrl }}?verb=ListIdentifiers&metadataPrefix=oai_dc&set=journal:{{ $journal->slug }}"
                               target="_blank"
                               class="text-xs text-slate-500 hover:text-blue-600 hover:underline transition-colors">Identifiers</a>
                            <a href="{{ $baseUrl }}?verb=ListRecords&metadataPrefix=oai_dc&set=journal:{{ $journal->slug }}"
                               target="_blank"
                               class="text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline transition-colors flex items-center gap-0.5">
                                Records
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Example Requests --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <h2 class="text-sm font-semibold text-slate-700">Contoh Request OAI Verb</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @php
                        $examples = [
                            [
                                'verb'   => 'Identify',
                                'desc'   => 'Informasi dasar repository',
                                'badge'  => 'bg-blue-100 text-blue-700 border-blue-200',
                                'params' => ['verb' => 'Identify'],
                            ],
                            [
                                'verb'   => 'ListMetadataFormats',
                                'desc'   => 'Format metadata yang didukung',
                                'badge'  => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                'params' => ['verb' => 'ListMetadataFormats'],
                            ],
                            [
                                'verb'   => 'ListSets',
                                'desc'   => 'Daftar set / koleksi jurnal',
                                'badge'  => 'bg-violet-100 text-violet-700 border-violet-200',
                                'params' => ['verb' => 'ListSets'],
                            ],
                            [
                                'verb'   => 'ListIdentifiers',
                                'desc'   => 'Identifier semua artikel (tanpa metadata lengkap)',
                                'badge'  => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'params' => ['verb' => 'ListIdentifiers', 'metadataPrefix' => 'oai_dc'],
                            ],
                            [
                                'verb'   => 'ListRecords',
                                'desc'   => 'Semua record artikel dengan metadata Dublin Core',
                                'badge'  => 'bg-amber-100 text-amber-700 border-amber-200',
                                'params' => ['verb' => 'ListRecords', 'metadataPrefix' => 'oai_dc'],
                            ],
                            [
                                'verb'   => 'GetRecord',
                                'desc'   => 'Satu record artikel berdasarkan identifier',
                                'badge'  => 'bg-rose-100 text-rose-700 border-rose-200',
                                'params' => ['verb' => 'GetRecord', 'metadataPrefix' => 'oai_dc', 'identifier' => 'oai:' . parse_url(config('app.url'), PHP_URL_HOST) . ':article:1'],
                            ],
                        ];
                    @endphp

                    @foreach($examples as $ex)
                    <div class="px-5 py-3.5 flex items-center gap-4 hover:bg-slate-50/70 transition-colors group">
                        <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-md border {{ $ex['badge'] }} shrink-0 whitespace-nowrap">
                            {{ $ex['verb'] }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-600">{{ $ex['desc'] }}</p>
                            <p class="text-xs font-mono text-slate-400 mt-0.5 truncate">
                                ?{{ http_build_query($ex['params']) }}
                            </p>
                        </div>
                        <a href="{{ $baseUrl }}?{{ http_build_query($ex['params']) }}"
                           target="_blank"
                           class="shrink-0 inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg transition-colors">
                            Coba
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── Kolom Kanan (1/3) ───────────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Base URL --}}
            <div class="bg-slate-900 rounded-2xl p-5 shadow-lg">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">OAI Base URL</p>
                <p class="font-mono text-sm text-emerald-400 break-all leading-relaxed">{{ $baseUrl }}</p>
                <div class="mt-3 pt-3 border-t border-slate-700">
                    <p class="text-xs text-slate-500 leading-relaxed">Salin URL ini ke form pendaftaran pada layanan pengindeks atau harvester OAI.</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-4">Statistik Repository</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900 leading-none tabular-nums">{{ number_format($articleCount) }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">Artikel Terindeks</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900 leading-none tabular-nums">{{ $journals->count() }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">Jurnal Aktif</p>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wide font-medium mb-1">Datestamp Terlama</p>
                        <p class="text-sm font-semibold text-slate-700 font-mono">{{ $earliestDate }}</p>
                    </div>
                </div>
            </div>

            {{-- Compliance --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Kepatuhan Protokol</h3>
                <ul class="space-y-2.5">
                    @foreach([
                        ['OAI-PMH 2.0',                true],
                        ['Dublin Core (oai_dc)',        true],
                        ['Selective Harvesting (set)',  true],
                        ['GetRecord per artikel',       true],
                        ['Crossref DOI',               true],
                        ['Resumption Token',           false],
                        ['METS / MARC format',         false],
                    ] as [$feat, $ok])
                    <li class="flex items-center gap-2.5 text-xs">
                        @if($ok)
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">{{ $feat }}</span>
                        @else
                        <svg class="w-4 h-4 text-slate-300 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-400">{{ $feat }}</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Register to Harvesters --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Daftarkan ke Pengindeks</h3>
                <p class="text-xs text-slate-400 mb-3 leading-relaxed">Gunakan Base URL di atas untuk mendaftarkan repository ke layanan berikut.</p>
                <ul class="space-y-2.5">
                    @foreach([
                        ['DOAJ',       'https://doaj.org/apply/journal',                      'violet'],
                        ['BASE',       'https://www.base-search.net/about/en/suggest.php',    'blue'],
                        ['OpenDOAR',   'https://v2.sherpa.ac.uk/opendoar/suggest.html',       'emerald'],
                        ['CORE',       'https://core.ac.uk/about/data-providers',             'amber'],
                        ['PKP Index',  'https://index.pkp.sfu.ca/',                           'rose'],
                        ['Garuda',     'https://garuda.kemdikbud.go.id/',                     'orange'],
                    ] as [$name, $url, $color])
                    <li>
                        <a href="{{ $url }}" target="_blank" rel="noopener"
                           class="flex items-center gap-2 text-xs text-slate-600 hover:text-slate-900 transition-colors group">
                            <span class="w-2 h-2 rounded-full bg-{{ $color }}-400 shrink-0"></span>
                            <span class="group-hover:underline flex-1">{{ $name }}</span>
                            <svg class="w-3 h-3 text-slate-300 group-hover:text-slate-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>

</div>

</x-layouts.app>

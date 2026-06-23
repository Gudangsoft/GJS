@push('head')
<meta name="robots" content="noindex, follow">
@endpush

<x-layouts.app :title="$repositoryName . ' — OAI-PMH 2.0'">

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('journals.home', $journal->slug) }}" class="hover:text-blue-600 transition-colors truncate max-w-xs">{{ $journal->name }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-700 font-medium">OAI-PMH</span>
    </nav>

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-start gap-4">

            {{-- Journal logo placeholder --}}
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shrink-0 shadow-md">
                <span class="text-white font-black text-lg tracking-tight">
                    {{ strtoupper(substr($journal->name, 0, 2)) }}
                </span>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-slate-900 leading-tight">{{ $journal->name }}</h1>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        OAI-PMH Aktif
                    </span>
                </div>
                <p class="text-slate-500 text-sm mt-0.5">{{ $repositoryName }}</p>
                @if($journal->issn_online || $journal->issn_print)
                <div class="flex items-center gap-3 mt-1.5 text-xs text-slate-400">
                    @if($journal->issn_online)<span>e-ISSN: <strong class="text-slate-600">{{ $journal->issn_online }}</strong></span>@endif
                    @if($journal->issn_print)<span>p-ISSN: <strong class="text-slate-600">{{ $journal->issn_print }}</strong></span>@endif
                    @if($journal->publisher)<span>&middot; {{ $journal->publisher }}</span>@endif
                </div>
                @endif
            </div>
        </div>

        <div class="mt-5 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-900 leading-relaxed">
            <strong>Informasi untuk Harvester.</strong>
            Endpoint ini menyediakan akses metadata artikel
            <strong>{{ $journal->name }}</strong>
            melalui protokol
            <a href="https://www.openarchives.org/OAI/openarchivesprotocol.html" target="_blank" rel="noopener" class="underline font-medium">OAI-PMH 2.0</a>.
            Gunakan Base URL di bawah untuk mendaftarkan jurnal ini ke layanan pengindeks.
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Kolom Kiri ──────────────────────────────────────────────────────── --}}
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
                        ['Set Spec',            'journal:' . $journal->slug, true],
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

            {{-- OAI Verb Examples (filtered to this journal) --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <h2 class="text-sm font-semibold text-slate-700">Request OAI — Khusus Jurnal Ini</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @php
                        $oai = $baseUrl;
                        $set = 'journal:' . $journal->slug;
                        $sampleId = 'oai:' . parse_url(config('app.url'), PHP_URL_HOST) . ':article:1';
                        $examples = [
                            [
                                'verb'   => 'Identify',
                                'desc'   => 'Informasi repository jurnal ini',
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
                                'desc'   => 'Set / koleksi dalam repository ini',
                                'badge'  => 'bg-violet-100 text-violet-700 border-violet-200',
                                'params' => ['verb' => 'ListSets'],
                            ],
                            [
                                'verb'   => 'ListIdentifiers',
                                'desc'   => 'Identifier artikel jurnal ini',
                                'badge'  => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'params' => ['verb' => 'ListIdentifiers', 'metadataPrefix' => 'oai_dc', 'set' => $set],
                            ],
                            [
                                'verb'   => 'ListRecords',
                                'desc'   => 'Semua record artikel dengan metadata Dublin Core',
                                'badge'  => 'bg-amber-100 text-amber-700 border-amber-200',
                                'params' => ['verb' => 'ListRecords', 'metadataPrefix' => 'oai_dc', 'set' => $set],
                            ],
                            [
                                'verb'   => 'GetRecord',
                                'desc'   => 'Satu record berdasarkan identifier OAI',
                                'badge'  => 'bg-rose-100 text-rose-700 border-rose-200',
                                'params' => ['verb' => 'GetRecord', 'metadataPrefix' => 'oai_dc', 'identifier' => $sampleId],
                            ],
                        ];
                    @endphp

                    @foreach($examples as $ex)
                    <div class="px-5 py-3.5 flex items-center gap-4 hover:bg-slate-50/70 transition-colors">
                        <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-md border {{ $ex['badge'] }} shrink-0 whitespace-nowrap">
                            {{ $ex['verb'] }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-600">{{ $ex['desc'] }}</p>
                            <p class="text-xs font-mono text-slate-400 mt-0.5 truncate">?{{ http_build_query($ex['params']) }}</p>
                        </div>
                        <a href="{{ $oai }}?{{ http_build_query($ex['params']) }}"
                           target="_blank"
                           class="shrink-0 inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg transition-colors">
                            Coba
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Metadata Format --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <h2 class="text-sm font-semibold text-slate-700">Format Metadata</h2>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-blue-50 border border-blue-100">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shrink-0 shadow-sm">
                            <span class="text-white text-xs font-black">DC</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">oai_dc — Dublin Core</p>
                            <p class="text-xs text-slate-500 mt-0.5 font-mono truncate">http://www.openarchives.org/OAI/2.0/oai_dc/</p>
                        </div>
                        <a href="http://www.openarchives.org/OAI/2.0/oai_dc.xsd" target="_blank" rel="noopener"
                           class="shrink-0 text-xs text-blue-600 hover:text-blue-800 hover:underline font-medium">
                            Schema →
                        </a>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Kolom Kanan ─────────────────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Base URL --}}
            <div class="bg-slate-900 rounded-2xl p-5 shadow-lg">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">OAI Base URL Jurnal</p>
                <p class="font-mono text-sm text-emerald-400 break-all leading-relaxed">{{ $baseUrl }}</p>
                <div class="mt-3 pt-3 border-t border-slate-700 space-y-1">
                    <p class="text-xs text-slate-400">Set Spec:</p>
                    <p class="font-mono text-xs text-amber-400">journal:{{ $journal->slug }}</p>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-700">
                    <p class="text-xs text-slate-500 leading-relaxed">Salin URL ini ke form pendaftaran pada layanan pengindeks atau harvester.</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-4">Statistik Jurnal</h3>
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

            {{-- Link ke Global OAI --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                <p class="text-xs text-blue-700 font-medium mb-2">Repository Global</p>
                <p class="text-xs text-blue-600 mb-3 leading-relaxed">
                    Endpoint global berisi semua jurnal dalam platform ini.
                </p>
                <a href="{{ route('oai') }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900 hover:underline flex items-center gap-1">
                    Lihat OAI Global
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

        </div>
    </div>
</div>

</x-layouts.app>

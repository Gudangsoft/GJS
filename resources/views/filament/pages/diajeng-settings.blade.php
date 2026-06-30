<x-filament-panels::page>
    {{ $this->form }}

    {{-- Status koneksi --}}
    @if($pingResult !== null)
    <div @class([
        'rounded-xl border p-4 flex items-start gap-3',
        'bg-green-50 border-green-200 text-green-800' => $pingSuccess,
        'bg-red-50   border-red-200   text-red-800'   => !$pingSuccess,
    ])>
        @if($pingSuccess)
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        @else
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        @endif
        <div>
            <p class="font-semibold text-sm">{{ $pingSuccess ? 'Koneksi Berhasil' : 'Koneksi Gagal' }}</p>
            <p class="text-sm mt-0.5">{{ $pingResult }}</p>
        </div>
    </div>
    @endif

    {{-- Preview data jurnal --}}
    @if($previewData !== null)
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
            <h3 class="text-sm font-semibold text-slate-700">Preview Data Jurnal dari DIAJENG</h3>
            @if(isset($previewData['meta']))
            <p class="text-xs text-slate-500 mt-0.5">Total terdaftar: {{ $previewData['meta']['total'] ?? '-' }} jurnal</p>
            @endif
        </div>
        <div class="divide-y divide-slate-100">
            @forelse(data_get($previewData, 'data', []) as $journal)
            <div class="px-5 py-3">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $journal['name'] ?? $journal['title'] ?? '-' }}</p>
                        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-slate-500">
                            @if(!empty($journal['issn_print']))
                                <span>ISSN Cetak: <strong>{{ $journal['issn_print'] }}</strong></span>
                            @endif
                            @if(!empty($journal['issn_online']))
                                <span>E-ISSN: <strong>{{ $journal['issn_online'] }}</strong></span>
                            @endif
                            @if(!empty($journal['publisher']))
                                <span>Penerbit: {{ is_array($journal['publisher']) ? ($journal['publisher']['name'] ?? '-') : $journal['publisher'] }}</span>
                            @endif
                            @if(!empty($journal['accreditation']))
                                <span class="text-green-700 font-medium">Akreditasi: {{ $journal['accreditation'] }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        ID: {{ $journal['id'] ?? '-' }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-slate-400">
                Tidak ada data jurnal yang dikembalikan. Periksa API key dan koneksi.
            </div>
            @endforelse
        </div>
        @if(!empty($previewData['data']))
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100">
            <p class="text-xs text-slate-400">Menampilkan 5 data pertama. Gunakan service DiajengService untuk akses penuh.</p>
        </div>
        @endif
    </div>
    @endif

    {{-- Dokumentasi penggunaan di kode --}}
    <div class="bg-slate-900 rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-200">Cara Pakai DiajengService di Kode</h3>
        </div>
        <pre class="text-xs text-green-400 px-5 py-4 overflow-x-auto leading-relaxed"><code>// Inject via constructor atau resolve langsung
use App\Services\DiajengService;

$diajeng = app(DiajengService::class);

// ── Daftar jurnal (dengan pagination & filter)
$journals = $diajeng->journals(['search' => 'teknik', 'per_page' => 20]);

// ── Cari jurnal berdasarkan ISSN
$journal = $diajeng->findJournalByIssn('2580-1234');

// ── Detail jurnal berdasarkan ID DIAJENG
$detail = $diajeng->journal(42);

// ── Daftar artikel
$articles = $diajeng->articles(['journal_id' => 42, 'year' => 2024]);

// ── Cari artikel via DOI
$article = $diajeng->findArticleByDoi('10.12345/jti.v1i1.001');

// ── Daftar penulis
$authors = $diajeng->authors(['search' => 'Budi Santoso']);

// ── Cek status koneksi (untuk health check)
$status = $diajeng->ping();
// ['ok' => true, 'message' => 'Terhubung ke DIAJENG. Total jurnal: 304.']

// ── Hapus cache manual
$diajeng->clearCache();
</code></pre>
    </div>
</x-filament-panels::page>

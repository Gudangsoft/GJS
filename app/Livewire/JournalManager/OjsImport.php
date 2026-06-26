<?php

namespace App\Livewire\JournalManager;

use App\Models\Issue;
use App\Models\Journal;
use App\Services\JournalExportService;
use App\Services\OjsImportService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class OjsImport extends Component
{
    /** Top-level mode: import | export */
    public string $mode = 'import';

    /** Active method tab: oai | crossref | rest */
    public string $method = 'oai';

    // ── Export state
    public string $exportPlugin  = '';
    public ?int   $exportIssueId = null;

    // ── OAI-PMH config
    public string $oaiUrl = '';
    public string $oaiSet = '';

    // ── CrossRef config
    public string $crossrefIssn       = '';
    public string $crossrefIssnOnline = '';

    // ── OJS REST API config
    public string $ojsUrl     = '';
    public string $apiKey     = '';
    public string $importWhat = 'all';

    // ── Shared state
    public ?bool  $connOk      = null;
    public string $connMsg     = '';
    public array  $importLog   = [];
    public array  $importStats = [];
    public bool   $done        = false;
    public bool   $sslVerify   = true;

    // ─────────────────────────────────────────────────────────────────────────

    private function activeJournal(): ?Journal
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            $journals = Journal::orderBy('name')->get();
        } else {
            $journals = Journal::whereHas('managers', fn($q) => $q->where('users.id', $user->id))
                ->orWhereHas('editors', fn($q) => $q->where('users.id', $user->id))
                ->get();
        }
        $activeId = session('manager_active_journal');
        return $journals->firstWhere('id', $activeId) ?? $journals->first();
    }

    public function mount(): void
    {
        $journal = $this->activeJournal();
        if ($journal?->url) {
            $this->ojsUrl = rtrim($journal->url, '/');
            // Also preset OAI URL to common OJS OAI endpoint
            $this->oaiUrl = rtrim($journal->url, '/') . '/oai';
        }
    }

    // ─── Test connection ──────────────────────────────────────────────────────

    public function testConnection(): void
    {
        $this->connOk  = null;
        $this->connMsg = '';

        $journal = $this->activeJournal();
        if (!$journal) {
            $this->connOk  = false;
            $this->connMsg = 'Tidak ada jurnal aktif.';
            return;
        }

        $service = new OjsImportService($journal->id);
        $service->setSslVerify($this->sslVerify);

        match ($this->method) {
            'oai' => $this->doTestOai($service),
            'crossref' => $this->doTestCrossref($service),
            'rest' => $this->doTestRest($service),
        };
    }

    private function doTestOai(OjsImportService $svc): void
    {
        $this->validate(['oaiUrl' => 'required|url'], [], ['oaiUrl' => 'URL OAI-PMH']);
        $result = $svc->testOaiConnection($this->oaiUrl);
        $this->connOk  = $result['ok'];
        $this->connMsg = $result['message'];
    }

    private function doTestCrossref(OjsImportService $svc): void
    {
        $this->validate(['crossrefIssn' => 'required'], [], ['crossrefIssn' => 'ISSN']);
        $issn   = $this->crossrefIssnOnline ?: $this->crossrefIssn;
        $result = $svc->testCrossrefByIssn($issn);
        $this->connOk  = $result['ok'];
        $this->connMsg = $result['message'];
    }

    private function doTestRest(OjsImportService $svc): void
    {
        $this->validate(['ojsUrl' => 'required|url'], [], ['ojsUrl' => 'URL OJS']);
        $svc->setRestConfig($this->ojsUrl, $this->apiKey ?: null);
        $result = $svc->testRestConnection();
        $this->connOk  = $result['ok'];
        $this->connMsg = $result['message'];
    }

    // ─── Start import ─────────────────────────────────────────────────────────

    public function startImport(): void
    {
        $journal = $this->activeJournal();
        if (!$journal) {
            $this->dispatch('toast', message: 'Tidak ada jurnal aktif.', type: 'error');
            return;
        }

        $this->done        = false;
        $this->importLog   = [];
        $this->importStats = [];

        $service = new OjsImportService($journal->id);
        $service->setSslVerify($this->sslVerify);

        match ($this->method) {
            'oai'      => $this->doImportOai($service),
            'crossref' => $this->doImportCrossref($service),
            'rest'     => $this->doImportRest($service),
        };

        $this->importLog   = $service->getLogs();
        $this->importStats = [
            'issuesCreated'   => $service->issuesCreated,
            'sectionsCreated' => $service->sectionsCreated,
            'articlesCreated' => $service->articlesCreated,
            'articlesUpdated' => $service->articlesUpdated,
            'errors'          => $service->errors,
        ];
        $this->done = true;

        $total = $service->articlesCreated + $service->articlesUpdated;
        $this->dispatch('toast',
            message: "Import selesai: {$service->articlesCreated} artikel baru, {$service->articlesUpdated} diperbarui.",
            type: $service->errors > 0 ? 'warning' : 'success'
        );
    }

    private function doImportOai(OjsImportService $svc): void
    {
        $this->validate(['oaiUrl' => 'required|url'], [], ['oaiUrl' => 'URL OAI-PMH']);
        $svc->importFromOai($this->oaiUrl, $this->oaiSet ?: null);
    }

    private function doImportCrossref(OjsImportService $svc): void
    {
        $this->validate(['crossrefIssn' => 'required'], [], ['crossrefIssn' => 'ISSN']);
        $issn = $this->crossrefIssnOnline ?: $this->crossrefIssn;
        $svc->importFromCrossref($issn);
    }

    private function doImportRest(OjsImportService $svc): void
    {
        $this->validate(['ojsUrl' => 'required|url'], [], ['ojsUrl' => 'URL OJS']);
        $svc->setRestConfig($this->ojsUrl, $this->apiKey ?: null);
        $only = match ($this->importWhat) {
            'issues'   => ['sections', 'issues'],
            'articles' => ['sections', 'articles'],
            default    => ['sections', 'issues', 'articles'],
        };
        $svc->importFromRest($only);
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function resetResult(): void
    {
        $this->connOk      = null;
        $this->connMsg     = '';
        $this->done        = false;
        $this->importLog   = [];
        $this->importStats = [];
    }

    // ─── Export ───────────────────────────────────────────────────────────────

    public static function exportPlugins(): array
    {
        return [
            [
                'key'  => 'crossref_xml',
                'name' => 'CrossRef XML Export Plugin',
                'desc' => 'Export metadata artikel ke format XML CrossRef untuk registrasi DOI.',
                'icon' => 'M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244',
                'color' => '#2563eb',
                'bg'    => '#eff6ff',
                'mode'  => 'export',
            ],
            [
                'key'  => 'datacite',
                'name' => 'DataCite Export/Registration Plugin',
                'desc' => 'Export metadata ke format DataCite XML kernel-4 untuk registrasi DOI DataCite.',
                'icon' => 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 2.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125',
                'color' => '#7c3aed',
                'bg'    => '#faf5ff',
                'mode'  => 'export',
            ],
            [
                'key'  => 'doaj',
                'name' => 'DOAJ Export Plugin',
                'desc' => 'Export metadata jurnal dan artikel ke format XML DOAJ (Directory of Open Access Journals).',
                'icon' => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418',
                'color' => '#15803d',
                'bg'    => '#f0fdf4',
                'mode'  => 'export',
            ],
            [
                'key'  => 'native_xml',
                'name' => 'Native XML Plugin',
                'desc' => 'Import dan export artikel dalam format XML native yang kompatibel dengan OJS.',
                'icon' => 'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
                'color' => '#0891b2',
                'bg'    => '#ecfeff',
                'mode'  => 'both',
            ],
            [
                'key'  => 'pubmed_xml',
                'name' => 'PubMed XML Export Plugin',
                'desc' => 'Export metadata artikel ke format XML NLM/PubMed untuk pengiriman ke PubMed Central.',
                'icon' => 'M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .03 2.798-1.442 2.798H4.24c-1.472 0-2.441-1.798-1.442-2.798L4.2 15.3M15 12H9m3 3V9',
                'color' => '#0f766e',
                'bg'    => '#f0fdfa',
                'mode'  => 'export',
            ],
            [
                'key'  => 'copernicus',
                'name' => 'Copernicus Export Plugin',
                'desc' => 'Export metadata jurnal ke format XML untuk pengiriman ke Copernicus Publications.',
                'icon' => 'M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z',
                'color' => '#b45309',
                'bg'    => '#fffbeb',
                'mode'  => 'export',
            ],
            [
                'key'  => 'users_xml',
                'name' => 'Users XML Plugin',
                'desc' => 'Import dan export data pengguna (editor, manajer) dalam format XML.',
                'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                'color' => '#475569',
                'bg'    => '#f8fafc',
                'mode'  => 'both',
            ],
        ];
    }

    public function getIssueOptions(): array
    {
        $journal = $this->activeJournal();
        if (!$journal) return [];

        return Issue::where('journal_id', $journal->id)
            ->where('published', true)
            ->orderByDesc('date_published')
            ->get(['id', 'volume', 'number', 'year', 'title',
                   'show_volume', 'show_number', 'show_year', 'show_title'])
            ->mapWithKeys(fn($i) => [$i->id => $i->getLabel() ?: "Vol.{$i->volume} No.{$i->number} ({$i->year})"])
            ->toArray();
    }

    public function exportData(): mixed
    {
        $journal = $this->activeJournal();
        if (!$journal || !$this->exportPlugin) {
            $this->dispatch('toast', message: 'Pilih plugin ekspor terlebih dahulu.', type: 'error');
            return null;
        }

        $svc = new JournalExportService($journal->id, $this->exportIssueId ?: null);

        [$xml, $filename] = match ($this->exportPlugin) {
            'crossref_xml' => [$svc->crossref(),   'crossref-'   . now()->format('Ymd') . '.xml'],
            'datacite'     => [$svc->datacite(),   'datacite-'   . now()->format('Ymd') . '.xml'],
            'doaj'         => [$svc->doaj(),       'doaj-'       . now()->format('Ymd') . '.xml'],
            'native_xml'   => [$svc->nativeXml(),  'native-'     . now()->format('Ymd') . '.xml'],
            'pubmed_xml'   => [$svc->pubmed(),     'pubmed-'     . now()->format('Ymd') . '.xml'],
            'copernicus'   => [$svc->copernicus(), 'copernicus-' . now()->format('Ymd') . '.xml'],
            'users_xml'    => [$svc->usersXml(),   'users-'      . now()->format('Ymd') . '.xml'],
            default        => [null, null],
        };

        if (!$xml) {
            $this->dispatch('toast', message: 'Plugin tidak dikenali.', type: 'error');
            return null;
        }

        return response()->streamDownload(
            fn () => print($xml),
            $filename,
            ['Content-Type' => 'application/xml; charset=UTF-8']
        );
    }

    public function render()
    {
        $journal = $this->activeJournal();
        return view('livewire.journal-manager.ojs-import', [
            'journal'      => $journal,
            'issueOptions' => $journal ? $this->getIssueOptions() : [],
            'exportPlugins'=> static::exportPlugins(),
        ]);
    }
}

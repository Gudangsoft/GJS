<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Services\OjsImportService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class OjsImport extends Component
{
    /** Active method tab: oai | crossref | rest */
    public string $method = 'oai';

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

    public function render()
    {
        return view('livewire.journal-manager.ojs-import', [
            'journal' => $this->activeJournal(),
        ]);
    }
}

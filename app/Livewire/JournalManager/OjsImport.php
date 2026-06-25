<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Services\OjsImportService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class OjsImport extends Component
{
    public string $ojsUrl    = '';
    public string $apiKey    = '';
    public string $importWhat = 'all'; // all | issues | articles

    public bool  $testing    = false;
    public bool  $importing  = false;
    public ?bool $connOk     = null;
    public string $connMsg   = '';

    public array $importLog  = [];
    public array $importStats = [];
    public bool  $done       = false;

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
        }
    }

    public function testConnection(): void
    {
        $this->validate(['ojsUrl' => 'required|url']);

        $this->testing = true;
        $this->connOk  = null;
        $this->connMsg = '';

        $journal = $this->activeJournal();
        if (!$journal) {
            $this->connOk  = false;
            $this->connMsg = 'Tidak ada jurnal aktif.';
            $this->testing = false;
            return;
        }

        $service = new OjsImportService($this->ojsUrl, $journal->id, $this->apiKey ?: null);
        $result  = $service->testConnection();

        $this->connOk  = $result['ok'];
        $this->connMsg = $result['message'];
        $this->testing = false;
    }

    public function startImport(): void
    {
        $this->validate(['ojsUrl' => 'required|url']);

        $journal = $this->activeJournal();
        if (!$journal) {
            $this->dispatch('toast', message: 'Tidak ada jurnal aktif.', type: 'error');
            return;
        }

        $this->importing  = true;
        $this->done       = false;
        $this->importLog  = [];
        $this->importStats = [];

        $service = new OjsImportService($this->ojsUrl, $journal->id, $this->apiKey ?: null);

        $only = match ($this->importWhat) {
            'issues'   => ['sections', 'issues'],
            'articles' => ['sections', 'articles'],
            default    => ['sections', 'issues', 'articles'],
        };

        if (in_array('sections', $only))  $service->importSections();
        if (in_array('issues', $only))    $service->importIssues();
        if (in_array('articles', $only))  $service->importArticles();

        $this->importLog   = $service->getLogs();
        $this->importStats = [
            'sectionsCreated'  => $service->sectionsCreated,
            'issuesCreated'    => $service->issuesCreated,
            'issuesUpdated'    => $service->issuesSkipped,
            'articlesCreated'  => $service->articlesCreated,
            'articlesUpdated'  => $service->articlesUpdated,
            'errors'           => $service->errors,
        ];

        $this->importing = false;
        $this->done      = true;

        $this->dispatch('toast',
            message: "Import selesai: {$service->articlesCreated} artikel baru, {$service->articlesUpdated} diperbarui.",
            type: $service->errors > 0 ? 'warning' : 'success'
        );
    }

    public function render()
    {
        return view('livewire.journal-manager.ojs-import', [
            'journal' => $this->activeJournal(),
        ]);
    }
}

<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Models\LetterOfAcceptance;
use App\Models\Submission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Loa extends Component
{
    public string $tab    = 'all';
    public string $search = '';
    public bool   $showForm  = false;
    public ?int   $editingId = null;

    // Form fields
    public ?int    $submission_id            = null;
    public string  $loa_number              = '';
    public string  $article_title           = '';
    public array   $authors                 = [['name' => '', 'affiliation' => '']];
    public string  $status                  = 'draft';
    public string  $acceptance_date         = '';
    public string  $expected_publication_date = '';
    public string  $volume                  = '';
    public string  $number                  = '';
    public string  $year                    = '';
    public string  $notes                   = '';

    public function getJournal(): ?Journal
    {
        $journals = Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->get();
        $activeId = session('manager_active_journal');
        return $journals->firstWhere('id', $activeId) ?? $journals->first();
    }

    public function getAcceptedSubmissions()
    {
        $journal = $this->getJournal();
        if (!$journal) return collect();
        return Submission::where('journal_id', $journal->id)
            ->whereIn('status', ['accepted','copyediting','production','scheduled','published'])
            ->orderByDesc('submitted_at')
            ->get(['id','title','status']);
    }

    public function updatedSubmissionId($value): void
    {
        if (!$value) return;
        $sub = Submission::with(['contributors', 'submitter'])->find($value);
        if (!$sub) return;

        $this->article_title = $sub->title;

        if ($sub->contributors->isNotEmpty()) {
            $this->authors = $sub->contributors->map(fn($c) => [
                'name'        => trim($c->first_name . ' ' . $c->last_name),
                'affiliation' => $c->affiliation ?? '',
            ])->toArray();
        } elseif ($sub->submitter) {
            $this->authors = [[
                'name'        => trim($sub->submitter->first_name . ' ' . $sub->submitter->last_name),
                'affiliation' => $sub->submitter->affiliation ?? '',
            ]];
        }
    }

    public function addAuthor(): void
    {
        $this->authors[] = ['name' => '', 'affiliation' => ''];
    }

    public function removeAuthor(int $index): void
    {
        if (count($this->authors) <= 1) return;
        array_splice($this->authors, $index, 1);
        $this->authors = array_values($this->authors);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $journal = $this->getJournal();
        if ($journal) {
            $this->loa_number      = LetterOfAcceptance::generateNumber($journal);
            $this->acceptance_date = now()->format('Y-m-d');
            $this->year            = (string) now()->year;
        }
        $this->showForm  = true;
        $this->editingId = null;
    }

    public function openEdit(int $id): void
    {
        $loa = LetterOfAcceptance::findOrFail($id);
        $this->editingId     = $id;
        $this->submission_id = $loa->submission_id;
        $this->loa_number    = $loa->loa_number;
        $this->article_title = $loa->article_title;

        // Normalize stored authors → [{name, affiliation}]
        $raw = $loa->authors ?? [];
        $this->authors = collect($raw)->map(fn($a) => is_array($a)
            ? ['name' => $a['name'] ?? '', 'affiliation' => $a['affiliation'] ?? '']
            : ['name' => (string) $a, 'affiliation' => '']
        )->toArray() ?: [['name' => '', 'affiliation' => '']];

        $this->status                    = $loa->status;
        $this->acceptance_date           = $loa->acceptance_date?->format('Y-m-d') ?? '';
        $this->expected_publication_date = $loa->expected_publication_date?->format('Y-m-d') ?? '';
        $this->volume                    = $loa->volume ?? '';
        $this->number                    = $loa->number ?? '';
        $this->year                      = $loa->year ?? '';
        $this->notes                     = $loa->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'article_title'   => 'required|string',
            'loa_number'      => 'required|string',
            'acceptance_date' => 'required|date',
            'status'          => 'required|in:draft,issued,revoked',
        ]);

        $journal = $this->getJournal();
        if (!$journal) return;

        // Filter out blank author rows
        $authors = array_values(array_filter($this->authors, fn($a) => trim($a['name'] ?? '') !== ''));
        if (empty($authors)) {
            $authors = $this->authors;
        }

        $data = [
            'journal_id'                => $journal->id,
            'submission_id'             => $this->submission_id,
            'issued_by'                 => auth()->id(),
            'loa_number'               => $this->loa_number,
            'article_title'            => $this->article_title,
            'authors'                  => $authors,
            'status'                   => $this->status,
            'acceptance_date'          => $this->acceptance_date,
            'expected_publication_date'=> $this->expected_publication_date ?: null,
            'volume'                   => $this->volume ?: null,
            'number'                   => $this->number ?: null,
            'year'                     => $this->year ?: null,
            'notes'                    => $this->notes ?: null,
        ];

        if ($this->editingId) {
            LetterOfAcceptance::findOrFail($this->editingId)->update($data);
        } else {
            LetterOfAcceptance::create($data);
        }

        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('toast', message: 'LOA berhasil disimpan.', type: 'success');
    }

    public function delete(int $id): void
    {
        $loa = LetterOfAcceptance::findOrFail($id);
        $journal = $this->getJournal();
        if ($loa->journal_id === $journal?->id) {
            $loa->delete();
            $this->dispatch('toast', message: 'LOA dihapus.', type: 'success');
        }
    }

    public function resetForm(): void
    {
        $this->submission_id = null;
        $this->loa_number = $this->article_title = '';
        $this->authors    = [['name' => '', 'affiliation' => '']];
        $this->status     = 'draft';
        $this->acceptance_date = $this->expected_publication_date = '';
        $this->volume = $this->number = $this->year = $this->notes = '';
        $this->editingId  = null;
    }

    public function render()
    {
        $journal = $this->getJournal();

        $query = LetterOfAcceptance::with(['submission','issuedBy'])
            ->where('journal_id', $journal?->id ?? 0);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('article_title', 'like', '%'.$this->search.'%')
                  ->orWhere('loa_number', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->tab !== 'all') {
            $query->where('status', $this->tab);
        }

        $loas = $query->orderByDesc('created_at')->get();

        $counts = [
            'all'     => LetterOfAcceptance::where('journal_id', $journal?->id ?? 0)->count(),
            'draft'   => LetterOfAcceptance::where('journal_id', $journal?->id ?? 0)->where('status','draft')->count(),
            'issued'  => LetterOfAcceptance::where('journal_id', $journal?->id ?? 0)->where('status','issued')->count(),
            'revoked' => LetterOfAcceptance::where('journal_id', $journal?->id ?? 0)->where('status','revoked')->count(),
        ];

        $acceptedSubmissions = $this->getAcceptedSubmissions();

        return view('livewire.journal-manager.loa', compact('journal','loas','counts','acceptedSubmissions'))
            ->title('Letter of Acceptance — '.($journal?->name_abbrev ?? 'Manager'));
    }
}
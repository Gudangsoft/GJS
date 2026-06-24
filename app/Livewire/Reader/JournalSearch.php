<?php

namespace App\Livewire\Reader;

use App\Models\Article;
use App\Models\Journal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class JournalSearch extends Component
{
    use WithPagination;

    public Journal $journal;

    #[Url(as: 'q')]
    public string $query = '';

    #[Url(as: 'kw')]
    public string $keyword = '';

    public function mount(Journal $journal): void
    {
        $this->journal = $journal;
    }

    public function updatedQuery(): void
    {
        $this->resetPage();
        $this->keyword = '';
    }

    public function updatedKeyword(): void
    {
        $this->resetPage();
        $this->query = '';
    }

    public function render()
    {
        $results = collect();
        $totalCount = 0;

        $q = trim($this->query ?: $this->keyword);

        if (strlen($q) >= 2) {
            $baseQuery = Article::with(['submission.contributors', 'section', 'issue'])
                ->where('journal_id', $this->journal->id)
                ->whereNotNull('date_published');

            if ($this->keyword) {
                // Search keywords JSON column
                $baseQuery->whereHas('submission', fn($sq) =>
                    $sq->whereRaw("LOWER(CAST(keywords AS CHAR)) LIKE ?", ['%' . strtolower($q) . '%'])
                );
            } else {
                // Full text search across title, abstract, author names
                $baseQuery->where(function($wq) use ($q) {
                    $wq->whereHas('submission', fn($sq) =>
                        $sq->where('title', 'LIKE', "%{$q}%")
                           ->orWhere('abstract', 'LIKE', "%{$q}%")
                           ->orWhereRaw("LOWER(CAST(keywords AS CHAR)) LIKE ?", ['%' . strtolower($q) . '%'])
                    )->orWhereHas('submission.contributors', fn($cq) =>
                        $cq->where('first_name', 'LIKE', "%{$q}%")
                           ->orWhere('last_name', 'LIKE', "%{$q}%")
                    );
                });
            }

            $totalCount = $baseQuery->count();
            $results    = $baseQuery->orderByDesc('date_published')->paginate(10);
        }

        return view('livewire.reader.journal-search', compact('results', 'totalCount', 'q'))
            ->title('Pencarian: ' . ($q ?: '…') . ' — ' . $this->journal->name);
    }
}

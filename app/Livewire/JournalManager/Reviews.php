<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Models\ReviewAssignment;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Reviews extends Component
{
    public string $statusFilter = 'all';

    public function setFilter(string $f): void
    {
        $this->statusFilter = $f;
    }

    protected function getJournal(): ?Journal
    {
        return Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->first();
    }

    public function render()
    {
        $journal = $this->getJournal();
        $assignments = collect();

        if ($journal) {
            $query = ReviewAssignment::with(['reviewer', 'submission.section'])
                ->whereHas('submission', fn($q) => $q->where('journal_id', $journal->id));

            if ($this->statusFilter !== 'all') {
                $query->where('status', $this->statusFilter);
            }

            $assignments = $query->orderByDesc('created_at')->get();
        }

        return view('livewire.journal-manager.reviews', compact('journal', 'assignments'))
            ->title('Penugasan Review — Panel Pengelola');
    }
}

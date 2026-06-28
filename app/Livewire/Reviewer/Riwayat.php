<?php

namespace App\Livewire\Reviewer;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ReviewAssignment;
use App\Models\Journal;

#[Layout('layouts.reviewer')]
class Riwayat extends Component
{
    use WithPagination;

    #[Url]
    public string $filterStatus = 'all';

    #[Url]
    public string $filterJournal = '';

    #[Url]
    public string $filterYear = '';

    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterJournal(): void { $this->resetPage(); }
    public function updatingFilterYear(): void { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->filterStatus  = 'all';
        $this->filterJournal = '';
        $this->filterYear    = '';
        $this->resetPage();
    }

    public function render()
    {
        $userId = auth()->id();

        $query = ReviewAssignment::where('reviewer_id', $userId)
            ->with(['submission.journal', 'review'])
            ->orderByDesc('date_assigned');

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterJournal !== '') {
            $query->whereHas('submission.journal', fn ($q) => $q->where('id', $this->filterJournal));
        }

        if ($this->filterYear !== '') {
            $query->whereYear('date_assigned', $this->filterYear);
        }

        $assignments = $query->paginate(10);

        // Summary stats (always from full set, no filters)
        $rawCounts = ReviewAssignment::where('reviewer_id', $userId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'invited'   => ReviewAssignment::where('reviewer_id', $userId)->count(),
            'accepted'  => (int) ($rawCounts['accepted'] ?? 0),
            'completed' => (int) ($rawCounts['completed'] ?? 0),
            'declined'  => (int) (($rawCounts['declined'] ?? 0) + ($rawCounts['cancelled'] ?? 0)),
        ];

        $completedForAvg = ReviewAssignment::where('reviewer_id', $userId)
            ->where('status', 'completed')
            ->whereNotNull('date_completed')
            ->whereNotNull('date_assigned')
            ->get();

        $stats['avg_days'] = $completedForAvg->isNotEmpty()
            ? round($completedForAvg->avg(fn ($a) => $a->date_assigned->diffInDays($a->date_completed)))
            : null;

        // Available journals for filter (only journals where user has assignments)
        $journals = Journal::whereHas('submissions.reviewAssignments', fn ($q) => $q->where('reviewer_id', $userId))
            ->orderBy('name')
            ->get(['id', 'name']);

        // Available years
        $years = ReviewAssignment::where('reviewer_id', $userId)
            ->whereNotNull('date_assigned')
            ->selectRaw('YEAR(date_assigned) as yr')
            ->groupBy('yr')
            ->orderByDesc('yr')
            ->pluck('yr');

        return view('livewire.reviewer.riwayat', compact(
            'assignments', 'stats', 'journals', 'years'
        ))->title('Riwayat Review — Panel Reviewer');
    }
}

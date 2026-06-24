<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Models\Submission;
use App\Models\Issue;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Dashboard extends Component
{
    public string $activeJournalSlug = '';
    public string $tab = 'pending';

    public function mount(): void
    {
        $journals = $this->getManagedJournals();
        if ($journals->isNotEmpty()) {
            $activeId = session('manager_active_journal');
            $active = $journals->firstWhere('id', $activeId) ?? $journals->first();
            $this->activeJournalSlug = $active->slug;
        }
    }

    public function getManagedJournals()
    {
        return Journal::whereHas('managers', fn ($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn ($q) => $q->where('users.id', auth()->id()))
            ->orderBy('name')->get();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function setJournal(string $slug): void
    {
        $this->activeJournalSlug = $slug;
        $this->tab = 'pending';
        $journal = $this->getManagedJournals()->firstWhere('slug', $slug);
        if ($journal) session(['manager_active_journal' => $journal->id]);
    }

    public function render()
    {
        $journals = $this->getManagedJournals();
        $journal  = $journals->firstWhere('slug', $this->activeJournalSlug) ?? $journals->first();

        $counts = [];
        $submissions = collect();

        if ($journal) {
            $base = fn () => Submission::with(['submitter', 'section'])
                ->where('journal_id', $journal->id)
                ->whereNotIn('status', ['draft']);

            $submissions = match ($this->tab) {
                'pending'  => $base()->whereIn('status', ['submitted','queued'])->latest('submitted_at')->get(),
                'review'   => $base()->whereIn('status', ['assigned','review'])->latest('submitted_at')->get(),
                'revision' => $base()->whereIn('status', ['revision_required','resubmit'])->latest('submitted_at')->get(),
                'decided'  => $base()->whereIn('status', ['accepted','declined','copyediting','production','scheduled','published'])->latest('submitted_at')->get(),
                default    => $base()->whereIn('status', ['submitted','queued'])->latest('submitted_at')->get(),
            };

            $raw = Submission::where('journal_id', $journal->id)
                ->whereNotIn('status', ['draft'])
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $counts = [
                'pending'  => $raw->only(['submitted','queued'])->sum(),
                'review'   => $raw->only(['assigned','review'])->sum(),
                'revision' => $raw->only(['revision_required','resubmit'])->sum(),
                'decided'  => $raw->only(['accepted','declined','copyediting','production','scheduled','published'])->sum(),
                'total'    => $raw->sum(),
            ];

            $journalStats = [
                'issues'    => Issue::where('journal_id', $journal->id)->count(),
                'published' => Issue::where('journal_id', $journal->id)->where('published', true)->count(),
                'articles'  => \App\Models\Article::where('journal_id', $journal->id)->count(),
                'reviewers' => \App\Models\User::whereHas('roles', fn ($q) => $q->where('name','reviewer'))->count(),
            ];
        } else {
            $journalStats = [];
        }

        return view('livewire.journal-manager.dashboard', compact('journals', 'journal', 'submissions', 'counts', 'journalStats'))
            ->title('Dashboard Pengelola — ' . ($journal?->name ?? 'GJS'));
    }
}

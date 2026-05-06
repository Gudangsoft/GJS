<?php

namespace App\Livewire\Reader;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\Issue;
use App\Models\Journal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class JournalHome extends Component
{
    public Journal $journal;

    public function mount(Journal $journal): void
    {
        $this->journal = $journal;
    }

    public function render()
    {
        $currentIssue = Issue::where('journal_id', $this->journal->id)
            ->where('current', true)
            ->first();

        // Articles in current issue grouped by section (OJS TOC style)
        $tocBySection = collect();
        if ($currentIssue) {
            $tocBySection = Article::where('issue_id', $currentIssue->id)
                ->with(['submission.contributors', 'section', 'galleys'])
                ->orderBy('sequence')
                ->get()
                ->groupBy(fn ($a) => $a->section?->title ?? 'Artikel');
        }

        $announcements = Announcement::where('journal_id', $this->journal->id)
            ->where(fn ($q) => $q->whereNull('date_expire')->orWhere('date_expire', '>', now()))
            ->orderByDesc('date_posted')
            ->take(5)
            ->get();

        $pastIssues = Issue::where('journal_id', $this->journal->id)
            ->where('published', true)
            ->where('current', false)
            ->orderByDesc('year')->orderByDesc('volume')->orderByDesc('number')
            ->take(5)
            ->get();

        return view('livewire.reader.journal-home',
            compact('currentIssue', 'tocBySection', 'announcements', 'pastIssues'))
            ->title($this->journal->name);
    }
}

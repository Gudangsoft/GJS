<?php

namespace App\Livewire\Reader;

use App\Models\Article;
use App\Models\Issue;
use App\Models\Journal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class IssueToc extends Component
{
    public Journal $journal;
    public Issue $issue;

    public function mount(Journal $journal, Issue $issue): void
    {
        abort_unless($issue->journal_id === $journal->id, 404);
        $this->journal = $journal;
        $this->issue   = $issue;
    }

    public function render()
    {
        $articles = Article::where('issue_id', $this->issue->id)
            ->with(['submission.contributors', 'galleys', 'section'])
            ->orderBy('sequence')
            ->get()
            ->groupBy('section.title');

        return view('livewire.reader.issue-toc', compact('articles'))
            ->title($this->issue->getLabel() . ' — ' . $this->journal->name);
    }
}

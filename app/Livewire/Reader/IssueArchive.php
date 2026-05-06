<?php

namespace App\Livewire\Reader;

use App\Models\Issue;
use App\Models\Journal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class IssueArchive extends Component
{
    public Journal $journal;

    public function mount(Journal $journal): void
    {
        $this->journal = $journal;
    }

    public function render()
    {
        $issues = Issue::where('journal_id', $this->journal->id)
            ->where('published', true)
            ->withCount('articles')
            ->orderByDesc('year')
            ->orderByDesc('volume')
            ->orderByDesc('number')
            ->get()
            ->groupBy('year');

        return view('livewire.reader.issue-archive', compact('issues'))
            ->title('Arsip Terbitan — ' . $this->journal->name);
    }
}

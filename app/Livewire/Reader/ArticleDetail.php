<?php

namespace App\Livewire\Reader;

use App\Jobs\IncrementArticleViews;
use App\Models\Article;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\JournalSidebarBlock;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ArticleDetail extends Component
{
    use \App\Traits\CachesJournalStats;
    public Journal $journal;
    public Article $article;

    public function mount(Journal $journal, Article $article): void
    {
        abort_unless($article->journal_id === $journal->id, 404);
        $this->journal = $journal;
        $this->article = $article->load([
            'submission.contributors',
            'issue',
            'section',
        ]);

        IncrementArticleViews::dispatch($article->id);
    }

    public function render()
    {
        $articleGalleys = \App\Models\ArticleGalley::where('article_id', $this->article->id)
            ->where('is_approved', 1)
            ->orderBy('sequence')
            ->get();

        $sidebarBlocks = JournalSidebarBlock::where('journal_id', $this->journal->id)
            ->where('enabled', true)
            ->orderBy('sort_order')
            ->get();

        $journalStats = [];
        if ($sidebarBlocks->contains('type', 'statistics')) {
            $journalStats = $this->getJournalStats($this->journal->id);
        }

        return view('livewire.reader.article-detail', compact('articleGalleys', 'sidebarBlocks', 'journalStats'))
            ->title($this->article->submission->title . ' — ' . $this->journal->name);
    }
}

<?php

namespace App\Livewire\Reader;

use App\Jobs\IncrementArticleViews;
use App\Models\Article;
use App\Models\Journal;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ArticleDetail extends Component
{
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

    #[Computed]
    public function approvedGalleys()
    {
        return $this->article->galleys()
            ->where('is_approved', true)
            ->orderBy('sequence')
            ->get();
    }

    public function render()
    {
        return view('livewire.reader.article-detail')
            ->title($this->article->submission->title . ' — ' . $this->journal->name);
    }
}

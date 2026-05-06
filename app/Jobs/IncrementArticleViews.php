<?php

namespace App\Jobs;

use App\Models\Article;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IncrementArticleViews implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $articleId) {}

    public function handle(): void
    {
        Article::where('id', $this->articleId)->increment('views');
    }
}

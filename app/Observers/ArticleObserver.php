<?php

namespace App\Observers;

use App\Jobs\RegisterDoiJob;
use App\Models\Article;

class ArticleObserver
{
    public function created(Article $article): void
    {
        $this->maybeRegisterDoi($article);
    }

    public function updated(Article $article): void
    {
        // Trigger when date_published is set for the first time
        if ($article->wasChanged('date_published') && $article->date_published !== null) {
            $this->maybeRegisterDoi($article);
        }
    }

    private function maybeRegisterDoi(Article $article): void
    {
        if (!$article->doi) return;
        if (!$article->date_published) return;
        if (in_array($article->doi_status, ['registered', 'pending'])) return;

        $article->update(['doi_status' => 'pending']);

        RegisterDoiJob::dispatch($article)->onQueue('default');
    }
}

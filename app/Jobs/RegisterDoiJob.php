<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\CrossrefService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RegisterDoiJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries    = 3;
    public int $backoff  = 60; // seconds between retries

    public function __construct(public readonly Article $article)
    {
    }

    public function handle(CrossrefService $crossref): void
    {
        $crossref->registerDoi($this->article);
    }
}

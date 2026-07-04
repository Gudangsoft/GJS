<?php

namespace App\Livewire\Reader;

use App\Models\Article;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class JournalIndex extends Component
{
    public function render()
    {
        $journals = Journal::where('status', 'active')
            ->where('enabled', true)
            ->withCount([
                'submissions as articles_count' => fn ($q) => $q->where('status', 'published'),
                'issues as issues_count'        => fn ($q) => $q->where('published', true),
            ])
            ->with(['issues' => fn ($q) => $q->where('current', true)->limit(1)])
            ->orderBy('name')
            ->get();

        $stats = [
            'journals' => $journals->count(),
            'articles' => Article::whereHas('submission', fn ($q) => $q->where('status', 'published'))->count(),
            'issues'   => Issue::where('published', true)->count(),
            'authors'  => User::count(),
        ];

        $recentArticles = Article::with(['submission.contributors', 'journal', 'section'])
            ->whereHas('submission', fn ($q) => $q->where('status', 'published'))
            ->orderByDesc('date_published')
            ->take(6)
            ->get();

        return view('livewire.reader.journal-index', compact('journals', 'stats', 'recentArticles'));
    }
}

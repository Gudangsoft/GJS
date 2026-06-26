<?php

namespace App\Livewire\Reader;

use App\Models\Article;
use App\Models\SubmissionContributor;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AuthorProfile extends Component
{
    public User $author;

    public function mount(User $author): void
    {
        $this->author = $author;
    }

    public function render()
    {
        // Published articles where this user is a contributor (matched by email)
        $articles = Article::with(['submission.contributors', 'issue', 'journal', 'galleys'])
            ->whereHas('submission.contributors', function ($q) {
                $q->where('email', $this->author->email);
            })
            ->whereHas('issue', fn($q) => $q->where('published', true))
            ->orderByDesc('date_published')
            ->get();

        // Also get articles from direct submission authorship
        $ownSubmissions = Article::with(['submission', 'issue', 'journal', 'galleys'])
            ->whereHas('submission', fn($q) => $q->where('user_id', $this->author->id))
            ->whereHas('issue', fn($q) => $q->where('published', true))
            ->orderByDesc('date_published')
            ->get();

        $articles = $articles->merge($ownSubmissions)->unique('id')->sortByDesc('date_published');

        // Journals this author contributed to
        $journals = $articles->pluck('journal')->unique('id')->filter();

        // Co-authors
        $submissionIds = $articles->pluck('submission_id')->filter();
        $coAuthors = SubmissionContributor::whereIn('submission_id', $submissionIds)
            ->where('email', '!=', $this->author->email)
            ->select('first_name', 'last_name', 'email', 'orcid')
            ->get()
            ->unique(fn($c) => strtolower($c->email))
            ->take(12);

        return view('livewire.reader.author-profile', [
            'author'    => $this->author,
            'articles'  => $articles,
            'journals'  => $journals,
            'coAuthors' => $coAuthors,
        ])->title(($this->author->full_name ?? $this->author->name) . ' — Profil Penulis');
    }
}

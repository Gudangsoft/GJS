<?php

namespace App\Livewire\Reader;

use App\Models\Article;
use App\Models\Journal;
use App\Models\SubmissionContributor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class JournalBrowse extends Component
{
    public Journal $journal;
    public string  $by = 'author'; // author | title | keyword

    #[Url]
    public string $letter = '';

    public function mount(Journal $journal, string $by = 'author'): void
    {
        abort_unless(in_array($by, ['author', 'title', 'keyword']), 404);
        $this->journal = $journal;
        $this->by      = $by;
    }

    public function render()
    {
        $items   = collect();
        $letters = range('A', 'Z');

        $articleIds = Article::where('journal_id', $this->journal->id)
            ->whereNotNull('date_published')
            ->pluck('id');

        if ($this->by === 'author') {
            $query = SubmissionContributor::whereHas('submission.article', function($q) {
                $q->where('journal_id', $this->journal->id)->whereNotNull('date_published');
            });
            if ($this->letter) {
                $query->where(function($q) {
                    $q->where('last_name', 'LIKE', $this->letter . '%')
                      ->orWhere('first_name', 'LIKE', $this->letter . '%');
                });
            }
            $items = $query
                ->select('first_name', 'last_name', 'submission_id')
                ->with(['submission.article' => fn($q) => $q->where('journal_id', $this->journal->id)])
                ->get()
                ->groupBy(fn($c) => trim($c->last_name . ' ' . $c->first_name))
                ->sortKeys();

        } elseif ($this->by === 'title') {
            $query = Article::with(['submission', 'issue'])
                ->whereIn('id', $articleIds);
            if ($this->letter) {
                $query->whereHas('submission', fn($q) =>
                    $q->where('title', 'LIKE', $this->letter . '%')
                );
            }
            $items = $query->get()
                ->groupBy(fn($a) => strtoupper(substr($a->submission->title ?? '?', 0, 1)))
                ->sortKeys();

        } elseif ($this->by === 'keyword') {
            $allKw = \App\Models\Submission::whereHas('article', fn($q) =>
                $q->whereIn('id', $articleIds)
            )->whereNotNull('keywords')->pluck('keywords');

            $kwMap = collect();
            foreach ($allKw as $arr) {
                foreach ((array)$arr as $kw) {
                    $k = mb_strtolower(trim($kw));
                    if ($k) $kwMap[$k] = ($kwMap[$k] ?? 0) + 1;
                }
            }
            if ($this->letter) {
                $kwMap = $kwMap->filter(fn($c, $k) => strtoupper(substr($k, 0, 1)) === $this->letter);
            }
            $items = $kwMap->sortKeys();
        }

        return view('livewire.reader.journal-browse', compact('items', 'letters'))
            ->title('Jelajahi ' . match($this->by) {
                'author'  => 'Penulis',
                'title'   => 'Judul',
                'keyword' => 'Kata Kunci',
                default   => ''
            } . ' — ' . $this->journal->name);
    }
}

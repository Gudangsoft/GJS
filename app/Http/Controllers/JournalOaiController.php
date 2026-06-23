<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Journal;
use Illuminate\Http\Request;

class JournalOaiController extends Controller
{
    public function __invoke(Request $request, Journal $journal): mixed
    {
        abort_unless($journal->enabled && $journal->status === 'active', 404);

        $verb = $request->input('verb', '');

        if ($verb === '') {
            return $this->infoPage($journal);
        }

        // Pre-inject set filter for verbs that support it, unless caller already set it
        if (in_array($verb, ['ListIdentifiers', 'ListRecords']) && !$request->has('set')) {
            $request->query->set('set', 'journal:' . $journal->slug);
        }

        // Delegate to the global OAI controller scoped to this journal
        return (new OaiPmhController())->forJournal($journal)($request);
    }

    private function infoPage(Journal $journal): \Illuminate\Contracts\View\View
    {
        $baseUrl      = url("/journals/{$journal->slug}/oai");
        $articleCount = Article::where('journal_id', $journal->id)
                            ->whereNotNull('date_published')
                            ->count();
        $earliest     = Article::where('journal_id', $journal->id)
                            ->orderBy('date_published')
                            ->value('date_published');

        return view('oai.journal', [
            'journal'         => $journal,
            'baseUrl'         => $baseUrl,
            'repositoryName'  => $journal->name . ' OAI Repository',
            'adminEmail'      => $journal->email ?? config('mail.from.address'),
            'earliestDate'    => $earliest ? \Carbon\Carbon::parse($earliest)->toDateString() : now()->toDateString(),
            'articleCount'    => $articleCount,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Journal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index(Request $request, string $slug): JsonResponse
    {
        $journal = Journal::where('slug', $slug)->where('enabled', true)->firstOrFail();
        $perPage = min((int) $request->get('per_page', 20), 100);

        $issues = Issue::where('journal_id', $journal->id)
            ->where('published', true)
            ->orderByDesc('date_published')
            ->paginate($perPage);

        $items = $issues->map(fn($i) => $this->formatIssue($i, $slug));

        return response()->json([
            'apiVersion' => '1.0',
            'count'      => $issues->total(),
            'lastPage'   => $issues->lastPage(),
            'nextOffset' => $issues->currentPage() < $issues->lastPage()
                            ? $issues->currentPage() + 1 : null,
            'items'      => $items->values(),
        ]);
    }

    public function show(string $slug, int $issueId): JsonResponse
    {
        $journal = Journal::where('slug', $slug)->where('enabled', true)->firstOrFail();
        $issue   = Issue::where('id', $issueId)
            ->where('journal_id', $journal->id)
            ->where('published', true)
            ->with('articles.submission.contributors')
            ->firstOrFail();

        $data = $this->formatIssue($issue, $slug);
        $data['articles'] = $issue->articles->map(fn($a) => [
            'id'             => $a->id,
            'title'          => $a->submission?->title,
            'doi'            => $a->doi,
            'pages'          => $a->pages,
            'date_published' => $a->date_published?->toDateString(),
            'authors'        => ($a->submission?->contributors ?? collect())->map(fn($c) => [
                'given_name'  => $c->first_name,
                'family_name' => $c->last_name,
                'affiliation' => $c->affiliation,
                'orcid'       => $c->orcid,
            ])->values(),
            '_links' => [
                'self' => url("/api/v1/journals/{$slug}/articles/{$a->id}"),
            ],
        ])->values();

        return response()->json(['apiVersion' => '1.0', 'record' => $data]);
    }

    private function formatIssue(Issue $issue, string $slug): array
    {
        return [
            'id'             => $issue->id,
            'volume'         => $issue->volume,
            'number'         => $issue->number,
            'year'           => $issue->year,
            'title'          => $issue->title,
            'label'          => $issue->getLabel(),
            'date_published' => $issue->date_published?->toDateString(),
            'doi'            => $issue->doi,
            'cover_image'    => $issue->cover_image ? asset('storage/' . $issue->cover_image) : null,
            '_links'         => [
                'self'     => url("/api/v1/journals/{$slug}/issues/{$issue->id}"),
                'articles' => url("/api/v1/journals/{$slug}/articles?issue_id={$issue->id}"),
            ],
        ];
    }
}

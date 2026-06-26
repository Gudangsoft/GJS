<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Journal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request, string $slug): JsonResponse
    {
        $journal = Journal::where('slug', $slug)->where('enabled', true)->firstOrFail();
        $perPage = min((int) $request->get('per_page', 20), 100);

        $query = Article::where('journal_id', $journal->id)
            ->whereHas('issue', fn($q) => $q->where('published', true))
            ->with(['submission.contributors', 'issue', 'galleys'])
            ->orderByDesc('date_published');

        if ($issueId = $request->get('issue_id')) {
            $query->where('issue_id', (int) $issueId);
        }

        if ($sectionId = $request->get('section_id')) {
            $query->where('section_id', (int) $sectionId);
        }

        if ($year = $request->get('year')) {
            $query->whereHas('issue', fn($q) => $q->where('year', (int) $year));
        }

        $articles = $query->paginate($perPage);
        $items    = $articles->map(fn($a) => $this->formatArticle($a, $slug));

        return response()->json([
            'apiVersion' => '1.0',
            'count'      => $articles->total(),
            'lastPage'   => $articles->lastPage(),
            'nextOffset' => $articles->currentPage() < $articles->lastPage()
                            ? $articles->currentPage() + 1 : null,
            'items'      => $items->values(),
        ]);
    }

    public function show(string $slug, int $articleId): JsonResponse
    {
        $journal = Journal::where('slug', $slug)->where('enabled', true)->firstOrFail();
        $article = Article::where('id', $articleId)
            ->where('journal_id', $journal->id)
            ->whereHas('issue', fn($q) => $q->where('published', true))
            ->with(['submission.contributors', 'issue', 'section', 'galleys'])
            ->firstOrFail();

        $data = $this->formatArticle($article, $slug);

        // Full detail fields
        $sub = $article->submission;
        $data['abstract']    = $sub?->abstract;
        $data['keywords']    = $sub?->keywords ?? [];
        $data['disciplines'] = $sub?->disciplines ?? [];
        $data['locale']      = $sub?->locale ?? $journal->primary_locale;
        $data['section']     = $article->section ? [
            'id'    => $article->section->id,
            'title' => $article->section->title,
        ] : null;
        $data['galleys'] = ($article->galleys ?? collect())->map(fn($g) => [
            'id'         => $g->id,
            'label'      => $g->label,
            'locale'     => $g->locale,
            'remote_url' => $g->remote_url,
            'url'        => route('journals.articles.galley', [$slug, $article->id, $g->id]),
        ])->values();

        return response()->json(['apiVersion' => '1.0', 'record' => $data]);
    }

    private function formatArticle(Article $article, string $slug): array
    {
        $sub = $article->submission;
        return [
            'id'             => $article->id,
            'title'          => $sub?->title,
            'doi'            => $article->doi,
            'pages'          => $article->pages,
            'date_published' => $article->date_published?->toDateString(),
            'views'          => $article->views,
            'downloads'      => $article->downloads,
            'citations'      => $article->citations,
            'issue'          => $article->issue ? [
                'id'     => $article->issue->id,
                'label'  => $article->issue->getLabel(),
                'volume' => $article->issue->volume,
                'number' => $article->issue->number,
                'year'   => $article->issue->year,
            ] : null,
            'authors' => ($sub?->contributors ?? collect())->map(fn($c) => [
                'given_name'  => $c->first_name,
                'family_name' => $c->last_name,
                'affiliation' => $c->affiliation,
                'country'     => $c->country,
                'orcid'       => $c->orcid,
                'is_primary'  => (bool) $c->is_primary_contact,
            ])->values(),
            '_links' => [
                'self'    => url("/api/v1/journals/{$slug}/articles/{$article->id}"),
                'journal' => url("/api/v1/journals/{$slug}"),
            ],
        ];
    }
}

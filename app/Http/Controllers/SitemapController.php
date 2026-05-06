<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Issue;
use App\Models\Journal;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect();

        // Static pages
        $urls->push(['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0']);

        // Journals
        $journals = Journal::where('status', 'active')->where('enabled', true)->get();
        foreach ($journals as $journal) {
            $urls->push([
                'loc'        => route('journals.home', $journal->slug),
                'changefreq' => 'weekly',
                'priority'   => '0.9',
            ]);
            $urls->push([
                'loc'        => route('journals.issues', $journal->slug),
                'changefreq' => 'weekly',
                'priority'   => '0.7',
            ]);

            // Published issues
            $issues = Issue::where('journal_id', $journal->id)
                ->where('published', true)
                ->select(['id', 'journal_id', 'updated_at'])
                ->get();

            foreach ($issues as $issue) {
                $urls->push([
                    'loc'        => route('journals.issues.show', [$journal->slug, $issue->id]),
                    'changefreq' => 'monthly',
                    'priority'   => '0.6',
                    'lastmod'    => $issue->updated_at?->toAtomString(),
                ]);
            }
        }

        // Published articles
        $articles = Article::with('journal:id,slug')
            ->whereHas('submission', fn ($q) => $q->where('status', 'published'))
            ->whereNotNull('date_published')
            ->select(['id', 'journal_id', 'date_published', 'updated_at'])
            ->orderByDesc('date_published')
            ->get();

        foreach ($articles as $article) {
            if (!$article->journal) continue;
            $urls->push([
                'loc'        => route('journals.articles.show', [$article->journal->slug, $article->id]),
                'changefreq' => 'yearly',
                'priority'   => '0.8',
                'lastmod'    => $article->updated_at?->toAtomString(),
            ]);
        }

        $xml = $this->buildXml($urls);

        return response($xml, 200, [
            'Content-Type'  => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function buildXml($urls): string
    {
        $items = $urls->map(function ($url) {
            $lastmod  = isset($url['lastmod']) ? "<lastmod>{$url['lastmod']}</lastmod>" : '';
            return <<<XML
    <url>
        <loc>{$this->esc($url['loc'])}</loc>
        {$lastmod}
        <changefreq>{$url['changefreq']}</changefreq>
        <priority>{$url['priority']}</priority>
    </url>
XML;
        })->implode("\n");

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$items}
</urlset>
XML;
    }

    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}

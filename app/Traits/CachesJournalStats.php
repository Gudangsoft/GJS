<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Shared helper: fetch journal statistics in a single SQL round-trip, cached 5 minutes.
 */
trait CachesJournalStats
{
    protected function getJournalStats(int $journalId): array
    {
        return Cache::remember("journal_stats:{$journalId}", 300, function () use ($journalId) {
            // One query for articles aggregate
            $articleStats = DB::table('articles')
                ->where('journal_id', $journalId)
                ->selectRaw('COUNT(*) as articles, SUM(views) as views, SUM(downloads) as downloads, SUM(citations) as citations')
                ->first();

            // One query for published issues
            $issueCount = DB::table('issues')
                ->where('journal_id', $journalId)
                ->where('published', true)
                ->count();

            return [
                'articles'  => (int) ($articleStats->articles  ?? 0),
                'issues'    => (int) $issueCount,
                'views'     => (int) ($articleStats->views     ?? 0),
                'downloads' => (int) ($articleStats->downloads ?? 0),
                'citations' => (int) ($articleStats->citations ?? 0),
            ];
        });
    }
}

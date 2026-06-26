<?php

use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\IssueController;
use App\Http\Controllers\Api\V1\JournalController;
use App\Http\Controllers\Api\V1\KeywordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GJS Public REST API v1
|--------------------------------------------------------------------------
|
| Endpoint publik untuk metadata jurnal, terbitan, dan artikel.
| Tidak memerlukan autentikasi. Rate limit: 60 req/menit per IP.
|
| Base URL: /api/v1
|
*/

Route::prefix('v1')->middleware(['throttle:60,1'])->group(function () {

    // ── Journals ───────────────────────────────────────────────────────────
    Route::get('/journals',                          [JournalController::class, 'index']);
    Route::get('/journals/{slug}',                   [JournalController::class, 'show']);
    Route::get('/journals/{slug}/stats',             [JournalController::class, 'stats']);

    // ── Issues ─────────────────────────────────────────────────────────────
    Route::get('/journals/{slug}/issues',            [IssueController::class, 'index']);
    Route::get('/journals/{slug}/issues/{issueId}',  [IssueController::class, 'show']);

    // ── Articles ───────────────────────────────────────────────────────────
    Route::get('/journals/{slug}/articles',            [ArticleController::class, 'index']);
    Route::get('/journals/{slug}/articles/{articleId}',[ArticleController::class, 'show']);

    // ── Keywords autocomplete ──────────────────────────────────────────────
    Route::get('/keywords/suggest', [KeywordController::class, 'suggest']);

    // ── API Info ───────────────────────────────────────────────────────────
    Route::get('/', function () {
        return response()->json([
            'apiVersion' => '1.0',
            'application' => config('app.name'),
            'description' => 'GJS Public Journal API — OJS-compatible REST API',
            'endpoints' => [
                'GET /api/v1/journals'                              => 'List all journals',
                'GET /api/v1/journals/{slug}'                       => 'Journal detail',
                'GET /api/v1/journals/{slug}/stats'                 => 'Journal statistics',
                'GET /api/v1/journals/{slug}/issues'                => 'List issues (published)',
                'GET /api/v1/journals/{slug}/issues/{id}'           => 'Issue detail with articles',
                'GET /api/v1/journals/{slug}/articles'              => 'List articles (?issue_id, ?year, ?section_id, ?per_page)',
                'GET /api/v1/journals/{slug}/articles/{id}'         => 'Article detail (full metadata)',
                'GET /api/v1/keywords/suggest?q=...&locale=id'      => 'Keyword autocomplete',
            ],
        ]);
    });
});

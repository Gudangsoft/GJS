<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 20), 100);

        $journals = Journal::where('enabled', true)
            ->select([
                'id', 'slug', 'name', 'name_abbrev', 'description',
                'issn_print', 'issn_online', 'publisher', 'email',
                'primary_locale', 'country', 'url', 'logo',
                'sinta_level', 'sinta_id', 'doaj_id', 'garuda_id',
                'focus_scope', 'license_type', 'doi_prefix',
            ])
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'apiVersion' => '1.0',
            'count'      => $journals->total(),
            'lastPage'   => $journals->lastPage(),
            'nextOffset' => $journals->currentPage() < $journals->lastPage()
                            ? $journals->currentPage() + 1 : null,
            'items'      => $journals->items(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $journal = Journal::where('slug', $slug)->where('enabled', true)->firstOrFail();

        return response()->json([
            'apiVersion' => '1.0',
            'record'     => [
                'id'              => $journal->id,
                'slug'            => $journal->slug,
                'name'            => $journal->name,
                'name_abbrev'     => $journal->name_abbrev,
                'description'     => $journal->description,
                'issn_print'      => $journal->issn_print,
                'issn_online'     => $journal->issn_online,
                'publisher'       => $journal->publisher,
                'email'           => $journal->email,
                'primary_locale'  => $journal->primary_locale,
                'country'         => $journal->country,
                'url'             => $journal->url,
                'logo'            => $journal->logo ? asset('storage/' . $journal->logo) : null,
                'focus_scope'     => $journal->focus_scope,
                'license_type'    => $journal->license_type,
                'doi_prefix'      => $journal->doi_prefix,
                'sinta_level'     => $journal->sinta_level,
                'sinta_id'        => $journal->sinta_id,
                'doaj_id'         => $journal->doaj_id,
                'garuda_id'       => $journal->garuda_id,
                'open_access'     => true,
                '_links'          => [
                    'issues'   => url("/api/v1/journals/{$journal->slug}/issues"),
                    'articles' => url("/api/v1/journals/{$journal->slug}/articles"),
                ],
            ],
        ]);
    }

    public function stats(string $slug): JsonResponse
    {
        $journal = Journal::where('slug', $slug)->where('enabled', true)->firstOrFail();

        return response()->json([
            'apiVersion' => '1.0',
            'record'     => [
                'total_articles' => $journal->articles()->count(),
                'total_issues'   => $journal->issues()->where('published', true)->count(),
                'total_views'    => $journal->articles()->sum('views'),
                'total_downloads'=> $journal->articles()->sum('downloads'),
            ],
        ]);
    }
}

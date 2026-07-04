<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleGalley;
use App\Models\Journal;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class GalleyController extends Controller
{
    public function __invoke(Journal $journal, Article $article, ArticleGalley $galley): mixed
    {
        abort_unless($article->journal_id === $journal->id, 404);
        abort_unless($galley->article_id === $article->id, 404);
        abort_unless($galley->is_approved, 404);

        // Remote URL galley (hosted externally) — validate URL before redirecting
        if ($galley->remote_url) {
            $url = $galley->remote_url;
            $scheme = strtolower(parse_url($url, PHP_URL_SCHEME) ?? '');
            abort_unless(in_array($scheme, ['http', 'https'], true), 422, 'URL galley tidak valid.');

            $galley->increment('views');
            $article->increment('downloads');
            return redirect()->away($url);
        }

        // Local file
        $file = $galley->loadMissing('file')->file;

        if (!$file || !$file->path) {
            return redirect()
                ->route('journals.articles.show', [$journal->slug, $article->id])
                ->with('error', 'File galley "' . $galley->label . '" belum tersedia. Silakan hubungi editor jurnal.');
        }

        if (!Storage::disk('public')->exists($file->path)) {
            return redirect()
                ->route('journals.articles.show', [$journal->slug, $article->id])
                ->with('error', 'File galley tidak ditemukan di server. Silakan hubungi editor jurnal.');
        }

        $galley->increment('views');
        $article->increment('downloads');

        $filename = $file->original_file_name
            ?? 'galley-' . $galley->id . '.' . pathinfo($file->stored_file_name, PATHINFO_EXTENSION);

        $mimeType   = $file->mime_type ?? 'application/octet-stream';
        $isPdf      = str_contains($mimeType, 'pdf');
        $forceDownload = request()->boolean('dl');

        // PDF without ?dl=1 → serve inline so the browser viewer page can embed it
        if ($isPdf && !$forceDownload) {
            return response(
                Storage::disk('public')->get($file->path),
                200,
                [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                    'Cache-Control'       => 'private, max-age=3600',
                ]
            );
        }

        return Storage::disk('public')->download($file->path, $filename, [
            'Content-Type' => $mimeType,
        ]);
    }
}

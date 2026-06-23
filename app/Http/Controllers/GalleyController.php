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

        // Remote URL galley (hosted externally)
        if ($galley->remote_url) {
            $galley->increment('views');
            $article->increment('downloads');
            return redirect()->away($galley->remote_url);
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

        return Storage::disk('public')->download($file->path, $filename, [
            'Content-Type' => $file->mime_type ?? 'application/octet-stream',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleGalley;
use App\Models\Journal;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleyViewerController extends Controller
{
    public function __invoke(Journal $journal, Article $article, ArticleGalley $galley): View
    {
        abort_unless($article->journal_id === $journal->id, 404);
        abort_unless($galley->article_id === $article->id, 404);
        abort_unless($galley->is_approved, 404);

        $article->loadMissing(['submission.contributors', 'issue']);

        // HTML galley — render inline
        if ($galley->isHtml()) {
            $galley->increment('views');
            $article->increment('downloads');
            return view('reader.galley-viewer', [
                'journal'     => $journal,
                'article'     => $article,
                'galley'      => $galley,
                'pdfUrl'      => null,
                'htmlContent' => $galley->html_content,
            ]);
        }

        // Resolve direct storage URL so the iframe doesn't go through the PHP router
        // (avoids single-threaded server deadlock with built-in dev server).
        $pdfUrl = null;
        if ($galley->remote_url) {
            $pdfUrl = $galley->remote_url;
        } elseif ($galley->submission_file_id) {
            $file = $galley->loadMissing('file')->file;
            if ($file && Storage::disk('public')->exists($file->path)) {
                $pdfUrl = Storage::disk('public')->url($file->path);
            }
        }

        return view('reader.galley-viewer', [
            'journal'     => $journal,
            'article'     => $article,
            'galley'      => $galley,
            'pdfUrl'      => $pdfUrl,
            'htmlContent' => null,
        ]);
    }
}

<?php

use App\Http\Controllers\Auth\OrcidController;
use App\Http\Controllers\GalleyController;
use App\Http\Controllers\GalleyViewerController;
use App\Http\Controllers\JournalOaiController;
use App\Http\Controllers\OaiPmhController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Author\Dashboard;
use App\Livewire\Author\SubmissionDetail;
use App\Livewire\Author\SubmissionWizard;
use App\Livewire\Editor\Dashboard as EditorDashboard;
use App\Livewire\JournalManager\Dashboard as JournalManagerDashboard;
use App\Livewire\Editor\SubmissionReview;
use App\Livewire\Reader\ArticleDetail;
use App\Livewire\Reader\IssueArchive;
use App\Livewire\Reader\IssueToc;
use App\Livewire\Reader\JournalBrowse;
use App\Livewire\Reader\JournalHome;
use App\Livewire\Reader\JournalIndex;
use App\Livewire\Reader\JournalPage;
use App\Livewire\Reader\JournalSearch;
use App\Livewire\Reviewer\Dashboard as ReviewerDashboard;
use App\Livewire\Reviewer\ReviewForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

// ─── Public Reader Portal ────────────────────────────────────────────────────
Route::get('/', JournalIndex::class)->name('home');

Route::prefix('journals/{journal:slug}')->name('journals.')->group(function () {
    Route::get('/',                                        JournalHome::class)->name('home');
    Route::get('/issues',                                  IssueArchive::class)->name('issues');
    Route::get('/issues/{issue}',                          IssueToc::class)->name('issues.show');
    Route::get('/articles/{article}',                      ArticleDetail::class)->name('articles.show');
    Route::get('/articles/{article}/galley/{galley}',      GalleyController::class)->name('articles.galley');
    Route::get('/articles/{article}/galley/{galley}/view', GalleyViewerController::class)->name('articles.galley.view');
    Route::get('/search',                                  JournalSearch::class)->name('search');
    Route::get('/browse/{by}',                             JournalBrowse::class)->name('browse');
    Route::get('/about/{page?}',                           JournalPage::class)->name('page');
    Route::get('/oai',                                     JournalOaiController::class)->name('oai');
});

// ─── Public LOA Verification (no auth required — QR code accessible to anyone) ──
Route::get('/loa/verify/{code}', function (string $code) {
    // Rate limit: 30 verifications/minute per IP to prevent brute force enumeration
    $key = 'loa-verify:' . request()->ip();
    if (RateLimiter::tooManyAttempts($key, 30)) {
        abort(429, 'Terlalu banyak permintaan. Coba lagi nanti.');
    }
    RateLimiter::hit($key, 60);

    // Only allow alphanumeric + dash, exact format check
    if (!preg_match('/^[A-Z0-9]{8}-[A-Z0-9]{8}-[A-Z0-9]{8}$/', strtoupper($code))) {
        $loa = null;
    } else {
        $loa = \App\Models\LetterOfAcceptance::where('verification_code', strtoupper($code))
            ->with('journal', 'submission', 'issuedBy')
            ->first();
    }
    return view('loa.verify', compact('loa', 'code'));
})->name('loa.verify')->middleware('throttle:30,1');

// ─── Author Area ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasAnyRole(['journal_manager', 'editor', 'super_admin'])) {
            return redirect()->route('manager.dashboard');
        }
        return redirect()->route('dashboard.author');
    })->name('dashboard');

    Route::get('/dashboard/author',          Dashboard::class)->name('dashboard.author');
    Route::get('/submit',                    SubmissionWizard::class)->name('submit');
    Route::get('/submissions/{submission}',  SubmissionDetail::class)->name('submissions.show');
});

// ─── Journal Manager Area ─────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'journal.access:editor'])->group(function () {
    Route::get('/manager/dashboard',     \App\Livewire\JournalManager\Dashboard::class)->name('manager.dashboard');
    Route::get('/manager/submissions',   \App\Livewire\JournalManager\Submissions::class)->name('manager.submissions');
    Route::get('/manager/reviews',       \App\Livewire\JournalManager\Reviews::class)->name('manager.reviews');
    Route::get('/manager/issues',        \App\Livewire\JournalManager\Issues::class)->name('manager.issues');
    Route::get('/manager/sections',      \App\Livewire\JournalManager\Sections::class)->name('manager.sections');
    Route::get('/manager/announcements', \App\Livewire\JournalManager\Announcements::class)->name('manager.announcements');
    Route::get('/manager/plugins',       \App\Livewire\JournalManager\Plugins::class)->name('manager.plugins');
    Route::get('/manager/menu',          \App\Livewire\JournalManager\Menu::class)->name('manager.menu');
    Route::get('/manager/users',         \App\Livewire\JournalManager\Users::class)->name('manager.users');
    Route::get('/manager/loa',           \App\Livewire\JournalManager\Loa::class)->name('manager.loa');
    Route::get('/manager/email-blast',   \App\Livewire\JournalManager\EmailBlast::class)->name('manager.email-blast');
    Route::get('/manager/wa-blast',      \App\Livewire\JournalManager\WaBlast::class)->name('manager.wa-blast');

    // Manager-only (pengelola only, not regular editor)
    Route::middleware('journal.access:manager')->group(function () {
        Route::get('/manager/settings',   \App\Livewire\JournalManager\Settings::class)->name('manager.settings');
        Route::get('/manager/pages',      \App\Livewire\JournalManager\Pages::class)->name('manager.pages');
        Route::get('/manager/ojs-import', \App\Livewire\JournalManager\OjsImport::class)->name('manager.ojs-import');
    });

    Route::get('/loa/{loa}/preview', function (\App\Models\LetterOfAcceptance $loa) {
        // Only allow access to LOA of journals the user manages/edits
        $user = auth()->user();
        $hasAccess = $user->hasAnyRole(['super_admin', 'admin'])
            || $loa->journal->managers()->where('users.id', $user->id)->exists()
            || $loa->journal->editors()->where('users.id', $user->id)->exists()
            || $loa->submission?->user_id === $user->id;  // author of submission
        abort_unless($hasAccess, 403);
        $loa->load('journal', 'submission', 'issuedBy');
        return view('loa.preview', compact('loa'));
    })->name('loa.preview');

    Route::post('/manager/switch-journal', function (\Illuminate\Http\Request $req) {
        $journalId = (int) $req->input('journal_id');
        $user = auth()->user();
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            $exists = \App\Models\Journal::where('id', $journalId)->exists();
            if ($exists) session(['manager_active_journal' => $journalId]);
        } else {
            $allowed = \App\Models\Journal::whereHas('managers', fn($q) => $q->where('users.id', $user->id))
                ->orWhereHas('editors', fn($q) => $q->where('users.id', $user->id))
                ->pluck('id');
            if ($allowed->contains($journalId)) {
                session(['manager_active_journal' => $journalId]);
            }
        }
        return redirect()->back();
    })->name('manager.switch-journal');
});

// ─── Editor Area ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'journal.access:editor'])
    ->prefix('editor')->name('editor.')->group(function () {
    Route::get('/dashboard',                       EditorDashboard::class)->name('dashboard');
    Route::get('/submissions/{submission}/review', SubmissionReview::class)->name('submissions.review');
});

// ─── Reviewer Area ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'journal.access:reviewer'])
    ->prefix('reviewer')->name('reviewer.')->group(function () {
    Route::get('/dashboard',                      ReviewerDashboard::class)->name('dashboard');
    Route::get('/assignments/{assignment}/review', ReviewForm::class)->name('review');
});

// ─── Impersonation (super_admin only) ────────────────────────────────────────
Route::get('/impersonate/stop', function () {
    $impersonatorId = session('impersonator_id');
    if (!$impersonatorId) {
        abort(403);
    }
    // Only the original super_admin session can stop impersonation
    session()->forget(['impersonator_id', 'impersonating_as']);
    Auth::loginUsingId($impersonatorId);
    return redirect('/admin/users');
})->name('impersonate.stop')->middleware(['auth', 'verified']);

// ─── ORCID OAuth ──────────────────────────────────────────────────────────────
Route::get('/auth/orcid',          [OrcidController::class, 'redirect'])->name('orcid.redirect');
Route::get('/auth/orcid/callback', [OrcidController::class, 'callback'])->name('orcid.callback');

// ─── Protocol & Discovery ────────────────────────────────────────────────────
Route::get('/oai',         OaiPmhController::class)->name('oai');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', function () {
    return response(view('robots')->render(), 200, ['Content-Type' => 'text/plain']);
})->name('robots');

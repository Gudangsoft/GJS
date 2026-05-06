<?php

use App\Http\Controllers\Auth\OrcidController;
use App\Http\Controllers\OaiPmhController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Author\Dashboard;
use App\Livewire\Author\SubmissionDetail;
use App\Livewire\Author\SubmissionWizard;
use App\Livewire\Editor\Dashboard as EditorDashboard;
use App\Livewire\Editor\SubmissionReview;
use App\Livewire\Reader\ArticleDetail;
use App\Livewire\Reader\IssueArchive;
use App\Livewire\Reader\IssueToc;
use App\Livewire\Reader\JournalHome;
use App\Livewire\Reader\JournalIndex;
use App\Livewire\Reviewer\Dashboard as ReviewerDashboard;
use App\Livewire\Reviewer\ReviewForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─── Public Reader Portal ────────────────────────────────────────────────────
Route::get('/', JournalIndex::class)->name('home');

Route::prefix('journals/{journal:slug}')->name('journals.')->group(function () {
    Route::get('/',                 JournalHome::class)->name('home');
    Route::get('/issues',           IssueArchive::class)->name('issues');
    Route::get('/issues/{issue}',   IssueToc::class)->name('issues.show');
    Route::get('/articles/{article}', ArticleDetail::class)->name('articles.show');
});

// ─── Author Area ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard',                  Dashboard::class)->name('dashboard');
    Route::get('/submit',                     SubmissionWizard::class)->name('submit');
    Route::get('/submissions/{submission}',   SubmissionDetail::class)->name('submissions.show');
});

// ─── Editor Area ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('editor')->name('editor.')->group(function () {
    Route::get('/dashboard',                          EditorDashboard::class)->name('dashboard');
    Route::get('/submissions/{submission}/review',    SubmissionReview::class)->name('submissions.review');
});

// ─── Reviewer Area ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('reviewer')->name('reviewer.')->group(function () {
    Route::get('/dashboard',                          ReviewerDashboard::class)->name('dashboard');
    Route::get('/assignments/{assignment}/review',    ReviewForm::class)->name('review');
});

// ─── Impersonation ────────────────────────────────────────────────────────────
Route::get('/impersonate/stop', function () {
    $impersonatorId = session('impersonator_id');
    if (!$impersonatorId) return redirect('/dashboard');

    session()->forget(['impersonator_id', 'impersonating_as']);
    Auth::loginUsingId($impersonatorId);
    return redirect('/admin/users');
})->name('impersonate.stop')->middleware('auth');

// ─── ORCID OAuth ──────────────────────────────────────────────────────────────
Route::get('/auth/orcid',          [OrcidController::class, 'redirect'])->name('orcid.redirect');
Route::get('/auth/orcid/callback', [OrcidController::class, 'callback'])->name('orcid.callback');

// ─── Protocol & Discovery ────────────────────────────────────────────────────
Route::get('/oai',         OaiPmhController::class)->name('oai');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

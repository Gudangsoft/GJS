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
use App\Livewire\Reader\JournalHome;
use App\Livewire\Reader\JournalIndex;
use App\Livewire\Reviewer\Dashboard as ReviewerDashboard;
use App\Livewire\Reviewer\ReviewForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─── Public Reader Portal ────────────────────────────────────────────────────
Route::get('/', JournalIndex::class)->name('home');

Route::prefix('journals/{journal:slug}')->name('journals.')->group(function () {
    Route::get('/',                                         JournalHome::class)->name('home');
    Route::get('/issues',                                   IssueArchive::class)->name('issues');
    Route::get('/issues/{issue}',                           IssueToc::class)->name('issues.show');
    Route::get('/articles/{article}',                       ArticleDetail::class)->name('articles.show');
    Route::get('/articles/{article}/galley/{galley}',        GalleyController::class)->name('articles.galley');
    Route::get('/articles/{article}/galley/{galley}/view',  GalleyViewerController::class)->name('articles.galley.view');
    Route::get('/oai',                                      JournalOaiController::class)->name('oai');
});

// ─── Author Area ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasAnyRole(['journal_manager', 'editor', 'super_admin'])) {
            return redirect()->route('manager.dashboard');
        }
        return redirect()->route('dashboard.author');
    })->name('dashboard');
    Route::get('/dashboard/author',           Dashboard::class)->name('dashboard.author');
    Route::get('/submit',                     SubmissionWizard::class)->name('submit');
    Route::get('/submissions/{submission}',   SubmissionDetail::class)->name('submissions.show');
});

// ─── Journal Manager Area ─────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager/dashboard',     \App\Livewire\JournalManager\Dashboard::class)->name('manager.dashboard');
    Route::get('/manager/submissions',   \App\Livewire\JournalManager\Submissions::class)->name('manager.submissions');
    Route::get('/manager/reviews',       \App\Livewire\JournalManager\Reviews::class)->name('manager.reviews');
    Route::get('/manager/issues',        \App\Livewire\JournalManager\Issues::class)->name('manager.issues');
    Route::get('/manager/sections',      \App\Livewire\JournalManager\Sections::class)->name('manager.sections');
    Route::get('/manager/announcements', \App\Livewire\JournalManager\Announcements::class)->name('manager.announcements');
    Route::get('/manager/plugins',       \App\Livewire\JournalManager\Plugins::class)->name('manager.plugins');
    Route::get('/manager/settings',      \App\Livewire\JournalManager\Settings::class)->name('manager.settings');
    Route::get('/manager/users',         \App\Livewire\JournalManager\Users::class)->name('manager.users');
    Route::get('/manager/loa',           \App\Livewire\JournalManager\Loa::class)->name('manager.loa');
    Route::get('/manager/email-blast',   \App\Livewire\JournalManager\EmailBlast::class)->name('manager.email-blast');
    Route::get('/manager/wa-blast',      \App\Livewire\JournalManager\WaBlast::class)->name('manager.wa-blast');
    Route::get('/loa/{loa}/preview', function (\App\Models\LetterOfAcceptance $loa) {
        $loa->load('journal', 'submission', 'issuedBy');
        return view('loa.preview', compact('loa'));
    })->name('loa.preview');

    Route::get('/loa/verify/{code}', function (string $code) {
        $loa = \App\Models\LetterOfAcceptance::where('verification_code', $code)
            ->with('journal', 'submission', 'issuedBy')
            ->first();
        return view('loa.verify', compact('loa', 'code'));
    })->name('loa.verify');

    Route::post('/manager/switch-journal', function (\Illuminate\Http\Request $req) {
        $journalId = (int) $req->input('journal_id');
        $user = auth()->user();
        $allowed = \App\Models\Journal::whereHas('managers', fn($q) => $q->where('users.id', $user->id))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', $user->id))
            ->pluck('id');
        if ($allowed->contains($journalId)) {
            session(['manager_active_journal' => $journalId]);
        }
        return redirect()->back();
    })->name('manager.switch-journal');
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
Route::get('/robots.txt', function () {
    $content = view('robots')->render();
    return response($content, 200, ['Content-Type' => 'text/plain']);
})->name('robots');

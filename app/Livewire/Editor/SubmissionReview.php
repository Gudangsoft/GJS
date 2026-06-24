<?php

namespace App\Livewire\Editor;

use App\Mail\EditorialDecision;
use App\Mail\ReviewInvitation;
use App\Models\PlagiarismCheck;
use App\Models\ReviewAssignment;
use App\Models\ReviewRound;
use App\Models\Submission;
use App\Models\User;
use App\Services\PlagiarismService;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class SubmissionReview extends Component
{
    public Submission $submission;

    // Assign reviewer form
    #[Validate('required|exists:users,id')]
    public ?int $reviewerId = null;

    #[Validate('required|in:single_blind,double_blind,open')]
    public string $reviewMethod = 'double_blind';

    #[Validate('required|date|after:today')]
    public string $dateDue = '';

    #[Validate('required|date|after:today')]
    public string $dateResponseDue = '';

    // Decision form
    #[Validate('required|in:accepted,revision_required,declined')]
    public string $decision = '';

    #[Validate('required|min:10')]
    public string $decisionMessage = '';

    public function mount(Submission $submission): void
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['super_admin', 'admin'])) {
            $submission->loadMissing('journal');
            $hasAccess = $submission->journal->managers()->where('users.id', $user->id)->exists()
                || $submission->journal->editors()->where('users.id', $user->id)->exists();
            abort_unless($hasAccess, 403, 'Anda tidak memiliki akses ke submission ini.');
        }

        $this->submission = $submission;
        $this->dateDue = now()->addWeeks(4)->format('Y-m-d');
        $this->dateResponseDue = now()->addWeeks(1)->format('Y-m-d');
    }

    public function assignReviewer(): void
    {
        $this->validateOnly('reviewerId');
        $this->validateOnly('reviewMethod');
        $this->validateOnly('dateDue');
        $this->validateOnly('dateResponseDue');

        $round = ReviewRound::firstOrCreate(
            ['submission_id' => $this->submission->id, 'round' => 1],
            ['status' => 'awaiting_reviewers']
        );

        $assignment = ReviewAssignment::create([
            'submission_id'     => $this->submission->id,
            'review_round_id'   => $round->id,
            'reviewer_id'       => $this->reviewerId,
            'editor_id'         => auth()->id(),
            'status'            => 'awaiting_response',
            'review_method'     => $this->reviewMethod,
            'round'             => 1,
            'date_assigned'     => now(),
            'date_notified'     => now(),
            'date_due'          => $this->dateDue,
            'date_response_due' => $this->dateResponseDue,
        ]);

        $this->submission->update(['status' => 'assigned']);

        Mail::to($assignment->reviewer->email)->queue(new ReviewInvitation($assignment));

        $this->reviewerId = null;
        $this->submission->refresh();

        $this->dispatch('close-assign-modal');
        session()->flash('success', 'Reviewer berhasil ditugaskan dan undangan email dikirim.');
    }

    public function cancelAssignment(int $assignmentId): void
    {
        ReviewAssignment::findOrFail($assignmentId)->update([
            'status'         => 'cancelled',
            'date_cancelled' => now(),
        ]);

        $this->submission->refresh();
        session()->flash('success', 'Penugasan reviewer dibatalkan.');
    }

    public function runPlagiarismCheck(): void
    {
        $check = (new PlagiarismService)->check($this->submission);
        $this->submission->refresh();
        session()->flash('plagiarism_done', "Cek selesai — skor kemiripan: {$check->overall_score}%");
    }

    public function makeDecision(): void
    {
        $this->validateOnly('decision');
        $this->validateOnly('decisionMessage');

        $this->submission->update(['status' => $this->decision]);

        Mail::to($this->submission->submitter->email)
            ->queue(new EditorialDecision($this->submission, $this->decision, $this->decisionMessage));

        $this->decision = '';
        $this->decisionMessage = '';
        $this->submission->refresh();

        $this->dispatch('close-decision-modal');
        session()->flash('success', 'Keputusan editorial berhasil dikirim ke penulis.');
    }

    public function render()
    {
        $this->submission->load([
            'submitter', 'journal', 'section',
            'files', 'contributors',
            'reviewRounds.assignments.reviewer',
            'reviewRounds.assignments.review',
        ]);

        $assignments = ReviewAssignment::where('submission_id', $this->submission->id)
            ->with(['reviewer', 'review', 'reviewRound'])
            ->latest()
            ->get();

        $assignedIds = $assignments->whereNotIn('status', ['cancelled'])->pluck('reviewer_id');

        $availableReviewers = User::role('reviewer')
            ->whereNotIn('id', $assignedIds)
            ->orderBy('last_name')
            ->get();

        $plagiarismCheck = PlagiarismCheck::where('submission_id', $this->submission->id)
            ->latest('checked_at')
            ->first();

        return view('livewire.editor.submission-review', compact('assignments', 'availableReviewers', 'plagiarismCheck'))
            ->title('Kelola Review — ' . \Str::limit($this->submission->title, 50));
    }
}

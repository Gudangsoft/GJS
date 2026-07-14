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

        $activityTimeline = $this->buildActivityTimeline($assignments);

        return view('livewire.editor.submission-review', compact('assignments', 'availableReviewers', 'plagiarismCheck', 'activityTimeline'))
            ->title('Kelola Review — ' . \Str::limit($this->submission->title, 50));
    }

    private function buildActivityTimeline($assignments): array
    {
        $events = [];

        // Submission received
        if ($this->submission->submitted_at) {
            $events[] = [
                'at'    => $this->submission->submitted_at,
                'label' => 'Naskah Dikirim',
                'note'  => 'Oleh: ' . ($this->submission->submitter->full_name ?? 'penulis'),
                'color' => '#2563eb',
                'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            ];
        }

        // Per-assignment events
        $statusLabels = [
            'awaiting_response' => ['Reviewer diundang',    '#d97706'],
            'accepted'          => ['Reviewer menyetujui',  '#7c3aed'],
            'declined'          => ['Reviewer menolak',     '#dc2626'],
            'completed'         => ['Review diserahkan',    '#059669'],
            'cancelled'         => ['Penugasan dibatalkan', '#94a3b8'],
        ];

        foreach ($assignments as $a) {
            $reviewer = $a->reviewer->full_name ?? ('Reviewer #' . $a->reviewer_id);

            if ($a->date_assigned) {
                $events[] = [
                    'at'    => $a->date_assigned,
                    'label' => 'Reviewer diundang',
                    'note'  => $reviewer . ' (putaran ' . $a->round . ')',
                    'color' => '#d97706',
                    'icon'  => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                ];
            }
            if ($a->date_confirmed) {
                $events[] = [
                    'at'    => $a->date_confirmed,
                    'label' => 'Reviewer menyetujui',
                    'note'  => $reviewer,
                    'color' => '#7c3aed',
                    'icon'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                ];
            }
            if ($a->status === 'declined' && $a->date_reminded) {
                $events[] = [
                    'at'    => $a->date_reminded,
                    'label' => 'Reviewer menolak',
                    'note'  => $reviewer,
                    'color' => '#dc2626',
                    'icon'  => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                ];
            }
            if ($a->date_completed) {
                $rec = $a->review?->recommendation;
                $recLabels = ['accept'=>'Terima','pending_revisions'=>'Revisi Minor','resubmit_here'=>'Revisi Mayor','decline'=>'Tolak','see_comments'=>'Lihat Komentar'];
                $events[] = [
                    'at'    => $a->date_completed,
                    'label' => 'Review diserahkan',
                    'note'  => $reviewer . ($rec && isset($recLabels[$rec]) ? ' — ' . $recLabels[$rec] : ''),
                    'color' => '#059669',
                    'icon'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                ];
            }
        }

        // Status changes from Spatie activity log
        $logEntries = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', \App\Models\Submission::class)
            ->where('subject_id', $this->submission->id)
            ->where('event', 'updated')
            ->whereNotNull('properties->attributes->status')
            ->orderBy('created_at')
            ->get();

        $statusEventLabels = [
            'accepted'          => ['Keputusan: Diterima',     '#059669'],
            'declined'          => ['Keputusan: Ditolak',      '#dc2626'],
            'revision_required' => ['Keputusan: Perlu Revisi', '#d97706'],
            'review'            => ['Masuk tahap review',      '#7c3aed'],
            'published'         => ['Diterbitkan',             '#0891b2'],
        ];

        foreach ($logEntries as $log) {
            $newStatus = $log->properties['attributes']['status'] ?? null;
            if ($newStatus && isset($statusEventLabels[$newStatus])) {
                [$lbl, $col] = $statusEventLabels[$newStatus];
                $events[] = [
                    'at'    => $log->created_at,
                    'label' => $lbl,
                    'note'  => 'Oleh editor',
                    'color' => $col,
                    'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                ];
            }
        }

        usort($events, fn($a, $b) => $a['at'] <=> $b['at']);

        return $events;
    }
}

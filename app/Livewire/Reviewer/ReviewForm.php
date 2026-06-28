<?php

namespace App\Livewire\Reviewer;

use App\Models\Review;
use App\Models\ReviewAssignment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.reviewer')]
class ReviewForm extends Component
{
    public ReviewAssignment $assignment;

    #[Validate('required|in:accept,pending_revisions,resubmit_here,resubmit_elsewhere,decline,see_comments')]
    public string $recommendation = '';

    #[Validate('required|min:30')]
    public string $commentsForAuthor = '';

    #[Validate('nullable|min:10')]
    public string $commentsForEditor = '';

    public function mount(ReviewAssignment $assignment): void
    {
        $this->assignment = $assignment;

        // Ensure this reviewer owns this assignment
        abort_if($assignment->reviewer_id !== auth()->id(), 403);
        abort_if(!in_array($assignment->status, ['accepted', 'completed']), 403);

        // Pre-fill if editing existing review
        if ($assignment->review) {
            $this->recommendation    = $assignment->review->recommendation ?? '';
            $this->commentsForAuthor = $assignment->review->comments_for_author ?? '';
            $this->commentsForEditor = $assignment->review->comments_for_editors ?? '';
        }
    }

    public function submitReview(): void
    {
        $this->validate();

        Review::updateOrCreate(
            ['review_assignment_id' => $this->assignment->id],
            [
                'recommendation'      => $this->recommendation,
                'comments_for_author' => $this->commentsForAuthor,
                'comments_for_editors'=> $this->commentsForEditor,
            ]
        );

        $this->assignment->update([
            'status'         => 'completed',
            'date_completed' => now(),
        ]);

        session()->flash('success', 'Review berhasil dikirim. Terima kasih atas kontribusi Anda.');

        $this->redirect(route('reviewer.dashboard'));
    }

    public function render()
    {
        $this->assignment->load(['submission.journal', 'submission.section', 'submission.contributors', 'submission.files']);

        return view('livewire.reviewer.review-form')
            ->title('Form Review — ' . \Str::limit($this->assignment->submission->title, 50));
    }
}

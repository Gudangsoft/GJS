<?php

namespace App\Livewire\Reviewer;

use App\Models\Review;
use App\Models\ReviewAssignment;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Validate;

#[Layout('layouts.reviewer')]
class ReviewForm extends Component
{
    public ReviewAssignment $assignment;

    public int $step = 1;

    // Step 3 — core review fields
    public string $recommendation     = '';
    public string $commentsForAuthor  = '';
    public string $commentsForEditor  = '';
    public string $competingInterests = '';
    public bool   $noConflict         = false;

    // Review criteria ratings (form_responses)
    public array $criteria = [
        'relevance'   => '',
        'originality' => '',
        'methodology' => '',
        'analysis'    => '',
        'writing'     => '',
        'references'  => '',
    ];

    public function mount(ReviewAssignment $assignment): void
    {
        abort_if($assignment->reviewer_id !== auth()->id(), 403);
        abort_if(!in_array($assignment->status, ['accepted', 'completed']), 403);

        $this->assignment = $assignment->load([
            'submission.journal',
            'submission.section',
            'submission.contributors',
            'submission.files',
            'reviewRound',
            'review',
        ]);

        // Pre-fill if review already submitted
        if ($review = $assignment->review) {
            $this->recommendation    = $review->recommendation ?? '';
            $this->commentsForAuthor = $review->comments_for_author ?? '';
            $this->commentsForEditor = $review->comments_for_editors ?? '';
            if (!empty($review->form_responses)) {
                foreach ($review->form_responses as $k => $v) {
                    if (array_key_exists($k, $this->criteria)) {
                        $this->criteria[$k] = $v;
                    }
                }
            }
        }

        $this->competingInterests = $assignment->competing_interests ?? '';
        $this->noConflict = empty($this->competingInterests);

        // Jump to review form if already completed
        if ($assignment->status === 'completed') {
            $this->step = 3;
        }
    }

    public function nextStep(): void
    {
        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goStep(int $s): void
    {
        if ($s <= $this->step || $this->assignment->status === 'completed') {
            $this->step = $s;
        }
    }

    public function setCriteria(string $key, string $value): void
    {
        if (array_key_exists($key, $this->criteria)) {
            $this->criteria[$key] = $value;
        }
    }

    public function saveProgress(): void
    {
        Review::updateOrCreate(
            ['review_assignment_id' => $this->assignment->id],
            [
                'recommendation'       => $this->recommendation ?: null,
                'comments_for_author'  => $this->commentsForAuthor,
                'comments_for_editors' => $this->commentsForEditor,
                'form_responses'       => $this->criteria,
            ]
        );

        $this->assignment->update([
            'competing_interests' => $this->noConflict ? null : $this->competingInterests,
        ]);

        session()->flash('saved', 'Draft review tersimpan.');
    }

    public function submitReview(): void
    {
        $this->validate([
            'recommendation'    => 'required|in:accept,pending_revisions,resubmit_here,resubmit_elsewhere,decline,see_comments',
            'commentsForAuthor' => 'required|min:30',
            'commentsForEditor' => 'nullable|min:10',
        ], [
            'recommendation.required'    => 'Pilih rekomendasi sebelum mengirim.',
            'commentsForAuthor.required' => 'Komentar untuk penulis wajib diisi.',
            'commentsForAuthor.min'      => 'Komentar untuk penulis minimal 30 karakter.',
        ]);

        Review::updateOrCreate(
            ['review_assignment_id' => $this->assignment->id],
            [
                'recommendation'       => $this->recommendation,
                'comments_for_author'  => $this->commentsForAuthor,
                'comments_for_editors' => $this->commentsForEditor,
                'form_responses'       => array_filter($this->criteria),
            ]
        );

        $this->assignment->update([
            'status'              => 'completed',
            'date_completed'      => now(),
            'competing_interests' => $this->noConflict ? null : $this->competingInterests,
        ]);

        $this->step = 4;
    }

    public function render()
    {
        return view('livewire.reviewer.review-form')
            ->title('Review — ' . \Str::limit($this->assignment->submission->title ?? '', 50));
    }
}

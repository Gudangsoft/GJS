<?php

namespace App\Livewire\Author;

use App\Models\Submission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SubmissionDetail extends Component
{
    public Submission $submission;

    public function mount(Submission $submission): void
    {
        if (!auth()->user()->hasAnyRole(['editor', 'journal_manager', 'super_admin'])) {
            abort_unless($submission->user_id === auth()->id(), 403);
        }

        $this->submission = $submission->load([
            'journal',
            'section',
            'contributors',
            'reviewRounds.assignments.review',
            'reviewRounds.assignments.reviewer',
            'article.issue',
            'article.galleys',
        ]);
    }

    public function render()
    {
        return view('livewire.author.submission-detail')
            ->title('Submission #' . $this->submission->id . ' — ' . $this->submission->title);
    }
}

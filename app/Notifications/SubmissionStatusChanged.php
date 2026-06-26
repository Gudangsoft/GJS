<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubmissionStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        protected Submission $submission,
        protected string $newStatus,
        protected string $message = ''
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'submission_id'    => $this->submission->id,
            'submission_title' => $this->submission->title,
            'status'           => $this->newStatus,
            'message'          => $this->message,
            'url'              => route('submissions.show', $this->submission),
            'icon'             => $this->resolveIcon(),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    private function resolveIcon(): string
    {
        return match ($this->newStatus) {
            'accepted'          => 'check',
            'under_review',
            'revision_required' => 'pencil',
            'rejected'          => 'x-circle',
            default             => 'clock',
        };
    }
}

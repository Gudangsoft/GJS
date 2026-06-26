<?php

namespace App\Notifications;

use App\Models\ReviewAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected ReviewAssignment $assignment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $submission = $this->assignment->submission;
        $journal    = $submission?->journal;

        return [
            'assignment_id'    => $this->assignment->id,
            'submission_id'    => $submission?->id,
            'submission_title' => $submission?->title ?? '(Tanpa Judul)',
            'journal_name'     => $journal?->name ?? '',
            'deadline'         => $this->assignment->date_due?->format('d M Y'),
            'url'              => route('reviewer.review', $this->assignment),
            'icon'             => 'check',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

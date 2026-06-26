<?php

namespace App\Notifications;

use App\Models\CopyEditTask;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CopyEditAssigned extends Notification
{
    use Queueable;

    public function __construct(
        protected CopyEditTask $task
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $submission = $this->task->submission;

        return [
            'task_id'          => $this->task->id,
            'submission_id'    => $submission?->id,
            'submission_title' => $submission?->title ?? '(Tanpa Judul)',
            'notes'            => $this->task->editor_notes ?? '',
            'deadline'         => $this->task->deadline?->format('d M Y'),
            'url'              => route('dashboard'),
            'icon'             => 'pencil',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

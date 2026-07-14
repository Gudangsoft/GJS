<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionAcceptedForReview extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Submission $submission,
        public string $message = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Naskah Anda Diterima untuk Tahap Review — ' . $this->submission->journal->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.submission-accepted-for-review',
        );
    }
}

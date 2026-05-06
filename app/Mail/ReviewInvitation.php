<?php

namespace App\Mail;

use App\Models\ReviewAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ReviewAssignment $assignment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Review Naskah — ' . $this->assignment->submission->journal->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-invitation',
        );
    }
}

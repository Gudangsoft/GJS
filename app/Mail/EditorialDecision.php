<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EditorialDecision extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Submission $submission,
        public string $decision,
        public string $message = '',
    ) {}

    public function envelope(): Envelope
    {
        $label = match($this->decision) {
            'accepted'          => 'Naskah Anda Diterima',
            'revision_required' => 'Revisi Diperlukan untuk Naskah Anda',
            'declined'          => 'Naskah Anda Tidak Dapat Diterima',
            default             => 'Keputusan Editorial untuk Naskah Anda',
        };

        return new Envelope(
            subject: $label . ' — ' . $this->submission->journal->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.editorial-decision',
        );
    }
}

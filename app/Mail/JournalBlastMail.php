<?php

namespace App\Mail;

use App\Models\Journal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JournalBlastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Journal $journal,
        public string  $blastSubject,
        public string  $blastMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{$this->journal->name_abbrev}] {$this->blastSubject}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.journal-blast');
    }
}
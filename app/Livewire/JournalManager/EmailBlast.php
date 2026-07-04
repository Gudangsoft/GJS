<?php

namespace App\Livewire\JournalManager;

use App\Mail\JournalBlastMail;
use App\Models\Journal;
use App\Models\MessageBlast;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class EmailBlast extends Component
{
    public string $subject          = '';
    public string $message          = '';
    public string $recipients_type  = 'all_journal_users';
    public string $custom_emails    = '';
    public bool   $showHistory      = false;

    public string $previewBody      = '';

    protected function rules(): array
    {
        return [
            'subject'         => 'required|string|max:255',
            'message'         => 'required|string',
            'recipients_type' => 'required|string',
            'custom_emails'   => 'nullable|string',
        ];
    }

    protected function getJournal(): ?Journal
    {
        $journals = Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->get();
        $activeId = session('manager_active_journal');
        return $journals->firstWhere('id', $activeId) ?? $journals->first();
    }

    protected function resolveRecipients(Journal $journal): array
    {
        return match($this->recipients_type) {
            'all_journal_users' => User::whereHas('submissions', fn($q) => $q->where('journal_id', $journal->id))
                ->orWhereHas('managedJournals', fn($q) => $q->where('journals.id', $journal->id))
                ->pluck('email')->filter()->unique()->values()->toArray(),

            'authors' => User::whereHas('submissions', fn($q) => $q->where('journal_id', $journal->id))
                ->pluck('email')->filter()->unique()->values()->toArray(),

            'reviewers' => User::whereHas('reviewAssignments', fn($q) =>
                    $q->whereHas('submission', fn($sq) => $sq->where('journal_id', $journal->id)))
                ->pluck('email')->filter()->unique()->values()->toArray(),

            'editors' => User::whereHas('editedJournals', fn($q) => $q->where('journals.id', $journal->id))
                ->pluck('email')->filter()->unique()->values()->toArray(),

            'custom' => array_filter(array_map('trim', explode("\n", $this->custom_emails))),

            default => [],
        };
    }

    public function send(): void
    {
        // Rate limit: maks 3 blast per 24 jam per user
        $rateLimitKey = 'email-blast:' . auth()->id();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($rateLimitKey);
            $this->addError('rate_limit', 'Batas email blast tercapai (3x/24 jam). Coba lagi dalam ' . ceil($seconds / 3600) . ' jam.');
            return;
        }
        \Illuminate\Support\Facades\RateLimiter::hit($rateLimitKey, 86400);

        $this->validate();
        $journal = $this->getJournal();
        if (!$journal) return;

        $recipients = $this->resolveRecipients($journal);
        if (empty($recipients)) {
            $this->dispatch('toast', message: 'Tidak ada penerima ditemukan.', type: 'error');
            return;
        }

        // Queue email — tidak memblokir HTTP request
        $sent = 0; $failed = 0;
        foreach ($recipients as $email) {
            try {
                Mail::to($email)->queue(new JournalBlastMail($journal, $this->subject, $this->message));
                $sent++;
            } catch (\Throwable) {
                $failed++;
            }
        }

        MessageBlast::create([
            'journal_id'      => $journal->id,
            'sent_by'         => auth()->id(),
            'type'            => 'email',
            'subject'         => $this->subject,
            'message'         => $this->message,
            'recipients_type' => $this->recipients_type,
            'recipients'      => $recipients,
            'sent_count'      => $sent,
            'failed_count'    => $failed,
            'status'          => $failed === count($recipients) ? 'failed' : 'sent',
            'sent_at'         => now(),
        ]);

        $this->reset(['subject','message','custom_emails']);
        $this->recipients_type = 'all_journal_users';
        $type = $failed === count($recipients) ? 'error' : ($failed > 0 ? 'warning' : 'success');
        $this->dispatch('toast', message: "Email blast terkirim ke {$sent} penerima" . ($failed ? ", {$failed} gagal" : '.'), type: $type);
    }

    public function render()
    {
        $journal = $this->getJournal();
        $history = MessageBlast::where('journal_id', $journal?->id ?? 0)
            ->where('type', 'email')
            ->with('sentBy')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('livewire.journal-manager.email-blast', compact('journal','history'))
            ->title('Email Blast — Panel Pengelola');
    }
}
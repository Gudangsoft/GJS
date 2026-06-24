<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Models\MessageBlast;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class WaBlast extends Component
{
    public string $message          = '';
    public string $recipients_type  = 'all_journal_users';
    public string $custom_numbers   = '';

    protected function rules(): array
    {
        return [
            'message'         => 'required|string',
            'recipients_type' => 'required|string',
            'custom_numbers'  => 'nullable|string',
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

    protected function resolveNumbers(Journal $journal): array
    {
        $getNumbers = fn($query) => $query->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->pluck('phone')->map(fn($p) => preg_replace('/\D/', '', $p))
            ->filter(fn($p) => strlen($p) >= 9)
            ->unique()->values()->toArray();

        return match($this->recipients_type) {
            'all_journal_users' => $getNumbers(
                User::whereHas('submissions', fn($q) => $q->where('journal_id', $journal->id))
                    ->orWhereHas('managedJournals', fn($q) => $q->where('journals.id', $journal->id))
            ),
            'authors' => $getNumbers(
                User::whereHas('submissions', fn($q) => $q->where('journal_id', $journal->id))
            ),
            'reviewers' => $getNumbers(
                User::whereHas('reviewAssignments', fn($q) =>
                    $q->whereHas('submission', fn($sq) => $sq->where('journal_id', $journal->id)))
            ),
            'editors' => $getNumbers(
                User::whereHas('editedJournals', fn($q) => $q->where('journals.id', $journal->id))
            ),
            'custom' => array_filter(array_map(
                fn($n) => preg_replace('/\D/', '', trim($n)),
                explode("\n", $this->custom_numbers)
            ), fn($n) => strlen($n) >= 9),
            default => [],
        };
    }

    public function send(): void
    {
        $this->validate();
        $journal = $this->getJournal();
        if (!$journal) return;

        if (!$journal->wa_api_token) {
            $this->dispatch('toast', message: 'Token WA API belum dikonfigurasi di Pengaturan Jurnal.', type: 'error');
            return;
        }

        $numbers = $this->resolveNumbers($journal);
        if (empty($numbers)) {
            $this->dispatch('toast', message: 'Tidak ada nomor WA ditemukan. Pastikan pengguna sudah mengisi nomor telepon.', type: 'warning');
            return;
        }

        $sent = 0; $failed = 0;
        foreach ($numbers as $number) {
            try {
                $resp = Http::withHeaders(['Authorization' => $journal->wa_api_token])
                    ->asForm()
                    ->post('https://api.fonnte.com/send', [
                        'target'  => $number,
                        'message' => $this->message,
                    ]);
                $resp->json('status') ? $sent++ : $failed++;
            } catch (\Throwable) {
                $failed++;
            }
        }

        MessageBlast::create([
            'journal_id'      => $journal->id,
            'sent_by'         => auth()->id(),
            'type'            => 'wa',
            'subject'         => null,
            'message'         => $this->message,
            'recipients_type' => $this->recipients_type,
            'recipients'      => $numbers,
            'sent_count'      => $sent,
            'failed_count'    => $failed,
            'status'          => $failed === count($numbers) ? 'failed' : 'sent',
            'sent_at'         => now(),
        ]);

        $this->reset(['message','custom_numbers']);
        $this->recipients_type = 'all_journal_users';
        $type = $failed === count($numbers) ? 'error' : ($failed > 0 ? 'warning' : 'success');
        $this->dispatch('toast', message: "WA blast terkirim ke {$sent} nomor" . ($failed ? ", {$failed} gagal" : '.'), type: $type);
    }

    public function render()
    {
        $journal = $this->getJournal();
        $history = MessageBlast::where('journal_id', $journal?->id ?? 0)
            ->where('type', 'wa')
            ->with('sentBy')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('livewire.journal-manager.wa-blast', compact('journal','history'))
            ->title('WA Blast — Panel Pengelola');
    }
}
<?php

namespace App\Livewire\Reviewer;

use App\Models\ReviewAssignment;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public string $tab = 'pending';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function acceptInvitation(int $assignmentId): void
    {
        $assignment = ReviewAssignment::where('id', $assignmentId)
            ->where('reviewer_id', auth()->id())
            ->firstOrFail();

        $assignment->update([
            'status'         => 'accepted',
            'date_confirmed' => now(),
        ]);

        $assignment->submission->update(['status' => 'review']);

        session()->flash('success', 'Undangan review diterima. Silakan mulai review.');
    }

    public function declineInvitation(int $assignmentId): void
    {
        $assignment = ReviewAssignment::where('id', $assignmentId)
            ->where('reviewer_id', auth()->id())
            ->firstOrFail();

        $assignment->update([
            'status'         => 'declined',
            'date_cancelled' => now(),
        ]);

        session()->flash('success', 'Undangan review ditolak.');
    }

    public function render()
    {
        $base = fn () => ReviewAssignment::where('reviewer_id', auth()->id())
            ->with(['submission.journal', 'submission.section', 'reviewRound', 'review']);

        $assignments = match ($this->tab) {
            'pending'   => $base()->where('status', 'awaiting_response')->latest()->get(),
            'active'    => $base()->where('status', 'accepted')->latest()->get(),
            'completed' => $base()->where('status', 'completed')->latest()->get(),
            'declined'  => $base()->whereIn('status', ['declined', 'cancelled'])->latest()->get(),
            default     => $base()->where('status', 'awaiting_response')->latest()->get(),
        };

        $rawCounts = ReviewAssignment::where('reviewer_id', auth()->id())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'pending'   => (int) ($rawCounts['awaiting_response'] ?? 0),
            'active'    => (int) ($rawCounts['accepted'] ?? 0),
            'completed' => (int) ($rawCounts['completed'] ?? 0),
        ];

        return view('livewire.reviewer.dashboard', compact('assignments', 'counts'))
            ->title('Dashboard Reviewer — GJS');
    }
}

<?php

namespace App\Livewire\Reviewer;

use App\Models\ReviewAssignment;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.reviewer')]
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
        $userId = auth()->id();

        $base = fn () => ReviewAssignment::where('reviewer_id', $userId)
            ->with(['submission.journal', 'submission.section', 'reviewRound', 'review']);

        $assignments = match ($this->tab) {
            'pending'   => $base()->where('status', 'awaiting_response')->latest('date_assigned')->get(),
            'active'    => $base()->where('status', 'accepted')->orderBy('date_due')->get(),
            'completed' => $base()->where('status', 'completed')->latest('date_completed')->get(),
            'declined'  => $base()->whereIn('status', ['declined', 'cancelled'])->latest()->get(),
            default     => $base()->where('status', 'awaiting_response')->latest('date_assigned')->get(),
        };

        $rawCounts = ReviewAssignment::where('reviewer_id', $userId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'pending'   => (int) ($rawCounts['awaiting_response'] ?? 0),
            'active'    => (int) ($rawCounts['accepted'] ?? 0),
            'completed' => (int) ($rawCounts['completed'] ?? 0),
            'declined'  => (int) (($rawCounts['declined'] ?? 0) + ($rawCounts['cancelled'] ?? 0)),
        ];

        // Performance stats
        $completed = ReviewAssignment::where('reviewer_id', $userId)
            ->where('status', 'completed')
            ->whereNotNull('date_completed')
            ->whereNotNull('date_assigned')
            ->get();

        $avgDays = $completed->isNotEmpty()
            ? round($completed->avg(fn ($a) => $a->date_assigned->diffInDays($a->date_completed)))
            : null;

        $totalInvitations = ReviewAssignment::where('reviewer_id', $userId)
            ->whereNotNull('date_assigned')->count();

        $acceptanceRate = $totalInvitations > 0
            ? round((($counts['active'] + $counts['completed']) / $totalInvitations) * 100)
            : null;

        // Overdue assignments
        $overdueCount = ReviewAssignment::where('reviewer_id', $userId)
            ->where('status', 'accepted')
            ->whereNotNull('date_due')
            ->where('date_due', '<', now())
            ->count();

        // Upcoming deadlines (next 14 days)
        $upcomingDeadlines = ReviewAssignment::where('reviewer_id', $userId)
            ->where('status', 'accepted')
            ->whereNotNull('date_due')
            ->where('date_due', '>=', now())
            ->where('date_due', '<=', now()->addDays(14))
            ->with(['submission.journal'])
            ->orderBy('date_due')
            ->get();

        return view('livewire.reviewer.dashboard', compact(
            'assignments', 'counts', 'avgDays', 'acceptanceRate',
            'overdueCount', 'upcomingDeadlines'
        ))->title('Dashboard Reviewer — GJS');
    }
}

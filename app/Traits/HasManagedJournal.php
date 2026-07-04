<?php

namespace App\Traits;

use App\Models\Journal;
use Livewire\Attributes\Computed;

/**
 * Shared trait for JournalManager Livewire components.
 * Resolves the active journal once per request and memoizes it via Livewire computed property.
 */
trait HasManagedJournal
{
    #[Computed]
    public function journal(): ?Journal
    {
        $userId   = auth()->id();
        $activeId = session('manager_active_journal');

        // Single query: fetch only what we need in one round-trip
        $journal = Journal::where(function ($q) use ($userId) {
                $q->whereHas('managers', fn($sq) => $sq->where('users.id', $userId))
                  ->orWhereHas('editors',  fn($sq) => $sq->where('users.id', $userId));
            })
            ->when($activeId, fn($q) => $q->where('id', $activeId))
            ->first();

        // Fallback: if active session journal doesn't belong to user, get first available
        if (! $journal) {
            $journal = Journal::where(function ($q) use ($userId) {
                $q->whereHas('managers', fn($sq) => $sq->where('users.id', $userId))
                  ->orWhereHas('editors',  fn($sq) => $sq->where('users.id', $userId));
            })->first();
        }

        return $journal;
    }
}

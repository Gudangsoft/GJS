<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Models\Submission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Submissions extends Component
{
    public string $tab = 'pending';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    protected function getJournal(): ?Journal
    {
        return Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->first();
    }

    public function render()
    {
        $journal = $this->getJournal();
        $submissions = collect();
        $counts = [];

        if ($journal) {
            $base = fn() => Submission::with(['submitter', 'section'])
                ->where('journal_id', $journal->id)
                ->whereNotIn('status', ['draft']);

            $submissions = match ($this->tab) {
                'pending'  => $base()->whereIn('status', ['submitted', 'queued'])->latest('submitted_at')->get(),
                'review'   => $base()->whereIn('status', ['assigned', 'review'])->latest('submitted_at')->get(),
                'revision' => $base()->whereIn('status', ['revision_required', 'resubmit'])->latest('submitted_at')->get(),
                'decided'  => $base()->whereIn('status', ['accepted', 'declined', 'copyediting', 'production', 'scheduled', 'published'])->latest('submitted_at')->get(),
                default    => $base()->latest('submitted_at')->get(),
            };

            $raw = Submission::where('journal_id', $journal->id)
                ->whereNotIn('status', ['draft'])
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $counts = [
                'pending'  => $raw->only(['submitted', 'queued'])->sum(),
                'review'   => $raw->only(['assigned', 'review'])->sum(),
                'revision' => $raw->only(['revision_required', 'resubmit'])->sum(),
                'decided'  => $raw->only(['accepted', 'declined', 'copyediting', 'production', 'scheduled', 'published'])->sum(),
                'all'      => $raw->sum(),
            ];
        }

        return view('livewire.journal-manager.submissions', compact('journal', 'submissions', 'counts'))
            ->title('Submission — Panel Pengelola');
    }
}

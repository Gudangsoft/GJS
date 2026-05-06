<?php

namespace App\Livewire\Editor;

use App\Models\Submission;
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

    public function render()
    {
        $base = fn () => Submission::with(['submitter', 'journal', 'section'])
            ->whereNotIn('status', ['draft']);

        $submissions = match ($this->tab) {
            'pending'  => $base()->whereIn('status', ['submitted', 'queued'])->latest('submitted_at')->get(),
            'review'   => $base()->whereIn('status', ['assigned', 'review'])->latest('submitted_at')->get(),
            'revision' => $base()->whereIn('status', ['revision_required', 'resubmit'])->latest('submitted_at')->get(),
            'decided'  => $base()->whereIn('status', ['accepted', 'declined'])->latest('submitted_at')->get(),
            default    => $base()->whereIn('status', ['submitted', 'queued'])->latest('submitted_at')->get(),
        };

        $rawCounts = Submission::whereNotIn('status', ['draft'])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'pending'  => $rawCounts->only(['submitted', 'queued'])->sum(),
            'review'   => $rawCounts->only(['assigned', 'review'])->sum(),
            'revision' => $rawCounts->only(['revision_required', 'resubmit'])->sum(),
            'decided'  => $rawCounts->only(['accepted', 'declined'])->sum(),
        ];

        return view('livewire.editor.dashboard', compact('submissions', 'counts'))
            ->title('Dashboard Editor — GJS');
    }
}

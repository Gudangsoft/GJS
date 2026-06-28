<?php

namespace App\Livewire\Reviewer;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\ReviewAssignment;

#[Layout('layouts.reviewer')]
class Dokumen extends Component
{
    public function render()
    {
        $userId = auth()->id();

        // Surat Tugas: accepted + completed assignments
        $suratTugas = ReviewAssignment::where('reviewer_id', $userId)
            ->whereIn('status', ['accepted', 'completed'])
            ->with(['submission.journal'])
            ->orderByDesc('date_assigned')
            ->get();

        // Sertifikat: only completed assignments
        $sertifikat = ReviewAssignment::where('reviewer_id', $userId)
            ->where('status', 'completed')
            ->with(['submission.journal'])
            ->orderByDesc('date_completed')
            ->get();

        return view('livewire.reviewer.dokumen', compact('suratTugas', 'sertifikat'))
            ->title('Dokumen Saya — Panel Reviewer');
    }
}

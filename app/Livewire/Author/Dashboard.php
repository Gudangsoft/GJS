<?php

namespace App\Livewire\Author;

use App\Models\LetterOfAcceptance;
use App\Models\Submission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.author')]
#[Title('Dashboard Penulis')]
class Dashboard extends Component
{
    public string $activeTab = 'active';

    public function render()
    {
        $userId = auth()->id();

        $active = Submission::where('user_id', $userId)
            ->whereNotIn('status', ['published', 'declined', 'archived', 'draft'])
            ->with(['journal', 'section'])
            ->orderByDesc('submitted_at')
            ->get();

        $published = Submission::where('user_id', $userId)
            ->where('status', 'published')
            ->with(['journal', 'article.issue'])
            ->orderByDesc('submitted_at')
            ->get();

        $drafts = Submission::where('user_id', $userId)
            ->where('status', 'draft')
            ->with(['journal', 'section'])
            ->orderByDesc('updated_at')
            ->get();

        $declined = Submission::where('user_id', $userId)
            ->whereIn('status', ['declined', 'archived'])
            ->with(['journal'])
            ->orderByDesc('submitted_at')
            ->get();

        $loas = LetterOfAcceptance::whereHas('submission', fn($q) => $q->where('user_id', $userId))
            ->with(['submission', 'journal', 'issuedBy'])
            ->orderByDesc('created_at')
            ->get();

        $turnitin = Submission::where('user_id', $userId)
            ->whereNotNull('similarity_score')
            ->with(['journal'])
            ->orderByDesc('similarity_checked_at')
            ->get(['id', 'title', 'journal_id', 'similarity_score', 'similarity_checked_at', 'status']);

        return view('livewire.author.dashboard', compact('active', 'published', 'drafts', 'declined', 'loas', 'turnitin'));
    }
}

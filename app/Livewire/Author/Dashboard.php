<?php

namespace App\Livewire\Author;

use App\Models\Submission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
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

        return view('livewire.author.dashboard', compact('active', 'published', 'drafts', 'declined'));
    }
}

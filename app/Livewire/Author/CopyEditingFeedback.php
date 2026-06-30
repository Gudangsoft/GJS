<?php

namespace App\Livewire\Author;

use App\Models\CopyEditTask;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.author')]
class CopyEditingFeedback extends Component
{
    public function submitAuthorNotes(int $taskId, string $notes): void
    {
        $task = CopyEditTask::whereHas('submission', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($taskId);

        $task->update([
            'author_notes' => $notes,
            'status'       => 'in_progress',
        ]);

        session()->flash('success', 'Catatan Anda berhasil dikirim ke copyeditor.');
    }

    public function render()
    {
        $tasks = CopyEditTask::with(['submission.journal', 'assignee'])
            ->whereHas('submission', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.author.copy-editing-feedback', compact('tasks'))
            ->title('Copy Editing — Area Penulis');
    }
}

<?php

namespace App\Livewire\JournalManager;

use App\Models\CopyEditTask;
use App\Models\Journal;
use App\Models\Submission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class CopyEditing extends Component
{
    public string $tab    = 'pending';
    public string $search = '';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->search = '';
    }

    protected function activeJournal(): ?Journal
    {
        $journals = Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->get();
        $activeId = session('manager_active_journal');
        return $journals->firstWhere('id', $activeId) ?? $journals->first();
    }

    public function getTasks(Journal $journal)
    {
        $query = CopyEditTask::with(['submission', 'assignee', 'assignedBy'])
            ->where('journal_id', $journal->id)
            ->where('status', $this->tab);

        if (trim($this->search) !== '') {
            $query->whereHas('submission', function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function assignTask(int $taskId, int $userId): void
    {
        $task = CopyEditTask::findOrFail($taskId);
        $task->update([
            'assignee_id' => $userId,
            'status'      => 'assigned',
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
        ]);

        session()->flash('success', 'Copyeditor berhasil ditugaskan.');
    }

    public function updateStatus(int $taskId, string $status): void
    {
        $allowed = ['pending', 'assigned', 'in_progress', 'awaiting_author', 'completed'];
        if (! in_array($status, $allowed, true)) {
            return;
        }

        $task = CopyEditTask::findOrFail($taskId);

        $data = ['status' => $status];
        if ($status === 'completed') {
            $data['completed_at'] = now();
        }

        $task->update($data);
        session()->flash('success', 'Status task berhasil diperbarui.');
    }

    public function createTaskForSubmission(int $submissionId): void
    {
        $journal = $this->activeJournal();
        if (! $journal) {
            return;
        }

        $submission = Submission::where('id', $submissionId)
            ->where('journal_id', $journal->id)
            ->where('status', 'accepted')
            ->whereDoesntHave('copyEditTask')
            ->firstOrFail();

        CopyEditTask::create([
            'submission_id' => $submission->id,
            'journal_id'    => $journal->id,
            'assigned_by'   => auth()->id(),
            'round'         => 1,
            'status'        => 'pending',
        ]);

        // Advance submission status to copyediting
        $submission->update(['status' => 'copyediting']);

        session()->flash('success', 'Task copy editing berhasil dibuat.');
    }

    public function render()
    {
        $journal  = $this->activeJournal();
        $tasks    = collect();
        $editors  = collect();
        $submissions_ready = collect();

        if ($journal) {
            $tasks = $this->getTasks($journal);

            $editors = $journal->allMembers()
                ->wherePivotIn('role', ['editor', 'manager'])
                ->get();

            $submissions_ready = Submission::where('journal_id', $journal->id)
                ->where('status', 'accepted')
                ->whereDoesntHave('copyEditTask')
                ->with('section')
                ->orderByDesc('submitted_at')
                ->get();
        }

        return view('livewire.journal-manager.copy-editing', compact(
            'journal',
            'tasks',
            'editors',
            'submissions_ready',
        ))->title('Copy Editing — Panel Pengelola');
    }
}

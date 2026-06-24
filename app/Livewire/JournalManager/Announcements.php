<?php

namespace App\Livewire\JournalManager;

use App\Models\Announcement;
use App\Models\Journal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Announcements extends Component
{
    public bool   $showForm        = false;
    public ?int   $editingId       = null;
    public string $title           = '';
    public string $description_short = '';
    public string $description     = '';
    public string $date_expire     = '';

    protected function rules(): array
    {
        return [
            'title'             => 'required|string|max:255',
            'description_short' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'date_expire'       => 'nullable|date',
        ];
    }

    protected function getJournal(): ?Journal
    {
        $journals = Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->get();
        $activeId = session('manager_active_journal');
        return $journals->firstWhere('id', $activeId) ?? $journals->first();
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'title', 'description_short', 'description', 'date_expire']);
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $ann = Announcement::findOrFail($id);
        $this->editingId         = $ann->id;
        $this->title             = $ann->title;
        $this->description_short = $ann->description_short ?? '';
        $this->description       = $ann->description ?? '';
        $this->date_expire       = $ann->date_expire ? $ann->date_expire->format('Y-m-d') : '';
        $this->showForm          = true;
    }

    public function save(): void
    {
        $this->validate();
        $journal = $this->getJournal();
        if (!$journal) return;

        $data = [
            'journal_id'        => $journal->id,
            'user_id'           => auth()->id(),
            'title'             => $this->title,
            'description_short' => $this->description_short ?: null,
            'description'       => $this->description ?: null,
            'date_expire'       => $this->date_expire ?: null,
            'date_posted'       => now(),
        ];

        if ($this->editingId) {
            Announcement::findOrFail($this->editingId)->update($data);
            $msg = 'Pengumuman berhasil diperbarui.';
        } else {
            Announcement::create($data);
            $msg = 'Pengumuman berhasil dibuat.';
        }

        $this->cancelForm();
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function delete(int $id): void
    {
        Announcement::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Pengumuman dihapus.', type: 'success');
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->reset(['editingId', 'title', 'description_short', 'description', 'date_expire']);
    }

    public function render()
    {
        $journal       = $this->getJournal();
        $announcements = $journal
            ? Announcement::where('journal_id', $journal->id)->orderByDesc('date_posted')->get()
            : collect();

        return view('livewire.journal-manager.announcements', compact('journal', 'announcements'))
            ->title('Pengumuman — Panel Pengelola');
    }
}

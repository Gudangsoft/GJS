<?php

namespace App\Livewire\JournalManager;

use App\Models\Issue;
use App\Models\Journal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Issues extends Component
{
    public bool   $showForm    = false;
    public ?int   $editingId   = null;
    public string $volume      = '';
    public string $number      = '';
    public string $year        = '';
    public string $title       = '';
    public string $date_published = '';
    public bool   $published   = false;
    public bool   $current     = false;

    protected function rules(): array
    {
        return [
            'volume'         => 'nullable|string|max:50',
            'number'         => 'nullable|string|max:50',
            'year'           => 'required|digits:4',
            'title'          => 'nullable|string|max:255',
            'date_published' => 'nullable|date',
            'published'      => 'boolean',
            'current'        => 'boolean',
        ];
    }

    protected function getJournal(): ?Journal
    {
        return Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->first();
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'volume', 'number', 'year', 'title', 'date_published', 'published', 'current']);
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $issue = Issue::findOrFail($id);
        $this->editingId      = $issue->id;
        $this->volume         = $issue->volume ?? '';
        $this->number         = $issue->number ?? '';
        $this->year           = (string)($issue->year ?? '');
        $this->title          = $issue->title ?? '';
        $this->date_published = $issue->date_published ? $issue->date_published->format('Y-m-d') : '';
        $this->published      = (bool)$issue->published;
        $this->current        = (bool)$issue->current;
        $this->showForm       = true;
    }

    public function save(): void
    {
        $this->validate();
        $journal = $this->getJournal();
        if (!$journal) return;

        $data = [
            'journal_id'     => $journal->id,
            'volume'         => $this->volume ?: null,
            'number'         => $this->number ?: null,
            'year'           => $this->year,
            'title'          => $this->title ?: null,
            'date_published' => $this->date_published ?: null,
            'published'      => $this->published,
            'current'        => $this->current,
        ];

        if ($this->editingId) {
            Issue::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Terbitan berhasil diperbarui.');
        } else {
            Issue::create($data);
            session()->flash('success', 'Terbitan berhasil dibuat.');
        }

        $this->showForm = false;
        $this->reset(['editingId', 'volume', 'number', 'year', 'title', 'date_published', 'published', 'current']);
    }

    public function togglePublish(int $id): void
    {
        $issue = Issue::findOrFail($id);
        $issue->update(['published' => !$issue->published]);
    }

    public function delete(int $id): void
    {
        Issue::findOrFail($id)->delete();
        session()->flash('success', 'Terbitan dihapus.');
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->reset(['editingId', 'volume', 'number', 'year', 'title', 'date_published', 'published', 'current']);
    }

    public function render()
    {
        $journal = $this->getJournal();
        $issues  = $journal
            ? Issue::where('journal_id', $journal->id)->orderByDesc('year')->orderByDesc('volume')->orderByDesc('number')->get()
            : collect();

        return view('livewire.journal-manager.issues', compact('journal', 'issues'))
            ->title('Terbitan — Panel Pengelola');
    }
}

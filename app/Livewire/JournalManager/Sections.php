<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Models\Section;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Sections extends Component
{
    public bool   $showForm  = false;
    public ?int   $editingId = null;
    public string $title     = '';
    public string $abbrev    = '';
    public string $policy    = '';
    public int    $sequence  = 0;
    public bool   $is_inactive = false;

    protected function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'abbrev'      => 'nullable|string|max:50',
            'policy'      => 'nullable|string',
            'sequence'    => 'integer|min:0',
            'is_inactive' => 'boolean',
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
        $this->reset(['editingId', 'title', 'abbrev', 'policy', 'sequence', 'is_inactive']);
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $section = Section::findOrFail($id);
        $this->editingId   = $section->id;
        $this->title       = $section->title;
        $this->abbrev      = $section->abbrev ?? '';
        $this->policy      = $section->policy ?? '';
        $this->sequence    = (int)$section->sequence;
        $this->is_inactive = (bool)$section->is_inactive;
        $this->showForm    = true;
    }

    public function save(): void
    {
        $this->validate();
        $journal = $this->getJournal();
        if (!$journal) return;

        $data = [
            'journal_id'  => $journal->id,
            'title'       => $this->title,
            'abbrev'      => $this->abbrev ?: null,
            'policy'      => $this->policy ?: null,
            'sequence'    => $this->sequence,
            'is_inactive' => $this->is_inactive,
        ];

        if ($this->editingId) {
            Section::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Seksi berhasil diperbarui.');
        } else {
            Section::create($data);
            session()->flash('success', 'Seksi berhasil dibuat.');
        }

        $this->cancelForm();
    }

    public function delete(int $id): void
    {
        Section::findOrFail($id)->delete();
        session()->flash('success', 'Seksi dihapus.');
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->reset(['editingId', 'title', 'abbrev', 'policy', 'sequence', 'is_inactive']);
    }

    public function render()
    {
        $journal  = $this->getJournal();
        $sections = $journal
            ? Section::where('journal_id', $journal->id)->orderBy('sequence')->get()
            : collect();

        return view('livewire.journal-manager.sections', compact('journal', 'sections'))
            ->title('Seksi / Rubrik — Panel Pengelola');
    }
}

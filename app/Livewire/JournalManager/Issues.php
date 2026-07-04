<?php

namespace App\Livewire\JournalManager;

use App\Models\Issue;
use App\Models\Journal;
use App\Services\FileScannerService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.manager')]
class Issues extends Component
{
    use WithFileUploads;

    public bool   $showForm      = false;
    public ?int   $editingId     = null;
    public string $volume        = '';
    public string $number        = '';
    public string $year          = '';
    public string $title         = '';
    public string $date_published = '';
    public bool   $published     = false;
    public bool   $current       = false;
    public $newCoverImage        = null;
    public ?string $existingCover = null;

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
            'newCoverImage'  => 'nullable|image|max:2048',
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
        $this->reset(['editingId', 'volume', 'number', 'year', 'title', 'date_published', 'published', 'current', 'newCoverImage', 'existingCover']);
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
        $this->existingCover  = $issue->cover_image;
        $this->newCoverImage  = null;
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

        if ($this->newCoverImage) {
            $scan = app(FileScannerService::class)->scan($this->newCoverImage);
            if (! $scan['ok']) { $this->addError('newCoverImage', $scan['reason']); return; }
            $data['cover_image'] = $this->newCoverImage->store('issues/covers', 'public');
        }

        if ($this->editingId) {
            Issue::findOrFail($this->editingId)->update($data);
            $msg = 'Terbitan berhasil diperbarui.';
        } else {
            Issue::create($data);
            $msg = 'Terbitan berhasil dibuat.';
        }

        $this->showForm = false;
        $this->reset(['editingId', 'volume', 'number', 'year', 'title', 'date_published', 'published', 'current', 'newCoverImage', 'existingCover']);
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function removeCover(int $id): void
    {
        $issue = Issue::findOrFail($id);
        if ($issue->cover_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($issue->cover_image);
            $issue->update(['cover_image' => null]);
        }
        $this->existingCover = null;
    }

    public function togglePublish(int $id): void
    {
        $issue = Issue::findOrFail($id);
        $issue->update(['published' => !$issue->published]);
    }

    public function delete(int $id): void
    {
        Issue::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Terbitan dihapus.', type: 'success');
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->reset(['editingId', 'volume', 'number', 'year', 'title', 'date_published', 'published', 'current', 'newCoverImage', 'existingCover']);
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
<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Pages extends Component
{
    public array  $pages      = [];
    public bool   $editing    = false;
    public int    $editIndex  = -1;
    public string $editTitle   = '';
    public string $editSlug    = '';
    public string $editContent = '';
    public bool   $editEnabled = true;

    protected function getJournal(): ?Journal
    {
        $journals = Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->get();
        $activeId = session('manager_active_journal');
        return $journals->firstWhere('id', $activeId) ?? $journals->first();
    }

    public function mount(): void
    {
        $journal = $this->getJournal();
        if ($journal) {
            $this->pages = $journal->settings['custom_pages'] ?? [];
        }
    }

    public function newPage(): void
    {
        $this->editing    = true;
        $this->editIndex  = -1;
        $this->editTitle  = '';
        $this->editSlug   = '';
        $this->editContent = '';
        $this->editEnabled = true;
        $this->resetValidation();
    }

    public function editPage(int $i): void
    {
        $this->editing     = true;
        $this->editIndex   = $i;
        $this->editTitle   = $this->pages[$i]['title']   ?? '';
        $this->editSlug    = $this->pages[$i]['slug']    ?? '';
        $this->editContent = $this->pages[$i]['content'] ?? '';
        $this->editEnabled = (bool) ($this->pages[$i]['enabled'] ?? true);
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->editing   = false;
        $this->editIndex = -1;
        $this->resetValidation();
    }

    public function updatedEditTitle(string $value): void
    {
        if ($this->editIndex === -1) {
            $this->editSlug = Str::slug($value);
        }
    }

    public function savePage(): void
    {
        $this->validate([
            'editTitle'   => 'required|string|max:150',
            'editSlug'    => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/'],
            'editContent' => 'required|string',
        ], [
            'editSlug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
        ]);

        // Check slug uniqueness (except current editing page)
        foreach ($this->pages as $idx => $p) {
            if ($idx !== $this->editIndex && $p['slug'] === $this->editSlug) {
                $this->addError('editSlug', 'Slug sudah digunakan oleh halaman lain.');
                return;
            }
        }

        // Also block reserved slugs
        $reserved = ['about','editorial-team','guidelines','reviewer-guidelines','ethics','privacy','contact','submissions'];
        if (in_array($this->editSlug, $reserved)) {
            $this->addError('editSlug', 'Slug tersebut sudah dipakai oleh halaman bawaan jurnal.');
            return;
        }

        $page = [
            'title'   => $this->editTitle,
            'slug'    => $this->editSlug,
            'content' => $this->editContent,
            'enabled' => $this->editEnabled,
        ];

        if ($this->editIndex === -1) {
            $this->pages[] = $page;
        } else {
            $this->pages[$this->editIndex] = $page;
        }

        $this->pages = array_values($this->pages);
        $this->persistPages();
        $this->editing   = false;
        $this->editIndex = -1;
        $this->dispatch('toast', message: 'Halaman berhasil disimpan.', type: 'success');
    }

    public function deletePage(int $i): void
    {
        array_splice($this->pages, $i, 1);
        $this->pages = array_values($this->pages);
        $this->persistPages();
        $this->dispatch('toast', message: 'Halaman dihapus.', type: 'success');
    }

    public function toggleEnabled(int $i): void
    {
        $this->pages[$i]['enabled'] = ! ($this->pages[$i]['enabled'] ?? true);
        $this->persistPages();
    }

    protected function persistPages(): void
    {
        $journal = $this->getJournal();
        if (! $journal) return;
        $s = $journal->settings ?? [];
        $s['custom_pages'] = $this->pages;
        $journal->update(['settings' => $s]);
    }

    public function render()
    {
        return view('livewire.journal-manager.pages')
            ->title('Halaman Jurnal — Panel Pengelola');
    }
}

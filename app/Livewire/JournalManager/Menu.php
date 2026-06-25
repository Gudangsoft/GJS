<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Menu extends Component
{
    // Preset toggles
    public bool $menu_show_issues        = true;
    public bool $menu_show_announcements = true;
    public bool $menu_show_about         = true;
    public bool $menu_show_browse        = true;

    // Custom menu items
    public array  $items       = [];
    public bool   $editing     = false;
    public int    $editIndex   = -1;
    public string $editLabel   = '';
    public string $editUrl     = '';
    public string $editTarget  = '_self';

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
        if (! $journal) return;
        $s = $journal->settings ?? [];

        $this->menu_show_issues        = (bool) ($s['menu_show_issues']        ?? true);
        $this->menu_show_announcements = (bool) ($s['menu_show_announcements'] ?? true);
        $this->menu_show_about         = (bool) ($s['menu_show_about']         ?? true);
        $this->menu_show_browse        = (bool) ($s['menu_show_browse']        ?? true);
        $this->items                   = $s['custom_menu_items']                ?? [];
    }

    public function savePresets(): void
    {
        $this->persist();
        $this->dispatch('toast', message: 'Pengaturan menu tersimpan.', type: 'success');
    }

    public function newItem(): void
    {
        $this->editing    = true;
        $this->editIndex  = -1;
        $this->editLabel  = '';
        $this->editUrl    = '';
        $this->editTarget = '_self';
        $this->resetValidation();
    }

    public function editItem(int $i): void
    {
        $this->editing    = true;
        $this->editIndex  = $i;
        $this->editLabel  = $this->items[$i]['label']  ?? '';
        $this->editUrl    = $this->items[$i]['url']    ?? '';
        $this->editTarget = $this->items[$i]['target'] ?? '_self';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->editing   = false;
        $this->editIndex = -1;
        $this->resetValidation();
    }

    public function saveItem(): void
    {
        $this->validate([
            'editLabel'  => 'required|string|max:60',
            'editUrl'    => 'required|string|max:500',
            'editTarget' => 'required|in:_self,_blank',
        ]);

        $item = [
            'label'  => $this->editLabel,
            'url'    => $this->editUrl,
            'target' => $this->editTarget,
        ];

        if ($this->editIndex === -1) {
            $this->items[] = $item;
        } else {
            $this->items[$this->editIndex] = $item;
        }

        $this->items     = array_values($this->items);
        $this->editing   = false;
        $this->editIndex = -1;
        $this->persist();
        $this->dispatch('toast', message: 'Item menu disimpan.', type: 'success');
    }

    public function deleteItem(int $i): void
    {
        array_splice($this->items, $i, 1);
        $this->items = array_values($this->items);
        $this->persist();
        $this->dispatch('toast', message: 'Item menu dihapus.', type: 'success');
    }

    public function moveUp(int $i): void
    {
        if ($i === 0) return;
        [$this->items[$i - 1], $this->items[$i]] = [$this->items[$i], $this->items[$i - 1]];
        $this->persist();
    }

    public function moveDown(int $i): void
    {
        if ($i >= count($this->items) - 1) return;
        [$this->items[$i], $this->items[$i + 1]] = [$this->items[$i + 1], $this->items[$i]];
        $this->persist();
    }

    protected function persist(): void
    {
        $journal = $this->getJournal();
        if (! $journal) return;
        $s = $journal->settings ?? [];
        $s['menu_show_issues']        = $this->menu_show_issues;
        $s['menu_show_announcements'] = $this->menu_show_announcements;
        $s['menu_show_about']         = $this->menu_show_about;
        $s['menu_show_browse']        = $this->menu_show_browse;
        $s['custom_menu_items']       = array_values($this->items);
        $journal->update(['settings' => $s]);
    }

    public function render()
    {
        return view('livewire.journal-manager.menu')
            ->title('Menu Navigasi — Panel Pengelola');
    }
}

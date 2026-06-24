<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Users extends Component
{
    public string $search = '';

    protected function getJournal(): ?Journal
    {
        $journals = Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->get();
        $activeId = session('manager_active_journal');
        return $journals->firstWhere('id', $activeId) ?? $journals->first();
    }

    public function render()
    {
        $journal = $this->getJournal();
        $users   = collect();

        if ($journal) {
            $users = User::whereHas('roles', fn($q) => $q->whereIn('name', ['editor', 'reviewer', 'journal_manager']))
                ->when($this->search, fn($q) => $q->where(function($sub) {
                    $sub->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                }))
                ->with('roles')
                ->orderBy('first_name')
                ->get();
        }

        return view('livewire.journal-manager.users', compact('journal', 'users'))
            ->title('Daftar Pengguna — Panel Pengelola');
    }
}

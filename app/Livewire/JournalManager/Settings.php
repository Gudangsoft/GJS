<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Settings extends Component
{
    public ?Journal $journal = null;

    // Form fields
    public string $name              = '';
    public string $name_abbrev       = '';
    public string $issn_print        = '';
    public string $issn_online       = '';
    public string $publisher         = '';
    public string $email             = '';
    public string $contact_name      = '';
    public string $url               = '';
    public string $review_mode       = 'double_blind';
    public int    $num_weeks_per_review = 4;
    public string $license_type      = 'cc_by';
    public string $sinta_level       = '';
    public string $sinta_id          = '';
    public string $focus_scope       = '';
    public string $author_guidelines = '';
    public bool   $enabled           = true;
    public bool   $disable_submissions = false;

    public function mount(): void
    {
        $this->journal = Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->first();

        if ($this->journal) {
            $this->name               = $this->journal->name ?? '';
            $this->name_abbrev        = $this->journal->name_abbrev ?? '';
            $this->issn_print         = $this->journal->issn_print ?? '';
            $this->issn_online        = $this->journal->issn_online ?? '';
            $this->publisher          = $this->journal->publisher ?? '';
            $this->email              = $this->journal->email ?? '';
            $this->contact_name       = $this->journal->contact_name ?? '';
            $this->url                = $this->journal->url ?? '';
            $this->review_mode        = $this->journal->review_mode ?? 'double_blind';
            $this->num_weeks_per_review = (int)($this->journal->num_weeks_per_review ?? 4);
            $this->license_type       = $this->journal->license_type ?? 'cc_by';
            $this->sinta_level        = $this->journal->sinta_level ?? '';
            $this->sinta_id           = $this->journal->sinta_id ?? '';
            $this->focus_scope        = $this->journal->focus_scope ?? '';
            $this->author_guidelines  = $this->journal->author_guidelines ?? '';
            $this->enabled            = (bool)$this->journal->enabled;
            $this->disable_submissions = (bool)$this->journal->disable_submissions;
        }
    }

    protected function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'name_abbrev'        => 'nullable|string|max:50',
            'issn_print'         => 'nullable|string|max:20',
            'issn_online'        => 'nullable|string|max:20',
            'publisher'          => 'nullable|string|max:255',
            'email'              => 'nullable|email|max:255',
            'contact_name'       => 'nullable|string|max:255',
            'url'                => 'nullable|url|max:255',
            'review_mode'        => 'required|string',
            'num_weeks_per_review' => 'required|integer|min:1|max:52',
            'license_type'       => 'nullable|string|max:50',
            'sinta_level'        => 'nullable|string|max:10',
            'sinta_id'           => 'nullable|string|max:50',
            'focus_scope'        => 'nullable|string',
            'author_guidelines'  => 'nullable|string',
            'enabled'            => 'boolean',
            'disable_submissions' => 'boolean',
        ];
    }

    public function save(): void
    {
        $this->validate();

        if (!$this->journal) return;

        $this->journal->update([
            'name'               => $this->name,
            'name_abbrev'        => $this->name_abbrev ?: null,
            'issn_print'         => $this->issn_print ?: null,
            'issn_online'        => $this->issn_online ?: null,
            'publisher'          => $this->publisher ?: null,
            'email'              => $this->email ?: null,
            'contact_name'       => $this->contact_name ?: null,
            'url'                => $this->url ?: null,
            'review_mode'        => $this->review_mode,
            'num_weeks_per_review' => $this->num_weeks_per_review,
            'license_type'       => $this->license_type ?: null,
            'sinta_level'        => $this->sinta_level ?: null,
            'sinta_id'           => $this->sinta_id ?: null,
            'focus_scope'        => $this->focus_scope ?: null,
            'author_guidelines'  => $this->author_guidelines ?: null,
            'enabled'            => $this->enabled,
            'disable_submissions' => $this->disable_submissions,
        ]);

        session()->flash('success', 'Pengaturan jurnal berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.journal-manager.settings')
            ->title('Pengaturan Jurnal — Panel Pengelola');
    }
}

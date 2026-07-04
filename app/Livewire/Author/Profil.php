<?php

namespace App\Livewire\Author;

use App\Services\FileScannerService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.author')]
class Profil extends Component
{
    use WithFileUploads;

    public string $first_name       = '';
    public string $last_name        = '';
    public string $salutation       = '';
    public string $email            = '';
    public string $affiliation      = '';
    public string $position         = '';
    public string $department       = '';
    public string $country          = '';
    public string $phone            = '';
    public string $orcid            = '';
    public string $google_scholar   = '';
    public string $scopus_id        = '';
    public string $researchgate     = '';
    public string $sinta_id         = '';
    public string $semantic_scholar = '';
    public string $url              = '';
    public ?int   $h_index          = null;
    public ?int   $total_citations  = null;
    public string $bio              = '';
    public string $expertiseInput   = '';
    public array  $expertise_areas  = [];
    public        $photo            = null;
    public bool   $saved            = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->first_name       = $user->first_name ?? '';
        $this->last_name        = $user->last_name ?? '';
        $this->salutation       = $user->salutation ?? '';
        $this->email            = $user->email ?? '';
        $this->affiliation      = $user->affiliation ?? '';
        $this->position         = $user->position ?? '';
        $this->department       = $user->department ?? '';
        $this->country          = $user->country ?? '';
        $this->phone            = $user->phone ?? '';
        $this->orcid            = $user->orcid ?? '';
        $this->google_scholar   = $user->google_scholar ?? '';
        $this->scopus_id        = $user->scopus_id ?? '';
        $this->researchgate     = $user->researchgate ?? '';
        $this->sinta_id         = $user->sinta_id ?? '';
        $this->semantic_scholar = $user->semantic_scholar ?? '';
        $this->url              = $user->url ?? '';
        $this->h_index          = $user->h_index;
        $this->total_citations  = $user->total_citations;
        $this->bio              = $user->bio ?? '';
        $this->expertise_areas  = $user->expertise_areas ?? [];
    }

    public function addExpertise(): void
    {
        $tag = trim($this->expertiseInput);
        if ($tag && !in_array($tag, $this->expertise_areas) && count($this->expertise_areas) < 15) {
            $this->expertise_areas[] = $tag;
        }
        $this->expertiseInput = '';
    }

    public function removeExpertise(int $index): void
    {
        array_splice($this->expertise_areas, $index, 1);
    }

    public function save(): void
    {
        $userId = auth()->id();

        $this->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'nullable|string|max:100',
            'salutation'       => 'nullable|string|max:10',
            'affiliation'      => 'nullable|string|max:255',
            'position'         => 'nullable|string|max:255',
            'department'       => 'nullable|string|max:255',
            'country'          => 'nullable|string|max:2',
            'phone'            => 'nullable|string|max:30',
            'orcid'            => 'nullable|string|max:30|unique:users,orcid,' . $userId,
            'google_scholar'   => 'nullable|string|max:500',
            'scopus_id'        => 'nullable|string|max:50',
            'researchgate'     => 'nullable|string|max:500',
            'sinta_id'         => 'nullable|string|max:50',
            'semantic_scholar' => 'nullable|string|max:500',
            'url'              => 'nullable|string|max:500',
            'h_index'          => 'nullable|integer|min:0|max:9999',
            'total_citations'  => 'nullable|integer|min:0',
            'bio'              => 'nullable|string|max:3000',
            'photo'            => 'nullable|image|max:4096',
        ]);

        $user = auth()->user();
        $avatarPath = $user->avatar;

        if ($this->photo) {
            $scan = app(FileScannerService::class)->scan($this->photo);
            if (! $scan['ok']) { $this->addError('photo', $scan['reason']); return; }
            if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }
            $avatarPath = $this->photo->store('avatars', 'public');
        }

        $user->update([
            'first_name'       => trim($this->first_name),
            'last_name'        => trim($this->last_name) ?: null,
            'salutation'       => trim($this->salutation) ?: null,
            'affiliation'      => trim($this->affiliation) ?: null,
            'position'         => trim($this->position) ?: null,
            'department'       => trim($this->department) ?: null,
            'country'          => trim($this->country) ?: null,
            'phone'            => trim($this->phone) ?: null,
            'orcid'            => trim($this->orcid) ?: null,
            'google_scholar'   => trim($this->google_scholar) ?: null,
            'scopus_id'        => trim($this->scopus_id) ?: null,
            'researchgate'     => trim($this->researchgate) ?: null,
            'sinta_id'         => trim($this->sinta_id) ?: null,
            'semantic_scholar' => trim($this->semantic_scholar) ?: null,
            'url'              => trim($this->url) ?: null,
            'h_index'          => $this->h_index !== null ? (int) $this->h_index : null,
            'total_citations'  => $this->total_citations !== null ? (int) $this->total_citations : null,
            'bio'              => trim($this->bio) ?: null,
            'expertise_areas'  => !empty($this->expertise_areas) ? array_values($this->expertise_areas) : null,
            'avatar'           => $avatarPath,
        ]);

        $this->photo = null;
        $this->saved = true;
    }

    public function render()
    {
        $userId = auth()->id();

        $totalPublished = Submission::where('user_id', $userId)->where('status', 'published')->count();
        $totalSubmitted = Submission::where('user_id', $userId)->whereNotNull('submitted_at')->count();
        $totalActive    = Submission::where('user_id', $userId)
            ->whereNotIn('status', ['published', 'declined', 'archived', 'draft'])->count();

        $currentAvatar = auth()->user()->avatar;

        return view('livewire.author.profil', compact(
            'totalPublished', 'totalSubmitted', 'totalActive', 'currentAvatar'
        ))->title('Profil Saya — Panel Penulis');
    }
}

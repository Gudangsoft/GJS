<?php

namespace App\Livewire\Author;

use App\Models\Journal;
use App\Models\Section;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Models\SubmissionFile;
use App\Traits\SanitizesInput;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Kirim Naskah Baru')]
class SubmissionWizard extends Component
{
    use WithFileUploads, SanitizesInput;

    public int $step = 1;
    public const TOTAL_STEPS = 5;

    // Step 1 – Journal & Section
    public ?int $journalId  = null;
    public ?int $sectionId  = null;

    // Step 2 – Metadata
    public string $title         = '';
    public string $subtitle      = '';
    public string $abstract      = '';
    public string $keywordsInput = '';
    public string $locale        = 'id';

    // Step 3 – Contributors
    public array $contributors = [];

    // Step 4 – Details + File
    public string $coverLetter        = '';
    public string $competingInterests = '';
    public $manuscriptFile            = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->contributors = [[
            'first_name'     => $user->first_name,
            'last_name'      => $user->last_name,
            'email'          => $user->email,
            'affiliation'    => $user->affiliation ?? '',
            'primary_contact'=> true,
        ]];
    }

    // ── Computed ──────────────────────────────────────────────────────────────
    public function getJournalsProperty()
    {
        return Journal::where('status', 'active')->where('enabled', true)->orderBy('name')->get();
    }

    public function getSectionsProperty()
    {
        if (!$this->journalId) return collect();
        return Section::where('journal_id', $this->journalId)->where('is_inactive', false)->get();
    }

    // ── Navigation ────────────────────────────────────────────────────────────
    public function next(): void
    {
        if (!$this->validateStep($this->step)) return;
        if ($this->step < self::TOTAL_STEPS) $this->step++;
    }

    public function back(): void
    {
        if ($this->step > 1) $this->step--;
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->step) $this->step = $step;
    }

    // ── Contributors ──────────────────────────────────────────────────────────
    public function addContributor(): void
    {
        $this->contributors[] = [
            'first_name'     => '',
            'last_name'      => '',
            'email'          => '',
            'affiliation'    => '',
            'primary_contact'=> false,
        ];
    }

    public function removeContributor(int $index): void
    {
        if ($index === 0) return; // Keep primary author
        unset($this->contributors[$index]);
        $this->contributors = array_values($this->contributors);
    }

    // ── Submit ────────────────────────────────────────────────────────────────
    public function submit(): void
    {
        if (!$this->validateStep(4)) return;

        // Rate limit: maks 5 submission per jam per user
        $key = 'submission:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('rate_limit', "Terlalu banyak pengiriman. Coba lagi dalam {$seconds} detik.");
            return;
        }
        RateLimiter::hit($key, 3600);

        // Sanitasi input sebelum disimpan
        $keywords = array_values(array_filter(
            array_map(fn($k) => $this->sanitizePlain($k), explode(',', $this->keywordsInput))
        ));

        $submission = Submission::create([
            'journal_id'         => $this->journalId,
            'section_id'         => $this->sectionId,
            'user_id'            => auth()->id(),
            'status'             => 'submitted',
            'title'              => $this->sanitizePlain($this->title),
            'subtitle'           => $this->sanitizePlain($this->subtitle) ?: null,
            'abstract'           => $this->sanitizeRich($this->abstract),
            'keywords'           => $keywords ?: null,
            'locale'             => $this->locale,
            'competing_interests'=> $this->sanitizePlain($this->competingInterests) ?: null,
            'submitted_at'       => now(),
        ]);

        foreach ($this->contributors as $seq => $data) {
            SubmissionContributor::create([
                'submission_id'    => $submission->id,
                'first_name'       => $this->sanitizePlain($data['first_name']),
                'last_name'        => $this->sanitizePlain($data['last_name']),
                'email'            => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                'affiliation'      => $this->sanitizePlain($data['affiliation'] ?? ''),
                'primary_contact'  => (bool)($data['primary_contact'] ?? false),
                'include_in_browse'=> true,
                'sequence'         => $seq + 1,
                'country'          => 'ID',
            ]);
        }

        if ($this->manuscriptFile) {
            $mime         = $this->manuscriptFile->getMimeType();
            $originalName = $this->sanitizeFilename($this->manuscriptFile->getClientOriginalName());
            $storedName   = $this->manuscriptFile->hashName();
            $path         = $this->manuscriptFile->storeAs(
                'submissions/' . $submission->id,
                $storedName,
                'public'
            );

            SubmissionFile::create([
                'submission_id'     => $submission->id,
                'user_id'           => auth()->id(),
                'file_stage'        => 2,
                'original_file_name'=> $originalName,
                'stored_file_name'  => $storedName,
                'path'              => $path,
                'mime_type'         => $mime,
                'file_size'         => $this->manuscriptFile->getSize(),
                'revision'          => 1,
                'genre'             => 'Article Text',
                'viewable'          => true,
            ]);
        }

        session()->flash('submission_success', 'Naskah berhasil dikirim! Nomor: #' . $submission->id);
        $this->redirect(route('submissions.show', $submission), navigate: true);
    }

    // ── Validation ────────────────────────────────────────────────────────────
    private function validateStep(int $step): bool
    {
        match ($step) {
            1 => $this->validate([
                'journalId' => 'required|exists:journals,id',
                'sectionId' => 'required|exists:sections,id',
            ], [
                'journalId.required' => 'Pilih jurnal terlebih dahulu.',
                'sectionId.required' => 'Pilih seksi naskah.',
            ]),
            2 => $this->validate([
                'title'    => 'required|string|min:10|max:500',
                'abstract' => 'required|string|min:50|max:5000',
            ], [
                'title.required'    => 'Judul naskah wajib diisi.',
                'title.min'         => 'Judul terlalu pendek (minimal 10 karakter).',
                'abstract.required' => 'Abstrak wajib diisi.',
                'abstract.min'      => 'Abstrak terlalu pendek (minimal 50 karakter).',
            ]),
            3 => $this->validate([
                'contributors'              => 'required|array|min:1',
                'contributors.*.first_name' => 'required|string',
                'contributors.*.last_name'  => 'required|string',
                'contributors.*.email'      => 'required|email',
            ], [
                'contributors.*.first_name.required' => 'Nama depan penulis wajib diisi.',
                'contributors.*.last_name.required'  => 'Nama belakang penulis wajib diisi.',
                'contributors.*.email.required'      => 'Email penulis wajib diisi.',
                'contributors.*.email.email'         => 'Format email penulis tidak valid.',
            ]),
            4 => $this->validate([
                // File: wajib ada, hanya PDF/DOC/DOCX, maks 20MB
                'manuscriptFile' => 'required|file|mimes:pdf,doc,docx|max:20480',
            ], [
                'manuscriptFile.required' => 'File naskah wajib diunggah.',
                'manuscriptFile.mimes'    => 'Format file harus PDF, DOC, atau DOCX.',
                'manuscriptFile.max'      => 'Ukuran file maksimal 20MB.',
            ]),
            default => null,
        };

        return empty($this->getErrorBag()->all());
    }

    public function render()
    {
        return view('livewire.author.submission-wizard', [
            'journals'   => $this->journals,
            'sections'   => $this->sections,
            'totalSteps' => self::TOTAL_STEPS,
        ]);
    }
}

<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.manager')]
class Settings extends Component
{
    use WithFileUploads;

    public ?Journal $journal = null;

    // File uploads
    public $newLogo        = null;
    public $newCoverImage  = null;

    // Identitas
    public string $name                  = '';
    public string $name_abbrev           = '';
    public string $issn_print            = '';
    public string $issn_online           = '';
    public string $publisher             = '';
    public string $url                   = '';
    public string $publication_frequency = '';
    public string $primary_locale        = 'id';

    // Kontak
    public string $email           = '';
    public string $contact_name    = '';
    public string $contact_phone   = '';
    public string $mailing_address = '';

    // Akreditasi & Indeksasi
    public string $sinta_level          = '';
    public string $sinta_id             = '';
    public string $accreditation_no     = '';
    public string $accreditation_period = '';
    public string $doaj_id              = '';
    public string $garuda_id            = '';
    public string $doi_prefix           = '';

    // Review & Lisensi
    public string $review_mode            = 'double_blind';
    public int    $num_weeks_per_review   = 4;
    public string $license_type           = 'cc_by';
    public string $copyright_holder       = '';
    public string $open_access_statement  = '';
    public string $copyright_notice       = '';

    // Konten
    public string $focus_scope       = '';
    public string $author_guidelines = '';
    public string $about_journal     = '';
    public string $ethics_statement  = '';

    // APC
    public bool   $apc_enabled      = false;
    public string $apc_amount       = '';
    public string $apc_currency     = 'IDR';
    public string $apc_waiver_policy= '';
    public string $wa_contact       = '';

    // Turnitin
    public string $turnitin_api_key    = '';
    public string $turnitin_account_id = '';

    // WhatsApp
    public string $wa_api_token      = '';
    public string $wa_sender_number  = '';

    // LOA
    public string $loa_signer_name  = '';
    public string $loa_signer_title = '';

    // Konten Tambahan
    public string $description              = '';
    public string $reviewer_guidelines      = '';
    public string $privacy_statement        = '';
    public string $submission_acknowledgement = '';
    public string $announcements_intro      = '';
    public bool   $announcements_enabled    = true;

    // Submission settings
    public bool   $requires_author_competinginterests   = false;
    public bool   $requires_reviewer_competinginterests = false;
    public int    $num_weeks_per_response               = 1;
    public string $submission_checklist                 = '';

    // SINTA scores
    public string $sinta_score     = '';
    public string $sinta_score_3yr = '';

    // Tech support
    public string $tech_support_name  = '';
    public string $tech_support_email = '';

    // Lokasi
    public string $country  = '';
    public string $timezone = 'Asia/Jakarta';

    // Custom HTML
    public string $custom_header_html = '';
    public string $custom_footer_html = '';

    // Uploads tambahan
    public $newFavicon      = null;
    public $newHeaderBanner = null;

    // Header Jurnal (disimpan di settings JSON + homepage_image)
    public string $header_bg_type    = 'default'; // default|color|gradient|image
    public string $header_bg_color   = '#1e3a8a';
    public string $header_bg_color2  = '#4338ca';
    public bool   $header_text_light = true;
    public string $header_tagline    = '';

    // Status
    public bool $enabled             = true;
    public bool $disable_submissions = false;

    public function mount(): void
    {
        $journals = Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->get();

        $activeId      = session('manager_active_journal');
        $this->journal = $journals->firstWhere('id', $activeId) ?? $journals->first();

        if (!$this->journal) return;

        $j = $this->journal;
        $this->name                  = $j->name ?? '';
        $this->name_abbrev           = $j->name_abbrev ?? '';
        $this->issn_print            = $j->issn_print ?? '';
        $this->issn_online           = $j->issn_online ?? '';
        $this->publisher             = $j->publisher ?? '';
        $this->url                   = $j->url ?? '';
        $this->publication_frequency = $j->publication_frequency ?? '';
        $this->primary_locale        = $j->primary_locale ?? 'id';

        $this->email           = $j->email ?? '';
        $this->contact_name    = $j->contact_name ?? '';
        $this->contact_phone   = $j->contact_phone ?? '';
        $this->mailing_address = $j->mailing_address ?? '';

        $this->sinta_level          = $j->sinta_level ?? '';
        $this->sinta_id             = $j->sinta_id ?? '';
        $this->accreditation_no     = $j->accreditation_no ?? '';
        $this->accreditation_period = $j->accreditation_period ?? '';
        $this->doaj_id              = $j->doaj_id ?? '';
        $this->garuda_id            = $j->garuda_id ?? '';
        $this->doi_prefix           = $j->doi_prefix ?? '';

        $this->review_mode           = $j->review_mode ?? 'double_blind';
        $this->num_weeks_per_review  = (int)($j->num_weeks_per_review ?? 4);
        $this->license_type          = $j->license_type ?? 'cc_by';
        $this->copyright_holder      = $j->copyright_holder ?? '';
        $this->open_access_statement = $j->open_access_statement ?? '';
        $this->copyright_notice      = $j->copyright_notice ?? '';

        $this->focus_scope       = $j->focus_scope ?? '';
        $this->author_guidelines = $j->author_guidelines ?? '';
        $this->about_journal     = $j->about_journal ?? '';
        $this->ethics_statement  = $j->ethics_statement ?? '';

        $this->apc_enabled       = (bool)($j->apc_enabled ?? false);
        $this->apc_amount        = (string)($j->apc_amount ?? '');
        $this->apc_currency      = $j->apc_currency ?? 'IDR';
        $this->apc_waiver_policy = $j->apc_waiver_policy ?? '';
        $this->wa_contact        = $j->wa_contact ?? '';

        $this->turnitin_api_key    = $j->turnitin_api_key ?? '';
        $this->turnitin_account_id = $j->turnitin_account_id ?? '';
        $this->wa_api_token        = $j->wa_api_token ?? '';
        $this->wa_sender_number    = $j->wa_sender_number ?? '';

        $this->loa_signer_name  = $j->loa_signer_name ?? '';
        $this->loa_signer_title = $j->loa_signer_title ?? '';

        $this->description               = $j->description ?? '';
        $this->reviewer_guidelines       = $j->reviewer_guidelines ?? '';
        $this->privacy_statement         = $j->privacy_statement ?? '';
        $this->submission_acknowledgement= $j->submission_acknowledgement ?? '';
        $this->announcements_intro       = $j->announcements_intro ?? '';
        $this->announcements_enabled     = (bool)($j->announcements_enabled ?? true);

        $this->requires_author_competinginterests   = (bool)($j->requires_author_competinginterests ?? false);
        $this->requires_reviewer_competinginterests = (bool)($j->requires_reviewer_competinginterests ?? false);
        $this->num_weeks_per_response               = (int)($j->num_weeks_per_response ?? 1);

        $checklist = $j->submission_checklist;
        $this->submission_checklist = is_array($checklist)
            ? implode("\n", $checklist)
            : ($checklist ?? '');

        $this->sinta_score     = (string)($j->sinta_score ?? '');
        $this->sinta_score_3yr = (string)($j->sinta_score_3yr ?? '');

        $this->tech_support_name  = $j->tech_support_name ?? '';
        $this->tech_support_email = $j->tech_support_email ?? '';

        $this->country  = $j->country ?? '';
        $this->timezone = $j->timezone ?? 'Asia/Jakarta';

        $this->custom_header_html = $j->custom_header_html ?? '';
        $this->custom_footer_html = $j->custom_footer_html ?? '';

        $s = $j->settings ?? [];
        $this->header_bg_type    = $s['header_bg_type']   ?? 'default';
        $this->header_bg_color   = $s['header_bg_color']  ?? '#1e3a8a';
        $this->header_bg_color2  = $s['header_bg_color2'] ?? '#4338ca';
        $this->header_text_light = (bool)($s['header_text_light'] ?? true);
        $this->header_tagline    = $s['header_tagline']   ?? '';

        $this->enabled             = (bool)$j->enabled;
        $this->disable_submissions = (bool)$j->disable_submissions;
    }

    protected function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'name_abbrev'           => 'nullable|string|max:50',
            'issn_print'            => 'nullable|string|max:20',
            'issn_online'           => 'nullable|string|max:20',
            'publisher'             => 'nullable|string|max:255',
            'url'                   => 'nullable|url|max:255',
            'publication_frequency' => 'nullable|string|max:255',
            'primary_locale'        => 'nullable|string|max:10',
            'email'                 => 'nullable|email|max:255',
            'contact_name'          => 'nullable|string|max:255',
            'contact_phone'         => 'nullable|string|max:50',
            'mailing_address'       => 'nullable|string',
            'sinta_level'           => 'nullable|string|max:10',
            'sinta_id'              => 'nullable|string|max:50',
            'accreditation_no'      => 'nullable|string|max:100',
            'accreditation_period'  => 'nullable|string|max:50',
            'doaj_id'               => 'nullable|string|max:100',
            'garuda_id'             => 'nullable|string|max:100',
            'doi_prefix'            => 'nullable|string|max:50',
            'review_mode'           => 'required|string',
            'num_weeks_per_review'  => 'required|integer|min:1|max:52',
            'license_type'          => 'nullable|string|max:50',
            'copyright_holder'      => 'nullable|string|max:255',
            'open_access_statement' => 'nullable|string',
            'copyright_notice'      => 'nullable|string',
            'focus_scope'           => 'nullable|string',
            'author_guidelines'     => 'nullable|string',
            'about_journal'         => 'nullable|string',
            'ethics_statement'      => 'nullable|string',
            'apc_enabled'           => 'boolean',
            'apc_amount'            => 'nullable|numeric|min:0',
            'apc_currency'          => 'nullable|string|max:10',
            'apc_waiver_policy'     => 'nullable|string',
            'wa_contact'            => 'nullable|string|max:50',
            'turnitin_api_key'      => 'nullable|string|max:255',
            'turnitin_account_id'   => 'nullable|string|max:100',
            'wa_api_token'          => 'nullable|string|max:255',
            'wa_sender_number'      => 'nullable|string|max:20',
            'loa_signer_name'       => 'nullable|string|max:255',
            'loa_signer_title'      => 'nullable|string|max:255',
            'description'           => 'nullable|string|max:1000',
            'reviewer_guidelines'   => 'nullable|string',
            'privacy_statement'     => 'nullable|string',
            'submission_acknowledgement' => 'nullable|string',
            'announcements_intro'   => 'nullable|string',
            'announcements_enabled' => 'boolean',
            'requires_author_competinginterests'   => 'boolean',
            'requires_reviewer_competinginterests' => 'boolean',
            'num_weeks_per_response' => 'nullable|integer|min:1|max:52',
            'submission_checklist'  => 'nullable|string',
            'sinta_score'           => 'nullable|numeric|min:0',
            'sinta_score_3yr'       => 'nullable|numeric|min:0',
            'tech_support_name'     => 'nullable|string|max:255',
            'tech_support_email'    => 'nullable|email|max:255',
            'country'               => 'nullable|string|max:100',
            'timezone'              => 'nullable|string|max:100',
            'custom_header_html'    => 'nullable|string',
            'custom_footer_html'    => 'nullable|string',
            'enabled'               => 'boolean',
            'disable_submissions'   => 'boolean',
            'newLogo'               => 'nullable|image|max:2048',
            'newCoverImage'         => 'nullable|image|max:2048',
            'newFavicon'            => 'nullable|image|max:512',
            'newHeaderBanner'       => 'nullable|image|max:4096',
            'header_bg_type'        => 'nullable|string|in:default,color,gradient,image',
            'header_bg_color'       => 'nullable|string|max:20',
            'header_bg_color2'      => 'nullable|string|max:20',
            'header_text_light'     => 'boolean',
            'header_tagline'        => 'nullable|string|max:255',
        ];
    }

    public function save(): void
    {
        $this->validate();
        if (!$this->journal) return;

        $updateData = [
            'name'                  => $this->name,
            'name_abbrev'           => $this->name_abbrev ?: null,
            'issn_print'            => $this->issn_print ?: null,
            'issn_online'           => $this->issn_online ?: null,
            'publisher'             => $this->publisher ?: null,
            'url'                   => $this->url ?: null,
            'publication_frequency' => $this->publication_frequency ?: null,
            'primary_locale'        => $this->primary_locale ?: null,
            'email'                 => $this->email ?: null,
            'contact_name'          => $this->contact_name ?: null,
            'contact_phone'         => $this->contact_phone ?: null,
            'mailing_address'       => $this->mailing_address ?: null,
            'sinta_level'           => $this->sinta_level ?: null,
            'sinta_id'              => $this->sinta_id ?: null,
            'accreditation_no'      => $this->accreditation_no ?: null,
            'accreditation_period'  => $this->accreditation_period ?: null,
            'doaj_id'               => $this->doaj_id ?: null,
            'garuda_id'             => $this->garuda_id ?: null,
            'doi_prefix'            => $this->doi_prefix ?: null,
            'review_mode'           => $this->review_mode,
            'num_weeks_per_review'  => $this->num_weeks_per_review,
            'license_type'          => $this->license_type ?: null,
            'copyright_holder'      => $this->copyright_holder ?: null,
            'open_access_statement' => $this->open_access_statement ?: null,
            'copyright_notice'      => $this->copyright_notice ?: null,
            'focus_scope'           => $this->focus_scope ?: null,
            'author_guidelines'     => $this->author_guidelines ?: null,
            'about_journal'         => $this->about_journal ?: null,
            'ethics_statement'      => $this->ethics_statement ?: null,
            'apc_enabled'           => $this->apc_enabled,
            'apc_amount'            => $this->apc_amount ?: null,
            'apc_currency'          => $this->apc_currency ?: 'IDR',
            'apc_waiver_policy'     => $this->apc_waiver_policy ?: null,
            'wa_contact'            => $this->wa_contact ?: null,
            'turnitin_api_key'      => $this->turnitin_api_key ?: null,
            'turnitin_account_id'   => $this->turnitin_account_id ?: null,
            'wa_api_token'          => $this->wa_api_token ?: null,
            'wa_sender_number'      => $this->wa_sender_number ?: null,
            'loa_signer_name'       => $this->loa_signer_name ?: null,
            'loa_signer_title'      => $this->loa_signer_title ?: null,
            'description'           => $this->description ?: null,
            'reviewer_guidelines'   => $this->reviewer_guidelines ?: null,
            'privacy_statement'     => $this->privacy_statement ?: null,
            'submission_acknowledgement' => $this->submission_acknowledgement ?: null,
            'announcements_intro'   => $this->announcements_intro ?: null,
            'announcements_enabled' => $this->announcements_enabled,
            'requires_author_competinginterests'   => $this->requires_author_competinginterests,
            'requires_reviewer_competinginterests' => $this->requires_reviewer_competinginterests,
            'num_weeks_per_response' => $this->num_weeks_per_response ?: null,
            'submission_checklist'  => $this->submission_checklist
                ? array_values(array_filter(array_map('trim', explode("\n", $this->submission_checklist))))
                : null,
            'sinta_score'     => $this->sinta_score ?: null,
            'sinta_score_3yr' => $this->sinta_score_3yr ?: null,
            'tech_support_name'  => $this->tech_support_name ?: null,
            'tech_support_email' => $this->tech_support_email ?: null,
            'country'   => $this->country ?: null,
            'timezone'  => $this->timezone ?: null,
            'custom_header_html' => $this->custom_header_html ?: null,
            'custom_footer_html' => $this->custom_footer_html ?: null,
            'enabled'               => $this->enabled,
            'disable_submissions'   => $this->disable_submissions,
        ];

        if ($this->newLogo) {
            $updateData['logo'] = $this->newLogo->store('journals/logos', 'public');
            $this->newLogo = null;
        }
        if ($this->newCoverImage) {
            $updateData['cover_image'] = $this->newCoverImage->store('journals/covers', 'public');
            $this->newCoverImage = null;
        }
        if ($this->newFavicon) {
            $updateData['favicon'] = $this->newFavicon->store('journals/favicons', 'public');
            $this->newFavicon = null;
        }
        if ($this->newHeaderBanner) {
            $updateData['homepage_image'] = $this->newHeaderBanner->store('journals/banners', 'public');
            $this->newHeaderBanner = null;
        }

        $existingSettings = $this->journal->settings ?? [];
        $updateData['settings'] = array_merge($existingSettings, [
            'header_bg_type'    => $this->header_bg_type,
            'header_bg_color'   => $this->header_bg_color,
            'header_bg_color2'  => $this->header_bg_color2,
            'header_text_light' => $this->header_text_light,
            'header_tagline'    => $this->header_tagline,
        ]);

        $this->journal->update($updateData);

        $this->dispatch('toast', message: 'Pengaturan jurnal berhasil disimpan.', type: 'success');
    }

    public function render()
    {
        return view('livewire.journal-manager.settings')
            ->title('Pengaturan Jurnal — Panel Pengelola');
    }
}
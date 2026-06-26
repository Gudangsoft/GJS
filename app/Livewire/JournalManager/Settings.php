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
    public int $journalId = 0; // int fallback — reliable Livewire serialization

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

    // Bulan terbit (disimpan di settings JSON)
    public array $publication_months   = [];
    public int   $publication_freq_count = 1;

    // Header Jurnal (disimpan di settings JSON + homepage_image)
    public string $header_bg_type    = 'default';
    public string $header_bg_color   = '#1e3a8a';
    public string $header_bg_color2  = '#4338ca';
    public bool   $header_text_light = true;
    public string $header_tagline    = '';

    // Background Website Jurnal
    public string $site_bg_color = '#f1f5f9';

    // Indeksasi (disimpan di settings JSON)
    public array  $indexed_by        = [];   // [{name, url, logo}]
    public string $new_indexer_name  = '';
    public string $new_indexer_url   = '';
    public $new_indexer_logo         = null;

    // Sponsor & Mitra (disimpan di settings JSON)
    public array  $sponsors          = [];   // [{name, url, logo}]
    public string $new_sponsor_name  = '';
    public string $new_sponsor_url   = '';
    public $new_sponsor_logo         = null;

    // Menu Jurnal (disimpan di settings JSON)
    public bool   $menu_show_issues        = true;
    public bool   $menu_show_announcements = true;
    public bool   $menu_show_about         = true;
    public bool   $menu_show_browse        = true;
    public array  $custom_menu_items       = [];  // [{label, url, target}]
    public string $new_menu_label          = '';
    public string $new_menu_url            = '';
    public string $new_menu_target         = '_self';

    // Status
    public bool $enabled             = true;
    public bool $disable_submissions = false;

    private function resolveJournals()
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return Journal::orderBy('name')->get();
        }
        return Journal::whereHas('managers', fn($q) => $q->where('users.id', $user->id))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', $user->id))
            ->get();
    }

    public function mount(): void
    {
        $journals = $this->resolveJournals();

        $activeId      = session('manager_active_journal');
        $this->journal = $journals->firstWhere('id', $activeId) ?? $journals->first();

        if (!$this->journal) return;
        $this->journalId = $this->journal->id;

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
        $this->publication_months     = $s['publication_months']    ?? [];
        $this->publication_freq_count = (int)($s['publication_freq_count'] ?? 1);
        $this->header_bg_type    = $s['header_bg_type']   ?? 'default';
        $this->header_bg_color   = $s['header_bg_color']  ?? '#1e3a8a';
        $this->header_bg_color2  = $s['header_bg_color2'] ?? '#4338ca';
        $this->header_text_light = (bool)($s['header_text_light'] ?? true);
        $this->header_tagline    = $s['header_tagline']   ?? '';
        $this->site_bg_color     = $s['site_bg_color']    ?? '#f1f5f9';
        $this->indexed_by        = $s['indexed_by']        ?? [];
        $this->sponsors          = $s['sponsors']          ?? [];
        $this->menu_show_issues        = (bool)($s['menu_show_issues']        ?? true);
        $this->menu_show_announcements = (bool)($s['menu_show_announcements'] ?? true);
        $this->menu_show_about         = (bool)($s['menu_show_about']         ?? true);
        $this->menu_show_browse        = (bool)($s['menu_show_browse']        ?? true);
        $this->custom_menu_items       = $s['custom_menu_items']              ?? [];

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
            'publication_months'      => 'nullable|array',
            'publication_months.*'    => 'integer|between:1,12',
            'publication_freq_count'  => 'nullable|integer|in:1,2,3,4,6,12',
            'newHeaderBanner'       => 'nullable|image|max:4096',
            'new_indexer_logo'      => 'nullable|image|max:2048',
            'new_sponsor_logo'      => 'nullable|image|max:2048',
            'site_bg_color'         => 'nullable|string|max:20',
            'new_menu_label'        => 'nullable|string|max:60',
            'new_menu_url'          => 'nullable|string|max:500',
            'new_menu_target'       => 'nullable|in:_self,_blank',
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

        // Auto-generate publication_frequency dari bulan dipilih
        $monthNames = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                       7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $freqLabels = [1=>'Tahunan',2=>'Semesteran',3=>'3x Setahun',4=>'Kuartalan',6=>'2 Bulanan',12=>'Bulanan'];
        $selectedMonths = array_values(array_filter($this->publication_months));
        sort($selectedMonths);
        // Trim jika melebihi limit yang dipilih
        $selectedMonths = array_slice($selectedMonths, 0, $this->publication_freq_count);
        if (!empty($selectedMonths)) {
            $count = count($selectedMonths);
            $names = array_map(fn($m) => $monthNames[$m] ?? '', $selectedMonths);
            $last  = array_pop($names);
            $monthStr = empty($names) ? $last : implode(', ', $names) . ' & ' . $last;
            $label = $freqLabels[$this->publication_freq_count] ?? "{$this->publication_freq_count}x Setahun";
            $updateData['publication_frequency'] = "{$this->publication_freq_count}x {$label} ({$monthStr})";
        }

        $existingSettings = $this->journal->settings ?? [];
        $updateData['settings'] = array_merge($existingSettings, [
            'publication_months'     => $selectedMonths,
            'publication_freq_count' => $this->publication_freq_count,
            'header_bg_type'    => $this->header_bg_type,
            'header_bg_color'   => $this->header_bg_color,
            'header_bg_color2'  => $this->header_bg_color2,
            'header_text_light' => $this->header_text_light,
            'header_tagline'    => $this->header_tagline,
            'site_bg_color'     => $this->site_bg_color,
            'indexed_by'        => $this->indexed_by,
            'sponsors'          => $this->sponsors,
            'menu_show_issues'        => $this->menu_show_issues,
            'menu_show_announcements' => $this->menu_show_announcements,
            'menu_show_about'         => $this->menu_show_about,
            'menu_show_browse'        => $this->menu_show_browse,
            'custom_menu_items'       => $this->custom_menu_items,
        ]);

        $this->journal->update($updateData);

        $this->dispatch('toast', message: 'Pengaturan jurnal berhasil disimpan.', type: 'success');
    }

    private function getActiveJournal(): ?Journal
    {
        $journals = $this->resolveJournals();
        $activeId = session('manager_active_journal');
        return $journals->firstWhere('id', $activeId) ?? $journals->first();
    }

    private function persistLists(): void
    {
        // journalId (int) is always reliably preserved by Livewire — use it first
        $journal = ($this->journalId > 0)
            ? Journal::find($this->journalId)
            : ($this->journal ?? $this->getActiveJournal());

        if (!$journal) return;

        $journal->update([
            'settings' => array_merge($journal->fresh()->settings ?? [], [
                'indexed_by' => $this->indexed_by,
                'sponsors'   => $this->sponsors,
            ]),
        ]);
    }

    public function addPresetIndexer(string $name, string $url): void
    {
        $exists = collect($this->indexed_by)->pluck('name')->contains($name);
        if ($exists) return;
        $this->indexed_by[] = ['name' => $name, 'url' => $url, 'logo' => null];
        $this->persistLists();
    }

    public function addIndexer(): void
    {
        $this->validateOnly('new_indexer_logo');
        $name = trim($this->new_indexer_name);
        if (!$name) return;

        $logo = null;
        if ($this->new_indexer_logo) {
            $logo = $this->new_indexer_logo->store('journals/indexers', 'public');
            $this->new_indexer_logo = null;
        }

        $this->indexed_by[] = [
            'name' => $name,
            'url'  => trim($this->new_indexer_url) ?: null,
            'logo' => $logo,
        ];
        $this->new_indexer_name = '';
        $this->new_indexer_url  = '';
        $this->persistLists();
    }

    public function removeIndexer(int $i): void
    {
        $item = $this->indexed_by[$i] ?? null;
        if ($item && isset($item['logo']) && $item['logo']) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($item['logo']);
        }
        array_splice($this->indexed_by, $i, 1);
        $this->persistLists();
    }

    public function addSponsor(): void
    {
        $this->validateOnly('new_sponsor_logo');
        $name = trim($this->new_sponsor_name);
        if (!$name) return;

        $logo = null;
        if ($this->new_sponsor_logo) {
            $logo = $this->new_sponsor_logo->store('journals/sponsors', 'public');
            $this->new_sponsor_logo = null;
        }

        $this->sponsors[] = [
            'name' => $name,
            'url'  => trim($this->new_sponsor_url) ?: null,
            'logo' => $logo,
        ];
        $this->new_sponsor_name = '';
        $this->new_sponsor_url  = '';
        $this->persistLists();
        $this->dispatch('toast', message: 'Sponsor "' . $name . '" berhasil ditambahkan.', type: 'success');
    }

    public function removeSponsor(int $i): void
    {
        $item = $this->sponsors[$i] ?? null;
        if ($item && isset($item['logo']) && $item['logo']) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($item['logo']);
        }
        array_splice($this->sponsors, $i, 1);
        $this->persistLists();
        $this->dispatch('toast', message: 'Sponsor dihapus.', type: 'success');
    }

    public function addMenuItem(): void
    {
        $label = trim($this->new_menu_label);
        $url   = trim($this->new_menu_url);
        if (!$label || !$url) return;
        $this->custom_menu_items[] = [
            'label'  => $label,
            'url'    => $url,
            'target' => $this->new_menu_target ?: '_self',
        ];
        $this->new_menu_label  = '';
        $this->new_menu_url    = '';
        $this->new_menu_target = '_self';
    }

    public function removeMenuItem(int $i): void
    {
        array_splice($this->custom_menu_items, $i, 1);
    }

    public function moveMenuItemUp(int $i): void
    {
        if ($i > 0) {
            [$this->custom_menu_items[$i - 1], $this->custom_menu_items[$i]] =
                [$this->custom_menu_items[$i], $this->custom_menu_items[$i - 1]];
        }
    }

    public function moveMenuItemDown(int $i): void
    {
        $last = count($this->custom_menu_items) - 1;
        if ($i < $last) {
            [$this->custom_menu_items[$i], $this->custom_menu_items[$i + 1]] =
                [$this->custom_menu_items[$i + 1], $this->custom_menu_items[$i]];
        }
    }

    public function toggleMonth(int $month): void
    {
        if (in_array($month, $this->publication_months)) {
            $this->publication_months = array_values(
                array_filter($this->publication_months, fn($m) => $m !== $month)
            );
        } elseif (count($this->publication_months) < $this->publication_freq_count) {
            $this->publication_months[] = $month;
            sort($this->publication_months);
        }
        // Jika sudah penuh, tidak menambah (user harus uncheck dulu)
    }

    public function updatedPublicationFreqCount(): void
    {
        // Trim pilihan bulan yang melebihi limit baru
        if (count($this->publication_months) > $this->publication_freq_count) {
            $this->publication_months = array_slice($this->publication_months, 0, $this->publication_freq_count);
        }
    }

    public function render()
    {
        return view('livewire.journal-manager.settings')
            ->title('Pengaturan Jurnal — Panel Pengelola');
    }
}
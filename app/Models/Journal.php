<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Journal extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'slug', 'oai_identifier', 'name', 'name_abbrev', 'description', 'publication_frequency',
        'issn_print', 'issn_online', 'publisher', 'email',
        'contact_name', 'contact_phone', 'tech_support_name', 'tech_support_email', 'mailing_address',
        'url', 'logo', 'cover_image', 'favicon', 'homepage_image',
        'custom_header_html', 'custom_footer_html',
        'primary_locale', 'supported_locales', 'country', 'timezone',
        'status', 'enabled', 'announcements_enabled', 'announcements_intro',
        'focus_scope', 'ethics_statement', 'author_guidelines',
        'reviewer_guidelines', 'privacy_statement', 'about_journal',
        'review_mode', 'num_weeks_per_review', 'num_weeks_per_response',
        'requires_author_competinginterests', 'requires_reviewer_competinginterests',
        'disable_submissions', 'submission_checklist', 'submission_acknowledgement', 'copyright_notice',
        'license_type', 'copyright_holder', 'doi_prefix', 'doi_suffix_pattern', 'open_access_statement',
        'loa_signer_name', 'loa_signer_title',
        'apc_enabled', 'apc_amount', 'apc_currency', 'apc_waiver_policy',
        'wa_contact',
        'turnitin_api_key', 'turnitin_account_id',
        'wa_api_token', 'wa_sender_number',
        'settings',
        'sinta_id', 'sinta_level', 'sinta_score', 'sinta_score_3yr',
        'accreditation_no', 'accreditation_period', 'doaj_id', 'garuda_id',
    ];

    protected $casts = [
        'supported_locales'                    => 'array',
        'submission_checklist'                 => 'array',
        'settings'                             => 'array',
        'enabled'                              => 'boolean',
        'announcements_enabled'                => 'boolean',
        'disable_submissions'                  => 'boolean',
        'apc_enabled'                          => 'boolean',
        'requires_author_competinginterests'   => 'boolean',
        'requires_reviewer_competinginterests' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function sidebarBlocks(): HasMany
    {
        return $this->hasMany(JournalSidebarBlock::class)->orderBy('sort_order');
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'journal_managers')
            ->wherePivot('role', 'manager')
            ->withTimestamps();
    }

    public function editors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'journal_managers')
            ->wherePivot('role', 'editor')
            ->withTimestamps();
    }

    public function allMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'journal_managers')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

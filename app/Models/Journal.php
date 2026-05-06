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
        'slug', 'name', 'name_abbrev', 'description',
        'issn_print', 'issn_online', 'publisher', 'email', 'url',
        'logo', 'cover_image', 'primary_locale', 'supported_locales',
        'country', 'timezone', 'status', 'enabled',
        'focus_scope', 'ethics_statement', 'author_guidelines',
        'reviewer_guidelines', 'privacy_statement', 'about_journal',
        'review_mode', 'num_weeks_per_review', 'num_weeks_per_response',
        'requires_author_competinginterests', 'requires_reviewer_competinginterests',
        'settings',
    ];

    protected $casts = [
        'supported_locales' => 'array',
        'settings' => 'array',
        'enabled' => 'boolean',
        'requires_author_competinginterests' => 'boolean',
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

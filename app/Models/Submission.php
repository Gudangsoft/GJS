<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Translatable\HasTranslations;

class Submission extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasTranslations;

    public array $translatable = [
        'title', 'subtitle', 'abstract', 'competing_interests',
    ];

    protected $fillable = [
        'journal_id', 'section_id', 'user_id', 'status',
        'title', 'subtitle', 'abstract', 'keywords',
        'disciplines', 'subjects', 'languages', 'cover_letter_file',
        'locale', 'doi', 'submission_type', 'hide_author',
        'competing_interests', 'submitted_at',
        'similarity_score', 'similarity_checked_at', 'ojs_id', 'ojs_source_url',
    ];

    protected $casts = [
        'keywords' => 'array',
        'disciplines' => 'array',
        'subjects' => 'array',
        'languages' => 'array',
        'hide_author' => 'boolean',
        'submitted_at' => 'datetime',
        'similarity_checked_at' => 'datetime',
        'similarity_score' => 'float',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status', 'title', 'doi'])->logOnlyDirty();
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function contributors(): HasMany
    {
        return $this->hasMany(SubmissionContributor::class)->orderBy('sequence');
    }

    public function reviewRounds(): HasMany
    {
        return $this->hasMany(ReviewRound::class);
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class);
    }

    public function article(): HasOne
    {
        return $this->hasOne(Article::class);
    }

    public function copyEditTask(): HasOne
    {
        return $this->hasOne(\App\Models\CopyEditTask::class);
    }

    public function letterOfAcceptances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\LetterOfAcceptance::class);
    }

    public function scopeForJournal($query, int $journalId)
    {
        return $query->where('journal_id', $journalId);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}

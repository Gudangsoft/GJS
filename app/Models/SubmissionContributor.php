<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionContributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id', 'user_id', 'salutation',
        'first_name', 'last_name', 'email', 'orcid',
        'affiliation', 'country', 'bio', 'url',
        'user_group_id', 'primary_contact', 'include_in_browse', 'sequence',
    ];

    protected $casts = [
        'primary_contact' => 'boolean',
        'include_in_browse' => 'boolean',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

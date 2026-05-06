<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_id', 'title', 'abbrev', 'policy',
        'abstract_word_count', 'word_count', 'hide_title', 'hide_author',
        'is_inactive', 'editor_restricted', 'submitter_restricted', 'sequence',
    ];

    protected $casts = [
        'abstract_word_count' => 'boolean',
        'hide_title' => 'boolean',
        'hide_author' => 'boolean',
        'is_inactive' => 'boolean',
        'editor_restricted' => 'boolean',
        'submitter_restricted' => 'boolean',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}

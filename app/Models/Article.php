<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id', 'issue_id', 'journal_id', 'section_id',
        'doi', 'doi_status', 'pages', 'sequence',
        'date_published', 'access_status', 'views', 'downloads', 'citations',
    ];

    protected $casts = [
        'date_published' => 'datetime',
        'sequence' => 'float',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function galleys(): HasMany
    {
        return $this->hasMany(ArticleGalley::class)->orderBy('sequence');
    }
}

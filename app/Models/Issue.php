<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Issue extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['title', 'description'];

    protected $fillable = [
        'journal_id', 'volume', 'number', 'year', 'title', 'description',
        'cover_image', 'cover_image_alt_text', 'published', 'current',
        'show_volume', 'show_number', 'show_year', 'show_title',
        'access_status', 'doi', 'date_published', 'ojs_id', 'date_notified',
    ];

    protected $casts = [
        'published' => 'boolean',
        'current' => 'boolean',
        'show_volume' => 'boolean',
        'show_number' => 'boolean',
        'show_year' => 'boolean',
        'show_title' => 'boolean',
        'date_published' => 'datetime',
        'date_notified' => 'datetime',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class)->orderBy('sequence');
    }

    public function getLabel(): string
    {
        $parts = [];
        if ($this->show_volume && $this->volume) $parts[] = "Vol. {$this->volume}";
        if ($this->show_number && $this->number) $parts[] = "No. {$this->number}";
        if ($this->show_year && $this->year) $parts[] = "({$this->year})";
        if ($this->show_title && $this->title) $parts[] = $this->title;
        return implode(' ', $parts);
    }
}

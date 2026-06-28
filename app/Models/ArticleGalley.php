<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleGalley extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id', 'label', 'locale', 'submission_file_id',
        'remote_url', 'html_content', 'sequence', 'is_approved', 'views',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(SubmissionFile::class, 'submission_file_id');
    }

    public function isHtml(): bool
    {
        return str_contains(strtolower($this->label), 'html') || !empty($this->html_content);
    }

    public function hasContent(): bool
    {
        return !empty($this->remote_url)
            || !empty($this->submission_file_id)
            || !empty($this->html_content);
    }
}

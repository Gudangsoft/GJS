<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id', 'user_id', 'file_stage',
        'original_file_name', 'stored_file_name', 'path',
        'mime_type', 'file_size', 'revision',
        'source_submission_file_id', 'uploaderUserGroupId',
        'assoc_type', 'assoc_id', 'genre', 'viewable',
    ];

    protected $casts = [
        'viewable' => 'boolean',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_assignment_id', 'recommendation',
        'comments_for_author', 'comments_for_editors',
        'form_responses', 'reviewed_file_id',
    ];

    protected $casts = [
        'form_responses' => 'array',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ReviewAssignment::class, 'review_assignment_id');
    }

    public function reviewedFile(): BelongsTo
    {
        return $this->belongsTo(SubmissionFile::class, 'reviewed_file_id');
    }
}

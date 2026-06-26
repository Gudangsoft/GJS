<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CopyEditFile extends Model
{
    protected $fillable = [
        'copy_edit_task_id', 'submission_file_id', 'type', 'round', 'uploaded_by',
    ];

    public function task(): BelongsTo           { return $this->belongsTo(CopyEditTask::class, 'copy_edit_task_id'); }
    public function submissionFile(): BelongsTo  { return $this->belongsTo(SubmissionFile::class); }
    public function uploader(): BelongsTo        { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function typeLabel(): string
    {
        return match($this->type) {
            'manuscript'      => 'Naskah',
            'proof'           => 'Proof',
            'author_revision' => 'Revisi Penulis',
            'final'           => 'Final',
            default           => $this->type,
        };
    }
}

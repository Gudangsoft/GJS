<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CopyEditTask extends Model
{
    protected $fillable = [
        'submission_id', 'journal_id', 'assigned_by', 'assignee_id',
        'round', 'status', 'editor_notes', 'copyeditor_notes', 'author_notes',
        'deadline', 'assigned_at', 'completed_at',
    ];

    protected $casts = [
        'deadline'     => 'date',
        'assigned_at'  => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function submission(): BelongsTo { return $this->belongsTo(Submission::class); }
    public function journal(): BelongsTo    { return $this->belongsTo(Journal::class); }
    public function assignedBy(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }
    public function assignee(): BelongsTo   { return $this->belongsTo(User::class, 'assignee_id'); }
    public function files(): HasMany        { return $this->hasMany(CopyEditFile::class); }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'         => 'Menunggu',
            'assigned'        => 'Ditugaskan',
            'in_progress'     => 'Sedang Dikerjakan',
            'awaiting_author' => 'Menunggu Penulis',
            'completed'       => 'Selesai',
            default           => $this->status,
        };
    }
}

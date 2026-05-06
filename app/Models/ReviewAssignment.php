<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ReviewAssignment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'submission_id', 'review_round_id', 'reviewer_id', 'editor_id',
        'status', 'review_method', 'round',
        'date_assigned', 'date_notified', 'date_confirmed',
        'date_due', 'date_response_due', 'date_reminded',
        'date_completed', 'date_cancelled',
        'reminder_was_automatic', 'competing_interests', 'unconsidered',
    ];

    protected $casts = [
        'date_assigned' => 'datetime',
        'date_notified' => 'datetime',
        'date_confirmed' => 'datetime',
        'date_due' => 'datetime',
        'date_response_due' => 'datetime',
        'date_reminded' => 'datetime',
        'date_completed' => 'datetime',
        'date_cancelled' => 'datetime',
        'unconsidered' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status'])->logOnlyDirty();
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewRound(): BelongsTo
    {
        return $this->belongsTo(ReviewRound::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}

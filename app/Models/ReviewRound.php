<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewRound extends Model
{
    use HasFactory;

    protected $fillable = ['submission_id', 'round', 'status'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class);
    }
}

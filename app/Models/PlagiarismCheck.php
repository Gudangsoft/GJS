<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlagiarismCheck extends Model
{
    protected $fillable = [
        'submission_id', 'checked_by', 'overall_score',
        'source_length', 'sources_checked', 'results', 'checked_at',
    ];

    protected $casts = [
        'results'    => 'array',
        'checked_at' => 'datetime',
    ];

    public function submission() { return $this->belongsTo(Submission::class); }
    public function checker()    { return $this->belongsTo(User::class, 'checked_by'); }
}

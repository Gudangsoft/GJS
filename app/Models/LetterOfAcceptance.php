<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LetterOfAcceptance extends Model
{
    protected $fillable = [
        'submission_id', 'journal_id', 'issued_by',
        'loa_number', 'verification_code', 'article_title', 'authors', 'status',
        'notes', 'acceptance_date', 'expected_publication_date',
        'volume', 'number', 'year',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $loa) {
            if (!$loa->verification_code) {
                $loa->verification_code = strtoupper(Str::random(8) . '-' . Str::random(8) . '-' . Str::random(8));
            }
        });
    }

    public function verifyUrl(): string
    {
        return route('loa.verify', $this->verification_code);
    }

    protected $casts = [
        'authors'                   => 'array',
        'acceptance_date'           => 'date',
        'expected_publication_date' => 'date',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public static function generateNumber(Journal $journal): string
    {
        $abbrev = strtoupper($journal->name_abbrev ?: substr($journal->name, 0, 4));
        $year   = now()->year;
        $last   = static::where('journal_id', $journal->id)
                        ->whereYear('created_at', $year)
                        ->count() + 1;
        return sprintf('LOA/%s/%d/%03d', $abbrev, $year, $last);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_id', 'user_id', 'title',
        'description_short', 'description',
        'date_expire', 'date_posted', 'send_email',
    ];

    protected $casts = [
        'date_expire' => 'datetime',
        'date_posted' => 'datetime',
        'send_email' => 'boolean',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

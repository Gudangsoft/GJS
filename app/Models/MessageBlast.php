<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageBlast extends Model
{
    protected $fillable = [
        'journal_id','sent_by','type','subject','message',
        'recipients_type','recipients','sent_count','failed_count',
        'status','sent_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'sent_at'    => 'datetime',
    ];

    public function journal()  { return $this->belongsTo(Journal::class); }
    public function sentBy()   { return $this->belongsTo(User::class, 'sent_by'); }
}

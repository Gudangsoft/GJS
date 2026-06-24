<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalSidebarBlock extends Model
{
    protected $fillable = [
        'journal_id', 'type', 'title', 'settings', 'enabled', 'sort_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'enabled'  => 'boolean',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function getDefaultTitle(): string
    {
        return match ($this->type) {
            'journal_info'      => 'Informasi Jurnal',
            'submission'        => 'Kirim Naskah',
            'article_template'  => 'Template Artikel',
            'statistics'        => 'Statistik Jurnal',
            'focus_scope'       => 'Fokus & Ruang Lingkup',
            'custom_html'       => 'Informasi',
            default             => 'Blok Sidebar',
        };
    }

    public function getDisplayTitle(): string
    {
        return $this->title ?: $this->getDefaultTitle();
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}

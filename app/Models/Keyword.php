<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = ['keyword', 'locale', 'discipline', 'usage_count', 'is_approved'];

    protected $casts = ['is_approved' => 'boolean', 'usage_count' => 'integer'];

    public static function suggest(string $query, string $locale = 'id', int $limit = 10): array
    {
        return static::where('locale', $locale)
            ->where('is_approved', true)
            ->where('keyword', 'like', $query . '%')
            ->orderByDesc('usage_count')
            ->limit($limit)
            ->pluck('keyword')
            ->toArray();
    }

    public static function recordUsage(array $keywords, string $locale = 'id'): void
    {
        foreach ($keywords as $kw) {
            $kw = trim($kw);
            if (!$kw) continue;
            static::firstOrCreate(['keyword' => $kw, 'locale' => $locale])
                  ->increment('usage_count');
        }
    }
}

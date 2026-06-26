<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    // ── Static helpers ────────────────────────────────────────────────────────

    public static function get(string $groupKey, mixed $default = null): mixed
    {
        [$group, $key] = self::parseKey($groupKey);

        return Cache::rememberForever("setting.{$group}.{$key}", function () use ($group, $key, $default) {
            return static::where('group', $group)->where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $groupKey, mixed $value): void
    {
        [$group, $key] = self::parseKey($groupKey);

        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );

        Cache::forget("setting.{$group}.{$key}");
    }

    public static function getGroup(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->toArray();
    }

    public static function setGroup(string $group, array $data): void
    {
        foreach ($data as $key => $value) {
            if ($value === null) continue;
            static::updateOrCreate(
                ['group' => $group, 'key' => $key],
                ['value' => $value]
            );
            Cache::forget("setting.{$group}.{$key}");
        }
    }

    public static function forgetGroup(string $group): void
    {
        $keys = static::where('group', $group)->pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting.{$group}.{$key}");
        }
    }

    private static function parseKey(string $groupKey): array
    {
        return str_contains($groupKey, '.')
            ? explode('.', $groupKey, 2)
            : ['general', $groupKey];
    }
}

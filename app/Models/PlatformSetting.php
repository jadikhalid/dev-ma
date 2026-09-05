<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    public const REQUIRE_TALENT_ADMIN_VALIDATION = 'require_talent_admin_validation';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function requiresTalentAdminValidation(): bool
    {
        return static::bool(self::REQUIRE_TALENT_ADMIN_VALIDATION, true);
    }

    public static function setRequiresTalentAdminValidation(bool $enabled): void
    {
        static::setBool(self::REQUIRE_TALENT_ADMIN_VALIDATION, $enabled);
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $raw = Cache::remember(
            static::cacheKey($key),
            now()->addDay(),
            fn () => static::query()->where('key', $key)->value('value')
        );

        if ($raw === null) {
            return $default;
        }

        return filter_var($raw, FILTER_VALIDATE_BOOLEAN);
    }

    public static function setBool(string $key, bool $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value ? '1' : '0'],
        );

        Cache::forget(static::cacheKey($key));
    }

    private static function cacheKey(string $key): string
    {
        return 'platform_setting:'.$key;
    }
}

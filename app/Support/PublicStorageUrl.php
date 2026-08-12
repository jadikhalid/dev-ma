<?php

namespace App\Support;

use DateTimeInterface;

class PublicStorageUrl
{
    /**
     * Build a relative /storage/... URL with an optional cache-busting query.
     */
    public static function make(?string $path, DateTimeInterface|int|string|null $version = null): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $url = '/storage/'.ltrim($path, '/');
        $normalized = self::normalizeVersion($version);

        if ($normalized === null) {
            return $url;
        }

        return $url.'?v='.$normalized;
    }

    private static function normalizeVersion(DateTimeInterface|int|string|null $version): ?string
    {
        if ($version instanceof DateTimeInterface) {
            return (string) $version->getTimestamp();
        }

        if (is_int($version)) {
            return (string) $version;
        }

        if (is_string($version) && $version !== '' && ctype_digit($version)) {
            return $version;
        }

        return null;
    }
}

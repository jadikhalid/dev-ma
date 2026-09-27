<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PortalHost
{
    public static function wwwHost(): string
    {
        return strtolower(trim((string) config('talenma.hosts.www', 'localhost')));
    }

    public static function companyHost(): string
    {
        return strtolower(trim((string) config('talenma.hosts.company', 'entreprises.localhost')));
    }

    public static function isCompanyHost(?Request $request = null): bool
    {
        $request ??= request();

        return self::normalizeHost($request->getHost()) === self::companyHost();
    }

    public static function isTalentHost(?Request $request = null): bool
    {
        $request ??= request();

        return self::normalizeHost($request->getHost()) === self::wwwHost();
    }

    public static function companyRootUrl(): string
    {
        return self::absoluteRoot(self::companyHost());
    }

    public static function wwwRootUrl(): string
    {
        return self::absoluteRoot(self::wwwHost());
    }

    /**
     * Absolute URL on the company portal (path must start with /).
     */
    public static function companyUrl(string $path = '/', array $query = []): string
    {
        $path = '/'.ltrim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        $url = rtrim(self::companyRootUrl(), '/').($path === '/' ? '/' : $path);

        if ($query !== []) {
            $url .= (str_contains($url, '?') ? '&' : '?').http_build_query($query);
        }

        return $url;
    }

    public static function companyLoginUrl(): string
    {
        return self::companyUrl('/login');
    }

    public static function companyOfferUrl(array $query = []): string
    {
        try {
            return route('company.offer', $query, absolute: true);
        } catch (\Throwable) {
            return self::companyUrl('/', $query);
        }
    }

    /**
     * Temporarily force URL generation onto the company host.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public static function onCompanyHost(callable $callback): mixed
    {
        return self::withRootUrl(self::companyRootUrl(), $callback);
    }

    /**
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public static function onWwwHost(callable $callback): mixed
    {
        return self::withRootUrl(self::wwwRootUrl(), $callback);
    }

    /**
     * Generate URLs on the portal where this user can sign in
     * (sessions are not shared between hosts).
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public static function forUser(?User $user, callable $callback): mixed
    {
        if (! $user) {
            return $callback();
        }

        return $user->isCompany()
            ? self::onCompanyHost($callback)
            : self::onWwwHost($callback);
    }

    /** @var list<string|null> */
    private static array $forcedRootStack = [];

    private static function withRootUrl(string $root, callable $callback): mixed
    {
        $previous = self::$forcedRootStack === [] ? null : end(self::$forcedRootStack);
        self::$forcedRootStack[] = $root;
        URL::forceRootUrl($root);

        try {
            return $callback();
        } finally {
            array_pop(self::$forcedRootStack);
            URL::forceRootUrl($previous);
        }
    }

    private static function normalizeHost(string $host): string
    {
        $host = strtolower(trim($host));

        if (str_contains($host, ':')) {
            $host = explode(':', $host, 2)[0];
        }

        return $host;
    }

    private static function absoluteRoot(string $host): string
    {
        $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: 'http';
        $port = parse_url((string) config('app.url'), PHP_URL_PORT);
        $root = $scheme.'://'.$host;

        if ($port && ! in_array((int) $port, [80, 443], true)) {
            $root .= ':'.$port;
        }

        return $root;
    }
}

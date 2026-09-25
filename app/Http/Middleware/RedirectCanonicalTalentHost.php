<?php

namespace App\Http\Middleware;

use App\Support\PortalHost;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force a single talent host (HOST_WWW). Requests on the www/apex twin redirect 301.
 * Example: www.talentsdumaroc.com/* → https://talentsdumaroc.com/*
 */
class RedirectCanonicalTalentHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $canonical = PortalHost::wwwHost();
        $host = strtolower($request->getHost());

        if (str_contains($host, ':')) {
            $host = explode(':', $host, 2)[0];
        }

        if ($this->shouldSkip($canonical)) {
            return $next($request);
        }

        $alias = str_starts_with($canonical, 'www.')
            ? substr($canonical, 4)
            : 'www.'.$canonical;

        if ($host !== $alias || $host === $canonical) {
            return $next($request);
        }

        $target = PortalHost::wwwRootUrl().$request->getRequestUri();

        return redirect()->away($target, 301);
    }

    private function shouldSkip(string $canonical): bool
    {
        if ($canonical === '' || $canonical === 'localhost' || $canonical === '127.0.0.1') {
            return true;
        }

        // Local-style hosts without a public TLD (e.g. "localhost", single label).
        return ! str_contains($canonical, '.');
    }
}

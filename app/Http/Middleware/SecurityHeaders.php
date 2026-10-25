<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vite HMR (often on http://[::1]:5173) breaks under a strict CSP in local.
        // Keep the other headers; enforce CSP only outside local.
        if (! app()->environment('local')) {
            Vite::useCspNonce();
        }

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=(), interest-cohort=()',
        );
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        if (! app()->environment('local')) {
            $response->headers->set(
                'Content-Security-Policy',
                $this->contentSecurityPolicy((string) Vite::cspNonce()),
            );
        }

        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=63072000; includeSubDomains',
            );
        }

        return $response;
    }

    private function contentSecurityPolicy(string $nonce): string
    {
        // Alpine evaluates x-data / x-show with new Function(); without 'unsafe-eval'
        // those directives never bind and dropdowns stay visible/frozen.
        $scriptSrc = ["'self'", "'nonce-{$nonce}'", "'unsafe-eval'"];
        $styleSrc = ["'self'", "'unsafe-inline'", 'https://fonts.bunny.net'];
        $connectSrc = ["'self'"];
        $fontSrc = ["'self'", 'https://fonts.bunny.net', 'data:'];

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            'script-src '.implode(' ', $scriptSrc),
            // Event handlers (onclick/onerror) still present in a few Blade views.
            "script-src-attr 'unsafe-inline'",
            'style-src '.implode(' ', $styleSrc),
            "img-src 'self' data: blob: https:",
            'font-src '.implode(' ', $fontSrc),
            'connect-src '.implode(' ', $connectSrc),
            "worker-src 'self' blob:",
            "media-src 'self' blob:",
            "frame-src 'none'",
        ];

        if (! app()->environment('local')) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_html_responses_include_core_security_headers(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');

        $permissions = (string) $response->headers->get('Permissions-Policy');
        $this->assertStringContainsString('camera=()', $permissions);
        $this->assertStringContainsString('geolocation=()', $permissions);
    }

    public function test_csp_is_enforced_outside_local(): void
    {
        $this->app['env'] = 'production';

        $response = $this->get(route('home'));

        $response->assertOk();

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);
        $this->assertStringContainsString('nonce-', $csp);
        $this->assertStringContainsString("script-src-attr 'unsafe-inline'", $csp);
        $this->assertStringContainsString("'unsafe-eval'", $csp);
        $this->assertStringContainsString('https://fonts.bunny.net', $csp);
    }

    public function test_csp_is_skipped_in_local_for_vite_hmr(): void
    {
        $this->app['env'] = 'local';

        $response = $this->get(route('home'));

        $response->assertOk();
        $this->assertNull($response->headers->get('Content-Security-Policy'));
    }
}

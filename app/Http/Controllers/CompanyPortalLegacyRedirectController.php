<?php

namespace App\Http\Controllers;

use App\Support\PortalHost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyPortalLegacyRedirectController extends Controller
{
    /**
     * Permanent redirect from www /entreprises* to the company portal host.
     */
    public function __invoke(Request $request, ?string $path = null): RedirectResponse
    {
        $path = trim((string) $path, '/');

        $targetPath = match ($path) {
            'demo' => '/demo',
            'trial' => '/trial',
            default => '/',
        };

        $query = $request->query();
        $url = PortalHost::companyUrl($targetPath, is_array($query) ? $query : []);

        return redirect()->away($url, $request->isMethod('GET') ? 301 : 308);
    }
}

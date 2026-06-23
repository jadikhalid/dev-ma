<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LocaleFromIpService
{
    public function suggestLocale(Request $request): string
    {
        $country = $this->resolveCountryCode($request);

        if ($country === null) {
            return 'fr';
        }

        return in_array($country, config('talenma.francophone_countries', []), true) ? 'fr' : 'en';
    }

    private function resolveCountryCode(Request $request): ?string
    {
        $cloudflareCountry = $request->header('CF-IPCountry');

        if (is_string($cloudflareCountry) && $cloudflareCountry !== '' && $cloudflareCountry !== 'XX') {
            return strtoupper($cloudflareCountry);
        }

        $ip = $request->ip();

        if ($ip === null || $this->isPrivateIp($ip)) {
            return null;
        }

        return Cache::remember("geoip:country:{$ip}", now()->addDay(), function () use ($ip) {
            try {
                $response = Http::timeout(2)
                    ->get("http://ip-api.com/json/{$ip}", ['fields' => 'countryCode']);

                if (! $response->successful()) {
                    return null;
                }

                $code = $response->json('countryCode');

                return is_string($code) && $code !== '' ? strtoupper($code) : null;
            } catch (\Throwable) {
                return null;
            }
        });
    }

    private function isPrivateIp(string $ip): bool
    {
        return ! filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}

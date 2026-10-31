<?php

namespace App\Services;

use App\Models\Profile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class TalentCatalogPromptParser
{
    public const MAX_PROMPT_LENGTH = 500;

    public const MAX_KEYWORDS = 3;

    /** @var list<string> */
    public const EXPERIENCE_VALUES = ['all', '0-1', '1-5', '5-10', '10+'];

    public function __construct(
        private ProfessionCatalogService $professionCatalog,
    ) {}

    public function isConfigured(): bool
    {
        return filled(config('services.gemini.api_key'));
    }

    /**
     * @return array{
     *     sector: string,
     *     profession: string,
     *     experience: string,
     *     status: string,
     *     keywords: list<string>,
     *     country: string,
     *     city: string,
     *     summary: string
     * }
     */
    public function parse(string $prompt, ?string $locale = null): array
    {
        $prompt = trim(Str::limit($prompt, self::MAX_PROMPT_LENGTH, ''));

        if ($prompt === '') {
            throw new RuntimeException('empty_prompt');
        }

        if (! $this->isConfigured()) {
            throw new RuntimeException('not_configured');
        }

        $locale = $locale ?? app()->getLocale();
        $catalog = $this->compactCatalog($locale);
        $statusValues = array_keys(Profile::statusOptions());
        $countryOptions = Profile::countryOptions();
        $citiesByCountry = Profile::citiesByCountry();

        $system = <<<'PROMPT'
Tu convertis une recherche RH en filtres catalogue JSON strict.
Réponds UNIQUEMENT avec un objet JSON valide, sans markdown, de la forme:
{"sector":"slug-ou-vide","profession":"slug-ou-vide","experience":"all|0-1|1-5|5-10|10+","status":"all|…","keywords":["…"],"country":"code-ou-vide","city":"ville-catalogue-ou-vide","summary":"phrase courte"}
Règles:
- sector et profession doivent être des slugs du catalogue fourni, ou "".
- Si profession est renseigné, sector doit être le secteur parent de ce métier.
- experience: all si non précisé; 0-1 jeune diplômé / <1 an; 1-5; 5-10; 10+ sinon.
- status: all si non précisé; sinon une des valeurs exactes listées.
- keywords: 0 à 3 libellés courts tirés du prompt (ex. MOA, Laravel) ; [] si aucun.
- country: code pays du catalogue (ma, fr, …) ou "".
- city: libellé exact de cities_by_country pour ce country, ou "". Si country est "", city doit être "".
- summary: une courte phrase (FR ou EN selon la langue du prompt) décrivant les filtres choisis.
- N'invente aucun slug / code / ville hors catalogue. Choisis le métier et le lieu les plus proches sémantiquement.
PROMPT;

        // Slim prompt catalog (no specializations) keeps Gemini latency under PHP's 30s web limit.
        $userPayload = [
            'locale' => $locale,
            'prompt' => $prompt,
            'allowed_experience' => self::EXPERIENCE_VALUES,
            'allowed_status' => array_merge(['all'], $statusValues),
            'allowed_countries' => collect($countryOptions)
                ->map(fn (string $name, string $code) => ['code' => $code, 'name' => $name])
                ->values()
                ->all(),
            'cities_by_country' => $citiesByCountry,
            'catalog' => $this->promptCatalog($catalog),
        ];

        $raw = $this->callGemini($system."\n\n".json_encode($userPayload, JSON_UNESCAPED_UNICODE));

        return $this->normalizeFilters($raw, $catalog, $statusValues);
    }

    /**
     * @return list<array{slug: string, name: string, professions: list<array{slug: string, name: string, specializations: list<string>}>}>
     */
    private function compactCatalog(string $locale): array
    {
        return $this->professionCatalog->sectorsForLocale($locale)
            ->map(fn (array $sector) => [
                'slug' => $sector['slug'],
                'name' => $sector['name'],
                'professions' => collect($sector['professions'])
                    ->map(fn (array $profession) => [
                        'slug' => $profession['slug'],
                        'name' => $profession['name'],
                        'specializations' => array_values(array_slice(
                            collect($profession['specializations'] ?? [])->all(),
                            0,
                            12
                        )),
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * Compact catalog for the LLM prompt only (slugs + names).
     *
     * @param  list<array{slug: string, name: string, professions: list<array{slug: string, name: string, specializations: list<string>}>}>  $catalog
     * @return list<array{slug: string, name: string, professions: list<array{slug: string, name: string}>}>
     */
    private function promptCatalog(array $catalog): array
    {
        return array_map(fn (array $sector) => [
            'slug' => $sector['slug'],
            'name' => $sector['name'],
            'professions' => array_map(fn (array $profession) => [
                'slug' => $profession['slug'],
                'name' => $profession['name'],
            ], $sector['professions']),
        ], $catalog);
    }

    private function callGemini(string $prompt): array
    {
        $apiKey = (string) config('services.gemini.api_key');
        $primary = (string) config('services.gemini.model', 'gemini-3.5-flash-lite');
        $fallbacks = config('services.gemini.fallback_models', []);
        $models = array_values(array_unique(array_filter(array_merge(
            [$primary],
            is_array($fallbacks) ? $fallbacks : []
        ))));

        $endpointTemplate = (string) config(
            'services.gemini.endpoint',
            'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent'
        );
        // Stay under typical PHP web max_execution_time (30s), including a possible fallback.
        $timeout = min(20, max(5, (int) config('services.gemini.timeout', 20)));
        $lastException = null;

        foreach ($models as $index => $model) {
            $url = str_replace('{model}', rawurlencode($model), $endpointTemplate);
            $attemptTimeout = $index === 0 ? $timeout : min(12, $timeout);

            try {
                $response = Http::timeout($attemptTimeout)
                    ->connectTimeout(5)
                    ->acceptJson()
                    ->asJson()
                    ->post($url.'?key='.urlencode($apiKey), [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [['text' => $prompt]],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.1,
                            'maxOutputTokens' => 256,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);
            } catch (ConnectionException $e) {
                $lastException = new RuntimeException('api_unreachable', 0, $e);

                continue;
            }

            if (! $response->successful()) {
                $lastException = new RuntimeException('api_error');

                continue;
            }

            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

            if (! is_string($text) || trim($text) === '') {
                // Some models put JSON in a later part when thought signatures are present.
                $parts = data_get($response->json(), 'candidates.0.content.parts', []);
                if (is_array($parts)) {
                    foreach ($parts as $part) {
                        if (is_array($part) && isset($part['text']) && is_string($part['text']) && trim($part['text']) !== '') {
                            $text = $part['text'];
                            break;
                        }
                    }
                }
            }

            if (! is_string($text) || trim($text) === '') {
                $lastException = new RuntimeException('empty_response');

                continue;
            }

            $decoded = json_decode($this->extractJsonObject($text), true);

            if (! is_array($decoded)) {
                $lastException = new RuntimeException('invalid_json');

                continue;
            }

            return $decoded;
        }

        throw $lastException ?? new RuntimeException('api_error');
    }

    private function extractJsonObject(string $text): string
    {
        $text = trim($text);

        if (str_starts_with($text, '```')) {
            $text = preg_replace('/^```(?:json)?\s*/i', '', $text) ?? $text;
            $text = preg_replace('/\s*```$/', '', $text) ?? $text;
            $text = trim($text);
        }

        if (str_starts_with($text, '{')) {
            return $text;
        }

        if (preg_match('/\{.*\}/s', $text, $matches) === 1) {
            return $matches[0];
        }

        return $text;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @param  list<array{slug: string, name: string, professions: list<array{slug: string, name: string, specializations: list<string>}>}>  $catalog
     * @param  list<string>  $statusValues
     * @return array{
     *     sector: string,
     *     profession: string,
     *     experience: string,
     *     status: string,
     *     keywords: list<string>,
     *     country: string,
     *     city: string,
     *     summary: string
     * }
     */
    private function normalizeFilters(array $raw, array $catalog, array $statusValues): array
    {
        $sectorSlugs = collect($catalog)->pluck('slug')->all();
        $professionBySlug = [];
        $specializationsByProfession = [];

        foreach ($catalog as $sector) {
            foreach ($sector['professions'] as $profession) {
                $professionBySlug[$profession['slug']] = $sector['slug'];
                $specializationsByProfession[$profession['slug']] = $profession['specializations'];
            }
        }

        $profession = trim((string) ($raw['profession'] ?? ''));
        $sector = trim((string) ($raw['sector'] ?? ''));

        if ($profession !== '' && ! array_key_exists($profession, $professionBySlug)) {
            $profession = '';
        }

        if ($profession !== '') {
            $sector = $professionBySlug[$profession];
        } elseif ($sector !== '' && ! in_array($sector, $sectorSlugs, true)) {
            $sector = '';
        }

        $experience = trim((string) ($raw['experience'] ?? 'all'));
        if (! in_array($experience, self::EXPERIENCE_VALUES, true)) {
            $experience = 'all';
        }

        $status = trim((string) ($raw['status'] ?? 'all'));
        if ($status !== 'all' && ! in_array($status, $statusValues, true)) {
            $status = 'all';
        }

        $allowedKeywords = $profession !== ''
            ? ($specializationsByProfession[$profession] ?? [])
            : [];

        $keywords = [];

        if ($allowedKeywords !== []) {
            $matched = [];

            foreach ($raw['keywords'] ?? [] as $item) {
                if (! is_string($item) && ! is_numeric($item)) {
                    continue;
                }

                $keyword = trim(Str::limit((string) $item, 60, ''));
                if ($keyword === '') {
                    continue;
                }

                $needle = mb_strtolower($keyword);

                foreach ($allowedKeywords as $allowed) {
                    $haystack = mb_strtolower($allowed);
                    if ($haystack === $needle || str_contains($haystack, $needle) || str_contains($needle, $haystack)) {
                        $matched[] = $allowed;
                        break;
                    }
                }
            }

            $keywords = array_values(array_slice(array_unique($matched), 0, self::MAX_KEYWORDS));
        }

        $country = trim((string) ($raw['country'] ?? ''));
        if ($country !== '' && ! Profile::isValidCountry($country)) {
            $country = '';
        }

        $city = '';
        if ($country !== '') {
            $city = Profile::resolveCityForCountry($country, trim((string) ($raw['city'] ?? ''))) ?? '';
        }

        $summary = trim(Str::limit((string) ($raw['summary'] ?? ''), 160, ''));

        return [
            'sector' => $sector,
            'profession' => $profession,
            'experience' => $experience,
            'status' => $status,
            'keywords' => $keywords,
            'country' => $country,
            'city' => $city,
            'summary' => $summary,
        ];
    }
}

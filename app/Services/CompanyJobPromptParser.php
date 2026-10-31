<?php

namespace App\Services;

use App\Models\CompanyProfile;
use Illuminate\Support\Str;
use RuntimeException;

class CompanyJobPromptParser
{
    public const MAX_PROMPT_LENGTH = 500;

    public const MAX_KEYWORDS = 5;

    public function __construct(
        private ProfessionCatalogService $professionCatalog,
        private GeminiJsonClient $gemini,
    ) {}

    public function isConfigured(): bool
    {
        return $this->gemini->isConfigured();
    }

    /**
     * @return array{
     *     keywords: list<string>,
     *     author: string,
     *     sector: string,
     *     profession: string,
     *     country: string,
     *     city: string,
     *     day: int|null,
     *     month: int|null,
     *     year: int|null,
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
        $countryOptions = CompanyProfile::countryOptions();
        $citiesByCountry = CompanyProfile::citiesByCountry();

        $system = <<<'PROMPT'
Tu convertis une recherche d'annonces emploi entreprise en filtres JSON strict.
Réponds UNIQUEMENT avec un objet JSON valide, sans markdown, de la forme:
{"keywords":["…"],"author":"","sector":"slug-ou-vide","profession":"slug-ou-vide","country":"code-ou-vide","city":"ville-catalogue-ou-vide","day":null,"month":null,"year":null,"summary":"phrase courte"}
Règles:
- keywords: 0 à 5 libellés courts tirés du prompt (titre, compétences, mots libres) ; [] si aucun.
- author: fragment du nom de l'auteur / créateur de l'annonce, ou "".
- sector et profession: slugs du catalogue fourni, ou "". Si profession est renseigné, sector doit être le secteur parent.
- country: code pays du catalogue (ma, fr, …) ou "".
- city: libellé exact de cities_by_country pour ce country, ou "". Si country est "", city doit être "".
- day: entier 1–31 si un jour est mentionné (ex. "le 10"), sinon null.
- month: entier 1–12 si un mois est mentionné (juin→6, octobre→10), sinon null.
- year: entier 2000–2100 si une année est mentionnée, sinon null.
- summary: courte phrase (FR ou EN selon le prompt) décrivant les filtres.
- N'invente aucun slug / code / ville hors catalogue.
PROMPT;

        $userPayload = [
            'locale' => $locale,
            'prompt' => $prompt,
            'allowed_countries' => collect($countryOptions)
                ->map(fn (string $name, string $code) => ['code' => $code, 'name' => $name])
                ->values()
                ->all(),
            'cities_by_country' => $citiesByCountry,
            'catalog' => $this->promptCatalog($catalog),
        ];

        $raw = $this->gemini->generateJson($system."\n\n".json_encode($userPayload, JSON_UNESCAPED_UNICODE));

        return $this->normalizeFilters($raw, $catalog);
    }

    /**
     * @return list<array{slug: string, name: string, professions: list<array{slug: string, name: string}>}>
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
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  list<array{slug: string, name: string, professions: list<array{slug: string, name: string}>}>  $catalog
     * @return list<array{slug: string, name: string, professions: list<array{slug: string, name: string}>}>
     */
    private function promptCatalog(array $catalog): array
    {
        return $catalog;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @param  list<array{slug: string, name: string, professions: list<array{slug: string, name: string}>}>  $catalog
     * @return array{
     *     keywords: list<string>,
     *     author: string,
     *     sector: string,
     *     profession: string,
     *     country: string,
     *     city: string,
     *     day: int|null,
     *     month: int|null,
     *     year: int|null,
     *     summary: string
     * }
     */
    private function normalizeFilters(array $raw, array $catalog): array
    {
        $sectorSlugs = collect($catalog)->pluck('slug')->all();
        $professionBySlug = [];

        foreach ($catalog as $sector) {
            foreach ($sector['professions'] as $profession) {
                $professionBySlug[$profession['slug']] = $sector['slug'];
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

        $keywords = [];
        foreach ($raw['keywords'] ?? [] as $item) {
            if (! is_string($item) && ! is_numeric($item)) {
                continue;
            }

            $keyword = trim(Str::limit((string) $item, 60, ''));
            if ($keyword === '') {
                continue;
            }

            $keywords[] = $keyword;
        }
        $keywords = array_values(array_slice(array_unique($keywords), 0, self::MAX_KEYWORDS));

        $author = trim(Str::limit((string) ($raw['author'] ?? ''), 80, ''));

        $country = trim((string) ($raw['country'] ?? ''));
        $country = CompanyProfile::normalizeCountryCode($country) ?? '';
        if ($country !== '' && ! CompanyProfile::isValidCountry($country)) {
            $country = '';
        }

        $city = '';
        if ($country !== '') {
            $city = CompanyProfile::normalizeCityForCountry(
                trim((string) ($raw['city'] ?? '')),
                $country
            ) ?? '';
        }

        $day = $this->normalizeBoundedInt($raw['day'] ?? null, 1, 31);
        $month = $this->normalizeBoundedInt($raw['month'] ?? null, 1, 12);
        $year = $this->normalizeBoundedInt($raw['year'] ?? null, 2000, 2100);

        $summary = trim(Str::limit((string) ($raw['summary'] ?? ''), 160, ''));

        return [
            'keywords' => $keywords,
            'author' => $author,
            'sector' => $sector,
            'profession' => $profession,
            'country' => $country,
            'city' => $city,
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'summary' => $summary,
        ];
    }

    private function normalizeBoundedInt(mixed $value, int $min, int $max): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        $int = (int) $value;

        return ($int >= $min && $int <= $max) ? $int : null;
    }
}

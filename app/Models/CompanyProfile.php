<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'logo_path',
    'representative_name',
    'phone',
    'linkedin_url',
    'sector',
    'registration_sector',
    'profession_sector_id',
    'country',
    'city',
    'description',
    'website',
    'employee_count',
    'hiring_needs',
    'registration_hiring_needs',
])]
class CompanyProfile extends Model
{
    use HasFactory;

    /** ISO 3166-1 alpha-2 (lowercase): EU + Canada + United States + Morocco + Gulf (GCC). */
    public const COUNTRY_CODES = [
        'at', 'be', 'bg', 'hr', 'cy', 'cz', 'dk', 'ee', 'fi', 'fr',
        'de', 'gr', 'hu', 'ie', 'it', 'lv', 'lt', 'lu', 'mt', 'nl',
        'pl', 'pt', 'ro', 'sk', 'si', 'es', 'se',
        'ca', 'us',
        'ma',
        'ae', 'bh', 'kw', 'om', 'qa', 'sa',
    ];

    public const DEFAULT_COUNTRY = 'fr';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function professionSector(): BelongsTo
    {
        return $this->belongsTo(ProfessionSector::class);
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyProfileDocument::class)->orderBy('sort_order');
    }

    public function memberships(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyMembership::class);
    }

    public function jobPostings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JobPosting::class);
    }

    /**
     * @return array<string, string> code => localized label (sorted by label)
     */
    public static function countryOptions(): array
    {
        $options = [];

        foreach (self::COUNTRY_CODES as $code) {
            $options[$code] = __('talenma.company.countries.'.$code);
        }

        asort($options, SORT_NATURAL | SORT_FLAG_CASE);

        return $options;
    }

    public static function isValidCountry(?string $code): bool
    {
        return filled($code) && in_array(strtolower($code), self::COUNTRY_CODES, true);
    }

    public static function countryLabelFor(?string $code): ?string
    {
        if (! filled($code)) {
            return null;
        }

        $normalized = strtolower($code);

        if (! self::isValidCountry($normalized)) {
            return $code;
        }

        return __('talenma.company.countries.'.$normalized);
    }

    /**
     * Map legacy free-text country values to ISO codes.
     */
    public static function normalizeCountryCode(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $trimmed = trim($value);
        $lower = mb_strtolower($trimmed);

        if (self::isValidCountry($lower)) {
            return $lower;
        }

        $aliases = [
            'france' => 'fr',
            'belgique' => 'be',
            'belgium' => 'be',
            'espagne' => 'es',
            'spain' => 'es',
            'italie' => 'it',
            'italy' => 'it',
            'allemagne' => 'de',
            'germany' => 'de',
            'deutschland' => 'de',
            'pays-bas' => 'nl',
            'netherlands' => 'nl',
            'hollande' => 'nl',
            'portugal' => 'pt',
            'autriche' => 'at',
            'austria' => 'at',
            'irlande' => 'ie',
            'ireland' => 'ie',
            'pologne' => 'pl',
            'poland' => 'pl',
            'suède' => 'se',
            'sweden' => 'se',
            'danemark' => 'dk',
            'denmark' => 'dk',
            'finlande' => 'fi',
            'finland' => 'fi',
            'grèce' => 'gr',
            'greece' => 'gr',
            'roumanie' => 'ro',
            'romania' => 'ro',
            'hongrie' => 'hu',
            'hungary' => 'hu',
            'tchéquie' => 'cz',
            'république tchèque' => 'cz',
            'czech republic' => 'cz',
            'slovakia' => 'sk',
            'slovaquie' => 'sk',
            'slovénie' => 'si',
            'slovenia' => 'si',
            'croatie' => 'hr',
            'croatia' => 'hr',
            'bulgarie' => 'bg',
            'bulgaria' => 'bg',
            'lituanie' => 'lt',
            'lithuania' => 'lt',
            'lettonie' => 'lv',
            'latvia' => 'lv',
            'estonie' => 'ee',
            'estonia' => 'ee',
            'luxembourg' => 'lu',
            'malte' => 'mt',
            'malta' => 'mt',
            'chypre' => 'cy',
            'cyprus' => 'cy',
            'canada' => 'ca',
            'maroc' => 'ma',
            'morocco' => 'ma',
            'émirats arabes unis' => 'ae',
            'emirats arabes unis' => 'ae',
            'united arab emirates' => 'ae',
            'uae' => 'ae',
            'bahreïn' => 'bh',
            'bahrain' => 'bh',
            'koweït' => 'kw',
            'kuwait' => 'kw',
            'oman' => 'om',
            'qatar' => 'qa',
            'arabie saoudite' => 'sa',
            'saudi arabia' => 'sa',
            'états-unis' => 'us',
            'etats-unis' => 'us',
            'united states' => 'us',
            'united states of america' => 'us',
            'usa' => 'us',
            'u.s.' => 'us',
            'u.s.a.' => 'us',
        ];

        return $aliases[$lower] ?? null;
    }

    public function countryLabel(): ?string
    {
        return self::countryLabelFor($this->country);
    }

    /**
     * @return array<string, list<string>>
     */
    public static function citiesByCountry(): array
    {
        return [
            'at' => ['Vienne', 'Graz', 'Linz', 'Salzbourg', 'Innsbruck'],
            'be' => ['Bruxelles', 'Anvers', 'Gand', 'Liège', 'Charleroi'],
            'bg' => ['Sofia', 'Plovdiv', 'Varna', 'Burgas', 'Ruse'],
            'hr' => ['Zagreb', 'Split', 'Rijeka', 'Osijek', 'Zadar'],
            'cy' => ['Nicosie', 'Limassol', 'Larnaca', 'Paphos', 'Famagouste'],
            'cz' => ['Prague', 'Brno', 'Ostrava', 'Plzen', 'Liberec'],
            'dk' => ['Copenhague', 'Aarhus', 'Odense', 'Aalborg', 'Esbjerg'],
            'ee' => ['Tallinn', 'Tartu', 'Narva', 'Parnu', 'Kohtla-Jarve'],
            'fi' => ['Helsinki', 'Espoo', 'Tampere', 'Vantaa', 'Oulu'],
            'fr' => ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Lille'],
            'de' => ['Berlin', 'Munich', 'Hambourg', 'Francfort', 'Cologne'],
            'gr' => ['Athènes', 'Thessalonique', 'Patras', 'Héraklion', 'Larissa'],
            'hu' => ['Budapest', 'Debrecen', 'Szeged', 'Miskolc', 'Pecs'],
            'ie' => ['Dublin', 'Cork', 'Limerick', 'Galway', 'Waterford'],
            'it' => ['Rome', 'Milan', 'Naples', 'Turin', 'Florence'],
            'lv' => ['Riga', 'Daugavpils', 'Liepaja', 'Jelgava', 'Jurmala'],
            'lt' => ['Vilnius', 'Kaunas', 'Klaipeda', 'Siauliai', 'Panevezys'],
            'lu' => ['Luxembourg', 'Esch-sur-Alzette', 'Differdange', 'Dudelange', 'Ettelbruck'],
            'mt' => ['La Valette', 'Birkirkara', 'Sliema', 'Mosta', 'Qormi'],
            'nl' => ['Amsterdam', 'Rotterdam', 'La Haye', 'Utrecht', 'Eindhoven'],
            'pl' => ['Varsovie', 'Cracovie', 'Lodz', 'Wroclaw', 'Poznan'],
            'pt' => ['Lisbonne', 'Porto', 'Braga', 'Coimbra', 'Faro'],
            'ro' => ['Bucarest', 'Cluj-Napoca', 'Timisoara', 'Iasi', 'Constanta'],
            'sk' => ['Bratislava', 'Kosice', 'Presov', 'Zilina', 'Nitra'],
            'si' => ['Ljubljana', 'Maribor', 'Celje', 'Kranj', 'Velenje'],
            'es' => ['Madrid', 'Barcelone', 'Valence', 'Séville', 'Bilbao'],
            'se' => ['Stockholm', 'Goteborg', 'Malmo', 'Uppsala', 'Vasteras'],
            'ca' => ['Toronto', 'Montréal', 'Vancouver', 'Calgary', 'Ottawa'],
            'us' => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Miami'],
            'ma' => ['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Agadir'],
            'ae' => ['Dubaï', 'Abu Dhabi', 'Charjah', 'Ajman', 'Ras el Khaïmah'],
            'bh' => ['Manama', 'Riffa', 'Muharraq', 'Hamad Town', 'Aali'],
            'kw' => ['Koweït', 'Hawalli', 'Salmiya', 'Al Farwaniyah', 'Al Jahra'],
            'om' => ['Mascate', 'Salalah', 'Sohar', 'Nizwa', 'Sur'],
            'qa' => ['Doha', 'Al Rayyan', 'Al Wakrah', 'Umm Salal', 'Al Khor'],
            'sa' => ['Riyad', 'Djeddah', 'La Mecque', 'Médine', 'Dammam'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function citiesForCountry(?string $country): array
    {
        if (! filled($country)) {
            return [];
        }

        return self::citiesByCountry()[strtolower($country)] ?? [];
    }

    public static function isValidCityForCountry(?string $city, ?string $country): bool
    {
        if (! filled($city) || ! filled($country)) {
            return false;
        }

        return in_array($city, self::citiesForCountry($country), true);
    }

    /**
     * Map a free-text city to the canonical list for a country when possible.
     */
    public static function normalizeCityForCountry(?string $city, ?string $country): ?string
    {
        if (! filled($city) || ! filled($country)) {
            return null;
        }

        $allowed = self::citiesForCountry($country);

        if ($allowed === []) {
            return null;
        }

        if (in_array($city, $allowed, true)) {
            return $city;
        }

        $lower = mb_strtolower(trim($city));

        foreach ($allowed as $canonical) {
            if (mb_strtolower($canonical) === $lower) {
                return $canonical;
            }
        }

        $aliases = [
            'barcelona' => 'Barcelone',
            'seville' => 'Séville',
            'sevilla' => 'Séville',
            'valencia' => 'Valence',
            'brussels' => 'Bruxelles',
            'bruxelles' => 'Bruxelles',
            'antwerp' => 'Anvers',
            'ghent' => 'Gand',
            'liege' => 'Liège',
            'munich' => 'Munich',
            'hamburg' => 'Hambourg',
            'frankfurt' => 'Francfort',
            'cologne' => 'Cologne',
            'koln' => 'Cologne',
            'köln' => 'Cologne',
            'vienna' => 'Vienne',
            'salzburg' => 'Salzbourg',
            'athens' => 'Athènes',
            'thessaloniki' => 'Thessalonique',
            'heraklion' => 'Héraklion',
            'rome' => 'Rome',
            'roma' => 'Rome',
            'milan' => 'Milan',
            'milano' => 'Milan',
            'naples' => 'Naples',
            'napoli' => 'Naples',
            'turin' => 'Turin',
            'torino' => 'Turin',
            'florence' => 'Florence',
            'firenze' => 'Florence',
            'amsterdam' => 'Amsterdam',
            'the hague' => 'La Haye',
            'den haag' => 'La Haye',
            'lisbon' => 'Lisbonne',
            'lisboa' => 'Lisbonne',
            'warsaw' => 'Varsovie',
            'krakow' => 'Cracovie',
            'cracow' => 'Cracovie',
            'bucharest' => 'Bucarest',
            'copenhagen' => 'Copenhague',
            'gothenburg' => 'Goteborg',
            'göteborg' => 'Goteborg',
            'malmö' => 'Malmo',
            'montreal' => 'Montréal',
            'dubai' => 'Dubaï',
            'abu dhabi' => 'Abu Dhabi',
            'sharjah' => 'Charjah',
            'muscat' => 'Mascate',
            'riyadh' => 'Riyad',
            'jeddah' => 'Djeddah',
            'mecca' => 'La Mecque',
            'medina' => 'Médine',
            'kuwait city' => 'Koweït',
            'valletta' => 'La Valette',
            'nantes' => 'Paris', // won't match allowed unless fr - skip wrong maps
        ];

        // Drop the bad nantes alias - nantes is not in fr top 5
        unset($aliases['nantes']);

        $mapped = $aliases[$lower] ?? null;

        if ($mapped !== null && in_array($mapped, $allowed, true)) {
            return $mapped;
        }

        return null;
    }

    public function logoUrl(): ?string
    {
        $this->loadMissing('user');

        $avatarUrl = $this->user?->avatarUrl();

        if ($avatarUrl) {
            return $avatarUrl;
        }

        if (! $this->logo_path) {
            return null;
        }

        // Legacy company_profiles.logo_path — kept as fallback until re-uploaded via /profile.
        return '/storage/'.ltrim($this->logo_path, '/');
    }

    public function displayName(): string
    {
        $this->loadMissing('user');

        return trim((string) ($this->user?->name ?: ''));
    }

    public function initials(): string
    {
        $name = $this->displayName();

        if ($name === '') {
            return '—';
        }

        $parts = preg_split('/\s+/u', $name) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials !== '' ? $initials : mb_strtoupper(mb_substr($name, 0, 2));
    }
}

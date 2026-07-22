<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\ProfessionSector;
use App\Models\User;
use App\Services\ProfessionCatalogService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Entreprises fictives pour tests de recherche talent → entreprise.
 * Couvre chaque secteur actif + mots-clés entreprise (pas les spécialisations talent).
 *
 * Usage : php artisan db:seed --class=DevCompanySeeder
 *
 * Identifiants : *@dev.companies.talentsdumaroc.test / password
 */
class DevCompanySeeder extends Seeder
{
    private const EMAIL_DOMAIN = 'dev.companies.talentsdumaroc.test';

    private const PASSWORD = 'password';

    /** @var list<string> */
    private array $countries = ['fr', 'be', 'de', 'es', 'it', 'ca', 'us', 'ma', 'ae', 'sa'];

    /** @var list<string> */
    private array $companyPrefixes = [
        'Atlas', 'Maghreb', 'Sahara', 'Médina', 'Horizon', 'Nova', 'Prime',
        'Delta', 'Orion', 'Vertex', 'Apex', 'Nexus', 'Pulse', 'Forge',
    ];

    /** @var list<string> */
    private array $companySuffixes = [
        'Group', 'Solutions', 'Partners', 'Labs', 'Services', 'Consulting',
        'Industries', 'Tech', 'Holdings', 'Studio',
    ];

    public function run(): void
    {
        $this->call(ProfessionSeeder::class);

        User::query()
            ->where('email', 'like', '%@'.self::EMAIL_DOMAIN)
            ->each(function (User $user) {
                $user->companyProfile?->documents()->delete();
                $user->companyProfile?->delete();
                $user->delete();
            });

        $catalog = app(ProfessionCatalogService::class);
        $companyKeywordsBySector = $catalog->companyKeywordsCatalog();

        $sectors = ProfessionSector::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $created = 0;
        $index = 0;

        foreach ($sectors as $sector) {
            $keywordRows = $companyKeywordsBySector[$sector->slug] ?? [];
            $labelsFr = array_values(array_filter(array_map(
                fn (array $row) => $row['label_fr'] ?? null,
                $keywordRows,
            )));

            if ($labelsFr === []) {
                $labelsFr = [$sector->name_fr];
            }

            // Une entreprise par mot-clé entreprise du secteur.
            foreach ($labelsFr as $keywordIndex => $primaryKeyword) {
                $set = [$primaryKeyword];
                foreach ($labelsFr as $offset => $label) {
                    if ($label === $primaryKeyword) {
                        continue;
                    }
                    $set[] = $label;
                    if (count($set) >= 3) {
                        break;
                    }
                }

                $this->createCompany(
                    index: ++$index,
                    sector: $sector,
                    focus: $primaryKeyword,
                    hiringNeeds: sprintf(
                        'Entreprise du secteur « %s ». Profil : %s. Besoins RH / activité : %s.',
                        $sector->name_fr,
                        $primaryKeyword,
                        implode(', ', $set)
                    ),
                    keywords: $set,
                );

                $created++;
            }

            // Entreprise « secteur » transversale.
            $cross = array_slice($labelsFr, 0, min(3, count($labelsFr)));

            $this->createCompany(
                index: ++$index,
                sector: $sector,
                focus: $sector->name_fr,
                hiringNeeds: sprintf(
                    'Groupe multi-activités en %s. Priorités : %s.',
                    $sector->name_fr,
                    implode(', ', $cross)
                ),
                keywords: $cross,
            );

            $created++;
        }

        $this->command?->info(
            "DevCompanySeeder : {$created} entreprises créées (@".self::EMAIL_DOMAIN.', mot de passe : '.self::PASSWORD.').'
        );
    }

    /**
     * @param  list<string>  $keywords
     */
    private function createCompany(
        int $index,
        ProfessionSector $sector,
        string $focus,
        string $hiringNeeds,
        array $keywords,
    ): void {
        $country = $this->countries[($index - 1) % count($this->countries)];
        $cities = CompanyProfile::citiesForCountry($country);
        $city = $cities[($index - 1) % max(count($cities), 1)] ?? 'Paris';

        $prefix = $this->companyPrefixes[($index - 1) % count($this->companyPrefixes)];
        $suffix = $this->companySuffixes[($index - 1) % count($this->companySuffixes)];
        $companyName = trim($prefix.' '.$suffix.' '.$index);

        $slug = Str::slug($sector->slug.'-'.$index);
        $firstName = ['Karim', 'Sophie', 'Yasmine', 'Thomas', 'Amine', 'Claire'][($index - 1) % 6];
        $lastName = ['Benali', 'Martin', 'El Amrani', 'Dubois', 'Tazi', 'Moreau'][($index - 1) % 6];

        $user = User::create([
            'name' => $companyName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $slug.'@'.self::EMAIL_DOMAIN,
            'password' => Hash::make(self::PASSWORD),
            'role' => 'company',
            'email_verified_at' => now(),
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
            'is_subscribed' => false,
            'subscription_expires_at' => null,
        ]);

        CompanyProfile::create([
            'user_id' => $user->id,
            'representative_name' => trim($firstName.' '.$lastName),
            'representative_email' => 'contact-'.$slug.'@'.self::EMAIL_DOMAIN,
            'sector' => $sector->name_fr,
            'registration_sector' => $sector->name_fr,
            'profession_sector_id' => $sector->id,
            'country' => $country,
            'city' => $city,
            'description' => sprintf(
                '%s — démonstration « %s ». Focus : %s. Mots-clés entreprise : %s.',
                $companyName,
                $sector->name_fr,
                $focus,
                implode(', ', $keywords)
            ),
            'hiring_needs' => $hiringNeeds,
            'registration_hiring_needs' => $hiringNeeds,
            'website' => 'https://example.com/'.$slug,
            'employee_count' => (string) (([10, 50, 120, 300, 800])[($index - 1) % 5]),
        ]);
    }
}

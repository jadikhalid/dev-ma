<?php

namespace Database\Seeders;

use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Talents fictifs pour le développement / tests de recherche.
 * Couvre chaque secteur, chaque métier et les mots-clés du catalogue.
 *
 * Usage : php artisan db:seed --class=DevTalentSeeder
 */
class DevTalentSeeder extends Seeder
{
    private const EMAIL_DOMAIN = 'dev.talentsdumaroc.test';

    private const PASSWORD = 'password';

    /** @var list<string> */
    private array $cities = ['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Agadir'];

    /** @var list<string> */
    private array $availabilities = ['disponible', 'sous 2 semaines', 'mission en cours'];

    public function run(): void
    {
        $this->call(ProfessionSeeder::class);

        // Idempotent : on remplace les talents de démo précédents.
        User::query()
            ->where('email', 'like', '%@'.self::EMAIL_DOMAIN)
            ->each(function (User $user) {
                $user->profile?->delete();
                $user->delete();
            });

        $created = 0;
        $index = 0;

        $sectors = ProfessionSector::query()
            ->where('is_active', true)
            ->with(['professions' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')
                ->with(['suggestions' => fn ($sq) => $sq->where('is_active', true)->orderBy('sort_order')])])
            ->orderBy('sort_order')
            ->get();

        foreach ($sectors as $sector) {
            foreach ($sector->professions as $profession) {
                $labels = $profession->suggestions
                    ->map(fn ($suggestion) => $suggestion->label_fr)
                    ->filter()
                    ->values()
                    ->all();

                if ($labels === []) {
                    $labels = [$profession->name_fr];
                }

                // Un talent par suggestion (couverture des mots-clés).
                foreach ($profession->suggestions as $suggestionIndex => $suggestion) {
                    $specializationLabels = $this->pickSpecializationSet($labels, $suggestionIndex, $suggestion->label_fr);
                    $skills = $this->skillsFromSuggestion($suggestion->keywords, $suggestion->label_fr);

                    $this->createTalent(
                        index: ++$index,
                        sector: $sector,
                        profession: $profession,
                        title: $suggestion->label_fr,
                        specialization: implode(', ', $specializationLabels),
                        skills: $skills,
                    );

                    $created++;
                }

                // Talent « métier » supplémentaire avec 3+ mots-clés du métier.
                $bundle = array_slice($labels, 0, max(3, min(5, count($labels))));
                if (count($bundle) < 3 && count($labels) > 0) {
                    while (count($bundle) < 3) {
                        $bundle[] = $labels[count($bundle) % count($labels)];
                    }
                    $bundle = array_values(array_unique($bundle));
                    while (count($bundle) < 3) {
                        $bundle[] = $profession->name_fr.' '.count($bundle);
                    }
                }

                $this->createTalent(
                    index: ++$index,
                    sector: $sector,
                    profession: $profession,
                    title: $profession->name_fr,
                    specialization: implode(', ', array_slice($bundle, 0, max(3, count($bundle)))),
                    skills: array_values(array_unique(array_merge(
                        array_slice($bundle, 0, 3),
                        [$sector->name_fr, $profession->name_fr],
                    ))),
                );

                $created++;
            }
        }

        $this->command?->info("DevTalentSeeder : {$created} talents créés (@".self::EMAIL_DOMAIN.", mot de passe : ".self::PASSWORD.').');
    }

    /**
     * @param  list<string>  $labels
     * @return list<string>
     */
    private function pickSpecializationSet(array $labels, int $suggestionIndex, string $primary): array
    {
        $set = [$primary];

        foreach ($labels as $offset => $label) {
            if ($label === $primary) {
                continue;
            }

            $set[] = $label;

            if (count($set) >= 3) {
                break;
            }
        }

        // Garantir 3 entrées même si le métier a peu de suggestions.
        $i = 0;
        while (count($set) < 3) {
            $set[] = $labels[$i % count($labels)].($i > 0 ? ' #'.($i + 1) : '');
            $i++;
            if ($i > 10) {
                break;
            }
        }

        return array_values(array_unique($set));
    }

    /**
     * @return list<string>
     */
    private function skillsFromSuggestion(?string $keywords, string $label): array
    {
        $parts = preg_split('/[\s,]+/u', trim((string) $keywords)) ?: [];
        $parts = array_values(array_filter(array_map('trim', $parts)));

        return array_values(array_unique(array_filter(array_merge(
            [$label],
            $parts,
        ))));
    }

    /**
     * @param  list<string>  $skills
     */
    private function createTalent(
        int $index,
        ProfessionSector $sector,
        Profession $profession,
        string $title,
        string $specialization,
        array $skills,
    ): void {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $slug = Str::slug($sector->slug.'-'.$profession->slug.'-'.$index);

        $user = User::create([
            'name' => trim($firstName.' '.$lastName),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $slug.'@'.self::EMAIL_DOMAIN,
            'password' => Hash::make(self::PASSWORD),
            'role' => 'dev',
            'email_verified_at' => now(),
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
            'is_subscribed' => true,
            'subscription_expires_at' => now()->addYear(),
        ]);

        Profile::create([
            'user_id' => $user->id,
            'profession_sector_id' => $sector->id,
            'profession_id' => $profession->id,
            'specialization' => $specialization,
            'registration_description' => 'Profil de démonstration pour tests — '.$sector->name_fr.' / '.$profession->name_fr.'.',
            'title' => $title,
            'bio' => sprintf(
                'Talent marocain spécialisé en %s (%s). Profil généré pour le développement et les tests de recherche sur Talents du Maroc. Compétences : %s.',
                $title,
                $profession->name_fr,
                $specialization,
            ),
            'experience_years' => fake()->numberBetween(2, 12),
            'daily_rate_eur' => fake()->numberBetween(200, 550),
            'availability' => fake()->randomElement($this->availabilities),
            'work_modes' => ['full_remote', 'hybrid'],
            'languages' => ['Français', 'Anglais', 'Arabe'],
            'city' => fake()->randomElement($this->cities),
            'country' => 'Maroc',
            'skills' => array_slice($skills, 0, 6),
            'linkedin_url' => 'https://www.linkedin.com/in/example-'.$slug,
        ]);
    }
}

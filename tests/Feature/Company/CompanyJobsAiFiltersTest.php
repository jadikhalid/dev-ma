<?php

namespace Tests\Feature\Company;

use App\Models\CompanyProfile;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CompanyJobsAiFiltersTest extends TestCase
{
    use RefreshDatabase;

    private ProfessionSector $sector;

    private Profession $profession;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.gemini.api_key' => 'test-gemini-key',
            'services.gemini.model' => 'gemini-3.5-flash-lite',
            'services.gemini.fallback_models' => [],
            'services.gemini.endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent',
        ]);

        $this->sector = ProfessionSector::query()->create([
            'slug' => 'it-digital',
            'name_fr' => 'Technologies de l\'information',
            'name_en' => 'Information technology',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->profession = Profession::query()->create([
            'profession_sector_id' => $this->sector->id,
            'slug' => 'full-stack-developer',
            'name_fr' => 'Développeur Full-Stack',
            'name_en' => 'Full-Stack Developer',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_company_gets_validated_job_filters_from_gemini(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'keywords' => ['Laravel', 'Backend'],
                                        'author' => 'Khalid',
                                        'sector' => 'it-digital',
                                        'profession' => 'full-stack-developer',
                                        'country' => 'fr',
                                        'city' => 'Paris',
                                        'day' => 10,
                                        'month' => 6,
                                        'year' => 2026,
                                        'summary' => 'Laravel par Khalid, Paris, 10 juin 2026',
                                    ], JSON_UNESCAPED_UNICODE),
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $company = $this->companyWithJobsAccess();

        $this->actingAs($company)
            ->postJson(route('company.jobs.ai-filters'), [
                'prompt' => 'Annonces Laravel de Khalid le 10 juin 2026 à Paris',
            ])
            ->assertOk()
            ->assertJsonPath('filters.keywords', ['Laravel', 'Backend'])
            ->assertJsonPath('filters.author', 'Khalid')
            ->assertJsonPath('filters.sector', 'it-digital')
            ->assertJsonPath('filters.profession', 'full-stack-developer')
            ->assertJsonPath('filters.country', 'fr')
            ->assertJsonPath('filters.city', 'Paris')
            ->assertJsonPath('filters.day', 10)
            ->assertJsonPath('filters.month', 6)
            ->assertJsonPath('filters.year', 2026)
            ->assertJsonPath('filters.summary', 'Laravel par Khalid, Paris, 10 juin 2026');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generativelanguage.googleapis.com')
                && str_contains($request->url(), 'key=test-gemini-key');
        });
    }

    public function test_invalid_job_catalog_values_are_stripped(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'keywords' => ['Laravel'],
                                        'author' => 'Sara',
                                        'sector' => 'fake-sector',
                                        'profession' => 'not-a-job',
                                        'country' => 'xx',
                                        'city' => 'Atlantis',
                                        'day' => 99,
                                        'month' => 15,
                                        'year' => 1900,
                                        'summary' => 'Hors catalogue',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $company = $this->companyWithJobsAccess();

        $this->actingAs($company)
            ->postJson(route('company.jobs.ai-filters'), [
                'prompt' => 'Recherche inventée',
            ])
            ->assertOk()
            ->assertJsonPath('filters.keywords', ['Laravel'])
            ->assertJsonPath('filters.author', 'Sara')
            ->assertJsonPath('filters.sector', '')
            ->assertJsonPath('filters.profession', '')
            ->assertJsonPath('filters.country', '')
            ->assertJsonPath('filters.city', '')
            ->assertJsonPath('filters.day', null)
            ->assertJsonPath('filters.month', null)
            ->assertJsonPath('filters.year', null);
    }

    public function test_profession_forces_parent_sector_for_jobs(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'keywords' => [],
                                        'author' => '',
                                        'sector' => 'wrong-sector',
                                        'profession' => 'full-stack-developer',
                                        'country' => '',
                                        'city' => '',
                                        'day' => null,
                                        'month' => null,
                                        'year' => null,
                                        'summary' => 'Full-stack',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $company = $this->companyWithJobsAccess();

        $this->actingAs($company)
            ->postJson(route('company.jobs.ai-filters'), [
                'prompt' => 'Développeur full-stack',
            ])
            ->assertOk()
            ->assertJsonPath('filters.sector', 'it-digital')
            ->assertJsonPath('filters.profession', 'full-stack-developer');
    }

    public function test_missing_gemini_key_returns_service_unavailable_for_jobs(): void
    {
        config(['services.gemini.api_key' => null]);

        $company = $this->companyWithJobsAccess();

        $this->actingAs($company)
            ->postJson(route('company.jobs.ai-filters'), [
                'prompt' => 'Laravel juin',
            ])
            ->assertStatus(503)
            ->assertJsonPath('code', 'not_configured');
    }

    private function companyWithJobsAccess(): User
    {
        $company = User::factory()->companyOwner()->create([
            'email_verified_at' => now(),
        ]);
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        return $company->fresh(['companyProfile']);
    }
}

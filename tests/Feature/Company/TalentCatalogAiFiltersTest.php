<?php

namespace Tests\Feature\Company;

use App\Models\CompanyProfile;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\ProfessionSuggestion;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TalentCatalogAiFiltersTest extends TestCase
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

        ProfessionSuggestion::query()->create([
            'profession_id' => $this->profession->id,
            'label_fr' => 'PHP / Laravel',
            'label_en' => 'PHP / Laravel',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ProfessionSuggestion::query()->create([
            'profession_id' => $this->profession->id,
            'label_fr' => 'Backend',
            'label_en' => 'Backend',
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    public function test_company_with_plan_gets_validated_filters_from_gemini(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'sector' => 'it-digital',
                                        'profession' => 'full-stack-developer',
                                        'experience' => '5-10',
                                        'status' => Profile::STATUS_AVAILABLE,
                                        'keywords' => ['PHP / Laravel', 'InventedSkill', 'Backend'],
                                        'summary' => 'Full-stack Laravel disponible, 5-10 ans',
                                    ], JSON_UNESCAPED_UNICODE),
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $company = $this->companyWithAccess();

        $response = $this->actingAs($company)->postJson(route('company.search.ai-filters'), [
            'prompt' => 'Développeur Laravel senior disponible, 5 à 10 ans',
        ]);

        $response->assertOk()
            ->assertJsonPath('filters.sector', 'it-digital')
            ->assertJsonPath('filters.profession', 'full-stack-developer')
            ->assertJsonPath('filters.experience', '5-10')
            ->assertJsonPath('filters.status', Profile::STATUS_AVAILABLE)
            ->assertJsonPath('filters.keywords', ['PHP / Laravel', 'Backend'])
            ->assertJsonPath('filters.summary', 'Full-stack Laravel disponible, 5-10 ans');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generativelanguage.googleapis.com')
                && str_contains($request->url(), 'gemini-3.5-flash-lite')
                && str_contains($request->url(), 'key=test-gemini-key');
        });
    }

    public function test_invalid_catalog_values_are_stripped(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'sector' => 'fake-sector',
                                        'profession' => 'not-a-real-job',
                                        'experience' => '99-years',
                                        'status' => 'vacation',
                                        'keywords' => ['Random'],
                                        'summary' => 'Hors catalogue',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $company = $this->companyWithAccess();

        $this->actingAs($company)
            ->postJson(route('company.search.ai-filters'), [
                'prompt' => 'Profil inventé hors catalogue',
            ])
            ->assertOk()
            ->assertJsonPath('filters.sector', '')
            ->assertJsonPath('filters.profession', '')
            ->assertJsonPath('filters.experience', 'all')
            ->assertJsonPath('filters.status', 'all')
            ->assertJsonPath('filters.keywords', []);
    }

    public function test_profession_forces_parent_sector(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'sector' => 'wrong-sector',
                                        'profession' => 'full-stack-developer',
                                        'experience' => '1-5',
                                        'status' => 'all',
                                        'keywords' => [],
                                        'summary' => 'Dev full-stack',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $company = $this->companyWithAccess();

        $this->actingAs($company)
            ->postJson(route('company.search.ai-filters'), [
                'prompt' => 'Full-stack developer mid-level',
            ])
            ->assertOk()
            ->assertJsonPath('filters.sector', 'it-digital')
            ->assertJsonPath('filters.profession', 'full-stack-developer');
    }

    public function test_guest_and_talent_cannot_parse_ai_filters(): void
    {
        $this->postJson(route('company.search.ai-filters'), [
            'prompt' => 'Laravel senior',
        ])->assertUnauthorized();

        $talent = User::factory()->talent()->create([
            'email_verified_at' => now(),
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);

        $this->actingAs($talent)
            ->postJson(route('company.search.ai-filters'), [
                'prompt' => 'Laravel senior',
            ])
            ->assertForbidden();
    }

    public function test_company_without_plan_cannot_parse_ai_filters(): void
    {
        $company = User::factory()->companyOwner()->create([
            'email_verified_at' => now(),
        ]);
        CompanyProfile::factory()->expired()->create(['user_id' => $company->id]);

        $this->actingAs($company)
            ->postJson(route('company.search.ai-filters'), [
                'prompt' => 'Laravel senior',
            ])
            ->assertForbidden();
    }

    public function test_missing_gemini_key_returns_service_unavailable(): void
    {
        config(['services.gemini.api_key' => null]);

        $company = $this->companyWithAccess();

        $this->actingAs($company)
            ->postJson(route('company.search.ai-filters'), [
                'prompt' => 'Laravel senior',
            ])
            ->assertStatus(503)
            ->assertJsonPath('code', 'not_configured');
    }

    public function test_gemini_api_failure_returns_service_unavailable(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => 'quota'], 429),
        ]);

        $company = $this->companyWithAccess();

        $this->actingAs($company)
            ->postJson(route('company.search.ai-filters'), [
                'prompt' => 'Laravel senior',
            ])
            ->assertStatus(503)
            ->assertJsonPath('code', 'api_error');
    }

    private function companyWithAccess(): User
    {
        $company = User::factory()->companyOwner()->create([
            'email_verified_at' => now(),
        ]);
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        return $company->fresh(['companyProfile']);
    }
}

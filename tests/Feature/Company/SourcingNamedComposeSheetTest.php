<?php

namespace Tests\Feature\Company;

use App\Models\CompanyProfile;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\ProfileDocument;
use App\Models\RecruitmentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SourcingNamedComposeSheetTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_named_compose_button_for_pro_company(): void
    {
        $company = $this->makeProCompany();

        $this->actingAs($company)
            ->get(route('sourcing.index'))
            ->assertOk()
            ->assertSee('openNamedCreate()', false)
            ->assertSee('canComposeNamed: true', false)
            ->assertSee('sourcing-named-compose-sheet', false);
    }

    public function test_index_disables_named_compose_for_trial_company(): void
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        $this->actingAs($company->fresh())
            ->get(route('sourcing.index'))
            ->assertOk()
            ->assertSee('canComposeNamed: false', false)
            ->assertSee(__('talenma.company_offer.sourcing_not_included'), false);
    }

    public function test_talent_suggestions_return_eligible_talents_by_name(): void
    {
        $company = $this->makeProCompany();
        $eligible = $this->makeListableTalent('Sara Eligible');
        $this->makeListableTalent('Other Person');

        $this->actingAs($company)
            ->getJson(route('sourcing.talent-suggestions', ['q' => 'Sara']))
            ->assertOk()
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.id', $eligible->id);
    }

    public function test_named_store_embed_returns_card_html(): void
    {
        $company = $this->makeProCompany();
        $talent = $this->makeListableTalent('Sara Eligible');

        $this->actingAs($company)
            ->postJson(route('recruitment.store'), [
                'developer_user_id' => $talent->id,
                'role_title' => 'Intermédiation — Sara Eligible',
                'need' => str_repeat('Contexte détaillé pour une intermédiation ciblée. ', 3),
                'embed' => 1,
            ])
            ->assertOk()
            ->assertJsonPath('mode', RecruitmentRequest::MODE_NAMED)
            ->assertJsonPath('stay', true)
            ->assertJsonStructure(['card_html', 'message']);

        $this->assertDatabaseHas('recruitment_requests', [
            'company_user_id' => $company->id,
            'developer_user_id' => $talent->id,
            'mode' => RecruitmentRequest::MODE_NAMED,
        ]);
    }

    private function makeProCompany(): User
    {
        $company = User::factory()->companyOwner()->create([
            'approval_status' => User::APPROVAL_APPROVED,
        ]);

        CompanyProfile::factory()->create([
            'user_id' => $company->id,
            'plan' => CompanyProfile::PLAN_PRO,
            'is_subscribed' => true,
            'subscription_expires_at' => now()->addYear(),
            'country' => 'fr',
            'city' => 'Paris',
        ]);

        return $company->fresh(['companyProfile']);
    }

    private function makeListableTalent(string $name): User
    {
        $sector = ProfessionSector::query()->firstOrCreate(
            ['slug' => 'it-sourcing-named'],
            [
                'name_fr' => 'IT',
                'name_en' => 'IT',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        $profession = Profession::query()->firstOrCreate(
            ['slug' => 'dev-sourcing-named'],
            [
                'profession_sector_id' => $sector->id,
                'name_fr' => 'Développeur',
                'name_en' => 'Developer',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        $talent = User::factory()->talent()->create(['name' => $name]);

        $profile = $talent->profile()->create([
            'profession_id' => $profession->id,
            'profession_sector_id' => $sector->id,
            'bio' => 'Bio test',
            'is_public' => true,
        ]);

        $profile->documents()->create([
            'document_type' => ProfileDocument::TYPE_CV,
            'language' => 'fr',
            'path' => 'profile-documents/cv-'.$talent->id.'.pdf',
            'original_name' => 'cv.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
        ]);

        return $talent->fresh(['profile']);
    }
}

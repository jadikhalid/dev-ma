<?php

namespace Tests\Feature\Company;

use App\Models\CompanyProfile;
use App\Models\DirectHireRequest;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\ProfileDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DirectHireComposeSheetTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_enabled_compose_button_when_company_can_propose(): void
    {
        $company = $this->makeCompanyOnTrial();

        $this->actingAs($company)
            ->get(route('company.direct-hire.index'))
            ->assertOk()
            ->assertSee(__('talenma.direct_hire.compose_open_aria'), false)
            ->assertSee('direct-hire-open-compose', false)
            ->assertSee('directHireIndexWorkspace', false);
    }

    public function test_index_disables_compose_button_when_open_hire_exists(): void
    {
        $company = $this->makeCompanyOnTrial();
        $talent = User::factory()->talent()->create(['name' => 'Talent Open']);

        DirectHireRequest::query()->create([
            'company_user_id' => $company->id,
            'talent_user_id' => $talent->id,
            'talent_name_snapshot' => $talent->name,
            'company_profile_id' => $company->companyProfile->id,
            'company_name_snapshot' => $company->name,
            'subject' => 'Software engineer',
            'message' => str_repeat('Proposition détaillée. ', 4),
            'status' => DirectHireRequest::STATUS_PENDING_RESPONSE,
            'company_seen_at' => now(),
        ]);

        $this->actingAs($company)
            ->get(route('company.direct-hire.index'))
            ->assertOk()
            ->assertSee(__('talenma.direct_hire.cta_disabled_hint'), false)
            ->assertSee('canCompose: false', false)
            ->assertSee('disabled', false);
    }

    public function test_talent_suggestions_return_eligible_talents_by_name(): void
    {
        $company = $this->makeCompanyOnTrial();
        $eligible = $this->makeListableTalent('Amine Eligible');
        $this->makeListableTalent('Other Person');

        $this->actingAs($company)
            ->getJson(route('company.direct-hire.talent-suggestions', ['q' => 'Amine']))
            ->assertOk()
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.id', $eligible->id);
    }

    public function test_talent_suggestions_empty_when_company_has_open_hire(): void
    {
        $company = $this->makeCompanyOnTrial();
        $eligible = $this->makeListableTalent('Amine Eligible');

        DirectHireRequest::query()->create([
            'company_user_id' => $company->id,
            'talent_user_id' => $eligible->id,
            'talent_name_snapshot' => $eligible->name,
            'company_profile_id' => $company->companyProfile->id,
            'company_name_snapshot' => $company->name,
            'subject' => 'Software engineer',
            'message' => str_repeat('Proposition détaillée. ', 4),
            'status' => DirectHireRequest::STATUS_PENDING_RESPONSE,
            'company_seen_at' => now(),
        ]);

        $this->actingAs($company)
            ->getJson(route('company.direct-hire.talent-suggestions', ['q' => 'Amine']))
            ->assertOk()
            ->assertJsonPath('results', []);
    }

    public function test_store_from_compose_returns_show_url_json(): void
    {
        Mail::fake();

        $company = $this->makeCompanyOnTrial();
        $talent = $this->makeListableTalent('Amine Eligible');

        $response = $this->actingAs($company)
            ->postJson(route('company.direct-hire.store', $talent), [
                'subject' => 'Backend engineer',
                'message' => str_repeat('Voici une proposition claire et détaillée. ', 3),
            ]);

        $response->assertOk()
            ->assertJsonPath('message', __('talenma.direct_hire.sent'));

        $hire = DirectHireRequest::query()->first();
        $this->assertNotNull($hire);
        $response->assertJsonPath('show_url', route('company.direct-hire.show', $hire));
    }

    private function makeCompanyOnTrial(): User
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        return $company->fresh(['companyProfile']);
    }

    private function makeListableTalent(string $name): User
    {
        $sector = ProfessionSector::query()->firstOrCreate(
            ['slug' => 'it-compose'],
            [
                'name_fr' => 'IT',
                'name_en' => 'IT',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        $profession = Profession::query()->firstOrCreate(
            ['slug' => 'dev-compose'],
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

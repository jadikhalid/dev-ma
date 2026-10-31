<?php

namespace Tests\Feature\Company;

use App\Models\DirectHireRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectHireHideClosedTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_hide_closed_hire_while_talent_keeps_it(): void
    {
        [$company, $talent, $hire] = $this->makeClosedDirectHire();

        $this->actingAs($company)
            ->deleteJson(route('company.direct-hire.destroy', $hire))
            ->assertOk()
            ->assertJsonPath('message', __('talenma.direct_hire.deleted_closed'))
            ->assertJsonPath('unseen_pending', false);

        $this->assertNotNull($hire->fresh()?->company_hidden_at);

        $this->actingAs($company)
            ->get(route('company.direct-hire.index'))
            ->assertOk()
            ->assertDontSee($hire->subject, false);

        $this->actingAs($talent)
            ->get(route('talent.direct-hire.index'))
            ->assertOk()
            ->assertSee($hire->subject, false);

        $this->actingAs($company)
            ->get(route('company.direct-hire.show', $hire))
            ->assertNotFound();
    }

    public function test_both_parties_hiding_purges_the_dossier(): void
    {
        [$company, $talent, $hire] = $this->makeClosedDirectHire();
        $hireId = $hire->id;

        $this->actingAs($company)
            ->deleteJson(route('company.direct-hire.destroy', $hire))
            ->assertOk();

        $this->actingAs($talent)
            ->deleteJson(route('talent.direct-hire.destroy', $hire))
            ->assertOk();

        $this->assertDatabaseMissing('direct_hire_requests', ['id' => $hireId]);
    }

    public function test_open_hire_cannot_be_hidden(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create(['country' => 'fr']);
        $talent = User::factory()->talent()->create();

        $hire = DirectHireRequest::query()->create([
            'company_user_id' => $company->id,
            'talent_user_id' => $talent->id,
            'talent_name_snapshot' => $talent->name,
            'company_profile_id' => $company->companyProfile->id,
            'company_name_snapshot' => $company->name,
            'hire_origin' => DirectHireRequest::ORIGIN_COMPANY,
            'subject' => 'Open process',
            'message' => str_repeat('Proposition détaillée. ', 4),
            'status' => DirectHireRequest::STATUS_PENDING_RESPONSE,
            'company_seen_at' => now(),
        ]);

        $this->actingAs($company)
            ->deleteJson(route('company.direct-hire.destroy', $hire))
            ->assertStatus(422);

        $this->assertDatabaseHas('direct_hire_requests', [
            'id' => $hire->id,
            'company_hidden_at' => null,
        ]);
    }

    /**
     * @return array{0: User, 1: User, 2: DirectHireRequest}
     */
    private function makeClosedDirectHire(): array
    {
        $company = User::factory()->companyOwner()->create(['name' => 'Acme Corp']);
        $company->companyProfile()->create(['country' => 'fr']);

        $talent = User::factory()->talent()->create(['name' => 'Talent Alpha']);

        $hire = DirectHireRequest::query()->create([
            'company_user_id' => $company->id,
            'talent_user_id' => $talent->id,
            'talent_name_snapshot' => $talent->name,
            'company_profile_id' => $company->companyProfile->id,
            'company_name_snapshot' => $company->name,
            'hire_origin' => DirectHireRequest::ORIGIN_COMPANY,
            'subject' => 'Software engineer closed',
            'message' => str_repeat('Proposition détaillée. ', 4),
            'status' => DirectHireRequest::STATUS_CLOSED_NEGATIVE,
            'closed_at' => now(),
            'company_seen_at' => null,
            'talent_seen_at' => null,
        ]);

        return [$company->fresh(['companyProfile']), $talent, $hire];
    }
}

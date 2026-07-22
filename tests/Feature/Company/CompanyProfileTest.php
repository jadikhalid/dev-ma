<?php

namespace Tests\Feature\Company;

use App\Models\User;
use Database\Seeders\ProfessionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProfessionSeeder::class);
    }

    private function approvedCompany(array $profileOverrides = [], array $userOverrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => 'company',
            'name' => 'Acme SAS',
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ], $userOverrides));

        $user->companyProfile()->create(array_merge([
            'country' => 'fr',
        ], $profileOverrides));

        return $user->fresh(['companyProfile']);
    }

    /**
     * @return array<string, mixed>
     */
    private function catalogReadyAttributes(): array
    {
        return [
            'sector' => 'Informatique & digital',
            'employee_count' => '11-50',
            'country' => 'fr',
            'city' => 'Paris',
            'website' => 'https://acme.example',
            'hiring_needs' => 'Nous recherchons des développeurs Laravel seniors pour une mission longue durée.',
        ];
    }

    public function test_company_profile_page_is_displayed_for_approved_company(): void
    {
        $user = $this->approvedCompany();

        $response = $this->actingAs($user)->get(route('company.profile.edit'));

        $response->assertOk();
    }

    public function test_pending_company_cannot_access_profile(): void
    {
        $user = User::factory()->create([
            'role' => 'company',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $response = $this->actingAs($user)->get(route('company.profile.edit'));

        $response->assertRedirect(route('account.pending'));
    }

    public function test_company_can_update_identity_section(): void
    {
        $user = $this->approvedCompany();

        $response = $this->actingAs($user)->post(route('company.profile.update'), [
            'section' => 'identity',
            'sector' => 'it-digital',
            'employee_count' => '51-200',
            'country' => 'fr',
            'city' => 'Lyon',
            'website' => 'https://acme.example',
        ]);

        $response->assertRedirect(route('company.profile.edit'));
        $response->assertSessionHas('updated_section', 'identity');

        $profile = $user->fresh()->companyProfile;
        $this->assertSame('Acme SAS', $user->fresh()->name);
        $this->assertSame('Informatique & digital', $profile->sector);
        $this->assertSame('Lyon', $profile->city);
        $this->assertSame('fr', $profile->country);
    }

    public function test_company_can_update_identity_section_via_ajax(): void
    {
        $user = $this->approvedCompany();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post(route('company.profile.update'), [
                'section' => 'identity',
                'sector' => 'it-digital',
                'employee_count' => '11-50',
                'country' => 'fr',
                'city' => 'Marseille',
                'website' => 'https://acme.example',
            ]);

        $response->assertOk()
            ->assertJsonPath('message', __('talenma.company.section_updated.identity'))
            ->assertJsonPath('sector_label', 'Informatique & digital')
            ->assertJsonPath('location_label', 'Marseille, France');

        $this->assertSame('Marseille', $user->fresh()->companyProfile->city);
    }

    public function test_company_logo_url_prefers_account_avatar(): void
    {
        $user = $this->approvedCompany(['logo_path' => 'company-logos/old.jpg']);
        $user->update(['avatar_path' => 'avatars/'.$user->id.'.jpg']);

        $this->assertSame('/storage/avatars/'.$user->id.'.jpg', $user->fresh()->companyProfile->logoUrl());
    }

    public function test_company_profile_identity_update_ignores_logo_upload(): void
    {
        Storage::fake('public');

        $user = $this->approvedCompany();

        $response = $this->actingAs($user)->post(route('company.profile.update'), [
            'section' => 'identity',
            'sector' => 'it-digital',
            'employee_count' => '11-50',
            'country' => 'fr',
            'city' => 'Paris',
            'website' => 'https://acme.example',
            'logo' => UploadedFile::fake()->image('logo.jpg', 300, 300),
        ]);

        $response->assertRedirect(route('company.profile.edit'));

        $profile = $user->fresh()->companyProfile;
        $this->assertNull($profile->logo_path);
    }

    public function test_company_can_update_hiring_section_via_ajax(): void
    {
        $user = $this->approvedCompany();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post(route('company.profile.update'), [
                'section' => 'hiring',
                'hiring_needs' => 'Recherche urgente de profils data engineers et DevOps pour renfort équipe produit.',
            ]);

        $response->assertOk()
            ->assertJsonPath('message', __('talenma.company.section_updated.hiring'));

        $this->assertStringContainsString(
            'data engineers',
            $user->fresh()->companyProfile->hiring_needs,
        );
    }

    public function test_company_can_update_hiring_section(): void
    {
        $user = $this->approvedCompany();

        $response = $this->actingAs($user)->post(route('company.profile.update'), [
            'section' => 'hiring',
            'hiring_needs' => 'Recherche urgente de profils data engineers et DevOps pour renfort équipe produit.',
        ]);

        $response->assertRedirect(route('company.profile.edit'));
        $response->assertSessionHas('updated_section', 'hiring');

        $this->assertStringContainsString(
            'data engineers',
            $user->fresh()->companyProfile->hiring_needs,
        );
    }

    public function test_catalog_is_blocked_when_profile_is_incomplete(): void
    {
        $user = $this->approvedCompany([
            'hiring_needs' => null,
            'sector' => null,
        ]);

        $response = $this->actingAs($user)->get(route('company.search'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('toast_error');
    }

    public function test_catalog_is_accessible_when_profile_is_ready(): void
    {
        $user = $this->approvedCompany($this->catalogReadyAttributes());

        $response = $this->actingAs($user)->get(route('company.search'));

        $response->assertOk();
    }

    public function test_dashboard_shows_completion_for_company(): void
    {
        $user = $this->approvedCompany($this->catalogReadyAttributes());

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Acme SAS', false);
    }
}

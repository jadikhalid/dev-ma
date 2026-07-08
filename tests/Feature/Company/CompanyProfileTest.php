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

    private function approvedCompany(array $profileOverrides = []): User
    {
        $user = User::factory()->create([
            'role' => 'company',
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);

        $user->companyProfile()->create(array_merge([
            'company_name' => 'Acme SAS',
            'country' => 'France',
        ], $profileOverrides));

        return $user->fresh(['companyProfile']);
    }

    /**
     * @return array<string, mixed>
     */
    private function catalogReadyAttributes(): array
    {
        return [
            'company_name' => 'Acme SAS',
            'sector' => 'Informatique & digital',
            'employee_count' => '11-50',
            'country' => 'France',
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
            'company_name' => 'Acme International',
            'sector' => 'it-digital',
            'employee_count' => '51-200',
            'country' => 'France',
            'city' => 'Lyon',
            'website' => 'https://acme.example',
        ]);

        $response->assertRedirect(route('company.profile.edit'));
        $response->assertSessionHas('updated_section', 'identity');

        $profile = $user->fresh()->companyProfile;
        $this->assertSame('Acme International', $profile->company_name);
        $this->assertSame('Informatique & digital', $profile->sector);
        $this->assertSame('Lyon', $profile->city);
    }

    public function test_company_can_upload_logo(): void
    {
        Storage::fake('public');

        $user = $this->approvedCompany();

        $response = $this->actingAs($user)->post(route('company.profile.update'), [
            'section' => 'identity',
            'company_name' => 'Acme SAS',
            'sector' => 'it-digital',
            'employee_count' => '11-50',
            'country' => 'France',
            'city' => 'Paris',
            'website' => 'https://acme.example',
            'logo' => UploadedFile::fake()->image('logo.jpg', 300, 300),
        ]);

        $response->assertRedirect(route('company.profile.edit'));

        $profile = $user->fresh()->companyProfile;
        $this->assertNotNull($profile->logo_path);
        Storage::disk('public')->assertExists($profile->logo_path);
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

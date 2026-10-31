<?php

namespace Tests\Feature\Company;

use App\Models\CompanyProfile;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\Profile;
use App\Models\ProfileDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyTalentPoolListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_talent_pool_lists_profiles_with_profession_and_cv(): void
    {
        $sector = ProfessionSector::query()->create([
            'slug' => 'it-pool',
            'name_fr' => 'IT',
            'name_en' => 'IT',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $profession = Profession::query()->create([
            'profession_sector_id' => $sector->id,
            'slug' => 'dev-pool',
            'name_fr' => 'Développeur',
            'name_en' => 'Developer',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $withCv = $this->createTalent($profession, bio: null, withCv: true);
        $withBioOnly = $this->createTalent($profession, bio: 'Bio sans CV', withCv: false);
        $withoutProfession = $this->createTalent(null, bio: 'Bio', withCv: true);

        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        $ids = collect(
            $this->actingAs($company)
                ->getJson(route('company.search'))
                ->assertOk()
                ->json('talents')
        )->pluck('id');

        $this->assertTrue($ids->contains($withCv->id));
        $this->assertFalse($ids->contains($withBioOnly->id));
        $this->assertFalse($ids->contains($withoutProfession->id));
    }

    public function test_private_profile_cv_is_visible_to_company(): void
    {
        Storage::fake('public');

        $sector = ProfessionSector::query()->create([
            'slug' => 'it-private',
            'name_fr' => 'IT',
            'name_en' => 'IT',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $profession = Profession::query()->create([
            'profession_sector_id' => $sector->id,
            'slug' => 'dev-private',
            'name_fr' => 'Développeur',
            'name_en' => 'Developer',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $talent = $this->createTalent($profession, bio: null, withCv: true, isPublic: false);
        Storage::disk('public')->put('profile-documents/cv-'.$talent->id.'.pdf', '%PDF-1.4');

        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        $listed = collect(
            $this->actingAs($company)
                ->getJson(route('company.search'))
                ->json('talents')
        )->firstWhere('id', $talent->id);

        $this->assertFalse($listed['is_public']);
        $this->assertSame(route('company.talent.cv', $talent), $listed['cv_url']);

        $this->actingAs($company)
            ->getJson(route('company.talent.show', $talent))
            ->assertOk()
            ->assertJsonPath('cv_url', route('company.talent.cv', $talent))
            ->assertJsonPath('linkedin_url', null);

        $this->actingAs($company)
            ->get(route('company.talent.cv', $talent))
            ->assertOk();
    }

    public function test_talent_pool_can_filter_by_country_and_city(): void
    {
        $sector = ProfessionSector::query()->create([
            'slug' => 'it-location',
            'name_fr' => 'IT',
            'name_en' => 'IT',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $profession = Profession::query()->create([
            'profession_sector_id' => $sector->id,
            'slug' => 'dev-location',
            'name_fr' => 'Développeur',
            'name_en' => 'Developer',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $casablanca = $this->createTalent(
            $profession,
            bio: null,
            withCv: true,
            country: Profile::COUNTRY_MA,
            city: 'Casablanca',
        );
        $rabat = $this->createTalent(
            $profession,
            bio: null,
            withCv: true,
            country: Profile::COUNTRY_MA,
            city: 'Rabat',
        );
        $paris = $this->createTalent(
            $profession,
            bio: null,
            withCv: true,
            country: Profile::COUNTRY_FR,
            city: 'Paris',
        );

        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        $moroccoIds = collect(
            $this->actingAs($company)
                ->getJson(route('company.search', ['country' => Profile::COUNTRY_MA]))
                ->assertOk()
                ->json('talents')
        )->pluck('id');

        $this->assertTrue($moroccoIds->contains($casablanca->id));
        $this->assertTrue($moroccoIds->contains($rabat->id));
        $this->assertFalse($moroccoIds->contains($paris->id));

        $cityIds = collect(
            $this->actingAs($company)
                ->getJson(route('company.search', [
                    'country' => Profile::COUNTRY_MA,
                    'city' => 'Casablanca',
                ]))
                ->assertOk()
                ->json('talents')
        )->pluck('id');

        $this->assertTrue($cityIds->contains($casablanca->id));
        $this->assertFalse($cityIds->contains($rabat->id));
        $this->assertFalse($cityIds->contains($paris->id));
    }

    private function createTalent(
        ?Profession $profession,
        ?string $bio,
        bool $withCv,
        bool $isPublic = true,
        ?string $country = null,
        ?string $city = null,
    ): User {
        $talent = User::factory()->talent()->create();

        $profile = $talent->profile()->create([
            'profession_id' => $profession?->id,
            'profession_sector_id' => $profession?->profession_sector_id,
            'bio' => $bio,
            'is_public' => $isPublic,
            'country' => $country,
            'city' => $city,
        ]);

        if ($withCv) {
            $profile->documents()->create([
                'document_type' => ProfileDocument::TYPE_CV,
                'language' => 'fr',
                'path' => 'profile-documents/cv-'.$talent->id.'.pdf',
                'original_name' => 'cv.pdf',
                'mime_type' => 'application/pdf',
                'size' => 1024,
            ]);
        }

        return $talent;
    }
}

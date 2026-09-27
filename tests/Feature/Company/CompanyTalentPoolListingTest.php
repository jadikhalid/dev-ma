<?php

namespace Tests\Feature\Company;

use App\Models\CompanyProfile;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\ProfileDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    private function createTalent(?Profession $profession, ?string $bio, bool $withCv): User
    {
        $talent = User::factory()->talent()->create();

        $profile = $talent->profile()->create([
            'profession_id' => $profession?->id,
            'profession_sector_id' => $profession?->profession_sector_id,
            'bio' => $bio,
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

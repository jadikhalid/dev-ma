<?php

namespace Tests\Unit;

use App\Models\Profile;
use App\Models\ProfileDocument;
use App\Models\User;
use App\Services\TalentProfileCompletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TalentProfileCompletionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_percent_is_done_over_total_items(): void
    {
        $user = User::factory()->create(['role' => 'dev']);
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'profession_sector_id' => null,
            'profession_id' => null,
            'specialization' => null,
            'bio' => str_repeat('a', 40),
            'experience_years' => 5,
            'education_level' => 'bac+5',
            'skills' => ['Laravel'],
            'city' => 'Casablanca',
            'country' => 'Maroc',
            'daily_rate_eur' => 300,
            'availability' => 'disponible',
            'work_modes' => ['remote'],
            'languages' => ['fr'],
            'linkedin_url' => null,
            'github_url' => null,
            'portfolio_url' => null,
            'phone' => null,
        ]);

        $assessment = app(TalentProfileCompletionService::class)->assess($profile->fresh('documents'));

        // 3 presentation required + skills + 5 availability = 9 done out of 17
        $this->assertSame(17, $assessment['total_count']);
        $this->assertSame(9, $assessment['done_count']);
        $this->assertSame(53, $assessment['percent']);
        $this->assertFalse($assessment['is_catalog_ready']);
    }

    public function test_catalog_ready_requires_core_fields_not_optional_links(): void
    {
        $user = User::factory()->create(['role' => 'dev']);
        $sector = \App\Models\ProfessionSector::query()->create([
            'slug' => 'it',
            'name_fr' => 'IT',
            'name_en' => 'IT',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $profession = \App\Models\Profession::query()->create([
            'profession_sector_id' => $sector->id,
            'slug' => 'dev',
            'name_fr' => 'Dev',
            'name_en' => 'Dev',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'profession_sector_id' => $sector->id,
            'profession_id' => $profession->id,
            'specialization' => 'Laravel',
            'bio' => str_repeat('b', 40),
            'experience_years' => 3,
            'education_level' => 'bac+5',
            'skills' => [],
            'city' => 'Rabat',
            'country' => 'Maroc',
            'daily_rate_eur' => 250,
            'availability' => 'disponible',
            'work_modes' => ['remote'],
            'languages' => ['fr'],
            'linkedin_url' => null,
            'github_url' => null,
            'portfolio_url' => null,
            'phone' => null,
        ]);

        $assessment = app(TalentProfileCompletionService::class)->assess($profile->fresh('documents'));

        $this->assertTrue($assessment['is_catalog_ready']);
        $this->assertSame(11, $assessment['done_count']);
        $this->assertSame(65, $assessment['percent']); // 11/17
    }

    public function test_cv_counts_toward_percent(): void
    {
        $user = User::factory()->create(['role' => 'dev']);
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'linkedin_url' => null,
            'github_url' => null,
            'portfolio_url' => null,
            'phone' => null,
        ]);

        $before = app(TalentProfileCompletionService::class)->assess($profile->fresh('documents'));

        $profile->documents()->create([
            'document_type' => ProfileDocument::TYPE_CV,
            'path' => 'profile-documents/1/cv.pdf',
            'original_name' => 'cv.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1000,
            'sort_order' => 1,
        ]);

        $after = app(TalentProfileCompletionService::class)->assess($profile->fresh('documents'));

        $this->assertSame($before['done_count'] + 1, $after['done_count']);
        $this->assertSame(17, $after['total_count']);
    }
}

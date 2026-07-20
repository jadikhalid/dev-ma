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
            'availability' => 'disponible',
            'work_modes' => ['remote'],
            'languages' => ['fr'],
            'linkedin_url' => null,
            'github_url' => null,
            'portfolio_url' => null,
            'phone' => null,
        ]);

        $assessment = app(TalentProfileCompletionService::class)->assess($profile->fresh('documents'));

        // 4 presentation + 2 availability = 6 done out of 16
        $this->assertSame(16, $assessment['total_count']);
        $this->assertSame(6, $assessment['done_count']);
        $this->assertSame(38, $assessment['percent']);
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
        $this->assertSame(9, $assessment['done_count']);
        $this->assertSame(56, $assessment['percent']); // 9/16
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
        $this->assertSame(16, $after['total_count']);
    }
}

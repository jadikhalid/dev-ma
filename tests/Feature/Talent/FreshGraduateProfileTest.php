<?php

namespace Tests\Feature\Talent;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreshGraduateProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_talent_can_save_fresh_graduate_without_experience_years(): void
    {
        $talent = User::factory()->talent()->create();
        Profile::factory()->create([
            'user_id' => $talent->id,
            'bio' => str_repeat('Bio talent test suffisamment longue. ', 3),
            'experience_years' => 3,
            'education_level' => 'bac+5',
            'languages' => ['fr'],
        ]);

        $this->actingAs($talent)
            ->post(route('profile.details.update'), [
                'section' => 'presentation',
                'bio' => str_repeat('Bio talent test suffisamment longue. ', 3),
                'is_fresh_graduate' => '1',
                'education_level' => 'bac+5',
                'languages' => ['fr'],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $profile = $talent->fresh()->profile;

        $this->assertTrue($profile->is_fresh_graduate);
        $this->assertNull($profile->experience_years);
        $this->assertSame('Jeune diplômé(e)', $profile->experienceLabel());
    }

    public function test_talent_can_save_numeric_experience_when_not_fresh_graduate(): void
    {
        $talent = User::factory()->talent()->create();
        Profile::factory()->create([
            'user_id' => $talent->id,
            'bio' => str_repeat('Bio talent test suffisamment longue. ', 3),
            'is_fresh_graduate' => true,
            'experience_years' => null,
            'education_level' => 'bac+5',
            'languages' => ['fr'],
        ]);

        $this->actingAs($talent)
            ->post(route('profile.details.update'), [
                'section' => 'presentation',
                'bio' => str_repeat('Bio talent test suffisamment longue. ', 3),
                'is_fresh_graduate' => '0',
                'experience_years' => 2,
                'education_level' => 'bac+5',
                'languages' => ['fr'],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $profile = $talent->fresh()->profile;

        $this->assertFalse($profile->is_fresh_graduate);
        $this->assertSame(2, $profile->experience_years);
        $this->assertSame('2 ans d\'exp.', $profile->experienceLabel());
    }

    public function test_talent_cannot_save_zero_experience_years_without_fresh_graduate(): void
    {
        $talent = User::factory()->talent()->create();
        Profile::factory()->create([
            'user_id' => $talent->id,
            'bio' => str_repeat('Bio talent test suffisamment longue. ', 3),
            'is_fresh_graduate' => true,
            'experience_years' => null,
            'education_level' => 'bac+5',
            'languages' => ['fr'],
        ]);

        $this->actingAs($talent)
            ->post(route('profile.details.update'), [
                'section' => 'presentation',
                'bio' => str_repeat('Bio talent test suffisamment longue. ', 3),
                'is_fresh_graduate' => '0',
                'experience_years' => 0,
                'education_level' => 'bac+5',
                'languages' => ['fr'],
            ])
            ->assertSessionHasErrors('experience_years');
    }
}

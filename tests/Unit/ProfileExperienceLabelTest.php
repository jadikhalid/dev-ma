<?php

namespace Tests\Unit;

use App\Models\Profile;
use Tests\TestCase;

class ProfileExperienceLabelTest extends TestCase
{
    public function test_fresh_graduate_displays_dedicated_label_in_french(): void
    {
        app()->setLocale('fr');

        $profile = new Profile([
            'is_fresh_graduate' => true,
            'experience_years' => null,
        ]);

        $this->assertSame('Jeune diplômé(e)', $profile->experienceLabel());
        $this->assertTrue($profile->hasExperienceDeclared());
    }

    public function test_fresh_graduate_displays_dedicated_label_in_english(): void
    {
        app()->setLocale('en');

        $profile = new Profile([
            'is_fresh_graduate' => true,
            'experience_years' => null,
        ]);

        $this->assertSame('Fresh graduate', $profile->experienceLabel());
    }

    public function test_minimum_experience_starts_at_one_year(): void
    {
        app()->setLocale('fr');

        $this->assertSame('1 ans d\'exp.', Profile::experienceLabelFor(1));
        $this->assertSame('5 ans d\'exp.', Profile::experienceLabelFor(5));
    }

    public function test_experience_is_not_declared_when_neither_case_is_set(): void
    {
        $profile = new Profile([
            'is_fresh_graduate' => false,
            'experience_years' => null,
        ]);

        $this->assertFalse($profile->hasExperienceDeclared());
        $this->assertSame('', $profile->experienceLabel());
    }
}

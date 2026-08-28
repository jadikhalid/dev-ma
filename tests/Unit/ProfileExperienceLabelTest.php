<?php

namespace Tests\Unit;

use App\Models\Profile;
use Tests\TestCase;

class ProfileExperienceLabelTest extends TestCase
{
    public function test_zero_years_displays_fresh_graduate_label_in_french(): void
    {
        app()->setLocale('fr');

        $this->assertSame('Jeune diplômé(e)', Profile::experienceLabelFor(0));
    }

    public function test_zero_years_displays_fresh_graduate_label_in_english(): void
    {
        app()->setLocale('en');

        $this->assertSame('Fresh graduate', Profile::experienceLabelFor(0));
    }

    public function test_positive_years_keep_experience_format(): void
    {
        app()->setLocale('fr');

        $this->assertSame('5 ans d\'exp.', Profile::experienceLabelFor(5));
    }

    public function test_profile_instance_uses_experience_label_helper(): void
    {
        app()->setLocale('fr');

        $profile = new Profile(['experience_years' => 0]);

        $this->assertSame('Jeune diplômé(e)', $profile->experienceLabel());
    }
}

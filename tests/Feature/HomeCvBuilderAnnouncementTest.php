<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeCvBuilderAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_does_not_include_cv_builder_announcement(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('cvBuilderAnnouncement', false)
            ->assertDontSee('announcement-agent.png', false)
            ->assertDontSee('cv-builder-announcement-title', false);
    }

    public function test_guest_cv_builder_gate_redirects_to_login_then_cv_builder_after_auth(): void
    {
        $talent = User::factory()->talent()->create([
            'email' => 'talent.cv@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->get(route('cv-builder.gate'))
            ->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $talent->email,
            'password' => 'password',
        ])->assertRedirect(route('cv-builder.gate'));

        $this->get(route('cv-builder.gate'))
            ->assertRedirect(route('talent.cv-builder.index'));
    }
}

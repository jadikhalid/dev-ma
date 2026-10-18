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

    public function test_guest_home_cv_cta_opens_the_intro_page(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();
        $cta = 'href="'.route('cv-builder.intro').'"';

        $this->assertStringContainsString($cta, $html);
        $this->assertStringContainsString(__('talenma.home.tools_cv_cta'), $html);
    }

    public function test_talent_home_cv_cta_still_opens_the_builder(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('talent.cv-builder.index').'"', false)
            ->assertDontSee('href="'.route('cv-builder.intro').'"', false);
    }

    public function test_cv_builder_intro_invites_guests_to_sign_in_or_register(): void
    {
        $this->get(route('cv-builder.intro'))
            ->assertOk()
            ->assertSee(__('talenma.cv_builder_intro.title'), false)
            ->assertSee(__('talenma.cv_builder_intro.steps.0.title'), false)
            ->assertSee(__('talenma.cv_builder_intro.steps.3.title'), false)
            ->assertSee('images/cv-builder/intro/step-template.jpg', false)
            ->assertSee('images/cv-builder/intro/step-export.jpg', false)
            ->assertSee('href="'.route('cv-builder.gate').'"', false)
            ->assertSee('href="'.e(route('register', ['role' => 'dev', 'from_tool' => 'cv-builder'])).'"', false);

        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('cv-builder.intro'))
            ->assertRedirect(route('talent.cv-builder.index'));
    }

    public function test_guest_home_ats_cta_opens_the_intro_page(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('href="'.route('ats-score.intro').'"', $html);
        $this->assertStringContainsString(__('talenma.home.tools_ats_cta'), $html);
    }

    public function test_talent_home_ats_cta_still_opens_the_tool(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('talent.ats-score.index').'"', false)
            ->assertDontSee('href="'.route('ats-score.intro').'"', false);
    }

    public function test_ats_score_intro_invites_guests_to_sign_in_or_register(): void
    {
        $this->get(route('ats-score.intro'))
            ->assertOk()
            ->assertSee(__('talenma.ats_score_intro.title'), false)
            ->assertSee(__('talenma.ats_score_intro.steps.0.title'), false)
            ->assertSee(__('talenma.ats_score_intro.steps.2.title'), false)
            ->assertSee('images/ats-score/intro/step-upload.jpg', false)
            ->assertSee('images/ats-score/intro/step-optimize.jpg', false)
            ->assertSee('href="'.route('ats-score.gate').'"', false)
            ->assertSee('href="'.e(route('register', ['role' => 'dev', 'from_tool' => 'ats-score'])).'"', false);

        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('ats-score.intro'))
            ->assertRedirect(route('talent.ats-score.index'));
    }

    public function test_register_from_ats_intro_remembers_the_ats_gate(): void
    {
        $this->get(route('register', ['role' => 'dev', 'from_tool' => 'ats-score']))
            ->assertOk()
            ->assertSessionHas('url.intended', route('ats-score.gate'));
    }

    public function test_library_intro_invites_guests_to_sign_in_or_register(): void
    {
        $this->get(route('library.intro'))
            ->assertOk()
            ->assertSee(__('talenma.library_intro.title'), false)
            ->assertSee(__('talenma.library_intro.steps.0.title'), false)
            ->assertSee(__('talenma.library_intro.steps.2.title'), false)
            ->assertSee('images/library/intro/step-filter.jpg', false)
            ->assertSee('images/library/intro/step-download.jpg', false)
            ->assertSee('href="'.route('library.gate').'"', false)
            ->assertSee('href="'.e(route('register', ['role' => 'dev', 'from_tool' => 'library'])).'"', false);

        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('library.intro'))
            ->assertRedirect(route('talent.library.index'));
    }

    public function test_register_from_library_intro_remembers_the_library_gate(): void
    {
        $this->get(route('register', ['role' => 'dev', 'from_tool' => 'library']))
            ->assertOk()
            ->assertSessionHas('url.intended', route('library.gate'));
    }

    public function test_register_from_cv_intro_remembers_the_builder_gate(): void
    {
        $this->get(route('register', ['role' => 'dev', 'from_tool' => 'cv-builder']))
            ->assertOk()
            ->assertSessionHas('url.intended', route('cv-builder.gate'));
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

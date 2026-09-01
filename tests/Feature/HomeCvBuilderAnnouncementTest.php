<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeCvBuilderAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_includes_cv_builder_announcement_for_guests(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('cvBuilderAnnouncement', false)
            ->assertSee(__('talenma.home.cv_builder_announcement.logo_alt'), false)
            ->assertSee(__('talenma.home.cv_builder_announcement.title'), false)
            ->assertSee(__('talenma.home.cv_builder_announcement.cta'), false)
            ->assertSee(route('cv-builder.gate'), false);
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

    public function test_announcement_cta_links_talent_to_cv_builder(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('home'))
            ->assertOk()
            ->assertSee(route('talent.cv-builder.index'), false)
            ->assertSee(__('talenma.home.cv_builder_announcement.cta'), false);
    }

    public function test_homepage_includes_cv_builder_announcement_for_company_users(): void
    {
        $company = User::factory()->companyOwner()->create();

        $this->actingAs($company)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('cvBuilderAnnouncement', false);
    }
}

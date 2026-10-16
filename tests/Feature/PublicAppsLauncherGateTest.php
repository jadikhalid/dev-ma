<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicAppsLauncherGateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_hitting_ats_score_gate_is_sent_to_login(): void
    {
        $this->get(route('ats-score.gate'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function talent_passing_ats_score_gate_reaches_app(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('ats-score.gate'))
            ->assertRedirect(route('talent.ats-score.index'));
    }

    #[Test]
    public function company_hitting_ats_score_gate_is_sent_to_dashboard(): void
    {
        $company = User::factory()->companyOwner()->create();

        $this->actingAs($company)
            ->get(route('ats-score.gate'))
            ->assertRedirect(route($company->homeRouteName()));
    }

    #[Test]
    public function company_member_cannot_open_ats_score(): void
    {
        $member = User::factory()->companyMember()->create();

        $this->actingAs($member)
            ->get(route('talent.ats-score.index'))
            ->assertForbidden();
    }

    #[Test]
    public function company_dashboard_hides_apps_launcher(): void
    {
        $company = User::factory()->companyOwner()->create();

        $this->actingAs($company)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(__('talenma.nav.apps_launcher_title'), false);
    }

    #[Test]
    public function talent_dashboard_shows_mes_applications_tiles_under_welcome(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-dashboard-apps', false)
            ->assertSee(__('talenma.dashboard.talent.apps_section_title'), false)
            ->assertSee('href="'.route('talent.cv-builder.index').'"', false)
            ->assertSee('href="'.route('talent.ats-score.index').'"', false)
            ->assertSee('href="'.route('talent.library.index').'"', false)
            ->assertSee('data-dashboard-app="cv-builder"', false)
            ->assertSee('data-dashboard-app="ats-score"', false)
            ->assertSee('data-dashboard-app="library"', false)
            ->assertSee(__('talenma.dashboard.talent.profile_section_title'), false);
    }

    #[Test]
    public function public_home_shows_account_chevron_after_talent_name(): void
    {
        $talent = User::factory()->talent()->create();

        $html = $this->actingAs($talent)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('data-header-account-chevron', false)
            ->getContent();

        $this->assertGreaterThan(
            strpos($html, 'data-header-display-name'),
            strpos($html, 'data-header-account-chevron')
        );
    }

    #[Test]
    public function public_home_places_locale_switchers_beside_the_logo_and_on_the_right(): void
    {
        $html = $this->get(route('home'))
            ->assertOk()
            ->getContent();

        $phoneSwitcher = strpos($html, 'data-header-locale-switcher');
        $nav = strpos($html, 'data-header-nav-group');
        $desktopSwitcher = strpos($html, 'data-header-locale-switcher', $phoneSwitcher + 1);

        $this->assertNotFalse($phoneSwitcher);
        $this->assertNotFalse($desktopSwitcher);
        $this->assertLessThan($nav, $phoneSwitcher);
        $this->assertGreaterThan($nav, $desktopSwitcher);
        $this->assertLessThan(strpos($html, route('locale.switch', 'fr')), $phoneSwitcher);
        $this->assertSame(2, substr_count($html, route('locale.switch', 'en')));
    }

    #[Test]
    public function public_home_links_the_career_pages(): void
    {
        $html = $this->get(route('home'))
            ->assertOk()
            ->assertSee(__('talenma.nav.offers_link'))
            ->assertSee(__('talenma.nav.career_tools'))
            ->assertSee(__('talenma.nav.how_it_works'))
            ->assertSee(__('talenma.nav.career_advice'))
            ->assertSee(__('talenma.nav.hiring'))
            ->getContent();

        $groupStart = strpos($html, 'data-header-nav-group');
        $group = substr($html, $groupStart, strpos($html, 'data-header-locale-switcher', $groupStart) - $groupStart);

        $this->assertStringContainsString(route('career.offers'), $group);
        $this->assertStringContainsString(route('career.tools'), $group);
        $this->assertStringContainsString(route('career.how'), $group);
        $this->assertStringContainsString(route('blog.index'), $group);
        $this->assertStringContainsString(route('company.offer'), $group);
    }

    #[Test]
    public function public_home_links_career_tools_page_instead_of_apps_menu(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('career.tools'), false)
            ->assertDontSee(__('talenma.nav.apps_launcher_open'), false);

        $this->get(route('career.tools'))
            ->assertOk()
            ->assertSee(route('cv-builder.gate'), false)
            ->assertSee(route('ats-score.gate'), false)
            ->assertSee(route('library.gate'), false);
    }
}

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
    public function talent_home_keeps_only_career_advice_in_the_header_and_drops_career_footer_links(): void
    {
        $talent = User::factory()->talent()->create();

        $html = $this->actingAs($talent)
            ->get(route('home'))
            ->assertOk()
            ->assertSee(__('talenma.nav.my_space'), false)
            ->assertDontSee(__('talenma.nav.dashboard'), false)
            ->getContent();

        $groupStart = strpos($html, 'data-header-nav-group');
        $navTagStart = strrpos(substr($html, 0, $groupStart), '<nav');
        $nav = substr($html, $navTagStart, strpos($html, '</nav>', $navTagStart) - $navTagStart);

        $this->assertStringContainsString('xl:justify-end', $nav);
        $this->assertStringContainsString('xl:ml-11', $html);
        $this->assertStringContainsString(route('blog.index'), $nav);
        $this->assertStringContainsString(__('talenma.nav.career_advice'), $nav);
        $this->assertLessThan(
            strpos($nav, 'data-header-my-space-rule'),
            strpos($nav, route('blog.index'))
        );
        $this->assertLessThan(
            strpos($nav, 'bg-gray-900'),
            strpos($nav, 'data-header-my-space-rule')
        );
        $this->assertStringContainsString(route('dashboard'), $nav);
        $this->assertStringContainsString(__('talenma.nav.my_space'), $nav);
        $this->assertStringNotContainsString(route('career.offers'), $nav);
        $this->assertStringNotContainsString(route('career.tools'), $nav);
        $this->assertStringNotContainsString(route('career.how'), $nav);
        $this->assertStringNotContainsString(route('company.offer'), $nav);

        $footer = substr($html, strpos($html, '<footer'));
        $this->assertStringNotContainsString(route('career.offers'), $footer);
        $this->assertStringNotContainsString(route('career.tools'), $footer);
        $this->assertStringNotContainsString(route('career.how'), $footer);
        $this->assertStringContainsString(route('faq'), $footer);
        $this->assertStringContainsString(route('contact'), $footer);
        $this->assertStringContainsString(route('company.about'), $footer);
        $this->assertStringContainsString(route('company.offer'), $footer);
    }

    #[Test]
    public function talent_blog_index_disables_the_career_advice_button(): void
    {
        $guestHtml = $this->get(route('blog.index'))->assertOk()->getContent();
        $guestStart = strpos($guestHtml, 'data-header-nav-group');
        $guestNavStart = strrpos(substr($guestHtml, 0, $guestStart), '<nav');
        $guestNav = substr($guestHtml, $guestNavStart, strpos($guestHtml, '</nav>', $guestNavStart) - $guestNavStart);
        $this->assertStringContainsString('href="'.route('blog.index').'"', $guestNav);

        $talent = User::factory()->talent()->create();
        $html = $this->actingAs($talent)
            ->get(route('blog.index'))
            ->assertOk()
            ->getContent();

        $groupStart = strpos($html, 'data-header-nav-group');
        $navTagStart = strrpos(substr($html, 0, $groupStart), '<nav');
        $nav = substr($html, $navTagStart, strpos($html, '</nav>', $navTagStart) - $navTagStart);

        $this->assertStringContainsString('aria-disabled="true"', $nav);
        $this->assertStringContainsString('bg-slate-100', $nav);
        $this->assertStringContainsString('text-slate-400', $nav);
        $this->assertStringContainsString(__('talenma.nav.career_advice'), $nav);
        $this->assertStringNotContainsString('href="'.route('blog.index').'"', $nav);
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
            ->assertSee(__('talenma.career_pages.tools_cv_title'), false)
            ->assertSee(route('cv-builder.intro'), false)
            ->assertSee(route('ats-score.intro'), false)
            ->assertSee(route('library.intro'), false)
            ->assertDontSee(route('cv-builder.gate'), false)
            ->assertDontSee(route('ats-score.gate'), false)
            ->assertDontSee(route('library.gate'), false);
    }
}

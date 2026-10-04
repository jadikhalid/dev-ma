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
    public function public_home_places_locale_switcher_next_to_logo_with_divider(): void
    {
        $html = $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-header-locale-divider', false)
            ->getContent();

        $switcher = strpos($html, 'data-header-locale-switcher');
        $this->assertGreaterThan(strpos($html, 'brand-logo-desktop'), $switcher);
        $this->assertLessThan(strpos($html, route('locale.switch', 'fr')), $switcher);
        $this->assertSame(1, substr_count($html, route('locale.switch', 'en')));
    }

    #[Test]
    public function public_home_links_partner_companies_section_after_annonces(): void
    {
        $html = $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('home').'#entreprises', false)
            ->assertSee(__('talenma.nav.partner_companies'))
            ->getContent();

        $this->assertGreaterThan(
            strpos($html, route('home').'#opportunites'),
            strpos($html, 'data-header-partner-companies-link')
        );
    }

    #[Test]
    public function public_home_shows_apps_launcher_for_guests(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('cv-builder.gate'), false)
            ->assertSee(route('ats-score.gate'), false);
    }
}

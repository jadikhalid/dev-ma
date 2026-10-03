<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\PortalHost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyPortalHostTest extends TestCase
{
    use RefreshDatabase;

    public function test_www_entreprises_path_redirects_to_company_portal(): void
    {
        $www = config('talenma.hosts.www');

        $this->get('http://'.$www.'/entreprises?tab=trial')
            ->assertRedirect(PortalHost::companyUrl('/', ['tab' => 'trial']));
    }

    public function test_www_subdomain_redirects_to_canonical_talent_host(): void
    {
        config([
            'talenma.hosts.www' => 'talentsdumaroc.com',
            'app.url' => 'https://talentsdumaroc.com',
        ]);

        $this->get('https://www.talentsdumaroc.com/blog')
            ->assertRedirect('https://talentsdumaroc.com/blog');

        $this->get('https://www.talentsdumaroc.com/')
            ->assertRedirect('https://talentsdumaroc.com/');
    }

    public function test_company_portal_root_renders_offer(): void
    {
        $company = config('talenma.hosts.company');

        $this->get('http://'.$company.'/')
            ->assertOk()
            ->assertSee(__('talenma.company_offer.hero_cta_trial'), false)
            ->assertSee(__('talenma.nav.company_login'), false)
            ->assertDontSee(__('talenma.nav.jobs'), false);
    }

    public function test_company_dashboard_logo_targets_dashboard_not_portal_root(): void
    {
        $companyUser = User::factory()->companyOwner()->create([
            'email_verified_at' => now(),
        ]);

        $company = config('talenma.hosts.company');
        $www = config('talenma.hosts.www');

        $this->actingAs($companyUser)
            ->get('http://'.$company.'/dashboard')
            ->assertOk()
            ->assertSee('href="'.route('dashboard').'"', false)
            ->assertDontSee('href="'.PortalHost::companyRootUrl().'"', false)
            ->assertDontSee('href="http://'.$www.'"', false);
    }

    public function test_company_portal_header_shows_company_identity(): void
    {
        $companyUser = User::factory()->companyOwner()->create([
            'email_verified_at' => now(),
        ]);

        $company = config('talenma.hosts.company');

        $this->actingAs($companyUser)
            ->get('http://'.$company.'/')
            ->assertRedirect(route('dashboard'));

        $this->actingAs($companyUser)
            ->get('http://'.$company.'/a-propos')
            ->assertOk()
            ->assertSee('data-company-portal-identity', false)
            ->assertSee($companyUser->roleLabel(), false)
            ->assertSee($companyUser->headerDisplayName(), false)
            ->assertSee('href="'.route('dashboard').'"', false)
            ->assertSee('action="'.route('logout').'"', false);
    }

    public function test_talent_cannot_login_on_company_portal(): void
    {
        $talent = User::factory()->talent()->create([
            'email' => 'talent@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $company = config('talenma.hosts.company');

        $this->from('http://'.$company.'/')
            ->post('http://'.$company.'/login', [
                'login_panel' => '1',
                'email' => $talent->email,
                'password' => 'password',
            ])
            ->assertSessionHasErrors('email')
            ->assertRedirect('http://'.$company.'/');

        $this->assertGuest();

        $this->get('http://'.$company.'/')
            ->assertOk()
            ->assertSee('x-data="{ open: true }"', false)
            ->assertSee('value="'.$talent->email.'"', false);
    }

    public function test_company_portal_login_page_redirects_to_home_with_login_panel_open(): void
    {
        $company = config('talenma.hosts.company');

        $this->get('http://'.$company.'/login')
            ->assertRedirect(route('company.offer', ['login' => 1]));

        $this->get(route('company.offer', ['login' => 1]))
            ->assertOk()
            ->assertSee('data-company-login-panel', false)
            ->assertSee('x-data="{ open: true }"', false)
            ->assertSee('action="'.route('login').'"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="remember"', false);
    }

    public function test_company_portal_home_renders_closed_login_panel_by_default(): void
    {
        $company = config('talenma.hosts.company');

        $this->get('http://'.$company.'/')
            ->assertOk()
            ->assertSee('data-company-login-panel', false)
            ->assertSee('x-data="{ open: false }"', false)
            ->assertDontSee('href="'.PortalHost::companyUrl('/login').'"', false);
    }

    public function test_company_can_login_from_portal_login_panel(): void
    {
        $companyUser = User::factory()->companyOwner()->create([
            'email' => 'panel@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $company = config('talenma.hosts.company');

        $this->from('http://'.$company.'/')
            ->post('http://'.$company.'/login', [
                'login_panel' => '1',
                'email' => $companyUser->email,
                'password' => 'password',
            ])
            ->assertRedirect();

        $this->assertAuthenticatedAs($companyUser);
    }

    public function test_www_login_page_is_unchanged(): void
    {
        $www = config('talenma.hosts.www');

        $this->get('http://'.$www.'/login')
            ->assertOk()
            ->assertSee('name="password"', false);
    }

    public function test_company_cannot_login_on_talent_host(): void
    {
        $companyUser = User::factory()->companyOwner()->create([
            'email' => 'co@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $www = config('talenma.hosts.www');

        $this->from('http://'.$www.'/login')
            ->post('http://'.$www.'/login', [
                'email' => $companyUser->email,
                'password' => 'password',
            ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}

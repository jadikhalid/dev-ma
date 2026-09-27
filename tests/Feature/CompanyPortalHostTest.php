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
            ->assertSee(__('talenma.company_offer.cta_trial'), false)
            ->assertSee(__('talenma.nav.company_login'), false)
            ->assertDontSee(__('talenma.nav.jobs'), false);
    }

    public function test_company_dashboard_home_link_targets_company_portal_root(): void
    {
        $companyUser = User::factory()->companyOwner()->create([
            'email_verified_at' => now(),
        ]);

        $company = config('talenma.hosts.company');
        $www = config('talenma.hosts.www');

        $this->actingAs($companyUser)
            ->get('http://'.$company.'/dashboard')
            ->assertOk()
            ->assertSee('href="'.PortalHost::companyRootUrl().'"', false)
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

        $this->from('http://'.$company.'/login')
            ->post('http://'.$company.'/login', [
                'email' => $talent->email,
                'password' => 'password',
            ])
            ->assertSessionHasErrors('email')
            ->assertRedirect('http://'.$company.'/login');

        $this->assertGuest();
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

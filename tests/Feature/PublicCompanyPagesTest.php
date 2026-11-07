<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\User;
use App\Support\PortalHost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicCompanyPagesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function public_company_routes_are_anchored_on_the_talent_host(): void
    {
        $company = $this->makeCompany('ACME Maroc');
        $showUrl = route('companies.public.show', $company, absolute: true);
        $indexUrl = route('companies.public.index', absolute: true);

        $this->assertStringContainsString('://'.PortalHost::wwwHost().'/', $showUrl);
        $this->assertStringContainsString('://'.PortalHost::wwwHost().'/', $indexUrl);
        $this->assertStringNotContainsString(PortalHost::companyHost(), $showUrl);
        $this->assertStringNotContainsString(PortalHost::companyHost(), $indexUrl);

        $companyHostShow = PortalHost::onCompanyHost(
            fn () => route('companies.public.show', $company, absolute: true)
        );
        $this->assertStringContainsString('://'.PortalHost::wwwHost().'/', $companyHostShow);
        $this->assertStringNotContainsString(PortalHost::companyHost(), $companyHostShow);

        $this->get('http://'.PortalHost::companyHost().'/entreprises-partenaires')
            ->assertRedirect(PortalHost::wwwUrl('/entreprises-partenaires'));

        $this->get('http://'.PortalHost::companyHost().'/entreprises-partenaires/'.$company->id.'?utm=1')
            ->assertRedirect(PortalHost::wwwUrl('/entreprises-partenaires/'.$company->id, ['utm' => '1']));
    }

    #[Test]
    public function home_does_not_show_the_partner_companies_block(): void
    {
        $company = $this->makeCompany('ACME Maroc');

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee(__('talenma.home.companies_marquee_accent'), false)
            ->assertDontSee('id="home-companies-title"', false)
            ->assertDontSee(route('companies.public.show', $company), false);
    }

    #[Test]
    public function directory_lists_only_approved_companies_and_filters_by_name(): void
    {
        $this->makeCompany('ACME Maroc');
        $this->makeCompany('Zenith Partners');
        $this->makeCompany('Pending Corp', ['approval_status' => User::APPROVAL_PENDING]);

        $this->get(route('companies.public.index'))
            ->assertOk()
            ->assertSee('ACME Maroc')
            ->assertSee('Zenith Partners')
            ->assertDontSee('Pending Corp')
            ->assertSee(trans_choice('talenma.public_companies.count', 2, ['count' => 2]));

        $this->get(route('companies.public.index', ['q' => 'zenith']))
            ->assertOk()
            ->assertSee('Zenith Partners')
            ->assertDontSee('ACME Maroc');
    }

    #[Test]
    public function company_page_shows_overview_facts_and_published_jobs_only(): void
    {
        $company = $this->makeCompany('ACME Maroc');
        $profile = $company->companyProfile;
        $profile->update([
            'description' => 'Leader marocain du conseil numérique.',
            'hiring_needs' => 'Développeurs Laravel confirmés.',
            'website' => 'https://acme.example.com',
            'employee_count' => '51-200',
            'phone' => '+212600000000',
            'representative_name' => 'Karim Secret',
        ]);

        $published = $this->makeJob($profile, $company, 'Développeur Laravel', JobPosting::STATUS_PUBLISHED);
        $this->makeJob($profile, $company, 'Brouillon caché', JobPosting::STATUS_DRAFT);

        $this->get(route('companies.public.show', $company))
            ->assertOk()
            ->assertSee('ACME Maroc')
            ->assertSee('Leader marocain du conseil numérique.')
            ->assertSee(__('talenma.public_companies.hiring_title'))
            ->assertSee('Développeurs Laravel confirmés.')
            ->assertSee('https://acme.example.com', false)
            ->assertSee('51-200')
            ->assertSee('Développeur Laravel')
            ->assertSee(route('jobs.public.show', $published), false)
            ->assertDontSee('Brouillon caché')
            ->assertDontSee('+212600000000')
            ->assertDontSee('Karim Secret');
    }

    #[Test]
    public function company_page_always_shows_hiring_block_even_when_empty(): void
    {
        $company = $this->makeCompany('ACME Maroc');
        $company->companyProfile->update(['hiring_needs' => null]);

        $this->get(route('companies.public.show', $company))
            ->assertOk()
            ->assertSee(__('talenma.public_companies.hiring_title'))
            ->assertSee(__('talenma.public_companies.hiring_empty'));
    }

    #[Test]
    public function company_page_splits_overview_and_jobs_into_separate_tabs(): void
    {
        $company = $this->makeCompany('ACME Maroc');

        $html = $this->get(route('companies.public.show', $company))
            ->assertOk()
            ->assertSee('role="tablist"', false)
            ->assertSee('data-company-tab="about"', false)
            ->assertSee('data-company-tab="jobs"', false)
            ->getContent();

        $this->assertMatchesRegularExpression('/data-company-panel="about"/', $html);
        $this->assertMatchesRegularExpression('/x-show="tab === \'jobs\'"\s+x-cloak[^>]*data-company-panel="jobs"/', $html);
    }

    #[Test]
    public function company_page_is_not_found_for_hidden_accounts(): void
    {
        $pending = $this->makeCompany('Pending Corp', ['approval_status' => User::APPROVAL_PENDING]);
        $talent = User::factory()->talent()->create();
        $memberWithoutProfile = User::factory()->companyMember()->create(['name' => 'Member']);

        $this->get(route('companies.public.show', $pending))->assertNotFound();
        $this->get(route('companies.public.show', $talent))->assertNotFound();
        $this->get(route('companies.public.show', $memberWithoutProfile))->assertNotFound();
    }

    #[Test]
    public function company_page_ignores_non_http_website(): void
    {
        $company = $this->makeCompany('ACME Maroc');
        $company->companyProfile->update(['website' => 'javascript:alert(1)']);

        $this->get(route('companies.public.show', $company))
            ->assertOk()
            ->assertDontSee('data-company-website', false)
            ->assertDontSee('javascript:alert(1)', false);
    }

    #[Test]
    public function public_header_exposes_companies_directory_after_hiring(): void
    {
        $company = $this->makeCompany('ACME Maroc');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-header-hiring', false)
            ->assertSee('data-header-explore-companies', false)
            ->assertSee('data-header-explore-companies-icon', false)
            ->assertSee('data-header-explore-companies-card', false)
            ->assertDontSee('data-header-explore-companies-disabled', false)
            ->assertSee(__('talenma.nav.explore_companies'))
            ->assertSee(route('companies.public.index'), false);

        $this->get(route('companies.public.index'))
            ->assertOk()
            ->assertSee('page-companies-index', false)
            ->assertSee('data-companies-index-hero', false)
            ->assertSee('how-wash-lilac', false)
            ->assertSee('data-header-explore-companies-disabled', false)
            ->assertSee('aria-disabled="true"', false)
            ->assertSee(__('talenma.nav.explore_companies'));

        $this->get(route('companies.public.show', $company))
            ->assertOk()
            ->assertSee('data-header-explore-companies', false)
            ->assertDontSee('data-header-explore-companies-disabled', false)
            ->assertSee(__('talenma.nav.explore_companies'))
            ->assertDontSee(route('home').'#opportunites', false);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeCompany(string $name, array $attributes = []): User
    {
        $owner = User::factory()->companyOwner()->create(['name' => $name, ...$attributes]);

        CompanyProfile::factory()->create([
            'user_id' => $owner->id,
            'country' => 'ma',
            'city' => 'Casablanca',
        ]);

        return $owner->fresh('companyProfile');
    }

    private function makeJob(CompanyProfile $profile, User $owner, string $title, string $status): JobPosting
    {
        return JobPosting::create([
            'company_profile_id' => $profile->id,
            'created_by' => $owner->id,
            'title' => $title,
            'description' => 'Description de l\'offre.',
            'status' => $status,
            'published_at' => $status === JobPosting::STATUS_PUBLISHED ? now() : null,
            'work_modes' => ['remote'],
            'location_city' => 'Rabat',
        ]);
    }
}

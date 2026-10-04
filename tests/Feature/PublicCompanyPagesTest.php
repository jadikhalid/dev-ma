<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicCompanyPagesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function home_companies_section_links_view_all_and_each_company_page(): void
    {
        $company = $this->makeCompany('ACME Maroc');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-companies-view-all', false)
            ->assertSee(route('companies.public.index'), false)
            ->assertSee(route('companies.public.show', $company), false)
            ->assertSee(__('talenma.home.companies_marquee_view_all'));
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

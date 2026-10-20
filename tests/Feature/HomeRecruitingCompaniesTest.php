<?php

namespace Tests\Feature;

use App\Models\JobPosting;
use App\Models\User;
use App\Services\HomeRecruitingCompanyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HomeRecruitingCompaniesTest extends TestCase
{
    use RefreshDatabase;

    public function test_pick_prefers_recent_advertisers_then_returns_them_as_a_set(): void
    {
        $advertisers = collect([
            ['name' => 'Newest', 'logo_url' => null, 'initials' => 'N', 'published_at' => Carbon::parse('2026-10-19')],
            ['name' => 'Recent', 'logo_url' => null, 'initials' => 'R', 'published_at' => Carbon::parse('2026-10-10')],
            ['name' => 'Older', 'logo_url' => null, 'initials' => 'O', 'published_at' => Carbon::parse('2026-09-01')],
            ['name' => 'Ancient', 'logo_url' => null, 'initials' => 'A', 'published_at' => Carbon::parse('2025-01-01')],
        ]);

        $picked = app(HomeRecruitingCompanyService::class)->pick($advertisers, 3, fn (int $total): int => 1);

        $this->assertEqualsCanonicalizing(
            ['Newest', 'Recent', 'Older'],
            $picked->pluck('name')->all(),
        );
        $this->assertNotContains('Ancient', $picked->pluck('name')->all());
    }

    public function test_guest_card_links_to_public_offers_filtered_by_company(): void
    {
        $this->seedExternalJob('Partner Co', now());
        $this->seedExternalJob('Old Co', now()->subYear());
        JobPosting::query()->create([
            'company_profile_id' => null,
            'created_by' => User::factory()->create(['role' => 'admin', 'approval_status' => null])->id,
            'title' => 'Hidden draft',
            'description' => str_repeat('Draft only. ', 3),
            'status' => JobPosting::STATUS_DRAFT,
            'application_mode' => JobPosting::APPLICATION_EXTERNAL,
            'external_company_name' => 'Draft Co',
        ]);

        $response = $this->get(route('home'))->assertOk()->assertSee(__('talenma.home.recruiting_companies_title'), false);

        $this->assertSeeUrl($response->getContent(), route('career.offers', ['q' => 'Partner Co']));
        $this->assertSeeUrl($response->getContent(), route('career.offers', ['q' => 'Old Co']));
        $response->assertDontSee('Draft Co', false);
    }

    public function test_same_company_is_shown_once_even_with_several_jobs(): void
    {
        $this->seedExternalJob('Partner Co', now()->subDay());
        $this->seedExternalJob('Partner Co', now());

        $response = $this->get(route('home'))->assertOk();

        $this->assertSame(1, substr_count($response->getContent(), route('career.offers', ['q' => 'Partner Co'], false)));
    }

    public function test_cards_use_the_company_account_banner_or_the_external_banner(): void
    {
        $company = User::factory()->companyOwner()->create(['name' => 'Inscrite SA']);
        $profile = $company->companyProfile()->create([
            'country' => 'ma',
            'banner_path' => 'company-banners/inscrite.jpg',
        ]);
        JobPosting::query()->create([
            'company_profile_id' => $profile->id,
            'created_by' => $company->id,
            'title' => 'Poste interne',
            'description' => str_repeat('Opportunite interne pour les talents. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'application_mode' => JobPosting::APPLICATION_INTERNAL,
        ]);

        $external = $this->seedExternalJob('BMW', now()->subHour());
        $external->update(['external_company_banner_path' => 'job-external-banners/bmw.jpg']);

        $older = $this->seedExternalJob('KAUST', now()->subDays(2));
        $newerWithoutBanner = $this->seedExternalJob('KAUST', now()->subDay());
        $older->update(['external_company_banner_path' => 'job-external-banners/kaust.jpg']);

        $this->get(route('home'))->assertOk()->assertSee('item.banner_url', false);

        $cards = app(HomeRecruitingCompanyService::class)->forHome(false)->keyBy('name');

        $this->assertStringContainsString('company-banners/inscrite.jpg', (string) $cards['Inscrite SA']['banner_url']);
        $this->assertStringContainsString('job-external-banners/bmw.jpg', (string) $cards['BMW']['banner_url']);
        $this->assertStringContainsString('job-external-banners/kaust.jpg', (string) $cards['KAUST']['banner_url']);
        $this->assertNull($newerWithoutBanner->external_company_banner_path);
    }

    public function test_approved_talent_card_links_to_talent_jobs_with_the_same_filter(): void
    {
        $this->seedExternalJob('Partner Co', now());
        $talent = User::factory()->talent()->create();

        $content = $this->actingAs($talent)->get(route('home'))->assertOk()->getContent();

        $this->assertSeeUrl($content, route('talent.jobs.index', ['q' => 'Partner Co']));
        $this->assertStringNotContainsString(route('career.offers', ['q' => 'Partner Co'], false), $content);
    }

    private function assertSeeUrl(string $html, string $url): void
    {
        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        $needle = $path.($query !== null && $query !== '' ? '?'.$query : '');

        $this->assertStringContainsString($needle, $html, "URL [{$url}] was not found in the home page.");
    }

    private function seedExternalJob(string $company, Carbon $publishedAt): JobPosting
    {
        return JobPosting::query()->create([
            'company_profile_id' => null,
            'created_by' => User::factory()->create(['role' => 'admin', 'approval_status' => null])->id,
            'title' => 'Role at '.$company.' '.$publishedAt->timestamp,
            'description' => str_repeat('External opportunity for talents. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => $publishedAt,
            'application_mode' => JobPosting::APPLICATION_EXTERNAL,
            'external_company_name' => $company,
            'external_apply_url' => 'https://partner.example/apply',
        ]);
    }
}

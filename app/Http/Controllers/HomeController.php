<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\SocialPost;
use App\Models\User;
use App\Services\CompanyCatalogSearchService;
use App\Services\HeroTalentMosaicService;
use App\Services\ProfessionCatalogService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function __construct(
        private ProfessionCatalogService $professionCatalog,
        private CompanyCatalogSearchService $companyCatalogSearch,
        private HeroTalentMosaicService $heroTalentMosaic,
    ) {}

    public function index()
    {
        $user = auth()->user();
        $canViewProfiles = $user
            && $user->isCompany()
            && $user->isApproved();

        $showCompanySearch = $user
            && $user->isTalent()
            && $user->isApproved();

        $canBrowseTalentJobs = $user
            && $user->isTalent()
            && $user->isApproved();

        $canManageCompanyJobs = $user && $user->canManageJobs();
        $viewerCompanyProfileId = $canManageCompanyJobs
            ? $user->companyOrganization()?->id
            : null;

        $latestJobsIndexUrl = match (true) {
            $canBrowseTalentJobs => route('talent.jobs.index'),
            $canManageCompanyJobs => route('company.jobs.index'),
            default => route('jobs.gate'),
        };

        $latestJobs = $this->latestPublishedJobs($canBrowseTalentJobs, $canManageCompanyJobs, $viewerCompanyProfileId);

        return view('home', [
            'talentsCount' => User::where('role', 'dev')
                ->where('approval_status', User::APPROVAL_APPROVED)
                ->count(),
            'heroTiles' => $this->heroTalentMosaic->tiles(),
            'socialPosts' => SocialPost::forHomeSlider(),
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
            'canViewProfiles' => $canViewProfiles,
            'showCompanySearch' => $showCompanySearch,
            'showCvBuilderAnnouncement' => true,
            'companyCountries' => $showCompanySearch
                ? $this->companyCatalogSearch->availableCountries()
                : [],
            'featuredCompanies' => $this->companyCatalogSearch->featuredForHome(),
            'latestJobs' => $latestJobs,
            'latestJobsIndexUrl' => $latestJobsIndexUrl,
        ]);
    }

    /**
     * @return Collection<int, array{
     *     title: string,
     *     excerpt: string,
     *     company: string,
     *     company_initials: string,
     *     logo_url: ?string,
     *     sector: ?string,
     *     date: string,
     *     url: string
     * }>
     */
    private function latestPublishedJobs(
        bool $canBrowseTalentJobs,
        bool $canManageCompanyJobs,
        ?int $viewerCompanyProfileId,
    ): Collection {
        return JobPosting::query()
            ->with(['companyProfile.user', 'professionSector'])
            ->where('status', JobPosting::STATUS_PUBLISHED)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'title', 'description', 'company_profile_id', 'profession_sector_id', 'published_at', 'created_at'])
            ->map(function (JobPosting $job) use ($canBrowseTalentJobs, $canManageCompanyJobs, $viewerCompanyProfileId) {
                $url = match (true) {
                    $canBrowseTalentJobs => route('talent.jobs.show', $job),
                    $canManageCompanyJobs && $viewerCompanyProfileId === (int) $job->company_profile_id
                        => route('company.jobs.show', $job),
                    $canManageCompanyJobs => route('company.jobs.index'),
                    default => route('jobs.gate', $job),
                };

                $profile = $job->companyProfile;
                $publishedAt = $job->published_at ?? $job->created_at;

                return [
                    'title' => $job->title,
                    'excerpt' => Str::of(strip_tags((string) $job->description))
                        ->squish()
                        ->limit(110)
                        ->toString(),
                    'company' => $profile?->displayName() ?: '—',
                    'company_initials' => $profile?->initials() ?: '—',
                    'logo_url' => $profile?->logoUrl(),
                    'sector' => $job->professionSector?->localizedName() ?: null,
                    'date' => $publishedAt?->translatedFormat('d M Y') ?? '',
                    'url' => $url,
                ];
            });
    }
}

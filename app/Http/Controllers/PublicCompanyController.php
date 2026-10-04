<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\User;
use App\Services\CompanyCatalogSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicCompanyController extends Controller
{
    public function __construct(
        private CompanyCatalogSearchService $companyCatalogSearch,
    ) {}

    public function index(Request $request): View
    {
        $search = Str::limit(trim((string) $request->query('q', '')), 80, '');

        $companies = $this->companyCatalogSearch->publicCompaniesQuery()
            ->with(['companyProfile' => fn ($q) => $q->withCount([
                'jobPostings as open_jobs_count' => fn ($jobs) => $jobs->where('status', JobPosting::STATUS_PUBLISHED),
            ])])
            ->when($search !== '', function ($query) use ($search) {
                $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $search);
                $query->where('name', 'like', '%'.$escaped.'%');
            })
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        $companies->setCollection(
            $companies->getCollection()->map(fn (User $company) => $this->companyCatalogSearch->presentForDirectory($company))
        );

        return view('companies.public-index', [
            'companies' => $companies,
            'search' => $search,
        ]);
    }

    public function show(User $company): View
    {
        abort_unless($this->companyCatalogSearch->isPubliclyVisible($company), 404);

        $company->load('companyProfile.professionSector');
        $profile = $company->companyProfile;

        $jobs = JobPosting::query()
            ->with(['professionSector', 'profession'])
            ->where('company_profile_id', $profile->id)
            ->where('status', JobPosting::STATUS_PUBLISHED)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        $about = trim(strip_tags((string) $profile->description));

        return view('companies.public-show', [
            'company' => $company,
            'profile' => $profile,
            'sector' => $profile->professionSector?->localizedName() ?: ($profile->sector ?: null),
            'location' => collect([$profile->city, $profile->countryLabel()])->filter()->implode(', '),
            'jobs' => $jobs,
            'websiteUrl' => preg_match('#^https?://#i', (string) $profile->website) ? $profile->website : null,
            'metaDescription' => $about !== ''
                ? Str::limit(Str::squish($about), 160)
                : __('talenma.public_companies.meta_show', ['name' => $company->name]),
        ]);
    }
}

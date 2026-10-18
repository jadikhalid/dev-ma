<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CareerPageController extends Controller
{
    public function offers(Request $request): View|Response|RedirectResponse
    {
        $user = auth()->user();
        $search = trim((string) $request->query('q', ''));

        if ($user?->isTalent() && $search !== '') {
            return redirect()->route('talent.jobs.index', ['q' => $search]);
        }

        $canBrowseTalentJobs = $user && $user->isTalent() && $user->isApproved();
        $canManageCompanyJobs = $user && $user->canManageJobs();
        $viewerCompanyProfileId = $canManageCompanyJobs
            ? $user->companyOrganization()?->id
            : null;

        $jobs = JobPosting::query()
            ->with(['companyProfile.user', 'professionSector'])
            ->where('status', JobPosting::STATUS_PUBLISHED)
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $query->where(function ($inner) use ($like) {
                    $inner->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('external_company_name', 'like', $like)
                        ->orWhere('location_city', 'like', $like)
                        ->orWhereHas('companyProfile.user', function ($user) use ($like) {
                            $user->where('name', 'like', $like);
                        });
                });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(function (JobPosting $job) use ($canBrowseTalentJobs, $canManageCompanyJobs, $viewerCompanyProfileId) {
                $url = match (true) {
                    $canBrowseTalentJobs => route('talent.jobs.show', $job),
                    $canManageCompanyJobs && $viewerCompanyProfileId !== null && $viewerCompanyProfileId === (int) $job->company_profile_id
                        => route('company.jobs.show', $job),
                    $canManageCompanyJobs => route('company.jobs.index'),
                    default => route('jobs.public.show', $job),
                };

                $publishedAt = $job->published_at ?? $job->created_at;
                $location = $job->locationLabel();

                return [
                    'title' => $job->title,
                    'excerpt' => Str::of(strip_tags((string) $job->description))->squish()->limit(140)->toString(),
                    'company' => $job->advertiserName(),
                    'company_initials' => $job->advertiserInitials(),
                    'logo_url' => $job->advertiserLogoUrl(),
                    'sector' => $job->professionSector?->localizedName() ?: null,
                    'location' => $location !== '' ? $location : null,
                    'work_modes' => $job->collaborationModeLabels(),
                    'experience' => $job->experienceLabel() ?: null,
                    'date' => $publishedAt?->translatedFormat('d M Y') ?? '',
                    'url' => $url,
                ];
            });

        $payload = [
            'jobs' => $jobs,
            'search' => $search,
        ];

        if ($request->ajax()) {
            return response()->view('career._offers-results', $payload);
        }

        return view('career.offers', $payload);
    }
}

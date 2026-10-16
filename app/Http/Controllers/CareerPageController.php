<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CareerPageController extends Controller
{
    public function offers(Request $request): View
    {
        $user = auth()->user();
        $canBrowseTalentJobs = $user && $user->isTalent() && $user->isApproved();
        $canManageCompanyJobs = $user && $user->canManageJobs();
        $viewerCompanyProfileId = $canManageCompanyJobs
            ? $user->companyOrganization()?->id
            : null;
        $search = trim((string) $request->query('q', ''));

        $jobs = JobPosting::query()
            ->with(['companyProfile.user', 'professionSector'])
            ->where('status', JobPosting::STATUS_PUBLISHED)
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $query->where(function ($inner) use ($like) {
                    $inner->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12)
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

                return [
                    'title' => $job->title,
                    'excerpt' => Str::of(strip_tags((string) $job->description))->squish()->limit(160)->toString(),
                    'company' => $job->advertiserName(),
                    'sector' => $job->professionSector?->localizedName() ?: null,
                    'date' => $publishedAt?->translatedFormat('d M Y') ?? '',
                    'url' => $url,
                ];
            });

        return view('career.offers', ['jobs' => $jobs]);
    }
}

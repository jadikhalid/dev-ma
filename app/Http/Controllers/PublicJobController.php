<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicJobController extends Controller
{
    public function show(Request $request, JobPosting $job): View|RedirectResponse
    {
        abort_unless($job->isPublished() && ! $job->isClosed(), 404);

        $user = $request->user();

        if ($user?->isTalent() && $user->isApproved()) {
            return redirect()->route('talent.jobs.show', $job);
        }

        if ($user?->canManageJobs()) {
            $orgId = $user->companyOrganization()?->id;

            if ($orgId && (int) $job->company_profile_id === (int) $orgId) {
                return redirect()->route('company.jobs.show', $job);
            }
        }

        $job->load(['companyProfile.user', 'professionSector', 'profession']);

        $excerpt = Str::of(strip_tags((string) $job->description))
            ->squish()
            ->limit(160)
            ->toString();

        return view('jobs.public-show', [
            'job' => $job,
            'metaDescription' => $excerpt,
            'authContinueUrl' => route('jobs.gate', $job),
            'registerUrl' => route('register', [
                'role' => 'dev',
                'from_job' => $job->id,
            ]),
            'viewerIsAuthenticated' => $user !== null,
        ]);
    }
}

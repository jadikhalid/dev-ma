<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobAccessGateController extends Controller
{
    /**
     * Send an authenticated user to the role-appropriate jobs page.
     * Guests hit this via home links; auth middleware stores the intended URL for post-login return.
     */
    public function __invoke(Request $request, ?JobPosting $job = null): RedirectResponse
    {
        $user = $request->user();

        if ($user->isTalent() && $user->isApproved()) {
            if ($job?->isPublished()) {
                return redirect()->route('talent.jobs.show', $job);
            }

            return redirect()->route('talent.jobs.index');
        }

        if ($user->canManageJobs()) {
            $orgId = $user->companyOrganization()?->id;

            if ($job && $orgId && (int) $job->company_profile_id === (int) $orgId) {
                return redirect()->route('company.jobs.show', $job);
            }

            return redirect()->route('company.jobs.index');
        }

        return redirect()->route($user->homeRouteName());
    }
}

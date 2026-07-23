<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TalentJobController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isTalent() && $user->isApproved(), 403);

        $jobs = JobPosting::query()
            ->where('status', JobPosting::STATUS_PUBLISHED)
            ->with('companyProfile.user')
            ->latest('published_at')
            ->paginate(15);

        $appliedIds = JobApplication::query()
            ->where('talent_user_id', $user->id)
            ->pluck('job_posting_id')
            ->all();

        return view('talent.jobs.index', compact('jobs', 'appliedIds'));
    }

    public function show(Request $request, JobPosting $job): View
    {
        $user = $request->user();
        abort_unless($user->isTalent() && $user->isApproved(), 403);
        abort_unless($job->isPublished(), 404);

        $job->load('companyProfile.user');

        $application = JobApplication::query()
            ->where('job_posting_id', $job->id)
            ->where('talent_user_id', $user->id)
            ->first();

        return view('talent.jobs.show', compact('job', 'application'));
    }

    public function apply(Request $request, JobPosting $job): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isTalent() && $user->isApproved(), 403);
        abort_unless($job->isPublished(), 404);

        $data = $request->validate([
            'cover_message' => ['nullable', 'string', 'max:2000'],
        ]);

        $existing = JobApplication::query()
            ->where('job_posting_id', $job->id)
            ->where('talent_user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('toast_error', __('talenma.jobs.already_applied'));
        }

        JobApplication::create([
            'job_posting_id' => $job->id,
            'talent_user_id' => $user->id,
            'cover_message' => $data['cover_message'] ?? null,
            'status' => JobApplication::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('talent.jobs.show', $job)
            ->with('toast_success', __('talenma.jobs.applied'));
    }
}

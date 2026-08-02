<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Services\JobPostingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TalentJobController extends Controller
{
    public function __construct(
        private JobPostingService $jobs,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isTalent() && $user->isApproved(), 403);

        $jobs = JobPosting::query()
            ->where(function ($query) use ($user) {
                $query->where('status', JobPosting::STATUS_PUBLISHED)
                    ->orWhereHas('applications', fn ($applications) => $applications->where('talent_user_id', $user->id));
            })
            ->with('companyProfile.user')
            ->latest('published_at')
            ->paginate(15);

        $applications = JobApplication::query()
            ->where('talent_user_id', $user->id)
            ->whereIn('job_posting_id', $jobs->getCollection()->pluck('id'))
            ->get()
            ->keyBy('job_posting_id');

        $appliedIds = $applications->keys()->map(fn ($id) => (int) $id)->all();

        return view('talent.jobs.index', compact('jobs', 'appliedIds', 'applications'));
    }

    public function show(Request $request, JobPosting $job): View
    {
        $user = $request->user();
        abort_unless($user->isTalent() && $user->isApproved(), 403);

        $application = JobApplication::query()
            ->where('job_posting_id', $job->id)
            ->where('talent_user_id', $user->id)
            ->first();

        abort_unless($job->isPublished() || $application !== null, 404);

        $job->load('companyProfile.user');

        if ($application) {
            $this->jobs->markApplicationsSeenForTalent($user, $job);
            $application->talent_seen_at = now();
        }

        return view('talent.jobs.show', compact('job', 'application'));
    }

    public function apply(Request $request, JobPosting $job): JsonResponse|RedirectResponse
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
            $message = __('talenma.jobs.already_applied');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('toast_error', $message);
        }

        JobApplication::create([
            'job_posting_id' => $job->id,
            'talent_user_id' => $user->id,
            'cover_message' => $data['cover_message'] ?? null,
            'status' => JobApplication::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'talent_seen_at' => now(),
        ]);

        $this->jobs->flagUnseenForParties($job);

        $message = __('talenma.jobs.applied');
        $showUrl = route('talent.jobs.show', $job);

        if ($request->expectsJson()) {
            session()->flash('toast_success', $message);

            return response()->json([
                'message' => $message,
                'show_url' => $showUrl,
            ]);
        }

        return redirect()
            ->route('talent.jobs.show', $job)
            ->with('toast_success', $message);
    }
}

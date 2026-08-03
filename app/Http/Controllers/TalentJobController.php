<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\JobPostingActivityEvent;
use App\Services\JobPostingService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TalentJobController extends Controller
{
    public function __construct(
        private JobPostingService $jobs,
        private ProfessionCatalogService $professions,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isTalent() && $user->isApproved(), 403);

        $user->loadMissing('profile');
        $requestedScope = $request->string('scope')->toString();
        $scope = in_array($requestedScope, ['applied', 'closed'], true) ? $requestedScope : 'all';
        $defaultSectorSlug = $this->professions->slugsFromProfile(
            $user->profile?->profession_sector_id,
            null,
        )['sector'];
        $sectorSlug = $request->has('sector')
            ? trim($request->string('sector')->toString())
            : $defaultSectorSlug;
        $professionSlug = trim($request->string('profession')->toString());

        $applySearchFilters = $scope === 'all';
        $activeSectorSlug = $applySearchFilters ? $sectorSlug : '';
        $activeProfessionSlug = $applySearchFilters ? $professionSlug : '';

        $baseQuery = JobPosting::query()
            ->where(function ($query) use ($user) {
                $query->where('status', JobPosting::STATUS_PUBLISHED)
                    ->orWhereHas('applications', fn ($applications) => $applications->where('talent_user_id', $user->id));
            });

        $ownApplications = fn ($applications) => $applications->where('talent_user_id', $user->id);
        $openApplications = fn ($applications) => $ownApplications($applications)
            ->whereNotIn('status', JobApplication::closedStorageStatuses());
        $closedApplications = fn ($applications) => $ownApplications($applications)
            ->whereIn('status', JobApplication::closedStorageStatuses());

        $counts = [
            'all' => (clone $baseQuery)->where('status', JobPosting::STATUS_PUBLISHED)->count(),
            'applied' => (clone $baseQuery)->whereHas('applications', $openApplications)->count(),
            'closed' => (clone $baseQuery)->whereHas('applications', $closedApplications)->count(),
        ];

        $jobsQuery = (clone $baseQuery)
            ->when(
                $scope === 'applied',
                fn ($query) => $query->whereHas('applications', $openApplications),
            )
            ->when(
                $scope === 'closed',
                fn ($query) => $query->whereHas('applications', $closedApplications),
            )
            ->when(
                $scope === 'all',
                fn ($query) => $query->where('status', JobPosting::STATUS_PUBLISHED),
            )
            ->when($activeSectorSlug !== '', function ($query) use ($activeSectorSlug) {
                $query->whereHas(
                    'professionSector',
                    fn ($sector) => $sector->where('slug', $activeSectorSlug)
                );
            })
            ->when($activeProfessionSlug !== '', function ($query) use ($activeProfessionSlug) {
                $query->whereHas(
                    'profession',
                    fn ($profession) => $profession->where('slug', $activeProfessionSlug)
                );
            })
            ->with(['companyProfile.user', 'professionSector', 'profession'])
            ->when(
                in_array($scope, ['applied', 'closed'], true),
                fn ($query) => $query->orderByDesc(
                    JobApplication::query()
                        ->select('submitted_at')
                        ->whereColumn('job_posting_id', 'job_postings.id')
                        ->where('talent_user_id', $user->id)
                        ->limit(1)
                ),
                fn ($query) => $query->latest('published_at'),
            );

        $jobs = $jobsQuery->limit(100)->get();

        $applications = JobApplication::query()
            ->where('talent_user_id', $user->id)
            ->whereIn('job_posting_id', $jobs->pluck('id'))
            ->get()
            ->keyBy('job_posting_id');

        $presented = $jobs->map(fn (JobPosting $job) => $this->presentJobCard(
            $job,
            $applications->get($job->id),
        ))->values();

        if ($request->wantsJson()) {
            return response()->json([
                'scope' => $scope,
                'sector' => $applySearchFilters ? $sectorSlug : $defaultSectorSlug,
                'profession' => $applySearchFilters ? $professionSlug : '',
                'total' => $presented->count(),
                'counts' => $counts,
                'jobs' => $presented,
            ]);
        }

        return view('talent.jobs.index', [
            'jobs' => $presented,
            'scope' => $scope,
            'counts' => $counts,
            'sectorSlug' => $applySearchFilters ? $sectorSlug : $defaultSectorSlug,
            'professionSlug' => $applySearchFilters ? $professionSlug : '',
            'defaultSectorSlug' => $defaultSectorSlug,
            'professionSectors' => $this->professions->sectorsForLocale(),
        ]);
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

        // Closed postings stay visible only to applicants (read-only history).
        if ($job->isClosed() && $application === null) {
            abort(404);
        }

        $job->load(['companyProfile.user', 'professionSector', 'profession']);

        if ($application) {
            $this->jobs->markApplicationsSeenForTalent($user, $job);
            $application->talent_seen_at = now();
        }

        return view('talent.jobs.show', [
            'job' => $job,
            'application' => $application,
            'applicationHistory' => $this->applicationHistoryForTalent($job, $application),
        ]);
    }

    public function apply(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isTalent() && $user->isApproved(), 403);
        abort_unless($job->isPublished() && ! $job->isClosed(), 404);

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
            'status' => JobApplication::STATUS_RECEIVED,
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

    /**
     * @return array{
     *     id: int,
     *     title: string,
     *     url: string,
     *     company: string,
     *     summary: string,
     *     location: string,
     *     applied: bool,
     *     applied_label: string,
     *     application_status: string|null,
     *     application_status_label: string|null,
     *     unseen: bool
     * }
     */
    private function presentJobCard(JobPosting $job, ?JobApplication $application): array
    {
        $summaryParts = array_filter([
            $job->professionSummary(),
            $job->experienceLabel() !== '' ? $job->experienceLabel() : null,
        ]);

        return [
            'id' => $job->id,
            'title' => $job->title,
            'url' => route('talent.jobs.show', $job),
            'company' => $job->companyProfile?->displayName() ?? '—',
            'summary' => implode(' · ', $summaryParts),
            'location' => $job->locationLabel() !== '' ? $job->locationLabel() : '—',
            'applied' => $application !== null,
            'applied_label' => __('talenma.jobs.applied_badge'),
            'application_status' => $application?->status,
            'application_status_label' => $application?->statusLabel(),
            'unseen' => $application?->hasUnseenChangesForTalent() ?? false,
        ];
    }

    /**
     * @return Collection<int, array{at: \Illuminate\Support\Carbon|null, kind: string, label: string, actor: string|null, status: string|null, detail: string|null}>
     */
    private function applicationHistoryForTalent(JobPosting $job, ?JobApplication $application): Collection
    {
        if ($application === null) {
            return collect();
        }

        $application->loadMissing('talent');

        $companyName = $job->companyProfile?->displayName();
        $talentName = $application->talent?->name;
        $history = collect();

        $history->push([
            'at' => $application->submitted_at ?? $application->created_at,
            'kind' => 'submitted',
            'label' => __('talenma.jobs.application_history_submitted'),
            'actor' => null,
            'status' => JobApplication::STATUS_RECEIVED,
            'detail' => null,
        ]);

        $statusEvents = JobPostingActivityEvent::query()
            ->where('job_posting_id', $job->id)
            ->where('event', JobPostingActivityEvent::EVENT_APPLICATION_STATUS)
            ->where(function ($query) use ($application, $job, $talentName) {
                $query->where('talent_user_id', $application->talent_user_id);

                $soleApplicant = JobApplication::query()
                    ->where('job_posting_id', $job->id)
                    ->count() === 1;

                if ($soleApplicant) {
                    $query->orWhereNull('talent_user_id');
                } elseif (filled($talentName)) {
                    $query->orWhere(function ($inner) use ($talentName) {
                        $inner->whereNull('talent_user_id')
                            ->where('actor_label', $talentName);
                    });
                }
            })
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $previousStatus = JobApplication::STATUS_RECEIVED;

        foreach ($statusEvents as $event) {
            $rawStatus = is_string($event->status) ? $event->status : null;
            $status = match ($rawStatus) {
                'submitted', 'received' => JobApplication::STATUS_RECEIVED,
                'reviewed', 'shortlisted', 'viewed' => JobApplication::STATUS_VIEWED,
                'rejected', 'withdrawn', 'closed' => JobApplication::STATUS_CLOSED,
                default => null,
            };

            if ($status === null || $status === $previousStatus) {
                continue;
            }

            $statusLabel = __('talenma.jobs.application_status_'.$status);
            $previousLabel = __('talenma.jobs.application_status_'.$previousStatus);

            $history->push([
                'at' => $event->created_at,
                'kind' => 'status',
                'label' => __('talenma.jobs.application_history_status', [
                    'status' => $statusLabel,
                ]),
                'actor' => $companyName,
                'status' => $status,
                'detail' => __('talenma.jobs.application_history_status_detail', [
                    'from' => $previousLabel,
                    'to' => $statusLabel,
                ]),
            ]);

            $previousStatus = $status;
        }

        if (
            $application->normalizedStatus() !== JobApplication::STATUS_RECEIVED
            && $application->normalizedStatus() !== $previousStatus
        ) {
            $statusLabel = $application->statusLabel();
            $previousLabel = __('talenma.jobs.application_status_'.$previousStatus);

            $history->push([
                'at' => $application->updated_at,
                'kind' => 'status',
                'label' => __('talenma.jobs.application_history_status', [
                    'status' => $statusLabel,
                ]),
                'actor' => $companyName,
                'status' => $application->normalizedStatus(),
                'detail' => __('talenma.jobs.application_history_status_detail', [
                    'from' => $previousLabel,
                    'to' => $statusLabel,
                ]),
            ]);
        }

        return $history->values();
    }
}

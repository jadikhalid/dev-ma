<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\JobPostingActivityEvent;
use App\Services\JobPostingService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyJobController extends Controller
{
    public function __construct(
        private JobPostingService $jobs,
        private ProfessionCatalogService $professions,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        abort_unless($user->canManageJobs(), 403);

        $org = $user->companyOrganization();
        abort_unless($org, 404);

        $requestedScope = $request->string('scope')->toString();
        $scope = in_array($requestedScope, ['mine', 'closed'], true) ? $requestedScope : 'all';
        $defaultSectorSlug = $this->professions->slugsFromProfile($org->profession_sector_id, null)['sector'];
        $sectorSlug = $request->has('sector')
            ? trim($request->string('sector')->toString())
            : $defaultSectorSlug;
        $query = trim($request->string('q')->toString());

        // Search filters apply only to "all" postings — "mine" / "closed" list every matching posting.
        $applySearchFilters = $scope === 'all';
        $activeSectorSlug = $applySearchFilters ? $sectorSlug : '';
        $activeQuery = $applySearchFilters ? $query : '';

        $baseQuery = JobPosting::query()->where('company_profile_id', $org->id);
        $openQuery = (clone $baseQuery)->where('status', '!=', JobPosting::STATUS_CLOSED);

        $counts = [
            'all' => (clone $openQuery)->count(),
            'mine' => (clone $openQuery)->where('created_by', $user->id)->count(),
            'closed' => (clone $baseQuery)
                ->where('status', JobPosting::STATUS_CLOSED)
                ->where('created_by', $user->id)
                ->count(),
        ];

        $jobsQuery = (clone $baseQuery)
            ->when(
                $scope === 'closed',
                fn ($builder) => $builder
                    ->where('status', JobPosting::STATUS_CLOSED)
                    ->where('created_by', $user->id),
                fn ($builder) => $builder->where('status', '!=', JobPosting::STATUS_CLOSED),
            )
            ->when($scope === 'mine', fn ($builder) => $builder->where('created_by', $user->id))
            ->when($activeSectorSlug !== '', function ($builder) use ($activeSectorSlug) {
                $builder->whereHas(
                    'professionSector',
                    fn ($sector) => $sector->where('slug', $activeSectorSlug)
                );
            })
            ->when($activeQuery !== '', function ($builder) use ($activeQuery) {
                $like = '%'.$activeQuery.'%';

                $builder->where(function ($inner) use ($like) {
                    $inner->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('location_city', 'like', $like)
                        ->orWhereHas('profession', function ($profession) use ($like) {
                            $profession->where('name_fr', 'like', $like)
                                ->orWhere('name_en', 'like', $like);
                        })
                        ->orWhereHas('professionSector', function ($sector) use ($like) {
                            $sector->where('name_fr', 'like', $like)
                                ->orWhere('name_en', 'like', $like);
                        });
                });
            })
            ->with(['professionSector', 'profession'])
            ->withCount('applications')
            ->when(
                $scope === 'closed',
                fn ($builder) => $builder->latest('closed_at'),
                fn ($builder) => $builder->latest(),
            );

        if ($request->wantsJson()) {
            $jobs = $jobsQuery->limit(100)->get();

            return response()->json([
                'scope' => $scope,
                'sector' => $applySearchFilters ? $sectorSlug : $defaultSectorSlug,
                'q' => $applySearchFilters ? $query : '',
                'total' => $jobs->count(),
                'counts' => $counts,
                'jobs' => $jobs->map(fn (JobPosting $job) => $this->presentJobCard($job))->values(),
            ]);
        }

        $jobs = $jobsQuery->limit(100)->get()
            ->map(fn (JobPosting $job) => $this->presentJobCard($job))
            ->values();
        $professionSectors = $this->professions->sectorsForLocale();

        return view('company.jobs.index', [
            'jobs' => $jobs,
            'scope' => $scope,
            'counts' => $counts,
            'sectorSlug' => $applySearchFilters ? $sectorSlug : $defaultSectorSlug,
            'defaultSectorSlug' => $defaultSectorSlug,
            'query' => $applySearchFilters ? $query : '',
            'professionSectors' => $professionSectors,
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->canManageJobs(), 403);

        $org = $user->companyOrganization();
        $slugs = $this->professions->slugsFromProfile($org?->profession_sector_id, null);

        return view('company.jobs.form', $this->formViewData(new JobPosting([
            'status' => JobPosting::STATUS_DRAFT,
            'remote_ok' => false,
            'location_country' => CompanyProfile::DEFAULT_COUNTRY,
            'profession_sector_id' => $org?->profession_sector_id,
        ]), $slugs['sector'], $slugs['profession']));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->canManageJobs(), 403);
        $org = $user->companyOrganization();
        abort_unless($org, 404);

        $data = $this->validated($request);

        $job = JobPosting::create([
            ...$data,
            'company_profile_id' => $org->id,
            'created_by' => $user->id,
            'status' => JobPosting::STATUS_DRAFT,
            'company_seen_at' => now(),
            'staff_seen_at' => now(),
        ]);

        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_CREATED, $user, JobPosting::STATUS_DRAFT);

        $message = __('talenma.jobs.created');

        return $this->respond($request, $message, route('company.jobs.show', $job));
    }

    public function show(Request $request, JobPosting $job): View
    {
        $this->authorizeJob($request, $job);
        $this->jobs->markSeenForCompany($request->user(), $job);

        $job->load(['applications.talent.profile', 'creator', 'professionSector', 'profession']);

        $applicationsTotal = $job->applications->count();
        $matchingApplications = $job->applications
            ->filter(fn (JobApplication $application) => $job->matchesTalentProfile($application->talent))
            ->values();

        return view('company.jobs.show', [
            'job' => $job,
            'matchingApplications' => $matchingApplications,
            'applicationsTotal' => $applicationsTotal,
        ]);
    }

    public function edit(Request $request, JobPosting $job): View|RedirectResponse
    {
        $this->authorizeJob($request, $job);

        if ($job->isClosed()) {
            return redirect()
                ->route('company.jobs.show', $job)
                ->with('toast_error', __('talenma.jobs.closed_immutable'));
        }

        $slugs = $this->professions->slugsFromProfile($job->profession_sector_id, $job->profession_id);

        return view('company.jobs.form', $this->formViewData($job, $slugs['sector'], $slugs['profession']));
    }

    public function update(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        $this->authorizeJob($request, $job);

        if ($response = $this->rejectIfClosed($request, $job)) {
            return $response;
        }

        $job->update($this->validated($request));
        $this->jobs->acknowledgeForCompany($job);

        $message = __('talenma.jobs.updated');

        return $this->respond($request, $message, route('company.jobs.show', $job));
    }

    public function publish(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        $this->authorizeJob($request, $job);

        if ($response = $this->rejectIfClosed($request, $job)) {
            return $response;
        }

        $job->update([
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => $job->published_at ?? now(),
            'closed_at' => null,
        ]);

        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_PUBLISHED, $request->user(), JobPosting::STATUS_PUBLISHED);
        $this->jobs->acknowledgeForCompany($job);

        return $this->respond($request, __('talenma.jobs.published'), reload: true);
    }

    public function close(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        $this->authorizeJob($request, $job);

        if ($response = $this->rejectIfClosed($request, $job)) {
            return $response;
        }

        $job->update([
            'status' => JobPosting::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_CLOSED, $request->user(), JobPosting::STATUS_CLOSED);
        $this->jobs->closeAllApplications($job, $request->user());
        $this->jobs->acknowledgeForCompany($job);

        return $this->respond($request, __('talenma.jobs.closed'), reload: true);
    }

    public function hide(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        $this->authorizeJob($request, $job);

        if ($response = $this->rejectIfClosed($request, $job)) {
            return $response;
        }

        $job->update([
            'status' => JobPosting::STATUS_HIDDEN,
            'closed_at' => null,
        ]);

        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_HIDDEN, $request->user(), JobPosting::STATUS_HIDDEN);
        $this->jobs->flagApplicantsUnseen($job);
        $this->jobs->acknowledgeForCompany($job);

        return $this->respond($request, __('talenma.jobs.hidden'), reload: true);
    }

    public function updateApplication(Request $request, JobPosting $job, JobApplication $application): JsonResponse|RedirectResponse
    {
        $this->authorizeJob($request, $job);
        abort_unless($application->job_posting_id === $job->id, 404);

        if ($response = $this->rejectIfClosed($request, $job)) {
            return $response;
        }

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(JobApplication::STATUSES)],
        ]);

        $current = $application->normalizedStatus();
        $next = $data['status'];

        if ($next === $current) {
            return $this->respond($request, __('talenma.jobs.application_updated'));
        }

        if (! $application->canTransitionTo($next)) {
            $message = __('talenma.jobs.application_status_irreversible');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('toast_error', $message);
        }

        $application->update(['status' => $next]);
        $application->loadMissing('talent');

        JobPostingActivityEvent::record(
            $job,
            JobPostingActivityEvent::EVENT_APPLICATION_STATUS,
            $request->user(),
            $next,
            $application->talent?->name,
            true,
            (int) $application->talent_user_id,
        );
        $this->jobs->flagUnseenForTalentApplication($application);
        $this->jobs->acknowledgeForCompany($job);

        return $this->respond($request, __('talenma.jobs.application_updated'), reload: true);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50', 'max:10000'],
            'sector' => ['required', 'string', 'max:100'],
            'profession' => ['required', 'string', 'max:100'],
            'experience_level' => ['required', 'string', Rule::in(JobPosting::EXPERIENCE_LEVELS)],
            'contract_type' => ['nullable', 'string', Rule::in(JobPosting::CONTRACT_TYPES)],
            'location_country' => ['nullable', 'string', Rule::in(CompanyProfile::COUNTRY_CODES)],
            'location_city' => [
                'nullable',
                'string',
                'max:100',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    if (! filled($value)) {
                        return;
                    }

                    $country = $request->input('location_country');
                    $allowed = CompanyProfile::citiesForCountry(is_string($country) ? $country : null);

                    if ($allowed === [] || ! in_array($value, $allowed, true)) {
                        $fail(__('talenma.company.city_invalid'));
                    }
                },
            ],
            'remote_ok' => ['nullable', 'boolean'],
        ]);

        $resolved = $this->professions->resolveSelection(
            $data['sector'],
            $data['profession'],
            null,
        );

        unset($data['sector'], $data['profession']);

        $data['profession_sector_id'] = $resolved['profession_sector_id'];
        $data['profession_id'] = $resolved['profession_id'];
        $data['remote_ok'] = $request->boolean('remote_ok');

        return $data;
    }

    /**
     * @return array{
     *     id: int,
     *     title: string,
     *     url: string,
     *     status: string,
     *     status_label: string,
     *     summary: string,
     *     unseen: bool,
     *     tone: array{bar: string, badge: string, hover: string}
     * }
     */
    private function presentJobCard(JobPosting $job): array
    {
        $summaryParts = array_filter([
            $job->professionSummary(),
            $job->experienceLabel() !== '' ? $job->experienceLabel() : null,
            __('talenma.jobs.applications_count', ['count' => $job->applications_count]),
            $job->locationLabel() !== '' ? $job->locationLabel() : null,
            $job->remote_ok ? __('talenma.jobs.remote') : null,
        ]);

        return [
            'id' => $job->id,
            'title' => $job->title,
            'url' => route('company.jobs.show', $job),
            'status' => $job->status,
            'status_label' => $job->statusLabel(),
            'summary' => implode(' · ', $summaryParts),
            'unseen' => $job->hasUnseenChangesForCompany(),
            'tone' => match ($job->status) {
                JobPosting::STATUS_PUBLISHED => [
                    'bar' => 'bg-emerald-500',
                    'badge' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
                    'hover' => 'hover:ring-emerald-200 hover:bg-emerald-50/40',
                ],
                JobPosting::STATUS_CLOSED => [
                    'bar' => 'bg-slate-400',
                    'badge' => 'bg-slate-100 text-slate-700 ring-slate-200',
                    'hover' => 'hover:ring-slate-300 hover:bg-slate-50',
                ],
                JobPosting::STATUS_HIDDEN => [
                    'bar' => 'bg-slate-500',
                    'badge' => 'bg-slate-100 text-slate-800 ring-slate-200',
                    'hover' => 'hover:ring-slate-300 hover:bg-slate-50',
                ],
                JobPosting::STATUS_POSTPONED => [
                    'bar' => 'bg-violet-500',
                    'badge' => 'bg-violet-50 text-violet-800 ring-violet-200',
                    'hover' => 'hover:ring-violet-200 hover:bg-violet-50/40',
                ],
                default => [
                    'bar' => 'bg-amber-500',
                    'badge' => 'bg-amber-50 text-amber-900 ring-amber-200',
                    'hover' => 'hover:ring-amber-200 hover:bg-amber-50/40',
                ],
            },
        ];
    }

    /**
     * @return array{
     *     job: JobPosting,
     *     countryOptions: array<string, string>,
     *     citiesByCountry: array<string, list<string>>,
     *     professionSectors: \Illuminate\Support\Collection,
     *     sectorSlug: string,
     *     professionSlug: string
     * }
     */
    private function formViewData(JobPosting $job, string $sectorSlug = '', string $professionSlug = ''): array
    {
        return [
            'job' => $job,
            'countryOptions' => CompanyProfile::countryOptions(),
            'citiesByCountry' => CompanyProfile::citiesByCountry(),
            'professionSectors' => $this->professions->sectorsForLocale(),
            'sectorSlug' => old('sector', $sectorSlug),
            'professionSlug' => old('profession', $professionSlug),
        ];
    }

    private function authorizeJob(Request $request, JobPosting $job): void
    {
        $user = $request->user();
        abort_unless($user->canManageJobs(), 403);

        $org = $user->companyOrganization();
        abort_unless($org && $job->company_profile_id === $org->id, 403);
    }

    private function rejectIfClosed(Request $request, JobPosting $job): JsonResponse|RedirectResponse|null
    {
        if ($job->isMutable()) {
            return null;
        }

        $message = __('talenma.jobs.closed_immutable');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->with('toast_error', $message);
    }

    private function respond(Request $request, string $message, ?string $showUrl = null, bool $reload = false): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            if ($showUrl !== null) {
                session()->flash('toast_success', $message);

                return response()->json([
                    'message' => $message,
                    'show_url' => $showUrl,
                ]);
            }

            $payload = ['message' => $message];

            if ($reload) {
                $payload['reload'] = true;
            }

            return response()->json($payload);
        }

        if ($showUrl !== null) {
            return redirect()->to($showUrl)->with('toast_success', $message);
        }

        return back()->with('toast_success', $message);
    }
}

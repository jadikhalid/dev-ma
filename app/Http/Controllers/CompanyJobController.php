<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\JobPostingActivityEvent;
use App\Models\Profile;
use App\Services\CompanyJobPromptParser;
use App\Services\JobPostingService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class CompanyJobController extends Controller
{
    public function __construct(
        private JobPostingService $jobs,
        private ProfessionCatalogService $professions,
        private CompanyJobPromptParser $jobPromptParser,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        abort_unless($user->canManageJobs(), 403);

        $org = $user->companyOrganization();
        abort_unless($org, 404);

        $requestedScope = $request->string('scope')->toString();
        $scope = in_array($requestedScope, ['company', 'company_closed', 'mine', 'closed'], true)
            ? $requestedScope
            : 'company';
        $filters = $this->searchFiltersFromRequest($request);

        $orgQuery = JobPosting::query()->where('company_profile_id', $org->id);
        $orgOpenQuery = (clone $orgQuery)->where('status', '!=', JobPosting::STATUS_CLOSED);

        $counts = [
            'company' => (clone $orgOpenQuery)->count(),
            'company_closed' => (clone $orgQuery)->where('status', JobPosting::STATUS_CLOSED)->count(),
            'mine' => (clone $orgOpenQuery)->where('created_by', $user->id)->count(),
            'closed' => (clone $orgQuery)
                ->where('status', JobPosting::STATUS_CLOSED)
                ->where('created_by', $user->id)
                ->count(),
        ];

        $jobsQuery = match ($scope) {
            'company_closed' => (clone $orgQuery)->where('status', JobPosting::STATUS_CLOSED),
            'mine' => (clone $orgOpenQuery)->where('created_by', $user->id),
            'closed' => (clone $orgQuery)
                ->where('status', JobPosting::STATUS_CLOSED)
                ->where('created_by', $user->id),
            default => (clone $orgOpenQuery),
        };

        $this->applySearchFilters($jobsQuery, $filters);

        $jobsQuery
            ->with(['professionSector', 'profession', 'creator'])
            ->withCount('applications')
            ->when(
                in_array($scope, ['company_closed', 'closed'], true),
                fn ($builder) => $builder->latest('closed_at'),
                fn ($builder) => $builder->latest(),
            );

        $present = fn (JobPosting $job) => $this->presentJobCard($job);

        if ($request->wantsJson()) {
            $jobs = $jobsQuery->limit(100)->get();

            return response()->json([
                'scope' => $scope,
                'filters' => $filters,
                'total' => $jobs->count(),
                'counts' => $counts,
                'jobs' => $jobs->map($present)->values(),
            ]);
        }

        $jobs = $jobsQuery->limit(100)->get()
            ->map($present)
            ->values();

        return view('company.jobs.index', [
            'jobs' => $jobs,
            'scope' => $scope,
            'counts' => $counts,
            'filters' => $filters,
        ]);
    }

    public function parseAiFilters(Request $request): JsonResponse
    {
        abort_unless($request->user()?->canManageJobs(), 403);

        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:'.CompanyJobPromptParser::MAX_PROMPT_LENGTH],
        ]);

        if (function_exists('set_time_limit')) {
            @set_time_limit(45);
        }

        try {
            $filters = $this->jobPromptParser->parse($validated['prompt'], app()->getLocale());
        } catch (RuntimeException $e) {
            $status = match ($e->getMessage()) {
                'not_configured', 'api_unreachable', 'api_error', 'empty_response', 'invalid_json' => 503,
                'empty_prompt' => 422,
                default => 503,
            };

            return response()->json([
                'message' => __('talenma.jobs.ai_filters_error'),
                'code' => $e->getMessage(),
            ], $status);
        }

        return response()->json([
            'filters' => $filters,
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
            'work_modes' => [],
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

        $user = $request->user();
        $this->jobs->markSeenForCompany($user, $job);
        $job->load(['applications.talent.profile', 'creator', 'professionSector', 'profession', 'companyProfile.user']);

        $applicationsTotal = $job->applications->count();
        $matchingApplications = $job->applications
            ->filter(fn (JobApplication $application) => $job->matchesTalentProfile($application->talent))
            ->values();

        return view('company.jobs.show', [
            'job' => $job,
            'ownsJob' => true,
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

        if (! $job->isPublished()) {
            $org = $request->user()->companyOrganization();

            if ($org && ! $org->canPublishAnotherJob()) {
                $message = __('talenma.jobs.active_limit_reached', [
                    'limit' => $org->activeJobLimit(),
                ]);

                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['message' => $message], 422);
                }

                return redirect()
                    ->route('company.jobs.show', $job)
                    ->with('toast_error', $message);
            }
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
            'profession' => ['nullable', 'string', 'max:100'],
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
            'work_modes' => ['required', 'array', 'min:1'],
            'work_modes.*' => ['string', Rule::in(array_keys(Profile::workModeOptions()))],
        ]);

        $resolved = $this->professions->resolveJobSelection($data['sector'], $data['profession'] ?? null);

        unset($data['sector'], $data['profession']);

        $data['profession_sector_id'] = $resolved['profession_sector_id'];
        $data['profession_id'] = $resolved['profession_id'];
        $data['work_modes'] = array_values(array_unique($data['work_modes']));
        $data['remote_ok'] = in_array('remote', $data['work_modes'], true);

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
     *     creator: string|null,
     *     published_label: string|null,
     *     closed_label: string|null,
     *     unseen: bool,
     *     tone: array{bar: string, badge: string, hover: string}
     * }
     */
    private function presentJobCard(JobPosting $job): array
    {
        $attribution = $job->creatorAttribution();
        $creatorLabel = filled($attribution['person']) ? $attribution['person'] : null;
        $publishedAt = $job->published_at;

        $summaryParts = array_filter([
            $job->professionSummary(),
            $job->experienceLabel() !== '' ? $job->experienceLabel() : null,
            __('talenma.jobs.applications_count', ['count' => $job->applications_count]),
            $job->locationLabel() !== '' ? $job->locationLabel() : null,
            $job->workModesSummary() !== '' ? $job->workModesSummary() : null,
        ]);

        return [
            'id' => $job->id,
            'title' => $job->title,
            'url' => route('company.jobs.show', $job),
            'status' => $job->status,
            'status_label' => $job->statusLabel(),
            'summary' => implode(' · ', $summaryParts),
            'creator' => $creatorLabel,
            'published_label' => $publishedAt
                ? __('talenma.jobs.card_published_on', ['date' => $publishedAt->translatedFormat('d M Y')])
                : null,
            'closed_label' => $job->closed_at
                ? __('talenma.jobs.card_closed_on', ['date' => $job->closed_at->translatedFormat('d M Y')])
                : null,
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
            'workModeOptions' => Profile::workModeOptions(),
        ];
    }

    private function authorizeJob(Request $request, JobPosting $job): void
    {
        $user = $request->user();
        abort_unless($user->canManageJobs(), 403);

        $org = $user->companyOrganization();
        abort_unless($org && $job->company_profile_id === $org->id, 403);
    }

    /**
     * @return array{
     *     keywords: list<string>,
     *     author: string,
     *     sector: string,
     *     profession: string,
     *     country: string,
     *     city: string,
     *     day: int|null,
     *     month: int|null,
     *     year: int|null
     * }
     */
    private function searchFiltersFromRequest(Request $request): array
    {
        $keywords = $request->input('keywords', []);
        if (is_string($keywords)) {
            $keywords = preg_split('/\s*,\s*/', $keywords) ?: [];
        }
        if (! is_array($keywords)) {
            $keywords = [];
        }

        $keywords = array_values(array_filter(array_map(
            fn ($item) => is_string($item) || is_numeric($item) ? trim((string) $item) : '',
            $keywords
        )));

        $day = $request->filled('day') && is_numeric($request->input('day')) ? (int) $request->input('day') : null;
        $month = $request->filled('month') && is_numeric($request->input('month')) ? (int) $request->input('month') : null;
        $year = $request->filled('year') && is_numeric($request->input('year')) ? (int) $request->input('year') : null;

        return [
            'keywords' => array_slice($keywords, 0, CompanyJobPromptParser::MAX_KEYWORDS),
            'author' => trim($request->string('author')->toString()),
            'sector' => trim($request->string('sector')->toString()),
            'profession' => trim($request->string('profession')->toString()),
            'country' => trim($request->string('country')->toString()),
            'city' => trim($request->string('city')->toString()),
            'day' => ($day !== null && $day >= 1 && $day <= 31) ? $day : null,
            'month' => ($month !== null && $month >= 1 && $month <= 12) ? $month : null,
            'year' => ($year !== null && $year >= 2000 && $year <= 2100) ? $year : null,
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\JobPosting>  $builder
     * @param  array{
     *     keywords: list<string>,
     *     author: string,
     *     sector: string,
     *     profession: string,
     *     country: string,
     *     city: string,
     *     day: int|null,
     *     month: int|null,
     *     year: int|null
     * }  $filters
     */
    private function applySearchFilters($builder, array $filters): void
    {
        foreach ($filters['keywords'] as $keyword) {
            $like = '%'.$keyword.'%';

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
        }

        if ($filters['author'] !== '') {
            $like = '%'.$filters['author'].'%';

            $builder->whereHas('creator', function ($creator) use ($like) {
                $creator->where('name', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like);
            });
        }

        if ($filters['sector'] !== '') {
            $builder->whereHas(
                'professionSector',
                fn ($sector) => $sector->where('slug', $filters['sector'])
            );
        }

        if ($filters['profession'] !== '') {
            $builder->whereHas(
                'profession',
                fn ($profession) => $profession->where('slug', $filters['profession'])
            );
        }

        if ($filters['country'] !== '') {
            $builder->where('location_country', $filters['country']);
        }

        if ($filters['city'] !== '') {
            $builder->where('location_city', $filters['city']);
        }

        $day = $filters['day'];
        $month = $filters['month'];
        $year = $filters['year'];

        if ($day === null && $month === null && $year === null) {
            return;
        }

        $ambiguousDayOrMonth = $day !== null
            && $month === null
            && $year === null
            && $day >= 1
            && $day <= 12;

        $builder->where(function ($dateGroup) use ($day, $month, $year, $ambiguousDayOrMonth) {
            foreach (['published_at', 'closed_at'] as $column) {
                $dateGroup->orWhere(function ($columnQuery) use ($column, $day, $month, $year, $ambiguousDayOrMonth) {
                    $columnQuery->whereNotNull($column);

                    if ($year !== null) {
                        $columnQuery->whereYear($column, $year);
                    }

                    if ($month !== null) {
                        $columnQuery->whereMonth($column, $month);
                    }

                    if ($ambiguousDayOrMonth) {
                        $columnQuery->where(function ($either) use ($column, $day) {
                            $either->whereDay($column, $day)
                                ->orWhereMonth($column, $day);
                        });
                    } elseif ($day !== null) {
                        $columnQuery->whereDay($column, $day);
                    }
                });
            }
        });
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

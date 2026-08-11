<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\JobPostingActivityEvent;
use App\Models\Profile;
use App\Services\JobPostingService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobPostingController extends Controller
{
    public function __construct(
        private JobPostingService $jobs,
        private ProfessionCatalogService $professions,
    ) {}

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $q = trim($request->string('q')->toString());

        $jobs = JobPosting::query()
            ->with(['companyProfile.user', 'creator', 'professionSector', 'profession'])
            ->withCount('applications')
            ->when(
                $status !== '' && in_array($status, JobPosting::STATUSES, true),
                fn ($query) => $query->where('status', $status)
            )
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('title', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%')
                        ->orWhereHas('companyProfile', function ($company) use ($q): void {
                            $company->where('representative_name', 'like', '%'.$q.'%')
                                ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$q.'%')
                                    ->orWhere('email', 'like', '%'.$q.'%'));
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.jobs.index', [
            'jobs' => $jobs,
            'status' => $status,
            'q' => $q,
            'statuses' => JobPosting::STATUSES,
        ]);
    }

    public function show(Request $request, JobPosting $job): View
    {
        $this->jobs->markSeenForStaff($request->user(), $job);
        $job->load(['applications.talent.profile', 'creator', 'companyProfile.user', 'professionSector', 'profession']);

        return view('admin.jobs.show', compact('job'));
    }

    public function edit(JobPosting $job): View|RedirectResponse
    {
        if ($job->isClosed()) {
            return redirect()
                ->route('admin.jobs.show', $job)
                ->with('toast_error', __('talenma.jobs.closed_immutable'));
        }

        $slugs = $this->professions->slugsFromProfile($job->profession_sector_id, $job->profession_id);

        return view('admin.jobs.form', [
            'job' => $job,
            'countryOptions' => CompanyProfile::countryOptions(),
            'citiesByCountry' => CompanyProfile::citiesByCountry(),
            'professionSectors' => $this->professions->sectorsForLocale(),
            'sectorSlug' => old('sector', $slugs['sector']),
            'professionSlug' => old('profession', $slugs['profession']),
            'workModeOptions' => Profile::workModeOptions(),
        ]);
    }

    public function update(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        if ($response = $this->rejectIfClosed($request, $job)) {
            return $response;
        }

        $job->update($this->validated($request));
        $this->jobs->flagUnseenForCompanyFromStaff($job);

        $message = __('talenma.jobs.updated');

        return $this->respond($request, $message, route('admin.jobs.show', $job));
    }

    public function publish(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        if ($response = $this->rejectIfClosed($request, $job)) {
            return $response;
        }

        $job->update([
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => $job->published_at ?? now(),
            'closed_at' => null,
        ]);

        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_PUBLISHED, $request->user(), JobPosting::STATUS_PUBLISHED, null, false);
        $this->jobs->flagUnseenForCompanyFromStaff($job);

        return $this->respond($request, __('talenma.jobs.published'), reload: true);
    }

    public function close(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        if ($response = $this->rejectIfClosed($request, $job)) {
            return $response;
        }

        $job->update([
            'status' => JobPosting::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_CLOSED, $request->user(), JobPosting::STATUS_CLOSED, null, false);
        $this->jobs->closeAllApplications($job, $request->user());
        $this->jobs->flagUnseenForCompanyFromStaff($job);

        return $this->respond($request, __('talenma.jobs.closed'), reload: true);
    }

    public function hide(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        if ($response = $this->rejectIfClosed($request, $job)) {
            return $response;
        }

        $job->update([
            'status' => JobPosting::STATUS_HIDDEN,
            'closed_at' => null,
        ]);

        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_HIDDEN, $request->user(), JobPosting::STATUS_HIDDEN, null, false);
        $this->jobs->flagApplicantsUnseen($job);
        $this->jobs->flagUnseenForCompanyFromStaff($job);

        return $this->respond($request, __('talenma.jobs.hidden'), reload: true);
    }

    public function destroy(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        // Admin wants permanent deletion (remove every related trace).
        $this->jobs->purgeCompletely($job);

        return $this->respond(
            $request,
            __('talenma.jobs.deleted'),
            route('admin.jobs.index'),
        );
    }

    public function updateApplication(Request $request, JobPosting $job, JobApplication $application): JsonResponse|RedirectResponse
    {
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
            false,
            (int) $application->talent_user_id,
        );
        $this->jobs->flagUnseenForTalentApplication($application);
        $this->jobs->flagUnseenForCompanyFromStaff($job);

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
            'work_modes' => ['required', 'array', 'min:1'],
            'work_modes.*' => ['string', Rule::in(array_keys(Profile::workModeOptions()))],
        ]);

        $resolved = $this->professions->resolveSelection(
            $data['sector'],
            $data['profession'],
            null,
        );

        unset($data['sector'], $data['profession']);

        $data['profession_sector_id'] = $resolved['profession_sector_id'];
        $data['profession_id'] = $resolved['profession_id'];
        $data['work_modes'] = array_values(array_unique($data['work_modes']));
        $data['remote_ok'] = in_array('remote', $data['work_modes'], true);

        return $data;
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

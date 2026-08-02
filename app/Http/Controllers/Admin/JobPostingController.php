<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\JobPostingActivityEvent;
use App\Services\JobPostingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobPostingController extends Controller
{
    public function __construct(
        private JobPostingService $jobs,
    ) {}

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $q = trim($request->string('q')->toString());

        $jobs = JobPosting::query()
            ->with(['companyProfile.user', 'creator'])
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
        $job->load(['applications.talent.profile', 'creator', 'companyProfile.user']);

        return view('admin.jobs.show', compact('job'));
    }

    public function edit(JobPosting $job): View
    {
        return view('admin.jobs.form', [
            'job' => $job,
            'countryOptions' => CompanyProfile::countryOptions(),
            'citiesByCountry' => CompanyProfile::citiesByCountry(),
        ]);
    }

    public function update(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        $job->update($this->validated($request));
        $this->jobs->flagUnseenForCompanyFromStaff($job);

        $message = __('talenma.jobs.updated');

        return $this->respond($request, $message, route('admin.jobs.show', $job));
    }

    public function publish(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
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
        $job->update([
            'status' => JobPosting::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_CLOSED, $request->user(), JobPosting::STATUS_CLOSED, null, false);
        $this->jobs->flagApplicantsUnseen($job);
        $this->jobs->flagUnseenForCompanyFromStaff($job);

        return $this->respond($request, __('talenma.jobs.closed'), reload: true);
    }

    public function hide(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        $job->update([
            'status' => JobPosting::STATUS_HIDDEN,
            'closed_at' => null,
        ]);

        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_HIDDEN, $request->user(), JobPosting::STATUS_HIDDEN, null, false);
        $this->jobs->flagApplicantsUnseen($job);
        $this->jobs->flagUnseenForCompanyFromStaff($job);

        return $this->respond($request, __('talenma.jobs.hidden'), reload: true);
    }

    public function postpone(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        $job->update([
            'status' => JobPosting::STATUS_POSTPONED,
            'closed_at' => null,
        ]);

        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_POSTPONED, $request->user(), JobPosting::STATUS_POSTPONED, null, false);
        $this->jobs->flagApplicantsUnseen($job);
        $this->jobs->flagUnseenForCompanyFromStaff($job);

        return $this->respond($request, __('talenma.jobs.postponed'), reload: true);
    }

    public function destroy(Request $request, JobPosting $job): JsonResponse|RedirectResponse
    {
        JobPostingActivityEvent::record($job, JobPostingActivityEvent::EVENT_DELETED, $request->user(), $job->status, null, false);
        $this->jobs->recordDeletedForApplicants($job, $request->user());

        $job->delete();

        $message = __('talenma.jobs.deleted');

        return $this->respond($request, $message, route('admin.jobs.index'));
    }

    public function updateApplication(Request $request, JobPosting $job, JobApplication $application): JsonResponse|RedirectResponse
    {
        abort_unless($application->job_posting_id === $job->id, 404);

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(JobApplication::STATUSES)],
        ]);

        $application->update(['status' => $data['status']]);
        $application->loadMissing('talent');

        JobPostingActivityEvent::record(
            $job,
            JobPostingActivityEvent::EVENT_APPLICATION_STATUS,
            $request->user(),
            $data['status'],
            $application->talent?->name,
            false,
            (int) $application->talent_user_id,
        );
        $this->jobs->flagUnseenForTalentApplication($application);
        $this->jobs->flagUnseenForCompanyFromStaff($job);

        return $this->respond($request, __('talenma.jobs.application_updated'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50', 'max:10000'],
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

        $data['remote_ok'] = $request->boolean('remote_ok');

        return $data;
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

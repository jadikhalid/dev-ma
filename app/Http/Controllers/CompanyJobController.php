<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyJobController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->canManageJobs(), 403);

        $org = $user->companyOrganization();
        abort_unless($org, 404);

        $jobs = JobPosting::query()
            ->where('company_profile_id', $org->id)
            ->withCount('applications')
            ->latest()
            ->paginate(15);

        return view('company.jobs.index', compact('jobs', 'org'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->canManageJobs(), 403);

        return view('company.jobs.form', [
            'job' => new JobPosting([
                'status' => JobPosting::STATUS_DRAFT,
                'remote_ok' => false,
                'location_country' => CompanyProfile::DEFAULT_COUNTRY,
            ]),
            'countryOptions' => CompanyProfile::countryOptions(),
            'citiesByCountry' => CompanyProfile::citiesByCountry(),
        ]);
    }

    public function store(Request $request): RedirectResponse
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
        ]);

        return redirect()
            ->route('company.jobs.show', $job)
            ->with('toast_success', __('talenma.jobs.created'));
    }

    public function show(Request $request, JobPosting $job): View
    {
        $this->authorizeJob($request, $job);

        $job->load(['applications.talent.profile', 'creator']);

        return view('company.jobs.show', compact('job'));
    }

    public function edit(Request $request, JobPosting $job): View
    {
        $this->authorizeJob($request, $job);

        return view('company.jobs.form', [
            'job' => $job,
            'countryOptions' => CompanyProfile::countryOptions(),
            'citiesByCountry' => CompanyProfile::citiesByCountry(),
        ]);
    }

    public function update(Request $request, JobPosting $job): RedirectResponse
    {
        $this->authorizeJob($request, $job);

        $job->update($this->validated($request));

        return redirect()
            ->route('company.jobs.show', $job)
            ->with('toast_success', __('talenma.jobs.updated'));
    }

    public function publish(Request $request, JobPosting $job): RedirectResponse
    {
        $this->authorizeJob($request, $job);

        $job->update([
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => $job->published_at ?? now(),
            'closed_at' => null,
        ]);

        return back()->with('toast_success', __('talenma.jobs.published'));
    }

    public function close(Request $request, JobPosting $job): RedirectResponse
    {
        $this->authorizeJob($request, $job);

        $job->update([
            'status' => JobPosting::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        return back()->with('toast_success', __('talenma.jobs.closed'));
    }

    public function updateApplication(Request $request, JobPosting $job, JobApplication $application): RedirectResponse
    {
        $this->authorizeJob($request, $job);
        abort_unless($application->job_posting_id === $job->id, 404);

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(JobApplication::STATUSES)],
        ]);

        $application->update(['status' => $data['status']]);

        return back()->with('toast_success', __('talenma.jobs.application_updated'));
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

    private function authorizeJob(Request $request, JobPosting $job): void
    {
        $user = $request->user();
        abort_unless($user->canManageJobs(), 403);

        $org = $user->companyOrganization();
        abort_unless($org && $job->company_profile_id === $org->id, 403);
    }
}

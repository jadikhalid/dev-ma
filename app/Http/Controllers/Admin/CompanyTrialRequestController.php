<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyTrialRequest;
use App\Services\CompanyTrialProvisioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyTrialRequestController extends Controller
{
    public function __construct(
        private CompanyTrialProvisioningService $provisioning,
    ) {}

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString() ?: CompanyTrialRequest::STATUS_PENDING;

        $query = CompanyTrialRequest::query()->latest();

        if (in_array($status, [
            CompanyTrialRequest::STATUS_PENDING,
            CompanyTrialRequest::STATUS_PROVISIONED,
            CompanyTrialRequest::STATUS_REJECTED,
        ], true)) {
            $query->where('status', $status);
        }

        return view('admin.company-trial-requests.index', [
            'status' => $status,
            'pendingCount' => CompanyTrialRequest::query()
                ->where('status', CompanyTrialRequest::STATUS_PENDING)
                ->count(),
            'requests' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function provision(CompanyTrialRequest $companyTrialRequest): RedirectResponse
    {
        $user = $this->provisioning->provision($companyTrialRequest, request()->user());

        return redirect()
            ->route('admin.users.index', ['filter' => 'companies', 'q' => $user->email])
            ->with('toast_success', __('talenma.admin.company_trial_requests.provisioned', [
                'company' => $user->name,
            ]));
    }

    public function reject(Request $request, CompanyTrialRequest $companyTrialRequest): RedirectResponse
    {
        $data = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->provisioning->reject(
            $companyTrialRequest,
            $request->user(),
            $data['rejection_reason'] ?? null,
        );

        return back()->with('toast_success', __('talenma.admin.company_trial_requests.rejected'));
    }
}

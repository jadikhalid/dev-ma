<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyDemoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyDemoRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        if (! in_array($status, CompanyDemoRequest::STATUSES, true)) {
            $status = CompanyDemoRequest::STATUS_NEW;
        }

        return view('admin.company-demo-requests.index', [
            'status' => $status,
            'newCount' => CompanyDemoRequest::query()
                ->where('status', CompanyDemoRequest::STATUS_NEW)
                ->count(),
            'requests' => CompanyDemoRequest::query()
                ->with('handler')
                ->where('status', $status)
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function updateStatus(Request $request, CompanyDemoRequest $companyDemoRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(CompanyDemoRequest::STATUSES)],
        ]);

        $isNew = $data['status'] === CompanyDemoRequest::STATUS_NEW;

        $companyDemoRequest->update([
            'status' => $data['status'],
            'handled_by' => $isNew ? null : $request->user()->id,
            'handled_at' => $isNew ? null : now(),
        ]);

        return back()->with('toast_success', __('talenma.admin.company_demo_requests.status_updated'));
    }
}

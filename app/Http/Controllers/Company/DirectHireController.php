<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use App\Models\User;
use App\Services\DirectHireService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectHireController extends Controller
{
    public function __construct(private DirectHireService $directHires) {}

    public function create(Request $request, User $talent): View|RedirectResponse
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        abort_unless($talent->isTalent() && $talent->approval_status === 'approved', 404);

        return view('company.direct-hire.create', [
            'talent' => $talent->load('profile'),
        ]);
    }

    public function store(Request $request, User $talent): RedirectResponse
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        abort_unless($talent->isTalent() && $talent->approval_status === 'approved', 404);

        $data = $request->validate([
            'subject' => ['required', 'string', 'min:5', 'max:120'],
            'message' => ['required', 'string', 'min:40', 'max:5000'],
        ], [
            'subject.required' => __('talenma.direct_hire.subject_required'),
            'subject.min' => __('talenma.direct_hire.subject_min'),
            'message.required' => __('talenma.direct_hire.message_required'),
            'message.min' => __('talenma.direct_hire.message_min'),
        ]);

        $directHire = $this->directHires->create(
            $request->user(),
            $talent,
            $data['subject'],
            $data['message'],
        );

        return redirect()
            ->route('company.direct-hire.show', $directHire)
            ->with('toast_success', __('talenma.direct_hire.sent'));
    }

    public function show(Request $request, DirectHireRequest $directHire): View
    {
        $this->directHires->assertCompanyCanManage($directHire, $request->user());

        $directHire->load([
            'talent.profile',
            'companyProfile',
            'rounds',
            'conversation',
        ]);

        return view('company.direct-hire.show', [
            'directHire' => $directHire,
            'roundStatuses' => DirectHireRound::statuses(),
        ]);
    }

    public function storeRound(Request $request, DirectHireRequest $directHire): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'company_note' => ['nullable', 'string', 'max:2000'],
        ], [
            'title.required' => __('talenma.direct_hire.round_title_required'),
            'title.min' => __('talenma.direct_hire.round_title_min'),
        ]);

        $this->directHires->addRound(
            $directHire,
            $request->user(),
            $data['title'],
            $data['company_note'] ?? null,
        );

        return back()->with('toast_success', __('talenma.direct_hire.round_added'));
    }

    public function updateRound(Request $request, DirectHireRequest $directHire, DirectHireRound $round): RedirectResponse
    {
        abort_unless($round->direct_hire_request_id === $directHire->id, 404);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'min:3', 'max:120'],
            'status' => ['required', 'in:'.implode(',', DirectHireRound::statuses())],
            'scheduled_at' => ['nullable', 'date'],
            'company_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->directHires->updateRound($round, $request->user(), $data);

        return back()->with('toast_success', __('talenma.direct_hire.round_updated'));
    }

    public function close(Request $request, DirectHireRequest $directHire): RedirectResponse
    {
        $data = $request->validate([
            'outcome' => ['required', 'in:hired,closed_negative'],
            'closure_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->directHires->close(
            $directHire,
            $request->user(),
            $data['outcome'],
            $data['closure_note'] ?? null,
        );

        return back()->with('toast_success', __('talenma.direct_hire.closed'));
    }

    public function withdraw(Request $request, DirectHireRequest $directHire): RedirectResponse
    {
        $data = $request->validate([
            'closure_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->directHires->withdraw(
            $directHire,
            $request->user(),
            $data['closure_note'] ?? null,
        );

        return back()->with('toast_success', __('talenma.direct_hire.withdrawn'));
    }
}

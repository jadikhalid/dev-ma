<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Models\DirectHireRequest;
use App\Services\DirectHireService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectHireController extends Controller
{
    public function __construct(private DirectHireService $directHires) {}

    public function index(Request $request): View
    {
        $requests = DirectHireRequest::query()
            ->where('talent_user_id', $request->user()->id)
            ->with(['companyProfile', 'company', 'rounds'])
            ->latest()
            ->paginate(20);

        return view('talent.direct-hire.index', [
            'requests' => $requests,
        ]);
    }

    public function show(Request $request, DirectHireRequest $directHire): View
    {
        $this->directHires->assertTalentCanView($directHire, $request->user());

        $directHire->load([
            'companyProfile',
            'company',
            'rounds',
            'conversation',
        ]);

        return view('talent.direct-hire.show', [
            'directHire' => $directHire,
        ]);
    }

    public function decide(Request $request, DirectHireRequest $directHire): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:'.implode(',', DirectHireRequest::talentDecisions())],
            'talent_decision_note' => ['nullable', 'string', 'max:2000'],
        ], [
            'decision.required' => __('talenma.direct_hire.decision_required'),
            'decision.in' => __('talenma.direct_hire.error_decision_invalid'),
        ]);

        $this->directHires->decide(
            $directHire,
            $request->user(),
            $data['decision'],
            $data['talent_decision_note'] ?? null,
        );

        $message = match ($data['decision']) {
            DirectHireRequest::DECISION_ACCEPT => __('talenma.direct_hire.decision_accepted'),
            DirectHireRequest::DECISION_DECLINE => __('talenma.direct_hire.decision_declined'),
            default => __('talenma.direct_hire.decision_deferred'),
        };

        return redirect()
            ->route('talent.direct-hire.show', $directHire)
            ->with('toast_success', $message);
    }
}

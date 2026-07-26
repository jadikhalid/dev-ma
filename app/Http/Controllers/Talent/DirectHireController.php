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
        $user = $request->user();
        abort_unless($user->isTalent(), 403);

        $base = DirectHireRequest::query()
            ->where('talent_user_id', $user->id)
            ->with(['companyProfile', 'company', 'rounds']);

        $open = (clone $base)
            ->whereIn('status', DirectHireRequest::openStatuses())
            ->latest()
            ->get();

        $closed = (clone $base)
            ->whereIn('status', DirectHireRequest::terminalStatuses())
            ->latest()
            ->get();

        return view('talent.direct-hire.index', [
            'openRequests' => $open,
            'closedRequests' => $closed,
        ]);
    }

    public function show(Request $request, DirectHireRequest $directHire): View
    {
        $this->directHires->assertTalentCanView($directHire, $request->user());
        $this->directHires->ensureThreadSeeded($directHire);
        $this->directHires->markSeenForTalent($request->user(), $directHire);

        $directHire->load([
            'companyProfile',
            'company',
            'rounds',
            'messages.sender',
        ]);

        return view('talent.direct-hire.show', [
            'directHire' => $directHire,
        ]);
    }

    public function storeMessage(Request $request, DirectHireRequest $directHire): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:2000'],
        ], [
            'body.required' => __('talenma.direct_hire.chat_required'),
            'body.min' => __('talenma.direct_hire.chat_min'),
            'body.max' => __('talenma.direct_hire.chat_max'),
        ]);

        $this->directHires->postMessage($directHire, $request->user(), $data['body']);

        return redirect()
            ->route('talent.direct-hire.show', $directHire)
            ->withFragment('direct-hire-chat')
            ->with('toast_success', __('talenma.direct_hire.chat_sent'));
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

<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use App\Models\User;
use App\Services\DirectHireService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectHireController extends Controller
{
    public function __construct(private DirectHireService $directHires) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isCompany(), 403);

        $open = $this->directHires->queryForCompany($user)
            ->with(['talent', 'rounds'])
            ->whereIn('status', DirectHireRequest::openStatuses())
            ->latest()
            ->get();

        $closed = $this->directHires->queryForCompany($user)
            ->with(['talent', 'rounds'])
            ->whereIn('status', DirectHireRequest::terminalStatuses())
            ->latest()
            ->get();

        return view('company.direct-hire.index', [
            'openRequests' => $open,
            'closedRequests' => $closed,
        ]);
    }

    public function create(Request $request, User $talent): View|RedirectResponse
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        abort_unless($talent->isTalent() && $talent->approval_status === 'approved', 404);

        $blockReason = $this->directHires->companyProposeBlockReason($request->user(), $talent);

        if ($blockReason !== null) {
            $message = $blockReason === 'hired'
                ? __('talenma.direct_hire.error_already_hired')
                : __('talenma.direct_hire.error_process_open');

            return redirect()
                ->route('company.search')
                ->with('toast_error', $message);
        }

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
        $this->directHires->ensureThreadSeeded($directHire);
        $this->directHires->markSeenForCompany($request->user(), $directHire);

        $directHire->load([
            'talent.profile',
            'companyProfile',
            'rounds',
            'messages.sender',
        ]);

        return view('company.direct-hire.show', [
            'directHire' => $directHire,
            'roundStatuses' => DirectHireRound::outcomeStatuses(),
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
            ->route('company.direct-hire.show', $directHire)
            ->withFragment('direct-hire-chat')
            ->with('toast_success', __('talenma.direct_hire.chat_sent'));
    }

    public function storeRound(Request $request, DirectHireRequest $directHire): RedirectResponse|JsonResponse
    {
        $request->merge([
            'meeting_url' => filled($request->input('meeting_url')) ? trim((string) $request->input('meeting_url')) : null,
            'company_note' => filled($request->input('company_note')) ? trim((string) $request->input('company_note')) : null,
        ]);

        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'scheduled_at' => ['required', 'date'],
            'meeting_url' => ['nullable', 'string', 'url', 'max:2048'],
            'company_note' => ['nullable', 'string', 'max:2000'],
        ], [
            'title.required' => __('talenma.direct_hire.round_title_required'),
            'title.min' => __('talenma.direct_hire.round_title_min'),
            'title.max' => __('talenma.direct_hire.round_title_max'),
            'scheduled_at.required' => __('talenma.direct_hire.round_scheduled_required'),
            'scheduled_at.date' => __('talenma.direct_hire.round_scheduled_required'),
            'meeting_url.url' => __('talenma.direct_hire.round_meeting_url_invalid'),
            'meeting_url.max' => __('talenma.direct_hire.round_meeting_url_max'),
            'company_note.max' => __('talenma.direct_hire.round_note_max'),
        ]);

        $round = $this->directHires->addRound(
            $directHire,
            $request->user(),
            $data['title'],
            $data['scheduled_at'],
            $data['company_note'] ?? null,
            $data['meeting_url'] ?? null,
        );

        $message = __('talenma.direct_hire.round_added');

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'html' => view('company.direct-hire._round-card', [
                    'directHire' => $directHire,
                    'round' => $round,
                    'canManageRounds' => true,
                    'roundStatuses' => DirectHireRound::outcomeStatuses(),
                ])->render(),
            ]);
        }

        return back()->with('toast_success', $message);
    }

    public function updateRound(Request $request, DirectHireRequest $directHire, DirectHireRound $round): RedirectResponse|JsonResponse
    {
        abort_unless($round->direct_hire_request_id === $directHire->id, 404);

        if ($request->exists('meeting_url')) {
            $request->merge([
                'meeting_url' => filled($request->input('meeting_url')) ? trim((string) $request->input('meeting_url')) : null,
            ]);
        }

        if ($request->exists('company_note')) {
            $request->merge([
                'company_note' => filled($request->input('company_note')) ? trim((string) $request->input('company_note')) : null,
            ]);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:120'],
            'scheduled_at' => ['sometimes', 'required', 'date'],
            'meeting_url' => ['nullable', 'string', 'url', 'max:2048'],
            'company_note' => ['nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'required', 'in:'.implode(',', DirectHireRound::outcomeStatuses())],
        ], [
            'title.required' => __('talenma.direct_hire.round_title_required'),
            'title.min' => __('talenma.direct_hire.round_title_min'),
            'title.max' => __('talenma.direct_hire.round_title_max'),
            'scheduled_at.required' => __('talenma.direct_hire.round_scheduled_required'),
            'scheduled_at.date' => __('talenma.direct_hire.round_scheduled_required'),
            'meeting_url.url' => __('talenma.direct_hire.round_meeting_url_invalid'),
            'meeting_url.max' => __('talenma.direct_hire.round_meeting_url_max'),
            'company_note.max' => __('talenma.direct_hire.round_note_max'),
            'status.required' => __('talenma.direct_hire.error_round_status_invalid'),
            'status.in' => __('talenma.direct_hire.error_round_status_invalid'),
        ]);

        // Ensure optional clears are passed through when the edit form is submitted.
        if ($request->exists('meeting_url')) {
            $data['meeting_url'] = $request->input('meeting_url');
        }

        if ($request->exists('company_note')) {
            $data['company_note'] = $request->input('company_note');
        }

        $round = $this->directHires->updateRound($round, $request->user(), $data);

        $message = __('talenma.direct_hire.round_updated');

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'round' => $this->roundClientPayload($directHire, $round),
            ]);
        }

        return back()->with('toast_success', $message);
    }

    public function cancelRound(Request $request, DirectHireRequest $directHire, DirectHireRound $round): RedirectResponse|JsonResponse
    {
        abort_unless($round->direct_hire_request_id === $directHire->id, 404);

        $data = $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'cancellation_reason.required' => __('talenma.direct_hire.round_cancel_reason_required'),
            'cancellation_reason.min' => __('talenma.direct_hire.round_cancel_reason_min'),
            'cancellation_reason.max' => __('talenma.direct_hire.round_cancel_reason_max'),
        ]);

        $round = $this->directHires->cancelRound(
            $round,
            $request->user(),
            $data['cancellation_reason'],
        );

        $message = __('talenma.direct_hire.round_cancelled');

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'round' => $this->roundClientPayload($directHire, $round),
            ]);
        }

        return back()->with('toast_success', $message);
    }

    /**
     * @return array<string, mixed>
     */
    private function roundClientPayload(DirectHireRequest $directHire, DirectHireRound $round): array
    {
        $canCancel = $round->isCancellable();

        return [
            'id' => $round->id,
            'position' => $round->position,
            'title' => $round->title,
            'status' => $round->status,
            'status_label' => $round->statusLabel(),
            'status_tone' => $round->statusTone(),
            'can_edit' => $round->isEditable(),
            'can_cancel' => $canCancel,
            'cancel_url' => $canCancel
                ? route('company.direct-hire.rounds.cancel', [$directHire, $round])
                : null,
            'scheduled_at_local' => $round->scheduled_at
                ?->timezone(config('app.timezone'))
                ->format('Y-m-d\TH:i'),
            'scheduled_at_label' => $round->scheduled_at?->translatedFormat('d M Y H:i'),
            'meeting_url' => $round->meeting_url,
            'company_note' => $round->company_note,
            'cancellation_reason' => $round->cancellation_reason,
            'is_cancelled' => $round->isCancelled(),
        ];
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

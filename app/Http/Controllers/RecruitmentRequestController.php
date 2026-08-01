<?php

namespace App\Http\Controllers;

use App\Models\RecruitmentRequest;
use App\Models\User;
use App\Services\CompanyTalentActionStateService;
use App\Services\RecruitmentRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecruitmentRequestController extends Controller
{
    public function __construct(
        private RecruitmentRequestService $recruitmentRequests,
        private CompanyTalentActionStateService $talentActions,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        $user = $request->user();

        $all = $user->isCompanyOwner()
            ? $user->recruitmentRequests()->with('talent')->latest()->get()
            : collect();

        return view('sourcing.index', [
            'openRequests' => $all->where('mode', RecruitmentRequest::MODE_OPEN)->values(),
            'namedRequests' => $all->where('mode', RecruitmentRequest::MODE_NAMED)->values(),
        ]);
    }

    public function show(Request $request, RecruitmentRequest $recruitmentRequest): View|RedirectResponse
    {
        abort_unless($recruitmentRequest->canAccess($request->user()), 403);

        if (! $request->user()->isCompany() && ! $request->user()->isStaff()) {
            return redirect()->route('dashboard');
        }

        $recruitmentRequest->load(['talent.profile', 'company', 'messages.sender', 'statusUpdatedBy', 'statusEvents.actor']);

        if ($request->user()->isCompany()) {
            $this->recruitmentRequests->markSeenForCompany($request->user(), $recruitmentRequest);
        }

        return view('sourcing.show', [
            'recruitment' => $recruitmentRequest,
            'isStaff' => $request->user()->isStaff(),
            'statuses' => RecruitmentRequest::statuses(),
        ]);
    }

    public function unlockTalent(Request $request, RecruitmentRequest $recruitmentRequest): JsonResponse|RedirectResponse
    {
        abort_unless($request->user()->isCompany(), 403);

        $this->recruitmentRequests->unlockTalentForCompany($recruitmentRequest, $request->user());

        $talent = $recruitmentRequest->fresh(['talent'])?->talent;
        $message = __('talenma.recruitment.talent_unlocked');

        if ($request->expectsJson() || $request->wantsJson()) {
            abort_unless($talent, 404);

            return response()->json([
                'message' => $message,
                ...$this->talentActions->for($request->user(), $talent),
            ]);
        }

        return redirect()
            ->route('sourcing.show', $recruitmentRequest)
            ->with('toast_success', $message);
    }

    public function storeMessage(Request $request, RecruitmentRequest $recruitmentRequest): JsonResponse|RedirectResponse
    {
        abort_unless($recruitmentRequest->canAccess($request->user()), 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:2000'],
        ], [
            'body.required' => __('talenma.recruitment.chat_body_required'),
            'body.min' => __('talenma.recruitment.chat_body_min'),
            'body.max' => __('talenma.recruitment.chat_body_max'),
        ]);

        $message = $this->recruitmentRequests->postMessage(
            $recruitmentRequest,
            $request->user(),
            $data['body'],
        );

        $message->loadMissing('sender');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('talenma.recruitment.chat_sent'),
                'message_html' => view('sourcing._chat-message', [
                    'msg' => $message,
                    'recruitment' => $recruitmentRequest,
                    'viewer' => $request->user(),
                ])->render(),
            ]);
        }

        return redirect()
            ->to(route('sourcing.show', $recruitmentRequest).'#sourcing-chat')
            ->with('toast_success', __('talenma.recruitment.chat_sent'));
    }

    public function create(Request $request, ?User $talent = null): View|RedirectResponse
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        if ($talent) {
            abort_unless($talent->isTalent() && $talent->approval_status === 'approved', 404);

            $existing = $this->recruitmentRequests->existingNamedRequestForCompanyTalent(
                $request->user(),
                $talent,
            );

            if ($existing) {
                return redirect()
                    ->route('sourcing.show', $existing)
                    ->with('toast_error', $this->recruitmentRequests->namedRequestDisabledHint(
                        $request->user(),
                        $talent,
                    ));
            }
        }

        return view('recruitment.create', [
            'talent' => $talent?->load('profile'),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'developer_user_id' => ['nullable', 'exists:users,id'],
            'role_title' => ['required', 'string', 'min:5', 'max:120'],
            'need' => ['required', 'string', 'min:50', 'max:5000'],
        ], [
            'role_title.required' => __('talenma.recruitment.role_title_required'),
            'role_title.min' => __('talenma.recruitment.role_title_min'),
            'role_title.max' => __('talenma.recruitment.role_title_max'),
            'need.required' => __('talenma.recruitment.need_required'),
            'need.min' => __('talenma.recruitment.need_min'),
            'need.max' => __('talenma.recruitment.need_max'),
        ]);

        $user = $request->user();
        $talent = null;

        if (filled($data['developer_user_id'] ?? null)) {
            $talent = User::query()->find($data['developer_user_id']);

            if (! $talent || ! $talent->isTalent() || $talent->approval_status !== 'approved') {
                $talent = null;
            }
        }

        if ($talent) {
            $existing = $this->recruitmentRequests->existingNamedRequestForCompanyTalent($user, $talent);

            if ($existing) {
                $message = $this->recruitmentRequests->namedRequestDisabledHint($user, $talent)
                    ?: __('talenma.recruitment.named_blocked_open');

                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 422);
                }

                return redirect()
                    ->route('sourcing.show', $existing)
                    ->with('toast_error', $message);
            }
        }

        $mode = $talent
            ? RecruitmentRequest::MODE_NAMED
            : RecruitmentRequest::MODE_OPEN;

        $recruitment = RecruitmentRequest::create([
            'company_user_id' => $user->id,
            'developer_user_id' => $talent?->id,
            'mode' => $mode,
            'subject' => $data['role_title'],
            'message' => $data['need'],
            'status' => RecruitmentRequest::STATUS_PENDING,
            'company_seen_at' => now(),
        ]);

        $this->recruitmentRequests->recordSubmitted($recruitment, $user);
        $this->recruitmentRequests->notifySubmitted($recruitment);

        $recruitment->load('talent');

        $message = __('talenma.recruitment.sent_dashboard_'.$mode);

        if ($request->expectsJson()) {
            if ($request->boolean('embed')) {
                return response()->json([
                    'message' => $message,
                    'mode' => $mode,
                    'card_html' => view('sourcing._request-card', [
                        'recruitment' => $recruitment,
                    ])->render(),
                    'stay' => true,
                ]);
            }

            session()->flash('toast_success', $message);

            return response()->json([
                'message' => $message,
                'show_url' => route('sourcing.show', $recruitment),
            ]);
        }

        return redirect()
            ->route('sourcing.show', $recruitment)
            ->with('toast_success', $message);
    }
}

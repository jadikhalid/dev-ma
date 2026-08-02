<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use App\Models\User;
use App\Services\DirectHireService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DirectHireController extends Controller
{
    public function __construct(
        private DirectHireService $directHires,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->isStaff(), 403);

        $filter = $request->string('filter')->toString() ?: 'open';
        if (! in_array($filter, ['open', 'closed', 'all'], true)) {
            $filter = 'open';
        }

        $origin = $request->string('origin')->toString() ?: 'all';
        if (! in_array($origin, ['all', ...DirectHireRequest::staffHireOrigins()], true)) {
            $origin = 'all';
        }

        $query = $this->directHires->queryForStaff()
            ->with(['talent', 'company', 'companyProfile', 'initiatedBy', 'rounds'])
            ->latest();

        if ($filter === 'open') {
            $query->whereIn('status', DirectHireRequest::openStatuses());
        } elseif ($filter === 'closed') {
            $query->whereIn('status', DirectHireRequest::terminalStatuses());
        }

        if ($origin !== 'all') {
            $query->where('hire_origin', $origin);
        }

        $requests = $query->paginate(20)->withQueryString();

        $baseCounts = $this->directHires->queryForStaff();
        if ($origin !== 'all') {
            $baseCounts->where('hire_origin', $origin);
        }

        $counts = [
            'open' => (clone $baseCounts)->whereIn('status', DirectHireRequest::openStatuses())->count(),
            'closed' => (clone $baseCounts)->whereIn('status', DirectHireRequest::terminalStatuses())->count(),
            'all' => (clone $baseCounts)->count(),
        ];

        $originCounts = [
            'all' => $this->directHires->queryForStaff()->count(),
            DirectHireRequest::ORIGIN_STAFF_INTERNAL => $this->directHires->queryForStaff()
                ->where('hire_origin', DirectHireRequest::ORIGIN_STAFF_INTERNAL)
                ->count(),
            DirectHireRequest::ORIGIN_STAFF_ON_BEHALF => $this->directHires->queryForStaff()
                ->where('hire_origin', DirectHireRequest::ORIGIN_STAFF_ON_BEHALF)
                ->count(),
        ];

        return view('admin.direct-hire.index', [
            'requests' => $requests,
            'filter' => $filter,
            'origin' => $origin,
            'counts' => $counts,
            'originCounts' => $originCounts,
        ]);
    }

    public function searchTalents(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isStaff(), 403);

        $data = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $term = trim($data['q']);
        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        $results = User::query()
            ->where('role', 'dev')
            ->where('approval_status', 'approved')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like);
            })
            ->orderBy('name')
            ->limit(12)
            ->get(['id', 'name', 'email', 'first_name', 'last_name'])
            ->map(fn (User $talent) => [
                'id' => $talent->id,
                'label' => $talent->name,
                'email' => $talent->email,
                'create_url' => route('admin.direct-hire.create', $talent),
            ])
            ->values()
            ->all();

        return response()->json([
            'results' => $results,
        ]);
    }

    public function searchCompanies(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isStaff(), 403);

        $data = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $term = trim($data['q']);
        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        $results = User::query()
            ->where('role', 'company')
            ->where('approval_status', 'approved')
            ->where(function ($query) {
                $query->whereNull('company_seat')
                    ->orWhere('company_seat', User::SEAT_OWNER);
            })
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhereHas('companyProfile', function ($profile) use ($like) {
                        $profile->where('representative_name', 'like', $like);
                    });
            })
            ->with('companyProfile')
            ->orderBy('name')
            ->limit(12)
            ->get()
            ->map(function (User $company) {
                $org = $company->companyProfile;
                $label = $org?->displayName() ?: $company->name;

                return [
                    'id' => $company->id,
                    'label' => $label,
                    'email' => $company->email,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'results' => $results,
        ]);
    }

    public function create(Request $request, User $talent): View|RedirectResponse
    {
        abort_unless($request->user()?->isStaff(), 403);
        abort_unless($talent->isTalent() && $talent->approval_status === 'approved', 404);

        return view('admin.direct-hire.create', [
            'talent' => $talent->load('profile'),
            'staffInternalOpen' => $this->directHires->staffHasOpenInternalRequest(),
        ]);
    }

    public function store(Request $request, User $talent): RedirectResponse|JsonResponse
    {
        abort_unless($request->user()?->isStaff(), 403);
        abort_unless($talent->isTalent() && $talent->approval_status === 'approved', 404);

        $data = $request->validate([
            'hire_origin' => ['required', Rule::in(DirectHireRequest::staffHireOrigins())],
            'company_id' => [
                'nullable',
                'integer',
                Rule::requiredIf(fn () => $request->input('hire_origin') === DirectHireRequest::ORIGIN_STAFF_ON_BEHALF),
                'exists:users,id',
            ],
            'subject' => ['required', 'string', 'min:5', 'max:120'],
            'message' => ['required', 'string', 'min:40', 'max:5000'],
        ], [
            'hire_origin.required' => __('talenma.direct_hire.origin_required'),
            'hire_origin.in' => __('talenma.direct_hire.error_origin_invalid'),
            'company_id.required' => __('talenma.direct_hire.error_beneficiary_required'),
            'company_id.exists' => __('talenma.direct_hire.error_beneficiary_invalid'),
            'subject.required' => __('talenma.direct_hire.subject_required'),
            'subject.min' => __('talenma.direct_hire.subject_min'),
            'message.required' => __('talenma.direct_hire.message_required'),
            'message.min' => __('talenma.direct_hire.message_min'),
        ]);

        $beneficiary = null;
        if ($data['hire_origin'] === DirectHireRequest::ORIGIN_STAFF_ON_BEHALF) {
            $beneficiary = User::query()->findOrFail($data['company_id']);
        }

        $directHire = $this->directHires->createByStaff(
            $request->user(),
            $talent,
            $data['subject'],
            $data['message'],
            $data['hire_origin'],
            $beneficiary,
        );

        $message = __('talenma.direct_hire.sent');

        if ($request->expectsJson()) {
            session()->flash('toast_success', $message);

            return response()->json([
                'message' => $message,
                'show_url' => route('admin.direct-hire.show', $directHire),
            ]);
        }

        return redirect()
            ->route('admin.direct-hire.show', $directHire)
            ->with('toast_success', $message);
    }

    public function showTalentProfile(Request $request, User $talent): JsonResponse
    {
        abort_unless($request->user()?->isStaff(), 403);

        if ($talent->role !== 'dev' || $talent->approval_status !== User::APPROVAL_APPROVED) {
            abort(404);
        }

        $talent->load(['profile.profession', 'profile.professionSector', 'profile.documents']);

        $profile = $talent->profile;
        $forceReveal = true;
        $isPublic = $profile?->isRevealedAsPublic($forceReveal) ?? false;
        $keywords = collect(explode(',', (string) $profile?->specialization))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->values();
        $cv = $profile?->cvDocument();

        return response()->json([
            'talent_id' => (int) $talent->id,
            'name' => $profile?->visibleDisplayName($talent, $forceReveal) ?? $talent->publicDisplayName(),
            'avatar_url' => $profile?->visibleAvatarUrl($talent, $forceReveal),
            'initials' => $talent->initials(),
            'is_public' => $isPublic,
            'employer_label' => $profile?->employerLabel($forceReveal),
            'profession_label' => $profile?->professionLabel(),
            'sector_label' => $profile?->sectorLabel(),
            'experience_label' => $profile?->experience_years !== null
                ? __('talenma.talents.experience', ['years' => $profile->experience_years])
                : null,
            'availability_label' => $profile?->statusLabel(),
            'availability_tone' => $profile?->statusTone(),
            'keywords' => $keywords,
            'work_modes' => $profile?->workModeLabels() ?? [],
            'languages' => $profile?->languageLabels() ?? [],
            'bio' => $profile?->bio,
            'education_label' => $profile?->educationLabel(),
            'certifications' => $isPublic ? $profile?->certifications : null,
            'linkedin_url' => $isPublic ? $profile?->linkedin_url : null,
            'github_url' => $isPublic ? $profile?->github_url : null,
            'portfolio_url' => $isPublic ? $profile?->portfolio_url : null,
            'cv_url' => ($isPublic && $cv) ? route('admin.profile-documents.show', $cv) : null,
            'presentation_video_url' => ($isPublic && filled($profile?->presentation_video_url))
                ? $profile->presentation_video_url
                : null,
            'compose_url' => null,
            'talent_locked' => false,
            'can_request_named' => false,
            'named_request_disabled_hint' => null,
            'recruitment_url' => null,
            'named_unlock_url' => null,
            'direct_hire_url' => null,
            'can_propose_direct_hire' => false,
            'direct_hire_disabled_hint' => null,
            'direct_hire_unlock_url' => null,
            'can_propose_direct_hire_globally' => false,
        ]);
    }

    public function show(Request $request, DirectHireRequest $directHire): View
    {
        $this->directHires->assertStaffCanManage($directHire, $request->user());
        $this->directHires->ensureThreadSeeded($directHire);
        $this->directHires->markSeenForHiringSide($request->user(), $directHire);

        $directHire->load([
            'talent.profile',
            'company',
            'companyProfile',
            'initiatedBy',
            'rounds',
            'messages.sender',
            'statusEvents.actor',
        ]);

        return view('admin.direct-hire.show', [
            'directHire' => $directHire,
            'roundStatuses' => DirectHireRound::outcomeStatuses(),
            'hireRoute' => 'admin.direct-hire',
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
            ->route('admin.direct-hire.show', $directHire)
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
                    'hireRoute' => 'admin.direct-hire',
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
                ? route('admin.direct-hire.rounds.cancel', [$directHire, $round])
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

    public function close(Request $request, DirectHireRequest $directHire): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'outcome' => ['required', 'in:hired,closed_negative'],
            'closure_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $directHire = $this->directHires->close(
            $directHire,
            $request->user(),
            $data['outcome'],
            $data['closure_note'] ?? null,
        );

        $directHire->refresh()->load(['rounds', 'talent', 'companyProfile', 'statusEvents.actor']);
        $message = __('talenma.direct_hire.closed');

        if ($request->expectsJson()) {
            $canManageRounds = false;
            $roundStatuses = DirectHireRound::outcomeStatuses();

            return response()->json([
                'message' => $message,
                'can_manage_rounds' => $canManageRounds,
                'can_withdraw' => false,
                'can_chat' => $directHire->allowsChat(),
                'status_badge_html' => view('company.direct-hire._status-badge', [
                    'directHire' => $directHire,
                ])->render(),
                'history_html' => view('direct-hire._proposal-history', [
                    'directHire' => $directHire,
                ])->render(),
                'rounds_list_html' => view('company.direct-hire._rounds-list', [
                    'directHire' => $directHire,
                    'canManageRounds' => $canManageRounds,
                    'roundStatuses' => $roundStatuses,
                    'hireRoute' => 'admin.direct-hire',
                ])->render(),
            ]);
        }

        return back()->with('toast_success', $message);
    }

    public function respondToDeferral(Request $request, DirectHireRequest $directHire): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:'.implode(',', DirectHireRequest::companyDeferralActions())],
            'note' => ['nullable', 'string', 'max:2000'],
        ], [
            'action.required' => __('talenma.direct_hire.deferral_action_required'),
            'action.in' => __('talenma.direct_hire.error_deferral_action_invalid'),
            'note.max' => __('talenma.direct_hire.chat_max'),
        ]);

        $directHire = $this->directHires->respondToDeferral(
            $directHire,
            $request->user(),
            $data['action'],
            $data['note'] ?? null,
        );

        $directHire->refresh()->load(['rounds', 'talent', 'companyProfile', 'statusEvents.actor']);

        $message = $data['action'] === DirectHireRequest::DEFERRAL_ACCEPT
            ? __('talenma.direct_hire.deferral_accepted')
            : __('talenma.direct_hire.deferral_refused');

        if ($request->expectsJson()) {
            $canManageRounds = $directHire->status === DirectHireRequest::STATUS_IN_PROCESS;
            $canRespondToDeferral = $directHire->awaitsCompanyDeferralReply();
            $canWithdraw = $directHire->isOpen() && ! $canRespondToDeferral;
            $roundStatuses = DirectHireRound::outcomeStatuses();

            return response()->json([
                'message' => $message,
                'can_manage_rounds' => $canManageRounds,
                'can_respond_to_deferral' => $canRespondToDeferral,
                'can_withdraw' => $canWithdraw,
                'can_chat' => $directHire->allowsChat(),
                'status_badge_html' => view('company.direct-hire._status-badge', [
                    'directHire' => $directHire,
                ])->render(),
                'history_html' => view('direct-hire._proposal-history', [
                    'directHire' => $directHire,
                ])->render(),
                'withdraw_html' => $canWithdraw
                    ? view('company.direct-hire._withdraw', [
                        'directHire' => $directHire,
                        'hireRoute' => 'admin.direct-hire',
                    ])->render()
                    : null,
                'rounds_list_html' => view('company.direct-hire._rounds-list', [
                    'directHire' => $directHire,
                    'canManageRounds' => $canManageRounds,
                    'roundStatuses' => $roundStatuses,
                    'hireRoute' => 'admin.direct-hire',
                ])->render(),
            ]);
        }

        return back()->with('toast_success', $message);
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

    public function unlockTalent(Request $request, DirectHireRequest $directHire): RedirectResponse|JsonResponse
    {
        $this->directHires->unlockTalentForCompany($directHire, $request->user());

        $message = __('talenma.direct_hire.talent_unlocked');

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('admin.direct-hire.show', $directHire)
            ->with('toast_success', $message);
    }
}

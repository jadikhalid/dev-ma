<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecruitmentRequest;
use App\Services\RecruitmentRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecruitmentRequestController extends Controller
{
    public function __construct(
        private RecruitmentRequestService $recruitmentRequests,
    ) {}

    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString() ?: 'pending';
        $mode = $request->string('mode')->toString() ?: 'all';

        if (! in_array($filter, ['all', ...RecruitmentRequest::statuses()], true)) {
            $filter = 'pending';
        }

        if (! in_array($mode, ['all', ...RecruitmentRequest::modes()], true)) {
            $mode = 'all';
        }

        $query = RecruitmentRequest::query()
            ->with([
                'company',
                'talent',
                'statusUpdatedBy',
            ])
            ->latest();

        if ($filter !== 'all') {
            if (in_array($filter, [
                RecruitmentRequest::STATUS_COMPLETED_SUCCESSFUL,
                RecruitmentRequest::STATUS_COMPLETED_UNSUCCESSFUL,
                'completed',
            ], true)) {
                if ($filter === 'completed') {
                    $query->whereIn('status', RecruitmentRequest::closedStatuses());
                } elseif ($filter === RecruitmentRequest::STATUS_COMPLETED_SUCCESSFUL) {
                    $query->whereIn('status', [
                        RecruitmentRequest::STATUS_COMPLETED_SUCCESSFUL,
                        RecruitmentRequest::STATUS_COMPLETED,
                    ]);
                } else {
                    $query->whereIn('status', [
                        RecruitmentRequest::STATUS_COMPLETED_UNSUCCESSFUL,
                        RecruitmentRequest::STATUS_CANCELLED,
                    ]);
                }
            } else {
                $query->where('status', $filter);
            }
        }

        if ($mode !== 'all') {
            $query->where('mode', $mode);
        }

        $requests = $query->paginate(20)->withQueryString();

        $baseCounts = RecruitmentRequest::query();
        if ($mode !== 'all') {
            $baseCounts->where('mode', $mode);
        }

        $counts = [
            'pending' => (clone $baseCounts)->where('status', RecruitmentRequest::STATUS_PENDING)->count(),
            'in_progress' => (clone $baseCounts)->where('status', RecruitmentRequest::STATUS_IN_PROGRESS)->count(),
            'completed_successful' => (clone $baseCounts)->whereIn('status', [
                RecruitmentRequest::STATUS_COMPLETED_SUCCESSFUL,
                RecruitmentRequest::STATUS_COMPLETED,
            ])->count(),
            'completed_unsuccessful' => (clone $baseCounts)->whereIn('status', [
                RecruitmentRequest::STATUS_COMPLETED_UNSUCCESSFUL,
                RecruitmentRequest::STATUS_CANCELLED,
            ])->count(),
            'all' => (clone $baseCounts)->count(),
        ];

        $modeCounts = [
            'all' => RecruitmentRequest::query()->count(),
            RecruitmentRequest::MODE_NAMED => RecruitmentRequest::query()
                ->where('mode', RecruitmentRequest::MODE_NAMED)
                ->count(),
            RecruitmentRequest::MODE_OPEN => RecruitmentRequest::query()
                ->where('mode', RecruitmentRequest::MODE_OPEN)
                ->count(),
        ];

        return view('admin.recruitment.index', [
            'requests' => $requests,
            'filter' => $filter,
            'mode' => $mode,
            'counts' => $counts,
            'modeCounts' => $modeCounts,
            'statuses' => RecruitmentRequest::statuses(),
        ]);
    }

    public function show(Request $request, RecruitmentRequest $recruitmentRequest): View
    {
        $recruitmentRequest->load(['talent.profile', 'company', 'messages.sender', 'statusUpdatedBy', 'statusEvents.actor']);

        return view('sourcing.show', [
            'recruitment' => $recruitmentRequest,
            'isStaff' => true,
            'statuses' => RecruitmentRequest::statuses(),
        ]);
    }

    public function storeMessage(Request $request, RecruitmentRequest $recruitmentRequest): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:2000'],
        ], [
            'body.required' => __('talenma.recruitment.chat_body_required'),
            'body.min' => __('talenma.recruitment.chat_body_min'),
            'body.max' => __('talenma.recruitment.chat_body_max'),
        ]);

        $this->recruitmentRequests->postMessage(
            $recruitmentRequest,
            $request->user(),
            $data['body'],
        );

        return redirect()
            ->to(route('admin.recruitment.show', $recruitmentRequest).'#sourcing-chat')
            ->with('toast_success', __('talenma.recruitment.chat_sent'));
    }

    public function updateStatus(Request $request, RecruitmentRequest $recruitmentRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', RecruitmentRequest::statuses())],
            'admin_comment' => ['nullable', 'string', 'max:2000'],
        ], [
            'status.required' => __('talenma.recruitment.admin_status_required'),
            'status.in' => __('talenma.recruitment.admin_status_invalid'),
            'admin_comment.max' => __('talenma.recruitment.admin_comment_max'),
        ]);

        $statusChanged = $recruitmentRequest->status !== $data['status'];
        $newComment = filled($data['admin_comment'] ?? null) ? trim($data['admin_comment']) : null;
        $commentChanged = $newComment !== $recruitmentRequest->admin_comment;

        $payload = [
            'status' => $data['status'],
            'admin_comment' => $newComment,
        ];

        if ($statusChanged) {
            $payload['status_updated_at'] = now();
            $payload['status_updated_by'] = $request->user()->id;
        }

        $recruitmentRequest->update($payload);

        $this->recruitmentRequests->notifyStatusOrComment(
            $recruitmentRequest->fresh(['company', 'talent']),
            $statusChanged,
            $commentChanged,
            $request->user(),
        );

        $redirectTo = $request->string('redirect_to')->toString();

        if ($redirectTo === 'show') {
            return redirect()
                ->route('admin.recruitment.show', $recruitmentRequest)
                ->with('toast_success', __('talenma.recruitment.admin_status_updated'));
        }

        return redirect()
            ->route('admin.recruitment.index', [
                'filter' => $request->string('filter')->toString() ?: $data['status'],
                'mode' => $request->string('mode')->toString() ?: 'all',
            ])
            ->with('toast_success', __('talenma.recruitment.admin_status_updated'));
    }
}

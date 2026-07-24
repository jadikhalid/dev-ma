<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\RecruitmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecruitmentRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString() ?: 'pending';

        if (! in_array($filter, ['all', ...RecruitmentRequest::statuses()], true)) {
            $filter = 'pending';
        }

        $query = RecruitmentRequest::query()
            ->with([
                'company',
                'talent',
                'professionSector',
                'statusUpdatedBy',
            ])
            ->latest();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $requests = $query->paginate(20)->withQueryString();

        $counts = [
            'pending' => RecruitmentRequest::query()->where('status', RecruitmentRequest::STATUS_PENDING)->count(),
            'in_progress' => RecruitmentRequest::query()->where('status', RecruitmentRequest::STATUS_IN_PROGRESS)->count(),
            'completed' => RecruitmentRequest::query()->where('status', RecruitmentRequest::STATUS_COMPLETED)->count(),
            'cancelled' => RecruitmentRequest::query()->where('status', RecruitmentRequest::STATUS_CANCELLED)->count(),
            'all' => RecruitmentRequest::query()->count(),
        ];

        $conversationIds = Conversation::query()
            ->where('channel', Conversation::CHANNEL_STAFF)
            ->whereIn('company_user_id', $requests->pluck('company_user_id')->unique()->filter())
            ->pluck('id', 'company_user_id');

        return view('admin.recruitment.index', [
            'requests' => $requests,
            'filter' => $filter,
            'counts' => $counts,
            'conversationIds' => $conversationIds,
            'statuses' => RecruitmentRequest::statuses(),
        ]);
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

        $recruitmentRequest->update([
            'status' => $data['status'],
            'admin_comment' => filled($data['admin_comment'] ?? null) ? trim($data['admin_comment']) : null,
            'status_updated_at' => now(),
            'status_updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.recruitment.index', ['filter' => $request->string('filter')->toString() ?: $data['status']])
            ->with('toast_success', __('talenma.recruitment.admin_status_updated'));
    }
}

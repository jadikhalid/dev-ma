<?php

namespace App\Services;

use App\Models\DirectHireMessage;
use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use App\Models\ProfileDocumentDownload;
use App\Models\ProfileView;
use App\Models\RecruitmentRequest;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TalentDashboardStatsService
{
    public function __construct(
        private MessagingService $messaging,
    ) {}

    /**
     * @return array{
     *     profile_views_7d: int,
     *     profile_views_total: int,
     *     cv_downloads_7d: int,
     *     unread_messages: int,
     *     recruitment_requests_total: int,
     *     recent_activity: list<array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     * }
     */
    public function build(User $talent): array
    {
        $since = now()->subDays(7);

        $views7d = ProfileView::query()
            ->where('talent_user_id', $talent->id)
            ->where('created_at', '>=', $since)
            ->count();

        $viewsTotal = ProfileView::query()
            ->where('talent_user_id', $talent->id)
            ->count();

        $downloads7d = ProfileDocumentDownload::query()
            ->where('talent_user_id', $talent->id)
            ->where('created_at', '>=', $since)
            ->count();

        $recruitmentTotal = RecruitmentRequest::query()
            ->where('developer_user_id', $talent->id)
            ->count();

        return [
            'profile_views_7d' => $views7d,
            'profile_views_total' => $viewsTotal,
            'cv_downloads_7d' => $downloads7d,
            'unread_messages' => $this->messaging->unreadCountFor($talent),
            'recruitment_requests_total' => $recruitmentTotal,
            'recent_activity' => $this->recentActivity($talent),
        ];
    }

    /**
     * @return list<array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function recentActivity(User $talent, int $limit = 10): array
    {
        $fetch = max($limit * 2, 20);

        return $this->profileViewEvents($talent, $fetch)
            ->concat($this->cvDownloadEvents($talent, $fetch))
            ->concat($this->directHireEvents($talent, $fetch))
            ->sortByDesc(fn (array $item) => $item['at']?->timestamp ?? 0)
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function profileViewEvents(User $talent, int $limit): Collection
    {
        return ProfileView::query()
            ->where('talent_user_id', $talent->id)
            ->with('viewer.companyProfile')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (ProfileView $view) => $this->activityItem(
                type: 'profile_view',
                actor: $this->actorName($view->viewer),
                at: $view->created_at,
            ));
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function cvDownloadEvents(User $talent, int $limit): Collection
    {
        return ProfileDocumentDownload::query()
            ->where('talent_user_id', $talent->id)
            ->with(['downloader.companyProfile', 'document'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (ProfileDocumentDownload $download) => $this->activityItem(
                type: 'cv_download',
                actor: $this->actorName($download->downloader),
                at: $download->created_at,
                detail: $download->document?->languageLabel(),
            ));
    }

    /**
     * @return Collection<int, array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}>
     */
    private function directHireEvents(User $talent, int $limit): Collection
    {
        $events = collect();

        $requests = DirectHireRequest::query()
            ->where('talent_user_id', $talent->id)
            ->with(['companyProfile', 'company'])
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        foreach ($requests as $request) {
            $actor = $request->companyDisplayName()
                ?: $this->actorName($request->company);
            $subject = $request->shortSubject();
            $href = route('talent.direct-hire.show', $request);

            if ($request->created_at) {
                $events->push($this->activityItem(
                    type: 'direct_hire_proposed',
                    actor: $actor,
                    at: $request->created_at,
                    subject: $subject,
                    href: $href,
                ));
            }

            if ($request->closed_at) {
                $type = match ($request->status) {
                    DirectHireRequest::STATUS_HIRED => 'direct_hire_hired',
                    DirectHireRequest::STATUS_WITHDRAWN => 'direct_hire_withdrawn',
                    default => 'direct_hire_closed_negative',
                };

                $events->push($this->activityItem(
                    type: $type,
                    actor: $actor,
                    at: $request->closed_at,
                    subject: $subject,
                    href: $href,
                ));
            }
        }

        $rounds = DirectHireRound::query()
            ->whereHas('request', fn ($query) => $query->where('talent_user_id', $talent->id))
            ->with(['request.companyProfile', 'request.company'])
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        foreach ($rounds as $round) {
            $request = $round->request;

            if (! $request) {
                continue;
            }

            $actor = $request->companyDisplayName()
                ?: $this->actorName($request->company);
            $subject = $request->shortSubject();
            $href = route('talent.direct-hire.show', $request);

            if ($round->created_at) {
                $events->push($this->activityItem(
                    type: 'direct_hire_round_added',
                    actor: $actor,
                    at: $round->created_at,
                    detail: $round->title,
                    subject: $subject,
                    href: $href,
                ));
            }

            if ($round->isCancelled()) {
                $events->push($this->activityItem(
                    type: 'direct_hire_round_cancelled',
                    actor: $actor,
                    at: $round->updated_at ?? $round->completed_at ?? $round->created_at,
                    detail: $round->title,
                    subject: $subject,
                    href: $href,
                ));
            } elseif ($round->completed_at && in_array($round->status, DirectHireRound::outcomeStatuses(), true)) {
                $events->push($this->activityItem(
                    type: 'direct_hire_round_result',
                    actor: $actor,
                    at: $round->completed_at,
                    detail: $round->title,
                    subject: $subject,
                    result: $round->statusLabel(),
                    href: $href,
                ));
            }
        }

        $messages = DirectHireMessage::query()
            ->where('sender_user_id', '!=', $talent->id)
            ->whereHas('request', fn ($query) => $query->where('talent_user_id', $talent->id))
            ->with(['request.companyProfile', 'request.company'])
            ->latest()
            ->limit($limit)
            ->get();

        foreach ($messages as $message) {
            $request = $message->request;

            if (! $request) {
                continue;
            }

            // Skip the seeded proposal copy — already covered by direct_hire_proposed.
            if (
                $message->created_at
                && $request->created_at
                && $message->created_at->diffInSeconds($request->created_at) <= 5
            ) {
                continue;
            }

            $events->push($this->activityItem(
                type: 'direct_hire_message',
                actor: $request->companyDisplayName() ?: $this->actorName($request->company),
                at: $message->created_at,
                subject: $request->shortSubject(),
                href: route('talent.direct-hire.show', $request),
            ));
        }

        return $events;
    }

    /**
     * @return array{type: string, actor: string, detail: ?string, subject: ?string, result: ?string, href: ?string, at: CarbonInterface}
     */
    private function activityItem(
        string $type,
        string $actor,
        ?CarbonInterface $at,
        ?string $detail = null,
        ?string $subject = null,
        ?string $result = null,
        ?string $href = null,
    ): array {
        return [
            'type' => $type,
            'actor' => $actor,
            'detail' => $detail,
            'subject' => $subject,
            'result' => $result,
            'href' => $href,
            'at' => $at,
        ];
    }

    private function actorName(?User $user): string
    {
        if (! $user) {
            return __('talenma.dashboard.talent.stats.unknown_actor');
        }

        $user->loadMissing('companyProfile');

        return $user->companyProfile?->displayName()
            ?: ($user->name ?: __('talenma.dashboard.talent.stats.unknown_actor'));
    }
}
